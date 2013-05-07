<?php
class RadioShoutcastParam extends AppModel {
	public $name = 'RadioShoutcastParam';
	public $displayField = 'TitleFormat';
	public $validate = array(
		'RealTime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'W3CEnable' => array(
			'inList' => array(
				'rule' => array('inlist',array('Yes','No')),
				'message' => 'Может быть лишь Yes или No',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'NameLookups' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Может быть лишь 0 или 1',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'RelayPort' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'RelayServer' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Могут быть только цифры, буквы.',
				'allowEmpty' => true,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'AutoDumpUsers' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Может быть лишь 0 или 1',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'AutoDumpSourceTime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'ContentDir' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Могут быть только цифры и буквы.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
//		'IntroFile' => array(
//			'file' => array(
//				'rule' => array('file'),
//				'message' => 'Некорректное имя файла.',
//				'allowEmpty' => true,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'BackupFile' => array(
//			'file' => array(
//				'rule' => array('file'),
//				'message' => 'Некорректное имя файла.',
//				'allowEmpty' => true,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
		'TitleFormat' => array(
			'alphanumeric' => array(
				'rule' => 'notEmpty',
				'message' => 'Поле не может быть пустым.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'PublicServer' => array(
			'inList' => array(
				'rule' => array('inlist',array('always','never','default')),
				'message' => 'Может быть лишь Always, Never или Default',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'AllowRelay' => array(
			'inList' => array(
				'rule' => array('inlist',array('Yes','No')),
				'message' => 'Может быть лишь Yes или No',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'AllowPublicRelay' => array(
			'inList' => array(
				'rule' => array('inlist',array('Yes','No')),
				'message' => 'Может быть лишь Yes или No',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'MetaInterval' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'ListenerTimer' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
//		'BanFile' => array(
//			'file' => array(
//				'rule' => array('file'),
//				'message' => 'Некорректное имя файла.',
//				'allowEmpty' => true,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'RipFile' => array(
//			'file' => array(
//				'rule' => array('file'),
//				'message' => 'Некорректное имя файла.',
//				'allowEmpty' => true,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
		'RIPOnly' => array(
			'inlist' => array(
				'rule' => array('inlist',array('Yes','No')),
				'message' => 'Может быть лишь Yes или No',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'Sleep' => array(
//			'numeric' => array(
//				'rule' => array('numeric'),
//				'message' => 'Могут быть только цифры',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
			'rule-2' => array(
	            'rule' => array('comparison', '>=', 100), 
	            'message' => 'Минимальное значение 100'
	        ),
			'rule-3' => array(
	            'rule' => array('comparison', '<=', 1024), 
	            'message' => 'Максимальное значение 1024'
	        )
		),
		'CleanXML' => array(
			'inlist' => array(
				'rule' => array('inlist',array('Yes','No')),
				'message' => 'Может быть лишь Yes или No',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'ShowLastSongs' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'rule-2' => array(
	            'rule' => array('comparison', '>=', 1), 
	            'message' => 'Минимальное значение 1'
	        ),
			'rule-3' => array(
	            'rule' => array('comparison', '<=', 20), 
	            'message' => 'Максимальное значение 20'
	        ) 
	        	
		),
		'bitrate' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Могут быть только цифры',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $hasAndBelongsToMany = array(
		'Server' => array(
			'className' => 'Server',
			'joinTable' => 'radio_shoutcast_params_servers',
			'foreignKey' => 'radio_shoutcast_param_id',
			'associationForeignKey' => 'server_id',
			'unique' => true,
			'conditions' => '',
			'fields' => 'id',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>