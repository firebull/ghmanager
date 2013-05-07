<?php
/*
 * Created on 22.05.2010
 *
 */


class Server extends AppModel {

    public $name = 'Server';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		),
		'Type' => array (
			'className' => 'Type',
			'joinTable' => 'servers_types',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true
		),
		'RootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'servers_root_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		),
		'Service' => array (
			'className' => 'Service',
			'joinTable' => 'servers_services',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'service_id',
			'unique' => true
		),
		'Order' => array (
			'className' => 'order',
			'joinTable' => 'orders_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'order_id',
			'unique' => true
		),
		'User' => array (
			'className' => 'User',
			'joinTable' => 'servers_users',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields' => 'id,first_name,second_name,username,email,steam_id,guid,tokenhash,money'
		),
		'VoiceMumbleParam' => array (
			'className' => 'VoiceMumbleParam',
			'joinTable' => 'servers_voice_mumble_params',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'voice_mumble_param_id',
			'unique' => true
		),
		'RadioShoutcastParam' => array (
			'className' => 'RadioShoutcastParam',
			'joinTable' => 'radio_shoutcast_params_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'radio_shoutcast_param_id',
			'unique' => true
		)
	);

}
// Модель, для работы в других контроллерах
class ServerComp extends AppModel {

    public $name = 'ServerComp';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'ServerMod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'ServerPlugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		),
		'ServerType' => array (
			'className' => 'Type',
			'joinTable' => 'servers_types',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'ServerGameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true
		),
		'ServerLocation' => array (
			'className' => 'Location',
			'joinTable' => 'locations_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'location_id',
			'unique' => true
		),
		'ServerRootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'servers_root_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		),
		'ServerService' => array (
			'className' => 'Service',
			'joinTable' => 'servers_services',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'service_id',
			'unique' => true
		),
		'ServerOrder' => array (
			'className' => 'order',
			'joinTable' => 'orders_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'order_id',
			'unique' => true
		),
		'ServerUser' => array (
			'className' => 'User',
			'joinTable' => 'servers_users',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields'=> 'id,first_name,second_name,username,steam_id,guid,email'
		),
		'ServerVoiceMumbleParam' => array (
			'className' => 'VoiceMumbleParam',
			'joinTable' => 'servers_voice_mumble_params',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'voice_mumble_param_id',
			'unique' => true
		),
		'ServerRadioShoutcastParam' => array (
			'className' => 'RadioShoutcastParam',
			'joinTable' => 'radio_shoutcast_params_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'radio_shoutcast_param_id',
			'unique' => true
		)
	);

}

/* Модель, определяющая основные парамерты сервера */
class ServerCore extends AppModel {

    public $name = 'ServerCore';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
    	'Type' => array (
			'className' => 'Type',
			'joinTable' => 'servers_types',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true
		),
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		)
	);

}

/* В этой модели будем хранить данные серверов,
 * которые временно перенесём, когда клиент
 * захочет сменить игру сервера.
 */
class ServerStore extends AppModel {

    public $name = 'ServerStore';
    public $useTable     = 'server_stores';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
    	'Type' => array (
			'className' => 'Type',
			'joinTable' => 'server_stores_types',
			'foreignKey' => 'server_store_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_server_stores',
			'foreignKey' => 'server_store_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true
		),
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_server_stores',
			'foreignKey' => 'server_store_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_server_stores',
			'foreignKey' => 'server_store_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		),
		'RootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'server_stores_root_servers',
			'foreignKey' => 'server_store_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		)
	);

}

class ServerBelong extends AppModel {

    public $name = 'ServerBelong';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'Type' => array (
			'className' => 'Type',
			'joinTable' => 'servers_types',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		),
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id,name, longname, slots_min, slots_max, price, pricePrivatePassword, pricePrivatePower'
		),
		'RootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'servers_root_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		),
		'User' => array (
			'className' => 'User',
			'joinTable' => 'servers_users',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields'=> 'id,first_name,second_name,username,steam_id,guid,email'
		)

	);

}

class ServerModPlugin extends AppModel {

    public $name = 'ServerModPlugin';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		),
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		)
	);

}

