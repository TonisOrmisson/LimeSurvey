<?php

/**
* This view generate the interface tab inside global settings.
*
*/

?>

<?php

$RPCInterface = App()->getConfig('RPCInterface');

?>
<div class="container">
<div class="mb-3">
    <label class=" form-label"  for='RPCInterface'><?php eT("Default RPC interface:"); ?></label>
    <div>
        <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
            'name'          => 'RPCInterface',
            'ariaLabel' => gT('Default RPC interface:'),
            'checkedOption' => $RPCInterface,
            'selectOptions' => [
                "off"  => gT("Off", 'unescaped'),
                "json" => gT("JSON-RPC", 'unescaped'),
                "xml"  => gT("XML-RPC", 'unescaped'),
                "toon" => gT("TOON-RPC", 'unescaped')
            ]
        ]); ?>
    </div>
</div>

<div class="mb-3">
    <label class=" form-label"><?php eT("Enabled RPC interfaces:"); ?></label>
    <div class="row">
        <div class="col-12 col-md-4">
            <label class="form-label" for='rpc_json_enabled'><?php eT("JSON-RPC:"); ?></label>
            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
                'name'          => 'rpc_json_enabled',
                'checkedOption' => \LimeSurvey\RemoteControl\RpcConfiguration::getEffectiveToggleValue('json'),
                'selectOptions' => [
                    '1' => gT('On'),
                    '0' => gT('Off'),
                ]
            ]); ?>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label" for='rpc_xml_enabled'><?php eT("XML-RPC:"); ?></label>
            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
                'name'          => 'rpc_xml_enabled',
                'checkedOption' => \LimeSurvey\RemoteControl\RpcConfiguration::getEffectiveToggleValue('xml'),
                'selectOptions' => [
                    '1' => gT('On'),
                    '0' => gT('Off'),
                ]
            ]); ?>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label" for='rpc_toon_enabled'><?php eT("TOON-RPC:"); ?></label>
            <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
                'name'          => 'rpc_toon_enabled',
                'checkedOption' => \LimeSurvey\RemoteControl\RpcConfiguration::getEffectiveToggleValue('toon'),
                'selectOptions' => [
                    '1' => gT('On'),
                    '0' => gT('Off'),
                ]
            ]); ?>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class=" form-label" ><?php eT("URL:"); ?></label>
    <div class="">
        <?php echo $this->createAbsoluteUrl("admin/remotecontrol"); ?>
    </div>
</div>

<div class="mb-3">
    <label class=" form-label"  for='rpc_publish_api'><?php eT("Publish API on /admin/remotecontrol:"); ?></label>
    <div>
        <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
            'name'          => "rpc_publish_api",
            'ariaLabel' => gT('Publish API on /admin/remotecontrol:'),
            'checkedOption' => App()->getConfig('rpc_publish_api'),
            'selectOptions' => [
                '1' => gT('On'),
                '0' => gT('Off'),
            ]
        ]); ?>
    </div>
</div>

<div class="mb-3">
    <label class=" form-label"  for='add_access_control_header'><?php eT("Set Access-Control-Allow-Origin header:"); ?></label>
    <div>
        <?php $this->widget('ext.ButtonGroupWidget.ButtonGroupWidget', [
            'name'          => 'add_access_control_header',
            'ariaLabel' => gT('Set Access-Control-Allow-Origin header:'),
            'checkedOption' => App()->getConfig('add_access_control_header'),
            'selectOptions' => [
                '1' => gT('On'),
                '0' => gT('Off'),
            ]
        ]) ?>
    </div>
</div>

<?php if (Yii::app()->getConfig("demoMode") == true) :?>
    <p><?php eT("Note: Demo mode is activated. Marked (*) settings can't be changed."); ?></p>
<?php endif; ?>
</div>
