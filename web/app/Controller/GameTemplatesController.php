<?php
/*

Game Templates controller.
Main logic to work with servers templates: desc, mods, plugins and so on.
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

class GameTemplatesController extends AppController {

    public $name = 'GameTemplates';
    public $layout = 'client';

    public $_DarkAuth;

    public $helpers = array (
        'Time',
        'Js',
        'Html',
        'Cache'
    );
    public $components = array (
        'RequestHandler',
        'Session',
        'DarkAuth'
    );

    public $cacheAction = array(
        'getMods/' => '15 minutes',
        'getTemplates/' => '15 minutes'
    );

        public function uploadMapImage() {

            if ($this->data['Map']['map_image']) {
                //Проверяем наличие ошибок и вывод сообщения, если есть
                if ($this->data['Map']['map_image']['error'] == 0) {
                    //Проверяем рабочий файл на предмет загружен или нет
                    if (is_uploaded_file($this->data['Map']['map_image']['tmp_name']) === true) {
                        //Проверяем соответсвие загруженного файла требуемым расширениям
                        if ($this->Map->imageIsAllowedType($this->data['Map']['map_image']['type'])) {
                            //Проверяем размер автара
                            $check_size = $this->Map->imageIsAllowedSize($this->data['Map']['map_image']);
                            if ( $check_size ) {
                            //Новое имя файла исходя из имени карты
                                $newname = $this->data['Map']['name'];
                                if ($this->Map->resizeImage( $this->data['Map']['map_image']['tmp_name'],
                                                             $newname)) {
                                        $this->Session->setFlash('Изображение карты сохранено успешно', 'flash_success');
                                        return true;
                                    } else {
                                        $this->Session->setFlash('Сохранение файла не удалось! Возможно не существует или заблокирован конечный путь!', 'flash_error');
                                        return false;
                                    }
                            } else {
                                $this->Session->setFlash($check_size,'flash_error');
                                return false;
                            }

                        } else {
                            $this->Session->setFlash('Данный тип файла не разрешён!', 'flash_error');
                            return false;
                        }

                    } else {
                        $this->Session->setFlash('У вас нет доступа к этому файлу!', 'flash_error');
                        return false;
                    }
                } else {
                    //Вывод сообщения об ошибке загрузки
                    $error = $this->Map->fileUploadErrorMessage($this->data['Map']['map_image']['error']);
                    $this->Session->setFlash($error, 'flash_error');
                    return false;
                }
            }

    }

    public function beforeRender() {
        $userInfo = $this->DarkAuth->getAllUserInfo();

        if ($userInfo){
            // Убрать все теги, xss-уязвимость
            foreach ( $userInfo['User'] as $key => $value ) {
                    $userInfo['User'][$key] = strip_tags($value);
            }

            $this->set('userinfo', $userInfo);

            $this->loadModel('Support');
            $openTickets = $this->Support->query("SELECT COUNT(*) FROM `support_tickets` WHERE `status`='open'");
            $this->set('openTickets', $openTickets[0][0]['COUNT(*)']);
        }

    }

    public function control() {
        $this->DarkAuth->requiresAuth(array('Admin'));

        $this->request->data = $this->GameTemplate->find('all');
        $this->set('gameTemplates', $this->data);

    }

    public function add() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Type');
        //Save the data
        if ($this->data) {
            if (@$this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Шаблон добавлен', 'flash_success');
                $this->redirect(array('action' => 'control'));
            } else {

            }
        } else {
            $typesList = $this->Type->find('list', array('fields' => array('id','longname')));
            asort($typesList);
            $this->set('typesList', $typesList);
        }

    }

    public function edit($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->GameTemplate->id = $id;
        $this->loadModel('Type');
        $this->loadModel('Protocol');

        if (empty($this->data)) {

            $this->request->data = $this->GameTemplate->read();
            $typesList = $this->Type->find('list', array('fields' => array('id','longname')));
            asort($typesList);
            $this->set('typesList', $typesList);
            //Если шаблон уже имеет тип
                if (@$this->data['Type'][0]['id']) {
                    $this->set('typeId', @$this->data['Type'][0]['id']);
                } else {
                    //Выставить пункт списка по умолчанию на SRCDS
                    $this->set('typeId', '1');
                }
            $protocols = $this->Protocol->find('all');
            $protocolsList[' '] = 'Нет протокола';
            foreach ( $protocols as $protocol ) {
                $protocolsList[$protocol['Protocol']['id']] = $protocol['Protocol']['name']." (".$protocol['Protocol']['port'].")";
            }
            asort($protocolsList);
            $this->set('protocolsList', $protocolsList);
            //Если шаблон уже имеет протокол
                if (@$this->data['Protocol'][0]['id']) {
                    $this->set('protocolId', @$this->data['Protocol'][0]['id']);
                } else {
                    $this->set('protocolId', ' ');
                }

        } else {
            if ($this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Информация о сервере обновлена.','flash_success');
                $this->redirect(array('action' => 'control'));
            }
        }

    }

    public function addType() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Type');

        //Save the data
        if ($this->data) {

            if ($this->Type->save($this->data)) {
                $this->Session->setFlash('Тип серверов добавлен, теперь его можно выбирать при создании шаблона.', 'flash_success');
                $this->redirect(array('action' => 'control'));
            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
                $this->redirect(array('action' => 'control'));
            }
        }

    }

    public function addMod() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Mod');

        //Save the data
        if ($this->data) {

            if ($this->Mod->save($this->data)) {
                $this->Session->setFlash('Мод добавлен, теперь привяжите к нему плагины, а сам мод к шаблону.', 'flash_success');
                $this->redirect(array('action' => 'control'));
            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
                $this->redirect(array('action' => 'control'));
            }
        }

    }

    public function addPlugin() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Plugin');

        //Save the data
        if ($this->data) {
            if ($this->Plugin->save($this->data)) {
                $this->Session->setFlash('Плагин добавлен, теперь привяжите его к моду и шаблонам.', 'flash_success');
                $this->redirect(array('action' => 'control'));
            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
                $this->redirect(array('action' => 'control'));
            }
        }

    }

    public function addConfig() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Config');

        //Save the data
        if ($this->data) {
            if ($this->Config->save($this->data)) {
                $this->Session->setFlash('Конфиг добавлен, теперь привяжите его к шаблонам.', 'flash_success');
                $this->redirect(array('action' => 'control'));
            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
                $this->redirect(array('action' => 'control'));
            }
        }

    }

    public function addMap() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Map');
        $this->loadModel('MapType');

        //Save the data
        if ($this->data) {

            if ( empty($this->data['Map']['map_image']['tmp_name'])
                    or
                ($this->data['Map']['map_image']['tmp_name'] !== null and $this->uploadMapImage())

                ) {

                if ($this->Map->save($this->data)) {

                    // Переименовать картинку под ID карты
                    $imagesPath = WWW_ROOT."/img/gameMaps/";
                    rename($imagesPath."/".$this->data['Map']['name'].'.jpg', $imagesPath.'/'.$this->Map->id.'.jpg');
                    rename($imagesPath."/".$this->data['Map']['name'].'_thumb.jpg', $imagesPath.'/'.$this->Map->id.'_thumb.jpg');

                    $this->Session->setFlash('Карта добавлена, теперь привяжите её к шаблону.', 'flash_success');
                } else {
                    $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
                }
            } else {
                //Сообщение об ошибке выводится из $this->uploadMapImage()
                //$this->Session->setFlash('Не удалось загрузить изображение карты!', 'flash_error');
            }

            $this->redirect($this->referer());

        } else {
            // Составить список типов карт
            $mapTypes = $this->MapType->find('all');
            $mapTypesList = array();
            foreach ( $mapTypes as $mapType ) {
                $mapTypesList[$mapType['MapType']['id']] = $mapType['MapType']['longname'];
            }

            $this->set('mapTypes', $mapTypesList);
        }

    }



    public function editMap( $id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Map');
        $this->loadModel('MapType');
        $this->loadModel('Type');

        // Сохранить данные карты
        if ($this->data) {

            if (!empty($this->data['Map']['GameTemplate'])) {
                $this->request->data['GameTemplate'] = $this->data['Map']['GameTemplate'];
            }

            if ($this->Map->save($this->data)) {

                $error = '';
                if ($this->data['Map']['map_image']['tmp_name'] !== null and $this->uploadMapImage()) {
                    // Переименовать картинку под ID карты
                    $imagesPath = WWW_ROOT."/img/gameMaps/";

                    // Удалить старую, если есть
                    if (file_exists($imagesPath.'/'.$this->Map->id.'.jpg')) {
                        if (!unlink($imagesPath.'/'.$this->Map->id.'.jpg')) {
                            $error  .= 'Не удалось удалить старое изображение карты. Проверьте права доступа';
                        }
                    }

                    if (file_exists($imagesPath.'/'.$this->Map->id.'_thumb.jpg')) {
                        if (!unlink($imagesPath.'/'.$this->Map->id.'_thumb.jpg')) {
                            $error  .= '<br/>Не удалось удалить старое малое изображение карты. Проверьте права доступа';
                        }
                    }

                    rename($imagesPath."/".$this->data['Map']['name'].'.jpg', $imagesPath.'/'.$this->Map->id.'.jpg');
                    rename($imagesPath."/".$this->data['Map']['name'].'_thumb.jpg', $imagesPath.'/'.$this->Map->id.'_thumb.jpg');

                }

                if ($error !== '') {
                    $this->Session->setFlash('Карта изменена успешно. Но возникли ошибки при обновлени изображений: <br/>'.$error, 'flash_error');
                } else {
                    $this->Session->setFlash('Карта изменена успешно.', 'flash_success');
                }

            } else {
                $this->Session->setFlash('Возникла ошибка:<br/>'.mysql_error(), 'flash_error');
            }

            $this->redirect($this->referer());

        } else {
            if ($id !== null) {

                // Составить список типов карт
                $mapTypes = $this->MapType->find('all');
                $mapTypesList = array();
                foreach ( $mapTypes as $mapType ) {
                    $mapTypesList[$mapType['MapType']['id']] = $mapType['MapType']['longname'];
                }

                $this->set('mapTypes', $mapTypesList);

                // Составить список игр

                // Нефиг запрашивать лишнюю информацию из базы
                $this->Type->bindModel(array(
                                            'hasAndBelongsToMany' => array(
                                                                'GameTemplate' => array('fields' => 'id, name, longname' )
                                                    )));
                // Исключить голосовые и т.д.
                $types = $this->Type->find('all', array( 'conditions' => array ( 'id not' => array(2, 3, 4) )));

                $gameList = array();
                foreach ( $types as $type ) {

                    foreach ( $type['GameTemplate'] as $game ) {

                        $gameList[$game['id']] = $game['longname'];

                    }

                }

                asort($gameList);
                $this->set('gameList', $gameList);

                $this->Map->id = $id;
                $this->request->data = $this->Map->read();

            } else {
                $this->Session->setFlash('Не указана карта.', 'flash_error');
            }
        }

    }

    public function commonMapList() {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Map');

        $maps = $this->Map->find('all');
        $this->request->data['MapList']['none'] = array();
        foreach ( $maps as $map ) {

            // Проверить на наличие изображения карты
            if (file_exists(WWW_ROOT.'/img/gameMaps/'.$map['Map']['id'].'.jpg')) {
                $map['Map']['image'] = true;
            } else {
                $map['Map']['image'] = false;
            }

            if (!empty($map['GameTemplate'][0]['name'])) {
                $this->request->data['MapList'][$map['GameTemplate'][0]['name']][] = $map;
            } else {
                $this->request->data['MapList']['none'][] = $map;
            }

        }
    }

    public function linkMapToTemplate($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));

        // Нефиг запрашивать лишнюю информацию из базы
        $this->GameTemplate->unbindModel( array( 'hasAndBelongsToMany' => array(
                                                                    'Mod',
                                                                    'Plugin',
                                                                    'Config',
                                                                    'Service',
                                                                    'Server'
                                                        )));

        $this->loadModel('GameTemplateMap');

        //Save the association
        if (!empty($this->request->data)) {
            if (@$this->GameTemplateMap->save($this->data)) {
                $this->Session->setFlash('Карта к шаблону добавлена.', 'flash_success');
            } else {
                $this->Session->setFlash('Ошибка при добавлении карт к шаблону: '.mysql_error(), 'flash_error');
            }
            $this->redirect(array('action' => 'control'));
        } else {

                $this->GameTemplateMap->id = $id;
                $this->request->data = $this->GameTemplateMap->read();

                //********************************************************
                // Надо сформировать список карт, которые уже привязаны к другим шаблонам
                $gameTemplates =  $this->GameTemplateMap->find('all');
                $gameTemplatesMaps = array();
                $GameTemplateMaps  = array();
                // Предварительно составить список ID карт, привязанных к текущему шаблону
                if (!empty($this->data['Map'])) {
                    foreach ( $this->data['Map'] as $map ) {
                        $GameTemplateMaps[] = $map['id'];
                    }
                }

                // Есть сходные шаблоны, у которых карты одинаковые
                // Для них надо получить список карт для парного шаблона
                // Для CSSv34 это CSS и наоборот
                $twinGameTemplateMaps = array();
                if ($this->data['GameTemplateMap']['name'] === 'css'
                        or
                    $this->data['GameTemplateMap']['name'] === 'cssv34'
                    ) {

                        switch ( $this->data['GameTemplateMap']['name'] ) {
                            case 'css':
                            $this->GameTemplateMap->id = 29;
                            $twinGameTemplate = $this->GameTemplateMap->read();
                            break;

                        case 'cssv34':
                            $this->GameTemplateMap->id = 21;
                            $twinGameTemplate = $this->GameTemplateMap->read();
                            break;

                        default:
                            break;
                        }

                        if (!empty($twinGameTemplate['Map'])) {
                            foreach ( $twinGameTemplate['Map'] as $map ) {
                                $twinGameTemplateMaps[] = $map['id'];
                            }
                        }

                    }

                foreach ( $gameTemplates as $gameTemplate ) {
                    if (!empty($gameTemplate['Map'])) {
                        foreach ( $gameTemplate['Map'] as $map ) {
                            $gameTemplatesMaps[] = $map['id'];
                        }
                    }

                }

                // Теперь убрать из последнего списка те карты,
                // которые привязаны к текущему шаблону
                $gameTemplatesMaps = array_diff($gameTemplatesMaps, $GameTemplateMaps, $twinGameTemplateMaps);

                //********************************************************
                //берем полный список карт
                $this->loadModel('Map');
                $this->Map->unbindModel( array( 'belongsTo' => array(
                                                                                    'MapType'
                                                                        )));
                $maps = $this->Map->find('all', array('recursive' => 0));

                //pr($maps);
                foreach ( $maps as $map ) {
                    // Массив многомерный, потому array_diff не работает
                    if (!in_array($map['Map']['id'], $gameTemplatesMaps)) {
                        $mapsList[$map['Map']['id']] = $map['Map']['longname'].' ['.$map['Map']['name'].']';
                    }
                }
                $this->set('mapsList', @$mapsList);

                // Конец выбора карт
                //********************************************************

        }
    }

    public function linkModToTemplate($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Mod');

        //Save the association
        //pr($this->data);
        if ($this->data) {
            if (@$this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Мод к шаблону добавлен. Теперь привяжите доступные плагины.', 'flash_success');
                $this->redirect(array('action' => 'linkPluginToTemplate', $this->GameTemplate->id));
            }
        } else {
                // Нефиг запрашивать лишнюю информацию из базы
                $this->GameTemplate->unbindModel( array( 'hasAndBelongsToMany' => array(
                                                                            'Map',
                                                                            'Plugin',
                                                                            'Config',
                                                                            'Service',
                                                                            'Server'
                                                                )));
                $this->GameTemplate->id = $id;
                $this->request->data = $this->GameTemplate->read();
                //********************************************************
                //берем полный список модов серверов
                $mods = $this->Mod->find('all', array('recursive' => 0));
                foreach ( $mods as $mod ) {
                    $modsList[$mod['Mod']['id']] = $mod['Mod']['longname'].' '.$mod['Mod']['version'];
                }

                natcasesort($modsList);
                $this->set('modsList', $modsList);

                // Конец выбора модов серверов
                //********************************************************

        }
    }

    public function linkPluginToTemplate($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Mod');
        $this->loadModel('Plugin');

        //Save the association
        if ($this->data) {
            if (@$this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Плагин к шаблону добавлен', 'flash_success');
                $this->redirect(array('action' => 'control'));
            }
        } else {
                $this->GameTemplate->id = $id;
                $this->request->data = $this->GameTemplate->read();
                //********************************************************
                // создадим список плагинов, которые привязаны к моду или модам
                $pluginsList = array();
                $mods = $this->data['Mod'];

                foreach ( $mods as $mod ) {
                    $this->Mod->id = $mod['id'];
                    $mod = $this->Mod->read();

                    foreach ( $mod['Plugin'] as $plugin ) {

                        $pluginsList += array ($plugin['id'] => $plugin['longname']." ".$plugin['version']);

                    }

                }
                natcasesort($pluginsList);
                $this->set('pluginsList', $pluginsList);
                //Если шаблон уже имеет привязанные плагины
                if (@$this->data['Plugin'][0]['id']) {
                    $this->set('PluginsId', @$this->data['Plugin'][0]['id']);
                }

                // Конец выбора плагинов серверов
                //********************************************************

        }
    }

    public function linkConfigToTemplate($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Config');

        //Save the association
        if ($this->data) {
            if (@$this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Конфиги к шаблону добавлены..', 'flash_success');
                $this->redirect(array('action' => 'control'));
            }
        } else {
                $this->GameTemplate->id = $id;
                $this->request->data = $this->GameTemplate->read();
                //********************************************************
                //берем полный список конфигов
                $cfgs = $this->Config->find('all');
                foreach ( $cfgs as $cfg ) {
                    $cfgsList[$cfg['Config']['id']] = $cfg['Config']['path']."/".$cfg['Config']['name'];
                }
                natcasesort($cfgsList);
                $this->set('cfgsList', $cfgsList);

                // Конец выбора конфигов
                //********************************************************

        }
    }

    public function linkServiceToTemplate($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));

        //Save the association
        //pr($this->data);
        if ($this->data) {
            if (@$this->GameTemplate->save($this->data)) {
                $this->Session->setFlash('Услуги к шаблону добавлены..', 'flash_success');
                $this->redirect(array('action' => 'control'));
            }
        } else {
                $this->GameTemplate->id = $id;
                $this->request->data = $this->GameTemplate->read();
                //********************************************************
                //берем полный список конфигов
                $services = $this->GameTemplate->Service->find('all');
                foreach ( $services as $service ) {
                    $servicesList[$service['Service']['id']] = $service['Service']['longname'];
                }
                $this->set('servicesList', $servicesList);

                // Конец выбора конфигов
                //********************************************************

        }
    }

    public function linkPluginToMod($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Mod');
        $this->loadModel('Plugin');
        $this->loadModel('Config');

        //Save the association

        if ($this->data) {
            if ($this->Mod->save($this->data)) {
                $this->Session->setFlash('Привязка плагинов к моду обновлена', 'flash_success');
                $this->redirect(array('action' => 'control'));
            }
        } elseif ($id) {
                $this->Mod->id = $id;
                $this->request->data = $this->Mod->read();
                //********************************************************
                //берем полный список плагинов
                $plugins = $this->Plugin->find('all');;
                foreach ( $plugins as $plugin ) {

                        $pluginsList[$plugin['Plugin']['id']] = $plugin['Plugin']['longname']." ".$plugin['Plugin']['version'];

                    }

                natcasesort($pluginsList);
                $this->set('pluginsList', $pluginsList);

                //Если мод уже имеет привязку плагинов
                if (@$this->data['Plugin'][0]['id']) {
                    $this->set('pluginsId', @$this->data['Plugin'][0]['id']);
                }

                // Конец выбора плагинов
                //********************************************************
                //берем полный список конфигов
                $cfgs = $this->Config->find('all');
                foreach ( $cfgs as $cfg ) {
                    $configsList[$cfg['Config']['id']] = $cfg['Config']['path']."/".$cfg['Config']['name'];
                }
                natcasesort($configsList);
                $this->set('configsList', $configsList);

                //Если мод уже имеет привязку конфигов
                if (@$this->data['Config'][0]['id']) {
                    $this->set('configsId', @$this->data['Config'][0]['id']);
                }

                // Конец выбора конфигов
                //********************************************************

        } else {
            $this->Session->setFlash('Не указан ID мода', 'flash_error');
        }
    }

    public function linkConfigAndTagToPlugin($id = null) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->loadModel('Plugin');

        //Save the association
        if ($this->data) {
            if (!empty($this->data['Tag']['tags'])) {
                $tags = split(',', $this->data['Tag']['tags']);
                foreach ( $tags as $tag ) {
                    $tag = trim($tag);
                    if ($tag !== '') {
                        $tagsList[] = $tag;
                    }
                }
                $tags = array_unique($tagsList);
                unset($this->data['Tag']['tags']);
                $this->Plugin->Tag->unbindModel(array(
                                                    'hasAndBelongsToMany' => array('Plugin' )),
                                                false);
                // Получим из базы уже существующие тэги
                $savedTags = $this->Plugin->Tag->find('all', array('conditions' => array ('name' => $tags)));

                // Сохранить введеные теги в отдельной переменной
                $inputTags = $tags;

                // Удалим из полученной строки тэги, которые уже есть в базе
                if (!empty($savedTags)) {
                    foreach ( $savedTags as $tag ) {
                        $tagId = array_search($tag['Tag']['name'], $tags);
                        if (@$tagId !== false) {
                            unset($tags[$tagId]);
                        }

                    }
                }

                // Подготовить данные для сохранения
                if (!empty($tags)) {
                    foreach ( $tags as $tag ) {
                        $tagsList['Tag'][] = array('name' => $tag);
                    }

                    if ($this->Plugin->Tag->saveAll($tagsList['Tag'])) {
                        // Повторно запросить полный спискок тэгов
                        $savedTags = $this->Plugin->Tag->find('all', array('conditions' => array ('name' => $inputTags)));
                    } else {
                        $this->Session->setFlash('Возникла ошибка сохранения тэгов: '.mysql_error(), 'flash_error');
                    }
                }

                // Переберём итоговый список и подготовим его
                // для привязки к плагину

                foreach ( $savedTags as $tag ) {
                    $tagsIds['Tag'][] = $tag['Tag']['id'];
                }

                // Теперь получить текущий список тэгов плагина
                $this->Plugin->unbindModel(array(
                                            'hasAndBelongsToMany' => array('Config' )));
                $this->Plugin->id = $this->data['Plugin']['id'];
                $plugin = $this->Plugin->read();

                foreach ( $plugin['Tag'] as $tag ) {
                    $tagsIds['Tag'][] = $tag['id'];
                }

                // Убрать повторяющиеся элементы
                $tagsIds = array_unique($tagsIds);

                // Внести итоговый массив в данные для сохранения
                $this->request->data['Tag'] = $tagsIds;

            }

            if ($this->Plugin->save($this->data)) {
                $this->Session->setFlash('Привязка конфигов и тэгов к плагину обновлена', 'flash_success');
                $this->redirect(array('action' => 'control'));
            }
        } elseif ($id) {
                $this->Plugin->id = $id;
                $this->request->data = $this->Plugin->read();
                $plugin = $this->data;
                //берем полный список конфигов
                $cfgs = $this->Plugin->Config->find('all');
                foreach ( $cfgs as $cfg ) {
                    $configsList[$cfg['Config']['id']] = $cfg['Config']['path']."/".$cfg['Config']['name'];
                }

                natcasesort($configsList);

                $this->set('configsList', $configsList);
                $this->set('tagsList', $this->Plugin->Tag->find('list'));
                //Если плагин уже имеет привязку конфигов
                if (@$plugin['Config'][0]['id']) {
                    $this->set('configsId', @$plugin['Config'][0]['id']);
                }

                // Конец выбора конфигов
                //********************************************************

        } else {
            $this->Session->setFlash('Не указан ID мода', 'flash_error');
        }
    }

    public function delete($id) {
        $this->DarkAuth->requiresAuth(array('Admin'));
        $this->GameTemplate->delete($id);
        $this->Session->setFlash('Сервер #'.$id.' удалён.');
        $this->redirect(array('action'=>'control'));
    }

    public function getMods( $id = null) {
            $this->layout = 'ajax';
            if (!$id) {
                if (empty($this->params['url']['templateId'])) {
                    $id = $this->data['GameTemplate']['id'];
                } else {
                    $id = $this->params['url']['templateId'];
                }
            }
            $this->GameTemplate->id = $id;
            $this->request->data = $this->GameTemplate->read(array('id','longname'));
            //********************************************************
            //Составляем список вариантов

            if ( !empty($this->data['Mod']) ) {
                    $modsList = array();
                    foreach ($this->data['Mod'] as $mod):

                    $modsList += array ($mod['id']=>$mod['longname']." ".$mod['version']);

                    endforeach;
                    // Для COD4 с фиксированными слотами мод устанавливать обязательно
                    if ($id != 27) {
                        $modsList += array(""=>"Не устанавливать");
                    }

                    $this->set('modsList', $modsList);
            } else {
                    $modsList = array("0"=>"Нет модов");
                    $this->set('modsList', $modsList);
            }

    }

        public function getTemplates( $id = null) {
                $this->layout = 'ajax';

                if (!$id) {
                    if (empty($this->params['url']['typeId'])) {
                        $id = $this->data['Type']['id'];
                    } else {
                        $id = $this->params['url']['typeId'];
                    }

                }

                $this->GameTemplate->Type->id = $id;
                $this->request->data = $this->GameTemplate->Type->read( array( 'id','longname' ));

                //********************************************************
                //Составляем список вариантов

                if ( !empty($this->data['GameTemplate']) ) {

                        $templatesList = array();

                        foreach ($this->data['GameTemplate'] as $gameTemplate):

                            if ($gameTemplate['active'] == 1) {
                                $templatesList += array($gameTemplate['id']=>$gameTemplate['longname']);
                            }

                        endforeach;

                        asort($templatesList);
                } else {
                        $templatesList = array("0"=>"Нет модов/плагинов");
                }

                $this->set('templatesList', $templatesList);

    }

}
?>