class ServerMod extends AppModel {

    public $name = 'ServerMod';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Mod' => array (
			'className' => 'Mod',
			'joinTable' => 'mods_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'mod_id',
			'unique' => true
		)
	);

}

class ServerPlugin extends AppModel {

    public $name = 'ServerPlugin';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true
		)
	);

}

class ServerType extends AppModel {

    public $name = 'ServerType';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'Type' => array (
			'className' => 'Type',
			'joinTable' => 'servers_types',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'type_id',
			'unique' => true
		)

	);
}

class ServerTemplate extends AppModel {

    public $name = 'ServerTemplate';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id, name, longname, current_version, slots_min, slots_max'
		)

	);

}

class ServerTemplateUser extends AppModel {

    public $name = 'ServerTemplateUser';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id,name,longname,slots_min,slots_max,price, pricePrivatePassword, pricePrivatePower'
		),
		'User' => array (
			'className' => 'User',
			'joinTable' => 'servers_users',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'user_id',
			'unique' => true,
			'fields'=> 'id,first_name,second_name,username,steam_id,guid,email,money,discount'
		)

	);

}

class ServerTemplateProtocol extends AppModel {

    public $name = 'ServerTemplateProtocol';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplateProtocol',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id,name,longname,current_version'

		)

	);

}

class ServerService extends AppModel {

    public $name = 'ServerService';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Service' => array (
			'className' => 'Service',
			'joinTable' => 'servers_services',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'service_id',
			'unique' => true
		)
	);
	// В этой базе хрянится время, когда была использована услуга.
	// Требуется для услуг, на которые наложены временнЫе ограничения.
	public $hasMany = array(
							'UsedServices'
							);

}

class ServerRootserver extends AppModel {

    public $name = 'ServerRootserver';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'Location' => array (
			'className' => 'Location',
			'joinTable' => 'locations_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'location_id',
			'unique' => true
		),
		'RootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'servers_root_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		)
	);

}

class ServerClean extends AppModel {

    public $name = 'ServerClean';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id, name, longname'
		)

	);

}

class ServerEac extends AppModel {

    public $name = 'ServerEac';
    public $useTable = 'servers';
    public $displayField = 'name';

	public $hasAndBelongsToMany = array (
		'GameTemplate' => array (
			'className' => 'GameTemplate',
			'joinTable' => 'game_templates_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'game_template_id',
			'unique' => true,
			'fields' =>'id, name, longname'
		)

	);

	public $hasOne = array(
		'Eac' => array(
			'className' => 'Eac',
			'foreignKey' => 'server_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}

class ServerPluginId extends AppModel {

    public $name = 'ServerPluginId';
    public $useTable = 'servers';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array (

		'Plugin' => array (
			'className' => 'Plugin',
			'joinTable' => 'plugins_servers',
			'foreignKey' => 'server_id',
			'associationForeignKey' => 'plugin_id',
			'unique' => true,
			'fields' => 'id'
		)
	);

	/*
	 * Функция для проверки наличия значения в мульти-массивах
	 * Спасибо cousinka@gmail.com за алгоритм!
	 */
	function in_multiarray($elem, $array)
    {
        // if the $array is an array or is an object
         if( is_array( $array ) || is_object( $array ) )
         {
             // if $elem is in $array object
             if( is_object( $array ) )
             {
                 $temp_array = get_object_vars( $array );
                 if( in_array( $elem, $temp_array ) )
                     return TRUE;
             }

             // if $elem is in $array return true
             if( is_array( $array ) && in_array( $elem, $array ) )
                 return TRUE;


             // if $elem isn't in $array, then check foreach element
             foreach( $array as $array_element )
             {
                 // if $array_element is an array or is an object call the in_multiarray function to this element
                 // if in_multiarray returns TRUE, than return is in array, else check next element
                 if( ( is_array( $array_element ) || is_object( $array_element ) ) && $this->in_multiarray( $elem, $array_element ) )
                 {
                     return TRUE;
                     exit;
                 }
             }
         }

         // if isn't in array return FALSE
         return FALSE;
    }


}

?>
