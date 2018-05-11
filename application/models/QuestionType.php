<?php

/**
 * Class QuestionType
 * @property Question $question
 * @property string $fieldType The type of field question needs for storing data
 * @property string $fieldDataType numeric vs string types
 * @property boolean $isText Whether the type is text (string longer than char)
 * @property boolean $isChar Whether the type is char (one-character-string)
 * @property boolean $isString Whether the type is string (text or char)
 * @property boolean $isNumeric Whether the type numeric (integer, double)
 * @property boolean $isInteger Whether the type integer
 * @property boolean $isDouble Whether the type double
 * @property boolean $hasSubSets Whether type has any subquestion sets
 * @property boolean $hasComment Whether type has additional comment field
 *
 * {@inheritdoc}
 */
class QuestionType extends StaticModel
{
    const QT_1_ARRAY_MULTISCALE = '1'; //ARRAY (Flexible Labels) multi scale
    const QT_5_POINT_CHOICE = '5';
    const QT_A_ARRAY_5_CHOICE_QUESTIONS = 'A'; // ARRAY OF 5 POINT CHOICE QUESTIONS
    const QT_B_ARRAY_10_CHOICE_QUESTIONS = 'B'; // ARRAY OF 10 POINT CHOICE QUESTIONS
    const QT_C_ARRAY_YES_UNCERTAIN_NO = 'C'; // ARRAY OF YES\No\gT("Uncertain") QUESTIONS
    const QT_D_DATE = 'D';
    const QT_E_ARRAY_OF_INC_SAME_DEC_QUESTIONS = 'E';
    const QT_F_ARRAY_FLEXIBLE_ROW = 'F';
    const QT_G_GENDER_DROPDOWN = 'G';
    const QT_H_ARRAY_FLEXIBLE_COLUMN = 'H';
    const QT_I_LANGUAGE = 'I';
    const QT_K_MULTIPLE_NUMERICAL_QUESTION = 'K';
    const QT_L_LIST_DROPDOWN = 'L';
    const QT_M_MULTIPLE_CHOICE = 'M';
    const QT_N_NUMERICAL = 'N';
    const QT_O_LIST_WITH_COMMENT = 'O';
    const QT_P_MULTIPLE_CHOICE_WITH_COMMENTS = 'P';
    const QT_Q_MULTIPLE_SHORT_TEXT = 'Q';
    const QT_R_RANKING_STYLE = 'R';
    const QT_S_SHORT_FREE_TEXT = 'S';
    const QT_T_LONG_FREE_TEXT = 'T';
    const QT_U_HUGE_FREE_TEXT = 'U';
    const QT_X_BOILERPLATE_QUESTION = 'X';
    const QT_Y_YES_NO_RADIO = 'Y';
    const QT_Z_LIST_RADIO_FLEXIBLE = 'Z';
    const QT_EXCLAMATION_LIST_DROPDOWN = '!';
    const QT_VERTICAL_FILE_UPLOAD = '|';
    const QT_ASTERISK_EQUATION = '*';
    const QT_COLON_ARRAY_MULTI_FLEX_NUMBERS = ':';
    const QT_SEMICOLON_ARRAY_MULTI_FLEX_TEXT = ';';

    /** @var Question */
    public $question;

    /** @var string $code */
    public $code;

    /** @var string $description */
    public $description;

    /** @var string $group Group name*/
    public $group;

    /** @var integer $subquestions how many subquestion sets question has 0|1|2 */
    public $subquestions;

    /** @var integer $assessable //TODO make it boolean instead */
    public $assessable;

    /** @var integer $hasdefaultvalues //TODO make it boolean instead */
    public $hasdefaultvalues;

    /** @var integer $answerscales number of answer scales 0|1|2 */
    public $answerscales;

    /** @var string $class the css class for question (container??)*/
    public $class;


    /**
     * {@inheritdoc}
     */
    public function attributeNames()
    {
        return ['code', 'description', 'group', 'subquestions', 'assessable',
            'hasdefaultvalues', 'answerscales', 'class'];
    }

