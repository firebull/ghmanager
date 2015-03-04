<?php
/*
 * Created on 19.06.2010
 *
*/

class Plugin extends AppModel {

    public $name = 'plugin';

    public $useTable = 'plugins';
    
    public $hasAndBelongsToMany = array (

			'Config' => array (
				'className' => 'Config',
				'joinTable' => 'configs_plugins',
				'foreignKey' => 'plugin_id',
				'associationForeignKey' => 'config_id',
				'unique' => true,
				'fields' => 'id, name, shortDescription, path'
			),
			
			'Tag' => array (
				'className' => 'Tag',
				'joinTable' => 'plugins_tags',
				'foreignKey' => 'plugin_id',
				'associationForeignKey' => 'tag_id',
				'unique' => true
			)

	);

}

?>
