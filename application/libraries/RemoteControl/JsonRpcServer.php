<?php

namespace LimeSurvey\RemoteControl;

use remotecontrol_handle;
use Yii;
use BigData;
use Exception;

/**
 * Class JsonRpcServer
 *
 * Implements JSON-RPC 2.0/1.0 server for RemoteControl API.
 * This wraps the existing LSjsonRPCServer logic into the new interface.
 * Git history preserved from application/libraries/LSjsonRPCServer.php.
 */
class JsonRpcServer implements RpcServerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(remotecontrol_handle $handler)
    {
        if (!$this->isJsonRequest()) {
            return false;
        }

        Yii::import('application.libraries.BigData', true);

        // Read input
        $input = file_get_contents('php://input');
        $request = json_decode($input, true);

        if (is_null($request)) {
            $response = [
                'id' => null,
                'result' => null,
                'error' => 'unable to decode malformed json'
            ];
        } else {
            try {
                // Ensure we have method and id
                $method = isset($request['method']) ? $request['method'] : null;
                $params = isset($request['params']) ? $request['params'] : [];
                $id = isset($request['id']) ? $request['id'] : null;

                if (!$method) {
                    throw new Exception('no method specified');
                }

                $result = @call_user_func_array([$handler, $method], $params);

                if ($result !== false) {
                    $response = [
                        'id' => $id,
                        'result' => $result,
                        'error' => null
                    ];
                } else {
                    $response = [
                        'id' => $id,
                        'result' => null,
                        'error' => 'unknown method or incorrect parameters'
                    ];
                }
            } catch (Exception $e) {
                $response = [
                    'id' => isset($request['id']) ? $request['id'] : null,
                    'result' => null,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Output response
        if (is_null($request) || !empty($request['id'])) {
            header('Content-Type: application/json');
            BigData::json_echo($response);
        }

        return true;
    }

    /**
     * Accept explicit format=json selection even when intermediaries drop Content-Type.
     *
     * @return bool
     */
    protected function isJsonRequest()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return false;
        }

        $requestedFormat = strtolower((string) Yii::app()->getRequest()->getParam('format', ''));
        if ($requestedFormat === 'json') {
            return true;
        }

        $contentType = !empty($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        return strpos((string) $contentType, 'application/json') !== false;
    }
}
