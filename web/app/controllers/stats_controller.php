<?php
/*

Stats and Journal controller.
Log actions journal, load stat, attacks and so on.
Copyright (C) 2013 Nikita Bulaev

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

*/

class StatsController extends AppController {

	var $name = 'Stats';

	public $layout = 'client';

	public $helpers = array (
		'Time',
		//'Ajax',
		'javascript',
		'Html',
		'Text',
		'Js' => array('Jquery'),
		'Common'
	);

	public $components = array (
		'RequestHandler',
		'Session',
		'TeamServer'
	);

	function beforeRender() {
		$userInfo = $this->DarkAuth->getAllUserInfo();

		// Убрать все теги, xss-уязвимость
		foreach ( $userInfo['User'] as $key => $value ) {
   				$userInfo['User'][$key] = strip_tags($value);
		}

		$this->set('userinfo', $userInfo);
	}

	function saveStatParam($d){
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));
		if (!empty($d['Stat'])){
			$this->Stat->create();
			if (!$this->Stat->save($d)){
				$this->Session->setFlash('Возникла ошибка при сохранении статистики: '.mysql_error(), 'flash_error');
				return false;
			}
			else
			{
				return true;
			}
		}
	}

	// Статистика по заказам. Умолчание - 6 месяцев
	function orderStats( $period = 6){
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

		$this->layout = 'ajax';

		$this->loadModel('OrderClean');
		$curMonth = date('Y-m-d', strtotime(date('Y-m', time())));


		$fromDate = date('Y-m-d', mktime( 0, 0, 0,
										  date('m') - $period,
										  '01',
										  date('Y')
										 )
						);

		$statsPure = $this->Stat->find('all',
										array('conditions' => array('paramName' => array('sumPayedByMonth',
																						 'sumGotByMonth'),
																	'month >=' => $fromDate,
																	),
											  'order' => array('paramName DESC', 'month DESC' )
											  ));



		if (!empty($statsPure)){
			foreach ($statsPure as $statParam) {
				//pr($statParam);
				if($statParam['Stat']['paramName'] == 'sumPayedByMonth'){
					$statsMonths['payed'][$statParam['Stat']['month']] = $statParam['Stat']['paramValue'];

					if ($statParam['Stat']['month'] == $curMonth)
					{
						$payedId = $statParam['Stat']['id'];
					}
				}
				else
				{
					$statsMonths['got'][$statParam['Stat']['month']] = $statParam['Stat']['paramValue'];

					if ($statParam['Stat']['month'] == $curMonth)
					{
						$gotId = $statParam['Stat']['id'];
					}
				}
			}
		}
		else
		{
			$statsMonths = array();
		}


		ksort($statsMonths['payed']);
		ksort($statsMonths['got']);

		// Проверить наличие данных.
		// Если их нет - внести.

		for ($i=0; $i <= $period; $i++) {
			$m = date('Y-m-d', mktime(  0,
										0,
										0,
										date('m') - $i,
										'01',
										date('Y')
									  )
						);
			$d = date('Y-m-d H:i:s',    mktime( 0,
										  0,
										  0,
										  date('m') - $i,
										  '01',
										  date('Y'))
							);
			$d2 = date('Y-m-d H:i:s',    mktime( 0,
										  0,
										  0,
										  date('m') - ($i - 1),
										  '01',
										  date('Y'))
							);

			if (empty($statsMonths['payed'][$m])
					or
				empty($statsMonths['got'][$m])
					or
				$m == $curMonth
				)
			{
				$ordersSum = $this->OrderClean->query("SELECT SUM(`sumPayed`), SUM(`sumGot`) FROM `orders` WHERE `payedDate` > '".$d."' AND `payedDate` < '".$d2."'");

				$curStatPayed['Stat']['paramName']  = 'sumPayedByMonth';
				$curStatPayed['Stat']['paramValue'] = round($ordersSum[0][0]['SUM(`sumPayed`)']);
				$curStatPayed['Stat']['month'] = $m;

				$curStatGot['Stat']['paramName']  = 'sumGotByMonth';
				$curStatGot['Stat']['paramValue'] = round($ordersSum[0][0]['SUM(`sumGot`)']);
				$curStatGot['Stat']['month'] = $m;

				// Текущий месяц нужно пересчитывать
				if ($m == $curMonth and !empty($statsMonths['payed'][$m]) and !empty($statsMonths['got'][$m])){

					$this->Stat->id = $payedId;
					$this->Stat->save($curStatPayed);

					$this->Stat->id = $gotId;
					$this->Stat->save($curStatGot);

				}
				// Иначе создать новые записи
				else
				{
					$this->saveStatParam($curStatPayed);
					$this->saveStatParam($curStatGot);
				}
			}
		}

		$this->set('stat', $statsMonths);

	}

	// Общий журнал действий
	function control(){
		$this->DarkAuth->requiresAuth(array('Admin', 'GameAdmin'));

		$redis = $this->TeamServer->redisConnect(10);

		$lastLogId = $redis->get('attackerActionId');

		$redis->multi();
		for ($i = $lastLogId; $i > ($lastLogId - 50); $i--) {
			$redis->lRange('log:'.$i, -5, -1);
		}

		$this->set('iptablesLog', $redis->exec());

		// Журнал действий пользователей

		$this->loadModel('Action');

		$this->Action->bindModel(array(
										'belongsTo' => array(
															'User' => array(
																'className' => 'User',
																'foreignKey' => 'user_id',
																'conditions' => '',
																'fields' => 'id,username',
																'order' => ''
															)
												)));

		$journal = $this->Action->find('all', array('order' => array(
															            'Action.id' => 'desc'
															        ),
										 			'limit' => '50'));

		$this->set('journal', $journal);

	}

	function getAttackerIpInfo($ip = null){
		$this->DarkAuth->requiresAuth();
		$this->layout = 'ajax';

		if (!is_null($ip) and preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip) > 0){

			$this->set('geo', @geoip_record_by_name($ip));

			$redis = $this->TeamServer->redisConnect(10);

			$logIds = $redis->lRange('src:'.$ip, -25, -1);

			$redis->multi();

			foreach ($logIds as $logId) {
				$redis->lRange('log:'.$logId, -5, -1);
			}

			$this->set('iptablesLog', $redis->exec());

			$this->set('ip', $ip);

		}
	}

	function getDestinationIpInfo($ip = null, $port = null){
		$this->DarkAuth->requiresAuth();
		$this->layout = 'ajax';

		if (!is_null($ip)
				and
			preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip) > 0
				and
			intval($port) > 0
			){

			$redis = $this->TeamServer->redisConnect(10);

			$logIds = $redis->lRange('dst:'.$ip.':'.$port, -25, -1);

			$redis->multi();

			foreach ($logIds as $logId) {
				$redis->lRange('log:'.$logId, -5, -1);
			}

			$this->set('iptablesLog', $redis->exec());

			$this->set('ip', $ip.':'.$port);

		}
	}

	function rootServerStat($id = null, $period = 72){
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->layout = 'ajax';

		if (!is_null($id))
		{
			/*
				TODO: Сделать выбор серверов для суммирования
			*/

			$this->loadModel('RootServer');
			$this->RootServer->unbindModel(array(
										'hasAndBelongsToMany' => array(
															'RootServerIp'
												)));

			if ($id == 'sum')
			{
				$this->set('server', array('id' => 'sum', 'name' => 'Sum'));
				$this->set('graphs', array('players', 'servers'));
			}
			else
			{
				$this->RootServer->id = intval($id);
				$rootServer = $this->RootServer->read();

				if (!empty($rootServer))
				{
					$this->set('server', array('id' => $id, 'name' => $rootServer['RootServer']['name']));
					$this->set('graphs', array('players', 'servers', 'errors', 'slots', 'voice'));
				}
				else
				{
					$this->Session->setFlash('Нет такого сервера.', 'flash_error');
				}
			}

		}
		else
		{
			$this->Session->setFlash('Не указан сервер', 'flash_error');
		}


	}

	function rootServerStatJson($id = null, $type = null, $points = null, $interval = null){
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->layout = 'ajax';

		$redis = $this->TeamServer->redisConnect();

		if (is_null($points))
		{
			$points = 288; // Сутки
		}

		if ($interval != null && intval($interval) >= 10)
		{
			$step = intval($interval/5); // Через какие промежутки выдать данные графика
		}
		else
		{
			$step = 1;
		}

		if (intval($id) > 0) // Если запрос на конкретный сервер
		{

			Cache::set(array('duration' => '+30 seconds'));

			if (($timeStamps = Cache::read('timeStamps_'.$id)) === false) {

				$timeStamps = $redis->zRange('stat:'.$id.':time', -intval($points), -1);

				Cache::set(array('duration' => '+30 seconds'));
				Cache::write('timeStamps_'.$id, $timeStamps);
			}

			$stat = array();


			if (!empty($timeStamps) && !is_null($type))
			{
				$graphData = $redis->lRange('stat:'.$id.':'.$type, -intval($points), -1);

				if (!empty($graphData))
				{
					$i = 0;
					$j = 0;
					foreach ($timeStamps as $timeStamp) {
						$i++;

						if ($i == $step)
						{
							$stat[$timeStamp*1000] = intval($graphData[$j]);
							$i = 0;
						}

						$j++;
					}
				}
			}
		}
		else
		{
			$this->loadModel('RootServer');
			$this->RootServer->unbindModel(array(
										'hasAndBelongsToMany' => array(
															'RootServerIp'
												)));

			if ($id == 'sum') // Сумма показателей
			{
				$rootServers = $this->RootServer->find('all', array('conditions' => array('id >=' => 27)));

				$graphData = array();

				foreach ($rootServers as $rootServer) {
					$graphData['timestamps'][$rootServer['RootServer']['id']] = $timeStamps = $redis->zRange('stat:'.$rootServer['RootServer']['id'].':time', -intval($points), -1);
					$graphData[$type][$rootServer['RootServer']['id']] = $redis->lRange('stat:'.$rootServer['RootServer']['id'].':'.$type, -intval($points), -1);
				}

				// Собрать массив из уникальных временных точек
				$commonTimestamps = array();

				foreach ($graphData['timestamps'] as $time) {
					if (empty($commonTimestamps))
					{
						$commonTimestamps = $time;
						break;
					}

					$commonTimestamps = array_intersect($commonTimestamps, $time);
				}

				$commonTimestamps = array_unique($commonTimestamps); // Уникальные временные точки

				$i = 0;
				$stat = array();
				$midSum = 0;
				$curSum = 0;
				// Перебор всех временных точек и склеивание суммы
				foreach ($commonTimestamps as $timeStamp) {
					$i++;

					if ($i == $step)
					{

						if (empty($stat[$timeStamp*1000]))
						{
							$stat[$timeStamp*1000] = 0;
						}

						// Ищем индекс, по которому делать выборку графиков
						foreach ($graphData['timestamps'] as $rid => $time) {

							//var_dump($i);

							if ($key = array_search($timeStamp, $time))
							{
								$curSum += @$graphData[$type][$rid][$key];
							}
						}
						$i = 0;

						$stat[$timeStamp*1000] = intval(($curSum + $midSum)/$step);
						$curSum = 0;
						$midSum = 0;

					}
					else
					{
						foreach ($graphData['timestamps'] as $rid => $time) {
							$midSum += intval(@$graphData[$type][$rid][$key]);
						}
					}



				}

			}
		}

		$this->set('result', $stat);
		$this->header("Content-type: text/javascript");
		$this->render('result_json');

	}
}
