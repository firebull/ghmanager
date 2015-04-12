<?php

/*

Messages (News) controller.
Show Messages (News) at main page of panel.
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

/* Это контроллер Новостей.
 *
 * Почему называется Messages?
 * Дело в том, что в английском нет единственного числа для News.
 * А в cakephp именование идет во множественном числе, и, чтобы
 * адекватно обрабатывались все запросы, надо было бы именовать
 * News как New, что есть полный бред.
 */

class MessagesController extends AppController {

    public $name   = 'Messages';
    public $layout = 'client';

    public $_DarkAuth;

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

    function beforeRender() {
        $userInfo = $this->DarkAuth->getAllUserInfo();

        // Убрать все теги, xss-уязвимость
        foreach ( $userInfo['User'] as $key => $value ) {
                $userInfo['User'][$key] = strip_tags($value);
        }

        $this->set('userinfo', $userInfo);
    }

    function index() {
        $this->Message->recursive = 0;
        $this->paginate = array('limit' => 5, 'order' => 'created DESC' );

        $this->set('news', $this->paginate());
    }

    function control() {
        $this->layout = 'client';
        $this->DarkAuth->requiresAuth(array('Admin','GameAdmin'));
        $this->Message->recursive = 0;
        $this->paginate = array('limit' => 10, 'order' => 'created DESC' );

        $this->set('news', $this->paginate());
    }

    /* Функция, отображающая новости на главной странице
     * На главной странице будем выводить новости хостинга
     *
     * Если у клиента уже есть серверы, то выводить только
     * одну последнюю новость. Остальные по клику.
     *
     * Если последняя новость помечена прочитанной для клиента,
     * то выводить только поле, по нажатию на которое выводятся
     * пять последних новостей
     *
     * Если серверов нет, то выводить пять последних новостей
     *
     * TODO: надо сделать кнопку "Я знаю", после которой новости
     * будут скрываться
     * */
    function init(){

        $user = $this->DarkAuth->getAllUserInfo();

        if (count($user['Server']) >= 1){
            $this->Message->bindModel(array(
                                            'hasAndBelongsToMany' => array(
                                                                'User' => array(
                                                                                  'fields'     => 'id',
                                                                                  /* Если в результате ответа, поле ['User'] будет непустым,
                                                                                   * то можно считать, что новость им прочитана */
                                                                                  'conditions' => array('id' => $user['User']['id'])
                                                                                  )
                                                    )));

            $news = $this->Message->find('first', array('conditions' => array(),
                                                     'order' => 'created DESC',
                                                     'limit' => 1));
            $this->set('news_item', @$news);
        }
        else
        {
            $this->paginate = array('limit' => 3, 'order' => 'created DESC' );

            $this->set('news', $this->paginate());
            $this->render('index');
        }

    }

    /* Пометить последню новость, как прочитанную для
     * клиента, чтобы она не маячила на первой странице
     */
    function hide ( $id = null){
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Не указан ID новости', 'flash_error');
        }
        else
        {
            $this->Message->id = $id;
            if ($news_item = $this->Message->read()){
                $news_item['User']['id'] = $this->DarkAuth->getUserId();
                $this->Message->bindModel(array(
                                                'hasAndBelongsToMany' => array(
                                                                    'User' => array('unique' => false)
                                                        )), false);

                unset($news_item['Message']['modified']);
                if (!$this->Message->save($news_item)){
                    $this->Session->setFlash('Не удалось установить статус "Прочитанно": '.mysql_error(), 'flash_error');
                }
            }
            else
            {
                $this->Session->setFlash('Нет такой новости', 'flash_error');
            }
        }

        $this->redirect( $this->referer());

    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid news', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('news', $this->Message->read(null, $id));
    }

    function add() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        if (!empty($this->data)) {
            $this->Message->create();
            if ($this->Message->save($this->data)) {
                $this->Session->setFlash(__('The news has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The news could not be saved. Please, try again.', true));
            }
        }
    }

    function edit($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid news', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Message->save($this->data)) {
                $this->Session->setFlash(__('The news has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The news could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->request->data = $this->Message->read(null, $id);
        }
    }

    function delete($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for news', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Message->delete($id)) {
            $this->Session->setFlash(__('News deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('News was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }
}
?>
