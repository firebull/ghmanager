<?php
class Confirm extends AppModel {
	public $name = 'Confirm';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Server' => array(
			'className' => 'Server',
			'foreignKey' => 'server_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

}
