<?php
/*
 * Created on 24.03.2015
 *
 * File created for project GHManager
 * by Nikita Bulaev
 */
 $id = $this->data['Server']['id'];
 $slots = $this->data['Server']['slots'];

 if ($slots > 0 and $slots <= 12) {
    $fpsmax = 1000;
 } elseif ($slots > 12 and $slots <= 32) {
    $fpsmax = 500;
 } else {
    $fpsmax = 300;
 }

?>
<div><?php echo $this->Session->flash(); ?></div>
<div id="serverStartParams">
<div class="ui error message" data-bind="visible: errors().length > 0">
    <div class="ui small header">Во время запроса произошли ошибки:</div>
    <ul data-bind="foreach: {data: errors, as: 'error'}">
        <li data-bind="text: error"></li>
    </ul>
    <!-- ko if: infos().length > 0-->
    <div class="ui tiny header">Журнал операций:</div>
    <ul data-bind="foreach: {data: infos, as: 'info'}">
        <li data-bind="text: info"></li>
    </ul>
    <!-- /ko -->
</div>
<div class="ui positive message" data-bind="visible: success">
    <div class="ui small header">Успешно. Перегрузите сервер для применения изменений.</div>
    <a data-bind="visible: showInfos() == false, if: infos().length > 0, event: { click: function(){showInfos(true);}}">Показать журнал</a>
    <ul data-bind="visible: showInfos, foreach: {data: infos, as: 'info'}">
        <li data-bind="text: info"></li>
    </ul>
