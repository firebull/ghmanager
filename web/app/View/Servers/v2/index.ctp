
<div id="usersServersIndex">
	<div class="ui padded grid">
		<div class="ui equal height stretched row">
		<div class="six wide white column">
			<div class="ui top attached block header" data-bind="visible: gameServers().length > 0">Игровые серверы</div>
			<div class="ui divided selection list" data-bind="visible: gameServers().length > 0, foreach: {data: gameServers, afterRender: defineSelected.bind($data, 'game')}">

				<div class="item" data-bind="event: {click: $root.setSelected.bind($data, 'game')}">
					<div class="ui tiny_gh image">
						<img data-bind="visible: Status.image, attr: {src: Status.image}" />
					    <img data-bind="visible: !Status.image ,attr: {src: '/img/icons/servers/big/' + $root.serverIcon(GameTemplate['name'])}" src="/img/personage01.png"/>
					</div>
					<div class="content top aligned" data-bind="template: {name: 'render-server-item'}"></div>
				</div>

			</div>
			<div class="ui top attached block header" data-bind="visible: voiceServers().length > 0">Голосовые серверы</div>
			<div class="ui divided selection list" data-bind="visible: voiceServers().length > 0">
		    	<!-- ko foreach: voiceServers -->
				<div class="item" data-bind="event: {click: $root.setSelected.bind($data, 'voice')}">
					<div class="ui mini_gh image">
					  <img src="/img/bigicons/mumble.png">
					</div>
					<div class="content" data-bind="template: {name: 'render-server-item'}"></div>
				</div>
		    	<!-- /ko -->
		    </div>
		    <div class="ui top attached block header" data-bind="visible: eacServers().length > 0">Серверы EAC</div>
			<div class="ui divided selection list" data-bind="visible: eacServers().length > 0">
		    	<!-- ko foreach: eacServers -->
				<div class="item" data-bind="event: {click: $root.setSelected.bind($data, 'eac')}">
					<div class="ui mini_gh image">
					  <img src="/img/bigicons/eac.png">
					</div>
					<div class="content">
						<div class="header">
							<span data-bind="text: '#' + Server.id"></span> <span data-bind="text: Server.name"></span>
							<span data-bind="text: GameTemplate.longname, visible: !Server.name"></span>
						</div>
						<div class="description" data-bind="visible: Server.address && Server.port">
							<span data-bind="text: Server.address + ':' + Server.port"></span>
						</div>
					    <div class="description">
					    	<span data-bind="text: GameTemplate.longname, visible: Server.name"></span>
					    </div>
					    <div style="margin-top: 5px;">
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
	</div>
</div>

<script type="text/html" id="render-server-item">
	<div class="header">
		<span data-bind="text: '#' + Server.id"></span> <span data-bind="text: Server.name"></span><span data-bind="text: GameTemplate.longname, visible: !Server.name"></span>
	</div>
	<div class="description" data-bind="visible: Server.address && Server.port"><span data-bind="html: '<b>Адрес:</b> ' + Server.address + ':' + Server.port"></span></div>
	<div class="description" data-bind="visible: Status.mapName, html: '<b>Карта:</b> ' + Status.mapName"></div>
    <div class="description"><span data-bind="visible: Server.slots, html: $root.showSlots(Server.slots, Status.numberOfPlayers)"></span></div>
    <div style="margin-top: 5px;">
    	<div class="ui small label" data-bind="visible: $root.daysLeft(Server.payedTill) == 'payed' && Server.initialised === false"><div class="ui active mini inline loader"></div> Установка</div>
	    <div class="ui small label" data-bind="visible: Server.status && Server.initialised"><i data-bind="css: $root.serverIconClass(Server, Status)" class="circle icon"></i> <span data-bind="text: $root.serverStatus(Server, Status)"></span></div>
		<div class="ui small label"><i data-bind="css: {green: $root.daysLeft(Server.payedTill) == 'payed', red: $root.daysLeft(Server.payedTill) == 'nonpayed'}" class="circle icon"></i> <span data-bind="text: $root.daysLeft(Server.payedTill) == 'payed' ? 'Оплачен' : 'Не оплачен'"></span></div>
    </div>
