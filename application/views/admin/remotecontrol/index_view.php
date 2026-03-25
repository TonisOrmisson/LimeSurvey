<div class="container-fluid">
    <div id='remotecontrol' class="row">
        <div class="col-12">
        <?php $transportList = implode(', ', $transports); ?>
        <div class="pagetitle h3">
            <?php
            if (count($transports) === 1) {
                echo sprintf(gT('RemoteControl is available using %s for transport and exposes the following functionality:'), $transportList);
            } else {
                echo sprintf(gT('RemoteControl is available using %s for transports and exposes the following functionality:'), $transportList);
            }
            ?>
        </div>
        <dl>
        <?php
        foreach ($list as $apiMethod => $info) {
            echo \CHtml::tag(
                "dt",
                array('class'=>"h4"),
                \CHtml::link($apiMethod,"#definition-{$apiMethod}",array(
                    "role" => "button",
                    "data-bs-toggle" => "collapse",
                    "aria-expanded" => "false",
                    "aria-controls" => "definition-{$apiMethod}",
                ))
            );
            echo \CHtml::tag(
                "dd",
                array(
                    "class" => "collapse",
                    "id" => "definition-{$apiMethod}"
                ),
                \CHtml::tag("pre",array(),\CHtml::encode($info['description']))
            );
        }
        ?>
        </dl>
        </div>
    </div>
</div>
