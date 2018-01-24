<?php


class Settings extends CModel
{
    /** @var LSActiveRecord The initial Setting item records */
    public $settings;

    /** @var string *The Class Name of Setting item*/
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
        $this->populate();
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
            // set empty strings as null
            if ($value === ""){
                $this->$key = NULL;
            }
        }
        return parent::beforeValidate();
    }

    /**
     * Pupulates the model with the setting records
     */
    public function populate() {
        if(!empty($this->settings)){
            foreach ($this->settings as $key => $setting) {
                $this->{$key} = $setting->{$this->valueField};
            }
        }
    }


    /**
     * Loads the initial records from which the model attributes are populated
     */
    protected function setSettings() {
        /** @var LSActiveRecord $item */
        $item = new $this->itemClass;
        $item->findAll();
        $this->settings =$item->findAll();
    }


}