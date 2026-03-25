<?php

namespace LimeSurvey\RemoteControl;

use remotecontrol_handle;
use Yii;
use Zend_XmlRpc_Server;

/**
 * Class XmlRpcServer
 *
 * Implements XML-RPC server for RemoteControl API using Zend_XmlRpc.
 */
class XmlRpcServer implements RpcServerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(remotecontrol_handle $handler)
    {
        // For XML-RPC, we check for appropriate content-type or if it's a POST
        // XML-RPC clients often use text/xml or application/xml.

        $cur_path = get_include_path();
        set_include_path($cur_path . PATH_SEPARATOR . APPPATH . 'helpers');

        $xmlrpc = new Zend_XmlRpc_Server();
        $xmlrpc->sendArgumentsToAllMethods(false);

        Yii::import('application.libraries.LSZend_XmlRpc_Response_Http');
        $xmlrpc->setResponseClass('LSZend_XmlRpc_Response_Http');

        $xmlrpc->setClass($handler);
        $result = $xmlrpc->handle();

        if ($result instanceof \LSZend_XmlRpc_Response_Http) {
            $result->printXml();
        } else {
            // Zend_XmlRpc_Server_Fault
            echo $result;
        }

        return true;
    }
}
