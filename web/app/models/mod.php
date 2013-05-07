<?php
/*
 * Created on 19.06.2010
 *
*/

class Mod extends AppModel {

    public $name = 'mod';

    public $useTable = 'mods';
    
        public $hasAndBelongsToMany = array (

			'Plugin' => array (
				'className' => 'Plugin',
				'joinTable' => 'mods_plugins',
				'foreignKey' => 'mod_id',
				'associationForeignKey' => 'plugin_id',
				'unique' => true
			),
			'Config' => array (
				'className' => 'Config',
				'joinTable' => 'configs_mods',
				'foreignKey' => 'mod_id',
				'associationForeignKey' => 'config_id',
				'unique' => true,
				'fields' => 'id, name, shortDescription, path'
			)

	);

}

?>
