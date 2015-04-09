<?php
/*
 * Created on 08.04.2015
 *
 * File created for project GH Mananger
 * by Nikita Bulaev
 */
?>
<div id="maps">
<div class="ui green message" data-bind="visible: success, text: success"></div>
<div class="ui negative message" data-bind="visible: errors().length > 0">
    <ul data-bind="foreach: {data: errors, as: 'error'}">
        <li>
            <span data-bind="text: error"></span>:
        </li>
    </ul>
</div>
    <div class="ui form">
        <div class="two fields">
            <div class="field">
            <select class="ui dropdown"
                data-bind="options: mapTypes,
                           optionsText: function(item){
                                if (item.name != 'installed'){
                                    return item.name + ': ' + item.longname;
                                } else {
                                    return item.longname;
                                }
                           },
                           optionsValue: 'name',
                           value: showType,
                           event: {change: showMaps}"></select>
            </div>
        </div>
    </div>
    <!-- ko if: mapOutput() == 'list'-->
    <div class="ui horizontal divider" data-bind="if: installedMaps().length > 0">Установленные карты</div>
    <div class="ui large divided list" data-bind="if: installedMaps && mapOutput() == 'list'">
        <!-- ko foreach: {data: installedMaps, as: 'item'} -->
        <div class="item">
            <!-- ko if: item.canDelete && item.installed() && item.official && item.on()-->
            <div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'turnOff')}"><i class="ban icon"></i> Выключить</div>
            <!-- /ko -->
            <!-- ko if: item.canDelete && item.installed() && item.official && !item.on()-->
            <div class="right floated compact ui green button" data-bind="event: {click: $root.action.bind($data, 'turnOn')}"><i class="add circle icon"></i> Включить</div>
            <!-- /ko -->
            <!-- ko if: item.canDelete && item.installed() && !item.official-->
            <div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'delete')}"><i class="remove circle icon"></i> Удалить</div>
            <!-- /ko -->
            <div class="ui red pointing right floated label" data-bind="visible: $root.showErrorLabel() == item.id">Ошибка</div>
            <img class="ui top aligned tiny image" src="/img/personage01.png" data-bind="if: item.image, attr: {'src': item.image}">
            <a class="content" data-bind="event: {click: $root.showDescAction.bind($data)}">
                <div class="header">
                    <span data-bind="text: item.name"></span>
                </div>
                <!-- ko if: $root.mapTypesByName()[item.map_type] !== undefined -->
                <div  class="description" data-bind="text: $root.mapTypesByName()[item.map_type]['longname']"></div>
                <!-- /ko -->

            </a>
            <div class="ui secondary inverted segment" data-bind="visible: $root.showDesc() == item.id">
                <!-- ko if: item.image -->
                <img class="ui left floated image" data-bind="if: item.image, attr: {'src': item.image}">
                <!-- /ko -->
                <!-- ko if: !item.image -->
                <img class="ui left floated image" src="/img/personage01.png">
                <!-- /ko -->
                <div class="ui header" data-bind="text: item.longname"></div>
                <div class="ui subheader" data-bind="text: item.name"></div>
                <!-- ko if: $root.mapTypesByName()[item.map_type] !== undefined -->
                <div  class="ui subheader" data-bind="text: $root.mapTypesByName()[item.map_type]['longname']"></div>
                <!-- /ko -->
                <div class="ui subheader" data-bind="visivle: item.official">Официальная карта</div>
                <p data-bind="html: item.desc"></p>
            </div>
        </div>
        <!-- /ko -->
    </div>
    <div class="ui horizontal divider" data-bind="if: availiableMaps().length > 0">Доступные карты</div>
    <div class="ui large divided list" data-bind="if: availiableMaps && mapOutput() == 'list'">
        <!-- ko foreach: {data: availiableMaps, as: 'item'} -->
        <div class="item">
            <div class="right floated compact ui green button" data-bind="event: {click: $root.action.bind($data, 'install')}"><i class="download icon"></i> Установить</div>
            <div class="ui red pointing right floated label" data-bind="visible: $root.showErrorLabel() == item.id">Ошибка</div>
            <!-- ko if: item.image -->
            <img class="ui top aligned tiny image" src="/img/personage01.png" data-bind="if: item.image, attr: {'src': item.image}">
            <!-- /ko -->
            <a class="content" data-bind="event: {click: $root.showDescAction.bind($data)}">
                <div class="header">
                    <span data-bind="text: item.name"></span>
                </div>
                <!-- ko if: $root.mapTypesByName()[item.map_type] !== undefined -->
                <div  class="description" data-bind="text: $root.mapTypesByName()[item.map_type]['longname']"></div>
                <!-- /ko -->
            </a>
            <div class="ui secondary inverted segment" data-bind="visible: $root.showDesc() == item.id">
                <!-- ko if: item.image -->
                <img class="ui left floated image" src="/img/personage01.png" data-bind="if: item.image, attr: {'src': item.image}">
                <!-- /ko -->
                <!-- ko if: !item.image -->
                <img class="ui left floated image" src="/img/personage01.png">
                <!-- /ko -->
                <div class="ui header" data-bind="text: item.longname"></div>
                <div class="ui subheader" data-bind="text: item.name"></div>
                <!-- ko if: $root.mapTypesByName()[item.map_type] !== undefined -->
                <div  class="ui subheader" data-bind="text: $root.mapTypesByName()[item.map_type]['longname']"></div>
                <!-- /ko -->
                <div class="ui subheader" data-bind="visivle: item.official">Официальная карта</div>
                <p data-bind="html: item.desc"></p>
            </div>
        </div>
        <!-- /ko -->
    </div>

    <!-- /ko -->
