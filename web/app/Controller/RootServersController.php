<?php
/*

Root Servers controller.
Manage Root servers at Locations (DataCenters).
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

class RootServersController extends AppController {

    public $name = 'RootServers';
    public $layout = 'client';
    public $_DarkAuth = array (
                                'required' => array (
                                    'Admin'
                                )
                            );
    public $helpers = array (
        'Time',
        'Html',
        'Js' => array('Jquery')
    );
    public $components = array (
                                'RequestHandler',
                                'Session',
                                'DarkAuth'
                            );

    public function beforeRender() {
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

    public function control() {
        $this->set('rootServers', $this->RootServer->find('all'));
    }

    public function add() {
        if (!empty($this->data)) {
            if ($this->RootServer->save($this->data)) {
                $this->Session->setFlash('Сервер добавлен.<br/>Готов к наполнению.', 'flash_success');
                $this->redirect(array('controller'=> 'locations', 'action' => 'control'));
            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
            }
        }
    }

    public function edit($id = null) {
        $this->RootServer->id = $id;
        if (empty($this->data)) {
            $this->request->data = $this->RootServer->read();
        } else {
            if ($this->RootServer->save($this->data)) {
                $this->Session->setFlash('Информация о сервере обновлена.', 'flash_success');
                $this->redirect(array('controller'=> 'locations', 'action' => 'control'));
            }
        }
    }

    public function delete($id) {
        $this->RootServer->delete($id);
        $this->Session->setFlash('Сервер #'.$id.' удалён.', 'flash_success');
        $this->redirect($this->referer());
    }

    public function editRootServerIp() {

    }

    public function viewRootServerIp($id = null) {
        $this->RootServer->id = $id;
        $server = $this->RootServer->read();
        $this->loadModel('ServerClean');

        foreach ($server['RootServerIp'] as $key => $ip) {
            if ($ip['type'] === 'public') {
                $server['RootServerIp'][$key]['used'] = false;
            } else {
                /* TODO: Быдлокод, уж простите. Болею, сил нет сделать одним запросом к базе =( */
                $serverId = $this->ServerClean->findByAddress($ip, array('id'));

                if (!empty($serverId)) {
                    $server['RootServerIp'][$key]['used'] = $serverId['ServerClean']['id'];
                } else {
                    $server['RootServerIp'][$key]['used'] = false;
                }

                unset($serverId);
            }
        }

        $this->set('ipList', $server['RootServerIp']);
    }

    public function viewRootServerServers() {
        $this->loadModel('RootServerServers');
        $root = $this->RootServerServers->find('all');
        $this->set('root', $root);
    }

}
?>
