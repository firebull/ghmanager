<?php
class Support extends AppModel {
	public $name = 'Support';
	public $displayField = 'text';
	public $validate = array(
		'text' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Can not be empty',
				'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public $belongsTo  = [
				'User' => [ 'className'  => 'User',
						    'conditions' => [],
						    'fields' => 'id',
						    'order'  => '',
							'counterCache' => [
				                'tickets_unread_count'   => ['Support.readstatus' => 'unread',
				                                             'Support.answerBy' => 'support']
				            ]
				],
				'SupportTicket' => [ 'className' => 'SupportTicket',
						    		 'conditions' => [],
						    		 'fields' => 'id, title',
						    		 'order' => '',
									 'counterCache' => [
									 	'supports_count'      => ['Support.text !=' => "''"],
				                		'unread_user_count'   => ['Support.readstatus' => 'unread',
				                		                          'Support.answerBy' => 'support'],
				                		'unread_admin_count'  => ['Support.readstatus' => 'unread',
				                		                          'Support.answerBy' => 'owner']
				            		]
				]
			];

}
?>
