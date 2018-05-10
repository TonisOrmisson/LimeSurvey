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
        $this->survey = $survey;
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
                $questionFields = $this->createQuestionFields($question);
                $models = array_merge($models, $questionFields);
            }
        }
        return $models;
    }

    /**
     * Create Fields for question and all its subquestions
     * @param Question $question
     * @return Field[]
     */
    private function  createQuestionFields($question) {
        $result = [];
        $field = new Field($question);
        $result[$field->name] = $field;

        if ($question->hasSubQuestions) {
            foreach ($question->subquestions as $subqQuestion) {
                $subqQuestionField = $this->createSubQuestionField($subqQuestion);
                if (!empty($subqQuestionField)) {
                    $result[$subqQuestionField->name] = $subqQuestionField;
                }
            }
        }
        return $result;
    }

    /**
     * Create a Field for a subQuestion
     * @param Question $question
     * @return Field|null
     */
    private function createSubQuestionField($question)
    {
        // FIXME
        return null;
    }


}