    public function rules()
    {
        return [
            ['subquestions, assessable, hasdefaultvalues, answerscales','numerical', 'integerOnly'=>true],
            ['code, description, group, class', 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => gT("Code"),
            'description' => gT("Description"),
            'group' => gT("Group"),
        ];
    }


    public static function modelsAttributes()
    {
        return [
            self::QT_1_ARRAY_MULTISCALE => [
                'code' => self::QT_1_ARRAY_MULTISCALE,
                'description' => gT("Array dual scale"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'assessable' => 1,
                'hasdefaultvalues' => 0,
                'answerscales' => 2,
                'class' => 'array-flexible-duel-scale',
            ],
            self::QT_5_POINT_CHOICE => [
                'code' => self::QT_5_POINT_CHOICE,
                'description' => gT("5 Point Choice"),
                'group' => gT("Single choice questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => "choice-5-pt-radio"
            ],
            self::QT_A_ARRAY_5_CHOICE_QUESTIONS => [
                'code' => self::QT_A_ARRAY_5_CHOICE_QUESTIONS,
                'description' => gT("Array (5 Point Choice)"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'array-5-pt'
            ],
            self::QT_B_ARRAY_10_CHOICE_QUESTIONS => [
                'code' => self::QT_B_ARRAY_10_CHOICE_QUESTIONS,
                'description' => gT("Array (10 Point Choice)"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'array-10-pt'
            ],
            self::QT_C_ARRAY_YES_UNCERTAIN_NO => [
                'code' => self::QT_C_ARRAY_YES_UNCERTAIN_NO,
                'description' => gT("Array (Yes/No/Uncertain)"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'array-yes-uncertain-no'
            ],
            self::QT_D_DATE => [
                'code' => self::QT_D_DATE,
                'description' => gT("Date/Time"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'date'
            ],
            self::QT_E_ARRAY_OF_INC_SAME_DEC_QUESTIONS => [
                'code' => self::QT_E_ARRAY_OF_INC_SAME_DEC_QUESTIONS,
                'description' => gT("Array (Increase/Same/Decrease)"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'array-increase-same-decrease'
            ],
            self::QT_F_ARRAY_FLEXIBLE_ROW => [
                'code' => self::QT_F_ARRAY_FLEXIBLE_ROW,
                'description' => gT("Array"),
                'group' => gT('Arrays'),
                'subquestions' => 1,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'array-flexible-row'
            ],
            self::QT_G_GENDER_DROPDOWN => [
                'code' => self::QT_G_GENDER_DROPDOWN,
                'description' => gT("Gender"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'gender'
            ],
            self::QT_H_ARRAY_FLEXIBLE_COLUMN => [
                'code' => self::QT_H_ARRAY_FLEXIBLE_COLUMN,
                'description' => gT("Array by column"),
                'group' => gT('Arrays'),
                'hasdefaultvalues' => 0,
                'subquestions' => 1,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'array-flexible-column'
            ],
            self::QT_I_LANGUAGE => [
                'code' => self::QT_I_LANGUAGE,
                'description' => gT("Language Switch"),
                'group' => gT("Mask questions"),
                'hasdefaultvalues' => 0,
                'subquestions' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'language'
            ],
            self::QT_K_MULTIPLE_NUMERICAL_QUESTION => [
                'code' => self::QT_K_MULTIPLE_NUMERICAL_QUESTION,
                'description' => gT("Multiple Numerical Input"),
                'group' => gT("Mask questions"),
                'hasdefaultvalues' => 1,
                'subquestions' => 1,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'numeric-multi'
            ],
            self::QT_L_LIST_DROPDOWN => [
                'code' => self::QT_L_LIST_DROPDOWN,
                'description' => gT("List (Radio)"),
                'group' => gT("Single choice questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'list-radio'
            ],
            self::QT_M_MULTIPLE_CHOICE => [
                'code' => self::QT_M_MULTIPLE_CHOICE,
                'description' => gT("Multiple choice"),
                'group' => gT("Multiple choice questions"),
                'subquestions' => 1,
                'hasdefaultvalues' => 1,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'multiple-opt'
            ],
            self::QT_N_NUMERICAL => [
                'code' => self::QT_N_NUMERICAL,
                'description' => gT("Numerical Input"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'numeric'
            ],
            self::QT_O_LIST_WITH_COMMENT => [
                'code' => self::QT_O_LIST_WITH_COMMENT,
                'description' => gT("List with comment"),
                'group' => gT("Single choice questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'list-with-comment'
            ],
            self::QT_P_MULTIPLE_CHOICE_WITH_COMMENTS => [
                'code' => self::QT_P_MULTIPLE_CHOICE_WITH_COMMENTS,
                'description' => gT("Multiple choice with comments"),
                'group' => gT("Multiple choice questions"),
                'subquestions' => 1,
                'hasdefaultvalues' => 1,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'multiple-opt-comments'
            ],
            self::QT_Q_MULTIPLE_SHORT_TEXT => [
                'code' => self::QT_Q_MULTIPLE_SHORT_TEXT,
                'description' => gT("Multiple Short Text"),
                'group' => gT("Text questions"),
                'subquestions' => 1,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'multiple-short-txt'
            ],
            self::QT_R_RANKING_STYLE => [
                'code' => self::QT_R_RANKING_STYLE,
                'description' => gT("Ranking"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'ranking'
            ],
            self::QT_S_SHORT_FREE_TEXT => [
                'code' => self::QT_S_SHORT_FREE_TEXT,
                'description' => gT("Short Free Text"),
                'group' => gT("Text questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'text-short'
            ],
            self::QT_T_LONG_FREE_TEXT => [
                'code' => self::QT_T_LONG_FREE_TEXT,
                'description' => gT("Long Free Text"),
                'group' => gT("Text questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'text-long'
            ],
            self::QT_U_HUGE_FREE_TEXT => [
                'code' => self::QT_U_HUGE_FREE_TEXT,
                'description' => gT("Huge Free Text"),
                'group' => gT("Text questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'text-huge'
            ],
            self::QT_X_BOILERPLATE_QUESTION => [
                'code' => self::QT_X_BOILERPLATE_QUESTION,
                'description' => gT("Text display"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'boilerplate'
            ],
            self::QT_Y_YES_NO_RADIO => [
                'code' => self::QT_Y_YES_NO_RADIO,
                'description' => gT("Yes/No"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'yes-no'
            ],
            self::QT_EXCLAMATION_LIST_DROPDOWN => [
                'code' => self::QT_EXCLAMATION_LIST_DROPDOWN,
                'description' => gT("List (Dropdown)"),
                'group' => gT("Single choice questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 1,
                'assessable' => 1,
                'answerscales' => 1,
                'class' => 'list-dropdown'
            ],
            self::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS => [
                'code' => self::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS,
                'description' => gT("Array (Numbers)"),
                'group' => gT('Arrays'),
                'subquestions' => 2,
                'hasdefaultvalues' => 0,
                'assessable' => 1,
                'answerscales' => 0,
                'class' => 'array-multi-flexi'
            ],
            self::QT_SEMICOLON_ARRAY_MULTI_FLEX_TEXT => [
                'code' => self::QT_SEMICOLON_ARRAY_MULTI_FLEX_TEXT,
                'description' => gT("Array (Texts)"),
                'group' => gT('Arrays'),
                'subquestions' => 2,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'array-multi-flexi-text'
            ],
            self::QT_VERTICAL_FILE_UPLOAD => [
                'code' => self::QT_VERTICAL_FILE_UPLOAD,
                'description' => gT("File upload"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'upload-files'
            ],
            self::QT_ASTERISK_EQUATION => [
                'code' => self::QT_ASTERISK_EQUATION,
                'description' => gT("Equation"),
                'group' => gT("Mask questions"),
                'subquestions' => 0,
                'hasdefaultvalues' => 0,
                'assessable' => 0,
                'answerscales' => 0,
                'class' => 'equation'
            ],
        ];
    }

    /**
     * Get all type codes of that can be used in Quotas
     * @return string[]
     */
    public static function quotableCodes()
    {
        return [ self::QT_G_GENDER_DROPDOWN, self::QT_M_MULTIPLE_CHOICE, self::QT_Y_YES_NO_RADIO,
            self::QT_A_ARRAY_5_CHOICE_QUESTIONS, self::QT_B_ARRAY_10_CHOICE_QUESTIONS,
            self::QT_I_LANGUAGE, self::QT_L_LIST_DROPDOWN, self::QT_O_LIST_WITH_COMMENT,
            self::QT_EXCLAMATION_LIST_DROPDOWN, self::QT_ASTERISK_EQUATION
        ];
    }


    /**
     * Get all type codes of that represent data in text (string longer than char)
     * @return string[]
     */
    public static function textCodes()
    {
        return [
            self::QT_I_LANGUAGE, self::QT_S_SHORT_FREE_TEXT, self::QT_U_HUGE_FREE_TEXT,
            self::QT_Q_MULTIPLE_SHORT_TEXT, self::QT_T_LONG_FREE_TEXT, self::QT_SEMICOLON_ARRAY_MULTI_FLEX_TEXT,
            self::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS, self::QT_ASTERISK_EQUATION
        ];
    }


    /**
     * Get all type codes of that represent data in char (one-character-string)
     * @return string[]
     */
    public static function charCodes()
    {
        return [
            self::QT_5_POINT_CHOICE, self::QT_G_GENDER_DROPDOWN, self::QT_Y_YES_NO_RADIO

        ];
    }


    /**
     * Get all type codes of that represent data in string (text and char)
     * @return string[]
     */
    public static function stringCodes()
    {
        return array_merge(self::textCodes(), self::charCodes());
    }



    /**
     * Get all type codes of that represent data as integer
     * @return string[]
     */
    public static function integerCodes()
    {
        return [
            self::QT_VERTICAL_FILE_UPLOAD
        ];

    }

    /**
     * Get all type codes of that represent data as double
     * @return string[]
     */
    public static function doubleCodes()
    {
        return [self::QT_N_NUMERICAL, self::QT_K_MULTIPLE_NUMERICAL_QUESTION];
    }

    /**
     * Get all type codes of that represent data as double
     * @return string[]
     */
    public static function numericCodes()
    {
        return array_merge(self::integerCodes(), self::doubleCodes());
    }

    /**
     * get Codes of questiion Types that have NO sets of subquestions
     * @return array
     */
    public static function withNoSubSetCodes() {
        return self::findColumnByAttributeValue('subquestions',0,'code');
    }

    /**
     * get Codes of questiion Types that have one set of subquestions
     * @return array
     */
    public static function withOneSubSetCodes() {
        return self::findColumnByAttributeValue('subquestions',1,'code');
    }


    /**
     * get Codes of questiion Types that have TWO sets of subquestions
     * @return array
     */
    public static function withTwoSubSetCodes() {
        return self::findColumnByAttributeValue('subquestions',2,'code');
    }

    /**
     * get Codes of questiion Types that have ANY sets of subquestions
     * @return array
     */
    public static function withSubSetCodes() {
        return self::findColumnByAttributeValue('subquestions',[1,2],'code');
    }

    /**
     * get Codes of questiion Types that have additional comment field
     * @return array
     */
    public static function withCommentCodes()
    {
        return [
          self::QT_O_LIST_WITH_COMMENT,
          self::QT_P_MULTIPLE_CHOICE_WITH_COMMENTS,
        ];
    }


    /**
     * @return bool
     */
    public function getHasSubSets()
    {
        return in_array($this->code, self::withSubSetCodes());
    }

    /**
     * @return bool
     */
    public function getHasComment()
    {
        return in_array($this->code, self::withCommentCodes());
    }




    /**
     * @return bool
     */
    public function getIsText()
    {
        return in_array($this->code, self::textCodes());
    }

    /**
     * @return bool
     */
    public function getIsChar()
    {
        return in_array($this->code, self::charCodes());
    }

    /**
     * @return bool
     */
    public function getIsString()
    {
        return in_array($this->code, self::charCodes());
    }

    /**
     * @return bool
     */
    public function getIsInteger()
    {
        return in_array($this->code, self::integerCodes());
    }

    /**
     * @return bool
     */
    public function getIsDouble()
    {
        return in_array($this->code, self::doubleCodes());
    }

    /**
     * @return bool
     */
    public function getIsNumeric()
    {
        return in_array($this->code, self::numericCodes());
    }


    /**
     * @return string
     */
    public function getFieldType()
    {
        if ($this->isDouble) {
            return "decimal(" . Field::DEFAULT_DOUBLE_LENGTH . "," . Field::DEFAULT_DOUBLE_DECIMALS . ")";
        }

        if ($this->isText) {
            return "text";
        }
        if ($this->isChar) {
            return "string(1)";
        }

        if ($this->code === self::QT_D_DATE) {
            return "datetime";
        }
        if ($this->code === self::QT_I_LANGUAGE) {
            return "string(20)";
        }

        if ($this->code === self::QT_X_BOILERPLATE_QUESTION) {
            return null;
        }


        return "string(" . Field::DEFAULT_STRING_LENGTH . ")";
    }


    /**
     * //TODO delete???
     * @return string
     */
    public function getFieldDataType()
    {
        if ($this->isString) {
            return Field::TYPE_STRING;
        }
        if ($this->isInteger) {
            return Field::TYPE_INTEGER;
        }

        throw new \Exception("Undefined field data type for QuestionType {$this->code}");
    }



    /**
     * The old logic
     * //TODO delete me when new is ready
     * @param $aRow
     */
    private function old($aRow)
    {

        switch ($aRow['type']) {
            case "lastpage":
                $aTableDefinition[$aRow['fieldname']] = "integer";
                break;
            case 'id':
                $aTableDefinition[$aRow['fieldname']] = "pk";
                break;
            case 'startlanguage':
                $aTableDefinition[$aRow['fieldname']] = "string(20) NOT NULL";
                break;
            case 'seed':
                $aTableDefinition[$aRow['fieldname']] = "string(31)";
                break;
            case "startdate":
            case "datestamp":
                $aTableDefinition[$aRow['fieldname']] = "datetime NOT NULL";
                break;
            case "submitdate":
                $aTableDefinition[$aRow['fieldname']] = "datetime";
                break;
            case Question::QT_N_NUMERICAL:
            case Question::QT_K_MULTIPLE_NUMERICAL_QUESTION:
                $aTableDefinition[$aRow['fieldname']] = "decimal (30,10)";
                break;
            case Question::QT_S_SHORT_FREE_TEXT:
            case Question::QT_U_HUGE_FREE_TEXT:
            case Question::QT_Q_MULTIPLE_SHORT_TEXT:
            case Question::QT_T_LONG_FREE_TEXT:
            case Question::QT_SEMICOLON_ARRAY_MULTI_FLEX_TEXT:
            case Question::QT_COLON_ARRAY_MULTI_FLEX_NUMBERS:
                $aTableDefinition[$aRow['fieldname']] = "text";
                break;
            case Question::QT_5_POINT_CHOICE:
            case Question::QT_G_GENDER_DROPDOWN:
            case Question::QT_Y_YES_NO_RADIO:
            case Question::QT_X_BOILERPLATE_QUESTION:
                $aTableDefinition[$aRow['fieldname']] = "string(1)";
                break;
            case Question::QT_D_DATE:
                $aTableDefinition[$aRow['fieldname']] = "datetime";
                break;
            case Question::QT_I_LANGUAGE:
                $aTableDefinition[$aRow['fieldname']] = "string(20)";
                break;
            case "token":
                $aTableDefinition[$aRow['fieldname']] = 'string(35)'.$this->collation;
                break;
            case "url":
                if ($this->survey->isRefUrl) {
                    $aTableDefinition[$aRow['fieldname']] = "text";
                }
                break;
            case "ipaddress":
                if ($this->survey->isIpAddr) {
                    $aTableDefinition[$aRow['fieldname']] = "text";
                }
                break;
            case Question::QT_ASTERISK_EQUATION:
                $aTableDefinition[$aRow['fieldname']] = "text";
                break;


            case Question::QT_L_LIST_DROPDOWN:
            case Question::QT_EXCLAMATION_LIST_DROPDOWN:
            case Question::QT_M_MULTIPLE_CHOICE:
            case Question::QT_P_MULTIPLE_CHOICE_WITH_COMMENTS:
            case Question::QT_O_LIST_WITH_COMMENT:
                if ($aRow['aid'] != 'other' && strpos($aRow['aid'], 'comment') === false && strpos($aRow['aid'], 'othercomment') === false) {
                    $aTableDefinition[$aRow['fieldname']] = "string(5)";
                } else {
                    $aTableDefinition[$aRow['fieldname']] = "text";
                }
                break;
            case Question::QT_VERTICAL_FILE_UPLOAD:
                $this->createSurveyDir = true;
                if (strpos($aRow['fieldname'], "_")) {
                    $aTableDefinition[$aRow['fieldname']] = "integer";
                } else {
                    $aTableDefinition[$aRow['fieldname']] = "text";
                }
                break;

            case Question::QT_R_RANKING_STYLE:
                /**
                 * See bug #09828: Ranking question : update allowed can broke Survey DB
                 * If max_subquestions is not set or is invalid : set it to actual answers numbers
                 */

                $nrOfAnswers = Answer::model()->countByAttributes(
                    array('qid' => $aRow['qid'])
                );
                $oQuestionAttribute = QuestionAttribute::model()->find(
                    "qid = :qid AND attribute = 'max_subquestions'",
                    array(':qid' => $aRow['qid'])
                );
                if (empty($oQuestionAttribute)) {
                    $oQuestionAttribute = new QuestionAttribute();
                    $oQuestionAttribute->qid = $aRow['qid'];
                    $oQuestionAttribute->attribute = 'max_subquestions';
                    $oQuestionAttribute->value = $nrOfAnswers;
                    $oQuestionAttribute->save();
                } elseif (intval($oQuestionAttribute->value) < 1) {
                    // Fix it if invalid : disallow 0, but need a sub question minimum for EM
                    $oQuestionAttribute->value = $nrOfAnswers;
                    $oQuestionAttribute->save();
                }
                $aTableDefinition[$aRow['fieldname']] = "string(5)";
                break;
            default:
                $aTableDefinition[$aRow['fieldname']] = "string(5)";
        }
    }

}