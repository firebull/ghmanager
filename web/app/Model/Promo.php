<?php
class Promo extends AppModel {
	public $name = 'Promo';
	public $displayField = 'description';	
	
}

class PromoWithCode extends AppModel {
	public $name = 'PromoWithCode';
	public $displayField = 'description';
	public $useTable = 'promos';
	
	public $hasMany = array(
		'PromoCode' => array(
			'className' => 'PromoCode'
		)
	);
	
}

?>