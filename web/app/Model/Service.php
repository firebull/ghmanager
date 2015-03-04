<?php
class Service extends AppModel {
	public $name = 'Service';
	public $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasAndBelongsToMany = array(
		'Server' => array(
			'className' => 'Server',
			'joinTable' => 'servers_services',
			'foreignKey' => 'service_id',
			'associationForeignKey' => 'server_id',
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