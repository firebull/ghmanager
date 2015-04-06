<?php

/*
 * Created on 05.05.2009
 *
 */

App::uses('AppModel', 'Model');

class User extends AppModel {

	public $useTable = 'users';
	public $actsAs = ['Containable'];

	public $hasAndBelongsToMany = array (
		'Group' => array (
			'className' => 'Group',
			'joinTable' => 'groups_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'unique' => true,
			'fields' => 'id,live,name,desc'
		),
		'Server' => array (
			'className' => 'Server',
			'joinTable' => 'servers_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'fields'=> array('id','initialised')
		),
		'SupportTicket' => array(
			'className' => 'SupportTicket',
			'joinTable' => 'support_tickets_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'support_ticket_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id,status,title,created',
			'order' => ' status DESC, created DESC',
			'group' => '',
			'limit' => '100',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	public $hasMany = array(
		'Eac' => array(
			'className' => 'Eac',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $validate = array (
						'username' => array (

									'username-rule-1' => array (
														'rule' => 'alphaNumeric',
														'message' => 'Логин может содержать только буквы и цифры.'
														),

									'username-rule-2' => array (
														'rule' => array ('minLength', 5),
														'message' => 'Минимальная длина логина 5 символов.'
														),

									'username-rule-3' => array (
														'rule' => 'isUnique',
														'message' => 'Такой логин уже существует.'
														)
											),

						'passwd' => array (
									'password-rule-1' => array (
														'rule' => array('between', 7, 32),
														'message' => 'Длина пароля должна быть от 7 до 32 символов.'
														),
//									 'password-rule-2' => array (
//														'rule' => array('validatePassword'),
//														'message' => 'Пароли не совпадают.'
//														)
											),

						'email' => array(
								   'email-rule-1' => array (
												     'rule' => array('email', true),
												     'message' => 'Введите корректный e-mail.'
											),
									'email-rule-2' => array (
														'rule' => 'isUnique',
														'message' => 'Такой e-mail уже существует. Попробуйте восстановить пароль существующего пользователя.'
														)
										),
						'phone' => array(
								    'phone-rule-1' => array (
												     'rule' => array('phone', '/^((\+?7|8)(?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[07])\d{8}|\+?(99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/'),
												     'message' => 'Введите корректный номер телефона: только цифры с кодом страны. Допустимы только страны СНГ.'
											),
									'phone-rule-2' => array (
														'rule' => 'isUnique',
														'message' => 'Такой номер телефона уже привязан к другому логину. Попробуйте восстановить пароль существующего пользователя.'
														)
										),
						'steam_id' => array(
									        'rule' => '/^STEAM_[0-9]:[0-9]:[0-9]{3,18}$/',
									        'message' => 'Steam ID должен быть вида STEAM_0:0:123456',
									        'allowEmpty' => true
									    )

						);

	public function validatePassword() {
				//only run if there are two password feield (like NOT on the contact or signin pages..)
				if ( isset($this->data['User']['confirmpassword']) ) {

						if ($this->data['User']['passwd'] != $this->data['User']['confirmpassword']) {
						//die(‘you fail’);
						$this -> invalidate('checkpassword');
						//they didnt condifrm password
						return false;

						} elseif ($this->data['User']['newpasswd'] === '' && $this->data['User']['confirmpassword'] === '') {

						$this->data['User']['passwd']=$this->exist['User']['passwd'];
						return true;
						} else {
						$this->data['User']['passwd']=md5('team'.$this->data['User']['passwd'].'server'.'09ds0909d0hf');
						return true;
						}
				}

			}
	/*			 
	This function determines visitor browser.
	*/
	public function getBrowser($user_agent) {
		$browsers = array(
			'Opera' => 'Opera',
			'Mozilla Firefox'=> '(Firebird)|(Firefox)',
			'Galeon' => 'Galeon',
			'Crome/Safari'=>'AppleWebKit',
			'MyIE'=>'MyIE',
			'Lynx' => 'Lynx',
			'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
			'Konqueror'=>'Konqueror',
			'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
			'IE9' => '(MSIE 9\.[0-9]+)',
			'IE8' => '(MSIE 8\.[0-9]+)',
			'IE7' => '(MSIE 7\.[0-9]+)',
			'IE6' => '(MSIE 6\.[0-9]+)',
			'IE5' => '(MSIE 5\.[0-9]+)',
			'IE4' => '(MSIE 4\.[0-9]+)'
		);

		foreach ($browsers as $browser => $pattern) {
				if (eregi($pattern, $user_agent)) {
					return $browser;
				}
			}
		return 'Unknown';
	}

	// Проверяем на корректность броузера,
	// для правильного отображения скриптов и т.д.
	public function isCorrectBrowser($user_agent = null) {
		$badBrowsersList = array (
										'Lynx',
										'SearchBot',
										'IE9',
										'IE8',
										'IE7',
										'IE6',
										'IE5',
										'IE4'

										);
		$clientBrowser = $this->User->getBrowser($user_agent);
		if (in_array ($clientBrowser, $badBrowsersList)) {
			return false;
		} else {
			return true;
		}

	}

}

class UserGroup extends AppModel {

	public $useTable = 'users';

	public $hasAndBelongsToMany = array (
		'Group' => array (
			'className' => 'Group',
			'joinTable' => 'groups_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'unique' => true,
			'fields' => 'id,live,name,desc'
		)
	);
}

class UserServersId extends AppModel {

	public $useTable = 'users';

	public $hasAndBelongsToMany = array (
		'Server' => array (
			'className' => 'Server',
			'joinTable' => 'servers_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'fields'=> 'id,name'
		)
	);
}
class UserOrder extends AppModel {

	public $useTable = 'users';

	public $hasMany = array (
		'Order' => array (
			'className' => 'Order',
			'unique' => true,
			'order' => ' payed ASC, created DESC',
		)
	);
}

class UserJournal extends AppModel {

	public $useTable = 'users';

	public $hasMany = array (
		'Actions' => array (
			'className' => 'Actions',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'limit' => '50',
			'order' => ' created DESC'
		)
	);
}

?>
