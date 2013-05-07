<?php
class Support extends AppModel {
	public $name = 'Support';
	public $displayField = 'text';
	public $validate = array(
		'text' => array(
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
		'SupportTicket' => array(
			'className' => 'SupportTicket',
			'joinTable' => 'supports_support_tickets',
			'foreignKey' => 'support_id',
			'associationForeignKey' => 'support_ticket_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>