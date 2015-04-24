<?php
/*
 * Created on 23.03.2015
 *
 * File created for project GHManager
 * by Nikita Bulaev
 */
?>
<div id="server_params">
    <div id="flash"><?php echo $this->Session->flash(); ?></div>
    <div class="ui segment" style="margin-top: 0px !important;">
        <a class="ui tiny header" data-bind="event: {click: setConfigOwner.bind($data, 'server')}" style="cursor: pointer;">
            <span data-bind="css: {'red': configsOwner() == 'server'}">Серверные конфиги</span>
            <div class="ui sub header" data-bind="css: {'red': configsOwner() == 'server'}">Основные настройки сервера</div>
        </a>
        <div class="ui padded grid" data-bind="if: $root.configsOwner() == 'server'">
            <div class="ui four wide column" data-bind="template: {name: 'render-config-menu', data: serverConfigs, as: 'configs'}" style="width: 24,9% !important;"></div>
            <div class="ui twelve wide column" id="editor_server">
                <div class="ui active inverted dimmer" data-bind="visible: loading">
                    <div class="ui text loader">Загружаю</div>
                </div>
                <div id="configEditor">
                    <div class="ui icon message">
                        <i class="info icon"></i>
                        <div class="content">
                            Выберите конфиг для редактирования слева или другой мод/плагин из общего списка.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui horizontal divider" data-bind="visible: modsList().length">Моды</div>
    <div class="ui segments" data-bind="template: {name: 'render-main-list', foreach: modsList, as: 'renderData'}"></div>

    <div class="ui horizontal divider" data-bind="visible: userModsList().length > 0"><?php echo __('User mods');?></div>
    <div class="ui segments" data-bind="template: {name: 'render-main-list', foreach: userModsList, as: 'renderData'}"></div>

<?php
    echo $this->Js->writeBuffer(); // Write cached scripts
?>

</div>
<script type="text/html" id="render-main-list">
    <div class="ui segment">
        <a class="ui tiny header" data-bind="event: {click: $root.setConfigOwner.bind($data,renderData.name)}" style="cursor: pointer;">
            <span data-bind="text: renderData.longname, css: {'red': $root.configsOwner() == renderData.name}"></span>
            <div class="sub header" data-bind="text: renderData.shortDescription, css: {'red': $root.configsOwner() == renderData.name}"></div>
        </a>
        <div class="ui padded grid" data-bind="if: $root.configsOwner() == renderData.name">
            <!-- ko if: renderData.type === undefined || renderData.type == 'known_mod'-->
            <div class="ui four wide column" data-bind="template: {name: 'render-config-menu', data: renderData.Config, as: 'configs'}" style="width: 24,9% !important;"></div>
            <!-- /ko -->
            <!-- ko if: renderData.type !== undefined && renderData.type == 'user_mod'-->
            <div class="ui four wide column" style="width: 24,9% !important;">
                <div class="ui fluid vertical small pointing menu">
                    <a class="item" data-bind="event: {click: $root.editConfig.bind($data, renderData.name)}, css: {'red active': $root.currentConfig() == renderData.name}">
                        <b>modserver.cfg</b><br/>
                        <small><?php echo __('Use this config for your mod');?></small>
                    </a>
                </div>
            </div>
            <!-- /ko -->
            <div class="ui twelve wide column" data-bind="attr: {'id': 'editor_' + renderData.name}">
                <div class="ui active inverted dimmer" data-bind="visible: $root.loading">
                    <div class="ui text loader">Загружаю</div>
                </div>
                <div id="configEditor">
                    <div class="ui icon message">
                        <i class="info icon"></i>
                        <div class="content">
                            Выберите конфиг для редактирования слева или другой мод/плагин из общего списка.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="render-config-menu">
    <div class="ui fluid vertical small pointing menu" data-bind="foreach: {data: configs, as: 'config'}">
        <a class="item" data-bind="event: {click: $root.editConfig.bind($data, config.id)}, css: {'red active': $root.currentConfig() == config.id}">
            <b data-bind="text: config.name"></b><br/>
            <small data-bind="text: config.shortDescription"></small>
        </a>
    </div>
</script>
<?php
    // Prepare data for Knockout.
    // This is needed as old View needs array generated in controller
    foreach ($mods as $key => $name) {
        $knownMods = Hash::extract($modsList, '{n}.name'); // Mods we know

        if (!in_array($name, $knownMods)){
            $userMods[] = ['type' => 'user_mod',
                           'name' => $name,
                           'longname' => ucfirst($name)];
        }
    }
?>
<script type="text/javascript">

    var serverParamsViewModel = function(){

        this.serverId = ko.observable(<?php echo $this->data['Server']['id'];?>);
        this.configsOwner   = ko.observable('server');
        this.serverConfigs  = ko.observableArray(<?php echo json_encode($serverConfigs);?>);
        this.modsList       = ko.observableArray(<?php echo json_encode($modsList);?>);
        this.userModsList   = ko.observableArray(<?php echo json_encode($userMods);?>);
        this.currentConfig  = ko.observable(false);

        this.loading = ko.observable(false);
        this.errors  = ko.observableArray();

        this.editConfig = function(id){
            var self = this;

            self.loading(true);

            $.get( '/servers/editConfigCommon/' + self.serverId() + '/' + id + '/read' )
             .done(
                    function(data, status, statusText){

                        $('#editor_' + self.configsOwner() + ' #configEditor').html(data);

                        self.currentConfig(id);
                        self.loading(false);
                    })
             .fail( function(data, status, statusText) {
                answer = "HTTP Error: " + statusText;
                self.errors.push(answer);
                self.loading(false);
             });

        }.bind(this);

        this.setConfigOwner = function(owner){
            var self = this;

            self.configsOwner(owner);
            self.currentConfig(false);

        }.bind(this);

    };

    ko.cleanNode(document.getElementById("server_params"));
    ko.applyBindings(new serverParamsViewModel(), document.getElementById("server_params"));
</script>
