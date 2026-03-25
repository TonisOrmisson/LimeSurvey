<?php

namespace LimeSurvey\RemoteControl;

class RpcConfiguration
{
    const SUPPORTED_FORMATS = ['json', 'xml', 'toon'];

    /**
     * @return string[]
     */
    public static function getSupportedFormats()
    {
        return self::SUPPORTED_FORMATS;
    }

    /**
     * @param string $format
     * @return bool
     */
    public static function isFormatEnabled($format)
    {
        if (!in_array($format, self::SUPPORTED_FORMATS, true)) {
            return false;
        }

        $setting = \SettingGlobal::model()->findByPk("rpc_{$format}_enabled");
        if ($setting !== null) {
            return $setting->stg_value === '1';
        }

        $configValue = self::getConfigDefinedRpcFormatEnabled($format);
        if ($configValue !== null) {
            return $configValue === '1';
        }

        return \Yii::app()->getConfig('RPCInterface') === $format;
    }

    /**
     * @param string $format
     * @return string
     */
    public static function getEffectiveToggleValue($format)
    {
        return self::isFormatEnabled($format) ? '1' : '0';
    }

    /**
     * @return string[]
     */
    public static function getEnabledFormats()
    {
        return array_values(array_filter(self::SUPPORTED_FORMATS, [self::class, 'isFormatEnabled']));
    }

    /**
     * @param string $format
     * @return string|null
     */
    public static function getConfigDefinedRpcFormatEnabled($format)
    {
        $configValues = self::getConfigDefinedRpcFormatValues();
        return $configValues[$format] ?? null;
    }

    /**
     * @param string $format
     * @return string
     */
    public static function getTransportLabel($format)
    {
        $labels = [
            'json' => 'JSON-RPC',
            'xml' => 'XML-RPC',
            'toon' => 'TOON-RPC',
        ];

        return $labels[$format] ?? strtoupper($format);
    }

    /**
     * @return string[]
     */
    private static function getConfigDefinedRpcFormatValues()
    {
        $paths = self::getRpcConfigFilePaths();
        $cacheKey = implode('|', array_map(function ($path) {
            return $path . ':' . md5_file($path);
        }, $paths));
        static $cache = [];

        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $configValues = [];
        foreach ($paths as $configFile) {
            $userConfig = require $configFile;
            if (!isset($userConfig['config']) || !is_array($userConfig['config'])) {
                continue;
            }

            foreach (self::SUPPORTED_FORMATS as $format) {
                $configKey = "rpc_{$format}_enabled";
                if (array_key_exists($configKey, $userConfig['config'])) {
                    $configValues[$format] = (string) $userConfig['config'][$configKey];
                }
            }
        }

        $cache[$cacheKey] = $configValues;
        return $cache[$cacheKey];
    }

    /**
     * @param string $format
     * @return string|null
     */
    private static function getRpcConfigFilePaths()
    {
        $paths = [];
        $configDirPath = \Yii::app()->getConfig('configdir') . DIRECTORY_SEPARATOR . 'config.php';

        if (is_file($configDirPath)) {
            $paths[] = $configDirPath;
        }

        return $paths;
    }
}
