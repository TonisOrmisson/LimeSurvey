<?php

/**
 * Class Field describes a column in responses data table
 * @property string $type
 */
class Field extends CModel
{

    const TYPE_STRING = 'string';
    const TYPE_CHAR = 'char';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE = 'double';

    /** @var Question */
    public $question;

    /** @var string $name Field column name */
    public $name;

    /**
     * Field constructor.
     * @param Question|null $question
     */
    public function __construct(Question $question = null)
    {
        $this->question = $question;
    }


    /**
     * {@inheritdoc}
     */
    public function attributeNames()
    {
        return [
            'name' => gT('Name'),
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (!empty($this->question)) {
            return $this->typeForQuestion();
        } else {
            // TODO
        }
    }

    private function typeForQuestion()
    {
        switch ($this->question->fieldType) {
            default:
        }
    }

}