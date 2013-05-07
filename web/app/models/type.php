<?php
/*
 * Created on 23.08.2010
 *
*/

class Type extends AppModel {

    public $name = 'type';

    public $useTable = 'types';
    
        public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_types',
			'foreignKey' => 'type_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true
		),
//		'Server' => array (
//			'className' => 'Server',
//			'joinTable' => 'servers_types',
//			'foreignKey' => 'type_id',
//			'associationForeignKey' => 'server_id',
//			'unique' => true
//		),
	);

}

?>
