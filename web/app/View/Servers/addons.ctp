<div id="addons" style="min-height: 600px;">
	<div class="ui active inverted dimmer" data-bind="visible: loading">
	    <div class="ui text loader">Загрузка</div>
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
	<div class="ui horizontal divider" data-bind="if: installedMods().length > 0">Установленные моды</div>
	<div class="ui divided list" data-bind="if: installedMods">
		<!-- ko foreach: {data: installedMods, as: 'item'} -->
		<div class="item">
			<div class="right floated compact ui red button"><i class="repeat icon"></i> Переустановить</div>
			<i class="green toggle on large icon"></i>
			<div class="content">
				<div class="header" data-bind="text: item.Mod.longname"></div>
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
			<div class="right floated compact ui button popup-titles"><i class="upload icon"></i> FTP</div>
			<div class="ui popup">Нажмите, если самостоятельно загрузили этот мод на сервер по FTP</div>
			<div class="right floated compact ui green button"><i class="download icon"></i> Установить</div>
			<i class="toggle off large icon"></i>
			<div class="content">
				<div class="header" data-bind="text: item.Mod.longname"></div>
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
			<div class="right floated compact ui green button"><i class="download icon"></i> Установить</div>
			<i class="toggle on large icon"></i>
			<div class="content">
				<div class="header" data-bind="text: item.Plugin.longname"></div>
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
			<div class="right floated compact ui button popup-titles"><i class="upload icon"></i> FTP</div>
			<div class="ui popup">Нажмите, если самостоятельно загрузили этот плагин на сервер по FTP</div>
			<div class="right floated compact ui green button"><i class="download icon"></i> Установить</div>
			<i class="toggle off large icon"></i>
			<div class="content">
				<div class="header" data-bind="text: item.Plugin.longname"></div>
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
        this.loading = ko.observable(false);
        this.infos   = ko.observableArray([]);
        this.errors  = ko.observableArray([]);

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

        this.rebuildModal = function(){
        	$('#indexModal').modal('show');
        }

		$.getJSON('/servers/pluginInstall/' + this.serverId() + '.json')
         .done(
                function(data){
                    answer = data;
                    if (answer.error !== undefined && answer.error.length > 0){
                        self.errors.push(answer.error);
                    }
                    else
                    {
                        self.success(true);

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
            $('.popup-titles').popup({inline: true, position: 'bottom left'});
         });
	};

	ko.cleanNode(document.getElementById("addons"));
    ko.applyBindings(new addonsViewModel(), document.getElementById("addons"));
</script>