</div>
<div class="ui vertically divided grid">
    <div class="one column row">
        <div class="column">
            <!-- Server admin add -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setModAdmin',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'adminAdd']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label>Добавить администратора <a style="cursor: pointer;">(справка)</a>:</label>
                <div class="ui action input">
                <?php
                echo $this->Form->input('admString', [ 'id'=>'admString',
                                                       'div' => false,
                                                       'label' => false,
                                                       //'size' => 25,
                                                       'style' => 'text-align: center;']);
                ?>

                    <button class="ui green button" data-bind="event: {click: sendForm.bind(true, '#adminAdd', false)}"><i class="checkmark icon"></i></button>
                </div>
                <label for="admString">
                    <center><div id="setAdminMsg"></div></center>
                </label>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="one column row" data-bind="if: gameTemplateName() != 'l4d2'">
        <div class="column">
            <!-- Server password set -->
            <?php
            // Установить заголовок по наличию пароля
            if ($serverPassword === false) {
                $passHeader = 'Установить';
            } else {
                $passHeader = 'Изменить';
            }

            echo $this->Form->create('Server', ['url' => ['action' => 'setConfigParam',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'serverPasswdSet']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            echo $this->Form->input('paramName', ['type'  => 'hidden',
                                                  'value' => 'sv_password']);

            ?>
            <div class="field">
                <label><?php echo $passHeader; ?> пароль сервера:</label>
                <div class="ui action input">
                <?php
                echo $this->Form->input('paramValue', [ 'id'    => 'paramPassword',
                                                        'value' => $serverPassword,
                                                        'div'   => false,
                                                        'label' => false,
                                                        'style' => 'text-align: center;']);
                ?>
                    <button class="ui green button" data-bind="event: {click: sendForm.bind(true, '#serverPasswdSet', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <!-- RCON password set -->
            <?php
            if (empty($this->data['Server']['rconPassword'])) {
                $word = 'Установить';
            } else {
                $word = 'Изменить';
            }

            echo $this->Form->create('Server', ['url' => ['action' => 'setRconPassword',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'rconPasswdSet']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label><?php echo $word; ?> пароль RCON:</label>
                <div class="ui action input">
                <?php
                echo $this->Form->input('rconPassword', [ 'id'    => 'setRconPass',
                                                        'value' => @$rconPassword,
                                                        'div'   => false,
                                                        'label' => false,
                                                        'style' => 'text-align: center;']);
                ?>
                    <button class="ui green button" data-bind="event: {click: sendForm.bind(true, '#rconPasswdSet', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <!-- Map Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setMap',
                                                          $this->data['Server']['id'],
                                                          '0',
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setMap']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            echo $this->Form->input('action', ['type'=>'hidden', 'value'=>'set']);
            ?>
            <div class="field">
                <label>Установить карту по умолчанию:</label>
                <div class="ui action input" data-bind="if: mapList().length > 0">
                    <select name="data[Server][map]" data-bind="options: mapList, value: currentMap"></select>
                    <button class="ui green button" id="mapButton" data-bind="event: {click: sendForm.bind(true, '#setMap', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <!-- ko if: Number(jQuery.inArray(gameTemplateName(), ['csgo', 'csgo-t128'])) >= 0 -->
    <div class="ui row">
        <div class="column" data-bind="">
            <!-- CSGO Map Group Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setMapGroup',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setMapGroup']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            echo $this->Form->input('action', ['type'=>'hidden', 'value'=>'set']);
            ?>
            <div class="field">
                <label>Установить группу карт:</label>
                <div class="ui action input" data-bind="if: mapGroupsList().length > 0">
                    <select name="data[Server][mapGroup]" data-bind="options: mapGroupsList, value: currentMapGroup"></select>
                    <button class="ui green button" id="mapGroupButton" data-bind="event: {click: sendForm.bind(true, '#setMapGroup', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="column">
            <!-- CSGO GameMode Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setGameMode',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setGameMode']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            echo $this->Form->input('action', ['type'=>'hidden', 'value'=>'set']);
            ?>
            <div class="field">
                <label>Установить режим игры:</label>
                <div class="ui action input" data-bind="if: gameModesList().length > 0">
                    <select name="data[Server][gameMode]" data-bind="options: gameModesList, value: currentGameMode, optionsText: 'name', optionsValue: 'id'"></select>
                    <button class="ui green button" id="gameModeButton" data-bind="event: {click: sendForm.bind(true, '#setGameMode', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="column">
            <!-- CSGO Hostmap Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setHostMap',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setHostMap']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label>Установить Hostamap</label>
                <div class="ui action input">
                <?php
                echo $this->Form->input('hostmap', [ 'id'=>'hostmap',
                                                       'div' => false,
                                                       'label' => false,
                                                       'style' => 'text-align: center;']);
                ?>

                    <button class="ui green button" data-bind="event: {click: sendForm.bind(true, '#setHostMap', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="column">
            <!-- CSGO Host collection Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setHostCollection',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setHostCollection']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>

            <div class="fields">
                <div class="six wide field">
                    <label>Коллекция Hostmap:</label>
                    <select name="data[Server][hostCollectionList]" data-bind="options: hostCollectionList, value: currentHostCollection, optionsText: 'name', optionsValue: 'id'"></select>
                </div>
                <div class="six wide field">
                    <label>&nbsp;</label>
                    <?php
                    echo $this->Form->input('hostcollection', [ 'id'=>'hostcollection',
                                                           'div' => false,
                                                           'label' => false,
                                                           'placeholder'  => 'Свой вариант',
                                                           'style' => 'text-align: center;']);
                    ?>
                </div>
                <div class="four wide field">
                <label>&nbsp;</label>
                    <button class="ui green fluid button" data-bind="event: {click: sendForm.bind(true, '#setHostCollection', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>

            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <!-- /ko -->
    <!-- ko if: Number(jQuery.inArray(gameTemplateName(), ['csgo', 'csgo-t128', 'l4d-t100', 'l4d2-t100', 'cssv34'])) >= 0 -->
    <div class="ui row">
        <div class="eight wide column">
            <!-- Tick rate Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setTickrate',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setTickrate']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label>Установить Tickrate:</label>
                <div class="ui action input">
                <!-- ko if: gameTemplateName() == 'csgo-t128' -->
                    <select name="data[Server][tickrate]" data-bind="options: csgoTickRates, value: currentTick"></select>
                    <!-- /ko -->
                    <!-- ko if: Number(jQuery.inArray(gameTemplateName(), ['l4d-t100', 'l4d2-t100', 'cssv34'])) >= 0 -->
                    <select name="data[Server][tickrate]" data-bind="options: tickRates, value: currentTick"></select>
                    <!-- /ko -->
                    <button class="ui green button" id="fpsButton" data-bind="event: {click: sendForm.bind(true, '#setTickrate', false)}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <!-- /ko -->
    <div class="two column row">
        <div class="column" style="width: 49.9% !important;">
            <!-- FPS Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setFps',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setFps']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label><center>Установить FPS:</center></label>
                <div class="ui action input">
                <?php
                echo $this->Form->input('fpsmax', [ 'id'    => 'fpsmax',
                                                    'div'   => false,
                                                    'label' => false,
                                                    'style' => 'text-align: center;']);
                ?>
                    <button class="ui green button" id="fpsButton" data-bind="event: {click: sendForm.bind(true, '#setFps', false)}"><i class="checkmark icon"></i></button>
                </div>
                <label><center id="fpsMsg"></center></label>
            </div>
             <div id="fpsWarning" class="ui fluid popup"><h5>Внимание!</h5>
            <p>Не работает на CS:S после v68, DOS:S, TF2!</p>
            <p>Какое бы значение вы не ставили для этих игр,
            FPS всегда будет 66!</p>
            <p>
            Для L4D и L4D2 не рекомендуется ставить значения
            больше стандартных 30FPS - качество игры это не
            изменит, зато может сильно увеличить VAR.
            </p><br/>
            Причины описаны в FAQ на нашем сайте.
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div class="column" style="width: 49.9% !important;">
            <!-- Slots number Set -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setSlots',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setSlots']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="field">
                <label><center>Количество слотов:</center></label>
                <div class="ui action input" data-bind="if: slotsRange()">
                    <select name="data[Server][slots]" data-bind="options: slotsAvail, value: slots, event: {change: countRent}"></select>
                    <button class="ui green button" id="slotsButton" data-bind="event: {click: sendForm.bind(true, '#setSlots', false)}"><i class="checkmark icon"></i></button>
                </div>
                <label><center data-bind="text: rentText"></center></label>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="column">
            <!-- Control by token  -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'setControlToken',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'setControlToken']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="ui checkbox" data-bind="event: {click: toggleSend.bind(true, '#setControlToken')}">
                <?php echo $this->Form->input('controlByToken', [ 'type'  => 'checkbox',
                                                                  'id'    => 'controlByToken',
                                                                  'label' => false,
                                                                  'div'   => false]);?>
                <label>Контроль сервера без пароля</label>
            </div>
            <a target="_blank" data-bind="if: controlToken, attr: {'href': '/servers/controlByToken/' + controlToken()}"><i class="forward mail icon"></i> Ссылка для управления</a>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
        <div class="two column row">
        <div class="column" style="width: 49.9% !important;">
            <!-- VAC -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'switchParam', 'vac'],
                                                'class'  => 'ui form',
                                                'id'     => 'changeVac']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="ui checkbox" data-bind="event: {click: sendParam.bind(true, '#changeVac', false, false)}">
                <?php echo $this->Form->input('vac', ['type'  => 'checkbox',
                                                      'id'    => 'vac',
                                                      'label' => false,
                                                      'div'   => false]);?>
                <label>Включить VAC</label>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <div class="column" style="width: 49.9% !important;">
            <!-- Nomaster -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'switchParam',
                                                          'nomaster'],
                                                'class'  => 'ui form',
                                                'id'     => 'changeNomaster']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="ui checkbox" data-bind="event: {click: sendParam.bind(true, '#changeNomaster', false, false)}">
                <?php echo $this->Form->input('nomaster', ['type'  => 'checkbox',
                                                           'id'    => 'nomaster',
                                                           'label' => false,
                                                           'div'   => false]);?>
                <label>Включить Nomaster</label>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="center aligned column">
            <!-- Auto update -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'switchParam',
                                                          'autoupdate'],
                                                'class'  => 'ui form',
                                                'id'     => 'changeAutoupdate']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            ?>
            <div class="ui checkbox" data-bind="event: {click: sendParamWithConfirm.bind(true, '#changeAutoupdate', '#autoUpdateConfirm', true)}">
                <?php echo $this->Form->input('autoUpdate', ['type'  => 'checkbox',
                                                      'id'    => 'autoupdate',
                                                      'label' => false,
                                                      'div'   => false]);?>
                <label>Автоматическое обновление</label>
            </div>
            <?php echo $this->Form->end(); ?>
            <div style="display: none;" id="autoUpdateConfirm">
                <div class="confirm_title">
                    Вы уверены, что хотите включить автоматическое обновление сервера?
                </div>
                <div class="confirm_text">
                    <p>Это приведет к существенной задержке при запуске сервера - от 30 секунд до нескольких минут.</p>
                    <p>Также скрипт автоматического поднятия сервера может ложно сработать и попытаться перегрузить сервер.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="ui row"  data-bind="if: gameTemplatesList().length > 0">
        <div class="column">
            <!-- Game change -->
            <?php
            echo $this->Form->create('Server', ['url' => ['action' => 'changeGame',
                                                          $this->data['Server']['id'],
                                                          'ext' => 'json'],
                                                'class'  => 'ui form',
                                                'id'     => 'changeGame']);

            echo $this->Form->input('id', ['type' => 'hidden']);
            echo $this->Form->input('action', ['type'=>'hidden', 'value'=>'set']);
            ?>
            <div class="field">
                <label>Изменить игру сервера на:</label>
                <div class="ui action input">
                    <select name="data[GameTemplate][id]" data-bind="options: gameTemplatesList, optionsText: 'name', optionsValue: 'id', value: gameTemplateId"></select>
                    <button class="ui green button" id="changeGameButton" data-bind="event: {click: sendFormWithConfirm.bind(true, '#changeGame', '#gameChangeConfirm')}"><i class="checkmark icon"></i></button>
                </div>
            </div>
            <div id="gameChangeWarning" class="ui fluid popup">
                <b>После установки новой игры, ваш текущий сервер удалён НЕ БУДЕТ.</b>
                <p>Если в будущем вы выберете игру, которая была установлена ранее,
                то будут возвращены также и все настройки, моды и плагины.</p>
                <p>
                Если стоимость слота текущего сервера ниже стоимость слота нового
                сервера, то срок аренды будет увеличен. Если же наоборот - то уменьшен.</p>
                <p>
                <b>Обращаем ваше внимание,</b> что смену игры можно производить не чаще
                одного раза в сутки. Также нельзя выбрать игру, которая не поддерживается
                текущим приватным режимом сервера.</p>
            </div>
            <div id="gameChangeConfirm" style="display: none;">
                <div class="confirm_title">
                    Вы уверены, что хотите сменить игру сервера?
                </div>
                <div class="confirm_text">
                    <p>Все текущие настройки будут сохранены, вы сможете к ним вернуться при смене игры на нынешнюю.</p>
                    <p>Обращаем ваше внимание, что смену игры можно производить не чаще одного раза в сутки!</p>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
    <div class="ui row">
        <div class="column">
            <button class="ui red fluid button" data-bind="event: {click: reInit.bind(false)}">Сбросить настройки</button>
        </div>
    </div>
