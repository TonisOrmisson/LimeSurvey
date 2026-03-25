<?php

/*
* LimeSurvey
* Copyright (C) 2007-2026 The LimeSurvey Project Team
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
*/

use LimeSurvey\RemoteControl\JsonRpcServer;
use LimeSurvey\RemoteControl\RpcConfiguration;
use LimeSurvey\RemoteControl\XmlRpcServer;
use LimeSurvey\RemoteControl\ToonRpcServer;
use LimeSurvey\RemoteControl\RpcServerInterface;

class RemoteControl extends SurveyCommonAction
{
    /**
     * @var RpcServerInterface
     */
    protected $rpcServer;

    /**
     * This is the RPC server routine
     *
     * @access public
     * @return void
     */
    public function run()
    {
        Yii::import('application.helpers.remotecontrol.*');

        $setAccessControlHeader = Yii::app()->getConfig('add_access_control_header', 1);
        if ($setAccessControlHeader == 1) {
            header("Access-Control-Allow-Origin: *");
        }

        $oHandler = new remotecontrol_handle($this->controller);

        if (Yii::app()->getRequest()->isPostRequest) {
            $format = $this->determineFormat();
            $this->rpcServer = $this->getRpcServer($format);

            if ($this->rpcServer) {
                $handled = $this->rpcServer->handle($oHandler);
                if ($handled === false) {
                    header("HTTP/1.1 400 Bad Request");
                    echo "Invalid or disabled RPC format requested.";
                }
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo "Invalid or disabled RPC format requested.";
            }

            foreach (App()->log->routes as $route) {
                $route->enabled = $route->enabled && !($route instanceof CWebLogRoute);
            }
            Yii::app()->session->destroy();
            exit;
        } else {
            $enabledFormats = RpcConfiguration::getEnabledFormats();
            // Disabled output of API methods for now
            if (Yii::app()->getConfig("rpc_publish_api") == true && !empty($enabledFormats)) {
                $reflector = new ReflectionObject($oHandler);
                foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    /* @var $method ReflectionMethod */
                    if (substr($method->getName(), 0, 1) !== '_') {
                        $list[$method->getName()] = array(
                            'description' => $method->getDocComment(),
                            'parameters'  => $method->getParameters(),
                        );
                    }
                }
                ksort($list);
                $aData['transports'] = array_map([RpcConfiguration::class, 'getTransportLabel'], $enabledFormats);
                $aData['list'] = $list;
                $this->renderWrappedTemplate('remotecontrol', array('index_view'), $aData);
            }
        }
    }

    /**
     * Determine the requested RPC format based on headers, query params, or global default.
     *
     * @return string
     */
    protected function determineFormat()
    {
        // 1. Check query parameter
        $format = strtolower((string) Yii::app()->getRequest()->getParam('format', ''));
        if (in_array($format, ['json', 'xml', 'toon'], true)) {
            return $format;
        }

        // 2. Check Content-Type header
        $contentType = Yii::app()->getRequest()->getContentType();
        if (stripos((string) $contentType, 'application/json') !== false) {
            return 'json';
        }
        if (stripos((string) $contentType, 'application/xml') !== false || stripos((string) $contentType, 'text/xml') !== false) {
            return 'xml';
        }
        if (stripos((string) $contentType, 'application/toon') !== false) {
            return 'toon';
        }

        // 3. Fallback to global default
        return Yii::app()->getConfig("RPCInterface");
    }

    /**
     * Get the appropriate RPC server implementation.
     *
     * @param string $format
     * @return RpcServerInterface|null
     */
    protected function getRpcServer($format)
    {
        if (!in_array($format, ['json', 'xml', 'toon'], true)) {
            return null;
        }

        if (!$this->isRpcFormatEnabled($format)) {
            return null;
        }

        switch ($format) {
            case 'json':
                return new JsonRpcServer();
            case 'xml':
                return new XmlRpcServer();
            case 'toon':
                return new ToonRpcServer();
        }

        return null;
    }

    /**
     * Keep JSON/XML RPC backward compatible for installs that only configured RPCInterface.
     *
     * @param string $format
     * @return bool
     */
    protected function isRpcFormatEnabled($format)
    {
        return RpcConfiguration::isFormatEnabled($format);
    }

    /**
     * Simple procedure to test most RPC functions
     *
     */
    public function test()
    {
        // Enable if you want to test this function
        $enabled = false;
        if ($enabled) {
            $RPCType = Yii::app()->getConfig("RPCInterface");
            $serverUrl = App()->createAbsoluteUrl('/admin/remotecontrol');
            $sFileToImport = dirname((string) Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'demosurveys' . DIRECTORY_SEPARATOR . 'ls205_sample_survey_english.lss';

            if ($RPCType == 'xml') {
                $cur_path = get_include_path();
                set_include_path($cur_path . PATH_SEPARATOR . APPPATH . 'helpers');
                $client = new Zend_XmlRpc_Client($serverUrl);
                // Increase timeout (default is 10 seconds). Importing the survey may take a while.
                $client->getHttpClient()->setConfig(['timeout' => 300]);
            } elseif ($RPCType == 'json') {
                Yii::app()->loadLibrary('jsonRPCClient');
                $client = new jsonRPCClient($serverUrl);
                // Set $client = new jsonRPCClient($serverUrl, true); to activate debug output
            } else {
                die('RPC interface not activated in global settings');
            }


            $sSessionKey = $client->call('get_session_key', array('admin', 'password'));
            if (is_array($sSessionKey)) {
                echo $sSessionKey['status'];
                die();
            } else {
                echo 'Retrieved session key' . '<br>';
            }

            $sLSSData = base64_encode(file_get_contents($sFileToImport));
            $iSurveyID = $client->call('import_survey', array($sSessionKey, $sLSSData, 'lss', 'Test import by JSON_RPC'));
            echo 'Created new survey SID:' . $iSurveyID . '<br>';

            $aResult = $client->call('activate_survey', array($sSessionKey, $iSurveyID));
            if ($aResult['status'] == 'OK') {
                echo 'Survey ' . $iSurveyID . ' successfully activated.<br>';
            }
            $aResult = $client->call('activate_tokens', array($sSessionKey, $iSurveyID, array(1, 2)));
            if ($aResult['status'] == 'OK') {
                echo 'Tokens for Survey ID ' . $iSurveyID . ' successfully activated.<br>';
            }
            $aResult = $client->call('set_survey_properties', array($sSessionKey, $iSurveyID, array('admin' => 'Admin name')));
            if (!array_key_exists('status', $aResult)) {
                echo 'Modified survey settings for survey ' . $iSurveyID . '<br>';
            }
            $aResult = $client->call('add_language', array($sSessionKey, $iSurveyID, 'ar'));
            if ($aResult['status'] == 'OK') {
                echo 'Added Arabian as additional language' . '<br>';
            }
            $aResult = $client->call('set_language_properties', array($sSessionKey, $iSurveyID, array('surveyls_welcometext' => 'An Arabian welcome text!'), 'ar'));
            if ($aResult['status'] == 'OK') {
                echo 'Modified survey locale setting welcometext for Arabian in survey ID ' . $iSurveyID . '<br>';
            }

            $aResult = $client->call('delete_language', array($sSessionKey, $iSurveyID, 'ar'));
            if ($aResult['status'] == 'OK') {
                echo 'Removed Arabian as additional language' . '<br>';
            }
            $aResult = $client->call('add_participants', array($sSessionKey, $iSurveyID, array(array('firstname' => 'Some', 'lastname' => 'Body', 'email' => 'somebody@test.com'))));
            if (!array_key_exists('status', $aResult)) {
                echo 'Added a participant to survey ' . $iSurveyID . '<br>';
            }
            $aResult = $client->call('set_participant_properties', array($sSessionKey, $iSurveyID, array('email' => 'somebody@test.com'), array('lastname' => 'One', 'email' => 'someone@test.com')));
            if (!array_key_exists('status', $aResult)) {
                echo 'Modified participant properties in survey ' . $iSurveyID . '<br>';
            }

            //Very simple example to export responses as Excel file
            //$aResult=$client->call('export_responses', array($sSessionKey,$iSurveyID,'xls'));
            //$aResult=$client->call('export_responses', array($sSessionKey,$iSurveyID,'pdf'));
            //$aResult=$client->call('export_responses', array($sSessionKey,$iSurveyID,'doc'));
            $aResult = $client->call('export_responses', array($sSessionKey, $iSurveyID, 'csv'));
            //file_put_contents('test.xls',base64_decode(chunk_split($aResult)));

            $aResult = $client->call('delete_survey', array($sSessionKey, $iSurveyID));
            echo 'Deleted survey SID:' . $iSurveyID . '-' . $aResult['status'] . '<br>';

            // Release the session key - close the session
            $Result = $client->call('release_session_key', array($sSessionKey));
            echo 'Closed the session' . '<br>';
        }
    }
}