</script>

<script type="text/html" id="render-template-game">
	<div class="ui top attached block header">
		<img data-bind="attr: {src: '/img/icons/servers/big/' + serverIcon(renderedServer().GameTemplate['name'])}" src="/img/personage01.png"/>
		<div class="content">
			<span data-bind='text: "\"" + renderedServer().Server.name + "\"", visible: gameServers()[selectedServer()].Server.name'></span>
			<span data-bind='text: renderedServer().GameTemplate.longname'></span>,
			<span data-bind='text: $root.privateType()'></span>
			<span data-bind="text: '(ID: ' + renderedServer().Server.id + ')'"></span>
			<br/>
			<span data-bind="visible: renderedServer().Server.address, text: renderedServer().Server.address + ':' + renderedServer().Server.port + ', '"></span>
			<span data-bind="visible: renderedServer().Location.name, text: renderedServer().Location.collocation + ' (' + renderedServer().Location.name + ')'"></span>
		</div>
		<div class="ui active inline loader" data-bind="visible: loadingModal"></div>

	</div>
	<div class="ui bottom attached segment" data-bind="visible: !renderedServer().Server.initialised && renderedServer().Server.payedTill">
		<div class="ui active inline loader"></div> Идёт установка сервера, подождите немного.
	</div>
	<div class="ui labeled icon fluid menu attached" data-bind="visible: renderedServer().Server.initialised">
		<!-- Продление только для инициализированного сервера
		     Иконка для продления оплаты игрового сервера -->
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Продлить аренду сервера', '/orders/prolongate/' + renderedServer().Server.id)}, visible: gameServers()[selectedServer()].Server.initialised">
			<i class="add to cart icon"></i>Продлить
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, 'small', 'Изменить имя сервера', '/servers/changeName/' + renderedServer().Server.id)}">
			<i class="edit icon"></i>Имя сервера
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Изменить параметры запуска сервера', '/servers/editStartParams/' + renderedServer().Server.id)}">
			<i class="setting icon"></i>Параметры
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, 'fullscreen', 'Изменить настройки сервера', '/servers/editParams/' + renderedServer().Server.id)}">
			<i class="file text icon"></i>Настройки
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, 'fullscreen', 'Просмотр логов сервера', '/servers/viewLog/' + renderedServer().Server.id + '/run')}">
			<i class="file text outline icon"></i>Логи
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, '', 'Установка модов и плагинов', '/servers/pluginInstall/' + renderedServer().Server.id)}">
			<i class="suitcase icon"></i>Плагины
		</a>
		<a class="item" data-bind="event: {click: $root.showModal.bind($data, 'large', 'Установка карт', '/servers/mapInstall/' + renderedServer().Server.id)}, visible: jQuery.inArray(renderedServer().GameTemplate['name'], ['css', 'cssv34', 'dods', 'tf', 'cs16', 'cs16-old']) != -1">
			<i class="bomb icon"></i>Карты
		</a>
		<a class="item" data-bind="event: {click: setShowType.bind($data, 'server')}, visible: $root.showType() == 'hltv'">
			<i class="game icon"></i> Сервер
		</a>
		<a class="item" data-bind="event: {click: setShowType.bind($data, 'hltv')}, visible: $root.showType() == 'server'">
			<i class="film icon"></i> HLTV
		</a>
	</div>
	<div class="ui bottom attached segment">
		<div class="ui grid">
			<div class="ui row">
				<div class="ui column">
					<div class="ui active inverted dimmer" data-bind="visible: $root.executing">
					    <div class="ui small text loader">Выполняю</div>
					</div>
					<!-- Предупреждения и ошибки -->
					<!-- Информационные сообщения -->
					<div class="ui positive message" data-bind="visible: $root.infos().length > 0">
						<ul data-bind="foreach: {data: infos, as: 'info'}">
				            <li>
				                <span data-bind="text: info"></span>:
				            </li>
				        </ul>
					</div>
					<!-- Общие ошибки -->
					<div class="ui negative message" data-bind="visible: $root.errors().length > 0">
						<ul data-bind="foreach: {data: errors, as: 'error'}">
				            <li>
				                <span data-bind="text: error"></span>:
				            </li>
				        </ul>
					</div>
					<!-- Если сервер запущен более 5 минут назад и нет статуса -->
					<div class="ui negative message" data-bind="visible: renderedServer().Status.error && moment().diff(renderedServer().Server.statusTime, 'minutes', true) > 5">
						<div class="header">
							При попытке проверить состояние сервера, обнаружена ошибка:
						</div>
						<ul class="list">
							<li data-bind="text: renderedServer().Status.error"></li>
						</ul>

						Сервер был запущен более 5 минут назад, но до сих пор не удаётся прочесть его статус.<br/>
						Вероятными причинами этого могут быть:
						<ul class="list">
							<li>Сервер обновляется. В этом случае процесс запуска может затянуться. Читайте соответсвующий лог.</li>
							<li>Вы включили читы параметром 'sv_cheats 1'</li>
							<li>Вы внесли IP вашего сервера в бан-лист. Проверьте banned_ip.cfg на наличие в нем IP-адреса вашего сервера.</li>
						</ul>

						Если в логе нет данных, либо есть ошибки, попробуйте перезапустить сервер. Если не поможет - обратитесь в техподдержку.<br/>
						<small>Время статуса: <span data-bind="text: moment(renderedServer().Server.statusTime).format('HH:mm:ss DD.MM.YYYY')"></span></small>
					</div>
					<!-- Если сервер запущен менее 5 минут назад и нет статуса -->
					<div class="ui warning message" data-bind="visible: renderedServer().Status.error && moment().diff(renderedServer().Server.statusTime, 'minutes', true) <= 5">
						Сервер запущен менее 5 минут назад. <br/>
						Если сервер требует обновления, процесс запуска может затянуться. Читайте соответствующий лог.
					</div>
					<!-- Если запуск не удался -->
					<div class="ui error message" data-bind="visible: renderedServer().Server.status == 'exec_error'">
						Ошибка запуска. <br/>
						Попробуйте перезапустить сервер. <br/>
						Если не поможет - обратитесь в техподдержку
						<span data-bind="visible: renderedServer().Server.statusDescription, text: 'Причина: ' + renderedServer().Server.statusDescription"></span>
						<br/>
						<small>Время статуса: <span data-bind="text: moment(renderedServer().Server.statusTime).format('HH:mm:ss DD.MM.YYYY')"></span></small>
					</div>
					<div class="ui negative message" data-bind="visible: renderedServer().Server.crashCount > 0">
					С момента последнего запуска сервера было зафиксировано <span data-bind="text: Number(renderedServer().Server.crashCount)"></span> падений.<br/>Последнее падение было <span data-bind="text: moment(renderedServer().Server.crashTime).format('HH:mm:ss DD.MM.YYYY')"></span>
					</div>
					<div class="ui warning icon message" data-bind="visible: !renderedServer().Server.initialised && !renderedServer().Server.payedTill">
						<i class="info icon"></i>
						<div class="content">
							<div class="header">
							Сервер не оплачен.
							</div>
							<p>При отсутствии оплаты в течение 2-х недель с момента создания заказа, сервер и заказ будут удалены.</p>
						</div>
					</div>
					<!-- Конец предупреждений и ошибок -->
					<!-- Меню действий -->
					<div class="ui labeled icon fluid small pointing  menu" data-bind="visible: renderedServer().Server.initialised && $root.daysLeft(renderedServer().Server.payedTill) == 'payed' && $root.showType() == 'server'">
						<a class="item" data-bind="event: {click: actionLogEnable.bind($data)}, visible: actionLog().length > 0, css: {'active': actionLogShow}">
							<i class="info circle orange icon"></i> Журнал
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.status != 'exec_success', event: {click: $root.serverAction.bind($element, 'start')}"  id="serverStart">
							<i class="green play icon"></i> Включить
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.status != 'exec_success', event: {click: $root.serverAction.bind($element, 'startDebug')}"  id="serverStartDebug">
							<i class="video play outline icon"></i> Отладка
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.status != 'update_started' && renderedServer().Server.status != 'stopped', event: {click: $root.serverAction.bind($element, 'stop')}"  id="serverStop">
							<i class="orange stop icon"></i> Выключить
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.status == 'exec_success', event: {click: $root.serverAction.bind($element, 'restart')}" id="serverRestart">
							<i class="orange repeat icon"></i> Рестарт
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.status == 'exec_success', event: {click: $root.showRcon.bind($data, false)}, css: {'red active' : $root.showRconConsole() == 'server'}">
							<i class="terminal icon"></i> RCON
						</a>
						<a class="item"><i class="download icon"></i> Обновление</a>
					</div>
					<div class="ui labeled icon fluid small pointing  menu" data-bind="visible: renderedServer().Server.initialised && $root.daysLeft(renderedServer().Server.payedTill) == 'payed' && renderedServer().Type.name == 'hlds' && $root.showType() == 'hltv'">
						<a class="item" data-bind="event: {click: actionLogEnable.bind($data)}, visible: actionLog().length > 0, css: {'active': actionLogShow}">
							<i class="info circle orange icon"></i> Журнал
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.hltvStatus != 'exec_success', event: {click: $root.serverAction.bind($element, 'startHltv')}"  id="serverStartHltv">
							<i class="green play icon"></i> Включить HLTV
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.hltvStatus == 'exec_success', event: {click: $root.serverAction.bind($element, 'stopHltv')}">
							<i class="orange stop icon"></i> Выключить HLTV
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.hltvStatus == 'exec_success'">
							<i class="orange repeat icon"></i> Рестарт HLTV
						</a>
						<a class="item" data-bind="visible: renderedServer().Server.hltvStatus == 'exec_success', event: {click: $root.showRcon.bind($data, true)}, css: {'red active' : $root.showRconConsole() == 'hltv'}">
							<i class="terminal icon"></i> HLTV RCON
						</a>
					</div>
				</div>
			</div>
			<!-- Лог операций -->
			<div class="ui row" data-bind="visible: actionLogShow">
				<div class="ui column" style="height: 400px; overflow: auto;">
					<div class="ui small feed" data-bind="foreach: actionLog()">
						<div class="event">
							<div class="content">
								<div class="summary" data-bind="text: time"></div>
								<div class="extra text" style="max-width: 100% !important;">
									<pre data-bind="html: log"></pre>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="ui row" data-bind="visible: $root.showRconConsole">
				<div class="ui column" id="rconConsole" style="background-color: #272822; color: white;"></div>
			</div>
			<div class="ui equal height stretched row" data-bind="if: renderedServer().Server.initialised && $root.showType() == 'server'">
				<div class="ui eight wide column">
					<div class="ui top attached tertiary segment">
						<b>Статус сервера</b>
					</div>
					<div class="ui bottom attached segment" data-bind="visible: renderedServer().Status.error">
						Нет данных
					</div>
					<div class="ui attached segment" data-bind="visible: renderedServer().Server.status == 'stopped'">
						Выключен <span data-bind="visible: renderedServer().Server.statusTime, text: moment(renderedServer().Server.statusTime).calendar()"></span>
					</div>
					<div class="ui bottom attached segment">
						<div class="description" data-bind="visible: renderedServer().Status.serverName">
							<b>Имя:</b>
							<span data-bind="text: renderedServer().Status.serverName"></span>
						</div>
						<div class="description" data-bind="visible: renderedServer().Status.mapName, html: '<b>Карта:</b> ' + renderedServer().Status.mapName"></div>
	    				<div class="description">
	    					<span data-bind="visible: renderedServer().Server.slots, html: $root.showSlots(renderedServer().Server.slots, renderedServer().Status.numberOfPlayers)"></span>
	    				</div>
	    				<div class="description" data-bind="visible: renderedServer().Status.secureServer !== undefined">
	    					<b>Пароль:</b>
	    					<span data-bind="visible: $root.passwordState() == -1" class="red">Не установлен</span>
	    					<span data-bind="visible: $root.passwordState() == 0">Не установлен</span>
	    					<span data-bind="visible: $root.passwordState() == 1" class="green">Установлен</span>
	    				</div>
	    				<div class="description" data-bind="visible: renderedServer().Status.secureServer">
	    					<b>VAC:</b>
	    					<span data-bind="html: renderedServer().Status.secureServer ? '<span class=\'green\'>Активен</span>' : 'Выключен'"></span>
	    				</div>
	    				<div class="description" data-bind="visible: renderedServer().Status.gameVersion">
	    					<b>Версия:</b>
	    					<span data-bind="html: renderedServer().Status.gameVersion ==  renderedServer().GameTemplate.current_version ? '<span class=\'green\'>' + renderedServer().Status.gameVersion + '</span>' : '<span class=\'red\'>' + renderedServer().Status.gameVersion + '</span>'"></span>
	    				</div>
	    				<div
	    					class="description"
	    					data-bind="
	    					css: {'green': Number(renderedServer().Server.scaleTime) >= 0.3, 'orange': Number(renderedServer().Server.scaleTime) > 0.15 && Number(renderedServer().Server.scaleTime) < 0.3,
	    					'red': Number(renderedServer().Server.scaleTime) < 0.15}">
	    					<b>Аренда истекает:</b>
	    					<span data-bind="text: moment(renderedServer().Server.payedTill).fromNow()"></span>
	    				<?php /*
	    					<div
		    					class="ui tiny  progress"
		    					id="paymentBar"
		    					data-bind="
		    					css: {'green': Number(renderedServer().Server.scaleTime) >= 0.3, 'yellow': Number(renderedServer().Server.scaleTime) > 0.15 && Number(renderedServer().Server.scaleTime) < 0.3,
		    					'red': Number(renderedServer().Server.scaleTime) < 0.15}">
		    					<div class="bar" data-bind="attr: {'style': 'width:' + Number(renderedServer().Server.scaleTime)*100 + '%;'}"></div>
		    				</div>
		    			      */ ?>
	    				</div>
    				</div>
    				<img data-bind="visible: renderedServer().Status.image, attr: {src: renderedServer().Status.image}" />
				</div>
				<div class="ui eight wide column">
					<div class="ui top attached tabular menu" >
					    <a class="active item" data-tab="24h" id="playersStat24h">Сутки</a>
					    <a class="item" data-tab="7d" id="playersStat7d">Неделя</a>
					</div>
					<div class="ui bottom attached active tab segment" data-tab="24h" data-bind="visible: renderedServer().Status.graphs['24h']">
						<img data-bind="visible: renderedServer().Status.graphs['24h'], attr: {src: renderedServer().Status.graphs['24h']}" />
					</div>
					<div class="ui bottom attached tab segment" data-tab="7d" data-bind="visible: renderedServer().Status.graphs['7d']">
						<img data-bind="visible: renderedServer().Status.graphs['7d'], attr: {src: renderedServer().Status.graphs['7d']}" />
					</div>
					<small data-bind="visible: renderedServer().Status.graphs['24h'] || renderedServer().Status.graphs['7d']">Графики обновляются каждые 15 минут </small>

					<div class="ui small top attached block header" data-bind="visible: renderedServer().Server.status == 'exec_success'">Игроки</div>
    				<div class="ui bottom attached basic segment" data-bind="visible: renderedServer().Status.numberOfPlayers == 0">
    					На сервере нет игроков
    				</div>
    				<div class="ui bottom attached basic segment" data-bind="visible: renderedServer().Status.numberOfPlayers > 0">
    					<table class="ui very basic small table">
    						<thead>
    							<tr>
    								<th>#</th>
    								<th>Игрок</th>
    								<th>Счёт</th>
    								<th>Время</th>
    							</tr>
    						</thead>
							<tbody data-bind="template: {name: 'render-players-valve', foreach: renderedServer().Status.players, as: 'player' }"></tbody>
    					</table>
    				</div>
				</div>

			</div>
			<!-- HLTV -->
			<div class="ui row" data-bind="visible: renderedServer().Type.name == 'hlds' && $root.showType() == 'hltv'">
				<div class="ui eight wide column">
					<div class="ui top attached small block header">
						Статус HLTV
					</div>
					<div class="ui bottom attached segment" data-bind="visible: !renderedServer().Status.hltv && renderedServer().Server.hltvStatus == 'exec_success'">
						Нет данных
					</div>
					<div class="ui bottom attached segment" data-bind="visible: renderedServer().Server.hltvStatus == 'stopped'">
						HLTV сервер выключен <span data-bind="visible: renderedServer().Server.hltvStatusTime, text: moment(renderedServer().Server.hltvStatusTime).calendar()"></span>
					</div>
					<div class="ui bottom attached segment" data-bind="visible: renderedServer().Server.hltvStatus == 'exec_error'">
						<span class="red">Ошибка</span>
					</div>
					<div class="ui bottom attached segment" data-bind="if: renderedServer().Status.hltv, visible: renderedServer().Status.hltv && renderedServer().Server.hltvStatus == 'exec_success'">
						<div class="description" data-bind="visible: renderedServer().Status.hltv.hostname">
							<b>Имя:</b>
							<span data-bind="text: renderedServer().Status.hltv.hostname"></span>
						</div>
						<div class="description">
							<b>Порт:</b>
							<span data-bind="text: Number(renderedServer().Server.port) + 1015"></span>
						</div>
						<div class="description">
	    					<span data-bind="visible: renderedServer().Status.hltv.maxPlayers, html: $root.showSlotsHltv(renderedServer().Status.hltv.maxPlayers, renderedServer().Status.hltv.numberOfPlayers)"></span>
	    				</div>
	    				<div class="description" data-bind="visible: renderedServer().Status.hltv.HLTVDelay">
							<b>Задержка:</b>
							<span data-bind="text: renderedServer().Status.hltv.HLTVDelay + 'с.'"></span>
						</div>
						<div class="description" data-bind="visible: renderedServer().Status.hltv.password !== undefined">
	    					<b>Пароль:</b>
	    					<span data-bind="text: renderedServer().Status.hltv.password ? 'Установлен' : 'Не установлен'"></span>
	    				</div>
					</div>
				</div>
				<div class="ui eight wide column">

				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="render-template-voice">