</div>
</div>
<?php
    // Rebuild GameTemplates list for knockout
    $gameTemplatesJson = array();
    if (!empty($gameTemplateList))
    {
        foreach ($gameTemplateList as $key => $value) {
            $gameTemplatesJson[] = ['id' => $key, 'name' => $value];
        }
    }

    $mapGroupsJson = array();
    if (!empty($mapGroups)){
        foreach ($mapGroups as $key => $value) {
            $mapGroupsJson[] = $value;
        }
    }

    $gameModesJson = array();
    if (!empty($gameModesList)){
        foreach ($gameModesList as $key => $value) {
            $gameModesJson[] = ['id' => $key, 'name' => $value];
        }
    }

    $hostCollectionList = [['id' => '0', 'name' => 'Нет'],
                           ['id' => '125499590', 'name' => '_SE'],
                           ['id' => '125499818', 'name' => '_SE + Mirage']];
?>
<script type="text/javascript">
    var logsViewModel = function(){

        this.serverId = ko.observable(<?php echo $this->data['Server']['id'];?>)
        this.gameTemplateName = ko.observable('<?php echo $this->data["GameTemplate"][0]["name"];?>')
        this.gameTemplateId = ko.observable(<?php echo $this->data["GameTemplate"][0]["id"];?>);

        this.showInfos = ko.observable(false);
        this.success   = ko.observable(false);
        this.loading   = ko.observable(false);
        this.infos     = ko.observableArray([]);
        this.errors    = ko.observableArray([]);


        this.slotsAvail = ko.observableArray([]);
        this.slots      = ko.observable(<?php echo $this->data['Server']['slots'];?>);
        this.currSlots  = ko.observable(<?php echo $this->data['Server']['slots'];?>);
        this.slots_min  = ko.observable(<?php echo $this->data['GameTemplate'][0]['slots_min'];?>);
        this.slots_max  = ko.observable(<?php echo $this->data['GameTemplate'][0]['slots_max'];?>);
        this.payedTill  = ko.observable("<?php echo $this->data['Server']['payedTill'];?>");
        this.rentText   = ko.observable();

        this.currentMap = ko.observable("<?php echo $this->data['Server']['map'];?>");
        this.mapList    = ko.observableArray(<?php echo @json_encode(array_keys($this->data["Server"]["maps"]));?>);

        this.controlToken = ko.observable("<?php echo $this->data['Server']['controlToken'];?>");

        this.gameTemplatesList = ko.observableArray(<?php echo json_encode($gameTemplatesJson);?>);

        this.mapGroupsList   = ko.observableArray(<?php echo json_encode($mapGroupsJson);?>);
        this.currentMapGroup = ko.observable("<?php echo $this->data['Server']['mapGroup'];?>");

        this.gameModesList   = ko.observableArray(<?php echo json_encode($gameModesJson);?>);
        this.currentGameMode = ko.observable("<?php echo $this->data['Server']['mod'];?>");

        this.hostCollectionList = ko.observableArray(<?php echo json_encode($hostCollectionList);?>);
        this.currentHostCollection = ko.observable("<?php echo $this->data['Server']['hostcollection'];?>");

        this.tickRates     = ko.observableArray(['30', '33', '60', '66', '90', '100']);
        this.csgoTickRates = ko.observableArray(['64', '66', '100', '128']);
        this.currentTick   = ko.observable("<?php echo $this->data['Server']['tickrate'];?>");

        this.sendForm = function(formId, callback){
            var self = this;
            var url  = $(formId).attr('action');

            if (callback === false){
                callback = function(){};
            }

            self.loading(true);
            self.infos([]);
            self.errors([]);
            self.success(false);
            self.showInfos(false);

            $(formId + ' .button').addClass('loading');

            $.post(url, $(formId).serialize())
             .done(
                    function(data){
                        answer = data.result;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            self.success(true);
                        }

                        if (answer.info !== undefined && $.isArray(answer.info)){
                            self.infos(answer.info);
                        } else if (answer.info !== undefined) {
                            self.infos([answer.info]);
                        }

                        self.loading(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                }
             })
             .always(function(){
                $(formId + ' .button').removeClass('loading');
             });

        }.bind(this);

        this.sendFormWithConfirm = function(formId, confirmId, onAction){
            var self = this;
            var url  = $(formId).attr('action');

            var confirmTitle = $(confirmId + ' .confirm_title').html();
            var confirmText = $(confirmId + ' .confirm_text').html();

            swal({
                  title: confirmTitle,
                  text: confirmText,
                  type: "warning",
                  showCancelButton: true,
                  closeOnConfirm: true,
                  confirmButtonText: "<?php echo __('Yes, I am sure');?>",
                  cancelButtonText: "<?php echo __('No');?>",
                  confirmButtonColor: "#ec6c62"
                }, function() {
                    self.sendForm(formId, function(error, data){
                        if (error !== false){
                            swal("<?php echo __('Error');?>", error, "error");
                        }
                    });
                });

        }.bind(this);

        this.sendParam = function(formId, onAction, callback){
            var self = this;
            var url  = $(formId).attr('action');

            if (callback === false){
                callback = function(){};
            }

            if ($(formId).checkbox('is checked') === true){
                url = url + '/off/' + self.serverId() + '.json';
            } else {
                url = url + '/on/' + self.serverId() + '.json';
            }

            self.loading(true);
            self.infos([]);
            self.errors([]);
            self.success(false);
            self.showInfos(false);

            $(formId).addClass('loading');

            $.post(url, $(formId).serialize())
             .done(
                    function(data){
                        answer = data.result;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            self.success(true);
                            if ($(formId).checkbox('is checked') === true){
                                $(formId).checkbox('uncheck')
                            } else {
                                $(formId).checkbox('check')
                            }
                        }

                        if (answer.info !== undefined && $.isArray(answer.info)){
                            self.infos(answer.info);
                        } else if (answer.info !== undefined) {
                            self.infos([answer.info]);
                        }

                        self.loading(false);
                        callback(false, true);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                    callback(answer, false);
                }
             })
             .always(function(){
                $(formId).removeClass('loading');
             });

        }.bind(this);

        this.sendParamWithConfirm = function(formId, confirmId, onAction){
            var self = this;
            var url  = $(formId).attr('action');

            if ($(formId).checkbox('is checked') !== onAction)
            {
                var confirmTitle = $(confirmId + ' .confirm_title').html();
                var confirmText = $(confirmId + ' .confirm_text').html();

                swal({
                      title: confirmTitle,
                      text: confirmText,
                      type: "warning",
                      showCancelButton: true,
                      closeOnConfirm: true,
                      confirmButtonText: "<?php echo __('Yes, I am sure');?>",
                      cancelButtonText: "<?php echo __('No');?>",
                      confirmButtonColor: "#ec6c62"
                    }, function() {
                        self.sendParam(formId, onAction, function(error, data){
                            if (error !== false){
                                swal("<?php echo __('Error');?>", error, "error");
                            }
                        });
                    });
            } else {
                self.sendParam(formId, onAction, false);
            }
        }.bind(this);

        this.toggleSend = function(formId){
            var self = this;
            var url  = $(formId).attr('action');

            self.loading(true);
            self.infos([]);
            self.errors([]);
            self.success(false);
            self.showInfos(false);

            $(formId).addClass('loading');

            $.post(url, $(formId).serialize())
             .done(
                    function(data){
                        answer = data.result;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            self.success(true);
                            if (answer.state !== undefined) {
                                if (answer.state == 'off') {
                                    $(formId).checkbox('uncheck')
                                } else {
                                    $(formId).checkbox('check')
                                }
                            }

                            if (formId == '#setControlToken' && answer.token !== undefined){
                                self.controlToken(answer.token);
                            }
                        }

                        if (answer.info !== undefined && $.isArray(answer.info)){
                            self.infos(answer.info);
                        } else if (answer.info !== undefined) {
                            self.infos([answer.info]);
                        }

                        self.loading(false);
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                }
             })
             .always(function(){
                $(formId).removeClass('loading');
             });

        }.bind(this);

        this.slotsRange = function(){
            var self = this;
            for (var i = self.slots_min(); i <= self.slots_max(); i++) {
                self.slotsAvail.push(i);
            };

            return true;
        }.bind(this);

        this.reInit = function(orderId){
            var self = this;

            swal({
              title: "<?php echo __('Confirm server recreation!');?>",
              text: "<?php echo __('Are you sure, that you want to recreate this server? All data and parameters wil be deleted!');?>",
              type: "warning",
              showCancelButton: true,
              closeOnConfirm: false,
              confirmButtonText: "<?php echo __('Yes, I am sure');?>",
              cancelButtonText: "<?php echo __('No');?>",
              confirmButtonColor: "#ec6c62"
            }, function() {
              $.ajax({
                url: "/servers/reInit/" + self.serverId(),
                type: "PUT"
              })
              .done(function(data) {
                window.location.href = "/servers";
              })
              .error(function(data) {
                swal("<?php echo __('Error');?>", "<?php echo __('Could not connect to server');?>", "error");
              });
            });
        }.bind(this);

        this.countRent = function(){
            var self = this;
            var newSlots = eval($("#sliderSlots").slider("value"));

            var rentLeft = (Number(moment(self.payedTill()).format('X'))
                            - Number(moment().format('X'))) / 3600;

            var newRent = parseInt((self.currSlots() / self.slots()) * rentLeft);
            var newDays = parseInt(newRent / 24);
            var newHours = newRent - newDays*24;

            self.rentText('~' + newDays + 'дн. ' + newHours + 'час.');
        }
    };

    ko.cleanNode(document.getElementById("serverStartParams"));
    ko.applyBindings(new logsViewModel(), document.getElementById("serverStartParams"));
