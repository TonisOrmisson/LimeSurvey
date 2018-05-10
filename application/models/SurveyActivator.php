<?php


class SurveyActivator
{
    /** @var Survey */
    protected $survey;
    /** @var array  */
    protected $tableDefinition = [];
    /** @var array  */
    protected $timingsTableDefinition = [];
    /** @var FieldMap  */
    protected $fieldMap;
    /** @var string */
    protected $collation;
    /** @var PluginEvent */
    protected $event;
    /** @var string */
    protected $error;
    /** @var bool */
    protected $createSurveyDir = false;


    /** @var boolean */
    public $isSimulation;


    /**
     * @param Survey $survey
     */
    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return array
     * @throws CException
     */
    public function activate()
    {

        $this->event = new PluginEvent('beforeSurveyActivate');
        $this->event->set('surveyId', $this->survey->primaryKey);
        $this->event->set('simulate', $this->isSimulation);
        App()->getPluginManager()->dispatchEvent($this->event);

        if (!$this->showEventMessages()) {
            return ['error'=>'plugin'];
        }

        $this->prepareResponsesTable();

        if ($this->isSimulation) {
            return array('dbengine'=>Yii::app()->db->getDriverName(), 'dbtype'=>Yii::app()->db->driverName, 'fields'=>$this->tableDefinition);
        }

        if (!$this->createParticipantsTable()) {
            return ['error'=>$this->error];
        }

        if (!$this->createTimingsTable()) {
            return ['error'=>'timingstablecreation'];
        }

        if (!empty($this->error)) {
            return ['error'=>$this->error];
        }

        Yii::app()->db->createCommand()->update(
                Survey::model()->tableName(),
                ['active'=>'Y'], 'sid=:sid',
                [':sid'=>$this->survey->primaryKey]
            );

        $aResult = array(
            'status' => 'OK',
            'pluginFeedback' => $this->event->get('pluginFeedback')
        );
        if (!$this->createSurveyDirectory()) {
            $aResult['warning'] = 'nouploadsurveydir';
        }

        return $aResult;
    }



    /**
     * For each question, create the appropriate field(s)
     * @return void
     */
    protected function prepareTableDefinition()
    {
        $this->tableDefinition = [];
        foreach ($this->fieldMap->getFullMap() as $field) {
            $this->tableDefinition[$field->name] = $field->type;
        }
    }

    /**
     * @return void
     */
    protected function prepareTimingsTable()
    {
        $timingsfieldmap = createTimingsFieldMap($this->survey->primaryKey, "full", false, false, $this->survey->language);
        $aTimingTableDefinition = array();
        $aTimingTableDefinition['id'] = $this->tableDefinition['id'];
        foreach ($timingsfieldmap as $field=>$fielddata) {
            $aTimingTableDefinition[$field] = 'FLOAT';
        }
        $this->timingsTableDefinition = $aTimingTableDefinition;
    }



    /**
     * @return void
     */
    protected function prepareCollation()
    {
        $this->collation = TokenDynamic::model()->tokenFieldCollation;
    }


    /**
     * @return void
     */
    protected function prepareSimulateQuery()
    {
        if ($this->isSimulation) {
            $tempTrim = trim($this->tableDefinition);
            $brackets = strpos($tempTrim, "(");
            if ($brackets === false) {
                $type = substr($tempTrim, 0, 2);
            } else {
                $type = substr($tempTrim, 0, 2);
            }
            $arrSim[] = array($type);
            $this->tableDefinition = $arrSim;
        }

    }


    /**
     * @return void
     */
    protected function prepareResponsesTable()
    {
        $this->prepareCollation();
        //Check for any additional fields for this survey and create necessary fields (token and datestamp)
        $this->survey->fixInvalidQuestions();
        //Get list of questions for the base language
        $this->fieldMap = createFieldMap($this->survey, 'full', true, false, $this->survey->language);
        $this->fieldMap = new FieldMap($this->survey);

        $this->prepareTableDefinition();
        $this->prepareSimulateQuery();
    }


