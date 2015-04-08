<?php
class MapType extends AppModel {
	public $name = 'MapType';
	public $displayField = 'name';
	public $actsAs = ['Containable'];

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
