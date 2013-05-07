<?php
class UsedService extends AppModel {
	public $name = 'UsedService';
	public $displayField = 'date_used';
	
	public $belongsTo = array(
		'Server' => array(
			'className' => 'Server',
			'foreignKey' => 'server_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>