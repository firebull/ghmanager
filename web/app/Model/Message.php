<?php
class Message extends AppModel {
	public $name = 'Message';
	public $displayField = 'title';

	public $validate = array(
				'title' => array(
					'notempty' => array(
						'rule' => array('notempty'),
						'message' => 'Header cannot be empty',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
				),
	);

//	public $hasAndBelongsToMany = array (
//
//			'User' => array (
//				'className' => 'User',
//				'joinTable' => 'messages_users',
//				'foreignKey' => 'user_id',
//				'associationForeignKey' => 'message_id',
//				'fields' => 'id',
//				'unique' => true
//			)
//
//	);
}
?>
