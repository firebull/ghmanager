<?php

/*

Users controller.
Main logic to manage Users (and clients).
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
App::uses('HttpSocket', 'Network/Http');

class UsersController extends AppController {

    public $name = 'Users';
    public $layout = 'login';
    public $helpers = array (
                                'Html',
                                'Form',
                                'Session',
                                'Js' => array('Jquery'),
                                'Common'
                            );

    //public $_DarkAuth;

    public $components = array (
        'RequestHandler',
        'Session',
        'TeamServer',
        'DarkAuth',
        'Captcha'
    );

    public $paginate = array(
                                'limit' => 15,
                                'order' => array(
                                    'User.id' => 'desc'
                                )
                            );

    public function beforeRender() {
        $this->User->contain(['Group']);

        $this->User->id = $this->DarkAuth->getUserId();

        $userInfo = $this->User->read();

        if (!empty($userInfo)) {
            unset($userInfo['User']['passwd'],
                $userInfo['User']['tokenhash']);

            // Убрать все теги, xss-уязвимость
            foreach ($userInfo['User'] as $key => $value) {
                $userInfo['User'][$key] = strip_tags($value);
            }

            // Вычислить имя пользователя
            $userInfo['User']['fullName'] = $this->TeamServer->countUserName($userInfo);

            $this->set('userinfo', $userInfo);
        }
    }

    public function login() {
        if (empty($this->DarkAuth->current_user))
        {
            return $this->redirect('/');
        }


    }

    public function logout() {
        if (!empty($this->DarkAuth->current_user))
        {
            $this->DarkAuth->logout();
        }
    }

    public function result() {
        // Функция пустышка для вывода результатов в окнах Ajax
    }

    public function createCaptcha() {
        $this->set('captcha_src', $captcha_src = $this->Captcha->create()); //create a capthca and assign to a variable
    }

    public function generatePass( $length = 8) {

        $consonantes = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz123456789';
        $r = '';
        for ($i = 0; $i < $length; $i++) {

                $r .= $consonantes{rand(0, strlen($consonantes) - 1)};
        }
        return $r;
    }

    /*
     * Регистрация нового пользователя.
     */
    public function register() {
        if (!empty($this->data)) {
            // Сначала проверить капчу
            if ($this->data['User']['ver_code']==$this->Session->read('ver_code')) {
                // Генерация токена, по которому будет подтвержден e-mail
                $hash = md5($this->data['User']['username'].rand(23658,8000064000).time());

                // TODO: Add individual salt to password and Hasher
                $user['User']['username'] = $this->data['User']['username'];
                $user['User']['tokenhash'] = $hash;
                $user['User']['email'] = $this->data['User']['email'];
                $user['Group']['id'] = 3;

                $this->User->set($user);

                // Validate data
                if ( $this->User->validates())
                {
                    $user['User']['passwd'] = $this->DarkAuth->hasher($this->data['User']['passwd']);

                    if ($this->User->save($user))
                    {
                        try {
                            //генерация e-mail
                            $Email = new CakeEmail();
                            $Email->config('smtp');
                            $Email->viewVars(array('hash' => $hash,
                                                   'username' => $this->data['User']['username']));
                            $Email->template('register_new_user', 'default')
                                  ->emailFormat('both')
                                  ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                                  ->to($this->data['User']['email'])
                                  ->subject('Подтверждение регистрации на портале GHmanager. (Confirm Registration)')
                                  ->send();

                            $this->Session->setFlash('Подтвердите ваш e-mail, щелкнув по ссылке в письме, ' .
                                                     'отправленному по указанному адресу. ЕСЛИ ВЫ НЕ ПОЛУЧИЛИ ' .
                                                     'ПИСЬМО СРАЗУ, ПРОВЕРЬТЕ ЕГО НАЛИЧИЕ В ПАПКЕ СПАМ - ' .
                                                     'иногда оно попадает туда. Если его нет и там, ' .
                                                     'напишите в техподдержку.', 'flash_login_success');

                        } catch (Exception $e) {
                            $this->Session->setFlash(sprintf('Не удалось отправить уведомление. Ошибка "%s". Свяжитесь с техподдержкой напрямую.', $e->getMessage()), 'flash_login_error');
                        }

                        return $this->redirect(array('action' => 'result'));
                    }
                    else
                    {
                        $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_login_error');
                    }
                } else {
                    // Обнулить прошлую капчу
                    $this->data['User']['ver_code'] = '';
                    //Обнулить повторный пароль
                    $this->data['User']['passwd'] = '';
                    $this->data['User']['confirmpassword'] = '';
                }
            } // капча
            else
            {
                $this->Session->setFlash('<strong>Неверный код с картинки.</strong><br/> Попробуйте еще.', 'flash_login_error');
            }
        }

        $this->createCaptcha();
    }

    /*
     * Подтвеждение регистрации
     */
    public function verify() {
        //Проверка токена
        if (!empty($this->passedArgs['t'])) {
            $tokenhash = $this->passedArgs['t'];
            if ($tokenhash !== 'null') { // чтобы проверять правильный хэш
                $user = $this->User->findByTokenhash($tokenhash);
                    //проверяем, не активирован ли уже профиль
                    if (!empty($user) && $user['User']['live'] == 0) {
                        $user['Group']['id'] = '3';
                        $user['User']['live'] = 1;
                        //активация профиля и присвоение группы
                        $this->User->set(array('tokenhash'=>'null'));
                        if ($this->User->saveAll($user, array ('validate'=> false))) {
                            $this->Session->setFlash('Регистрация завершена. Можете зайти в свой профиль.', 'flash_login_success');
                            $this->redirect('/');
                        } else {
                            $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_login_error');
                        }
                    } elseif ($user['User']['live'] == 1) {
                        $this->Session->setFlash('Ваш профиль уже активирован', 'flash_login_error');
                        $this->redirect('/');
                    }
            }
        }
    }

    // User home page
    public function home(){
        $this->DarkAuth->requiresAuth();
        $this->layout = 'v2/client';
    }

    // User home page in JSON
    // return JSON data
    public function homeData(){
        $this->DarkAuth->requiresAuth();
        $this->layout = 'ajax';
        $this->User->id = $this->DarkAuth->getUserId();

        $this->User->bindModel(['hasMany' => ['Orders'  => ['limit'  => 6,
                                                            'order'  => 'modified DESC',
                                                            'fields' =>  'id, month, sum, payed, created, payedDate'],
                                              'Actions' => ['limit' => 8, 'order' => 'created DESC'],
                                              'SupportTickets' => ['limit' => 6,
                                                                   'order' => 'created DESC',
                                                                   'conditions' => ['status' => 'open'],
                                                                   'fields' => 'id, title, status, supports_count, unread_user_count, created, modified']]]);

        $user = $this->User->read();

        $serversIds = Hash::extract($user, 'Server.{n}.id');

        $this->User->Server->contain(['GameTemplate' => ['fields' => 'id, name, longname'],
                                      'Type' => ['fields' => 'id, name']]);

        $userServers = $this->User->Server->find('all',
               ['conditions' => [ 'Server.id' => $serversIds,
                                  'OR' => [ 'Server.action' => NULL,
                                            'Server.action NOT' => 'delete']],
                'fields' => ['id', 'name', 'slots', 'address', 'port', 'slots', 'map', 'mapGroup',
                             'autoUpdate', 'privateType', 'privateStatus', 'payedTill',
                             'initialised', 'action', 'status', 'statusDescription', 'statusTime',
                             'hltvStatus', 'hltvStatusDescription', 'hltvStatusTime',
                             'crashReboot', 'crashCount', 'crashTime', 'controlByToken'],
               ]);

        // Format array to be more comfortable in JSON
        foreach ($userServers as $key => $server) {
            $userServers[$key]['Type'] = $server['Type'][0];
            $userServers[$key]['GameTemplate'] = $server['GameTemplate'][0];
            unset($server['Type'][0]);
            unset($server['GameTemplate'][0]);
        }

        $this->set('data', ['Servers' => $userServers,
                            'Orders'  => $user['Orders'],
                            'Actions' => $user['Actions'],
                            'Tickets' => $user['SupportTickets'],
                            'News'    => $this->TeamServer->getNews()]);

        $this->set('_serialize', ['data']);

    }

    // Action log
    public function actionLog(){
        $this->DarkAuth->requiresAuth();
        $this->layout = 'v2/client';

        $this->loadModel('Action');

        $this->paginate = [ 'conditions' => ['user_id' => $this->DarkAuth->getUserId()],
                            'order' => ['created' => 'desc'],
                            'limit' => 50];

        $this->set('log', $this->paginate('Action'));
    }

    /*
     * Восстановление пароля.
     * Адгоритм элементарен:
     * 2)   По логину ищется мыло,
     *      если оно свопадает с введёным в форму,
     *      генеририуется токен,
     *      записывается в базу,
     *      высылается на мыло ссылкой подверждения
     */
    public function rescuePass( $rescue = null ) {
        if (!empty($this->data)) {
            // Сначала проверить капчу
            if ($this->data['User']['ver_code']==$this->Session->read('ver_code')) {
                // Проверка логина
                if (!empty($this->data['User']['username'])) {
                    $user = $this->User->findByUsername($this->data['User']['username'],'id,email');
                    // Если получены данные из базы
                    if ($user !== false) {
                        //Проверяем мыло
                        if ($user['User']['email'] == $this->data['User']['email']) {
                            //Генерируем токен
                            $hash = md5($user['User']['email'].rand(500500,200045323).time());
                            // Сохраняем токен в базу
                            $this->User->id = $user['User']['id'];
                            $this->User->set(array('tokenhash' => $hash));
                            if ($this->User->save())
                            {
                                try {
                                    //генерация e-mail
                                    $Email = new CakeEmail();
                                    $Email->config('smtp');
                                    $Email->viewVars(array('hash' => $hash));
                                    $Email->template('request_pass', 'default')
                                          ->emailFormat('both')
                                          ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                                          ->to($user['User']['email'])
                                          ->subject('GHmanager: Запрос забытого пароля')
                                          ->send();

                                    $this->Session->setFlash('Подвердите смену пароля, щелкнув по ссылке в письме, отправленному по указанному адресу.', 'flash_login_success');

                                } catch (Exception $e) {
                                    $this->Session->setFlash(sprintf('Не удалось отправить уведомление. Ошибка "%s". Свяжитесь с техподдержкой.', $e->getMessage()), 'flash_login_error');
                                }

                                return $this->redirect(array('action' => 'result'));
                            }
                            else
                            {
                                $this->Session->setFlash('Возникла ошибка:'.mysql_error(), 'flash_login_error');
                                $this->redirect(array('action' => 'rescuePass'));
                            }
                        } else {
                            $this->Session->setFlash('Введёному логину соответсвует другой e-mail.', 'flash_login_error');
                            $this->redirect(array('action' => 'rescuePass'));
                        }
                    } else {
                        $this->Session->setFlash('Такого логина не существует.', 'flash_login_error');
                        $this->redirect(array('action' => 'rescuePass'));
                    }

                } else {
                    $this->Session->setFlash('Введите логин.', 'flash_login_error');
                    $this->redirect(array('action' => 'rescuePass'));

                }

            } else {
                $this->Session->setFlash('<strong>Неверный код с картинки.</strong><br/> Попробуйте еще.', 'flash_login_error');
                $this->redirect(array('action' => 'rescuePass'));
            }
        } else {
            $this->createCaptcha();
        }
    }

    public function confirmPassChange( $hash = null) {
        if (!empty($hash) && $hash !== 'null')
        {
            $user = $this->User->findByTokenhash($hash);
            if ($user !== false)
            {
                //Генерируем пароль
                $newPass = $this->generatePass();
                // Шифрование пароля для записи в базу
                $newDbPass = $this->DarkAuth->hasher($newPass);
                // Сохранить новый пароль в базу
                $this->User->id = $user['User']['id'];
                $this->User->set(array('tokenhash'=>'null','passwd'=>$newDbPass));
                if ($this->User->save())
                {
                    try {
                        //генерация e-mail
                        $Email = new CakeEmail();
                        $Email->config('smtp');
                        $Email->viewVars(array('newPass' => $newPass));
                        $Email->template('resque_pass', 'default')
                              ->emailFormat('both')
                              ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                              ->to($user['User']['email'])
                              ->subject('GHmanager: Восстановление забытого пароля')
                              ->send();

                        $this->Session->setFlash('Пароль отправлен по указанному адресу.', 'flash_login_success');

                    } catch (Exception $e) {
                        $this->Session->setFlash(sprintf('Не удалось отправить уведомление. Ошибка "%s". Свяжитесь с техподдержкой напрямую.', $e->getMessage()), 'flash_login_error');
                    }

                    return $this->redirect('/');
                }
                else
                {
                    $this->Session->setFlash('Возникла ошибка:'.mysql_error(), 'flash_login_error');
                    return $this->redirect(array('action' => 'rescuePass'));
                }
            }
            else
            {
                $this->Session->setFlash('Нет такого хэша. Запросите пароль повторно.', 'flash_login_error');
                return $this->redirect(array('action' => 'index'));
            }
        }
    }

    /*
     * Редактирование профиля пользователя
     */
    public function edit() {
        $this->DarkAuth->requiresAuth(array ('Admin','GameAdmin','Member'));
        $this->User->id = $this->DarkAuth->getUserId();
        $this->layout = 'ajax';

        if (isset($this->params['named']['ver'])){
            $ver = $this->params['named']['ver'];
            $path = sprintf('v%s/', $ver);
        } else {
            $ver = null;
            $path = '';
        }

        if ($this->request->is('post'))
        {
            $this->User->contain();
            $user = $this->User->read();

            // Убрать все теги, xss-уязвимость
            foreach ( $this->data['User'] as $key => $value ) {
                    $this->request->data['User'][$key] = trim(strip_tags($value));
            }

            if ($this->data['User']['mailing'] != 0 ) {
                $this->request->data['User']['mailing'] = 1;
            }

            $confirmation = false;
            $emailRegexp = "/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*([,;]\\s*\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*)*/";
            // Перед сменой email отправить запрос на старый ящик
            if ($this->data['User']['email'] != $user['User']['email'])
            {
                if (!preg_match($emailRegexp, $this->data['User']['email'])){
                    $this->Session->setFlash('Некорректный email', $path . 'flash_error');
                    return $this->redirect([ 'action' => 'edit', 'ver' => $ver]);
                }

                $this->TeamServer->logAction('Запрос изменение адреса email на'.$this->data['User']['email'], 'warn', $user['User']['id']);

                $this->User->contain();

                // Проверить, не принадлежит ли адрес другой записи
                if ($this->User->find('first', ['conditions' => ['id NOT' => $user['User']['id'],
                                                                 'email'  => $this->data['User']['email']]]))
                {
                    $this->Session->setFlash('Введённый email '.$this->data['User']['email'].' уже принадлежит другому клиенту.'.
                                             'Если вам нужно создать несколько аккаунтов на один email, напишите в техподдержку.',
                                             $path . 'flash_error');
                }
                else
                if (($code = $this->TeamServer
                                  ->saveConfirm( 'email',
                                                  $user['User']['id'],
                                                  null,
                                                  ['User' => ['email' => $this->data['User']['email']]])) !== false)
                {
                    // Если не указан мобильный, то уведомление слать на почту
                    if (empty($user['User']['phone']))
                    {
                        try {
                            //генерация e-mail
                            $Email = new CakeEmail();
                            $Email->config('smtp');
                            $Email->viewVars(array('code' => $code));
                            $Email->template('change_email', 'default')
                                  ->emailFormat('both')
                                  ->from(array('robot-no-answer@ghmanager.local' => 'GHmanager email robot'))
                                  ->to($user['User']['email'])
                                  ->subject('GHmanager: Подтверждение смены email портале. (Confirm New Email)')
                                  ->send();

                            $this->Session->setFlash('На ваш текущий email отправлен код подтверждения.<br/>'.
                                                     'Для смены email вам необходимо вставить этот код в поле ниже', $path . 'flash_success');

                        } catch (Exception $e) {
                            $this->Session->setFlash(sprintf('Не удалось отправить уведомление. Ошибка "%s". Свяжитесь с техподдержкой напрямую.', $e->getMessage()), $path . 'flash_error');
                        }
                    }
                    else
                    {
                        if ($this->TeamServer->sendSms($user['User']['phone'], 'Код подтверждения нового email: '.$code)) {
                            $this->Session->setFlash('На ваш телефон отправлен код подтверждения.<br/>'.
                                                     'Для смены email вам необходимо вставить этот код в поле ниже', $path . 'flash_success');
                        } else {
                            $this->Session->setFlash('Не удалось отправить код подтверждения на текущий телефон.<br/>'.
                                                     'Обратитесь в техподдержку.', $path . 'flash_error');
                        }
                    }
                }
                else
                {
                    $this->Session->setFlash('Не удалось отправить код подтверждения.<br/>'.
                                             'Обратитесь в техподдержку.', $path . 'flash_error');
                }

                $confirmation = true;
                $this->request->data['User']['email'] = $user['User']['email'];
            }

            // Перед сменой номера телефона, отправить запрос на оба номера
            if (isset($this->data['User']['phone'])
                    and ($this->data['User']['phone'] != $user['User']['phone'])
                    and $this->data['User']['phone'] != '')
            {
                $this->request->data['User']['phone'] = preg_replace('/\D/', '', $this->data['User']['phone']);

                // Проверить номер на соответствие шаблону СНГ
                if (preg_match('/^((\+?7|8)(?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[07])\d{8}|\+?(99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/',
                               $this->data['User']['phone'])) {

                    // После обработки повторно сравнить номера
                    if ($this->request->data['User']['phone'] != $user['User']['phone']) {

                        $this->TeamServer->logAction('Запрос изменение номера телефона на '.$this->data['User']['phone'], 'warn', $user['User']['id']);

                        // Проверить, не принадлежит ли телефон другой записи
                        if ($this->User->find('first', array( 'conditions' => array( 'id NOT' => $user['User']['id'],
                                                                                     'phone'  => $this->data['User']['phone'])))) {
                            $this->Session->setFlash('Введённый номер телефона '.$this->data['User']['phone'].' уже принадлежит другому клиенту.'.
                                                     'Если вам нужно создать несколько аккаунтов на один номер телефона, напишите в техподдержку.',
                                                     $path . 'flash_error');
                        } elseif (($code = $this->TeamServer->saveConfirm( 'phone',
                                                            $user['User']['id'],
                                                            null,
                                                            array('User' => array('phone' => $this->data['User']['phone']))
                                                            )) !== false) {
                            // Сначала послать уведомление на текущий номер
                            if (!empty($user['User']['phone'])) {
                                if (!$this->TeamServer->sendSms($user['User']['phone'], 'Код подтверждения смены номера: '.$code[0])) {
                                    $this->Session->setFlash('Не удалось отправить код подтверждения на текущий телефон.<br/>'.
                                                             'Обратитесь в техподдержку.', $path . 'flash_error');
                                }
                            }

                            // Следом послать уведомление на новый номер
                            if ($this->TeamServer->sendSms($user['User']['phone'], 'Код подтверждения нового номера: '.$code[1])) {
                                $this->Session->setFlash('На указанный телефон отправлен код подтверждения.<br/>'.
                                                         'Для смены номера вам необходимо вставить этот код в поле ниже.<br/>'.
                                                         'Если ранее был введён другой номер, вам необходимо будет вставить два кода в соответсвующие поля.', $path . 'flash_success');
                            } else {
                                $this->Session->setFlash('Не удалось отправить код подтверждения на текущий телефон.<br/>'.
                                                         'Обратитесь в техподдержку.', $path . 'flash_error');
                            }
                        } else {
                            $this->Session->setFlash('Не удалось отправить код подтверждения.<br/>'.
                                                     'Обратитесь в техподдержку.', $path . 'flash_error');
                        }

                        $confirmation = true;
                        $this->request->data['User']['phone'] = $user['User']['phone'];
                    } else {
                        $this->Session->setFlash('Вы ввели такой же номер.', $path . 'flash_success');
                        $this->redirect(array('action' => 'edit', 'ver' => $ver));
                    }
                } else {
                    $this->Session->setFlash('Некооректный номер телефона или номер не из стран СНГ.<br/>.'.
                                             'Допускаются только цифры, длина номера 11 цифр.', $path . 'flash_error');
                    $this->redirect(array('action' => 'edit', 'ver' => $ver));
                }

            }

            $this->User->contain();

            if ($this->User->save($this->data, true, array('first_name', 'second_name', 'steam_id', 'guid', 'mailing', 'sms_news'))) {
                if ($confirmation === false) {
                    $this->Session->setFlash('Информация о профиле обновлена.', $path . 'flash_success');
                    $this->redirect(array('action' => 'confirm'));
                } else {
                    $this->redirect(array('action' => 'edit', 'ver' => $ver));
                }
            }
        }
        else
        {
            // Не нужно запрашивать лишнее
            $this->User->unbindModel(array(
                                            'hasAndBelongsToMany' => array(
                                                                'Server',
                                                                'SupportTicket'
                                                    ),
                                            'hasMany' => array('Eac')
                                            )
                                    );

            $this->User->bindModel(array(
                                        'hasMany' => array(
                                                    'Confirm' => array (
                                                                            'className' => 'Confirm',
                                                                            'foreignKey' => 'user_id',
                                                                            'dependent' => false
                                                                        )
                                                        )));

            $this->request->data = $this->User->read();
            unset($this->request->data['User']['passwd']);
            unset($this->request->data['User']['ftppassword']);
            $this->set('userinfo', $this->data);
        }

        $this->render(sprintf('v%s/edit', $ver));
    }

    // Верификация операций по коду
    public function confirmByCode( $action = 'check') {
        $this->layout = 'ajax';
        $this->DarkAuth->requiresAuth(array ('Admin','GameAdmin','Member'));

        $this->loadModel('UserGroup');
        $this->loadModel('Confirm');

        $sessionUser = $this->UserGroup->findById($this->DarkAuth->getUserId());
        $sessionUserId = $sessionUser['UserGroup']['id'];
        $sessionUserGroup = $sessionUser['Group'][0]['id'];

        if (!empty($this->data)) {
            $this->Confirm->id = $this->data['Confirm']['id'];
            $confirm = $this->Confirm->read();
            if ($confirm) {
                // Проверить соответствие пользователя
                if (@$confirm['Confirm']['user_id'] == $sessionUserId   // Да, владеет
                        or
                    in_array($sessionUserGroup, array(1,2)) // Это администратор
                    ) {
                    if ($action === 'check') {
                        // Код на смену email
                        if ($confirm['Confirm']['type'] === 'email') {
                            // Проверить код
                            if (strtoupper($this->data['Confirm']['code']) == $confirm['Confirm']['code']) {
                                    $array = json_decode($confirm['Confirm']['array'], true);
                                    $array['User']['email_old'] = $sessionUser['UserGroup']['email'];
                                    $array['User']['id'] = $sessionUserId;

                                    $this->User->unbindModel(array(
                                                                    'hasAndBelongsToMany' => array(
                                                                                        'Server',
                                                                                        'SupportTicket',
                                                                                        'Group'
                                                                            ),
                                                                    'hasMany' => array('Eac')
                                                                    )
                                                            );

                                    $this->User->id = $sessionUserId;

                                    if ($this->User->save($array, true, array('email', 'email_old'))) {
                                        $this->TeamServer->logAction('Изменение адреса email подтверждено кодом.', 'warn', $sessionUserId);
                                        $this->Confirm->delete();
                                        $this->Session->setFlash('Новый адрес email успешно верифицирован.', 'flash_success');
                                    }
                            } else {
                                $this->Session->setFlash('Некорректный код.', 'flash_error');
                            }
                        } elseif ($confirm['Confirm']['type'] === 'phone') {
                            if (    // Если текущего номера нет, проверять второй код
                                    (empty($sessionUser['UserGroup']['phone']) and strtoupper($this->data['Confirm']['code2']) == $confirm['Confirm']['code2'])
                                        or
                                    // Если есть, то проверять два кода
                                    (strtoupper($this->data['Confirm']['code']) == $confirm['Confirm']['code'] and strtoupper($this->data['Confirm']['code2']) == $confirm['Confirm']['code2'])
                                ) {
                                $array = json_decode($confirm['Confirm']['array'], true);

                                // Сохранить старый номер, если есть
                                if (!empty($sessionUser['UserGroup']['phone'])) {
                                    $array['User']['phone_old'] = $sessionUser['UserGroup']['phone'];
                                }

                                $this->User->unbindModel(array(
                                                                    'hasAndBelongsToMany' => array(
                                                                                        'Server',
                                                                                        'SupportTicket',
                                                                                        'Group'
                                                                            ),
                                                                    'hasMany' => array('Eac')
                                                                    )
                                                            );

                                $this->User->id = $sessionUserId;

                                if ($this->User->save($array, true, array('phone', 'phone_old'))) {
                                    $this->TeamServer->logAction('Изменение номера телефона подтверждено кодом.', 'warn', $sessionUserId);
                                    $this->Confirm->delete();
                                    $this->Session->setFlash('Новый номер телефона успешно верифицирован.', 'flash_success');
                                }
                            } else {
                                $this->Session->setFlash('Некорректный код.', 'flash_error');
                            }
                        }
                    } elseif ($action === 'cancel') {
                        if ($confirm['Confirm']['type'] === 'email') {
                            $this->TeamServer->logAction('Отмена запроса на изменение адреса email', 'warn', $sessionUserId);
                        } elseif ($confirm['Confirm']['type'] === 'phone') {
                            $this->TeamServer->logAction('Отмена запроса на изменение номера телефона', 'warn', $sessionUserId);
                        }

                        // Удалить код
                        if ($this->Confirm->delete()) {

                            if ($confirm['Confirm']['type'] === 'email') {
                                $this->Session->setFlash('Запрос на изменение адреса email отменён.', 'flash_success');
                            } elseif ($confirm['Confirm']['type'] === 'phone') {
                                $this->Session->setFlash('Запрос на изменение номера телефона отменён.', 'flash_success');
                            }
                        } else {
                            $this->Session->setFlash('Ошибка при отмене запроса.', 'flash_error');
                        }
                    } else {
                        $this->Session->setFlash('Некорректное действие.', 'flash_error');
                    }
                } else {
                    $this->Session->setFlash('Некорректный пользователь.', 'flash_error');
                }
            } else {
                $this->Session->setFlash('Отсутствует описание кода верификации.', 'flash_error');
            }
        } else {
            $this->Session->setFlash('Отсутствуют данные.', 'flash_error');
        }

        $this->redirect(array('action' => 'edit'));

    }

    public function view( $id = null ) {
        $this->layout = 'ajax';
        $this->DarkAuth->requiresAuth(array ('Admin','GameAdmin'));

        if ($id === null) {
            $id = $this->data['User']['id'];
        }
        // Не нужно запрашивать лишнее
        $this->User->unbindModel(array(
                                        'hasAndBelongsToMany' => array(
                                                            'Server',
                                                            'SupportTicket'
                                                )));
        $this->User->id = $id;
        $this->data = $this->User->read();
        if ($this->data) {
            unset($this->request->data['User']['passwd']);

            // Убрать все теги, xss-уязвимость
            foreach ( $this->data['User'] as $key => $value ) {
                    $this->request->data['User'][$key] = strip_tags($value);
            }

        } else {
            $this->Session->setFlash('Не удалось получить данные пользователя:'.mysql_error(), 'flash_error');
        }

    }

    public function changePass() {
        $this->layout = 'ajax';
        $this->DarkAuth->requiresAuth();
        $this->User->id = $this->DarkAuth->getUserId();
        if (!empty($this->data)) {

            if ($this->data['User']['newpasswd'] == $this->data['User']['confirmpasswd']) {

                $newPassword = $this->DarkAuth->hasher($this->data['User']['newpasswd']);

                if ($this->User->saveField('passwd', $newPassword)) {
                    $this->Session->setFlash('Новый пароль сохранён.', 'flash_success');
                    $this->redirect(array('action' => 'confirm'));
                }
            } else {
                $this->Session->setFlash('Пароли не совпадают.', 'flash_error');
                $this->redirect(array('action' => 'edit'));
            }

        } else {
            $this->Session->setFlash('Вы не ввели пароль.', 'flash_error');
            $this->redirect(array('action' => 'edit'));
        }
    }

     /*
     * Административное редактирование профиля пользователя
     */
    public function editByAdmin($id = null) {
        $this->layout = 'ajax';
        $this->DarkAuth->requiresAuth(array('Admin'));
        if ($id) {
            $this->User->id = $id;
        }

        if (empty($this->data)) {
            // Не нужно запрашивать лишнее
            $this->User->unbindModel(array(
                                            'hasAndBelongsToMany' => array(
                                                                'Server',
                                                                'SupportTicket'
                                                    )));
            $this->data = $this->User->read();
            unset($this->request->data['User']['passwd']);

            // Убрать все теги, xss-уязвимость
            foreach ( $this->data['User'] as $key => $value ) {
                    $this->request->data['User'][$key] = strip_tags($value);
            }

            $groups = $this->User->Group->find('all');

            foreach ( $groups as $group ) {
                $groupList[$group['Group']['id']] = $group['Group']['desc'];
            }

            $this->set('groupList', $groupList);

        }
        else
        {
            if ($this->User->save($this->data, true, array('first_name', 'second_name', 'email', 'steam_id', 'guid', 'discount')))
            {
                $this->Session->setFlash('Информация о профиле обновлена.', 'flash_success');
            }
            else
            {
                $this->Session->setFlash('Не удалось сохранить данные:'.mysql_error(), 'flash_error');
            }

            $this->redirect(array('action' => 'confirm'));
        }
    }

    public function delete($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        /* TODO: Довести до ума!
         *
         * Прежде чем удалять пользователя,
         * необходимо убедиться, что к нему не
         * привязаны серверы.
         * Если ни разу клиент не подключался к панели,
         * либо пароль FTP отсутсвует - удалить клиента из базы.
         * Если есть инициализированные серверы, либо создан пароль FTP,
         * значит клиент уже имеет учётные записи на физических серверах
         * надо удалить серверы процедурой /servers/delete, а потом пометить
         * клиента на удаление., после чего будет запущен скрипт на каждом
         * из рутовых серверов, который удалит локального пользователя.
         */

         if ($id) {
            $this->User->id = $id;
            $user = $this->User->read();
            if (!empty($user['Server'][0])) {
                foreach ( $user['Server'] as $server ) {
                    $this->requestAction("/servers/delete/".$server['id']);
                }
                $this->Session->setFlash('Серверы клиента помечены на удаление. Когда не будет привязанных к клиенту серверов, его можно будет удалить.', 'flash_success');
            } elseif ($user['User']['last_login'] === '0000-00-00 00:00:00' or empty($user['User']['ftppassword']) ) {
                if ($this->User->delete($id)) {
                    $this->Session->setFlash('Информация о клиенте удалена.', 'flash_success');
                } else {
                    $this->Session->setFlash('Возникла ошибка: '.mysql_error, 'flash_error');
                }
            }
         }

         $this->redirect(array('action'=>'control'));

    }

    public function confirm($message = null) {
        $this->layout = 'ajax';
        $this->set('message', $message);

    }

    public function changeFtpPass($action = null) {
        $this->DarkAuth->requiresAuth();

        if (isset($this->params['named']['ver'])){
            $ver = $this->params['named']['ver'];
            $path = sprintf('v%s/', $ver);
        } else {
            $ver = null;
            $path = '';
        }

        $this->loadModel('RootServer');
        $user = $this->DarkAuth->getAllUserInfo();
        $userId = $user['User']['id'];
        $this->set('ftpLogin','client'.$userId);

        if (empty($action))
        {
            $this->set('ftpPassword',@$user['User']['ftppassword']);
        }
        else
        if ($action == 'change')
        {
            /* Смена пароля должна производиться на каждом физическом сервере,
             * где клиент имеет свои серверы.
             * Потому сначала надо получить их список.
             */
            $this->User->contain(['Server' => ['fields' => 'id',
                                               'conditions' => ['Server.address NOT' => NULL]]]);

            $user = $this->User->find('first', ['conditions' => ['User.id' => $userId],
                                                'fields' => ['id','ftppassword']]);

            $serversIdsList = HASH::extract($user, 'Server.{n}.id');

            $this->User->Server->contain(['RootServer' => ['fields' => 'id']]);
            $userServers = $this->User
                                ->Server
                                ->find('all', ['conditions' => ['Server.id' => $serversIdsList],
                                               'fields'     => ['id', 'address']]);

            // Обнулить переменные, на всякий случай
            $rootServers  = array();
            $tmp = array();

            $rootServersIdsList = array_unique(HASH::extract($userServers, '{n}.RootServer.{n}.id'));

            /*
             * Если физический сервер только один, просто берем
             * IP сервера пользователя и забудем, что делали выше.
             * Если их больше, то надо будет запросить список их IP.
             */

            if (count(@$rootServersIdsList) == 1)
            {
                $rootServersIps[] = $userServers[0]['Server']['address'];
            }
            else
            if (count(@$rootServersIdsList) > 1)
            {
                $rootServers = $this->RootServer->find('all', [
                                                'conditions' => ['id' => $rootServersIdsList]]);

                $rootServersIps  = array();
                $tmp = array();
                foreach ($rootServers as $rootServer):

                    $rootServersIps[] = @$rootServer['RootServerIp'][0]['ip'];

                endforeach;

            }
            else
            if (count(@$rootServersIdsList) <= 0) {
                $this->Session->setFlash('Нет инициализированных серверов, негде менять пароль.', $path . 'flash_error');
            }

            /*
             * Теперь, имея список IP, перебираем физические серверы
             * и меняем пароль на каждом из них
             */
            $ftp_password = 'none';
            if (!empty($rootServersIps))
            {
                $HttpSocket = new HttpSocket();
                $requestStr = sprintf("/~client%d/common/.change_pass.py", $userId);
                $data       = sprintf("p=%s&n=%s", $user['User']['ftppassword'], $ftp_password);

                foreach ($rootServersIps as $serverIp):

                    //Обращаемся к серверу по IP
                    $response = $HttpSocket->get('http://' . $serverIp . $requestStr, $data);

                   // Совершаем запрос и форматируем вывод
                    if (!$response or !$response->isOK())
                    {
                        $this->Session->setFlash(sprintf("Невозможно подключиться к серверу: <br />\n %s ($d)<br />\n", $response->reasonPhrase, $response->code), $path . 'flash_error');
                        $this->redirect(array('action'=>'changeFtpPass', 'ver' => $ver));

                    }
                    else
                    {
                        $var = eregi("<!-- RESULT START -->(.*)<!-- RESULT END -->", $response->body, $out);

                        $responsecontent = trim($out[1]);
                        if ($responsecontent != 'error') {

                            /*
                             * Пароль в базу сохраняем лишь однажды,
                             * нечего плодить лишних запросов.
                             */
                             if ($ftp_password === 'none') {

                                $this->User->id = $userId;

                                    if ($this->User->saveField('ftppassword', $responsecontent)) {
                                            $this->Session->setFlash('Пароль изменён.', $path . 'flash_success');
                                            $this->set('ftpPassword',@$responsecontent);
                                            $ftp_password = $responsecontent;
                                    } else {
                                        $this->Session->setFlash('Возникла ошибка БД', $path . 'flash_error');
                                    }
                             }

                        } else {
                            $this->Session->setFlash('Произошла ошибка: "'.$responsecontent.'"', $path . 'flash_error');
                            break;
                        }
                    }

                endforeach;
            }

            $this->redirect(['action' => 'changeFtpPass', 'ver' => $ver]);
        }

        $this->render(sprintf('v%s/change_ftp_pass', $ver));
    }

    public function autoComplete() {
        $this->layout = 'ajax';
        $this->DarkAuth->requiresAuth('Admin', 'GameAdmin', 'OrdersAdmin');

        if (isset($this->params['url']['term'])) {
            // Делаем выборку из базы по запросу jQuery
            // Не нужно запрашивать лишнее
            $this->User->unbindModel(array(
                                            'hasAndBelongsToMany' => array(
                                                                'Server',
                                                                'SupportTicket'
                                                    )));
            $terms = $this->User->find('all', array(
                        'conditions' => array(
                            'username LIKE' => $this->params['url']['term'].'%'
                        ),
                        'limit' => 5,
                        'fields' => array('id','username')
            ));
            // Готовим список для корректного преобразования в JSON
            if ( !empty($terms) ) {
                        $termsList = array();
                        foreach ($terms as $term):

                        $termsList[] = $term['User']['username'];

                        endforeach;
                        $this->set('list', $termsList);
            }
        }
    }

    public function search() {
        $this->layout = 'ajax';
    }

    public function control($sortby = 'id', $order = null) {

        if (!$order) {
            $this->layout = 'client';
        } else {
            $this->layout = 'ajax';
            $this->set('pastOrder', $order);
        }

        $this->DarkAuth->requiresAuth('Admin');

        $userInfo = $this->DarkAuth->getAllUserInfo();

        // Убрать все теги, xss-уязвимость
        foreach ( $userInfo['User'] as $key => $value ) {
                $userInfo['User'][$key] = strip_tags($value);
        }

        $this->set('userinfo', $userInfo);

//      $this->data = $this->User->find('all', array(
//                                                      'order' => "$sortby $order"
//
//                                                      ));
        $this->data = $this->paginate('User');
        // Убрать все теги, xss-уязвимость
        foreach ( $this->data as $key => $value ) {
                foreach ($value['User'] as $userParam => $userValue) {
                    $this->request->data[$key]['User'][$userParam] = strip_tags($userValue);
                }

        }

        $this->set('users', $this->data);

        $this->loadModel('Support');
        $openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
        $this->set('openTickets', $openTickets[0][0]['COUNT(*)']);
    }

    public function uploadAvatar() {
        $this->layout = 'client';

            if ($this->data['User']['uploaded_avatar']) {
                //Проверяем наличие ошибок и вывод сообщения, если есть
                if ($this->data['User']['uploaded_avatar']['error'] == 0) {
                    //Проверяем рабочий файл на предмет загружен или нет
                    if (is_uploaded_file($this->data['User']['uploaded_avatar']['tmp_name']) === true) {
                        //Проверяем соответсвие загруженного файла требуемым расширениям
                        if ($this->User->imageIsAllowedType($this->data['User']['uploaded_avatar']['type'])) {
                            //Проверяем размер автара
                            $check_size = $this->User->imageIsAllowedSize($this->data['User']['uploaded_avatar']);
                            if ( $check_size === true) {
                            //Новое имя файла исходя из ID пользователя
                                $newname = 'avatar_user_id_'.$this->DarkAuth->getUserId().
                                                                '.'.
                                                                $this->User->typeToExtension($this->data['User']['uploaded_avatar']['type']);
                                if (move_uploaded_file( $this->data['User']['uploaded_avatar']['tmp_name'],
                                                                WWW_ROOT."/img/userimg/avatars/".
                                                                $newname )) {
                                        $this->User->id=$this->DarkAuth->getUserId();
                                        $this->data['User']['avatar']=$newname;
                                        if ($this->User->save($this->data, array('validate' => false))) {

                                            $this->Session->setFlash('Аватар загружен.','flash_success');

                                        } else {
                                            $this->Session->setFlash('Аватар не загружен.'.mysql_error(),'flash_error');
                                        }

                                    } else {
                                        $this->Session->setFlash('Сохранение файла не удалось! Возможно не существует или заблокирован конечный путь!', 'flash_error');
                                    }
                            } else {
                                $this->Session->setFlash($check_size,'flash_error');
                            }

                        } else {
                            $this->Session->setFlash('Данный тип файла не разрешён!', 'flash_error');
                        }

                    } else {
                        $this->Session->setFlash('У вас нет доступа к этому файлу!', 'flash_error');
                    }
                } else {
                    //Вывод сообщения об ошибке загрузки
                    $error = $this->User->fileUploadErrorMessage($this->data['User']['uploaded_avatar']['error']);
                    $this->Session->setFlash($error, 'flash_error');
                }
            }
            $this->set('file', $this->data['User']['uploaded_avatar']);
            $this->redirect( array('controller' => $this->data['User']['urlOriginal']) );
    }

}
?>
