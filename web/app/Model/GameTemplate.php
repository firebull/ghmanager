<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 */

App::uses('AppModel', 'Model');

 // Этот чистый класс, к которому можно
 // биндить другие модели налету
  class GameTemplateClean extends AppModel {

    public $name = 'GameTemplateClean';
    public $useTable = 'game_templates';

	}
 // Это класс со всеми связанными моделями
 class GameTemplate extends AppModel {

    public $name = 'GameTemplate';


    public $hasAndBelongsToMany = array (
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'game_templates_mods',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'Type' => array (
			'className' => 'Type',
			'joinTable' => 'game_templates_types',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'game_templates_plugins',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		),
		'Config' => array (
			'className' => 'Config',
			'joinTable' => 'configs_game_templates',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'config_id',
			'unique' => true
		),
		'Protocol' => array (
			'className' => 'Protocol',
			'joinTable' => 'game_templates_protocols',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'protocol_id',
			'unique' => true
		),
		'Service' => array (
			'className' => 'Service',
			'joinTable' => 'game_templates_services',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'service_id',
			'fields' => 'id, name, longname',
			'unique' => true
		)
	);

}

 class GameTemplateType extends AppModel {

    public $name = 'GameTemplateType';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Type' => array (
				'className' => 'Type',
				'joinTable' => 'game_templates_types',
				'foreignKey' => 'game_template_id',
				'associationForeignKey' => 'type_id',
				'unique' => true
			)
	);
}

 class GameTemplateProtocol extends AppModel {

    public $name = 'GameTemplateProtocol';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Protocol' => array (
			'className' => 'Protocol',
			'joinTable' => 'game_templates_protocols',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'protocol_id',
			'unique' => true
		),
	'Type' => array (
			'className' => 'Type',
			'joinTable' => 'game_templates_types',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
	'Service' => array (
			'className' => 'Service',
			'joinTable' => 'game_templates_services',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'service_id',
			'unique' => true
		)
	);


}

 class GameTemplateTypeMod extends AppModel {

    public $name = 'GameTemplateTypeMod';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Type' => array (
				'className' => 'Type',
				'joinTable' => 'game_templates_types',
				'foreignKey' => 'game_template_id',
				'associationForeignKey' => 'type_id',
				'unique' => true
			),
	'Mod' => array (
				'className' => 'Mod',
				'joinTable' => 'game_templates_mods',
				'foreignKey' => 'game_template_id',
				'associationForeignKey' => 'mod_id',
				'unique' => true
			)

	);

}

  class GameTemplatePlugin extends AppModel {

    public $name = 'GameTemplatePlugin';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'game_templates_plugins',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		)
	);


}

  class GameTemplateService extends AppModel {

    public $name = 'GameTemplateService';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Service' => array (
			'className' => 'Service',
			'joinTable' => 'game_templates_services',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'service_id',
			'unique' => true
		)
	);


}

  class GameTemplateMap extends AppModel {

    public $name = 'GameTemplateMap';
    public $useTable = 'game_templates';


    public $hasAndBelongsToMany = array (
	'Map' => array (
			'className' => 'Map',
			'joinTable' => 'game_templates_maps',
			'foreignKey' => 'game_template_id',
			'associationForeignKey' => 'map_id',
			'unique' => true
		)
	);


}

?>