</script>

<script type="text/html" id="render-template-eac">

</script>

<script type="text/html" id="render-players-valve">
	<tr>
		<td data-bind="text: player.id"></td>
		<td data-bind="text: player.name"></td>
		<td data-bind="text: player.score"></td>
		<td data-bind="text: player.connectTime"></td>
	</tr>
</script>

<script type="text/javascript">

	$('#indexModal').modal();

	var userServersViewModel = function(){
			var self = this;

			this.gameServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Game']); ?>);

			this.voiceServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Voice']); ?>);

			this.eacServers = ko.observableArray(<?php echo @json_encode($serversGrouped['Eac']); ?>);

			this.showType       = ko.observable('server');
			this.selectedServer = ko.observable(false);
			this.selectedType   = ko.observable(false);
			this.renderedServer = ko.observableArray();
			this.showRconConsole = ko.observable(false);

			this.executing    = ko.observable(false);
			this.loadingModal = ko.observable(false);
			this.errors       = ko.observableArray();
			this.infos        = ko.observableArray();
			this.actionLog    = ko.observableArray();
			this.actionLogShow = ko.observable(false);


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

			this.serverStatus = function(Server, state){
				if (Server.status == 'stoped' || Server.status == 'stopped') {
					return 'Выключен';
				} else if (Server.status == 'update_started') {
					return 'Обновление';
				} else if (Server.status == 'update_error' || Server.status == 'exec_error') {
					return 'Ошибка';
				} else if (Server.status == 'exec_success') {
					if (state.error !== undefined) {
						if (moment().diff(Server.statusTime, 'minutes', true) < 5){
							return 'Запускается';
						} else {
							return 'Ошибка';
						}
					} else {
						return 'Работает';
					}
				} else {
					return 'Неизвестно'
				}

			};

			this.serverIconClass = function(Server, state){
				if (Server.status == 'stoped' || Server.status == 'stopped') {
					return 'black';
				} else if (Server.status == 'update_started') {
					return 'purple';
				} else if (Server.status == 'update_error' || Server.status == 'exec_error') {
					return 'red';
				} else if (Server.status == 'exec_success') {
					if (state.error !== undefined) {
						if (moment().diff(Server.statusTime, 'minutes', true) < 5){
							return 'blue';
						} else {
							return 'red';
						}
					} else {
						return 'green';
					}
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

					self.updateServerInfo();
				}

				return true;

			}.bind(this);

			this.setSelected = function(type, server, event){
				var self = this;

				if (type == 'eac'){
					self.selectedServer(self.eacServers().indexOf(server));
					self.selectedType('eac');
				} else if (type == 'game') {
					self.selectedServer(self.gameServers().indexOf(server));
					self.selectedType('game');
				} else if (type == 'voice') {
					self.selectedServer(self.voiceServers().indexOf(server));
					self.selectedType('voice');
				}

				self.updateServerInfo();

			}.bind(this);

			this.setShowType = function(type){
				this.showType(type);
				self.showRconConsole(false);
				$('#rconConsole').empty();
			}.bind(this);

			this.showSlots = function(serverSlots, players, data) {

				serverSlots = Number(serverSlots);

				if (serverSlots > 0 && players === undefined) {
					return '<b>Слотов:</b> ' + serverSlots;
				} else if (serverSlots > 0 && players >= 0) {
					if (players < serverSlots){
						return '<b>Игроков:</b> <span class="green">' + players + '/' + serverSlots + '</span>';
					} else {
						return '<b>Игроков:</b> <span class="red">' + players + '/' + serverSlots + '</span>';
					}
				}

				return true;
			}

			this.showSlotsHltv = function(serverSlots, players, data) {

				serverSlots = Number(serverSlots);

				if (serverSlots > 0 && players === undefined) {
					return '<b>Слотов:</b> ' + serverSlots;
				} else if (serverSlots > 0 && players >= 0) {
					if (players < serverSlots){
						return '<b>Зрителей:</b> <span class="green">' + players + '/' + serverSlots + '</span>';
					} else {
						return '<b>Зрителей:</b> <span class="red">' + players + '/' + serverSlots + '</span>';
					}
				}

				return true;
			}

			this.passwordState = function(){
				var self = this;

				if ($(self.renderedServer()).size() > 0){
					if (self.renderedServer().Status.passwordProtected !== undefined
							&& self.renderedServer().Server.privateType !== undefined)
					{

						var passwordProtected = self.renderedServer().Status.passwordProtected;
						var privateType = self.renderedServer().Server.privateType;

						if (privateType > 0 && passwordProtected === false){
							return -1;
						} else if (passwordProtected) {
							return 1;
						} else {
							return 0;
						}
					}
				}

			}.bind(this);

			this.privateType = function(){
				var self = this;

				if ($(self.renderedServer()).size() > 0){
					if (self.renderedServer().Server.privateType !== undefined){
						var privateType = Number(self.renderedServer().Server.privateType);

						if (privateType == 0){
							return 'Публичный';
						} else if (privateType == 1){
							return 'Приватный';
						} if (privateType == 2){
							return 'Приватный с автоотключением';
						}
					}
				}

			}.bind(this);

			this.renderSelected = ko.computed(function() {
				var self = this;
				var newData = [];

				if (self.selectedServer() !== false)
				{
					type = self.selectedType();

					if (type == 'eac'){
						newData = self.eacServers()[self.selectedServer()];
					} else if (type == 'game') {
						newData = self.gameServers()[self.selectedServer()];
					} else if (type == 'voice') {
						newData = self.voiceServers()[self.selectedServer()];
					}

					self.renderedServer(newData);
					return true;
				}

		        return false;
		    }, this);

		    this.barInit = function(){
		    	//$('#paymentBar').progress();
		    	return true;
		    }

			this.showModal = function(size, title, bodyUrl, data){
				var self = this;

				$('#indexModal').removeClass('small large fullscreen').addClass(size);
				$('#indexModal .header').html(title);

				self.loadingModal(true);

				$.get( bodyUrl + '/2')
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

			this.updateServerInfo = function(){
				var self = this;

				if ($(self.renderedServer()).size() > 0
						&& self.renderedServer().Server.initialised == 1)
				{
					var id = self.renderedServer().Server.id;

					self.loadingModal(true);

					$.post( "/servers/viewServer/" + id + "/json")
			    	 .done(
				    	 	function(data){
								answer = JSON.parse(data);
								if (answer.error === undefined){
									self.errors.push('Неизвестная ошибка');
								}
								else
								if (answer.error != 'ok'){
									if (answer.error == 'needAuth'){
										window.location.href = "/users/login";
									} else {
										self.errors.push(answer.error);
									}
								}
								else
								if (answer.error == 'ok')
								{
									if (answer.Server.id == self.renderedServer().Server.id){
										self.renderedServer(answer);
									}

									/*
									$('.playerInfo').popup(
										{distanceAway: 0,
										 offset: -50,
									     position: 'right center',
									     delay: {show: 300, hide: 10},
									     hoverable: true});*/


								}

								self.loadingModal(false);
							})
			    	 .fail( function(data, status, statusText) {
			    	 	answer = "HTTP Error: " + statusText;
			    	 	self.errors.push(answer);
			    	 	self.loadingModal(false);
			    	 })
			    	 .always(function() {
					    $('#playersStat24h').tab();
			    	 	$('#playersStat7d').tab();
					});
		    	}

			}.bind(this);

			this.serverAction = function(action, event, data){
				var self = this;
				//console.log($(event.currentTarget).addClass('disabled'));

				if ($(self.renderedServer()).size() > 0
						&& self.renderedServer().Server.initialised == 1
						&& action !== undefined)
				{
					var id = self.renderedServer().Server.id;
					self.executing(true);

					$.post( '/servers/script/' + id + '/' + action +'/0/json' )
			    	 .done(
				    	 	function(data, status, statusText){

				    	 		answer = JSON.parse(data);
								if (answer.error === undefined){
									self.errors.push('Неизвестная ошибка');
								}
								else
								if (answer.error.length > 0){
									self.errors.push(answer.error);
								}
								else
								if (answer.error.length == 0){
									self.actionLog.push({'log' : answer.result,
								                         'time': moment().calendar()});

									self.updateServerInfo();
									setTimeout(function(){self.updateServerInfo()}, 10000);
								}

								if (answer.info.length > 0){
									self.infos.push(answer.info);
								}

								self.executing(false);
							})
			    	 .fail( function(data, status, statusText) {
			    	 	answer = "HTTP Error: " + statusText;
			    	 	self.errors.push(answer);
			    	 	self.executing(false);
			    	 });
				}

			}.bind(this);

			this.actionLogEnable = function(){
				if (this.actionLogShow()){
					this.actionLogShow(false);
				} else {
					this.actionLogShow(true);
				}
			}.bind(this);

			this.showRcon = function(hltv, data){
				var self = this;

				if ($(self.renderedServer()).size() > 0
						&& self.renderedServer().Server.initialised == 1
						&& self.renderedServer().Server.status == 'exec_success')
				{
					if ((self.showRconConsole() == 'server' && hltv === false)
							|| (self.showRconConsole() == 'hltv' && hltv === true)){
						self.showRconConsole(false);
						$('#rconConsole').empty();
					} else {
					var id = self.renderedServer().Server.id;
					self.loadingModal(true);

					if (hltv){
						var url = '/servers/rcon/' + id + '/true';
					}
					else
					{
						var url = '/servers/rcon/' + id;
					}

					$.get( url )
			    	 .done(
				    	 	function(data, status, statusText){

				    	 		$('#rconConsole').html(data);
				    	 		if (hltv){
									self.showRconConsole('hltv');
								}
								else
								{
									self.showRconConsole('server');
								}

								self.loadingModal(false);
							})
			    	 .fail( function(data, status, statusText) {
			    	 	answer = "HTTP Error: " + statusText;
			    	 	self.errors.push(answer);
			    	 	self.loadingModal(false);
			    	 });
			    	}
		    	}

			}.bind(this);

			setInterval(function() {self.updateServerInfo();},90000);

		};

	ko.applyBindings(new userServersViewModel(), document.getElementById("usersServersIndex"));

</script>



