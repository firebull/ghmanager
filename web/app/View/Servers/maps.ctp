<div id="maps" style="min-height: 600px;">

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
			<!-- ko if: item.canDelete && item.installed && item.official-->
			<div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'turnOff')}"><i class="ban icon"></i> Выключить</div>
			<!-- /ko -->
			<!-- ko if: item.canDelete && item.installed && !item.official-->
			<div class="right floated compact ui orange button" data-bind="event: {click: $root.action.bind($data, 'delete')}"><i class="remove circle icon"></i> Удалить</div>
			<!-- /ko -->
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

		this.success = ko.observable(false);
        this.loading = ko.observable(true);
        this.infos   = ko.observableArray([]);
        this.errors  = ko.observableArray([]);
        this.showInfos = ko.observable(false);
        this.showDesc = ko.observable(false);

        this.action = function(action){
        	var self = this;
        }.bind(this);

        this.showMaps = function(){
        	var self = this;

        	if (self.showType() == 'installed'){
    			self.installedMaps(self.fullMapList()['installed']);
    			self.availiableMaps([]);
    		} else {
    			self.installedMaps([]);
    			self.availiableMaps([]);

    			if (self.fullMapList()[self.showType()] !== undefined){
    				$.each(self.fullMapList()[self.showType()], function(id, item){
    					if (item.installed){
    						self.installedMaps.push(item);
    					} else {
    						self.availiableMaps.push(item);
    					}
    				});
    			}
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

	                        	if (answer.fullMapList[self.showType()] !== undefined){
	                        		if (self.showType() == 'installed'){
	                        			self.installedMaps(answer.fullMapList['installed']);
	                        			self.availiableMaps([]);
	                        		} else {

	                        		}
	                        	}
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
	            $('#indexModal').modal('refresh');
	            //$('.popup-titles').popup({inline: true, position: 'bottom left'});
	         });
        }.bind(this);

     	this.loadData();
	};

	ko.cleanNode(document.getElementById("maps"));
    ko.applyBindings(new mapsViewModel(), document.getElementById("maps"));
</script>
