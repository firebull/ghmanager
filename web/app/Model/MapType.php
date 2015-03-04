<?php
class MapType extends AppModel {
	public $name = 'MapType';
	public $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasMany = array(
		'Map' => array(
			'className' => 'Map',
			'foreignKey' => 'map_type_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => 'id,name,longname,desc,official',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
?>