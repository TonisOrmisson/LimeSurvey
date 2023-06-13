<?php

namespace LimeSurvey\Helpers\Update;

use Exception;

/**
 * Fix organizer link : icon and survey activated
 * @package LimeSurvey\Helpers\Update
 */

class Update_607 extends DatabaseUpdateBase
{
    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->db->createCommand()->update(
            "{{settings_user}}",
            [
                'stg_name' => 'showScriptEdit'
            ],
            "stg_name='showScriptEditor'"
        );
    }
}
