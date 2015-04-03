<div id="addons" style="min-height: 600px;">
	<div class="ui active inverted dimmer" data-bind="visible: loading">
	    <div class="ui text loader">Загрузка</div>
	</div>
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
	<div class="ui icon warning message" data-bind="visible: installedMods().length == 0">
		<i class="info icon"></i>
        <div class="content">
            <div class="header">
            Не установлено ни одного мода
            </div>
            <p>Прежде, чем устанавливать плагины, необходимо установить мод сервера. Щелкните по имени мода ниже, либо подтвердите, что уже установили мод самостоятельно по FTP.</p>
        </div>
	</div>
	<div class="ui small icon red message">
		<i class="info icon"></i>
		<div class="content">
            <p>При установке новой версии мода или плагина, если уже установлена старая, новая версия будет записана поверх старой, <b>без сохранения ваших настроек!</b> Сохраните их через FTP или с помощью редактора файлов конфигурации, если требуется!</p>
        </div>
	</div>
	<div class="ui horizontal divider" data-bind="if: installedMods().length > 0">Установленные моды</div>
	<div class="ui divided list" data-bind="if: installedMods">
		<!-- ko foreach: {data: installedMods, as: 'item'} -->
		<div class="item">
			<div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'install', 'mod', false)}"><i class="repeat icon"></i> Переустановить</div>
			<i class="green toggle on large icon"></i>
			<div class="content">
				<div class="header">
					<span data-bind="text: item.Mod.longname"></span>
					<span data-bind="text: item.Mod.version"></span>
				</div>
				<div class="description">
					<span data-bind="text: item.Mod.shortDescription"></span>
					<a data-bind="event: {click: $root.descShow.bind($data, 'mod', item.Mod.id)}, attr: {'id': 'mod_show_desc_' + item.Mod.id}">(Подробнее)</a>
					<a data-bind="event: {click: $root.descHide.bind($data, 'mod', item.Mod.id)}, attr: {'id': 'mod_hide_desc_' + item.Mod.id}" style="display: none;">(Скрыть)</a>
					<pre data-bind="html: item.Mod.description, attr: {'id': 'mod_desc_' + item.Mod.id}" style="display: none;"></pre>
				</div>
			</div>
		</div>
		<!-- /ko -->
	</div>
	<div class="ui horizontal divider" data-bind="if: availiableMods().length > 0">Доступные моды</div>
	<div class="ui divided list" data-bind="if: availiableMods">
		<!-- ko foreach: {data: availiableMods, as: 'item'} -->
		<div class="item">
			<div class="right floated compact ui button" title="Нажмите, если самостоятельно загрузили этот мод на сервер по FTP" data-bind="event: {click: $root.action.bind($data, 'install', 'mod', true)}"><i class="upload icon"></i> FTP</div>
			<div class="right floated compact ui green button" data-bind="event: {click: $root.action.bind($data, 'install', 'mod', false)}"><i class="download icon"></i> Установить</div>
			<i class="toggle off large icon"></i>
			<div class="content">
				<div class="header">
					<span data-bind="text: item.Mod.longname"></span>
					<span data-bind="text: item.Mod.version"></span>
				</div>
				<div class="description">
					<span data-bind="text: item.Mod.shortDescription"></span>
					<a data-bind="event: {click: $root.descShow.bind($data, 'mod', item.Mod.id)}, attr: {'id': 'mod_show_desc_' + item.Mod.id}">(Подробнее)</a>
					<a data-bind="event: {click: $root.descHide.bind($data, 'mod', item.Mod.id)}, attr: {'id': 'mod_hide_desc_' + item.Mod.id}" style="display: none;">(Скрыть)</a>
					<pre data-bind="html: item.Mod.description, attr: {'id': 'mod_desc_' + item.Mod.id}" style="display: none;"></pre>
				</div>
			</div>
		</div>
		<!-- /ko -->
	</div>
	<div class="ui horizontal divider" data-bind="if: installedPlugins().length > 0">Установленные плагины</div>
	<div class="ui divided list" data-bind="if: installedPlugins">
		<!-- ko foreach: {data: installedPlugins, as: 'item'} -->
		<div class="item">
			<div class="right floated compact ui red icon button" title="Удалить плагин" data-bind="event: {click: $root.action.bind($data, 'delete', 'plugin', false)}"><i class="ban icon"></i></div>
			<div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'install', 'plugin', false)}"><i class="repeat icon"></i> Переустановить</div>
			<i class="toggle on green large icon"></i>
			<div class="content">
				<div class="header">
					<span data-bind="text: item.Plugin.longname"></span>
					<span data-bind="text: item.Plugin.version"></span>
				</div>
				<div class="description">
					<span data-bind="text: item.Plugin.shortDescription"></span>
					<a data-bind="event: {click: $root.descShow.bind($data, 'plugin', item.Plugin.id)}, attr: {'id': 'plugin_show_desc_' + item.Plugin.id}">(Подробнее)</a>
					<a data-bind="event: {click: $root.descHide.bind($data, 'plugin', item.Plugin.id)}, attr: {'id': 'plugin_hide_desc_' + item.Plugin.id}" style="display: none;">(Скрыть)</a>
					<pre data-bind="html: item.Plugin.description, attr: {'id': 'plugin_desc_' + item.Plugin.id}" style="display: none;"></pre>
				</div>
			</div>
		</div>
		<!-- /ko -->
	</div>
	<div class="ui horizontal divider" data-bind="if: availiablePlugins().length > 0">Доступные плагины</div>
	<div class="ui divided list" data-bind="if: availiablePlugins">
		<!-- ko foreach: {data: availiablePlugins, as: 'item'} -->
		<div class="item">
			<div class="right floated compact ui button" title="Нажмите, если самостоятельно загрузили этот мод на сервер по FTP" data-bind="event: {click: $root.action.bind($data, 'install', 'plugin', true)}"><i class="upload icon"></i> FTP</div>
			<div class="right floated compact ui green button" data-bind="event: {click: $root.action.bind($data, 'install', 'plugin', false)}"><i class="download icon"></i> Установить</div>
			<i class="toggle off large icon"></i>
			<div class="content">
				<div class="header">
					<span data-bind="text: item.Plugin.longname"></span>
					<span data-bind="text: item.Plugin.version"></span>
					<!-- ko foreach: {data: item.Tag, as: 'tag'}-->
						<div class="ui small label" data-bind="text: tag.name"></div>
					<!-- /ko -->
				</div>
				<div class="description">
					<span data-bind="text: item.Plugin.shortDescription"></span>
					<a data-bind="event: {click: $root.descShow.bind($data, 'plugin', item.Plugin.id)}, attr: {'id': 'plugin_show_desc_' + item.Plugin.id}">(Подробнее)</a>
					<a data-bind="event: {click: $root.descHide.bind($data, 'plugin', item.Plugin.id)}, attr: {'id': 'plugin_hide_desc_' + item.Plugin.id}" style="display: none;">(Скрыть)</a>
					<pre data-bind="html: item.Plugin.description, attr: {'id': 'plugin_desc_' + item.Plugin.id}" style="display: none;"></pre>
				</div>
			</div>
		</div>
		<!-- /ko -->
	</div>
	<div class="ui primary fluid small button" data-bind="visible: installedMods().length > 0, event: {click: action.bind($data, 'resync', false, false)}">Синхронизировать список плагинов</div>
