<?php
	//pr($this->data);

	$promoType = array('code'  => 'Многоразовый код',
					   'token' => 'Одноразовые коды');

	echo $this->Html->tag('h3', $promoType[$this->data['Promo']['type']]);

	foreach ( $this->data['PromoCode'] as $code ) {
			if ($code['used'] == 0){
    	   		echo $this->Html->tag('strong', $code['code'], array('style' => 'padding-left: 15px;'));
			}
			else
			{
				echo $this->Html->tag('s', $code['code'], array('style' => 'padding-left: 15px;'));
			}
    	   echo '<br/>';
	}

?>

