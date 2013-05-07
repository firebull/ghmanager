<?php
	//pr($this->data);
	
	$promoType = array('code'  => 'Многоразовый код',
					   'token' => 'Одноразовые коды');
					   
	echo $html->tag('h3', $promoType[$this->data['PromoWithCode']['type']]);
	
	foreach ( $this->data['PromoCode'] as $code ) {
			if ($code['used'] == 0){
    	   		echo $html->tag('strong', $code['code'], array('style' => 'padding-left: 15px;'));
			}
			else
			{
				echo $html->tag('s', $code['code'], array('style' => 'padding-left: 15px;'));
			}
    	   echo '<br/>';
	}
	
?>

