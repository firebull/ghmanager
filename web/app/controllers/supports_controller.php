<?php
/*

Tickets messages controller.
Manage support tickets messages.
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


class SupportsController extends AppController {

	public $name = 'Supports';
	public $layout = 'client';
	public $_DarkAuth;
	public $helpers = array('Javascript', 'Time', 'Js');
	public $components = array('RequestHandler', 'Session', 'Email');

	function beforeRender() {
		$userInfo = $this->DarkAuth->getAllUserInfo();

		// Убрать все теги, xss-уязвимость
		foreach ( $userInfo['User'] as $key => $value ) {
   				$userInfo['User'][$key] = strip_tags($value);
		}

		$this->set('userinfo', $userInfo);
	}

	function index() {
		$this->Support->recursive = 0;
		$this->set('supports', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid support', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('support', $this->Support->read(null, $id));
	}

	function add( $ticketId = null) {
		/*
		 * Предварительно необходимо пометить все сообщения в тикете, как прочитанные
		 */
		if (!empty($this->data)) {

			if (!$ticketId) {
				$ticketId = $this->data['SupportTicket']['id'];
			}

			$this->loadModel('SupportTicketUnreadId');
			$this->SupportTicketUnreadId->bindModel(array(
									'hasAndBelongsToMany' => array(
																    'Server' => array(),
																    'User'   => array( 'fields' => 'id, username, email' )
																   )));
			$this->SupportTicketUnreadId->id = $ticketId;
			$ticket = $this->SupportTicketUnreadId->read();

			$user = $this->DarkAuth->getAllUserInfo();

			/*
			 * Создать массив из ID непрочтённых сообщений
			 */
			// Сначала проверить права
			if ($ticket['User'][0]['id'] == $user['User']['id'] // Да, владеет
		 			||
		 		$user['Group'][0]['id'] == 1 						// Это администратор
		 			||
		 		$user['Group'][0]['id'] == 2
		 			) {

				foreach ( $ticket['Support'] as $unreadMessage ) {
	       			$unreadMessagesIds[] = $unreadMessage['id'];
				}

				$this->Support->updateAll(
										    array('readstatus' => "'read'"),
										    array('id' => $unreadMessagesIds)
										);
				$this->Support->create();
				$this->data['Support']['readstatus'] = 'unread';

				// Убрать всякие теги. Дабы особо умные не пытались передать скрипт техподдержке.
				$this->data['Support']['text'] = strip_tags($this->data['Support']['text']);

				// Сохранять напрямую $this->data, полученную от клиента, было глупо =)
				$supportTicket = array( 'Support' => $this->data['Support'],
										'SupportTicket' => array( 'id' => $this->data['SupportTicket']['id']));

				if ($ticket['User'][0]['id'] == $user['User']['id']){
					$supportTicket['Support']['answerBy'] = 'owner';
				}
				else
				if (in_array($user['Group'][0]['id'], array(1, 2, 6)))
				{
					$supportTicket['Support']['answerBy'] = 'support';
					$supportTicket['Support']['answerByName'] = $user['User']['username'];
				}
				else
				{
					$supportTicket['Support']['answerBy'] = 'unknown_'.$user['User']['id'];
					$supportTicket['Support']['answerByName'] = $user['User']['username'];
				}

				if ($this->Support->save($supportTicket)) {
					//генерация e-mail
					$this->Email->from = 'TeamServer Email Robot <robot-no-answer@teamserver.ru>';
					$this->Email->replyTo = 'robot-no-answer@teamserver.ru';
					$this->Email->subject = 'Новый ответ в тикет #'.$ticketId.": ".$ticket['SupportTicketUnreadId']['title'];
					$this->Email->xMailer = 'TeamServer Email Robot';
					$this->Email->delivery = 'smtp';
	    			$this->Email->sendAs = 'text';

	    			if (in_array($user['Group'][0]['id'], array(1, 2, 6)))
					{
						$this->Email->to = $ticket['User'][0]['email'];
						$this->Email->template = 'new_ticket_message_notify_client';
					}
					else
					{
						$this->Email->to = 'support@teamserver.ru';
						$this->Email->template = 'new_ticket_message_notify';
					}

	    			$emailTicket = $ticket['SupportTicketUnreadId'];
	    			$emailTicket['text'] = $this->data['Support']['text'];

	    			$this->set('ticket', $emailTicket);
	    			$this->set('user', $user['User']);
	    			$this->set('servers', $ticket['Server']);
					$this->Email->send();
					//send mail
					$this->Session->setFlash('Сообщение отправлено. Непрочтённые сообщения помечены, как прочтённые. <br/>Нажмите "Вывести все сообщения", чтобы увидеть весь тикет.', 'flash_success');
					$this->redirect($this->referer());
				} else {
					$this->Session->setFlash('The support could not be saved. Please, try again.', 'flash_error');
				}
			}
			else
			{
				$this->Session->setFlash('Вы пытаетесь записать в чужой тикет. Ай-ай-ай!.', 'flash_error');
			}
		}
		else
		{
			$this->data['SupportTicket']['id'] = $ticketId;
		}
	}

//	function edit($id = null) {
//		if (!$id && empty($this->data)) {
//			$this->Session->setFlash(__('Invalid support', true));
//			$this->redirect(array('action' => 'index'));
//		}
//		if (!empty($this->data)) {
//			if ($this->Support->save($this->data)) {
//				$this->Session->setFlash(__('The support has been saved', true));
//				$this->redirect(array('action' => 'index'));
//			} else {
//				$this->Session->setFlash(__('The support could not be saved. Please, try again.', true));
//			}
//		}
//		if (empty($this->data)) {
//			$this->data = $this->Support->read(null, $id);
//		}
//		$supportTickets = $this->Support->SupportTicket->find('list');
//		$this->set(compact('supportTickets'));
//	}
//
//	function delete($id = null) {
//		if (!$id) {
//			$this->Session->setFlash(__('Invalid id for support', true));
//			$this->redirect(array('action'=>'index'));
//		}
//		if ($this->Support->delete($id)) {
//			$this->Session->setFlash(__('Support deleted', true));
//			$this->redirect(array('action'=>'index'));
//		}
//		$this->Session->setFlash(__('Support was not deleted', true));
//		$this->redirect(array('action' => 'index'));
//	}
}
?>