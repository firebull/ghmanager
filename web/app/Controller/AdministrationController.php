<?php
/*

Empty controller, used as router.
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
?>
<?php
class AdministrationController extends AppController {

	public $name = 'Administration';
	public $layout = 'client';
	public $_DarkAuth = array (
								'required' => array (
									'Admin',
									'GameAdmin',
									'OrdersAdmin'
								)
							);

	public $helpers = array (
		'Time',
		//'Ajax',
		//'Js',
		'Html'
	);

	public $components = array (
		'RequestHandler',
		'Session',
		'DarkAuth'
	);

	function beforeRender() {
		$this->set('userinfo', $this->DarkAuth->getAllUserInfo());
	}

	function index() {
		$this->redirect(array('controller'=>'servers','action' => 'control'));
	}


}
?>
