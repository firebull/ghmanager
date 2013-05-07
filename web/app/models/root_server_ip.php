<?php
/*
 * Created on 19.06.2010
 *
*/

class RootServerIp extends AppModel {

    public $name = 'RootServerIp';

    public $useTable = 'root_server_ips';
    
    public $hasAndBelongsToMany = array (
		'RootServer' => array (
			'className' => 'RootServer',
			'joinTable' => 'root_server_ips_root_servers',
			'foreignKey' => 'root_server_ip_id',
			'associationForeignKey' => 'root_server_id',
			'unique' => true
		)
	);
    
    public $validate = array(
							'ip' => array(
									        'ip' => array(
									            'rule' => 'ip',
									            'message' => 'Введите корректный IP.'
									        ),
									        'unique' => array(
									            'rule' => 'isUnique',
									            'message' => 'Такой IP уже существует.'
									        )
									)
	);
	
	

}

?>
