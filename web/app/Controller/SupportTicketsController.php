<?php
/*

Tickets controller.
Main logic to manage support tickets.
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
App::uses('CakeEmail', 'Network/Email');

class SupportTicketsController extends AppController {

	public $name = 'SupportTickets';
	public $layout = 'client';
	public $_DarkAuth;
	public $helpers = array('Time', 'Js', 'Text', 'Common');
	public $components = array('RequestHandler', 'Session', 'DarkAuth');
	public $isAdmin = false;

	public function checkRights($ticketId = null) {

	 	  /*
		  * Шаблон проверки принадлежности тикета пользователю
		  * Сначала получаем из сессии ID пользователя и группы.
		  * Потом сверяем этиже ID из данных тикета.
		  */

		 $sessionUser = $this->DarkAuth->getAllUserInfo();
		 $sessionUserId = $sessionUser['User']['id'];
		 $sessionUserGroup = $sessionUser['Group'][0]['id'];

		 $this->SupportTicket->id = $ticketId;
		 $ticket = $this->SupportTicket->read();

		 if ($ticket) {
		 	// Проверим  - владееет ли пользователь сессии этим сервером?
		 	if (  $ticket['User'][0]['id'] == $sessionUserId // Да, владеет
		 			||
		 		  in_array($sessionUserGroup, array(1,2)) // Это администратор
		 		) {

	 				if (in_array($sessionUserGroup, array(1,2))) {
	 					$this->isAdmin = true;
	 					return true;
	 				} else {
	 					$this->isAdmin = false;
	 					return true;
	 				}

	 			}
			 	else // Нет, не владеет
			 	{
			 		$this->Session->setFlash('Вы пытаетесь прочесть чужой тикет. Ай-ай-ай!.', 'flash_error');
			 		return false;
			 	}

		 } else {
		 		$this->Session->setFlash('Тикета не существует.', 'flash_error');
		 		return false;
		 }

	}

	public function beforeRender() {
		$this->SupportTicket->User->unbindModel(array(
											'hasAndBelongsToMany' => array(
																'Server',
																'SupportTicket'
													)));

		$this->SupportTicket->User->id = $this->DarkAuth->getUserId();

		$userInfo = $this->SupportTicket->User->read();

		if (!empty($userInfo)) {
			// Убрать все теги, xss-уязвимость
			foreach ( $userInfo['User'] as $key => $value ) {
	   				$userInfo['User'][$key] = strip_tags($value);
			}
		}
		$this->set('userinfo', $userInfo);

		Cache::set(array('duration' => '+1 days'));

		if (($helpers = Cache::read('helpers')) === false) {

			$this->loadModel('Help');
			$helpers = $this->Help->find('list');

			Cache::set(array('duration' => '+1 days'));
			Cache::write('helpers', $helpers);
		}

		$this->set('helpers', $helpers);
	}

	public function index() {
		$this->set('title_for_layout', 'Техническая поддержка');
		//$this->SupportTicket->recursive = 1;
		$user = $this->DarkAuth->getAllUserInfo();
		$this->set('supportTickets', $user['SupportTicket']);
	}

	public function control() {
		$permitTo = array('Admin','GameAdmin');
		$this->DarkAuth->requiresAuth($permitTo);

		$tickets = array();
		if (!empty($this->data['User']['username'])) {
			// TODO: Сохранять имя клиента в сессию.

			$this->loadModel('User');
			// Не нужно запрашивать лишнее
			$this->User->unbindModel(array(
											'hasAndBelongsToMany' => array(
																'Server',
																'Group'
													),
											'hasMany' => array('Eac')
											)
									);
			$user = $this->User->findByUsername($this->data['User']['username']);

			$tickets = array();

			if (!empty($user['SupportTicket'])) {
				foreach ($user['SupportTicket'] as $ticket) {
					$tickets[] = $ticket['id'];
				}
			}
		}

		$this->SupportTicket->unbindModel(array(
												'hasAndBelongsToMany' => array(
																				'Support'
																	)));

		if (!empty($tickets)) {
			$this->paginate = array('limit' => 50,
								'order' => ' status DESC, created DESC',
								'conditions' => array('id' => $tickets));
		} else {
			$this->paginate = array('limit' => 50, 'order' => ' status DESC, created DESC', );
		}

		$this->set('supportTickets', $this->paginate());

		// Получить состояние ответа на тикеты и количество открытых
		$this->SupportTicket->unbindModel(array(
												'hasAndBelongsToMany' => array(
																				'User',
																				'Server'
																	)));
		$this->SupportTicket->bindModel(array(
											'hasAndBelongsToMany' => array(
																'Support' => array( 'joinTable' => 'supports_support_tickets',
																					'fields' => 'readstatus,answerBy,created',
																					'order' => 'created DESC',
																					'conditions' => array('readstatus' => 'unread')
																					)
													)));

		if (!empty($tickets)) {
			$openedTickets = $this->SupportTicket->find('all', array('conditions' => array('status'=>'open',
																						   'id' => $tickets),
																	 'order' => ' status DESC, created DESC'));

		} else {

			$openedTickets = $this->SupportTicket->find('all', array('conditions' => array('status'=>'open'),
																			 'order' => ' status DESC, created DESC'));
		}

		$this->set('openTickets', count($openedTickets));

		// Составить массив из ID тикета и последненго ответившего
		$ticketStates = array();

		foreach ($openedTickets as $ticket) {
			$ticketStates[$ticket['SupportTicket']['id']] = $ticket['Support'][0];
		}

		$this->set('ticketStates', $ticketStates);

	}

	// Просмотр списка открытых тикетов со смартфона
	public function admPda() {
		$this->layout = 'simple';
		$permitTo = array('Admin','GameAdmin');
		$this->DarkAuth->requiresAuth($permitTo);
		$this->loadModel('ServerClean');
		$this->ServerClean->bindModel(array(
											'hasAndBelongsToMany' => array(
																'GameTemplate' => array('fields'=>'name,longname')
													)));
		$this->SupportTicket->unbindModel(array(
												'hasAndBelongsToMany' => array(
																				'Support'
																	)));
		$tickets = $this->SupportTicket->find('all', array('conditions' => array('status'=>'open'),
																			 'order' => ' status DESC, created DESC'));
		foreach ( $tickets as $ticket ) {
       		if (!empty($ticket['Server'][0]['id'])) {
       			$this->ServerClean->id = $ticket['Server'][0]['id'];
       			$server = $this->ServerClean->read();
       			$ticket['GameTemplate'] = $server['GameTemplate'];
       		}
       		$supportTickets[] = $ticket;
		}
		$this->set('supportTickets', $supportTickets);
	}

	public function viewPda($id = null) {
		$this->layout = 'simple';
		$permitTo = array('Admin','GameAdmin');
		$this->DarkAuth->requiresAuth($permitTo);
		if (empty($this->data)) {
			$this->SupportTicket->bindModel(array(
											'hasAndBelongsToMany' => array(
																'Support' => array( 'joinTable' => 'supports_support_tickets',
																					'limit'=>'5',
																				    'order' => 'created DESC')
													)));
			$this->SupportTicket->id = $id;
			$ticket = $this->SupportTicket->read();
			// Для удобства надо отсортировать в обратном порядке - последний ответ снизу
			asort($ticket['Support']);
			$this->set('ticket', $ticket);

		}
	}

	/*
	 * Изначально загружать только непрочитанные сообщения.
	 * Если пользователь захочет, то по запросу загрузить все.
	 */
	public function view($id = null, $type = 'unread') {

		if ($this->checkRights($id)) {

			if ($type === 'unread') {
				$this->loadModel('SupportTicketFiveLast');
				$ticket = $this->SupportTicketFiveLast->read(null, $id);
				asort($ticket['Support']);
				$ticketStatus = $ticket['SupportTicketFiveLast']['status'];
				$comment = @$ticket['SupportTicketFiveLast']['internal_comment'];
			} elseif ($type === 'all') {
				$ticket = $this->SupportTicket->read(null, $id);
				$ticketStatus = $ticket['SupportTicket']['status'];
				$comment = @$ticket['SupportTicket']['internal_comment'];
			}

			$thread = $ticket['Support'];
			$this->set('thread', $thread);
			$this->set('id', $id);
			$this->set('ticketStatus', $ticketStatus);

			if ($this->isAdmin === true) {
				$this->set('int_comments', @json_decode($comment, true));

				// Получить список админов
				Cache::set(array('duration' => '+1 days'));

				if (($admins = Cache::read('admins')) === false) {

					$this->loadModel('Group');

					$this->Group->bindModel(array(
												'hasAndBelongsToMany' => array(
																	'User' => array( 'joinTable' => 'groups_users',
																						'fields' => 'id,username,supporter_spec',
																						'order' => 'username DESC',
																						'conditions' => array('live' => '1')
																						)
														)));

					$groups = $this->Group->find('all', array('conditions' => array('id' =>  array(1,2,6))));

					$admins = array();

					foreach ($groups as $group) {
						foreach ($group['User'] as $admin) {
							$admins[] = array('id' => $admin['id'],
											  'name' => $admin['username'],
											  'spec' => $admin['supporter_spec']);
						}
					}

					Cache::set(array('duration' => '+1 days'));
					Cache::write('admins', $admins);
				}

				$this->set('admins', $admins);

			}

		}
	}

	public function add() {
		$this->loadModel('ServerTemplate');
		$this->loadModel('Support');
		$this->loadModel('UserServersId');
		$user = $this->DarkAuth->getAllUserInfo();
		$userId = $user['User']['id'];
		$this->UserServersId->id = $userId;
		$serversIdBase = $this->UserServersId->read();

		// Составить массив из ID серверов
		 if (!empty($serversIdBase['Server'])) {
			 foreach ( $serversIdBase['Server'] as $server ) {
			 	$t[] = $server['id'];
			 }
			$serversId = $t;
		 }

		 if (!empty($serversId)) {
			 // Запрос списка серверов с шаблонами из полученных ID
			 $serverTemplate = $this->ServerTemplate->find('all', array('conditions' => array('ServerTemplate.id' => $serversId)));
			 // Составить меню из серверов клиента
			 if (!empty($serverTemplate)) {
				 foreach ( $serverTemplate as $server ) {
		       		if (empty($server['ServerTemplate']['name'])) {
		       			$servers[$server['ServerTemplate']['id']] = "#".$server['ServerTemplate']['id']." ".$server['GameTemplate'][0]['longname'];
		       		} else {
		       			$servers[$server['ServerTemplate']['id']] = "#".$server['ServerTemplate']['id']." ".$server['ServerTemplate']['name'];
		       		}
		       		// Составить список серверов для уведомления администратору
		       		if ( !empty($this->data['Server']['Server']) and in_array($server['ServerTemplate']['id'], $this->data['Server']['Server'])) {
		       			$emailServers[] = $server['ServerTemplate'];
		       		}

				 }
			 }
		 }

		if (!empty($this->data)) {

			// Проверка на владение сервером игроком
			$wrongServer = false;
			if (!empty($this->data['Server']) and $this->data['Server']['Server'][0] != 0) {
				foreach ( $this->data['Server']['Server'] as $serverId ) {
					if (!in_array($serverId, $serversId)) {
						$wrongServer = true;
						break;
					}
				}
			}

			if ( $wrongServer == false) {	// Нет в переданном списке чужих серверов

				$this->request->data['Support']['answerBy'] = 'owner';

				if ($this->Support->save($this->data['Support'])) {
					$this->request->data['Support']['id'] = $this->Support->id;
					$this->request->data['User']['id'] = $userId;

					if (empty($this->data['Server']) or $this->data['Server']['Server'][0] == 0) {
						unset($this->request->data['Server']);
					}

					// Убрать всякие теги. Дабы особо умные не пытались передать скрипт техподдержке.
					$this->request->data['Support']['text'] = strip_tags($this->data['Support']['text']);
					if ($this->SupportTicket->save($this->request->data)) {

		    			$emailTicket['id']    = $this->SupportTicket->id;
		    			$emailTicket['title'] = $this->data['SupportTicket']['title'];
		    			$emailTicket['text']  = $this->data['Support']['text'];

		    			try {
						    //генерация e-mail
							$Email = new CakeEmail();
							$Email->config('smtp');
							$Email->viewVars(array('ticket' => $emailTicket,
												   'user' => $user['User'],
												   'servers' => @$emailServers));
							$Email->template('new_ticket_notify', 'default')
		    					  ->emailFormat('text')
		    					  ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
		    					  ->to('support@ghmanager.local')
								  ->subject('Создан тикет #'.$this->SupportTicket->id.": ".$this->data['SupportTicket']['title'])
								  ->send();

						    $this->Session->setFlash('Тикет создан. Ожидайте ответа в ближайшее время.', 'flash_success');

						} catch (Exception $e) {
						    $this->Session->setFlash(sprintf('Тикет создан, но не удалось отправить уведомление администраторам. Ошибка "%s". Тем не менее, если вы не получите ответа в ближайшее время, свяжитесь с техподдержкой напрямую.', $e->getMessage()), 'flash_error');
						}

						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash('Возникла ошибка при сохранении тикета, попробуйте позже или ' .
												 'свяжитесь с техподдержкой по e-mail: support@teamserver.ru','flash_error');
					}
				} else {
					$this->Session->setFlash('Не удалось сохранить сообщение.', 'flash_error');
				}

			} else {
				$this->Session->setFlash('Вы указали чужой сервер.', 'flash_error');
			}
		}

//		foreach ( $servers as $serverId => $serverName ) {
//       		if (empty($serverName)) {
//       			$this->ServerTemplate->id = $serverId;
//       			$serverTemplate = $this->ServerTemplate->read();
//       			$servers[$serverId] = "#".$serverId." ".$serverTemplate['GameTemplate'][0]['longname'];
//       		}
//		}
		if (@$servers) {
			asort(@$servers);
		}

		$servers['0'] = "Проблема не с сервером";
		$this->set(compact('servers'));
	}

	public function closeTicket( $id = null) {
		if ($this->checkRights($id)) {
			$this->SupportTicket->id = $id;

			if ($this->SupportTicket->saveField('status','closed')) {
				$this->loadModel('SupportTicketUnreadId');
				$this->loadModel('Support');
				$this->SupportTicketUnreadId->id = $id;
				$ticket = $this->SupportTicketUnreadId->read();

				 // Создать массив из ID непрочтёных сообщений

				foreach ( $ticket['Support'] as $unreadMessage ) {
	       			$unreadMessagesIds[] = $unreadMessage['id'];
				}

				$this->Support->updateAll(
										    array('readstatus' => "'read'"),
										    array('id' => $unreadMessagesIds)
										);

				$this->Session->setFlash('Тикет закрыт.', 'flash_success');
			} else {
				$this->Session->setFlash('Возникла ошибка при закрытии тикета. Попробуйте позднее.', 'flash_error');
			}

		// Очистить кэш
		Cache::delete('ticketsHeaders');
		$this->redirect($this->referer());

		}
	}

	/* Добавить внутренний комментарий к тикету */
	public function addComment($id = null) {
		$permitTo = array('Admin','GameAdmin','OrdersAdmin');
		$this->DarkAuth->requiresAuth($permitTo);

		if ($id === null and !empty($this->data['SupportTicket']['id'])) {
			$id = $this->data['SupportTicket']['id'];
		}

		if ($id !== null) {
			if (!empty($this->data['SupportTicket']['text'])) {
				// Сначала получить текущий текст
				$this->SupportTicket->id = $id;
				$this->SupportTicket->unbindModel(array(
												'hasAndBelongsToMany' => array(
																				'Support',
																				'Server',
																				'User'
																	)));

				$comment = $this->SupportTicket->findById($id, array('fields' => 'internal_comment'));

				if (!empty($comment['SupportTicket']['internal_comment'])) {
					$comment = json_decode($comment['SupportTicket']['internal_comment'], true);
				} else {
					$comment = array();
				}

				// Получить имя, написавшего коммент
				$userInfo = $this->DarkAuth->getAllUserInfo();

				// Новый комментарий добавить к массиву json
				$comment[] = array('by' => $userInfo['User']['username'],
								   'text' => $this->data['SupportTicket']['text'],
								   'time' => date('Y-m-d H:i:s'));

				if ($this->SupportTicket->saveField('internal_comment', json_encode($comment))) {
					$this->Session->setFlash('Комментарий добавлен.', 'flash_success');
				} else {
					$this->Session->setFlash('Ошибка при добавлении комментария: '.mysql_error(), 'flash_error');
				}
			} else {
				$this->Session->setFlash('Нет текста =(', 'flash_error');
			}

			$this->redirect(array('action' => 'view', $id));
		}

	}

	/* Назначить тикет админу */
	public function linkToAdmin($ticket_id = null, $user_id = null) {
		if ($ticket_id !== null and $user_id !== null) {
			if ($this->checkRights($ticket_id) and $this->isAdmin === true) {
				/*
					TODO: Необходимо сделать проверку user_id пользователя,
					      чтобы тот также принадлежал админам
				*/

				$this->loadModel('UserGroup');
				$username = $this->UserGroup->findById($user_id, array('fields' => 'username'));

				if ($username) {
					if ($this->SupportTicket->saveField('supporter', $username['UserGroup']['username'])) {
						$this->Session->setFlash('Тикет #'.$ticket_id.' назначен.', 'flash_success');
					} else {
						$this->Session->setFlash('Ошибка при назначении тикета админу: '.mysql_error(), 'flash_error');
					}
				}

				$this->redirect(array('action' => 'control'));
			} else {
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

/*
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for support ticket'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SupportTicket->delete($id)) {
			$this->Session->setFlash(__('Support ticket deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Support ticket was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
*/
}
?>
