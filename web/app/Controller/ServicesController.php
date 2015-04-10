<?php
/*

Services controller.
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

class ServicesController extends AppController {

	public $name = 'Services';
	public $layout = 'client';

	public $helpers = array('Time', 'Html', 'Js', 'Cache');
	public $components = array('RequestHandler', 'Session', 'DarkAuth');
	//public $_DarkAuth;

	public $cacheAction = array(
		'getServices/' => '15 minutes'
	);

	public function beforeRender() {
		$userInfo = $this->DarkAuth->getAllUserInfo();

		// Убрать все теги, xss-уязвимость
		foreach ( $userInfo['User'] as $key => $value ) {
   				$userInfo['User'][$key] = strip_tags($value);
		}

		$this->set('userinfo', $userInfo);
	}

	public function control() {
		$this->DarkAuth->requiresAuth(array('Admin','GameAdmin'));
		$this->cacheAction = true;
		$this->Service->recursive = 0;
		$this->set('services', $this->paginate());
	}

	public function add() {
		$this->DarkAuth->requiresAuth(array('Admin'));
		if (!empty($this->data)) {
			$this->Service->create();
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Услуга создана.', 'flash_success');
				$this->redirect(array('action' => 'control'));
			} else {
				$this->Session->setFlash('Возникла ошибка при сохранении данных:'.mysql_error(), 'flash_error');
			}
		}

	}

	public function edit($id = null) {
		$this->DarkAuth->requiresAuth(array('Admin'));
		if (!$this->Service->exists($id) && empty($this->data)) {
			$this->Session->setFlash('Нет такой услуги', 'flash_error');
			$this->redirect(array('action' => 'control'));
		}

		if (!empty($this->data)) {
			if ($this->Service->save($this->data)) {
				$this->Session->setFlash('Новые данные сохранены.', 'flash_success');
				$this->redirect(array('action' => 'control'));
			} else {
				$this->Session->setFlash('Возникла ошибка при сохранении данных:'.mysql_error(), 'flash_error');
			}
		}

		if (empty($this->data)) {

			$this->data = $this->Service->read(null,$id);
		}
	}

	public function delete($id = null) {
		$this->DarkAuth->requiresAuth(array('Admin'));
		if (!$id) {
			$this->Session->setFlash('Нет такой услуги', 'flash_error');
			$this->redirect(array('action'=>'control'));
		}
		if ($this->Service->delete($id)) {
			$this->Session->setFlash('Услуга удалена.', 'flash_success');
			$this->redirect(array('action'=>'control'));
		}
		$this->Session->setFlash('Возникла ошибка при удалении услуги:'.mysql_error(), 'flash_error');
		$this->redirect(array('action' => 'control'));
	}

	// Формирование списка доступных услуг для скрипта
	public function getServices($templateId = null, $rootServer = 'all', $locationId = '2') {
		$this->layout = 'ajax';
		if (!empty($this->params['url']['templateId'])) {
			$templateId = $this->params['url']['templateId'];
		}
		$this->loadModel('GameTemplate');
		$this->GameTemplate->contain(['Service']);

		$this->GameTemplate->id = $templateId;
		$template = $this->GameTemplate->read();
		$servicesList = array();

		if (!empty($template['Service'])) {
			if ($rootServer === 'all') {
				$this->loadModel('Location');
				$this->Location->id = $locationId;
				$location = $this->Location->read();

				if (!empty($location)) {
					// Теперь последовательно перебрать все услуги
					foreach ( $template['Service'] as $service ) {
						// А теперь перебрать серверы. И если услуга доступна
						// серверу, считать, что она доступна всей локации
						foreach ( $location['RootServer'] as $rootServer ) {
       						if ($this->checkService($service['id'], $templateId, $rootServer['id'])) {
       							$servicesList[$service['id']]['id']   = $service['id'];
       							$servicesList[$service['id']]['name'] = $service['name'];
       							$servicesList[$service['id']]['longname'] = $service['longname'];
       							$servicesList[$service['id']]['desc']     = $service['description'];
       							$servicesList[$service['id']]['price']     = $service['price'];
       							break;
       						}
						}

					}
				} else {
					// В локации нет серверов
				}
			} else {
				// Тут можно прописать проверку для конкретного сервера
				foreach ( $template['Service'] as $service ) {
					if ($this->checkService($service['id'], $templateId, $rootServer)) {
						$servicesList[$service['id']]['name'] = $service['name'];
						$servicesList[$service['id']]['longname'] = $service['longname'];
						$servicesList[$service['id']]['desc']     = $service['description'];
						$servicesList[$service['id']]['price']     = $service['price'];
					}
				}
			}
			$this->set('result', array_values($servicesList));
			$this->set('_serialize', ['result']);
			return @$servicesList;
		} else {
			// Услуги шаблону не доступны или не привязаны
		}

	}

	// Функция проверки доступности услуги
	public function checkService( $serviceId = null, $templateId = null, $rootServerId = null, $locationId = 'all' ) {
		/* Если есть только $templateId - вернуть все доступные шаблону услуги
		 * Если есть еще и $rootServerId - вернуть все доступные услуги шаблону,
		 * 								   относительно физического сервера
		 * @location - Локация, переменная введена под развитие.
		 * 	Через неё будет задаваться локация, из которой будем
		 *  выбирать список физических серверов.
		 *  На данный момент значение локации 'all'
		 *
		 * Для некоторых услуг требуется проверка доступности еще
		 * и в локации, и на конкретном сервере. Но эти услуги надо
		 * обрабатывать отдельно каждую, потому по умолчанию просто
		 * проверить привязку услуги к шаблону, а далее ставить
		 * костыли на требуемые услуги.
		*/
		$this->DarkAuth->requiresAuth();
		if ($templateId) {
			$this->loadModel('GameTemplateClean');

			if (@$serviceId) { // Вернуть услугу

				$this->GameTemplateClean->bindModel(array(
					'hasAndBelongsToMany' => array(
						'Service' => array('conditions'=>array('Service.id'=>$serviceId))
				)));
				$this->GameTemplateClean->id = $templateId;
				$template = $this->GameTemplateClean->read();
				if (!empty($template['Service'])) {
					// Теперь проверить костыли - услуги, которые привязаны к серверам
					if ($template['Service'][0]['id'] == 1) {
						// Проверка услуги "Красивый порт"
						if (@$rootServerId and $this->checkServiceNoPort($rootServerId, $templateId)) {
							return true;
						} else {
							return false;
						}
					} elseif ($template['Service'][0]['id'] == 2) {
						// Проверка услуги "Выделенный IP"
						if (@$rootServerId and $this->checkServiceDedicatedIp($rootServerId, $templateId)) {
							return true;
						} else {
							return false;
						}
					} else {
						return true;
					}

				} else {
					// Услуга шаблону недоступна
					return false;
				}

			}

		} else {
			$this->Session->setFlash('Не указан шаблон.', 'flash_error');
			return false;
		}

	}

	// Функция для проверки доступности услуги
	// Красивый порт для запрашиваемого физ. сервера
	public function checkServiceNoPort($rootServerId = null, $templateId = null) {
		$this->DarkAuth->requiresAuth();
		$this->loadModel('RootServer');
		$this->RootServer->bindModel(array(
					'hasAndBelongsToMany' => array(
						'RootServerIp' => array('conditions'=>array('type'=>'public'))
				)));
		$this->RootServer->id = $rootServerId;
		$rootServer = $this->RootServer->read();

		if (!empty($rootServer['RootServerIp'])) {
			foreach ( $rootServer['RootServerIp'] as $ip ) {
       			// Список IP
       			$ipList[] = $ip['ip'];
			}

			// Теперь запросить серверы с полученными адресами и портом по-умолчанию

			// Сначала узнать порт по-умолчанию для шаблона
			$this->loadModel('GameTemplateClean');
			$this->GameTemplateClean->bindModel(array(
					'hasAndBelongsToMany' => array(
						'Protocol' => array('fields'=>'id, port')
				)));
			$this->GameTemplateClean->id = $templateId;
			$template = $this->GameTemplateClean->read();

			if (!empty($template['Protocol'])) {
				$port = $template['Protocol'][0]['port'];
				/*
				 * Теперь получить список IP, у которых этот порт уже есть
				 */
				 $this->loadModel('ServerClean');
				 $servers = $this->ServerClean->find('all', array('fields'=>'address',
				 												  'conditions' => array(
																						'ServerClean.port' => $port,
																						'ServerClean.address' => $ipList
																						)
				 												  )
				 									);
				$buzyIps = array();
				if (!empty($servers)) {
					foreach ( $servers as $ip ) {
       					$buzyIps[] = $ip['ServerClean']['address'];
					}
				}
				// Теперь надо вычислить, есть ли свободные IP
				$freeIps = array_diff($ipList, $buzyIps);

				if (!empty($freeIps)) {
					return $freeIps;
				}
				else // И всё-таки нет свободных IP :((
				{
					return false;
				}

			}
			else // Услуге вообще не требуется порт. Хотя вряд ли =)
			{
				return false;
			}

		}
		else // Публичных адресов нет вовсе у данного сервера. Нонсенс!
		{
			return false;
		}

	}

	// Функция для проверки доступности услуги
	// Красивый порт для запрашиваемого физ. сервера
	public function checkServiceDedicatedIp($rootServerId = null, $templateId = null) {
		$this->DarkAuth->requiresAuth();
		$this->loadModel('RootServer');
		$this->RootServer->bindModel(array(
					'hasAndBelongsToMany' => array(
						'RootServerIp' => array('conditions'=>array('type'=>'private'))
				)));
		$this->RootServer->id = $rootServerId;
		$rootServer = $this->RootServer->read();

		if (!empty($rootServer['RootServerIp'])) {
			foreach ( $rootServer['RootServerIp'] as $ip ) {
       			// Список IP
       			$ipList[] = $ip['ip'];
			}

			// Теперь запросить серверы с полученными адресами
			/*
			 * Теперь получить список уже купленных IP
			 */
			 $this->loadModel('ServerClean');
			 $servers = $this->ServerClean->find('all', array('fields'=>'address',
			 												  'conditions' => array(
																					'ServerClean.address' => $ipList
																					)
			 												  )
			 									);
			$buzyIps = array();
			if (!empty($servers)) {
				foreach ( $servers as $ip ) {
   					$buzyIps[] = $ip['ServerClean']['address'];
				}
			}
			// Теперь надо вычислить, есть ли свободные IP
			$freeIps = array_diff($ipList, $buzyIps);

			if (!empty($freeIps)) {
				return $freeIps;
			}
			else // И всё-таки нет свободных IP :((
			{
				return false;
			}

		}
		else // Приватных адресов нет вовсе у данного сервера.
		{
			return false;
		}

	}

}
?>
