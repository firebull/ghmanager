<?php
class SupportTicket extends AppModel {
	public $name = 'SupportTicket';
	public $displayField = 'title';
	public $validate = array(
		'status' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasAndBelongsToMany = array(
		'Server' => array(
			'className' => 'Server',
			'joinTable' => 'servers_support_tickets',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Support' => array(
			'className' => 'Support',
			'joinTable' => 'supports_support_tickets',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'support_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id,readstatus,text,answerBy,answerByName,created',
			'order' => 'created ASC',
			'limit' => '100',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'User' => array(
			'className' => 'User',
			'joinTable' => 'support_tickets_users',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id,first_name,second_name,username,live,email,steam_id,guid,tokenhash,created',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
}
/*
 * Эта модель для получения только непрочитанных сообщений
 */
class SupportTicketUnread extends AppModel {
	public $name = 'SupportTicketUnread';
	public $displayField = 'title';
	public $useTable = 'support_tickets';
	
	public $hasAndBelongsToMany = array(

		'Support' => array(
			'className' => 'Support',
			'joinTable' => 'supports_support_tickets',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'support_id',
			'unique' => true,
			'conditions' => array ('readstatus' => 'unread'),
			'fields' => 'id,readstatus,text,answerBy,answerByName,created',
			'order' => 'created ASC',
			'limit' => '100',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}

/*
 * Эта модель для получения пяти последних сообщений
 */
class SupportTicketFiveLast extends AppModel {
	public $name = 'SupportTicketFiveLast';
	public $displayField = 'title';
	public $useTable = 'support_tickets';
	
	public $hasAndBelongsToMany = array(

		'Support' => array(
			'className' => 'Support',
			'joinTable' => 'supports_support_tickets',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'support_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id,readstatus,text,answerBy,answerByName,created',
			'order' => 'created DESC',
			'limit' => '5',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}

/*
 * Эта модель для получения ID только непрочитанных сообщений
 */
class SupportTicketUnreadId extends AppModel {
	public $name = 'SupportTicketUnreadId';
//	public $displayField = 'id';
	public $useTable = 'support_tickets';
	
	public $hasAndBelongsToMany = array(

		'Support' => array(
			'className' => 'Support',
			'joinTable' => 'supports_support_tickets',
			'foreignKey' => 'support_ticket_id',
			'associationForeignKey' => 'support_id',
			'unique' => true,
			'conditions' => array ('readstatus' => 'unread'),
			'fields' => 'id',
			'order' => 'created ASC',
			'limit' => '100',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}

?>