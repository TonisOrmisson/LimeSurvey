<?php

namespace ls\tests;

class FieldMapTest extends TestBaseClass
{
    // FIXME get a survey like that
    public static $surveyWithAllQuestionTypes = 'ls205_sample_survey_english.lss';
    /**
     *
     */
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
        $file = self::$demoSurveysFolder.DIRECTORY_SEPARATOR.self::$surveyWithAllQuestionTypes;
        parent::importSurvey($file);
    }

    public function questionFieldTypeProvider()
    {
        return [

            [\QuestionType::QT_S_SHORT_FREE_TEXT, "city", "text"],
            [\QuestionType::QT_N_NUMERICAL, "yearsThere", "decimal(30,10)"],
            [\QuestionType::QT_R_RANKING_STYLE, "ranking", "string(5)"],

            [\QuestionType::QT_E_ARRAY_OF_INC_SAME_DEC_QUESTIONS, "opinions", null],
            // TODO CHILDREN OF "opinions"

            //[\QuestionType::QT_T_LONG_FREE_TEXT, "Q2", "text"],

            //[\QuestionType::QT_U_HUGE_FREE_TEXT, "text"],

            //[\QuestionType::QT_EXCLAMATION_LIST_DROPDOWN, "string(5)"],
            //[\QuestionType::QT_L_LIST_DROPDOWN, "string(5)"],
            //[\QuestionType::QT_O_LIST_WITH_COMMENT, "string(5)"],

            // these sore actual data in subquestions, first match is parent with no field
            //[\QuestionType::QT_B_ARRAY_10_CHOICE_QUESTIONS, "string(5)"],
            //[\QuestionType::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS, null],
            //[\QuestionType::QT_C_ARRAY_YES_UNCERTAIN_NO, null],
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
        $fieldMap = new \FieldMap(self::$testSurvey);
        $fieldMap->getFullMap();
        if(!empty($expected)) {
            $field = $fieldMap->fields[$key];
            $this->assertEquals($expected, $field->type);
        } else {
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
        $fieldMap = new \FieldMap(self::$testSurvey);
        $fieldMap->getFullMap();
        if(!empty($expected)) {
            $field = $fieldMap->fields[$key];
            $this->assertEquals($code, $field->question->type);
        } else {
            // no field, nothing to test
            $this->assertTrue(true);
        }
    }

}