
<div id="usersServersIndex">
	<div class="ui padded grid">
		<div class="ui row">
		<div class="five wide white column">
			<div class="ui top attached block header" data-bind="visible: gameServers().length > 0">Игровые серверы</div>
			<div class="ui divided link items" data-bind="visible: gameServers().length > 0, foreach: {data: gameServers, afterRender: defineSelected.bind($data, 'game')}">

				<div class="item">
					<div class="ui tiny image">
					  <img data-bind="attr: {src: '/img/icons/servers/big/' + $root.serverIcon(GameTemplate.name)}" src="/img/personage01.png">
					</div>
					<div class="content" data-bind="template: {name: 'render-server-item'}"></div>
				</div>

			</div>
			<div class="ui top attached block header" data-bind="visible: voiceServers().length > 0">Голосовые серверы</div>
			<div class="ui divided link items" data-bind="visible: voiceServers().length > 0">
		    	<!-- ko foreach: voiceServers -->
				<div class="item">
					<div class="ui tiny image">
					  <img src="/img/bigicons/mumble.png">
					</div>
					<div class="content" data-bind="template: {name: 'render-server-item'}"></div>
				</div>
		    	<!-- /ko -->
		    </div>
		    <div class="ui top attached block header" data-bind="visible: eacServers().length > 0">Серверы EAC</div>
			<div class="ui divided link items" data-bind="visible: eacServers().length > 0">
		    	<!-- ko foreach: eacServers -->
				<div class="item">
					<div class="ui tiny image">
					  <img src="/img/bigicons/eac.png">
					</div>
					<div class="content">
						<div class="header">
							<span data-bind="text: '#' + Server.id"></span> <span data-bind="text: Server.name"></span>
							<span data-bind="text: GameTemplate.longname, visible: !Server.name"></span>
						</div>
						<div class="meta" data-bind="visible: Server.address && Server.port">
							<span data-bind="text: Server.address + ':' + Server.port"></span>
						</div>
					    <div class="meta">
					    	<span data-bind="text: GameTemplate.longname, visible: Server.name"></span>
					    </div>
					    <div class="extra">
							<div class="ui small label" data-bind="visible: Eac.id && Server.initialised">
								<i data-bind="css: {'green' : Eac.active, 'black': Eac.active === false}" class="circle icon"></i> <span data-bind="text: Eac.active ? 'Работает' : 'Выключен'"></span>
							</div>
							<div class="ui small label">
								<i data-bind="css: {green: $root.daysLeft(Server.payedTill) == 'payed', red: $root.daysLeft(Server.payedTill) == 'nonpayed'}" class="circle icon"></i> <span data-bind="text: $root.daysLeft(Server.payedTill) == 'payed' ? 'Оплачен' : 'Не оплачен'"></span>
							</div>
					    </div>
					</div>
				</div>
		    	<!-- /ko -->
		    </div>
		</div>
		<div class="ten wide left aligned white column" id="indexRightColumn">
			<div data-bind="visible: renderSelected,
			                template: {if: selectedType(),
			                           name: 'render-template-' + selectedType()
									   }">

			</div>
		</div>
		</div>
	</div>
</div>

<div class="ui small modal" id="indexModal">
	<i class="close icon"></i>
	<div class="header"></div>
	<div class="content">
		<div class="image" style="display: none;"></div>
		<div class="description"></div>
	</div>
	<div class="actions">
		<div class="ui button">Отмена</div>
		<div class="ui green button">OK</div>
	</div>
</div>

<script type="text/html" id="render-server-item">
	<div class="header"><span data-bind="text: '#' + Server.id"></span> <span data-bind="text: Server.name"></span><span data-bind="text: GameTemplate.longname, visible: !Server.name"></span></div>
	<div class="meta" data-bind="visible: Server.address && Server.port"><span data-bind="text: Server.address + ':' + Server.port"></span></div>
    <div class="meta"><span data-bind="text: GameTemplate.longname, visible: Server.name"></span></div>
    <div class="extra">
    	<div class="ui small label" data-bind="visible: $root.daysLeft(Server.payedTill) == 'payed' && Server.initialised === false"><div class="ui active mini inline loader"></div> Установка</div>
	    <div class="ui small label" data-bind="visible: Server.status && Server.initialised"><i data-bind="css: $root.serverIconClass(Server.status)" class="circle icon"></i> <span data-bind="text: $root.serverStatus(Server.status)"></span></div>
		<div class="ui small label"><i data-bind="css: {green: $root.daysLeft(Server.payedTill) == 'payed', red: $root.daysLeft(Server.payedTill) == 'nonpayed'}" class="circle icon"></i> <span data-bind="text: $root.daysLeft(Server.payedTill) == 'payed' ? 'Оплачен' : 'Не оплачен'"></span></div>
    </div>
</script>

