<?php
class Location extends AppModel {
	public $name = 'Location';
	public $displayField = 'name';
	public $actsAs = ['Containable'];

	public $hasAndBelongsToMany = array(
		'RootServer' => array(
			'className' => 'RootServer',
			'joinTable' => 'locations_root_servers',
			'foreignKey' => 'location_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('(slotsMax - slotsBought) DESC'),
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>
