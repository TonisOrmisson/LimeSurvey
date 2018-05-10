<?php

/**
 * Class StaticModel
 * A general class to use in case of non-db models
 */
class StaticModel extends CModel
{

    /**
     * Models attributes as array indexed by primary key
     * @return array
     */
    public static function modelsAttributes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeNames()
    {
        return [];
    }

    /**
     * @param string $pk primary key of model
     * @return null|static
     */
    public static function findOne($pk)
    {
        $modelsAttributes = static::modelsAttributes();
        if (isset($modelsAttributes[$pk])) {
            $model = new static();
            $model->attributes = $modelsAttributes[$pk];
            return $model;
        }
        return null;
    }

    /**
     * @return static[]
     */
    public static function allModels()
    {
        $models = [];
        foreach (static::modelsAttributes() as $key => $attributes) {
            $model = new static();
            $model->attributes = $attributes;
            $models[$key] = $model;
        }
        return $models;
    }

    /**
     * @param string $attribute attribute name
     * @param array|mixed $values
     * @return array
     */
    public static function findByAttributeValue($attribute,$values)
    {
        $models = [];
        foreach (static::allModels() as $model) {
            if (is_array($values)) {
                if (in_array($model->{$attribute}, $values)){
                    $models[] = $model;
                }
            }
            else {
                if ($model->{$attribute} === $values){
                    $models[] = $model->code;
                }
            }
        }
        return $models;
    }

    /**
     * @param string $attribute attribute name
     * @param array|mixed $values
     * @param string $column column name to return
     * @return array
     */
    public static function findColumnByAttributeValue($attribute, $values, $column)
    {
        $result = [];
        $models = static::findByAttributeValue($attribute, $values);
        if (!empty($models)) {
             foreach ($models as $model) {
                 $result[] = $model->{$column};
             }
        }
        return $result;
    }

}