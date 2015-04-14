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
App::uses('CakeEmail', 'Network/Email');

class SupportsController extends AppController {

    public $name = 'Supports';
    public $layout = 'client';
    public $_DarkAuth;
    public $helpers = array('Time', 'Js', 'Markdown.Markdown');
    public $components = array('RequestHandler', 'Session', 'DarkAuth', 'TeamServer');

    public function beforeRender() {
        $userInfo = $this->DarkAuth->getAllUserInfo();

        if (!empty($userInfo)){
            // Убрать все теги, xss-уязвимость
            foreach ( $userInfo['User'] as $key => $value ) {
                $userInfo['User'][$key] = strip_tags($value);
                // Вычислить имя пользователя
                $userInfo['User']['fullName'] = $this->TeamServer->countUserName($userInfo);
                $this->set('userinfo', $userInfo);
            }
        }

        $this->TeamServer->setLang();
    }

    public function add( $ticketId = null) {
        $this->DarkAuth->requiresAuth();
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
                $user['Group'][0]['id'] == 1                        // Это администратор
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
                $this->request->data['Support']['readstatus'] = 'unread';

                // Убрать всякие теги. Дабы особо умные не пытались передать скрипт техподдержке.
                $this->request->data['Support']['text'] = strip_tags($this->data['Support']['text']);

                // Сохранять напрямую $this->data, полученную от клиента, было глупо =)
                $supportTicket = array( 'Support' => $this->request->data['Support'],
                                        'SupportTicket' => array( 'id' => $this->request->data['SupportTicket']['id']));

                if ($ticket['User'][0]['id'] == $user['User']['id']) {
                    $supportTicket['Support']['answerBy'] = 'owner';
                } elseif (in_array($user['Group'][0]['id'], array(1, 2, 6))) {
                    $supportTicket['Support']['answerBy'] = 'support';
                    $supportTicket['Support']['answerByName'] = $user['User']['username'];
                } else {
                    $supportTicket['Support']['answerBy'] = 'unknown_'.$user['User']['id'];
                    $supportTicket['Support']['answerByName'] = $user['User']['username'];
                }

                if ($this->Support->save($supportTicket)) {
                    //генерация e-mail
                    $Email = new CakeEmail();

                    if (in_array($user['Group'][0]['id'], array(1, 2, 6))) {
                        $Email->to($ticket['User'][0]['email']);
                        $Email->template('new_ticket_message_notify_client', 'default');
                    } else {
                        $Email->to('support@ghmanager.local');
                        $Email->template('new_ticket_message_notify', 'default');
                    }

                    $emailTicket = $ticket['SupportTicketUnreadId'];
                    $emailTicket['text'] = $this->request->data['Support']['text'];

                    //send mail
                    $this->Session->setFlash('Сообщение отправлено. Непрочтённые сообщения помечены, как прочтённые. <br/>Нажмите "Вывести все сообщения", чтобы увидеть весь тикет.', 'flash_success');

                    try {
                        //генерация e-mail

                        $Email->config('smtp');
                        $Email->viewVars(array('ticket' => $emailTicket,
                                               'user' => $user['User'],
                                               'servers' => @$emailServers));
                        $Email->emailFormat('text')
                              ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                              ->to('support@ghmanager.local')
                              ->subject('Новый ответ в тикет #'.$ticketId.": ".$ticket['SupportTicketUnreadId']['title'])
                              ->send();

                        $this->Session->setFlash('Сообщение отправлено. Непрочтённые сообщения помечены, как прочтённые. <br/>Нажмите "Вывести все сообщения", чтобы увидеть весь тикет.', 'flash_success');

                    } catch (Exception $e) {
                        $this->Session->setFlash(sprintf('Ответ сохранён, но не удалось отправить уведомление администраторам. Ошибка "%s". Тем не менее, если вы не получите ответа в ближайшее время, свяжитесь с техподдержкой напрямую.', $e->getMessage()), 'flash_error');
                    }

                    $this->redirect($this->referer());
                } else {
                    $this->Session->setFlash('The support could not be saved. Please, try again.', 'flash_error');
                }
            } else {
                $this->Session->setFlash('Вы пытаетесь записать в чужой тикет. Ай-ай-ай!.', 'flash_error');
            }
        } else {
            $this->request->data['SupportTicket']['id'] = $ticketId;
        }
    }

    public function faq() {
        $this->DarkAuth->requiresAuth();
        $this->layout = 'v2/client';
    }

    public function help() {
        $this->DarkAuth->requiresAuth();
        $this->layout = 'v2/client';

        Cache::set(array('duration' => '+1 days'));

        if (($helps = Cache::read('help')) === false) {

            $this->loadModel('Help');
            $helps = $this->Help->find('all');

            Cache::set(array('duration' => '+1 days'));
            Cache::write('help', $helps);
        }

        $this->set('helps', $helps);
    }

/*  public function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid support'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Support->save($this->data)) {
               $this->Session->setFlash(__('The support has been saved'));
               $this->redirect(array('action' => 'index'));
         } else {
            $this->Session->setFlash(__('The support could not be saved. Please, try again.'));
         }
     }
     if (empty($this->data)) {
        $this->request->data = $this->Support->get($id);
     }
     $supportTickets = $this->Support->SupportTicket->find('list');
     $this->set(compact('supportTickets'));
 }

    public function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for support'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Support->delete($id)) {
            $this->Session->setFlash(__('Support deleted'));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Support was not deleted'));
        $this->redirect(array('action' => 'index'));
 }
*/}
?>
