<?php
class VoiceMumbleParam extends AppModel {
	public $name = 'VoiceMumbleParam';
	public $displayField = 'welcometext';
	public $validate = array(
		'autobanAttempts' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'autobanTimeframe' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'autobanTime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'serverpassword' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Могут быть только цифры и буквы',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'bandwidth' => array(
			'numeric' => array(
				'rule' => array('comparison', 'less or equal', 131072),
				'message' => 'Максимум 128Кбит',
				//'allowEmpty' => false,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'textmessagelength' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'imagemessagelength' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'allowhtml' => array(
			'boolean' => array(
				'rule' => array('inList',array('true','false')),
				'message' => 'Может быть только true или false',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'logdays' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => true,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerPassword' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Могут быть только цифры и буквы',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerUrl' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Может быть только ссылка',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'registerHostname' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Могут быть только цифры и буквы',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'certrequired' => array(
			'boolean' => array(
				'rule' => array('inList',array('true','false')),
				'message' => 'Может быть только true или false',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasAndBelongsToMany = array(
		'Server' => array(
			'className' => 'Server',
			'joinTable' => 'servers_voice_mumble_params',
			'foreignKey' => 'voice_mumble_param_id',
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

}
?>