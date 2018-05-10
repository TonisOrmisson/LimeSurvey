<?php

/**
 * Class FieldMap describes the data-set (a set of data Fields /columns) of a Survey
 */
class FieldMap
{
    /** @var Survey */
    public $survey;

    /** @var string */
    public $language;

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
        $this->language = $survey->language;
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

        if ($this->survey->isDateStamp) {
            $names[] = Field::SYSFIELD_STARTDATE;
            $names[] = Field::SYSFIELD_DATESTAMP;
        }

        if ($this->survey->isIpAddr) {
            $names[] = Field::SYSFIELD_IP_ADDRESS;
        }
        if ($this->survey->isRefUrl) {
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
            $result[$field->name] = $field;
        }
        return $result;

    }

    /**
     * Create Fields for question and all its subquestions
     * @param Question $question
     * @return Field[]
     */
    private function createQuestionFields($question) {
        $result = [];
        if ($question->questionType->hasSubSets) {
            foreach ($question->subquestions as $subqQuestion) {
                if (!empty($subqQuestion->field)) {
                    $result[$subqQuestion->field->name] = $subqQuestion->field;
                }
            }
        } else {
            $result[$question->field->name] = $question->field;
        }
        return $result;
    }



}