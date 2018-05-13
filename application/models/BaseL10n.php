<?php

/**
 * Class BaseL10n
 * @property integer $id PK
 * @property string $language Question language code. Note: There is a unique key on qid & language columns combined
 * @property ModelWithL10ns $parent
 */
class BaseL10n extends LSActiveRecord
{
    /** @var string  */
    public static $titleColumn = 'title';

    /** @var string  */
    public static $parentFKColumn = 'parent_id';

    /** @var string  */
    public static $parentClassName;

    /**
     * {@inheritdoc}
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     * {@inheritdoc}
     */
    public static function model($class = __CLASS__)
    {
        /** @var static $model */
        $model = parent::model($class);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultScope()
    {
        return ['index'=>'language'];
    }

    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return array(
            'prent' => [self::BELONGS_TO, static::$parentClassName, static::$parentFKColumn],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['language,' . static::$titleColumn . ',' .static::$parentFKColumn, 'required'],
            [static::$titleColumn, 'string'],
            [static::$parentFKColumn,'integerOnly'=>true],
            ['language', 'length', 'min' => 2, 'max'=>20], // TODO in array languages ?
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'language' => gt('Language'),
        ];
    }

}