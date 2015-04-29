<div class="ui active inverted dimmer" id="mapRconLoader" style="display: none;">
    <div class="ui text loader"><?php echo __('Executing');?></div>
</div>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php

    echo $this->Form->create('Server', array('class' => 'ui form'));
?>
        <div class="two fields">
            <div class="field">
<?php
            echo $this->Form->input('maps', [
                                                'label' => __('Select map'),
                                                'div' => false]);
?>
            </div>
            <div class="field">
            <label>&nbsp;</label>
<?php
                echo $this->Js->submit(__('Change map'),
                                            [
                                            'url' => array(
                                                            'controller' => 'Servers',
                                                            'action' => 'setMapRcon',
                                                            $id,
                                                            'ver' => 2
                                             ),
                                            'div' => false,
                                            'update' => '#indexModal .content .description',
                                            'class'  => 'ui primary fluid button',
                                            'escape' => false,
                                            'id' => 'submitRconMap',
                                            'before' => '$("#mapRconLoader").show();',
                                            'complete' => '$("#mapRconLoader").hide();',
                                            'buffer' => false]);
?>
            </div>
        </div>
<?php




?>
<p></p>
<div id='clear'></div>
<div class="ui icon warning message">

    <div class="content">
        <div class="header">
            Обратите внимание!
        </div>
        <p>
            Cменив карту, вы не измените карту, установленную по-умолчанию!
        </p>
        <p>
            Если вы хотите изменить карту, которая запускается при включении сервера, установите её в <i class="setting icon"></i> "Параметрах запуска"
        </p>
    </div>
</div>