</div>
<script type="text/javascript">
    var mapsViewModel = function(){
        var self = this;

        this.serverId    = ko.observable(<?php echo $serverId; ?>);
        this.mapOutput   = ko.observable('<?php echo $output; ?>');
        this.fullMapList = ko.observableArray();
        this.showType    = ko.observable('installed');
        this.installedMaps  = ko.observableArray();
        this.availiableMaps = ko.observableArray();
        this.mapTypes = ko.observableArray();
        this.mapTypesByName = ko.observableArray();

        this.success   = ko.observable(false);
        this.loading   = ko.observable(true);
        this.infos     = ko.observableArray([]);
        this.errors    = ko.observableArray([]);
        this.showInfos = ko.observable(false);
        this.showDesc  = ko.observable(false);
        this.showErrorLabel = ko.observable(false);
        this.showLoadingButton = ko.observable(false);

        this.action = function(action, item){
            var self = this;

            if (!item.canDelete){
                return false;
            }

            self.loading(true);
            self.showLoadingButton(item.id);

            var url = '/servers/mapInstall/' + self.serverId() + '/' + item.id + '/installed/' + action + '.json';

            $.getJSON(url)
             .done( function(data){
                        answer = data.result;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                            self.showErrorLabel(item.id);
                            self.success(false);
                        }
                        else
                        {
                            if (action == 'install') {
                                var okMessage  = 'установлена на сервер';
                                self.availiableMaps.remove(item);
                                item.on(true);
                                item.installed(true);
                                self.installedMaps.push(item);
                                self.fullMapList().installed.push(item);
                            } else if (action == 'delete') {
                                var okMessage  = 'удалена с сервера';

                                $.each(self.fullMapList().installed, function(index,element){
                                    if (element !== undefined && element.id == item.id){
                                        self.fullMapList().installed.splice(index, 1);
                                    }
                                });

                                self.installedMaps.remove(item);

                                item.on(false);
                                item.installed(false);

                                if (self.showType() != 'installed'){
                                    self.availiableMaps.unshift(item);
                                }
                            } else if (action == 'turnOn') {
                                var okMessage  = 'включена в конфигах';
                                item.on(true);
                            } else if (action == 'turnOff') {
                                var okMessage  = 'отключена в конфигах';
                                item.on(false);
                            }

                            okMessage = 'Карта успешно ' + okMessage + '. Перегрузите сервер для применения изменений.';

                            self.success(okMessage);
                            self.showErrorLabel(false);
                        }
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + data.status + ' ' + statusText;
                    self.errors.push(answer);
                    self.showErrorLabel(item.id);
                }
             })
             .always(function(){
                self.loading(false);
                self.showLoadingButton(false);
             });

        }.bind(this);

        this.showMaps = function(){
            var self = this;


                self.installedMaps([]);
                self.availiableMaps([]);

                if (self.fullMapList()[self.showType()] !== undefined){
                    $.each(self.fullMapList()[self.showType()], function(id, item){
                        if (item.installed()){
                            self.installedMaps.push(item);
                        } else {
                            self.availiableMaps.push(item);
                        }
                    });
                }

            $('#indexModal').modal('show');
        }.bind(this);

        this.showDescAction = function(item){
            var self = this;

            if (self.showDesc() == item.id){
                self.showDesc(false);
            } else {
                self.showDesc(item.id);
            }

            return true;

        }.bind(this);

        this.loadData = function(){
            var self = this;
            $('#maps').attr('style', 'min-height: 800px;')
            $.getJSON('/servers/mapInstall/' + this.serverId() + '.json')
             .done( function(data){
                        answer = data;
                        if (answer.error !== undefined && answer.error.length > 0){
                            self.errors.push(answer.error);
                        }
                        else
                        {
                            if (answer.fullMapList !== undefined){
                                self.fullMapList(answer.fullMapList);
                                var installed = [];
                                $.each(self.fullMapList(), function(type, maps){
                                    $.each(maps, function(index, map){
                                        self.fullMapList()[type][index]['installed'] = ko.observable(map.installed);
                                        self.fullMapList()[type][index]['on'] = ko.observable(map.on);

                                        if (map.installed() === true){
                                            installed.push(map);
                                        }
                                    });
                                });

                                self.fullMapList()['installed'] = installed;
                            }

                            if (answer.mapTypes !== undefined){
                                self.mapTypes(answer.mapTypes);
                            }

                            if (answer.mapTypesByName !== undefined){
                                self.mapTypesByName(answer.mapTypesByName);
                            }

                        }
                    })
             .fail( function(data, status, statusText) {
                if (data.status == 401){
                    window.location.href = "/users/login";
                } else {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                }
             })
             .always(function(){
                self.loading(false);
                $('#maps').attr('style', '')
                $('#indexModal').modal('show');
                //$('.popup-titles').popup({inline: true, position: 'bottom left'});
             });
        }.bind(this);

        this.loadData();
    };

    ko.cleanNode(document.getElementById("maps"));
    ko.applyBindings(new mapsViewModel(), document.getElementById("maps"));
</script>
