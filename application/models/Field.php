<?php

/**
 * Class Field describes a column in responses data table
 * @property string $type technical type for DB
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
    const SYSFIELD_URL = 'url';
    const SYSFIELD_IP_ADDRESS = 'ipaddress';


    /** @var Question */
    public $question;

    /** @var string $systemFieldName Name for non-question field */
    public $systemFieldName;

    /** @var string $name Field column name */
    public $name;

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
                return "string(35) " . Token::model()->tokenFieldCollation;
                break;
            case self::SYSFIELD_URL:
                return "text";
                break;
            case self::SYSFIELD_IP_ADDRESS:
                return "string(45)";
                break;
            default:
                throw new \Exception("Undefined system column {$this->systemFieldName}");
        }
    }







}