</div>
<script type="text/javascript">
	var addonsViewModel = function(){
		var self = this;

		this.serverId = ko.observable(<?php echo $serverId; ?>)

		this.availiableMods    = ko.observableArray();
		this.availiablePlugins = ko.observableArray();
		this.installedMods     = ko.observableArray();
		this.installedPlugins  = ko.observableArray();

		this.success = ko.observable(false);
        this.loading = ko.observable(true);
        this.infos   = ko.observableArray([]);
        this.errors  = ko.observableArray([]);
        this.showInfos = ko.observable(false);

        this.descShow = function(type, id){
        	var self = this;

        	$('#' + type + '_desc_' + id).show();
        	$('#' + type + '_show_desc_' + id).hide();
        	$('#' + type + '_hide_desc_' + id).show();
        	$('#indexModal').modal('refresh');

        }.bind(this);

        this.descHide = function(type, id){
        	var self = this;

        	$('#' + type + '_desc_' + id).hide();
        	$('#' + type + '_show_desc_' + id).show();
        	$('#' + type + '_hide_desc_' + id).hide();

        }.bind(this);

        this.action = function(action, type, manual, item){
        	var self = this;

        	if (type == 'mod'){
        		var addonId = item.Mod.id;
        	} else if (type == 'plugin') {
        		var addonId = item.Plugin.id;
        	} else if (action != 'resync' && action != 'delete') {
        		return false;
        	}

        	self.loading(true);

        	if (manual === true){
        		var url = '/servers/pluginInstall/'
        				+ this.serverId()
        				+ '/' + addonId
        				+ '/' + type
        				+ '/manual.json';
        	} else if (action == 'resync'){
        		var url = '/servers/pluginResync/'
        				+ this.serverId()
        				+ '.json';
        	} else if (action == 'delete'){
        		var url = '/servers/pluginDelete/'
        				+ this.serverId()
        				+ '/' + addonId
        				+ '.json';
        	}else {
        		var url = '/servers/pluginInstall/'
        				+ this.serverId()
        				+ '/' + addonId
        				+ '/' + type
        				+ '.json';
        	}

        	self.infos([]);
        	self.errors([]);
        	self.showInfos(false);

        	$.getJSON(url)
	         .done( function(data){
	                    answer = data.result;

	                    if (answer.error !== undefined && answer.error.length > 0){
	                        self.errors.push(answer.error);
	                    }
	                    else
	                    {
	                        self.success(true);

	                        if (type == 'mod'){
				        		self.availiableMods.remove(item);
				        		self.loadData();
				        	} else if (type == 'plugin' && action == 'install') {
				        		self.installedPlugins.push(item);
				        		self.availiablePlugins.remove(item);
				        	} else if (action == 'resync') {
				        		self.loadData();
				        	} else if (action == 'delete') {
				        		self.installedPlugins.remove(item);
				        		self.availiablePlugins.unshift(item);
				        	}else {
				        		return false;
				        	}
	                    }

	                    if (answer.log !== undefined && $.isArray(answer.log)){
                            self.infos(answer.log);
                        } else if (answer.log !== undefined) {
                            self.infos([answer.log]);
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
	            $('#indexModal').modal('refresh');
	         });


        	}.bind(this);

		this.loadData = function(){
			var self = this;
			$.getJSON('/servers/pluginInstall/' + this.serverId() + '.json')
	         .done( function(data){
	                    answer = data;
	                    if (answer.error !== undefined && answer.error.length > 0){
	                        self.errors.push(answer.error);
	                    }
	                    else
	                    {
	                        if (answer.installedMod !== undefined
	                        		&& answer.installedMod.length > 0
	                        		&& answer.modsList !== undefined)
	                        {
	                        	$.each(answer.modsList, function(id, item){
	                        		if (Number($.inArray(item.Mod.id, answer.installedMod)) >= 0){
	                        			self.installedMods.push(item);
	                        		} else {
	                        			self.availiableMods.push(item);
	                        		}
	                        	})
	                        }
	                        else
	                        if (answer.modsList !== undefined
	                        			&& answer.modsList.length > 0)
	                        {
	                        	self.availiableMods(answer.modsList)
	                        }

	                        if (answer.installedPlugins !== undefined
	                        		&& answer.installedPlugins.length > 0
	                        		&& answer.pluginsList !== undefined)
	                        {
	                        	$.each(answer.pluginsList, function(id, item){
	                        		if (Number($.inArray(item.Plugin.id, answer.installedPlugins)) >= 0){
	                        			self.installedPlugins.push(item);
	                        		} else {
	                        			self.availiablePlugins.push(item);
	                        		}
	                        	})
	                        }
	                        else
	                        if (answer.pluginsList !== undefined
	                        			&& answer.pluginsList.length > 0)
	                        {
	                        	self.availiablePlugins(answer.pluginsList)
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
	            $('#indexModal').modal('refresh');
	            //$('.popup-titles').popup({inline: true, position: 'bottom left'});
	         });
        }.bind(this);

     	this.loadData();
	};

	ko.cleanNode(document.getElementById("addons"));
    ko.applyBindings(new addonsViewModel(), document.getElementById("addons"));
</script>
