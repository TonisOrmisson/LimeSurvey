<?php

/**
 * Class FieldMap describes the data-set (a set of data Fields /columns) of a Survey
 */
class FieldMap
{
    /** @var Survey */
    public $survey;

    /** @var Question $question question currently being processed */
    private $question;


    /**
     * FieldMap constructor.
     * @param Survey $survey
     * @throws Exception
     */
    public function __construct(Survey $survey)
    {
        if (!($survey instanceof Survey)) {
            throw new \Exception(get_class($survey) . " used as Survey while initiating " . self::class);
        }
    }

    /**
     * @return Field[]
     */
    public function getFullMap() {
        $models = [];
        return array_merge($models, $this->questionsFields());
    }

    /**
     * @return Field[]
     */
    private function questionsFields()
    {
        $models = [];
        if (!empty($this->survey->baseQuestions)) {
            foreach ($this->survey->baseQuestions as $question) {
                $this->question = $question;
                $field = new Field($question);
                $models[$field->name] = $field;
            }
        }
        return $models;
    }


}