<?php

/**
 * Class ModelWithL10ns
 * A generic class for models having the translation L10n child objects
 * @property Survey $survey
 */
class ModelWithL10ns extends LSActiveRecord
{
    public $l10nsClass = null;

    public function copy()
    {
        $newModel = new static();
        $newModel->attributes = $this->attributes;
        if ($newModel->save()) {
            foreach ($this->survey->allLanguages as $language) {


            }
        }
    }

}