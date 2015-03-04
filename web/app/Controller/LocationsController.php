<?php
/*

Locations controller.
Main logic to work with locations. This allows to control servers in different DataCenters.
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

class LocationsController extends AppController {

	public $name = 'Locations';

	public $layout = 'client';

	public $helpers = array('Time', 'Html', 'Js');
	public $components = array('RequestHandler', 'Session', 'DarkAuth');
	public $_DarkAuth;

	public function beforeRender() {
		$userInfo = $this->DarkAuth->getAllUserInfo();

		// Убрать все теги, xss-уязвимость
		if (!empty($userInfo)) {
			foreach ( $userInfo['User'] as $key => $value ) {
	   				$userInfo['User'][$key] = strip_tags($value);
			}

			$this->set('userinfo', $userInfo);

			$this->loadModel('Support');
			$openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
			$this->set('openTickets', $openTickets[0][0]['COUNT(*)']);

		}
	}

	public function control() {
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->set('locations', $this->Location->find('all'));
	}

	public function add() {
		$this->DarkAuth->requiresAuth(array('Admin'));
		if (!empty($this->data)) {
			if ($this->Location->save($this->data)) {
				$this->Session->setFlash('Локация добавлена. Можно привязывать к ней серверы.', 'flash_success');
				$this->redirect(array('controller' => 'Locations', 'action' => 'control'));
			} else {
				$this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
			}
		}
	}

	public function edit($id = null) {
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->Location->id = $id;
		if (empty($this->data)) {
			$this->data = $this->Location->read();
		} else {
			if ($this->Location->save($this->data)) {
				$this->Session->setFlash('Информация о локации обновлена.', 'flash_success');
				$this->redirect(array('controller' => 'Locations', 'action' => 'control'));
			}
		}
	}

	public function delete($id) {
		$this->DarkAuth->requiresAuth(array('Admin'));
		$this->Location->delete($id);
		$this->Session->setFlash('Локация #'.$id.' удалена.', 'flash_success');
		$this->redirect(array('controller' => 'Locations', 'action'=>'control'));
	}

	public function linkRootserverToLocation($id = null) {
		$this->DarkAuth->requiresAuth(array('Admin'));

		//Save the association
		//pr($this->data);
		if ($this->data) {
		    if (@$this->Location->save($this->data)) {
		        $this->Session->setFlash('Услуги к шаблону добавлены..', 'flash_success');
		        $this->redirect(array('action' => 'control'));
		    }
		} else {
				$this->Location->id = $id;
				$this->data = $this->Location->read();
				//********************************************************
				//берем полный список серверов
				$servers = $this->Location->RootServer->find('all');
				foreach ( $servers as $server ) {
       				$rootServersList[$server['RootServer']['id']] = $server['RootServer']['name'];
				}
				$this->set('rootServersList', $rootServersList);

				// Конец выбора серверов
				//********************************************************

		}
	}

}
?>
