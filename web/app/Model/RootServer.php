<?php
/*
 * Created on 01.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */

 class RootServer extends AppModel {

    public $name = 'RootServer';
    
        public $hasAndBelongsToMany = array (
		'RootServerIp' => array (
			'className' => 'RootServerIp',
			'joinTable' => 'root_server_ips_root_servers',
			'foreignKey' => 'root_server_id',
			'associationForeignKey' => 'root_server_ip_id',
			'unique' => true
		)
	);
    
    
    public $validate = array(
		'slotsMax' => array(
			'rule' => 'notEmpty'
		),
		'slotsBought' => array(
			'rule' => 'notEmpty'
		)
	);

}

 class RootServerServers extends AppModel {

	public $useTable = 'root_servers';
    public $name = 'RootServerServers';
    
        public $hasAndBelongsToMany = array (
		'Server' => array (
			'className' => 'Server',
			'joinTable' => 'servers_root_servers',
			'foreignKey' => 'root_server_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'fields' => 'id, privateType, privateStatus, status',
			'conditions' => array(
									'payedTill > NOW()',
									'initialised = 1',
									'privateType != 0'
									)
		)
	);
   
}

?>