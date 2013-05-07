<?php
/*
 * Created on 29.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 class Order extends AppModel {
 	
 	public $name = 'Order';
    
    public $hasAndBelongsToMany = array (
		'User' => array (
			'className' => 'User',
			'joinTable' => 'orders_users',
			'foreignKey' => 'order_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields'=> 'id,first_name,second_name,username,steam_id,guid,email'
		),
		'Server' => array (
			'className' => 'Server',
			'joinTable' => 'orders_servers',
			'foreignKey' => 'order_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'fields'=> 'id,name,initialised'
		)
	);
	
	public $validate = array (
						'sum' => array (

									'more-then-0' => array (
														'rule' => array('comparison', '>', 0),
														'message' => 'Сумма должна быть больше нуля.'
														),

									'less-then-1000000' => array (
														'rule' => array('comparison', '<', 1000000),
														'message' => 'Больше мильона? Ого!'
														)
											) ,
						'month' => array (

									'more-then-1' => array (
														'rule' => array('comparison', '>=', 1),
														'message' => 'Аренда сервера от 1 месяца.'
														),

									'less-then-6' => array (
														'rule' => array('comparison', '<=', 12),
														'message' => 'Аренда сервера только до 12 месяцев.'
														)
											)	




						);
	
 		
 }
 
 class OrderClean extends AppModel {
 	
 	public $name = 'OrderClean';
    public $useTable = 'orders';
	
	public $hasAndBelongsToMany = array (
		'User' => array (
			'className' => 'User',
			'joinTable' => 'orders_users',
			'foreignKey' => 'order_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields'=> 'id,username,email'
		)
	);

	public $validate = array (
						'sum' => array (

									'more-then-0' => array (
														'rule' => array('comparison', '>', 0),
														'message' => 'Сумма должна быть больше нуля.'
														),

									'less-then-1000000' => array (
														'rule' => array('comparison', '<', 1000000),
														'message' => 'Больше мильона? Ого!'
														)
											) ,
						'month' => array (

									'more-then-1' => array (
														'rule' => array('comparison', '>=', 1),
														'message' => 'Аренда сервера от 1 месяца.'
														),

									'less-then-6' => array (
														'rule' => array('comparison', '<=', 12),
														'message' => 'Аренда сервера только до 6 месяцев.'
														)
											)	




						);
	
 		
 }
 
?>