</script>

<script type="text/javascript">
    $(function() {
        $('#serverStartParams .ui.checkbox')
          .checkbox()
        ;

        $('#fpsmax').popup({
                            popup : $('#fpsWarning'),
                            on    : 'focus'
                          });

        $('#changeGameButton').popup({
                            popup : $('#gameChangeWarning'),
                            on    : 'hover'
                          });

        function matchMsg(){
            var string = $('#admString').val().trim();

            var steamIdRegex = /^STEAM_[01]:[01]:[0-9]{4,11}$/;
            var usrPassRegex = /^\"[0-9a-zA-Z-_\$@\+\=\^\!\?]+\"\s+\"[0-9a-zA-Z-_\$@\+\=\^\!\?]+\"$/;
            var ipRegex  = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
            var intRegex = /[0-9 -()+]+$/;

            var result = '';

            if (string.match(steamIdRegex)){
                result = 'Создать админа по Steam ID';
            }
            else
            if (string.match(ipRegex)){
                result = 'Создать админа по IP-адресу';
            }
            else
            if (string.match(usrPassRegex)){
                result = 'Создать админа по имени и паролю';
            }

            $('#setAdminMsg').text(result);

        }

        $("#admString").keyup(function() {
                                matchMsg();
                                return false;
        });

        function checkFps() {
            var fps = parseInt($('#fpsmax').val());
            var fpsMax = <?php echo $fpsmax; ?>;

            if (fps < 30) {
                $('#fpsMsg').text('Минимум 30FPS').addClass('red');
                $('#fpsButton').attr('disabled','disabled');
            } else if (fps >= 30 && fps <= fpsMax) {
                $('#fpsMsg').text('от 30 до ' + fpsMax + 'FPS').removeClass('red');
                $('#fpsButton').removeAttr('disabled');
            } else if (fps > fpsMax) {
                $('#fpsMsg').text('Максимум ' + fpsMax + 'FPS').addClass('red');
                $('#fpsButton').attr('disabled','disabled');
            }
        }

        $("#fpsmax").keyup(function() {
                                checkFps();
                                return false;
        });

        checkFps();

        $("#hostCollection").keyup(function() {
                                $("#hostMapList").val('0');
                                return false;
        });

    });
</script>
<?php
    echo $this->Js->writeBuffer(); // Write cached scripts
?>
