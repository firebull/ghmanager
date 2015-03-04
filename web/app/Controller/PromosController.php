<?php

/*

Promo actions controller.
Create and manage promo-codes.
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

class PromosController extends AppController {

	public $name = 'Promos';

	public $layout = 'client';
	public $_DarkAuth;
	public $helpers = array (
		'Time',
		'Html',
		'Js' => array('Jquery')
	);

	public $components = array('RequestHandler', 'Session', 'DarkAuth');

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
		$this->DarkAuth->requiresAuth(array('Admin','GameAdmin','OrdersAdmin'));

		$this->data = $this->Promo->find('all', array ( 'conditions' => array( 'valid_through > NOW()' )));

	}

	public function add() {
		$this->layout = 'ajax';
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

		if ($this->data) {
			$promo = $this->data;
			/* Промо код может быть простым, одним словом,
			 * так и быть задан необходимым кол-м
			 * одноразовых кодов*/
			$this->loadModel('PromoWithCode');
			if (!empty($promo['PromoCode'][0]['code'])) { // простой код
				$promo['PromoCode'][0]['code'] = strtoupper($promo['PromoCode'][0]['code']);
			} elseif (!empty($promo['Promo']['number'])) // кол-то кодов, которое надо сгенерировать
			{
				$consonantes = 'ABCDEFGHIGKLMNOPQRSTUVWXYZ123456789';

				for ($i = 0; $i < intval($promo['Promo']['number']); $i++) {
					$r = '';
					for ($j = 0; $j < 8; $j++) {

							$r .= $consonantes{rand(0, strlen($consonantes) - 1)};
					}

					$codes[$i]['code'] = $r;
				}

				$promo['PromoCode'] = $codes;
			}
			if ($this->PromoWithCode->saveAll($promo)) {
				$this->Session->setFlash('Акция сохранена','flash_success');
			} else {
				$this->Session->setFlash('Не удалось сохранить акцию:'.mysql_error(),'flash_error');
			}

			$this->redirect(array('action'=>'control'));
		} else {
			$types = array('code' => 'Общий код', 'token' => 'Набор одноразовых кодов');
			$this->set('types', $types);
		}

	}

	// Проверка валидности кода
	// Возвращает скидку по коду
	public function checkCode( $code = null ) {
		$this->layout = 'ajax';
		$this->loadModel('PromoCode');
		$this->PromoCode->bindModel(array(
									'belongsTo' => array(
														'Promo' => array()
													)));
		$promo = $this-> PromoCode->find('first', array(
															'conditions' => array (
																					'used not' => '1',
																					'code' => $code,
																					'valid_through > NOW()'
																				   )));

		if ( !empty($promo) ) {
			$this->set('promo', array('discount' => $promo['Promo']['discount']));
		}

	}

	// Отображает все коды промо-акции
	public function viewCodes($id = null) {
		$this->layout = 'ajax';
		$this->DarkAuth->requiresAuth(array('Admin','GameAdmin','OrdersAdmin'));

		$this->loadModel('PromoWithCode');

		if ($id === null) {
			$id = $this->data['Promo']['id'];
		}

		$this->PromoWithCode->id = $id;
		$this->data = $this->PromoWithCode->read();

	}

	// Подарочная акция
	// Два дня плюс аренды всем пропалченным серверам
	// За каждые 10 полных слотов еще один день сверху
	// За каждые десять дней до конца аренды еще один день сверху
	// За каждый месяц с начала аренды еще один день сверху
	// Прописать в базу к серверу, сколько добавлено дней и когда
	// перестать отображать сообщение о подарке
	public function gift() {
		$this->layout = 'ajax';
		$this->DarkAuth->requiresAuth(array('Admin','OrdersAdmin'));

		if (!empty($this->data) && $this->data['Gift']['confirm'] == 1) {

			$this->loadModel('ServerClean');

			$servers = $this->ServerClean->find('all', array ( 'conditions' => array ( 'payedTill > NOW()' ),
															   'fields' => 'id, name, payedTill, slots, created'));

			foreach ( $servers as $id => $server ) {

				$slots = $server['ServerClean']['slots'];

				$giftForSlots = round($server['ServerClean']['slots']/10) * $this->data['Gift']['slots'];

				$payedTill = strtotime($server['ServerClean']['payedTill']);
				$daysLeft = ($payedTill - time())/86400;

				$giftForPayed = round($daysLeft/10) * $this->data['Gift']['week'];

				$created = strtotime($server['ServerClean']['created']);
				$daysCreated = (time() - $created)/86400;

				$giftForCreated = round($daysCreated/30) * $this->data['Gift']['past'];

				// Ограничим подарки =)
				if ( $giftForPayed > 7) {
					$giftForPayed = 7;
				}

				if ( $giftForCreated > 7) {
					$giftForCreated = 7;
				}

				$giftDays = $this->data['Gift']['common'] +
							$giftForPayed +
							$giftForCreated +
							$giftForSlots;

				$server['ServerClean']['giftExpires'] = date('Y-m-d H:i:s', mktime( 23,
																				    59,
																				    0,
																				    date('m', time()),
																				    date('d', time()) + 21,
																				    date('Y', time())));

				$server['ServerClean']['payedTill'] = date('Y-m-d H:i:s', mktime( date('H', $payedTill),
																				  date('i', $payedTill),
																				  0,
																				  date('m', $payedTill),
																				  date('d', $payedTill) + $giftDays,
																				  date('Y', $payedTill)));

				if (strtotime($server['ServerClean']['giftExpires']) > strtotime($server['ServerClean']['payedTill'])) {
					$server['ServerClean']['giftExpires'] = $server['ServerClean']['payedTill'];
				}

				$server['ServerClean']['giftDays'] = $giftDays;

				$newServers[$id]['ServerClean'] = $server['ServerClean'];

			}

			if ($this->ServerClean->saveAll($newServers)) {
				$this->Session->setFlash('Клиенты счастливы!', 'flash_success');
			} else {
				$this->Session->setFlash('Не удалось осчастливить клиентов:'.mysql_error(), 'flash_error');
			}

		} else {
			$this->request->data['Gift']['common'] = 2;
			$this->request->data['Gift']['slots']  = 1;
			$this->request->data['Gift']['week']   = 1;
			$this->request->data['Gift']['past']   = 1;
		}

	}

}
?>
