<?php

class SettingsGlobal extends Settings
{

    public $itemClass = 'SettingGlobal';
    public $valueField = 'stg_value';


    /** @var string $admintheme */
    public $admintheme;

    /** @var integer $AssetsVersion */
    public $AssetsVersion;

    /** @var string $bounceaccounthost*/
    public $bounceaccounthost;

    /** @var string $bounceaccountpass*/
    public $bounceaccountpass;


    public function rules()
    {
        return array_merge(parent::rules(),[
            ['admintheme','in','range'=>AdminTheme::getAdminThemeList(),'allowEmpty'=>false],
            ['AssetsVersion','numeric','allowEmpty'=>false],
        ]);
    }

    public function attributeNames()
    {
        return [
            'admintheme' => gT('Admin theme'),
            'AssetsVersion' => gT('Assets version'),
        ];
    }

}