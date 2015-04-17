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
        if ($this->request->is(['post'])) {

            if (!$ticketId) {
                if (!empty($this->data['SupportTicket']['id'])){
                    $ticketId = intval($this->data['SupportTicket']['id']);
                } else {
                    throw new BadRequestException(__("No Ticket id"));
                }
            }

            $this->request->data['Support']['text'] = trim(Purifier::clean($this->request->data['Support']['text'], 'cleanup'));

            if (empty($this->request->data['Support']['text']))
            {
                throw new BadRequestException(__("Message is empty"));
            }


            $this->Support->SupportTicket->contain(['Server' => [],
                                                   'User'   => ['fields' => ['id', 'username', 'email']],
                                                   'Support' => ['conditions' => ['readstatus' => 'unread'],
                                                                 'fields' => ['id']]
                                                   ]);
            $this->Support->SupportTicket->id = $ticketId;
            $ticket = $this->Support->SupportTicket->read();

            if ($ticket['SupportTicket']['status'] == 'closed'){
                throw new BadRequestException(__("Ticket is closed"));
            }

            $user = $this->DarkAuth->getAllUserInfo();

            if (in_array($user['Group'][0]['id'], [1,2,6])){
                $isAdmin = true;
            } else {
                $isAdmin = false;
            }

            // Check rights
            if ($ticket['User']['id'] == $user['User']['id']  or $isAdmin )
            {
                $unreadMessagesIds = Hash::extract($ticket['Support'], '{n}.id');

                // Set read status
                if ($isAdmin){ // For admin
                    $this->Support->updateAll(['Support.readstatus' => "'read'"],
                                              ['AND' => ['Support.id' => $unreadMessagesIds,
                                                         'Support.answerBy' => 'owner']]
                                        );
                } else { // For user
                    $this->Support->updateAll(['Support.readstatus' => "'read'"],
                                              ['AND' => ['Support.id' => $unreadMessagesIds,
                                                         'Support.answerBy' => 'support']]
                                        );
                }

                // Сохранять напрямую $this->data, полученную от клиента, было глупо =)
                $this->Support->create();
                $supportMessage['Support']['readstatus'] = 'unread';
                $supportMessage['Support']['user_id']    = $ticket['User']['id'];
                $supportMessage['Support']['support_ticket_id'] = $ticketId;

                // Убрать всякие теги. Дабы особо умные не пытались передать скрипт техподдержке.
                $supportMessage['Support']['text'] = $this->request->data['Support']['text'];


                if ($ticket['User']['id'] == $user['User']['id']) {
                    $supportMessage['Support']['answerBy'] = 'owner';
                } elseif (in_array($user['Group'][0]['id'], array(1, 2, 6))) {
                    $supportMessage['Support']['answerBy'] = 'support';
                    $supportMessage['Support']['answerByName'] = $user['User']['username'];
                } else {
                    $supportMessage['Support']['answerBy'] = 'unknown_'.$user['User']['id'];
                    $supportMessage['Support']['answerByName'] = $user['User']['username'];
                }

                if ($this->Support->save($supportMessage)) {

                    // We need to return saved message to render it on page without reload
                    if ($supportMessage['Support']['answerBy'] == 'owner')
                    {
                        $support = $this->Support->find('first', ['conditions' => ['Support.id' => $this->Support->id],
                                                        'fields' => ['id', 'readstatus', 'support_ticket_id', 'answerBy', 'text', 'created',
                                                                     'SupportTicket.supports_count', 'SupportTicket.unread_user_count']]);
                    }
                    else
                    if ($supportMessage['Support']['answerBy'] == 'support')
                    {
                        $support = $this->Support->find('first', ['conditions' => ['Support.id' => $this->Support->id],
                                                        'fields' => ['id', 'readstatus', 'support_ticket_id', 'answerBy', 'answerByName', 'text', 'created',
                                                                     'SupportTicket.supports_count', 'SupportTicket.unread_user_count', 'SupportTicket.unread_admin_count']]);
                    }

                    $savedTicket = $support['Support'];
                    $savedTicket['supports_count'] = $support['SupportTicket']['supports_count'];
                    $savedTicket['unread_user_count'] = $support['SupportTicket']['unread_user_count'];

                    if ($supportMessage['Support']['answerBy'] == 'support'){
                        $savedTicket['unread_admin_count'] = $support['SupportTicket']['unread_admin_count'];
                    }

                    //генерация e-mail
                    $Email = new CakeEmail();

                    if (in_array($user['Group'][0]['id'], array(1, 2, 6))) {
                        $Email->to($ticket['User']['email']);
                        $Email->template('new_ticket_message_notify_client', 'default');
                    } else {
                        $Email->to('support@ghmanager.local');
                        $Email->template('new_ticket_message_notify', 'default');
                    }

                    $emailTicket = $ticket['SupportTicket'];
                    $emailTicket['text'] = $supportMessage['Support']['text'];

                    //send mail
                    try {
                        //генерация e-mail

                        $Email->config('smtp');
                        $Email->viewVars(array('ticket' => $emailTicket,
                                               'user' => $user['User'],
                                               'servers' => @$emailServers));
                        $Email->emailFormat('text')
                              ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                              ->to('support@ghmanager.local')
                              ->subject('Новый ответ в тикет #'.$ticketId.": ".$ticket['SupportTicket']['title'])
                              ->send();

                        if ($this->params['ext'] == 'json'){
                            $this->set('result', ['ok', 'message' => $savedTicket]);
                        } else {
                            $this->Session->setFlash(__('Reply was sent, unread messages marked as read.'), 'flash_success');
                        }

                    } catch (Exception $e) {
                        if ($this->params['ext'] == 'json'){
                            $this->set('result', ['message' => $savedTicket, 'info' => __('Reply was saved, but could not send message to admins. Error "%s". Please, contact support if you wont recieve a message soon.', $e->getMessage())]);
                        } else {
                            $this->Session->setFlash(sprintf('Ответ сохранён, но не удалось отправить уведомление администраторам. Ошибка "%s". Тем не менее, если вы не получите ответа в ближайшее время, свяжитесь с техподдержкой напрямую.', $e->getMessage()), 'flash_warning');
                        }
                    }

                    if ($this->params['ext'] == 'json'){

                        $this->set('_serialize', ['result']);
                        return $this->render();
                    } else {
                        $this->redirect($this->referer());
                    }
                } else {
                    $errors = $this->Support->validationErrors;

                    if ($this->params['ext'] == 'json'){
                        $this->set('result', ['error' => $errors]);
                        $this->set('_serialize', ['result']);
                        return $this->render();
                    }

                    $this->Session->setFlash('The support could not be saved. Please, try again.', 'flash_error');
                }
            } else {
                throw new ForbiddenException(__("Access to others tickets is denied"));
            }
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
