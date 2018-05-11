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

    /** @var Field[] */
    public $fields;

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
        $this->createGeneralStartFields();
        $this->questionsFields();
        return $this->fields;
    }

    private function questionsFields()
    {
        foreach ($this->survey->baseQuestions as $question) {
            $this->createQuestionFields($question);
        }
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
     * @param Field|Field[] $fields
     */
    private function addFields($fields) {
        if (!empty($fields)) {
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $this->addFields($field);
                }
            } else {
                $this->fields[$fields->name] = $fields;
            }
        }
    }

    private function createGeneralStartFields()
    {
        foreach ($this->generalStartFieldNames() as $fieldName) {
            $field = new Field();
            $field->systemFieldName = $fieldName;
            $this->addFields($field);
        }
    }

    /**
     * Create Fields for question and all its subquestions
     * @param Question $question
     * @return Field[]
     */
    private function createQuestionFields($question) {
        // all parent questions go into recursion
        if ($question->questionType->hasSubSets && !$question->hasParent) {
            foreach ($question->subquestions as $subqQuestion) {
                $this->createQuestionFields($subqQuestion);
            }
        } else {
            $this->addFields($question->field);
            $this->createCommentFields($question);
        }
    }


    /**
     * Create Fields for question and all its subquestions
     * @param Question $question
     */
    private function createCommentFields($question) {
        if ($question->questionType->hasComment) {
            $field = new Field($question);
            $field->isCommentField = true;
            $this->addFields($field);
        }
    }



}