<?php
/* @var $this AdminController */
/* @var QuestionGroup $oQuestionGroup */
/* @var Survey $oSurvey */

// DO NOT REMOVE This is for automated testing to validate we see that page
echo viewHelper::getViewTestTag('addQuestion');

?>

<?php
$aQuestionTypeGroups = array();
$aQuestionTypeList = Question::typeList();

foreach ( $aQuestionTypeList as $key=> $questionType)
{
    $htmlReadyGroup = str_replace(' ', '_', strtolower($questionType['group']));
    if (!isset($aQuestionTypeGroups[$htmlReadyGroup]))
    {
        $aQuestionTypeGroups[$htmlReadyGroup] = array(
            'questionGroupName' => $questionType['group']
        );
    }
    $aQuestionTypeGroups[$htmlReadyGroup]['questionTypes'][$key] = $questionType;
}
?>

<?php PrepareEditorScript(true, $this); ?>
<?php $this->renderPartial("./survey/Question/question_subviews/_ajax_variables", $ajaxDatas); ?>

<div id='edit-question-body' class='side-body <?php echo getSideBodyClass(false); ?>'>

    <!-- Page Title-->
    <div class="pagetitle h3">
        <?php
        if ($adding) {
            eT("Add a new question");
        } elseif ($copying) {
            eT("Copy question");
        } else {
            eT("Edit question");
            echo ': <em>'.$oQuestion->title.'</em> (ID:'.$qid.')';
        }
        ?>
    </div>

    <div class="row">
        <!-- Form for the whole page-->
        <?php echo CHtml::form(array("admin/database/index"), 'post',array('class'=>'form30 ','id'=>'frmeditquestion','name'=>'frmeditquestion')); ?>
        <!-- The tabs & tab-fanes -->
        <div class="col-sm-12 col-md-7 content-right">
            <?php if($adding):?>
                <?php
                $this->renderPartial(
                    './survey/Question/question_subviews/_tabs',
                    array(
                        'oSurvey'=>$oSurvey,
                        'oQuestion'=>$oQuestion,
                        'surveyid'=>$surveyid,
                        'gid'=>$groupid, 'qid'=>NULL,
                        'adding'=>$adding,
                        'aqresult'=>$aqresult,
                        'action'=>$action
                    )
                ); ?>
            <?php else:?>
                <?php
                $this->renderPartial(
                    './survey/Question/question_subviews/_tabs',
                    array(
                        'oSurvey'=>$oSurvey,
                        'oQuestion'=>$oQuestion,
                        'surveyid'=>$surveyid,
                        'gid'=>$gid, 'qid'=>$qid,
                        'adding'=>$adding,
                        'aqresult'=>$aqresult,
                        'action'=>$action
                    )
                ); ?>

            <?php endif;?>
        </div>

        <!-- The Accordion -->
        <div class="col-sm-12 col-md-5" id="accordion-container" style="background-color: #fff; z-index: 2;">
            <?php // TODO : find why the $groups can't be generated from controller?>
            <div id='questionbottom'>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                    <!-- Copy options -->
                    <?php if ($copying): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading-copy">
                                <div class="panel-title h4">
                                    <a class="btn btn-default btn-xs hide-button hidden-xs opened handleAccordion">
                                        <span class="fa fa-chevron-left"></span>
                                        <span class="sr-only"><?php eT("Expand/Collapse");?></span>
                                    </a>
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-copy" aria-expanded="false" aria-controls="collapse-copy">
                                        <?php eT("Copy options"); ?>
                                    </a>
                                </div>
                            </div>
                            <div id="collapse-copy" class="panel-collapse collapse  in" role="tabpanel" aria-labelledby="heading-copy">
                                <div class="panel-body">
                                    <div  class="form-group">
                                        <label class=" control-label" for='copysubquestions'><?php eT("Copy subquestions?"); ?></label>
                                        <div class="">
                                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                                'name' => 'copysubquestions',
                                                'id'=>'copysubquestions',
                                                'value' => 'Y',
                                                'onLabel' =>gT('Yes'),
                                                'offLabel' => gT('No')));
                                            ?>
                                        </div>
                                    </div>
                                    <div  class="form-group">
                                        <label class=" control-label" for='copyanswers'><?php eT("Copy answer options?"); ?></label>
                                        <div class="">
                                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                                'name' => 'copyanswers',
                                                'id'=>'copyanswers',
                                                'value' => 'Y',
                                                'onLabel' =>gT('Yes'),
                                                'offLabel' => gT('No')));
                                            ?>
                                        </div>
                                    </div>
                                    <div  class="form-group">
                                        <label class=" control-label" for='copyattributes'><?php eT("Copy advanced settings?"); ?></label>
                                        <div class="">
                                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array(
                                                'name' => 'copyattributes',
                                                'id' => 'copyattributes',
                                                'value' => 'Y',
                                                'onLabel' => gT('Yes'),
                                                'offLabel' => gT('No')));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; // Copying ?>

                    <!-- General Options -->
                    <div class="panel panel-default" id="questionTypeContainer">
                        <!-- General Options : Header  -->
                        <div class="panel-heading" role="tab" id="headingOne">
                            <div class="panel-title h4">
                                <a class="btn btn-default btn-xs hide-button hidden-xs opened handleAccordion">
                                    <span class="fa fa-chevron-left"></span>
                                    <span class="sr-only"><?php eT("Expand/Collapse");?></span>
                                </a>
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-question" aria-expanded="true" aria-controls="collapse-question">
                                    <?php eT("General options");?>
                                </a>
                            </div>
                        </div>

                        <div id="collapse-question" class="panel-collapse collapse <?php if (!$copying){echo ' in '; } ?>" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <!-- Question selector start -->
                                <div  class="form-group">
                                    <input type="hidden" id="question_type" name="type" value="<?php echo $oQuestion->type; ?>" />

                                    <?php if( $activated != "Y" && isset($selectormodeclass) && $selectormodeclass != "none"): ?>
                                        <label class=" control-label" for="question_type_button" title="<?php eT("Question type");?>" data-gethelp='{ "title":"Get help", "text" : "More on questions", "href":"https://manual.limesurvey.org/Questions_-_introduction" }' data-help="<?=gT("Select the question type here.")?>">
                                            <?php
                                            eT("Question type:");
                                            ?>
                                        </label>
                                        <div class=" btn-group" id="question_type_button">
                                            <button type="button" class="btn btn-default " data-target="#selector__modal_select-question-type" data-toggle="modal" aria-haspopup="true" aria-expanded="false" >
                                                <span class="buttontext" id="selector__editView_question_type_description">
                                                    <?=Question::getQuestionTypeName($oQuestion->type); ?>
                                                    <?php if(YII_DEBUG):?>
                                                        <em class="small">
                                                            Type code: <?php echo $oQuestion->type; ?>
                                                        </em>
                                                    <?php  endif;?>
                                                </span>
                                                &nbsp;&nbsp;&nbsp;
                                                <i class="fa fa-folder-open"></i>                                       
                                            </button>
                                        </div>
                                    <?php elseif($activated != "Y" && (isset($selectormodeclass) && $selectormodeclass == "none")): ?>
                                        <label class=" control-label" for="question_type" title="<?php eT("Question type");?>">
                                            <?php
                                            eT("Question type:");
                                            ?>
                                        </label>                                       
                                        <select id="question_type" name="type" class="form-control">
                                            <?php 
                                            foreach ($aQuestionTypeGroups as $sGroupHTMLConformString => $aQuestionTypeGroup) { 
                                                echo sprintf("<optgroup label='%s'>", $aQuestionTypeGroup['questionGroupName']);
                                                foreach ($aQuestionTypeGroup['questionTypes'] as $sQuestionTypeKey => $aQuestionType) {
                                                    $selected = $oQuestion->type == $sQuestionTypeKey ? 'selected' : '';
                                                    if(YII_DEBUG) {
                                                        echo sprintf("<option value='%s' %s>%s (%s)</option>", $sQuestionTypeKey, $selected, $aQuestionType['description'], $sQuestionTypeKey);
                                                    } else {
                                                        echo sprintf("<option value='%s' %s>%s</option>", $sQuestionTypeKey, $selected, $aQuestionType['description']);
                                                    }
                                                }
                                                echo "</optgroup>"; 
                                            } 
                                            ?>
                                        </select> 
                                    <?php elseif($activated == "Y") : ?>
                                        <input type="hidden" id="question_type" name="type" value="<?php echo $eqrow['type']; ?>" />
                                        <!-- TODO : control if we can remove, disable update type must be done by PHP -->
                                        <label class=" control-label" for="question_type_button" title="<?php eT("Question type");?>">
                                            <?php
                                            eT("Question type:");
                                            ?>
                                        </label>
                                        <div class=" btn-group" id="question_type_button">
                                            <button type="button" class="btn btn-default" disabled  aria-haspopup="true" aria-expanded="false" >
                                                <span class="buttontext" id="selector__editView_question_type_description">
                                                    <?=Question::getQuestionTypeName($oQuestion->type); ?>
                                                    <?php if(YII_DEBUG):?>
                                                        <em class="small">
                                                            Type code: <?php echo $oQuestion->type; ?>
                                                        </em>
                                                    <?php  endif;?>
                                                </span>
                                                &nbsp;&nbsp;&nbsp;
                                                <i class="fa  fa-lock"></i>                                       
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- Question selector end -->
                                <div  class="form-group">
                                    <label class=" control-label" for='gid' title="<?php eT("Set question group");?>"><?php eT("Question group:"); ?></label>
                                    <div class="">
                                        <select name='gid' id='gid' class="form-control" <?php if ($activated == "Y"){echo " disabled ";} ?> >
                                            <?php echo getGroupList3($oQuestion->gid,$surveyid); ?>
                                        </select>
                                        <?php if ($activated == "Y"): ?>
                                            <input type='hidden' name='gid' value='<?php echo $oQuestion->gid;?>' />
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div  class="form-group" id="OtherSelection">
                                    <label class=" control-label" title="<?php eT("Option 'Other':");?>"><?php eT("Option 'Other':"); ?></label>
                                    <?php if ($activated != "Y"): ?>
                                        <div class="">
                                            <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array('name' => 'other', 'value'=> $oQuestion->other === "Y", 'onLabel'=>gT('On'),'offLabel'=>gT('Off')));?>
                                        </div>
                                    <?php else:?>
                                        <?php eT("Cannot be changed (survey is active)");?>
                                        <input type='hidden' name='other' value="<?php echo ($oQuestion->other=='Y' ? 1 : 0); ?>" />
                                    <?php endif;?>
                                </div>

                                <div id='MandatorySelection' class="form-group">
                                    <label class=" control-label" title="<?php eT("Set \"Mandatory\" state");?>"><?php eT("Mandatory:"); ?></label>
                                    <div class="">
                                        <!-- Todo : replace by direct use of bootstrap switch. See statistics -->
                                        <?php $this->widget('yiiwheels.widgets.switch.WhSwitch', array('name' => 'mandatory', 'value'=> $oQuestion->mandatory === "Y", 'onLabel'=>gT('On'),'offLabel'=>gT('Off')));?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class=" control-label" for='relevance' title="<?php eT("Relevance equation");?>" data-gethelp='{ "title":"Get help", "text" : "More on relevance and the EM", "href":"https://manual.limesurvey.org/Expression_Manager" }' data-help="<?=gT("The relevance equation can be used to add branching logic. This is a rather advanced topic. If you are unsure, just leave it be.")?>"><?php eT("Relevance equation:"); ?></label>
                                    <div class="">
                                        <div class="input-group">
                                            <div class="input-group-addon">{</div>
                                            <textarea class="form-control" rows='1' id='relevance' name='relevance' <?php if (count($oQuestion->conditions)>0) {?> readonly='readonly'<?php } ?> ><?php echo $oQuestion->relevance; ?></textarea>
                                            <div class="input-group-addon">}</div>
                                        </div>
                                        <?php if (count($oQuestion->conditions)>0) :?>
                                            <div class='help-block text-warning'> <?php eT("Note: You can't edit the relevance equation because there are currently conditions set for this question."); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div id='Validation'  class="form-group">
                                    <label class=" control-label" for='preg'  title="<?php eT("Validation:");?>"><?php eT("Validation:"); ?></label>
                                    <div class="">
                                        <input class="form-control" type='text' id='preg' name='preg' size='50' value="<?php echo $oQuestion->preg; ?>" />
                                    </div>
                                </div>


                                <?php if ($adding || $copying ): ?>

                                    <!-- Rendering position widget -->
                                    <?php $this->widget('ext.admin.survey.question.PositionWidget.PositionWidget', array(
                                        'display'           => 'ajax_form_group',
                                        'oQuestionGroup'    => $oQuestionGroup,
                                    ));
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!$copying): ?>
                        <div class="loader-advancedquestionsettings text-center">
                            <span class="fa fa-refresh" style="font-size:3em;" aria-hidden='true'></span>
                        </div>
                        <!-- Advanced settings -->
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($adding): ?>
            <input type='hidden' name='action' value='insertquestion' />
            <input type='hidden' id='sid' name='sid' value='<?php echo $surveyid; ?>' />
            <p><input type='submit'  class="hidden" value='<?php eT("Add question"); ?>' /></p>
        <?php elseif ($copying): ?>
            <input type='hidden' name='action' value='copyquestion' />
            <input type='hidden' id='oldqid' name='oldqid' value='<?php echo $qid; ?>' />
            <p><input type='submit'  class="hidden" value='<?php eT("Copy question"); ?>' /></p>
        <?php else: ?>
            <input type='hidden' name='action' value='updatequestion' />
            <input type='hidden' id='qid' name='qid' value='<?php echo $qid; ?>' />
            <p><button type='submit' class="saveandreturn hidden" name="redirection" value="edit"><?php eT("Save") ?> </button></p>
            <input type='submit'  class="hidden" value='<?php eT("Save and close"); ?>' />
        <?php endif; ?>
        <input type='hidden' name='sid' value='<?php echo $surveyid; ?>' />
        </form>
    </div>
</div>


<?php if(isset($selectormodeclass) && $selectormodeclass != "none" && $activated != "Y"): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="selector__modal_select-question-type" style="z-index: 1250">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php Yii::app()->getController()->renderPartial('/admin/survey/Question/question_subviews/_question_type_select', ['currentType' => $oQuestion->type, 'aQuestionTypeGroups' => $aQuestionTypeGroups]); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
