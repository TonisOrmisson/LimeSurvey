<?php

/**
 * Class BaseL10n
 */
class BaseL10n extends LSActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function defaultScope()
    {
        return array('index'=>'language');
    }

}