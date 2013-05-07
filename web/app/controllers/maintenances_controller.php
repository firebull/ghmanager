<?php
/*

Maintenance controller.
Some logic to clean opereations - delete unpayed servers, orders and so on.
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

class MaintenancesController extends AppController {

    public $name = 'Maintenances';
    public $layout = 'client';
    public $_DarkAuth = array (
        'required' => array (
            'Admin'
        )
    );
    public $helpers = array (
        'Time',
        'Ajax',
        'javascript',
        'Html'
    );

    public $components = array (
        'RequestHandler',
        'Session'
    );

    function beforeRender() {
        $userInfo = $this->DarkAuth->getAllUserInfo();

        // Убрать все теги, xss-уязвимость
        if (!empty($userInfo))
        {
            foreach ( $userInfo['User'] as $key => $value ) {
                    $userInfo['User'][$key] = strip_tags($value);
            }

            $this->set('userinfo', $userInfo);

            $this->loadModel('Support');
            $openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
            $this->set('openTickets', $openTickets[0][0]['COUNT(*)']);

        }
    }

    function index() {
        $this->redirect(array('controller'=>'maintenances','action' => 'control'));
    }

    function control() {
        $this->render();
    }


}
?>
