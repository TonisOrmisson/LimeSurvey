<?php

/**
 * Class Field describes a column in responses data table
 * @property string $type technical type for DB
 * @property string $name fieldName - actual column name in DB
 * @property string $tokenFieldCollation the suitable collation for token field
 */
class Field extends CModel
{

    const TYPE_STRING = 'string';
    const TYPE_CHAR = 'char';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE = 'double';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'datetime';

    const DEFAULT_STRING_LENGTH = 5;
    const DEFAULT_DOUBLE_LENGTH = 30;
    const DEFAULT_DOUBLE_DECIMALS = 10;

    const SYSFIELD_ID = 'id';
    const SYSFIELD_TOKEN = 'token';
    const SYSFIELD_SEED = 'seed';
    const SYSFIELD_STARTLANGUAGE = 'startlanguage';
    const SYSFIELD_STARTDATE = 'startdate';
    const SYSFIELD_SUBMITDATE = 'submitdate';
    const SYSFIELD_DATESTAMP = 'datestamp';
    const SYSFIELD_LASTPAGE = 'lastpage';
    const SYSFIELD_REFURL = 'refurl';
    const SYSFIELD_IP_ADDRESS = 'ipaddr';


    /** @var Question */
    public $question;

    /** @var string $systemFieldName Name for non-question field */
    public $systemFieldName;


    /** @var string */
    public $id;

    /** @var bool $isCommentField  */
    public $isCommentField = false;

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
        return ['name'];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
            if ($this->isCommentField) {
                return "text";
            }
            return $this->question->questionType->fieldType;
        } else {
            return $this->systemFieldType();
        }
    }


    /**
     * @return string
     * @throws Exception
     */
    private function systemFieldType()
    {
        switch ($this->systemFieldName) {
            case self::SYSFIELD_ID:
                return "pk";
                break;
            case self::SYSFIELD_SEED:
                return "string(31)";
                break;
            case self::SYSFIELD_STARTLANGUAGE:
                return "string(31) string(20) NOT NULL";
                break;
            case self::SYSFIELD_STARTDATE:
            case self::SYSFIELD_DATESTAMP:
                return "datetime NOT NULL";
                break;
            case self::SYSFIELD_SUBMITDATE:
                return "datetime";
                break;
            case self::SYSFIELD_LASTPAGE:
                return "integer";
                break;
            case self::SYSFIELD_TOKEN:
                return "string(35) " . $this->tokenFieldCollation;
                break;
            case self::SYSFIELD_REFURL:
                return "text";
                break;
            case self::SYSFIELD_IP_ADDRESS:
                return "string(45)";
                break;
            default:
                throw new \Exception("Undefined system column {$this->systemFieldName}");
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getName()
    {
        if (!empty($this->question)) {
            return $this->questionFieldName();
        } elseif (!empty($this->systemFieldName)) {
            return $this->systemFieldName;
        }
        throw new \Exception('Either question or systemFieldName must be defined for Field');
    }

    /**
     * @return string
     */
    private function questionFieldName()
    {
        $name = $this->questionBaseFieldName();
        if (($this->question->questionType->hasSubSets && $this->question->hasParent)
                //TODO please document  what that means!
            || ($this->question->type === QuestionType::QT_T_LONG_FREE_TEXT && $this->question->hasParent)) {
            $name .= $this->question->title;
        }

        if ($this->isCommentField) {
            $name .="comment";
        }

        return $name;
    }

    /**
     * @return string
     */
    private function questionBaseFieldName()
    {
        $question = $this->question;
        if ($question->hasParent) {
            return "{$question->sid}X{$question->gid}X{$question->parent_qid}";
        } else {
            return "{$question->sid}X{$question->gid}X{$question->qid}";
        }
    }





    /**
     * @return string
     * @throws Exception
     */
    public function getTokenFieldCollation()
    {
        $driverName = Yii::app()->db->driverName;
        if ($driverName == 'mysqli' || $driverName == 'mysql') {
            return " COLLATE 'utf8mb4_bin'";
        }
        if ($driverName == 'sqlsrv' || $driverName == 'dblib' || $driverName == 'mssql') {
            return " COLLATE SQL_Latin1_General_CP1_CS_AS";
        }
        throw new \Exception('Unsupported database engine ' . $driverName);

    }



}