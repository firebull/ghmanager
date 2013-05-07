<?php
/*

Root server IPs controller.
Manage IPs of Root servers at DataCenters.
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

class RootServerIpsController extends AppController {

	public $name = 'RootServerIps';

	public $layout = 'client';

	public $_DarkAuth;

	public $helpers = array (
		'Time',
		'javascript',
		'Html',
		'Js' => array('Jquery')
	);
	public $components = array (
		'RequestHandler',
		'Session'
	);

	function beforeRender() {
		$userInfo = $this->DarkAuth->getAllUserInfo();

		// Убрать все теги, xss-уязвимость
		foreach ( $userInfo['User'] as $key => $value ) {
				$userInfo['User'][$key] = strip_tags($value);
		}

		$this->set('userinfo', $userInfo);
		$this->loadModel('Support');
		$openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
		$this->set('openTickets', $openTickets[0][0]['COUNT(*)']);
	}


	function add() {
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->loadModel('RootServer');
		$this->loadModel('Server');
//		Получаем список всех физических сервров,
//		чтобы сразу привязывать IP к какому-либо из-них
		$this->set('rootServersList', $this->RootServer->find('list'));
		if (!empty($this->data)) {

			// Можно задавать диапазон через '-'
			if (strpos($this->data['RootServerIp']['ip'], '-') !== false ){
				$diapIp = explode('-', $this->data['RootServerIp']['ip']);
				$ip = ip2long($diapIp[0]);
				$ip2 = ip2long($diapIp[1]);
				$i = 0;
				while ($ip <= $ip2){
					$newIp[$i]['RootServerIp']['ip'] = long2ip($ip);
					$newIp[$i]['RootServerIp']['type'] = $this->data['RootServerIp']['type'];
					$newIp[$i++]['RootServer'] = $this->data['RootServer'];
					$ip++;
				}
				unset($this->data);
				$this->data = $newIp;
			}

			//Сначала сохраним IP
			if ($this->RootServerIp->saveAll($this->data)) {
					// Теперь сохраняем привязку IP к серверу
					$this->Session->setFlash('IP добавлен и привязан.', 'flash_success');
					$this->redirect(array('controller'=>'RootServerIps','action' => 'add'));

			} else {
				$this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
			}


		}

	}

	function autoComplete(){
		$this->layout = 'ajax';

		$this->DarkAuth->requiresAuth('Admin', 'GameAdmin', 'OrdersAdmin');

		if (isset($this->params['url']['term'])){
			$this->RootServerIp->unbindModel(array(
											'hasAndBelongsToMany' => array(
																'RootServer'
													)));
			$terms = $this->RootServerIp->find('all', array(
						'conditions' => array(
							'ip LIKE' => $this->params['url']['term'].'%'
						),
						'limit' => 15,
						'fields' => array('id','ip')
			));
			// Готовим список для корректного преобразования в JSON
			if ( !empty($terms) ) {
						$termsList = array();
						foreach ($terms as $term):

						$termsList[] = $term['RootServerIp']['ip'];

						endforeach;
						$this->set('list', $termsList);
			}
		}
	}

}
?>
