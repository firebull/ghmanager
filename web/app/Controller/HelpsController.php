<?php
/*

Helps controller.
Empty controller to print quick helps in panel.
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

class HelpsController extends AppController {

    public $name = 'Helps';

    public $layout = 'client';

    public $helpers = array('Time', 'Html', 'Js');
    public $components = array('RequestHandler', 'Session');
    public $_DarkAuth;

    public function view($id = null) {

        if (!empty($this->data['Help']['id'])) {
            $id = $this->data['Help']['id'];
        }

        $this->Help->id = $id;
        $this->set('help', $this->Help->read());

    }

}
?>
