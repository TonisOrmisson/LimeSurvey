<?php


class Settings extends CModel
{
    /** @var LSActiveRecord */
    public $settings;

    /** @var string */
    public $itemClass;

    /** @var string Value field name in itemClass table */
    public $valueField = 'value';

    /** @var boolean whether we skip checking attribute existence */
    public $doCheck = true;


    public function __construct()
    {
        if(!$this->itemClass){
            throw new \Exception('ItemClass must be defined');
        }
        $this->checkSettings();
        $this->setSettings();
        $this->loadStrings();
    }


    public function attributeNames()
    {
        return [];
    }

    /**
     * Check if all defined attributes exist in settings[] and throw an error if its missing
     */
    protected function checkSettings(){
        if($this->doCheck){
            foreach ($this->attributes as $checkAttribute){
                $class =$this->itemClass;
                if(!$class::getByKey($checkAttribute)){
                    throw new  \Exception('Key "'.$checkAttribute.'" is missing in '.$class);
                }
            }
        }

    }

    public function beforeValidate() {
        foreach ($this->attributes as $key => $value){
            if ($value === ""){
                $this->$key = NULL;
            }
        }

        return parent::beforeValidate();
    }

    public function loadStrings() {
        if(!empty($this->settings)){
            foreach ($this->settings as $key => $setting) {
                // only accept keys that are described in the model
                $type = SurveyLanguagesettingType::getByKey($key);
                if($type){
                    $this->{$key} = $setting->{$this->valueField};
                }
            }
        }
    }


    /**
     *
     */
    public function setSettings() {
        throw new \Exception('setSettings() needs to be overriden');
    }


}