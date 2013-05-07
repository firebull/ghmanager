<?php
class Protocol extends AppModel {
	public $name = 'Protocol';
	public $displayField = 'name';
//	public $validate = array(
//		'name' => array(
//			'alphanumeric' => array(
//				'rule' => array('alphanumeric'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'port' => array(
//			'numeric' => array(
//				'rule' => array('numeric'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//	);
	
	public $hasAndBelongsToMany = array(
		'GameTemplate' => array(
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_protocols',
			'foreignKey' => 'protocol_id',
			'associationForeignKey' => 'game_template_id',
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