    /**
     * @return boolean
     * @throws CDbException
     * @throws CException
     */
    protected function createParticipantsTable()
    {
        $sTableName = $this->survey->responsesTableName;
        Yii::app()->loadHelper("database");
        try {
            Yii::app()->db->createCommand()->createTable($sTableName, $this->tableDefinition);
            Yii::app()->db->schema->getTable($sTableName, true); // Refresh schema cache just in case the table existed in the past
        } catch (Exception $e) {
            if (App()->getConfig('debug')) {
                $this->error = $e->getMessage();
            } else {
                $this->error = 'surveytablecreation';
            }
            return false;
        }
        try {
            if (isset($aTableDefinition['token'])) {
                Yii::app()->db->createCommand()->createIndex("idx_survey_token_{$this->survey->primaryKey}_".rand(1, 50000), $sTableName, 'token');
            }
        } catch (\Exception $e) {
        }

        $this->createParticipantsTableKeys();
        return true;

    }


    /**
     * @return boolean
     */
    protected function showEventMessages()
    {
        $success = $this->event->get('success');
        $message = $this->event->get('message');

        if ($success === false) {
            Yii::app()->user->setFlash('error', $message);
            return false;
        } else if (!empty($message)) {
            Yii::app()->user->setFlash('info', $message);
        }
        return true;

    }

    /**
     * @return void
     * @throws CDbException
     * @throws CException
     */
    protected function createParticipantsTableKeys()
    {
        $iAutoNumberStart = Yii::app()->db->createCommand()
            ->select('autonumber_start')
            ->from(Survey::model()->tableName())
            ->where('sid=:sid', [':sid'=>$this->survey->primaryKey])
            ->queryScalar();

        //if there is an autonumber_start field, start auto numbering here
        if ($iAutoNumberStart !== false && $iAutoNumberStart > 0) {
            if (Yii::app()->db->driverName == 'mssql' || Yii::app()->db->driverName == 'sqlsrv' || Yii::app()->db->driverName == 'dblib') {
                mssql_drop_primary_index($this->survey->responsesTableName);
                mssql_drop_constraint('id', $this->survey->responsesTableName);
                $sQuery = "ALTER TABLE {$this->survey->responsesTableName} drop column id ";
                Yii::app()->db->createCommand($sQuery)->execute();
                $sQuery = "ALTER TABLE {$this->survey->responsesTableName} ADD [id] int identity({$iAutoNumberStart},1)";
                Yii::app()->db->createCommand($sQuery)->execute();
                // Add back the primaryKey

                Yii::app()->db->createCommand()->addPrimaryKey('PRIMARY_'.rand(1, 50000), $this->survey->responsesTableName, 'id');
            } elseif (Yii::app()->db->driverName == 'pgsql') {
                $sQuery = "SELECT setval(pg_get_serial_sequence('{$this->survey->responsesTableName}', 'id'),{$iAutoNumberStart},false);";
                // FIXME @ not good
                @Yii::app()->db->createCommand($sQuery)->execute();
            } else {
                $sQuery = "ALTER TABLE {$this->survey->responsesTableName} AUTO_INCREMENT = {$iAutoNumberStart}";
                // FIXME @ not good
                @Yii::app()->db->createCommand($sQuery)->execute();
            }
        }

    }

    /**
     * @return boolean
     */
    protected function createTimingsTable()
    {
        if ($this->survey->isSaveTimings) {
            $this->prepareTimingsTable();
            $sTableName = $this->survey->timingsTableName;
            try {
                Yii::app()->db->createCommand()->createTable($sTableName, $this->timingsTableDefinition);
                Yii::app()->db->schema->getTable($sTableName, true); // Refresh schema cache just in case the table existed in the past
            } catch (\Exception $e) {
                throw $e;
            }

        }
        return true;
    }


    /**
     * @return bool
     */
    protected function createSurveyDirectory()
    {
        $iSurveyID = $this->survey->primaryKey;
        // create the survey directory where the uploaded files can be saved
        if ($this->createSurveyDir) {
            if (!file_exists(Yii::app()->getConfig('uploaddir')."/surveys/".$iSurveyID."/files")) {
                if (!(mkdir(Yii::app()->getConfig('uploaddir')."/surveys/".$iSurveyID."/files", 0777, true))) {
                    return false;
                } else {
                    file_put_contents(Yii::app()->getConfig('uploaddir')."/surveys/".$iSurveyID."/files/index.html", '<html><head></head><body></body></html>');
                }
            }
        }
        return true;

    }

}