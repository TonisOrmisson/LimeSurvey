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
        $models = $this->createGeneralStartFields();
        $models = array_merge($models, $this->questionsFields());
        return $models;
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
     * @return string[]
     */
    private function generalStartFieldNames() {
        $names = [
            Field::SYSFIELD_ID, Field::SYSFIELD_SUBMITDATE, Field::SYSFIELD_LASTPAGE,
            Field::SYSFIELD_STARTLANGUAGE, Field::SYSFIELD_SEED
        ];

        if (!$this->survey->isAnonymized) {
            $names[] = Field::SYSFIELD_TOKEN;
        }

        if (!$this->survey->isDateStamp) {
            $names[] = Field::SYSFIELD_STARTDATE;
            $names[] = Field::SYSFIELD_DATESTAMP;
        }

        if (!$this->survey->isIpAddr) {
            $names[] = Field::SYSFIELD_IP_ADDRESS;
        }
        if (!$this->survey->isIpAddr) {
            $names[] = Field::SYSFIELD_IP_ADDRESS;
        }
        if (!$this->survey->isRefUrl) {
            $names[] = Field::SYSFIELD_REFURL;
        }

        return $names;

    }

    /**
     * @return Field[]
     */
    private function createGeneralStartFields()
    {
        $result = [];
        foreach ($this->generalStartFieldNames() as $fieldName) {
            $field = new Field();
            $field->systemFieldName = $fieldName;
            $field->name = $fieldName;
            $result[$field->name] = $field;
        }
        return $result;

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