<?php

namespace LimeSurvey\Helpers\Update;

use LimeSurvey\RemoteControl\RpcConfiguration;

/**
 * Update_702 handles the migration of old RPC settings to the new individual toggles.
 * It also initializes the new rpc_toon_enabled setting.
 */
class Update_702 extends DatabaseUpdateBase
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $newSettings = [
            'rpc_json_enabled' => $this->resolveRpcFormatEnabled('json'),
            'rpc_xml_enabled'  => $this->resolveRpcFormatEnabled('xml'),
            'rpc_toon_enabled' => $this->resolveRpcFormatEnabled('toon'),
        ];

        foreach ($newSettings as $name => $value) {
            $existing = $this->db->createCommand()
                ->select('stg_name')
                ->from('{{settings_global}}')
                ->where('stg_name = :name', [':name' => $name])
                ->queryRow();

            if ($existing) {
                $this->db->createCommand()->update(
                    '{{settings_global}}',
                    ['stg_value' => $value],
                    'stg_name = :name',
                    [':name' => $name]
                );
            } else {
                $this->db->createCommand()->insert(
                    '{{settings_global}}',
                    ['stg_name' => $name, 'stg_value' => $value]
                );
            }
        }
    }

    /**
     * Resolve the new rpc_<format>_enabled value from the effective pre-upgrade configuration.
     *
     * @param string $format
     * @return string
     */
    protected function resolveRpcFormatEnabled($format)
    {
        $existingValue = $this->getSettingValue("rpc_{$format}_enabled");
        if ($existingValue !== null) {
            return $existingValue;
        }

        $configValue = RpcConfiguration::getConfigDefinedRpcFormatEnabled($format);
        if ($configValue !== null) {
            return $configValue === '1' ? '1' : '0';
        }

        return \Yii::app()->getConfig('RPCInterface') === $format ? '1' : '0';
    }

    /**
     * Return an existing settings_global value or null when the row does not exist.
     *
     * @param string $name
     * @return string|null
     */
    protected function getSettingValue($name)
    {
        $row = $this->db->createCommand()
            ->select('stg_value')
            ->from('{{settings_global}}')
            ->where('stg_name = :name', [':name' => $name])
            ->queryRow();

        return $row ? (string) $row['stg_value'] : null;
    }
}
