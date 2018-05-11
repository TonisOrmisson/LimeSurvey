<?php

namespace ls\tests;

class FieldMapTest extends TestBaseClass
{
    // FIXME get a survey like that
    public static $surveyWithAllQuestionTypes = '88881';

    public function questionFieldTypeProvider()
    {
        return [
            [\QuestionType::QT_S_SHORT_FREE_TEXT, "text"],
            [\QuestionType::QT_T_LONG_FREE_TEXT, "text"],
            [\QuestionType::QT_U_HUGE_FREE_TEXT, "text"],

            [\QuestionType::QT_EXCLAMATION_LIST_DROPDOWN, "string(5)"],
            [\QuestionType::QT_L_LIST_DROPDOWN, "string(5)"],
            [\QuestionType::QT_O_LIST_WITH_COMMENT, "string(5)"],

            [\QuestionType::QT_C_ARRAY_YES_UNCERTAIN_NO, "string(5)"],

            // these sore actual data in subquestions, first match is parent with no field
            [\QuestionType::QT_B_ARRAY_10_CHOICE_QUESTIONS, null],
            [\QuestionType::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS, null],
        ];
    }
    /**
     * @param string $code QuestionType type code
     * @param string $expected
     * @dataProvider questionFieldTypeProvider
     */
    public function testQuestionFieldTypes($code, $expected)
    {
        $file = self::$surveysFolder.DIRECTORY_SEPARATOR.'limesurvey_survey_'.self::$surveyWithAllQuestionTypes.'.lss';
        parent::importSurvey($file);
        $fieldMap = new \FieldMap(self::$testSurvey);
        $fieldMap->getFullMap();
        $questions = $fieldMap->getQuestionsByType($code);
        $this->assertEquals($expected, $questions[0]->field->type);

    }
}