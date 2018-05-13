<?php if (!defined('BASEPATH')) {
    die('No direct script access allowed');
}
/*
 * LimeSurvey
 * Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 */

/**
 * @property integer $gid
 * @property string $question Question dieplay text. The actual question.
 * @property string $group_name
 */
class QuestionGroupL10n extends BaseL10n
{
    public static $parentFKColumn = 'qid';
    public static $parentClassName = 'QuestionGroup';
    public static $titleColumn = 'group_name';


    /** @inheritdoc */
    public function tableName()
    {
        return '{{group_l10ns}}';
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return array_merge(parent::model()->rules(), [
            'group_name' => gt('Group name')
        ]);
    }
    
}