<script type="text/html" id="render-template-game">
	<div class="ui top attached block header">
		<img data-bind="attr: {src: '/img/icons/servers/big/' + serverIcon(gameServers()[selectedServer()].GameTemplate.name)}" src="/img/personage01.png"/>
		<div class="content">
			<span data-bind="text: '#' + gameServers()[selectedServer()].Server.id"></span>
			<span data-bind="text: gameServers()[selectedServer()].Server.name"></span>
			<span data-bind="visible: gameServers()[selectedServer()].Server.address, html : ' <i class=\'long arrow right icon\'></i>IP: ' + gameServers()[selectedServer()].Server.address + ':' + gameServers()[selectedServer()].Server.port"></span>
			&nbsp;&nbsp;
			<div class="ui active inline loader" data-bind="visible: loadingModal"></div>
		</div>

	</div>
	<div class="ui bottom attached segment" data-bind="visible: !gameServers()[selectedServer()].Server.initialised">
		<div class="ui active inline loader"></div> Идёт установка сервера, подождите немного.
	</div>
	<div class="ui labeled icon fluid menu bottom attached" data-bind="visible: gameServers()[selectedServer()].Server.initialised">
		<!-- Продление только для инициализированного сервера
		     Иконка для продления оплаты игрового сервера -->
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Продлить аренду сервера', '/orders/prolongate/' + gameServers()[selectedServer()].Server.id)}, visible: gameServers()[selectedServer()].Server.initialised">
			<i class="add to cart icon"></i>Продлить
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, 'small', 'Изменить имя сервера', '/servers/changeName/' + gameServers()[selectedServer()].Server.id)}">
			<i class="edit icon"></i>Имя сервера
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Изменить параметры запуска сервера', '/servers/editStartParams/' + gameServers()[selectedServer()].Server.id)}">
			<i class="setting icon"></i>Параметры
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Изменить настройки сервера', '/servers/editParams/' + gameServers()[selectedServer()].Server.id)}">
			<i class="file text icon"></i>Настройки
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Установка модов и плагинов', '/servers/pluginInstall/' + gameServers()[selectedServer()].Server.id)}">
			<i class="suitcase icon"></i>Плагины
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Установка карт', '/servers/mapInstall/' + gameServers()[selectedServer()].Server.id)}, visible: jQuery.inArray(gameServers()[selectedServer()].GameTemplate.name, ['css', 'cssv34', 'dods', 'tf', 'cs16', 'cs16-old']) != -1">
			<i class="bomb icon"></i>Карты
		</a>
	</div>
</script>

<script type="text/javascript">

	$('#indexModal').modal();

	var userServersViewModel = function(){
			this.gameServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Game']); ?>);

			this.voiceServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Voice']); ?>);

			this.eacServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Eac']); ?>);

			this.selectedServer = ko.observable(false);
			this.selectedType   = ko.observable(false);

			this.loading = ko.observable(false);
			this.loadingModal = ko.observable(false);
			this.errors = ko.observableArray([]);

			moment.locale('ru');

			console.log(this.gameServers());
			console.log(this.voiceServers());
			console.log(this.eacServers());

			this.serverIcon = function(game){
				if (game == 'cs16' || game == 'cs16-old'){
					return 'cs16.png';
				} else if (game == 'css' || game == 'cssv34') {
					return 'css.png';
				} else {
					return '';
				}
			}

			this.serverStatus = function(status){
				if (status == 'stoped' || status == 'stopped') {
					return 'Выключен';
				} else if (status == 'update_started') {
					return 'Обновление';
				} else if (status == 'update_error' || status == 'exec_error') {
					return 'Ошибка';
				} else if (status == 'exec_success') {
					return 'Работает';
				} else {
					return 'Неизвестно'
				}

			};

			this.serverIconClass = function(status){
				if (status == 'stoped' || status == 'stopped') {
					return 'black';
				} else if (status == 'update_started') {
					return 'purple';
				} else if (status == 'update_error' || status == 'exec_error') {
					return 'red';
				} else if (status == 'exec_success') {
					return 'green';
				} else {
					return ''
				}

			};

			this.daysLeft = function(date){
				var left = moment().diff(date, 'days', true);
				if (left < 0){
					return 'payed';
				}
				else
				{
					return 'nonpayed';
				}
			}.bind(this);

			this.defineSelected = function(type, event, server){
				var self = this;

				if (self.selectedServer() === false){
					if (type == 'eac'){
						if (self.daysLeft(server.Server.payedTill) == 'payed'){
							self.selectedServer(self.eacServers().indexOf(server));
							self.selectedType('eac');
						}
					} else if (type == 'game') {
						if (server.Server.initialised === true
								&& self.daysLeft(server.Server.payedTill) == 'payed'){
							self.selectedServer(self.gameServers().indexOf(server));
							self.selectedType('game');
						}
					} else if (type == 'voice') {
						if (server.Server.initialised === true
								&& self.daysLeft(server.Server.payedTill) == 'payed'){
							self.selectedServer(self.voiceServers().indexOf(server));
							self.selectedType('voice');
						}
					}
				}

				return true;

			}.bind(this);

			this.renderSelected = ko.computed(function() {
				var self = this;

				if (self.selectedServer() !== false)
				{

					return true;
				}

		        return false;
		    }, this);

			this.showModal = function(size, title, bodyUrl, data){
				var self = this;

				$('#indexModal').removeClass('small').addClass(size);
				$('#indexModal .header').html(title);

				self.loadingModal(true);

				$.get( bodyUrl )
		    	 .done(
			    	 	function(data){

			    	 		$('#indexModal .content .description').html(data);
							$('#indexModal').modal('show');

							self.loadingModal(false);
						})
		    	 .fail( function(data, status, statusText) {
		    	 	answer = "HTTP Error: " + statusText;
		    	 	self.errors.push(answer);
		    	 	self.loadingModal(false);
		    	 });

			}.bind(this);

		};

	ko.applyBindings(new userServersViewModel(), document.getElementById("usersServersIndex"));
</script>



