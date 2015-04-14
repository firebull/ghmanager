<?php
class VoiceMumbleParam extends AppModel {
	public $name = 'VoiceMumbleParam';
	public $displayField = 'welcometext';
	public $validate = array(
		'autobanAttempts' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'autobanTimeframe' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'autobanTime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'serverpassword' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Only digits and letters allowed',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'bandwidth' => array(
			'numeric' => array(
				'rule' => array('comparison', 'less or equal', 131072),
				'message' => 'Maximum 128Kbit',
				//'allowEmpty' => false,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'textmessagelength' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'imagemessagelength' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allowhtml' => array(
			'boolean' => array(
				'rule' => array('inList', array('true','false')),
				'message' => 'Only true or false allowed',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'logdays' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Only digits allowed',
				//'allowEmpty' => true,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerPassword' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Only digits and letters allowed',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerUrl' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Must be hyperlink',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerHostname' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Only digits and letters allowed',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'certrequired' => array(
			'boolean' => array(
				'rule' => array('inList', array('true','false')),
				'message' => 'Only true or false allowed',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'Server' => array(
			'className' => 'Server',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id, address, port',
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
