<?php

namespace ls\tests;

class FieldMapTest extends TestBaseClass
{
    public static $surveyWithAllQuestionTypes = 'limesurvey_survey_all_question_types.lss';

    /** @var \FieldMap */
    public static $fieldMap;


    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
        $file = self::$surveysFolder.DIRECTORY_SEPARATOR.self::$surveyWithAllQuestionTypes;
        parent::importSurvey($file);
        self::$fieldMap = new \FieldMap(self::$testSurvey);
        self::$fieldMap->getFullMap();

    }

    public function questionFieldTypeProvider()
    {
        return [
            // Single choice questions
            [\QuestionType::QT_EXCLAMATION_LIST_DROPDOWN, "SL1", "string(5)"],
            [\QuestionType::QT_EXCLAMATION_LIST_DROPDOWN, "SL1o", "string(5)"],     // with other
            [\QuestionType::QT_EXCLAMATION_LIST_DROPDOWN, "SL1o-other", "text"],    // the "other" field
            [\QuestionType::QT_5_POINT_CHOICE, "SL2", "string(1)"],
            [\QuestionType::QT_L_LIST_DROPDOWN, "SL3", "string(5)"],
            [\QuestionType::QT_L_LIST_DROPDOWN, "SL3o", "string(5)"],
            [\QuestionType::QT_L_LIST_DROPDOWN, "SL3o-other", "text"],
        ];
    }


    /**
     * @param string $code fieldmap Question type code
     * @param string $key fieldmap key (Question full-title)
     * @param string $expected
     * @dataProvider questionFieldTypeProvider
     */
    public function testQuestionFieldTypes($code, $key, $expected)
    {
        $fieldMap = self::$fieldMap;
        if(!empty($expected)) {
            $field = $fieldMap->fields[$key];
            $this->assertEquals($expected, $field->type);
        } else {
            // must not have field
            $this->assertFalse(isset($fieldMap->fields[$key]));
        }
    }

    /**
     * @param string $code fieldmap Question type code
     * @param string $key fieldmap key (Question full-title)
     * @param string $expected
     * @dataProvider questionFieldTypeProvider
     */
    public function testQuestionFieldTypesMatchesQuestionCode($code, $key, $expected)
    {
        $fieldMap = self::$fieldMap;
        if(!empty($expected)) {
            $field = $fieldMap->fields[$key];
            $this->assertEquals($code, $field->question->type);
        } else {
            // must not have field
            $this->assertFalse(isset($fieldMap->fields[$key]));
        }
    }

}