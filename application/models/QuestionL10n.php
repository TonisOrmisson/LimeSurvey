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
 * Class QuestionL10n
 * @property integer $id primary key
 * @property integer $qid question id
 * @property string $language Question language code. Note: There is a unique key on qid & language columns combined
 * @property string $question Question display text. The actual question.
 * @property string $help Question help-text for display
 *
 */
class QuestionL10n extends BaseL10n
{
    public static $parentFKColumn = 'qid';
    public static $parentClassName = 'Question';
    public static $titleColumn = 'question';


    /** @inheritdoc */
    public function tableName()
    {
        return '{{question_l10ns}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return array_merge( parent::model()->rules(), [
            ['help', 'LSYii_Validators'],
        ]);
    }

}
