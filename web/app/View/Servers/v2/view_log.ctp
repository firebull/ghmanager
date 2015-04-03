<div id="server_log_view">
<div class="ui active inverted dimmer" data-bind="visible: loading" id="logsLoading">
	<div class="ui text loader">Загрузка</div>
</div>
<?php

$class = [ 'run'     => 'item',
		   'startup' => 'item',
		   'update'  => 'item',
		   'debug'   => 'item' ];

$class[strtolower($type)]='red active item';

function cutString($string, $length = 30) {
	//Обрезка длинной строки до вида xxxxxx...xxxxx
	$string = rtrim($string, '.log');

	if (strlen($string) > $length)
	{
	 	$newString = substr($string, 0, ($length/2 - 3))."...";
	 	$newString .= substr($string, strlen($string) - ($length/2), ($length/2));
	 	return $newString;
	}
	else
	{
		return $string;
	}
}

?>
<div class="ui pointing menu">
<?php
	if (!in_array($gameType, ['hlds', 'ueds']))
	{
		echo $this->Html->link('Логи запуска сервера', '#',
							  array ('id'=>'server_startlog_'.$id,
								  	 'escape' => false,
								  	 'class' => $class['startup'])
							  );

		$event  = $this->Js->request(array('controller'=>'servers',
									 'action'   => 'viewLog', $id, "startup", "ver:2"),
							   array('update'   => '#server_log_view',
									 'before'   => "$('#logsLoading').show();",
								     'complete' => "$('#logsLoading').hide();"
									 ));

		$this->Js->get('#server_startlog_'.$id)
				 ->event('click', $event);
	}

	echo $this->Html->link('Логи работы сервера', '#',
					  array ('id' => 'server_runlog_'.$id,
					  	     'escape' => false,
					  	     'class'  => $class['run'])
					  );

	$event  = $this->Js->request(array('controller'=>'servers',
								 'action'   => 'viewLog', $id, 'run', "ver:2"),
						   array('update'   => '#server_log_view',
								 'before'   => "$('#logsLoading').show();",
								 'complete' => "$('#logsLoading').hide();"
								 ));

	$this->Js->get('#server_runlog_'.$id)
			 ->event('click', $event);

	echo $this->Html->link('Логи обновления сервера', '#',
					  array ('id' => 'server_updatelog_'.$id,
					  	     'escape' => false,
					  	     'class'  => $class['update'])
					  );

	$event  = $this->Js->request(array('controller' => 'servers',
								 'action'   => 'viewLog', $id, "update", "ver:2"),
						   array('update'   => '#server_log_view',
								 'before'   => "$('#logsLoading').show();",
								 'complete' => "$('#logsLoading').hide();"

								 ));

	$this->Js->get('#server_updatelog_'.$id)
			 ->event('click', $event);

	echo $this->Html->link('Логи режима отладки', '#',
					  array ('id' => 'server_debuglog_'.$id,
					  	     'escape' => false,
					  	     'class'  => $class['debug'])
					  );

	$event  = $this->Js->request(array('controller'=>'servers',
								 'action'   => 'viewLog', $id, "debug", "ver:2"),
						   array('update'   => '#server_log_view',
								 'before'   => "$('#logsLoading').show();",
								 'complete' => "$('#logsLoading').hide();",

								 ));

	$this->Js->get('#server_debuglog_'.$id)->event('click', $event);
?>
</div>
<small>Последние <?php echo count($logList); ?> файлов логов:</small>
<div class="ui padded grid">
	<div class="ui row">
		<div class="four wide column" style="width: 24,9% !important;">
			<div class="ui fluid vertical pointing menu">
				<a class="item" data-bind="click: showHelpAction, css: {'red active' : showHelp}">Помощь</a>
			<?php
				foreach ( $logList as $key => $log )
				{
					if ($log != '')
					{
						echo $this->Html->link(cutString(trim($log)), '#',
		       						['data-bind' =>
		       							"event: {click: printLog.bind(true, ".$key.",'".$log."')},".
		       							"css: {'red active' : logShowNumber() == ".$key."}",
									 'escape' => false,
									 'class' => 'item'
									]

		       						);
					}
				}
			?>
			</div>
		</div>
		<div class="twelve wide column">
			<div id="logHelp" data-bind="visible: showHelp">
				Щелкните по логу, который хотели бы просмотреть.
				Вы также можете скачать логи по FTP, которые находятся
				в директории "logs/имя&nbsp;сервера".
				<br><br>

				<div class="ui icon warning message">
				    <i class="warning sign icon"></i>
				    <div class="content">
				    	<p>Не забывайте, что нельзя менять изначальные настройки логов сервера, если хотите иметь возможность просматривать их из панели управления.</p>
				    </div>
				</div>
			</div>
			<b id="logTitle" data-bind="visible: showLog"></b>
			<div id="logView" data-bind="visible: showLog"></div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$('#indexModal').modal();

	var logsViewModel = function(){
		this.serverId       = ko.observable(<?php echo $id;?>);
		this.serverType     = ko.observable('<?php echo $gameType;?>');
		this.logsType       = ko.observable('<?php echo $type;?>');
		this.showHelp       = ko.observable(true);
		this.showLog		= ko.observable(false);
		this.logShowNumber  = ko.observable();

		this.loading = ko.observable(false);
		this.errors = ko.observableArray();

		this.printLog = function(id, log){
			var self = this;
			if (self.serverType() == 'hlds'){
				var link = '/servers/printLogHlds/';
			} else if (self.serverType() == 'hlds'){
				var link = '/servers/printLogCod/';
			} else if (self.serverType() == 'ueds'){
				var link = '/servers/printLogUeds/';
			} else {
				var link = '/servers/printLogSrcds/';
			}

			self.loading(true);

			$.get( link
						+ self.serverId() + '/'
						+ log + '/'
						+ self.logsType())
	    	 .done(
		    	 	function(data){

		    	 		$('#logTitle').text(log);
		    	 		$('#logView').html(data);

		    	 		self.logShowNumber(id);
		    	 		self.showHelp(false);
		    	 		self.showLog(true);
						self.loading(false);
					})
	    	 .fail( function(data, status, statusText) {
	    	 	answer = "HTTP Error: " + statusText;
	    	 	self.errors.push(answer);
	    	 	self.loading(false);
	    	 });

		}.bind(this);

		this.showHelpAction = function(){
			this.showHelp(true);
		    this.showLog(false);
		    this.logShowNumber('hide');
		    $('#logView').empty();
		}.bind(this);
	};

	ko.cleanNode(document.getElementById("server_log_view"));
	ko.applyBindings(new logsViewModel(), document.getElementById("server_log_view"));
</script>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
</div>
