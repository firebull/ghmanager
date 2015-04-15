<?php
class SupportTicket extends AppModel {
	public $name = 'SupportTicket';
	public $displayField = 'title';
	public $actsAs = ['Containable'];

	public $validate = array(
		'status' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Can not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'title' => array(
			'notempty' => [
				'rule' => ['notempty'],
				'message' => 'Can not be empty',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
			'length' => [
				'rule' => ['minLength', 6],
        		'message' => 'Title must be at least 6 characters long.'
			]
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
		)
	);

	public $hasMany = [
			'Support' => ['className' => 'Support',
						  'fields'    => 'id,readstatus,text,answerBy,answerByName,created',
						  'limit'     => 256, // =)
						  'order'     => 'created ASC'
						 ]
					];

	public $belongsTo  = [
			'User' => ['className' => 'User',
							'conditions' => [],
							'fields' => 'id,first_name,second_name,username,live,email,steam_id,guid,tokenhash,created',
							'order' => '',
			]
		];

}
/*
 * Эта модель для получения только непрочитанных сообщений
 */
class SupportTicketUnread extends AppModel {
	public $name = 'SupportTicketUnread';
	public $displayField = 'title';
	public $useTable = 'support_tickets';

	public $hasMany = array(

		'Support' => array(
			'conditions' => ['readstatus' => 'unread'],
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

	public $hasMany = array(

		'Support' => array(
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
	public $displayField = 'title';
	public $useTable = 'support_tickets';

	public $hasMany = array(

		'Support' => array(
			'className' => 'Support',
			'conditions' => ['readstatus' => 'unread'],
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
