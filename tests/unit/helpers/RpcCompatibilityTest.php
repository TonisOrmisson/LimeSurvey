<?php

namespace ls\tests;

use LimeSurvey\Helpers\Update\Update_702;
use LimeSurvey\RemoteControl\JsonRpcServer;
use LimeSurvey\RemoteControl\RpcConfiguration;

class RpcCompatibilityTest extends TestBaseClass
{
    /** @var string */
    private $originalConfigDir;

    /** @var string */
    private $originalRpcInterface;

    /** @var array */
    private $settingBackups = [];

    /** @var string */
    private $tempConfigDir;

    /** @var string */
    private $appConfigPath;

    /** @var string */
    private $originalAppConfigContents;

    /** @var array */
    private $originalGet;

    /** @var array */
    private $originalServer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConfigDir = \Yii::app()->getConfig('configdir');
        $this->originalRpcInterface = \Yii::app()->getConfig('RPCInterface');
        $this->tempConfigDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ls_rpc_compat_' . uniqid('', true);
        $this->appConfigPath = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        $this->originalAppConfigContents = file_get_contents($this->appConfigPath);
        $this->originalGet = $_GET;
        $this->originalServer = $_SERVER;

        mkdir($this->tempConfigDir, 0777, true);
        $this->writeConfig([]);
        \Yii::app()->setConfig('configdir', $this->tempConfigDir);

        foreach (['rpc_json_enabled', 'rpc_xml_enabled', 'rpc_toon_enabled'] as $settingName) {
            $this->backupAndDeleteSetting($settingName);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->settingBackups as $settingName => $settingValue) {
            if ($settingValue === null) {
                \SettingGlobal::model()->deleteByPk($settingName);
            } else {
                \SettingGlobal::setSetting($settingName, $settingValue);
            }
        }

        \Yii::app()->setConfig('configdir', $this->originalConfigDir);
        \Yii::app()->setConfig('RPCInterface', $this->originalRpcInterface);
        file_put_contents($this->appConfigPath, $this->originalAppConfigContents);
        $_GET = $this->originalGet;
        $_SERVER = $this->originalServer;

        @unlink($this->tempConfigDir . DIRECTORY_SEPARATOR . 'config.php');
        @rmdir($this->tempConfigDir);

        parent::tearDown();
    }

    public function testRemoteControlHonorsConfigDefinedToonToggleWithoutDatabaseRow(): void
    {
        $this->writeConfig(['rpc_toon_enabled' => '1']);
        \Yii::app()->setConfig('RPCInterface', 'off');

        $this->assertTrue(RpcConfiguration::isFormatEnabled('toon'));
        $this->assertSame('1', RpcConfiguration::getEffectiveToggleValue('toon'));
    }

    public function testRemoteControlHonorsLegacyRpcInterfaceForToonWithoutExplicitToggle(): void
    {
        $this->writeConfig([]);
        \Yii::app()->setConfig('RPCInterface', 'toon');

        $this->assertTrue(RpcConfiguration::isFormatEnabled('toon'));
        $this->assertSame('1', RpcConfiguration::getEffectiveToggleValue('toon'));
    }

    public function testRemoteControlHonorsLegacyRpcInterfaceForJsonWithoutExplicitToggle(): void
    {
        $this->writeConfig([]);
        \Yii::app()->setConfig('RPCInterface', 'json');

        $this->assertTrue(RpcConfiguration::isFormatEnabled('json'));
        $this->assertSame('1', RpcConfiguration::getEffectiveToggleValue('json'));
    }

    public function testEnabledFormatsIncludeConfigEnabledTransportWhenDefaultInterfaceIsOff(): void
    {
        $this->writeConfig(['rpc_xml_enabled' => '1']);
        \Yii::app()->setConfig('RPCInterface', 'off');

        $this->assertSame(['xml'], RpcConfiguration::getEnabledFormats());
    }

    public function testConfigDefinedRpcToggleIgnoresStaleApplicationConfigWhenConfigdirMoves(): void
    {
        $this->writeConfig(['rpc_xml_enabled' => '1']);
        file_put_contents($this->appConfigPath, "<?php\nreturn " . var_export(['config' => ['rpc_xml_enabled' => '0']], true) . ";\n");

        $this->assertSame('1', RpcConfiguration::getConfigDefinedRpcFormatEnabled('xml'));
    }

    public function testJsonRpcServerAcceptsFormatSelectorWithoutJsonContentType(): void
    {
        $_GET['format'] = 'json';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        unset($_SERVER['CONTENT_TYPE'], $_SERVER['HTTP_CONTENT_TYPE']);

        $server = new JsonRpcServer();
        $method = new \ReflectionMethod(JsonRpcServer::class, 'isJsonRequest');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($server));
    }

    public function testUpdate702MigratesLegacyRpcInterfaceFromMergedConfig(): void
    {
        $this->writeConfig([]);
        \Yii::app()->setConfig('RPCInterface', 'json');

        $this->assertSame('1', $this->invokeUpdate702ResolveRpcFormatEnabled('json'));
        $this->assertSame('0', $this->invokeUpdate702ResolveRpcFormatEnabled('xml'));
    }

    public function testUpdate702MigratesExplicitConfigToggleWithoutDatabaseRow(): void
    {
        $this->writeConfig(['rpc_toon_enabled' => '1']);
        \Yii::app()->setConfig('RPCInterface', 'off');

        $this->assertSame('1', $this->invokeUpdate702ResolveRpcFormatEnabled('toon'));
    }

    /**
     * @param string $settingName
     * @return void
     */
    private function backupAndDeleteSetting($settingName): void
    {
        $setting = \SettingGlobal::model()->findByPk($settingName);
        $this->settingBackups[$settingName] = $setting ? $setting->stg_value : null;
        \SettingGlobal::model()->deleteByPk($settingName);
    }

    /**
     * @param array $config
     * @return void
     */
    private function writeConfig(array $config): void
    {
        $contents = "<?php\nreturn " . var_export(['config' => $config], true) . ";\n";
        file_put_contents($this->tempConfigDir . DIRECTORY_SEPARATOR . 'config.php', $contents);
    }

    /**
     * @param string $format
     * @return string
     * @throws \ReflectionException
     */
    private function invokeUpdate702ResolveRpcFormatEnabled($format): string
    {
        $update = new Update_702(\Yii::app()->db, '');
        $method = new \ReflectionMethod(Update_702::class, 'resolveRpcFormatEnabled');
        $method->setAccessible(true);

        return $method->invoke($update, $format);
    }
}
