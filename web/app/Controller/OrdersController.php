<?php
/*

Orders controller.
Create orders and bills, transactions, confirmation of payment.
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

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'SoapWsse', array('file'=>'soap-server-wsse.php'));
App::uses('Xml', 'Utility');

class OrdersController extends AppController {

	public $name = 'Orders';

	public $layout = 'client';

	//public $_DarkAuth;

	public $helpers = array (
		'Time',
		'Html',
		'Text',
		'Js' => array('Jquery'),
		'Common'
	);
	public $components = array (
		'RequestHandler',
		'Session',
		'TeamServer',
		'DarkAuth'
	);

	public function beforeRender() {

		$this->loadModel('Support');

		$this->Order->User->unbindModel(array(
											'hasAndBelongsToMany' => array(
																'Server',
																'SupportTicket'
													)));

		$this->Order->User->id = $this->DarkAuth->getUserId();
		$userInfo = $this->Order->User->read();

		if (!empty($userInfo)) {
			// Убрать все теги, xss-уязвимость
			foreach ( $userInfo['User'] as $key => $value ) {
	   				$userInfo['User'][$key] = strip_tags($value);
			}

			$this->set('userinfo', $userInfo);
		}

		Cache::set(array('duration' => '+1 days'));

		if (($helpers = Cache::read('helpers')) === false) {

			$this->loadModel('Help');
			$helpers = $this->Help->find('list');

			Cache::set(array('duration' => '+1 days'));
			Cache::write('helpers', $helpers);
		}

		$openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
		$this->set('openTickets', $openTickets[0][0]['COUNT(*)']);
	}

	protected function checkRights($orderId = null) {

		if (@$orderId) {
			$this->DarkAuth->requiresAuth();
			$sessionUser = $this->DarkAuth->getAllUserInfo();
			$sessionUserId = $sessionUser['User']['id'];
			$sessionUserGroup = $sessionUser['Group'][0]['id'];

			$this->Order->id = $orderId;
			$order = $this->Order->read();
			//pr($order);
			//Сначала проверить наличие заказа вообще
			if (empty($order)) {
				$this->Session->setFlash('Такого заказа не существует', 'flash_error');
				return false;
			}
			//Проверить принадлежность заказа пользователю
			elseif ($order['User']['id'] == $sessionUserId // Да, владеет
						or
					 in_array($sessionUserGroup, array(1,6)) 						// Это администратор
					) {

					return true;

			} else {
				$this->Session->setFlash('Доступ к чужим заказам запрещен. О действии сообщено администратору.', 'flash_error');
				return false;
			}
		} else {
			$this->Session->setFlash('Не указан номер заказа.', 'flash_error');
			return false;
		}

	}

	// Расчёт остатка клиента
	protected function checkBill($userId = null) {

		if ($userId !== null) {

			$this->loadModel('Bill');
			$balance = $this->Bill->query("SELECT SUM(`sumPlus`), SUM(`sumMinus`) FROM bills WHERE `user_id` = $userId", false);

			$amount = $balance[0][0]['SUM(`sumPlus`)'] - $balance[0][0]['SUM(`sumMinus`)'];

			$this->Order->User->id = $userId;
			$amount = round($amount,2);
			$this->Order->User->saveField('money', $amount);

			return $amount;
		} else {
			return false;
		}
	}

	// Операции со счётом
	// @action: put - положить, get - снять
	protected function updateBill($userId = null,
								  $orderId = null,
								  $sum = null,      // Сумма, которую положить
								  $sumReal = null,  // Сумма за вычетов процентов платежной системы
								  $action = null,   // Действие - снять или положить (get/put)
								  $desc = null,     // Описание
								  $payedBy = null   // Платежная сисетма (internal - внутренняя проводка)
								) {

		if ($userId !== null and $sum !== null and $action !== null and $payedBy !== null) {
			$this->loadModel('Bill');
			$billRecord = array();
			$billRecord['Bill']['user_id'] = $userId;
			$billRecord['Bill']['sumPlusReal'] = round($sumReal, 2);
			$billRecord['Bill']['payedBy'] = $payedBy;

			if ($orderId !== null) {
				$billRecord['Bill']['order_id'] = $orderId;
			}
			if ($desc !== null) {
				$billRecord['Bill']['desc'] = $desc;
			}

			switch ($action) {
				case 'get':
					$billRecord['Bill']['sumMinus'] = round($sum, 2);
					break;

				case 'put':
					$billRecord['Bill']['sumPlus'] = round($sum, 2);
					break;

				default:
					return false;
					break;
			}

			if (!$this->Bill->save($billRecord)) {
				$this->checkBill($userId); // Пересчитать баланс клиента
				return false;
			} else {
				$this->checkBill($userId); // Пересчитать баланс клиента
				return true;
			}

		} else {
			return false;
		}
	}

	public function test() {
		//pr($this->checkBill(11));

	}

	public function index() {
		$this->set('title_for_layout', 'Просмотр заказов');
		$this->DarkAuth->requiresAuth();
		$this->loadModel('UserOrder');
		$this->loadModel('ServerTemplate');

		$this->UserOrder->Order->unbindModel(['belongsTo' => 'User']);
		$user = $this->UserOrder->find('first',
									   array(
									   		'recursive' => '2',
											'conditions' => array(
																'id' => $this->DarkAuth->getUserID()
																 )
									   )
										);
		//pr($user['Order']);
		if (!empty($user['Order'])) {
			$i = 0;
			foreach ( $user['Order'] as $order ) {

	       		// Заполнить поле payedBy, если пустое
	       		if ($order['payed'] == 1 and empty($order['payedBy'])) {

	       			$payedBy = 'unknown';

	       			if (empty($order['description'])) {
	       				$payedBy = 'manual';
	       			} else {
	       				$descSplitted = preg_split('/\<br\/\>/', $order['description']);

	       				if (count($descSplitted) > 0) {

	       					$pregYandex = '/^Платёж\sYandex\s\#\d+\sосуществлён\s\d{4}-\d{2}-\d{2}T\d{2}\:\d{2}\:\d{2}\+\d{2}\:\d{2}$/i';
	       					$pregWm     = '/^(?:Платёж\s\#\d+\sосуществлён\sчерез\s(?:систему\s\d{2}|WebMoney)\.|Счёт\sWM\s\#\d+,\sплатёж\s\#\d+\sосуществлён\sс\sкошелька\sR\d+\sпокупателем\sWMid\s\d+\.)$/i';
	       					$pregRbk    = '/^Платёж\sRBK\s\#\d+\sосуществлён\s\d{4}-\d{2}-\d{2}\s\d{2}\:\d{2}\:\d{2}$/i';
	       					//$pregQiwi = '//i';

		       				foreach ( $descSplitted as $descString ) {

	       						if (preg_match($pregYandex, $descString)) {
	       							$payedBy = 'yandex';
	       							break;
	       						} elseif (preg_match($pregWm, $descString)) {
	       							$payedBy = 'webmoney';
	       							break;
	       						} elseif (preg_match($pregRbk, $descString)) {
	       							$payedBy = 'rbk';
	       							break;
	       						}
							}
	       				}

	       			}

	       			$user['Order'][$i]['payedBy'] = $payedBy;

	       			$this->Order->id = $order['id'];

	       			if (!$this->Order->saveField('payedBy', $payedBy)) {
	       				$this->Session->setFlash('Возникла ошибка при сохранении системы платежа в БД. Сообщите в техподдержку об этой ошибке и номер заказа: #'.$order['id'], 'flash_error');
	       			}
	       		}

	       		if (!empty($order['Server'][0]['id'])) {
	       			$this->ServerTemplate->id = $order['Server'][0]['id'];
	       			$server = $this->ServerTemplate->read();
	       			$user['Order'][$i]['Server'][0]['GameTemplate'] = $server['GameTemplate'][0];
	       		}

	       		$i++;
				}

			$this->set('orders', $user['Order']);
		} else {
			$this->Session->setFlash('У вас еще нет заказов.', 'flash_success');
		}

	}

	public function control() {
		$this->set('title_for_layout', 'Заказы');
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

		if (!empty($this->data) or $this->Session->check('ordersChoise')) {

			if ( @$this->data['User']['username'] === 'all') {
				$this->Session->delete('ordersChoise.username');
				unset($this->request->data['User']['username']);
			}

			if ( @$this->data['Server']['id'] === 'all') {
				$this->Session->delete('ordersChoise.serverId');
				unset($this->request->data['Server']['id']);
			}
		}

		// Если есть и в сессии, и форма, то брать данные из формы
		if ($this->Session->check('ordersChoise')) {
			// Проверка имени клиента
			if ( (empty($this->data['User']['username'])) && $this->Session->check('ordersChoise.username') ) {
				 	$this->request->data['User']['username'] = $this->Session->read('ordersChoise.username');
				}
			// Проверка ID сервера
			if ( (empty($this->data['Server']['id'])) && $this->Session->check('ordersChoise.serverId') ) {
				 	$this->request->data['Server']['id'] = $this->Session->read('ordersChoise.serverId');
				}
		}

		// Выбор заказов по логину клиента
		if (!empty($this->request->data['User']['username']) and $this->request->data['User']['username'] !== 'all') {

			$this->Order->User->unbindModel(array( 'hasAndBelongsToMany' => array(
																					'Group',
																					'SupportTicket'
																				   )));

			$this->Order->User->bindModel(['hasMany' => ['Order' => ['className' => 'Order',
																		    'fields' => 'id' ]]]);

			$searchUser = $this->Order->User->findByUsername($this->request->data['User']['username']);

			if (!empty($searchUser)) {
				foreach ( $searchUser['Order'] as $order ) {
					$ordersIds[] = $order['id'];
				}

				//pr($ordersIds);
				$conditions['Order.id'] = @$ordersIds;
				$this->Session->write('ordersChoise.username', $this->request->data['User']['username']);
				$this->set('searchUserName', @$this->request->data['User']['username']);
			}

		}

		// Выбор заказов по серверу
		if (!empty($this->request->data['Server']['id']) and $this->request->data['Server']['id'] !== 'all') {

			$this->Order->Server->unbindModel(array( 'hasAndBelongsToMany' => array(
																					'Type',
																					'GameTemplate',
																					'Mod',
																					'Plugin',
																					'RootServer',
																					'Service',
																					'User',
																					'VoiceMumbleParam',
																					'RadioShoutcastParam'
																				   )));

			$this->Order->Server->bindModel(array(
													'hasAndBelongsToMany' => array(
																		'Server' => array('className' => 'Order',
																						  'fields' => 'id'
																						  )
															)));

			$searchServer = $this->Order->Server->findById($this->request->data['Server']['id']);

			if (!empty($searchServer)) {
				foreach ( $searchServer['Order'] as $server ) {
					$serversIds[] = $server['id'];
				}

				//pr($serversIds);
				$conditions['Order.id'] = @$serversIds;
				$this->Session->write('ordersChoise.serverId', $this->request->data['Server']['id']);
				$this->set('searchServerId', @$this->request->data['Server']['id']);
			}

		}

		if (!empty($conditions)) {
			$this->paginate =  array(   'conditions'=>$conditions,
										'limit' => 25,
										'order' => array(
												            'Order.id' => 'desc'
												        ));
			} else {
			$this->paginate = array('limit' => 25, 'order' => 'created DESC' );
		}

		$this->set('orders', $this->paginate());

		// Первое число месяца
		$dayOneOfMonth = date('Y-m-d H:i:s', strtotime(date('Y-m', time())));
		$payed = $this->Order->query("SELECT SUM(`sumPayed`), SUM(`sumGot`) FROM `orders` WHERE `created` > '".$dayOneOfMonth."' OR `payedDate` = NULL OR `payedDate` > '".$dayOneOfMonth."'");

		$sum = array (  'payed' =>  $payed[0][0]['SUM(`sumPayed`)'],
						'got'   =>  round($payed[0][0]['SUM(`sumGot`)'], 2) );

		$this->set('sumForMonth', $sum);

	}

	public function add($type = null, $game = null) {
		/*
		 * Алгоритм валидации заказа:
		 * 1) Проверить существование сервера
		 * 2) Общая проверка на принадлежность сервера этому юзеру
		 * 3) Взять шаблон по ID сервера - оттуда взять стоимость слота
		 * TODO: В шаблон внести два параметра - минимум и максимум слотов
		 * 4) Арифметически подсчитать стоимость заказа.
		 * 	  Если суммы совпадут - заказ принять.
		 * */

		$this->DarkAuth->requiresAuth();
		$user = $this->DarkAuth->getAllUserInfo();
		$this->loadModel('Location');
		$this->loadModel('Type');
		$this->loadModel('GameTemplate');
		$this->loadModel('Mod');
		$this->loadModel('ServerComp');
		$this->loadModel('ServerRootserver');
		//$this->loadModel('Service');

		// Баланс
		$balance = $this->checkBill($user['User']['id']);
		// Размеры скидок
		$discount[3] = 5; // От 3 месяцев скидка 5%
		$discount[6] = 10; // От 6 месяцев скидка 10%
		$discount[9] = 15; // От 9 месяцев скидка 15%

		$userDiscount = $user['User']['discount'];

		if ( $userDiscount > 50) {
			$userDiscount = 50;
		}

		if (!empty($this->data)) {
			$this->GameTemplate->id = $this->data['GameTemplate']['id'];
			$template = $this->GameTemplate->read();
			$slotCost = $template['GameTemplate']['price']; // Цена слота без скидок
			$slotDiscount = 0;
			$month = intval($this->data['Order']['month']);
			$slots = $this->data['Order']['slots'];

			// Цена слота
			if ($this->data['Server']['privateType'] == 1) { // приватный с паролем
				$slotCost = $template['GameTemplate']['pricePrivatePassword'];
			} elseif ($this->data['Server']['privateType'] == 2) { // приватный с автоотключением
				$slotCost = $template['GameTemplate']['pricePrivatePower'];
			}

			// Установить локацию
			if (!empty($this->data['Location'])) {
				$locationId = $this->data['Location']['id'];
			} else {
				$locationId = '1';
			}

			// Расчет стоимости услуг
			$serviceSum = 0;
			if (!empty($this->request->data['Service'])) {
				// Сначала проверить доступность услуг
				$serviceAvaliable = $this->requestAction(array('controller' => 'Services',
																'action'=>'getServices'),
														  array('pass' => array($template['GameTemplate']['id'],
														  						'all',
														  						$locationId)));

       			// По полученному списку доступных услуг проверяем, действительно
       			// ли доступны заказанные услуги
				foreach ( $this->data['Service'] as $service ) {
					if (!empty($serviceAvaliable[$service['id']])) {
						$serviceSum += $serviceAvaliable[$service['id']]['price'];
						$serviceList[] = $service['id'];
					} else {
						// По этой ошибке выводить флэш
						$unAvailiableService = true;
					}
				}

			}

			/* Проверить промо-код, если есть*/

			if (!empty($this->data['PromoCode']['code'])) {

				$this->loadModel('PromoCode');
				$this->PromoCode->bindModel(array(
											'belongsTo' => array(
																'Promo' => array()
															)));
				$promo = $this-> PromoCode->find('first', array(
															'conditions' => array (
																					'used not' => '1',
																					'code' => $this->data['PromoCode']['code']
																				   )));
				if ( !empty($promo) ) {
					$promoDiscount = $promo['Promo']['discount'];
				}

			}

			$userDiscount = $userDiscount + @$promoDiscount;

			if ( $month < 3) {
				$curDiscount = $userDiscount;
			} elseif ( $month >= 3 and $month < 6) {
				$curDiscount = $discount[3] + $userDiscount;
			} elseif ( $month >= 6 and $month < 9) {
				$curDiscount = $discount[6] + $userDiscount;
			} elseif ( $month >= 9 ) {
				$curDiscount = $discount[9] + $userDiscount;
			}

			// Сумма без скидки
			$sumNoDiscount = $month*($serviceSum + $slotCost*$slots);
			// Размер скидки
			$sumDiscount = $sumNoDiscount - round($sumNoDiscount*((100-$curDiscount)/100));
			// Сумма с учётом скидки
			$correctSum = $sumNoDiscount - $sumDiscount;

			if (!@$unAvailiableService and $correctSum > 0 and $month > 0) { // Если услуги учтены верно, формируем и сохраняем заказ дальше

				// Сформировать переменные заказа для сохранения,
				// дабы особо умные не передали лишнего
				$order['Order']['sum'] = $correctSum;
				$order['Order']['sumToPay'] = $correctSum;
				$order['Order']['month'] = $this->data['Order']['month'];

				// Теперь установим привязку заказа к пользователю
				$order['Order']['user_id'] = $this->DarkAuth->getUserID();

				/****************************************************************/
				// Лицевой счет
				// Если нет оплаты с лицевого счёта:
				$order['Order']['description'] = '';
				// Установить привязку к серверу и к пользователю

				$payFull = false;

				if ($this->data['Order']['payFrom'] === 'out') {
					$order['Order']['sumToPay'] = $correctSum;
				}
				else
				// Частичная оплата
				if ($this->data['Order']['payFrom'] === 'part') {
					// Если на счету больше требуемой части оплаты - списываем её
					// Если меньше требуемой, но больше нуля - списывать весь остаток

					$partAmount = floatval($this->data['Order']['partPayAmount']);
					if ($partAmount >= $correctSum && $balance >= $correctSum) {
						// Полная оплата
						$payFull = true;
					} elseif ($partAmount > 0 && $balance >= $partAmount && $partAmount < $correctSum) {
						// Частичная оплата
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списано '.$partAmount.' руб. после поступления средств.<br/>';
					} elseif ($balance > 0 && $balance < $partAmount) {
						$partAmount = $balance;
						// Полное списание остатка
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списан весь остаток в размере '.$partAmount.' руб. после поступления средств.<br/>';
					} elseif ($balance <= 0) {
						$partAmount = 0;
					}

					$order['Order']['sumToPay'] = round($correctSum - $partAmount, 2);
				}
				else
				// Полная оплата с лицевого счёта
				if ($this->data['Order']['payFrom'] === 'full') {

					// Если на счете недостаточно средств, то
					// повторно вывести окно с формированием заказа
					// и ошибкой
					if ($balance < $correctSum) {
						$this->Session->setFlash('На Личном счёте недостаточно средств. Сформируйте заказ снова.', 'flash_error');
						$this->redirect(array('action' => 'add'));
						return false;
					} else {
						$payFull = true;
					}
				}
				/****************************************************************/

				// Синхронизируем данные с Server
				$server['ServerType']['id'] = $this->request->data['Type']['id'];
				$server['ServerComp']['slots'] = $this->request->data['Order']['slots'];
				$server['ServerGameTemplate'] = $this->request->data['GameTemplate'];
				//$server['ServerMod'] = $this->request->data['Mod'];

				// Сразу прописать публичную статистику для сайта,
				// если сервер публичный
				if ($this->data['Server']['privateType'] == 0) {
					$server['ServerComp']['publicStat'] = 1;
				}

				/***** Установить fps *************************************************/
				if ($template['Type'][0]['id'] == 1
						or
					$template['Type'][0]['id'] == 5 ) {

						if ($template['GameTemplate']['name'] !== 'l4d'
								and
							$template['GameTemplate']['name'] !== 'l4d2') {
								if ($slots > 0 and $slots <= 12) {
									$server['ServerComp']['fpsmax'] = 1000;
								} elseif ($slots > 12 and $slots <= 32) {
									$server['ServerComp']['fpsmax'] = 500;
								} else {
									$server['ServerComp']['fpsmax'] = 300;
								}

							}
						$fpsMessage = "У сервера будет максимальный <strong>FPS ".$server['ServerComp']['fpsmax']."</strong>.";

					}
				/********************************************************************/

				/***** Для L4D Tick 100 установить мод tickrate *********************/
				if ($template['GameTemplate']['name'] === 'l4d-t100') {
					$server['ServerMod'] = array('id' => '39');
				} elseif ($template['GameTemplate']['name'] === 'l4d2-t100') {
					$server['ServerMod'] = array('id' => '40');
				}

				/********************************************************************/
				// Для серверов CoD нужно указать мод запуска:
				if ($this->data['Type']['id'] == 6) {
					$this->ServerComp->ServerMod->id = $this->data['Mod']['id'];
					$modDesc = $this->ServerComp->ServerMod->read();
					$server['ServerComp']['mod'] = $modDesc['ServerMod']['name'];
				}

				$server['ServerUser'] = ['id' => $order['Order']['user_id']];
				/* Услуги
				 * Сначала удалить старый кусок
				 * Потом прописать массив id для сохранения ассоциаций
				*/
				unset($this->request->data['Service']);
				$server['ServerService']['ServerService'] = @$serviceList;
				$this->request->data['Service']['Service'] = @$serviceList;
				$server['ServerComp']['privateType'] = $this->data['Server']['privateType'];
				// Сразу внести карту сервера по-умолчанию, если сервер игровой
				$this->GameTemplate->id = $this->data['GameTemplate']['id'];
				$template = $this->GameTemplate->read(array('longname','defaultMap'));
				if (!empty($template)) {
					$server['ServerComp']['map'] = $template['GameTemplate']['defaultMap'];
				}

				// Локация
				$server['ServerLocation']['id'] = $locationId;

				// Сохранить информацию о сервере
				if ($this->ServerComp->save($server)) {

					$order['Server']['id'] = $this->ServerComp->id;

					if ($curDiscount > 0) {
						$order['Order']['description'] .= 'Предоставлена скидка '.$curDiscount.'% на сумму '.$sumDiscount.' руб.';

						if ( !empty($promo) ) {
							$order['Order']['description'].= ' в том числе '.$promo['Promo']['discount'].'% по акции "'.@$promo['Promo']['description'].'".';
						}
					}

					/***********************************************************************/
					// Полная оплата с лицевого счёта //
					if ($payFull === true) {
						$order['Order']['sumToPay'] = 0;
						$order['Order']['payed'] = 1;
						$order['Order']['payedBy'] = 'internal';
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списана полная сумма.';

						if ($this->Order->save($order)) {
							unset($order);
							$order = $this->Order->read();

							if ($this->orderTransaction($order, 'internal') === false) {
								$this->Session->setFlash('Возникла ошибка при проведении заказа в системе. Посмотрите подробности в описании заказа.', 'flash_error');
							} else {
								$this->Session->setFlash('Заказ успешно проведен. Оплата снята с лицевого счёта.', 'flash_success');
							}

							$this->redirect(array('action' => 'detail', $order['Order']['id']));
							return true;
						} else {
							$this->Session->setFlash('Возникла ошибка при сохранении заказа в системе: '.mysql_error(), 'flash_error');

							$this->redirect(array('action' => 'add'));
							return false;
						}
					}

					/****************************************************************/

						// Вот теперь сохраняем заказ
						if (@$this->Order->save($order)) {

							$order = $this->Order->read();
							$confirmMessage = "Заказ на сумму <strong>".$order['Order']['sum'].
										  	" руб.</strong> на аренду сервера <strong>".$template['GameTemplate']['longname']." #" .$order['Server'][0]['id'].
										  	"</strong> на <strong>".$slots." слотов</strong> сроком на <nobr><strong>".
										  	$order['Order']['month']." мес.</strong></nobr> сформирован. " .
										  	@$fpsMessage."<br/>\n";

							$this->Session->setFlash($confirmMessage, 'flash_success');

							/* Удалить одноразовый промо-код*/
							if ( !empty($promo)
									 and
								 $promo['Promo']['type'] === 'token') {
								 		$this->PromoCode->id = $promo['PromoCode']['id'];
								 		$this->PromoCode->saveField('used',1);
								 }

							$this->redirect(array('action' => 'pay', $this->Order->id));

						} else {
							$this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
						}
				}
			} elseif ($correctSum <= 0) {
				$this->Session->setFlash('Некорректный заказ', 'flash_error');
			} else {
				$this->Session->setFlash('К сожалению, недоступны некоторые услуги. Попробуйте выбрать другую локацию и повторите заказ.', 'flash_error');
				$this->redirect(array('action' => 'add'));
			}

		} else {
			$this->set('userDiscount', $userDiscount);
			$this->set('discount', $discount);
			$this->set('balance', $balance);

			//********************************************************
			//берем полный список локаций
			$locationsList = $this->Location->find('list', array('fields' => array('id','name'),
														'conditions'=>array('enabled'=>'1')));
			if (!empty($locationsList)) {
				asort($locationsList);
				$this->set('locationsList', $locationsList);

				//Выставить пункт списка нас случайную локацию для равномерного распределения
				$this->set('locationId', array_rand($locationsList));
				//$this->set('locationId', 3); // Временно жестко ставить H1Host

				// Конец выбора локаций
				//********************************************************

				//********************************************************
				//берем полный список типов серверов
				$typesList = $this->Type->find('list', array('fields' => array('id','longname'),
															'conditions'=>array('active'=>'1')));
				asort($typesList);
				$this->set('typesList', $typesList);

				/* Если есть запрос выбрать тип, пропишем его тут.
				 * Тип соответсвует его ID в базе.
				 * Т.к. никаких проблем безопасности это не несёт -
				 * Вставляем переменную напрямую из запроса.
				 */
				if (@$type) {
					$this->set('typeId', @$type);
				} else {
					//Выставить пункт списка по умолчанию на SRCDS
					$this->set('typeId', '1');
				}

				// Конец выбора типов серверов
				//********************************************************
				/*
				 * Создать список типов приватных серверов
				 * */

				$typeDiscount = array(
										'0' => 'Публичный сервер',
										'1' => 'Приватный с паролем',
										'2' => 'Приватный с автоотключением'
									  );

				$this->set('typeDiscount', $typeDiscount);

				//$this->request->data['GameTemplate']['id']=$this->data['GameTemplate'][0]['id'];
				//Выберем шаблон, чтобы по нему составлять заказ
				$this->set('gameTemplatesList', $this->GameTemplate->find('list', array('fields' => array('longname'),
																						'conditions' => array('active'=>'1')
																						)));
				if (intval(@$game) > 0) {
					$this->set('gameTemplateId', $game); // Выставить пункт списка на игру
				}

				$this->set('modsList', $this->Mod->find('list'));
				// Кусочек скрипта для организации ползунка выбора слотов
				$script="";
				$i=1;

				$this->GameTemplate->unbindModel(array(
														'hasAndBelongsToMany' => array(
																			'Type',
																			'Mod',
																			'Plugin',
																			'Config',
																			'Protocol',
																			'Service'
																)));

				$gameTemplates = $this->GameTemplate->find('all', array('conditions' => array('active'=>'1')));

				foreach ($gameTemplates as $gameTemplate):
					if ($i>1) {
						$script.="\n else ";
					}
					$script.="if (selectedGame === '".$gameTemplate['GameTemplate']['id']."') {
									//".$gameTemplate['GameTemplate']['name']."
									v = ".$gameTemplate['GameTemplate']['slots_value'].";
									mi = ".$gameTemplate['GameTemplate']['slots_min'].";
									ma = ".$gameTemplate['GameTemplate']['slots_max'].";
									price = ".$gameTemplate['GameTemplate']['price'].";
									pricePassword = ".$gameTemplate['GameTemplate']['pricePrivatePassword'].";
									pricePower = ".$gameTemplate['GameTemplate']['pricePrivatePower'].";
								}\n";
					$i++;
					//pr($gameTemplate);
				endforeach;
				$script.="else {

								var v = 8;
								var mi = 8;
								var ma = 32;
							}\n";
				$this->set('script', $script);
			} else {
				$this->Session->setFlash('Нет доступных локаций со свободными ресурсами. Попробуйте сформировать заказ позже.', 'flash_error');
			}
		}

		}

	public function addEac($type = null) {
		/*
		 * Алгоритм валидации заказа:
		 * 1) Проверить существование сервера
		 * 2) Общая проверка на принадлежность сервера этому юзеру
		 * 3) Взять шаблон по ID сервера - оттуда взять стоимость слота
		 * TODO: В шаблон внести два параметра - минимум и максимум слотов
		 * 4) Арифметически подсчитать стоимость заказа.
		 * 	  Если суммы совпадут - заказ принять.
		 * */

		$this->DarkAuth->requiresAuth();
		$user = $this->DarkAuth->getAllUserInfo();
		$this->loadModel('Type');
		$this->loadModel('GameTemplate');
		$this->loadModel('ServerComp');
		$this->loadModel('ServerRootserver');
		//$this->loadModel('Service');

		// Баланс
		$balance = $this->checkBill($user['User']['id']);

		if (!empty($this->data)) {
			$this->GameTemplate->id = $this->data['GameTemplate']['id'];
			$template = $this->GameTemplate->read();
			$month = intval($this->data['Order']['month']);
			$correctSum = $template['GameTemplate']['price']*$month;

			if ($correctSum > 0 and $month > 0) { // Если услуги учтены верно, формируем и сохраняем заказ дальше

				// Сформировать переменные заказа для сохранения,
				// дабы особо умные не передали лишнего
				$order['Order']['sum'] = $correctSum;
				$order['Order']['sumToPay'] = $correctSum;
				$order['Order']['month'] = $month;

				// Теперь установим привязку заказа к пользователю
				$order['Order']['user_id'] = $this->DarkAuth->getUserID();

				/****************************************************************/
				// Лицевой счет
				// Если нет оплаты с лицевого счёта:
				$order['Order']['description'] = '';
				// Установить привязку к серверу и к пользователю

				$payFull = false;

				if ($this->data['Order']['payFrom'] === 'out') {
					$order['Order']['sumToPay'] = $correctSum;
				}
				else
				// Частичная оплата
				if ($this->data['Order']['payFrom'] === 'part') {
					// Если на счету больше требуемой части оплаты - списываем её
					// Если меньше требуемой, но больше нуля - списывать весь остаток

					$partAmount = floatval($this->data['Order']['partPayAmount']);
					if ($partAmount >= $correctSum && $balance >= $correctSum) {
						// Полная оплата
						$payFull = true;
					} elseif ($partAmount > 0 && $balance >= $partAmount && $partAmount < $correctSum) {
						// Частичная оплата
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списано '.$partAmount.' руб. после поступления средств.<br/>';
					} elseif ($balance > 0 && $balance < $partAmount) {
						$partAmount = $balance;
						// Полное списание остатка
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списан весь остаток в размере '.$partAmount.' руб. после поступления средств.<br/>';
					} elseif ($balance <= 0) {
						$partAmount = 0;
					}

					$order['Order']['sumToPay'] = round($correctSum - $partAmount, 2);
				}
				else
				// Полная оплата с лицевого счёта
				if ($this->data['Order']['payFrom'] === 'full') {

					// Если на счете недостаточно средств, то
					// повторно вывести окно с формированием заказа
					// и ошибкой
					if ($balance < $correctSum) {
						$this->Session->setFlash('На Личном счёте недостаточно средств. Сформируйте заказ снова.', 'flash_error');
						$this->redirect(array('action' => 'add'));
						return false;
					} else {
						$payFull = true;
					}
				}
				/****************************************************************/

				// Синхронизируем данные с Server
				$server['ServerType']['id'] = 8;
				$server['ServerComp']['slots'] = 1;
				$server['ServerGameTemplate'] = $this->data['GameTemplate'];

				$server['ServerUser'] = ['id' => $order['Order']['user_id']];

				// Сохранить информацию о сервере
				if ($this->ServerComp->save($server)) {
					$order['Server']['id'] = $this->ServerComp->id;

					/***********************************************************************/
					// Полная оплата с лицевого счёта //
					if ($payFull === true) {
						$order['Order']['sumToPay'] = 0;
						$order['Order']['payed'] = 1;
						$order['Order']['payedBy'] = 'internal';
						$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списана полная сумма.';

						if ($this->Order->save($order)) {
							unset($order);
							$order = $this->Order->read();

							if ($this->orderTransaction($order, 'internal') === false) {
								$this->Session->setFlash('Возникла ошибка при проведении заказа в системе. Посмотрите подробности в описании заказа.', 'flash_error');
							} else {
								$this->Session->setFlash('Заказ успешно проведен. Оплата снята с лицевого счёта.', 'flash_success');
							}

							$this->redirect(array('action' => 'detail', $order['Order']['id']));
							return true;
						} else {
							$this->Session->setFlash('Возникла ошибка при сохранении заказа в системе: '.mysql_error(), 'flash_error');

							$this->redirect(array('action' => 'add'));
							return false;
						}
					}

					/****************************************************************/

						// Вот теперь сохраняем заказ
						if (@$this->Order->save($order)) {

							$order = $this->Order->read();
							$confirmMessage = "Заказ на сумму <strong>".$order['Order']['sum'].
										  	" руб.</strong> на аренду сервера <strong>".$template['GameTemplate']['longname']." #" .$order['Server'][0]['id'].
										  	"</strong> сроком на <nobr><strong>".
										  	$order['Order']['month']." мес.</strong></nobr> сформирован. " .
										  	@$fpsMessage."<br/>\n";

							$this->Session->setFlash($confirmMessage, 'flash_success');

							$this->redirect(array('action' => 'pay', $this->Order->id));

						} else {
							$this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
						}
				}
			} elseif ($correctSum <= 0) {
				$this->Session->setFlash('Некорректный заказ', 'flash_error');
			} else {
				$this->Session->setFlash('К сожалению, недоступны некоторые услуги. Попробуйте выбрать другую локацию и повторите заказ.', 'flash_error');
				$this->redirect(array('action' => 'add'));
			}

		} else {
			$this->set('balance', $balance);

			//********************************************************
			//берем шаблоны типа EAC
			$this->Type->id = 8;
			$typeEac = $this->Type->read();

			// Конец выбора типов серверов
			//********************************************************
			$gameTemplatesList = array();
			$script="";
			$i=1;

			foreach ($typeEac['GameTemplate'] as $gameTemplate) {
				$gameTemplatesList[$gameTemplate['id']] = $gameTemplate['longname'];

				if ($i>1) {
					$script.="\n else ";
				}
				$script.="if (selectedEac === '".$gameTemplate['id']."') {
								//".$gameTemplate['name']."
								price = ".$gameTemplate['price'].";
							}\n";
				$i++;
			}

			//Выберем шаблон, чтобы по нему составлять заказ
			$this->set('gameTemplatesList', $gameTemplatesList);

			$this->set('script', $script);

		}

	}

	public function prolongate($serverId = null) {
		/* Выношу продление в отдельную функцию, т.к.
		 * сама эта процедура отнимает меньше ресурсов,
		 * чем формирование заказа - не нужно формировать
		 * списки типов, игр, модом и т.д.
		 *
		 * Алгоритм валидации заказа:
		 * 1) Проверить существование сервера
		 * 2) Общая проверка на принадлежность сервера этому юзеру
		 * 3) Взять шаблон по ID сервера - оттуда взять стоимость слота
		 * 4) Проверка стоимости заказа не требуется, т.к. формируется она тут
		 * */

		$this->DarkAuth->requiresAuth();
		$this->loadModel('ServerTemplateUser');
		$this->ServerTemplateUser->bindModel(array(
					'hasAndBelongsToMany' => array(
													'Service' => array()
												)));
		if ($serverId) {

			// Пересчитать баланс
			$this->checkBill($this->DarkAuth->getUserId());

			$this->ServerTemplateUser->id = $serverId;
			$server = $this->ServerTemplateUser->read();
			if (!empty($server)) {
				// Проверим, принадлежит ли сервер пользователю
				$rights = $this->DarkAuth->getAccessList();	// Права доступа
				if (
					$server['User'][0]['id'] == $this->DarkAuth->getUserId()
						or
					$rights['Admin'] == 1
					) {

					// Размеры скидок
					$discount[3] = 5; // От 3 месяцев скидка 5%
					$discount[6] = 10; // От 6 месяцев скидка 10%
					$discount[9] = 15; // От 9 месяцев скидка 15%

					$userDiscount = $server['User'][0]['discount'];

					if ( $userDiscount > 50) {
						$userDiscount = 50;
					}

					if (empty($this->data) and $server['ServerTemplateUser']['initialised'] == 1) { // Заполнить форму
						$this->data = $server;

						// Создать список типов приватных серверов
						$typeDiscount = array(
												'0' => 'Публичный сервер',
												'1' => 'Приватный с паролем',
												'2' => 'Приватный с автоотключением'
											  );

						$this->set('typeDiscount', $typeDiscount);
						// Создать список доступных услуг для конкретного физического севрера
						$this->loadModel('ServerRootserver', $serverId);
						$rootServer = $this->ServerRootserver->read();

					 	$this->set('discount', $discount);
					 	$this->set('userDiscount', $userDiscount);
					} elseif ($this->data and $server['ServerTemplateUser']['initialised'] == 1)
					// Запрос на формирование заказа на продление
					{
						if (
							!empty($this->data['Order']['month'])
								and
							intval(@$this->data['Order']['month']) > 0
							) {

							$this->loadModel('OrderClean');
							$slots = $server['ServerTemplateUser']['slots'];
							$month = $this->data['Order']['month'];
							$slotCost = $server['GameTemplate'][0]['price']; // Цена слота без скидок
							$slotDiscount = 0;

							// Цена слота
							if ($server['ServerTemplateUser']['privateType'] == 1) { // приватный с паролем
								$slotCost = $server['GameTemplate'][0]['pricePrivatePassword'];
							} elseif ($server['ServerTemplateUser']['privateType'] == 2) { // приватный с автоотключением
								$slotCost = $server['GameTemplate'][0]['pricePrivatePower'];
							}

							/* Проверить промо-код, если есть*/

							if (!empty($this->data['PromoCode']['code'])) {

								$this->loadModel('PromoCode');
								$this->PromoCode->bindModel(array(
															'belongsTo' => array(
																				'Promo' => array()
																			)));
								$promo = $this-> PromoCode->find('first', array(
																			'conditions' => array (
																									'used not' => '1',
																									'code' => $this->data['PromoCode']['code']
																								   )));
								if ( !empty($promo) ) {
									$promoDiscount = $promo['Promo']['discount'];
									$userDiscount = $userDiscount + @$promoDiscount;
								}

							}

							if ( $month < 3) {
								$curDiscount = $userDiscount;
							} elseif ( $month >= 3 and $month < 6) {
								$curDiscount = $discount[3] + $userDiscount;
							} elseif ( $month >= 6 and $month < 9) {
								$curDiscount = $discount[6] + $userDiscount;
							} elseif ( $month >= 9 ) {
								$curDiscount = $discount[9] + $userDiscount;
							}

							$serviceSum = 0;
			       			// Стоимость услуг
							foreach ( $server['Service'] as $service ) {
									$serviceSum += $service['price'];
								}

							// Сумма без скидки
							$sumNoDiscount = $month*($serviceSum + $slotCost*$slots);
							// Размер скидки
							$sumDiscount = $sumNoDiscount - round($sumNoDiscount*((100-$curDiscount)/100));
							// Сумма с учётом скидки
							$sum = $sumNoDiscount - $sumDiscount;

							/****************************************************************/
							// Лицевой счет
							// Если нет оплаты с лицевого счёта:
							$order['Order']['description'] = '';
							// Установить привязку к серверу и к пользователю
							$order['User']['id'] = $server['User'][0]['id'];
							$order['Server']['id'] = $server['ServerTemplateUser']['id'];

							$payFull = false;

							if ($this->data['Order']['payFrom'] === 'out') {
								$order['Order']['sumToPay'] = $sum;
							}
							else
							// Частичная оплата
							if ($this->data['Order']['payFrom'] === 'part') {
								// Если на счету больше требуемой части оплаты - списываем её
								// Если меньше требуемой, но больше нуля - списывать весь остаток

								$balance = $this->checkBill($server['User'][0]['id']);
								$partAmount = floatval($this->data['Order']['partPayAmount']);
								if ($partAmount >= $sum && $balance >= $sum) {
									// Полная оплата
									$payFull = true;
								} elseif ($partAmount > 0 && $balance >= $partAmount && $partAmount < $sum) {
									// Частичная оплата
									$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списано '.$partAmount.' руб. после поступления средств.<br/>';
								} elseif ($balance > 0 && $balance < $partAmount) {
									$partAmount = $balance;
									// Полное списание остатка
									$order['Order']['description'] .= 'Для оплаты заказа с Личного счёта будет списан весь остаток в размере '.$partAmount.' руб. после поступления средств.<br/>';
								} elseif ($balance <= 0) {
									$partAmount = 0;
								}

								$order['Order']['sumToPay'] = round($sum - $partAmount, 2);
							}
							else
							// Полная оплата с лицевого счёта
							if ($this->data['Order']['payFrom'] === 'full') {
								$balance = $this->checkBill($server['User'][0]['id']);

								// Если на счете недостаточно средств, то
								// повторно вывести окно с формированием заказа
								// и ошибкой
								if ($balance < $sum) {
									$this->Session->setFlash('На Лицевом счёте недостаточно средств. Сформируйте заказ снова.', 'flash_error');
									$this->redirect(array('action' => 'prolongate', $serverId));
									return false;
								} else {
									$payFull = true;
								}
							}

							if ($payFull === true) {
								$order['Order']['sum'] = $sum;
								$order['Order']['month'] = $month;
								$order['Order']['sumToPay'] = 0;
								$order['Order']['payed'] = 1;
								$order['Order']['payedBy'] = 'internal';
								$order['Order']['description'] = 'Для оплаты заказа с Лицевого счёта списана полная сумма.';

								if ($this->Order->save($order)) {
									$this->loadModel('ServerRootserver');
									unset($order);

									$order = $this->Order->read();

									if ($this->orderTransaction($order, 'internal') === false) {
										$this->Session->setFlash('Возникла ошибка при проведении заказа в системе. Посмотрите подробности в описании заказа.', 'flash_error');
									} else {
										$this->Session->setFlash('Заказ успешно проведен. Оплата снята с лицевого счёта.', 'flash_success');
									}

									$this->redirect(array('action' => 'detail', $this->Order->id));
								} else {
									$this->Session->setFlash('Возникла ошибка при сохранении заказа в системе: '.mysql_error(), 'flash_error');

									$this->redirect(array('action' => 'prolongate', $serverId));
									return false;
								}
							}

							/****************************************************************/

							$order['Order']['sum'] = $sum;
							$order['Order']['month'] = $month;
							if ($curDiscount > 0) {
								$order['Order']['description'] .= 'Предоставлена скидка '.$curDiscount.'% на сумму '.$sumDiscount.' руб.';

								if ( !empty($promo) ) {
									$order['Order']['description'].= ' в том числе '.$promo['Promo']['discount'].'% по акции "'.@$promo['Promo']['description'].'".';
								}
							}

							if (@$this->Order->save($order)) {

								$order = $this->Order->read();

								if ($order['Order']['sumToPay'] < $order['Order']['sum']) {
									$confirmComment = '<br/>С вашего Личного счёта будет снято '.round($order['Order']['sum'] - $order['Order']['sumToPay'], 2).' руб.<br/>'.
													  '<strong>Итого к оплате: '.$order['Order']['sumToPay'].' руб.</strong><br/><br/>';
								}

								$confirmMessage = "Заказ на сумму <strong>".$order['Order']['sum'].
								  		" руб.</strong> на продление аренды <br/>сервера <strong>".$server['GameTemplate'][0]['longname']." #" .$order['Server'][0]['id'].
								  		"</strong> сроком на <nobr><strong>".
								  		$order['Order']['month']." мес.</strong></nobr> сформирован.<br/>\n" .
								  		@$confirmComment.
								 		"Выберите способ оплаты.";

								$this->Session->setFlash($confirmMessage, 'flash_success');
								$this->redirect(array('action' => 'pay', $this->Order->id));

							} else {
								$this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
							}

						} else {
							$this->Session->setFlash('Не указан срок продления.', 'flash_error');
						}

					} else {
						$this->redirect(array('action'=>'add'));
					}

				} // Проверка прав
				else
				{
					$this->Session->setFlash('Это чужой сервер. Ай-ай-ай!', 'flash_error');
				}

			} // !empty($server)
			else
			{
				$this->Session->setFlash('Сервера не существует', 'flash_error');
			}

		} // $serverId
		else
		{
			$this->Session->setFlash('Не указан сервер', 'flash_error');
		}

		 	 }

	// Пополнение Личного счёта
	public function makeDeposit($sum = null) {
		$this->DarkAuth->requiresAuth();

		if (is_null($sum)
				and !empty($this->data['Order']['amount'])
				and intval($this->data['Order']['amount']) > 0)
		{
			$sum = intval($this->data['Order']['amount']);
		}

		if ($sum > 0)
		{

			$order = array();
			$sum   = intval($sum);
			$userId  = $this->DarkAuth->getUserId();
			$userAcc = $this->TeamServer->getUserBill();

			$order['Order']['sum'] = $sum;
			$order['Order']['sumToPay'] = $sum;
			$order['Order']['description'] = 'Пополнение лицевого счёта';
			$order['Order']['user_id'] = $userId;

			if ($this->Order->save($order))
			{
				$confirmMessage =   "Заказ на сумму <strong>".$order['Order']['sum'].
							  		" руб.</strong> на пополнение лицевого счёта #".$userAcc." сформирован.<br/>\n" .
							 		"Выберите способ оплаты.";

				$this->Session->setFlash($confirmMessage, 'flash_success');
				$this->set('userAcc', $userAcc);
				$this->redirect(array('action' => 'pay', $this->Order->id));
			} else {
				$this->Session->setFlash('Произошла ошибка при сохранении заказа: '.mysql_error(), 'flash_error');
			}

		}

	}

	//Диалог выбора системы оплаты
	public function pay($orderId = null) {
		//pr($this->params);
		if ($this->checkRights(@$orderId)) {
			$this->DarkAuth->requiresAuth();
			$this->Order->id = $orderId;
			$order = $this->Order->read();

			// Если заказ связан с сервером, значит он на сервер.
			// Иначе считать его пополнением баланса
			if (!empty($order['Server'])) {
				$this->loadModel('ServerTemplate');
				$this->ServerTemplate->id = $order['Server'][0]['id'];
				$server = $this->ServerTemplate->read();

				$balance = $this->checkBill($this->DarkAuth->getUserId());
				$partPay = round($order['Order']['sum'] - $order['Order']['sumToPay']);
				if ($order['Order']['sumToPay'] > 0 && $balance < $partPay) {
					$this->Session->setFlash('На Личном счёте недостаточно средств для частичной оплаты этого заказа! Сформируйте новый заказ или пополните счёт на '.$partPay.' руб.', 'flash_error');
				}

				$this->set('serverTemplate', $server['GameTemplate'][0]);
			}

			$this->set('order', $order);

			// Получим платёжные параметры из файла
			$this->set('paymentParams',parse_ini_file("../Config/payment_params.ini.php", true));
		}

	}

	public function payBySberbank() {
		$this->set('title_for_layout', 'Печать квитании "Сбербанка"');
		$this->layout = 'print';
		if (!empty($this->data['Order'])) {
			$orderId = $this->data['Order']['id'];
			$fio = $this->data['Order']['fio'];
			$address = $this->data['Order']['address'];
		}
		if ($this->checkRights(@$orderId)) {
			$this->DarkAuth->requiresAuth();
			$this->Order->id = $orderId;
			$order = $this->Order->read();

			// Получим платёжные параметры из файла
			$paymentParams = parse_ini_file("../config/payment_params.ini.php", true);

			if (!empty($paymentParams)) {
			$payParams = $paymentParams['teamserver'];
			$payParams['orderNum'] = $order['Order']['id'];
			$payParams['orderDate'] = $order['Order']['created'];
			$payParams['orderSum'] = $order['Order']['sum'];
			$payParams['payByName'] = @$fio;
			$payParams['payByAddress'] = @$address;

				$this->set('payParams', $payParams);
				$message = '<strong>Необходимо вручную заполнить поля:</strong><br>';
				if (!@$fio) {
					$message.= 'Ф.И.О плательщика<br>';
				}
				if (!@$address) {
					$message.= 'Адрес плательщика<br>';
				}

				$message .=   'Заполнить дату на момент оплаты<br>
						      <br>Не забудьте взять с собой паспорт.<br>
						      С указанной «Суммы платежа» Сбербанк за услуги берет 3%.';
				$this->Session->setFlash($message, 'flash_success');
			} else {
				$this->Session->setFlash('Не удалось прочесть параметры фирмы.', 'flash_success');
			}
		}
	}

	public function createPurchase($system = null, $using = null, $orderId = null) {
		/*
		 * @system - Система приёма платежей
		 * 		- rbk
		 * 		- yandex
		 * 		- bankCard
		 * @using - чем платить
		 * 		- Выбор системы оплаты на сайте RBK - common
		 * 		- Оплата с кошелька Rbk Money - inner
				Банковская карта Visa/MasterCard - bankCard
				Электронные платежные системы - exchangers
				Предоплаченная карта RBK Money - prepaidcard
				Системы денежных переводов - transfers
				Платѐжные терминалы - terminals
				SMS - iFree
				Банковский платѐж - bank
				Почта России - postRus
				Банкоматы - atm
				Интернет банкинг – ibank
		 */
		if ($this->checkRights(@$orderId)) {
			$this->loadModel('ServerTemplate');
			$this->DarkAuth->requiresAuth();
			$this->Order->id = $orderId;
			$order = $this->Order->read();
			$this->ServerTemplate->id = $order['Server'][0]['id'];
			$server = $this->ServerTemplate->read();

			// Получим платёжные параметры из файла
			$paymentParams = parse_ini_file("../config/payment_params.ini.php", true);

			// Если заказ уже не оплачен:
			if ($order['Order']['payed'] != 1 && !empty($paymentParams)) {
				// Теперь разблюдовка по системам оплаты:
				if ($system === 'rbk') { // RBK Money
					if ($using !== 'common') {
						$HttpSocket = new HttpSocket();
						$payRequest = array(
											'eshopId' => $paymentParams['rbk']['siteID'],
											'orderId' => $orderId,
											'serviceName' => 'Аренда сервера '.$server['GameTemplate'][0]['longname'].' ID'.$server['ServerTemplate']['id'],
											'recipientAmount' => $order['Order']['sumToPay'],
											'recipientCurrency' => 'RUR',
											'user_email' => $order['User']['email'],
											'version' => '2',
											'preference' => $using,
											'successUrl' => 'https://panel.teamserver.ru/',
											'failUrl' => 'https://panel.teamserver.ru/orders'

											);
						$results = $HttpSocket->post(' https://rbkmoney.ru/acceptpurchase.asp', $payRequest);
					}

				} elseif ($system === 'yandex') { // Yandex Money

				} elseif ($system === 'bankCard') { // Visa/MasterCard

				} else {
					$this->Session->setFlash('Не указана система оплаты', 'flash_error');
				}

			} else {
				$this->Session->setFlash('Заказ уже оплачен либо не могу прочесть данные для формирования платежа.', 'flash_error');
			}

		}

	}

	// Подтверждение оплаты
	public function confirm($confirmer = null, $id = null) {
		$this->loadModel('ServerRootserver');
		$this->loadModel('RootServer');
		if ($confirmer !== 'admin') {
			// Сначала получим данные для проверки данных
			$paymentParams = parse_ini_file("../config/payment_params.ini.php", true);
		}

		if ($confirmer == 'admin') {
		// Административное подтверждение оплаты
			$this->DarkAuth->requiresAuth('Admin','OrdersAdmin');

			$this->Order->id = $id;
			$order = $this->Order->read();
			$order['Order']['payed'] = 1;
			$order['Order']['payedBy'] = 'manual';
		}
		else if ($confirmer == 'platron')
		{
			$this->layout = 'platron';

			// Расшифровки платежных систем. Дополнить позже по факту.
			$platronSystems = array('WEBMONEYR' => 'Webmoney RUR',
									'WEBMONEYZ' => 'Webmoney USD',
									'WEBMONEYE' => 'Webmoney EUR',
									'YANDEXMONEY' => 'Яндекс.Деньги',
									'PAYPALUSD' => 'PayPal USD'
									);

			if (!empty($this->params['form']['pg_xml'])) {
				if (!empty($paymentParams['platron'])) {
					$this->set('sigKey', $paymentParams['platron']['secret_key']);

					$xml = new XML($this->params['form']['pg_xml']);
					$request = $xml->toArray();
					ksort($request['Request']);

					// Сразу выдернуть соль
					if (!empty($request['Request']['pg_salt'])) {
						$this->set('salt', $request['Request']['pg_salt']);
					}

					// Вычислить хэш
					$hash = 'platron;';
					foreach ($request['Request'] as $key => $value) {
						if ($key !== 'pg_sig') {
							if (!is_array($value)) {
								$hash .= $value.';';
							} else {
								$hash .= ';';
							}
						}

					}

					$hash = strtolower(md5($hash.$paymentParams['platron']['secret_key']));

					// Хэш верен
					if ($hash == $request['Request']['pg_sig']) {
						// Теперь проверить наличие заказа
						$this->Order->id = $request['Request']['pg_order_id'];
						$order = $this->Order->read();
						// Проверить, не оплачен ли уже заказ
						if (!empty($order['Order']) && $order['Order']['payed'] == 1) {
							// Заказ уже оплачен, просто вывести подтверждение
							$this->set('status', 'reject');
							$this->set('desc', 'Заказ уже оплачен. (Order is already paid)');
							$this->set('errorCode', 350);
							$this->render();
							return true;
						} elseif (!empty($order['Order']) && $order['Order']['payed'] == 0) {
							// Заказ существует и не оплачен
							$id = $request['Request']['pg_order_id'];
							// Проверить сумму и валюту платежа. Допустимо, чтобы клиент заплатил больше.
							if (floatval($order['Order']['sumToPay']) <= floatval($request['Request']['pg_amount'])
									and $request['Request']['pg_currency'] === 'RUR') {
								// Перевести платежную систему в более читаемый вид
								if (!empty($request['Request']['pg_payment_system'])) {
									if (!empty($platronSystems[$request['Request']['pg_payment_system']])) {
										$platronSystem = $platronSystems[$request['Request']['pg_payment_system']];
									} else {
										$platronSystem = $request['Request']['pg_payment_system'];
									}
								}

								// Платеж совершён
								if (!empty($request['Request']['pg_result']) and !empty($request['Request']['pg_payment_date'])) {

									// Платёж совершен успешно
									if ($request['Request']['pg_result'] == 1) {
										$order['Order']['sumPayed'] = $request['Request']['pg_amount'];
										$order['Order']['sumGot'] = $request['Request']['pg_net_amount'];

										// Тестовый платеж не проводим. Только сообщение пишем
										if (!in_array($request['Request']['pg_payment_system'], array('TEST', 'TESTCARD'))) {
											$order['Order']['description'] = @$order['Order']['description'].
											'<br/>'.
											'Оплачен через систему Platron:<br/>' .
											'за услугу "'.$request['Request']['pg_amount'].'.<br/> ' .
											'Платёж Platron #'.$request['Request']['pg_payment_id'].' осуществлён '.date('H:m:s d.m.Y', strtotime($request['Request']['pg_payment_date'])).'.<br/>'.
											'Оплата получена через '.$platronSystem;
											$order['Order']['payed'] = 1;
										} else {
											$order['Order']['description'] = @$order['Order']['description'].
											'<br/>'.
											'Тестовый платёж через систему Platron:<br/>' .
											'за услугу "'.$request['Request']['pg_amount'].'.<br/> ' .
											'Тестовый платёж Platron #'.$request['Request']['pg_payment_id'].' осуществлён '.date('H:m:s d.m.Y', strtotime($request['Request']['pg_payment_date'])).'.<br/>'.
											'Оплата якобы получена через '.$platronSystem;
										}

										$this->set('status', 'ok');
										$order['Order']['payedBy'] = 'platron';
									}
									// Ошибка при совершении платежа. Вписать в статус платежа
									else
									{
										$order['Order']['description'] = @$order['Order']['description'].
											    '<br/>'.
												'Ошибка при проведении платежа со стороны "Платрон":<br/>' .
												$request['Request']['pg_failure_description'];

										$this->set('status', 'ok'); // Платрону ответить, что сообщение принял
										$this->set('desc', 'Заказ НЕ проведён.');
									}
								}
								// Предварительная проверка заказа Платроном
								else
								{
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Принят на обработку в системе Platron, но средства еще не поступили.<br/>'.
									'Номер счета в системе: #'.@$request['Request']['pg_payment_id'];

									if (!empty($platronSystem)) {
										$order['Order']['description'] .= '<br/>Счёт планируется к оплате через '.$platronSystem;
									}

									$this->set('status', 'ok'); // Платрону ответить, что сообщение принял
								}
							} else {
								$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Предварительная проверка платежа :<br/>' .
									'Ошибка: Неверная сумма или валюта платежа.';
								$this->set('status', 'reject');
								$this->set('desc', 'Неверная сумма или валюта платежа. (Incorrect sum or currency)');
								$this->set('errorCode', 200);
							}

						}
						// Заказа нет вовсе
						else
						{
							$this->set('status', 'reject');
							$this->set('desc', 'Такого заказа не существует. (Wrong order)');
							$this->set('errorCode', 340);
						}
					} else {
						$this->set('status', 'error');
						$this->set('errorDesc', 'Некорректная подпись запроса (Incorrect sign)');
						$this->set('errorCode', 100);
					}

				} else {
					// Не удалось считать конфиг с параметрами платежей
					$this->set('status', 'error');
					$this->set('errorDesc', 'Временная проблема (Temp error)');
					$this->set('errorCode', 1000);
				}

			} else {
					$this->set('status', 'reject');
					$this->set('desc', 'Ошибка входных данных (Wrong data)');
					$this->set('errorCode', 200);
			}
		}// Platron end
		/********************************************************************************************/
		else if ($confirmer == 'rbk')
		{
			/* Проверка и обработка подверждения от РБК
				Номер сайта продавца (eshopId);
				Номер счета продавца (orderId);
				Описание покупки (serviceName);
				Номер счета в системе RBK Money (eshopAccount);
				Сумма платежа (recipientAmount);
				Валюта платежа (recipientCurrency);
				Статус платежа (paymentStatus);
				Имя покупателя (userName);
				Email покупателя (userEmail);
				Дата и время выполнения платежа (paymentData);
				Секретный ключ (secretKey)
			 */
			$this->layout = 'ajax';

			if (!empty($this->params['form'])) {
				$rbkMsg = $this->params['form'];
				//pr($this->params['form']);
				// 1) Проверка секретного ключа:
				if (!empty($paymentParams) && // удалось считать ini с параметрами
					$paymentParams['rbk']['secKey'] == $rbkMsg['secretKey'] && // секретный ключ верен
					$paymentParams['rbk']['siteID'] == $rbkMsg['eshopId'] // ID продавца, т.е. наш, верен
					) {
					// 2) Проверка целостности данных:
					$hash  = $rbkMsg['eshopId'];
					$hash .= '::'.$rbkMsg['orderId'];
					$hash .= '::'.$rbkMsg['serviceName'];
					$hash .= '::'.$rbkMsg['eshopAccount'];
					$hash .= '::'.$rbkMsg['recipientAmount'];
					$hash .= '::'.$rbkMsg['recipientCurrency'];
					$hash .= '::'.$rbkMsg['paymentStatus'];
					$hash .= '::'.$rbkMsg['userName'];
					$hash .= '::'.$rbkMsg['userEmail'];
					$hash .= '::'.$rbkMsg['paymentData'];
					$hash .= '::'.$rbkMsg['secretKey'];

					$hash = md5($hash);
					//pr($hash);

					if ($hash == $rbkMsg['hash']) { // Проверки безопасности пройдены
					// 3) Теперь проверить сумму заказа
						$this->Order->id = $rbkMsg['orderId'];
						$order = $this->Order->read();
						// Проверить, не оплачен ли уже заказ
						if (!empty($order['Order']) && $order['Order']['payed'] == 1) {
							$this->set('message','OK'); // Заказ уже оплачен, просто вывести подтверждение
							$this->render();
							return true;
						} elseif ( !empty($order['Order']) && $order['Order']['sumToPay'] == $rbkMsg['recipientAmount']) {
							// Все, теперь формируем записи в базу и сохраняем статус заказа
							$id = $rbkMsg['orderId'];
							if ($rbkMsg['paymentStatus'] == 5) { // Заказ оплачен
								$order['Order']['sumPayed'] = $rbkMsg['recipientAmount'];
								$order['Order']['sumGot'] = $rbkMsg['recipientAmount']*(1-($paymentParams['rbk']['procent']/100));
								$order['Order']['description'] = @$order['Order']['description'].
								'<br/>'.
								'Оплачен через систему RBK Money :<br/>' .
								'за услугу "'.$rbkMsg['serviceName'].'.<br/> ' .
								'Платёж RBK #'.$rbkMsg['paymentId'].' осуществлён '.$rbkMsg['paymentData'];
								$order['Order']['payed'] = 1;
								$order['Order']['payedBy'] = 'rbk';
							} elseif ($rbkMsg['paymentStatus'] == 3) { // Заказ принят на обработку
								 $order['Order']['description'] = @$order['Order']['description'].
								'<br/>'.'Счёт #'.$rbkMsg['paymentId'].' принят на обработку RBK';
							}

						} else {
							$this->set('message','Order params error');
						}

					} else {
							$this->set('message','Hash error');
					}

				} else {
						$this->set('message','Input params error');
				}
			} else {
				// Формируем пустую страницу
			}

		} // RBK Money end
		/********************************************************************************************/
		else if ($confirmer == 'yandex')
		{
			//pr($this->params['form']);
			$this->layout = 'yandex';
			if (!empty($this->params['form']) && !empty($paymentParams)) {// удалось считать ini с параметрами
				$yandexMsg = $this->params['form'];

				// Установим параметры для создания ответа Яндексу
				$this->set('action',@$yandexMsg['action']);
				$this->set('shopId',@$yandexMsg['shopId']);
				$this->set('invoiceId',@$yandexMsg['invoiceId']);

				// Сначала получим данные для проверки данных
				$paymentParams = parse_ini_file("../config/payment_params.ini.php", true);
				// 1) Проверка соответсвия запроса нам:
				if ( $paymentParams['yandex']['ShopID'] == @$yandexMsg['shopId'] // ID продавца, т.е. наш, верен
					) {
					// 2) Проверка хэша
					/*
					 * orderIsPaid;
					 * orderSumAmount;
					 * orderSumCurrencyPaycash;
					 * orderSumBankPaycash;
					 * shopId;
					 * invoiceId;
					 * customerNumber
					*/
					$hash  = @$yandexMsg['orderIsPaid'];
					$hash .= ";".@$yandexMsg['orderSumAmount'];
					$hash .= ";".@$yandexMsg['orderSumCurrencyPaycash'];
					$hash .= ";".@$yandexMsg['orderSumBankPaycash'];
					$hash .= ";".@$yandexMsg['shopId'];
					$hash .= ";".@$yandexMsg['invoiceId'];
					$hash .= ";".@$yandexMsg['customerNumber'];
					$hash .= ";".@$paymentParams['yandex']['secKey'];

					$hash = strtoupper(md5($hash));
					//pr($hash);

					if ($hash == $yandexMsg['md5']) { // Хэш верен
						// 3) Проверить соответсвие заказа запросу
						$this->Order->id = $yandexMsg['customerNumber'];
						$order = $this->Order->read();
						if (!empty($order)) {
							if ($order['Order']['sumToPay'] == $yandexMsg['orderSumAmount']) { // Сумма верна
								// Все, теперь формируем записи в базу и сохраняем статус заказа
								$id = $yandexMsg['customerNumber'];
								if (	( $yandexMsg['action'] === 'check'
											    or
											  $yandexMsg['action'] === 'PaymentSuccess'
											 )  and
											  $order['Order']['payed'] == 1
										)	// Если приходят уведомления, но заказ оплачен, надо сообщить об этом Яндексу
								{
									$this->set('errno',0);
									$this->render('confirm_yandex');
									return true;
								} elseif ($yandexMsg['action'] === 'check') { // Запрос от Яндекса на проверку корректности заказа
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Подвержден через систему Яндекс.Деньги, но средства еще не поступили :<br/>' .
									'Счёт Yandex #'.$yandexMsg['invoiceId'].' зарегистрирован '.$yandexMsg['orderCreatedDatetime'];
								} elseif ( $yandexMsg['action'] === 'PaymentSuccess'
										     and
										  $yandexMsg['orderIsPaid'] == 1
										 ) { // Уведомление об оплате

									$order['Order']['sumPayed'] = $yandexMsg['orderSumAmount'];
									$order['Order']['sumGot'] = $yandexMsg['shopSumAmount'];
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Оплачен через Яндекс.Деньги:<br/>' .
									'Платёж Yandex #'.$yandexMsg['invoiceId'].' осуществлён '.$yandexMsg['paymentDateTime'];
									$order['Order']['payed'] = 1;
									$order['Order']['payedBy'] = 'yandex';
								} else {
									$this->set('errno',200);
								}

							} else {
								$this->set('message','Оплачиваемая сумма отличается от сформированной в заказе. (Wrong sum)');

								if ($yandexMsg['action'] === 'check') { // Запрос от Яндекса на проверку корректности заказа
									$this->set('errno',100);
								} else {
									$this->set('errno',200);
								}

							}

						} else {
								$this->set('message','Такого заказа не существует. (Wrong order)');
								if ($yandexMsg['action'] === 'check') { // Запрос от Яндекса на проверку корректности заказа
									$this->set('errno',100);
								} else {
									$this->set('errno',200);
								}
						}

					} // хэш
					else
					{
							$this->set('message','Неверный хэш (Wrong hash)');
							$this->set('errno',1);
					}

				} else {
						$this->set('message','Платёж адресован не нам (Wrong ShopID)');
						$this->set('errno',200);
				}
			} else {
					$this->set('message','Ошибка входных данных (Wrong data)');
					$this->set('errno',200);
			}

		} // Yandex money end
		/********************************************************************************************/
		/********************************************************************************************/
		else if ($confirmer == 'yamoney')
		{ // (напрямую на кошелёк)
			//pr($this->params['form']);

			$this->layout = 'ajax';

			if (!empty($this->params['form']) && !empty($paymentParams)) {// удалось считать ini с параметрами
				$yandexMsg = $this->params['form'];

				// Сначала получим данные для проверки данных
				$paymentParams = parse_ini_file("../config/payment_params.ini.php", true);

					// 1) Проверка хэша
					/*
						notification_type	string	Тип уведомления. Фиксированное значение p2p-incoming.
						operation_id	string	Идентификатор операции в истории счета получателя.
						amount	amount	Сумма операции.
						currency	string	Код валюты счета пользователя. Всегда 643 (рубль РФ согласно ISO 4217).
						datetime	datetime	Дата и время совершения перевода.
						sender	string	Номер счета отправителя перевода.
						codepro	boolean	Перевод защищен кодом протекции.
						label	string	Метка платежа. Если метки у платежа нет, параметр содержит пустую строку.
						sha1_hash	string	SHA-1 hash параметров уведомления.
						test_notification	boolean	Флаг означает, что уведомление тестовое. По умолчанию параметр отсутствует.

						notification_type&operation_id&amount&currency&datetime&sender&codepro&notification_secret&label
					*/
					$hash  = @$yandexMsg['notification_type'];
					$hash .= "&".@$yandexMsg['operation_id'];
					$hash .= "&".@$yandexMsg['amount'];
					$hash .= "&".@$yandexMsg['currency'];
					$hash .= "&".@$yandexMsg['datetime'];
					$hash .= "&".@$yandexMsg['sender'];
					$hash .= "&".@$yandexMsg['codepro'];
					$hash .= "&".@$paymentParams['yamoney']['secKey'];
					$hash .= "&".@$yandexMsg['label'];

					$hash = sha1($hash);
					//pr($hash);

					if ($hash == $yandexMsg['sha1_hash']) { // Хэш верен
						// 2) Проверить соответсвие заказа запросу
						$this->Order->id = $yandexMsg['label'];
						$order = $this->Order->read();
						if (!empty($order)) {
							// Сумма верна: больше или равна сумме счёта
							if (round(floatval($order['Order']['sumToPay'])*0.995, 2) <= floatval($yandexMsg['amount'])) {
								// Все, теперь формируем записи в базу и сохраняем статус заказа
								$id = $yandexMsg['label'];
								if ( $order['Order']['payed'] == 1 )	// Если приходят уведомления, но заказ оплачен, надо сообщить об этом Яндексу
								{
									$this->set('status','OK');
									return true;
								} elseif ($yandexMsg['test_notification'] === 'true') { // Уведомление об оплате

									$order['Order']['sumPayed'] = $yandexMsg['amount'];
									$order['Order']['sumGot'] = 0;
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Виртуально оплачен через Яндекс.Деньги:<br/>' .
									'Тестовый платёж Yandex #'.$yandexMsg['operation_id'].
									'<br/>осуществлён '.date('H:m:s d.m.Y', strtotime($yandexMsg['datetime'])).
									'<br/>с кошелька '.$yandexMsg['sender'];
									$order['Order']['payed'] = 0;
									$order['Order']['payedBy'] = 'yandex';

									$this->set('status','OK');
									return true;
								} else { // Уведомление об оплате

									$order['Order']['sumPayed'] = round(floatval($yandexMsg['amount'])/0.995, 2);
									$order['Order']['sumGot'] = $yandexMsg['amount'];
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Оплачен через Яндекс.Деньги:<br/>' .
									'Платёж Yandex #'.$yandexMsg['operation_id'].
									'<br/>осуществлён '.date('H:m:s d.m.Y', strtotime($yandexMsg['datetime'])).
									'<br/>с кошелька '.$yandexMsg['sender'];
									$order['Order']['payed'] = 1;
									$order['Order']['payedBy'] = 'yandex';
								}
							} else {
								$this->set('message','Оплачиваемая сумма отличается от сформированной в заказе. (Wrong sum)');
								$this->header('HTTP/1.1 412 Precondition Failed');
								return false;

							}

						} else {
								$this->set('message','Такого заказа не существует. (Wrong order)');
								$this->header('HTTP/1.1 412 Precondition Failed');
								return false;
						}

					} // хэш
					else
					{
							$this->set('message','Неверный хэш (Wrong hash)');
							$this->header('HTTP/1.1 412 Precondition Failed');
							return false;
					}

			} else {
					$this->set('message','Ошибка входных данных (Wrong data)');
					$this->header('HTTP/1.1 412 Precondition Failed');
					return false;
			}

		} // Yandex money (напрямую на кошелёк) end
		/********************************************************************************************/
		else if ($confirmer == 'webmoney')
		{
			$this->layout = 'ajax';
			if (!empty($this->params['form']) && !empty($paymentParams)) {// удалось считать ini с параметрами
				$wmMsg = $this->params['form'];
				// 1) Проверка адресованы ли деньги нам
				if ($paymentParams['wm']['wallet'] == $wmMsg['LMI_MERCHANT_ID']) {
					// 2) Проверить соответсвие заказа запросу
						$this->Order->id = $wmMsg['LMI_PAYMENT_NO'];
						$order = $this->Order->read();
						if (!empty($order)) {
							if ($order['Order']['sumToPay'] == $wmMsg['LMI_PAYMENT_AMOUNT']) { // Сумма верна
								// 3) Проверка на тип запроса - предварительный или итоговый
								$wmPaymentSystems[2]  = "WebMoney Check";
								$wmPaymentSystems[3]  = "WebMoney Test";
								$wmPaymentSystems[8]  = "Альфа-банк";
								$wmPaymentSystems[24] = "Русский Стандарт Банк";
								$wmPaymentSystems[31] = 'WebMoney';

								if (!empty($wmPaymentSystems[$wmMsg['LMI_PAYMENT_SYSTEM']])) {
									$paySysName = $wmPaymentSystems[$wmMsg['LMI_PAYMENT_SYSTEM']];
								} else {
									$paySysName = '#'.$wmMsg['LMI_PAYMENT_SYSTEM'];
								}

								if ($order['Order']['payed'] == 1) { // Заказ может быть уже оплачен
									$this->set('status','YES');
									$this->render();
									return true;
								} elseif (!empty($wmMsg['LMI_PREREQUEST']) && $wmMsg['LMI_PREREQUEST'] == 1) {
									// Отвечаем на предварительный запрос
									$id = $wmMsg['LMI_PAYMENT_NO'];
									$order['Order']['description'] = @$order['Order']['description'].
								    '<br/>'.
									'Подвержден через систему Webmoney (PayMaster), но средства еще не поступили :<br/>' .
									'Платежная система '.$paySysName;
								} else {
									// Оповещение о платеже
									// 4) Проверка хэша

									/*
									 * Старый хэш
									$hash  = $wmMsg['LMI_PAYEE_PURSE'];
									$hash .= $wmMsg['LMI_PAYMENT_AMOUNT'];
									$hash .= $wmMsg['LMI_PAYMENT_NO'];
									$hash .= $wmMsg['LMI_MODE'];
									$hash .= $wmMsg['LMI_SYS_INVS_NO'];
									$hash .= $wmMsg['LMI_SYS_TRANS_NO'];
									$hash .= $wmMsg['LMI_SYS_TRANS_DATE'];
									$hash .= $paymentParams['wm']['secKey'];
									$hash .= $wmMsg['LMI_PAYER_PURSE'];
									$hash .= $wmMsg['LMI_PAYER_WM'];
									*/

									/* Новый хэш
									 *
									 * LMI_MERCHANT_ID - Наш ID
									 * LMI_PAYMENT_NO,
									 * LMI_SYS_PAYMENT_ID,
									 * LMI_SYS_PAYMENT_DATE,
									 * LMI_PAYMENT_AMOUNT,
									 * LMI_CURRENCY,
									 * LMI_PAID_AMOUNT,
									 * LMI_PAID_CURRENCY,
									 * LMI_PAYMENT_SYSTEM,
									 * LMI_SIM_MODE
									 * SEC KEY
									*/

									$hash  = $wmMsg['LMI_MERCHANT_ID'].';';
									$hash .= $wmMsg['LMI_PAYMENT_NO'].';';
									$hash .= $wmMsg['LMI_SYS_PAYMENT_ID'].';';
									$hash .= $wmMsg['LMI_SYS_PAYMENT_DATE'].';';
									$hash .= $wmMsg['LMI_PAYMENT_AMOUNT'].';';
									$hash .= $wmMsg['LMI_CURRENCY'].';';
									$hash .= $wmMsg['LMI_PAID_AMOUNT'].';';
									$hash .= $wmMsg['LMI_PAID_CURRENCY'].';';
									$hash .= $wmMsg['LMI_PAYMENT_SYSTEM'].';';
									$hash .= @$wmMsg['LMI_SIM_MODE'].';';
									$hash .= $paymentParams['wm']['secKey'];

									$hash = base64_encode(md5($hash, true));

									//var_dump($hash);
									if ($hash == $wmMsg['LMI_HASH']) { // Хэш верен
										// Можно подтверждать заказ

										$id = $wmMsg['LMI_PAYMENT_NO'];
										$order['Order']['sumPayed'] = $wmMsg['LMI_PAYMENT_AMOUNT'];
										$order['Order']['sumGot'] = $wmMsg['LMI_PAYMENT_AMOUNT']*(1-($paymentParams['wm']['procent']/100));
										$order['Order']['description'] = @$order['Order']['description'].
								        '<br/>'.
										'Оплачен через WebMoney (PayMaster):<br/>' .
										'Платёж #'.$wmMsg['LMI_SYS_PAYMENT_ID'].' осуществлён ' .
										'через '.$paySysName.'.'.
										"<br/>".'Платеж проведён '.$wmMsg['LMI_SYS_PAYMENT_DATE'].'.';
										$order['Order']['payed'] = 1;
										$order['Order']['payedBy'] = 'webmoney';

									} else {
											$this->set('message','Wrong hash');
											$this->header('HTTP/1.1 412 Precondition Failed');
											return false;
									}

								}

							}
							else // Неверная сумма заказа
							{
								$this->set('message','Неверная сумма заказа, должна быть '.$order['Order']['sumToPay'].' руб.');
							}
					}
					else // 2) // Заказ в БД не найден
					{
						$this->set('message','Wrong order number');
					}
				}
				else // 1)
			    {
					$this->set('message','Wrong wallet');
				}

			}
		} // webmoney money end
		/********************************************************************************************/
		else if ($confirmer == 'qiwi') {

			$this->layout = 'qiwi';
			$this->header('Content-Type:text/xml; charset=UTF-8');
			$soap = new DOMDocument();
			$status = '-1';

			// 1) Если есть входные данные и удалось считать файл с данными платежных систем
			if (@$soap->load('php://input') and !empty($paymentParams)) {

				$s = new WSSESoapServer($soap);
				$xmlAsArray = Xml::toArray(Xml::build($s->saveXML()));

				if (!empty($xmlAsArray['Envelope']['Body']['UpdateBill'])) {
					// Данные получены кооректно и обработаны
					$qiwiReq    = $xmlAsArray['Envelope']['Body']['UpdateBill'];
					$qiwiStatus = $qiwiReq['status'];
					$qiwiHash   = strtoupper(md5($qiwiReq['txn'].strtoupper(md5($paymentParams['qiwi']['password']))));

					// 2) Проверить адресован ли заказ нам (логин и хэш)
					if ( $qiwiReq['login'] == $paymentParams['qiwi']['login']
							and
						 $qiwiReq['password'] == $qiwiHash
					   ) {
						// 3) Проверить соответсвие заказа запросу
						$this->Order->id = $qiwiReq['txn'];
						$order = $this->Order->read();
						if (!empty($order) and $order['Order']['payed'] == 0) {
							$id = $qiwiReq['txn'];

							// В зависимости от статуса счета $qiwiStatus меняем статус заказа в магазине
							if ($qiwiStatus > 0 and $qiwiStatus < 50) {
								// Счёт выставлен, но не оплачен
								$order['Order']['description'] = @$order['Order']['description'].'<br/>'.'Счёт выставлен в системе Qiwi, но еще не оплачен. <br/>Статус: #'.$qiwiStatus;
								$status = 0;
							} elseif ($qiwiStatus >= 50 and $qiwiStatus < 60) {
								// счет в процессе проведения
								$order['Order']['description'] = @$order['Order']['description'].'<br/>'.'Счёт условно оплачен и проводится в системе Qiwi, но подтверждение еще не получено. <br/>Статус: #'.$qiwiStatus;
								$status = 0;
							} elseif ($qiwiStatus == 60) {
								// заказ оплачен
								// TODO: Проверять сумму заказа по запросу через SOAP
								$order['Order']['sumPayed'] = $order['Order']['sumToPay'];
								$order['Order']['sumGot'] = $order['Order']['sumToPay']*(1-($paymentParams['qiwi']['procent']/100));
								$order['Order']['description'] = @$order['Order']['description'].
						        '<br/>'.
								'Оплачен через Qiwi:<br/>' .
								'Платеж подтверждён '.date('H:i:s d.m.Y', time()).'.';
								$order['Order']['payed'] = 1;
								$order['Order']['payedBy'] = 'qiwi';
							} elseif ($qiwiStatus > 100) {
								/* заказ не оплачен (отменен пользователем, недостаточно средств на балансе и т.п.)

								150 Отменен (ошибка на терминале)
								151 Отменен (ошибка авторизации: недостаточно средств на балансе, отклонен
									абонентом при оплате с лицевого счета оператора сотовой связи и т.п.).
								160 Отменен
								161 Отменен (Истекло время)

								*/
								switch ($qiwiStatus) {
									case 150:
										$qiwiStatusText = 'ошибка на терминале.';
										break;

									case 151:
										$qiwiStatusText = 'ошибка авторизации.';
										break;

									case 161:
										$qiwiStatusText = 'истекло время.';
										break;

									default:
										$qiwiStatusText = '#'.$qiwiStatus;
										break;
								}

								$order['Order']['description'] = @$order['Order']['description'].'<br/>'.'Счёт был выставлен в системе Qiwi, но отменён. <br/>Статус: '.$qiwiStatusText;

								$status = 0;
							} else {
								// неизвестный статус заказа
								$order['Order']['description'] = @$order['Order']['description'].'<br/>'.'Счёт выставлен в системе Qiwi, но еще не оплачен. <br/>Статус: #'.$qiwiStatus;
								$status = 0;
							}

						} elseif (!empty($order) and $order['Order']['payed'] == 1) {
							// Если заказ уже оплачен, просто подтвердить его
							$this->set('status', 0);
							$this->render();
							return true;
						} else {
							$status = 210; // Счёт не найден
						}

					} else {
						$status = 150; // Ошибка авторизации
					}

				} else {
					$status = '-1';
				}

			} else {
				$status = '-1';
			}

			$this->set('status', $status);

		} // Qiwi end
		/********************************************************************************************/
		else
		{
			$this->layout = 'ajax';
			$this->header('HTTP/1.1 404 Not found');
			return false;
		}
		// сохраняем статус Оплачено

		if (@$id) {

			$this->orderTransaction($order, $confirmer);

		}

		if ($confirmer === 'yandex') {
			$this->render('confirm_yandex');
		} elseif ($confirmer === 'admin') {
			$this->redirect($this->referer());
		}

	}

	// Проводим заказ
	protected function orderTransaction($order = array(), $confirmer = null) {

			if (!empty($order)) {

				//$this->loadModel('OrderClean');
				//$this->loadModel('ServerRootserver');

				$id = $order['Order']['id'];

				$order['Order']['payedDate'] = date('Y-m-d H:i:s', time());

				// Если к заказу привязан сервер, то ножно проверять
				// вводимые данные - сумму и кол-во месяцев
				if (!empty($order['Server'])) {
					$order['Server']['id'] = $order['Server'][0]['id'];
					$validate = true;
				}
				// Иначе не нужно. Все проверки по сумме сделаны ранее, а также при платеже
				else
				{
					$validate = false;

					if (@$order['Order']['payed'] == 1 and $confirmer === 'admin') {
						$order['Order']['sumPayed'] = $order['Order']['sum'];
						// Указать примерную сумму зачисления
						// TODO: Указывать реальную сумму зачисления в форме подтверждения админом.
						$order['Order']['sumGot'] = round($order['Order']['sum']*0.96, 2);
					}

				}

				if ($this->Order->save($order, $validate))
				{
					if ($order['Order']['payed'] == 1)
					{ // Если заказ оплачен
						// Внести оплаченную сумму на счёт -> $order['Order']['sumPayed']
						if ($order['Order']['payedBy'] === 'manual')
						{
							if (!empty($order['Server'])) {
								$billDescPut = 'Фиктивное пополнение.';
								$order['Order']['sumGot'] = 0;
							} else {
								$billDescPut = 'Средства зачислены вручную.';
								$order['Order']['sumGot'] = $order['Order']['sum'];
							}

							$billDescGet = 'Фиктивное снятие.';
							$order['Order']['sumPayed'] = $order['Order']['sum'];

						}
						else
						{
							$billDescPut = NULL;
							$billDescGet = NULL;
						}

						if ($order['Order']['payedBy'] !== 'internal'  // Не пополнять счёт при оплате с Личного счёта
								or empty($order['Server']) // Пополнять, если заказ не привязан к серверу
							)
						{
							if ($this->updateBill(	$order['User']['id'],
													$id,
													$order['Order']['sumPayed'],
													$order['Order']['sumGot'],
													'put',
													$billDescPut,
													$order['Order']['payedBy']
												   )
								)
							{
								// Добавить комментарий к заказу о проведении средств
								if (!empty($order['Server'])) {
									$orderDesc = $order['Order']['description'].
												 '<br/>Заказ проводится в системе.';
								} else {
									$orderDesc = $order['Order']['description'].
												 '<br/>Средства успешно зачислены на счёт.';
								}
							}
							else
							{
								// Добавить комментарий к заказу об ошибке
								$orderDesc = $order['Order']['description'].
											 '<br/>Ошибка при зачислении средств на личный счёт.';
							}

							$this->Order->id = $id;
							$this->Order->saveField('description', $orderDesc); // сохранить описание заказа
						}

					}

					if ($confirmer === 'rbk') {
						$this->set('status','OK');
					} elseif ($confirmer === 'webmoney') {
						$this->set('status','YES');
					} elseif ($confirmer =='yandex') {
						$this->set('errno',0);
					}
				} else {
					$this->set('errno',1000);
					return false;
				}

				if (!empty($order['Server'])) {
					$this->Order->id = $id;
					$order = $this->Order->read();
					$this->ServerRootserver->bindModel(array(
															'hasAndBelongsToMany' => array(
																				'Type' => array(
																					'fields' => 'id'),
																				'GameTemplate' => array(
																					'fields' => 'id'),
																				'Service' => array(
																					'fields' => 'id')
																						),
																				));

					$this->ServerRootserver->id = $order['Server'][0]['id'];
					$server = $this->ServerRootserver->read();

					if ($order['Order']['payed'] == 1) {
						// Прибавляем количество оплаченных месяцев и два часа (запас на инициализацию)

						// Если поле оплаты заполнено и текущее UNIX-время
						// меньше этой даты - отталкиваться от него
						if (!empty($server['ServerRootserver']['payedTill'])
								and
							time() < strtotime($server['ServerRootserver']['payedTill'])) {
							$payedTill = strtotime($server['ServerRootserver']['payedTill']);
							$server['ServerRootserver']['payedTill'] = date('Y-m-d H:i:s', mktime(date('H', $payedTill) + 2,
																								  date('i', $payedTill),
																								  0,
																								  date('m', $payedTill) + $order['Order']['month'],
																								  date('d', $payedTill),
																								  date('Y', $payedTill)));
						} else {
							$server['ServerRootserver']['payedTill'] = date('Y-m-d H:i:s', mktime(date('H') + 2,
																								  date('i'),
																								  0,
																								  date('m') + $order['Order']['month'],
																								  date('d'),
																								  date('Y')));
						}

						$server['GameTemplate']['id'] = $server['GameTemplate'][0]['id'];

						// Если к серверу еще не привязан IP,
						if ($server['Type'][0]['id'] != 8) // и это не EAC
						{
							if (empty($server['ServerRootserver']['address'])) {
								// Выбор локации
								if (empty($server['Location'][0]['id'])) {
									$server['Location']['id'] = 2;
								} else {
									$server['Location']['id'] = $server['Location'][0]['id'];
								}
								$this->ServerRootserver->Location->bindModel(array(
															'hasAndBelongsToMany' => array(
																				'RootServer' => array(
																								'group'=>array('name'),
																								'order'=>array('(slotsMax - slotsBought) DESC'),
																								'limit'=>100
																									 )
																				)));

								$this->ServerRootserver->Location->id = $server['Location']['id'];
								$rootServers = $this->ServerRootserver->Location->read();

								// Если есть услуги, составим массив из их ID
								if (!empty($server['Service'])) {
									foreach ( $server['Service'] as $service ) {
			       						$servicesIds[] = $service['id'];
									}
								}
								if (!empty($servicesIds) && !empty($rootServers['RootServer'])) {

									// Для услуги "Красивый порт"
									if (in_array('1', $servicesIds)) {
										foreach ( $rootServers['RootServer'] as $rootServer ) {

				       						$avaliableIps = $this->requestAction(array('controller' => 'Services',
																				'action'=>'checkServiceNoPort'),
																			     array('pass' => array(
																			  						    $rootServer['id'],
																			  						    $server['GameTemplate'][0]['id']
																			  						   )
																			  		   )
																			  	 );
											if (@$avaliableIps) {
												$server['RootServer']['id'] = $rootServer['id'];
												$server['ServerRootserver']['address'] = current($avaliableIps);
												$server['ServerRootserver']['port'] = '27015';
											}
											break;
										}
									}
									else
									// Для услуги "Выделенный IP"
									if (in_array('2', $servicesIds)) {
										foreach ( $rootServers['RootServer'] as $rootServer ) {
				       						$avaliableIps = $this->requestAction(array('controller' => 'Services',
																				'action'=>'checkServiceDedicatedIp'),
																			     array('pass' => array(
																			  						    $rootServer['id'],
																			  						    $server['GameTemplate'][0]['id']
																			  						   )
																			  		   )
																			  	 );
											if (@$avaliableIps) {
												$server['RootServer']['id'] = $rootServer['id'];
												$server['ServerRootserver']['address'] = current($avaliableIps);
												$server['ServerRootserver']['port'] = '27015';
											}
											break;
										}
									}
									else
									// Для услуги "Смена игры сервера" и других - доступна на любом сервере
									{
										$server['RootServer']['id'] = $rootServers['RootServer'][0]['id'];
									}

								} else {
									$server['RootServer']['id'] = $rootServers['RootServer'][0]['id'];
								}

								// Выбираем физический сервер
			//					$rootServer = $this->RootServer->find('first', array(
			//															'group'=>array('name'),
			//															'order'=>array('MAX(slotsMax - slotsBought) DESC'),
			//															'limit'=>1
			//															));

							} else {
								$server['RootServer']['id'] = $server['RootServer'][0]['id'];
							}
						} else {
							$server['ServerRootserver']['initialised'] = 1; // Для EAC сразу прописать ключ инициализации
						}
						// Вносим в базу
						// Предварительно проверить, что на счету достаточно средств

						$billAmount = $this->checkBill($order['User']['id']);

						if ($billAmount >= $order['Order']['sum']) {
							// Снять сумму заказа с лицевого счета
							if ($this->updateBill(	$order['User']['id'],
													$id,
													$order['Order']['sum'],
													NULL,
													'get',
													$billDescGet,
													'internal'
												 )
								) {
								// Добавить комментарий к заказу о проведении средств
								$orderDesc = $order['Order']['description'].
											 '<br/>Заказ успешно проведен.';

								$this->Order->id = $id;
								$this->Order->saveField('description', $orderDesc);

								if ($this->ServerRootserver->save($server)) {

									$this->Session->setFlash('На заказ №'.$id.' Установлен статус "Оплачен"', 'flash_success');

									if ($confirmer === 'rbk') {
										$this->set('status','OK');
									} elseif ($confirmer === 'webmoney') {
										$this->set('status','YES');
									} elseif ($confirmer =='yandex') {
									$this->set('errno',0);
									}

									unset($order);
									unset($server);

									return true;

								} else {
									$this->Session->setFlash('Возникла ошибка при сохранении статуса заказа №'.$id.':'.mysql_error(), 'flash_error');
									return false;
								}

							} else {
								// Добавить комментарий к заказу об ошибке
								$orderDesc = $order['Order']['description'].
											 '<br/>Ошибка при снятии средств с лицевого счёта.';

								$this->Order->id = $id;
								$this->Order->saveField('description', $orderDesc);
								return false;
							}

						} else {
							// Добавить комментарий к заказу о недостаточности средств
							$orderDesc = $order['Order']['description'].
										 '<br/>Недостаточно средств на Личном счёте для проведения заказа.'.
										 '<br/>Требуется: '.$order['Order']['sum'].
										 '<br/>Имеется: '.$billAmount;

							$this->Order->id = $id;
							$this->Order->saveField('description', $orderDesc);
							return false;
						}
					}
				} /* Сохранение параметров сервера*/

			} else {
				return false;
			}
	}

	// Отмена заказа
	public function cancel($id = null) {
		$this->Order->id = $id;
		$order = $this->Order->read();

		if ($order['Order']['payed'] == 1) {
			$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

			if ($this->Order->delete($id)) {
				$this->Session->setFlash('Заказ #'.$id.' отменён.', 'flash_success');

				if ($order['Order']['payedBy'] === 'manual') {
					$this->loadModel('Bill');
					$this->Bill->deleteAll(array('order_id' => $id));
					$this->checkBill($order['User']['id']);
				}
			} else {
				$this->Session->setFlash('Возникла ошибка при удалении заказа #'.$id.':'.mysql_error(), 'flash_error');
			}
		} elseif ( $this->checkRights($id) and $order['Order']['payed'] == 0 ) {

			if ($this->Order->delete($id)) {

				if (!empty($order['Server'])) {
					$this->loadModel('ServerComp');
					$serverId = @$order['Server'][0]['id'];

					if (@$order['Server'][0]['initialised'] == 0) {

						if ($this->ServerComp->delete($serverId)) {
							$this->Session->setFlash('Ваш заказ №'.$id.' отменён. Сервер #'.$serverId.' удалён.', 'flash_success');
						} else {
							$this->Session->setFlash('Возникла ошибка при отмене заказа #'.$id.':'.mysql_error(), 'flash_error');
						}
					} else {
						$this->Session->setFlash('Ваш заказ №'.$id.' отменён. Сервер затронут не был.', 'flash_success');
					}
				} else {
					$this->Session->setFlash('Ваш заказ №'.$id.' отменён.', 'flash_success');
				}

				if ($order['Order']['payedBy'] === 'manual') {
					$this->loadModel('Bill');
					$this->Bill->deleteAll(array('order_id' => $id));
					$this->checkBill($order['User']['id']);
				}

			} else {
				$this->Session->setFlash('Возникла ошибка при отмене заказа #'.$id.':'.mysql_error(), 'flash_error');
			}

		}

		$this->redirect($this->referer());
	}

	// Очистка неоплаченных заказов, пока жестко старше двух недель
	public function clearExpired() {
		$this->DarkAuth->requiresAuth(array('Admin'));

		$dateOffset = date('Y-m-d H:i:s', mktime( date('H'),
												  date('i'),
												  0,
												  date('m'),
												  date('d') - 14,
												  date('Y')));

		$orders = $this->Order->find('all', array( 'conditions' => array( 'Order.payed' => 0,
																		  'Order.created <' => $dateOffset) ));

		// Пройтись по полученным данным и создать список привязанных НЕИНИЦИЛИЗИРОВАННЫХ серверов

		$serversList = array();
		$ordersList = array();
		foreach ($orders as $key => $order) {
			if (@$order['Server'][0]['initialised'] == 0 and !empty($order['Server'][0]['id'])) {
				$serversList[] = $order['Server'][0]['id'];
			}

			$ordersList[] = $order['Order']['id'];
		}

		// Теперь удалить заказы
		$error = '';

		if (!$this->Order->deleteAll(array( 'Order.id' => $ordersList), false)) {
			$error = '<br/>Не удалось удалить заказы: '.mysql_error();
		}

		// Теперь удалить серверы
		if (!$this->Order->Server->deleteAll(array( 'Server.id' => $serversList), false)) {
			$error = '<br/>Не удалось удалить серверы: '.mysql_error();
		}

		if ($error !== '') {
			$this->Session->setFlash('Возникли ошибки при отмене заказов:<br/>'.$error, 'flash_error');
		} else {
			$this->Session->setFlash('Очистка проведена успешно. <br/>Удалено заказов: '.count($ordersList).'<br/>Удалено серверов: '.count($serversList), 'flash_success');
		}

		$this->redirect($this->referer());
	}

	// Подробности заказа
	public function detail($orderId = null) {

		if ($this->checkRights(@$orderId)) {
			$this->Order->id = $orderId;
			$order = $this->Order->read();

			$this->Order->Server->unbindModel(array(
												'hasAndBelongsToMany' => array(
																	'Plugin',
																	'VoiceMumbleParam',
																	'RadioShoutcastParam',
																	'User'
														)));

			$this->set('order', $order);

			if (!empty($order['Server'])) {
				$this->Order->Server->id = @$order['Server'][0]['id'];
				$this->set('server', $this->Order->Server->read());
			}

		}
	}

	public function balanceEmpty($userId = null) {
		$this->layout = 'ajax';
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

		if (intval($userId) > 0) {
			$this->loadModel('User');
			// Не нужно запрашивать лишнее
			$this->User->unbindModel(array(
											'hasAndBelongsToMany' => array(
																'Server',
																'SupportTicket',
																'Group'
													),
											'hasMany' => array('Eac')
											)
									);
			$this->User->id = $userId;
			$user = $this->User->read(array('id', 'money'));
			if (!empty($user['User'])) {
				$this->loadModel('Bill');

				$moneyLeft = $this->checkBill($userId); // Пересчитать баланс клиента;

				if ($moneyLeft > 0) {
					$bill['Bill']['user_id'] = $userId;
					$bill['Bill']['sumPlus'] = 0;
					$bill['Bill']['sumPlusReal'] = 0;
					$bill['Bill']['sumMinus'] = $moneyLeft;
					$bill['Bill']['payedBy'] = 'internal';
					$bill['Bill']['desc'] = 'Возврат остатка клиенту или обнуление счёта.';

					if ($this->Bill->save($bill)) {
						$this->checkBill($userId); // Пересчитать баланс клиента;
						$this->Session->setFlash('Баланс клиента обнулён успешно.', 'flash_success');
					} else {
						$this->Session->setFlash('Возникла ошибка при изменении баланса!', 'flash_error');
					}
				}
			}
		} else {
			$this->Session->setFlash('Некорректный User ID!', 'flash_error');
		}

		$this->redirect( array('controller' => 'Users', 'action' => 'result') );
	}
}
?>
