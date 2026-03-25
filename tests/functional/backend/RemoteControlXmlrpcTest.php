<?php

namespace ls\tests;

use Yii;

class RemoteControlXmlrpcTest extends TestBaseClassWeb
{
    private static $tmpBaseUrl;
    private static $tmpRPCType;
    private static $tmpRpcXmlEnabled;
    private static $hadRpcXmlEnabled;

    private static $client;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $urlMan = Yii::app()->urlManager;
        self::$tmpBaseUrl = $urlMan->getBaseUrl();
        $urlMan->setBaseUrl('http://' . self::$domain . '/index.php');
        //$serverUrl = App()->createAbsoluteUrl('/admin/remotecontrol');
        $serverUrl = $urlMan->createUrl('/admin/remotecontrol');

        self::$tmpRPCType = Yii::app()->getConfig('RPCInterface');
        $rpcXmlEnabled = \SettingGlobal::model()->findByPk('rpc_xml_enabled');
        self::$hadRpcXmlEnabled = $rpcXmlEnabled !== null;
        self::$tmpRpcXmlEnabled = $rpcXmlEnabled ? $rpcXmlEnabled->stg_value : null;

        if (self::$hadRpcXmlEnabled) {
            \SettingGlobal::model()->deleteByPk('rpc_xml_enabled');
        }

        if (self::$tmpRPCType !== 'xml') {
            \SettingGlobal::setSetting('RPCInterface', 'xml');
            $RPCType = 'xml';
        }

        $cur_path = get_include_path();
        set_include_path($cur_path . PATH_SEPARATOR . APPPATH . 'helpers');

        self::$client = new \Zend_XmlRpc_Client($serverUrl);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $urlMan = Yii::app()->urlManager;
        $urlMan->setBaseUrl(self::$tmpBaseUrl);

        \SettingGlobal::setSetting('RPCInterface', self::$tmpRPCType);

        if (self::$hadRpcXmlEnabled) {
            \SettingGlobal::setSetting('rpc_xml_enabled', self::$tmpRpcXmlEnabled);
        } else {
            \SettingGlobal::model()->deleteByPk('rpc_xml_enabled');
        }
    }

    public function testGetSessionKey()
    {
        $username = getenv('ADMINUSERNAME');
        if (!$username) {
            $username = 'admin';
        }

        $password = getenv('PASSWORD');
        if (!$password) {
            $password = 'password';
        }
        $sessionKey = self::$client->call('get_session_key', [$username, $password]);
        $this->assertIsString($sessionKey);

        self::$client->call('release_session_key', [$sessionKey]);
    }

    public function testCredentialsError()
    {
        /* generate a random string to get an invalid user, no restriction on users_name */
        $sessionKey = self::$client->call('get_session_key', [Yii::app()->securityManager->generateRandomString(64), Yii::app()->securityManager->generateRandomString(64)]);
        $this->assertIsArray($sessionKey);
        $this->assertSame("Invalid user name or password", $sessionKey['status']);

        self::$client->call('release_session_key', [$sessionKey]);
    }
}
