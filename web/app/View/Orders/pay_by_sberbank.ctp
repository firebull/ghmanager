<?php
	echo $this->Html->css(array ('kv'));
	function printBySymbol ($string = null){
		if (strlen($string) > 0){
			$width = intval(100/strlen($string));
			echo '<table class ="celled-number" cellpadding="0" cellspacing="0" width="100%">'."\n";
			echo '<tr>'."\n";
			for ($i = 0; $i<strlen($string); $i++){

				echo '<td align="center" height="10" width="'.$width.'%">'.substr($string,$i,1).'</td>'."\n";
			}
			echo '<tr>'."\n";
			echo '</table>'."\n";
		}
	}
?>
<table align="center" width="650" border="0">
  <tr>
    <td><div id="flash"><?php echo $this->Session->flash(); ?></div></td>
  </tr>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td>
<table class="ramka" align="center" border="0" cellpadding="0" cellspacing="0" width="650">
  <tbody>
  	<tr>
    <td class="lineh" align="center" height="245" width="182">
      <table border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr><td class="t10b" align="center" height="100" valign="top">И з в е щ е н и е</td></tr>
			<tr><td class="t10b" align="center" height="100" valign="bottom">Кассир</td></tr>
      	</tbody>
      </table>
    </td>
    <td class="linevh" height="245" width="10">&nbsp;</td>
    <td class="lineh" height="245" width="468">
      <table style="height: 245px;" align="center" border="0" cellpadding="0" cellspacing="0" width="468">
	<tbody><tr>
	  <td height="30">
	    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td>
			<?php
				echo $this->Html->image('icons/sberbank_114x23.png', array('title' => 'Логотип Сбербанка'))
			?>
		</td>
		<td class="t6n" align="right" valign="middle"><i>Форма № ПД-4</i></td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td class="h8b" align="center" valign="bottom"><?php echo $payParams['firmName'];?></td>
	</tr>
	<tr>
	  <td class="t6n" align="center" height="10" valign="top">(наименование получателя платежа)</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td valign="bottom" width="30%">
		      <?php echo printBySymbol($payParams['inn']);?>
		</td>
		<td valign="bottom" width="10%">&nbsp;</td>
		<td valign="bottom" width="60%">
		  <?php echo printBySymbol($payParams['schet']);?>
		</td>
	      </tr>
	      <tr>
		<td class="t6n" align="center" valign="top">(ИНН получателя платежа)</td>
		<td class="t6n" valign="top">&nbsp;</td>
		<td class="t6n" align="center" valign="top">(номер счета получателя платежа)</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	  	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
	    <tbody><tr>
	      <td class="t8n" valign="bottom" width="10">в</td>
	      <td class="h8b" align="center" valign="bottom">
	      	<small>
	      		<?php echo $payParams['bank'];?>
	      	</small>
	      </td>

          <td class="t8n" align="right" valign="bottom" width="40">КПП&nbsp;</td>
		  <td valign="bottom" width="27%">
				<?php echo printBySymbol($payParams['kpp']);?>
	      </td>
	      <td class="t8n" align="right" valign="bottom" width="40">БИК&nbsp;</td>
	      <td valign="bottom" width="27%">
				<?php echo printBySymbol($payParams['bik']);?>
	      </td>
	    </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td class="t6n" align="center" valign="top">банк получателя платежа</td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
	    </tr>
	  </tbody></table>
	</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t7n" valign="bottom">Номер&nbsp;кор./сч.&nbsp;банка&nbsp;получателя&nbsp;платежа</td>
		<td valign="bottom" width="60%">
		  <?php echo printBySymbol($payParams['korSchet']);?>
		</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	     <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="h8b" valign="bottom" width="100%">
			<?php
				echo 'Оплата заказа №'.$payParams['orderNum'].
				' от '.$this->Time->format(@$payParams['orderDate']).
				' на сумму '.$payParams['orderSum'].' руб. 00 коп.'
				?></td>
		<td width="5%">&nbsp;</td>
		      </tr>
	      <tr>
		<td class="t6n" align="center" valign="top">(наименование платежа)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(номер лицевого счета (код) плательщика)</td>
		<td>&nbsp;</td>
			      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="1%">Ф.И.О&nbsp;плательщика&nbsp;</td>
		<td class="h8b" valign="bottom"><?php echo @$payParams['payByName']; ?></td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="1%">Адрес&nbsp;плательщика&nbsp;</td>
		<td class="h8b" valign="bottom"><?php echo @$payParams['payByAddress']; ?></td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr><td class=t8n valign=bottom width='1%'>Сумма&nbsp;платежа&nbsp;</td>
	      <td class=h8b valign=bottom width='8%'><?php echo @$payParams['orderSum']; ?></td><td class=t8n valign=bottom width='1%'>&nbsp;руб.&nbsp;</td>
	      <td class=h8b valign=bottom width='8%'>00</td><td class=t8n valign=bottom width='1%'>&nbsp;коп.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сумма&nbsp;платы&nbsp;за&nbsp;услуги&nbsp;</td><td class=h8b valign=bottom width='8%'>&nbsp;</td><td class=t8n valign=bottom width='1%'>&nbsp;руб.&nbsp;</td><td class=h8b valign=bottom width='8%'>&nbsp;</td><td class=t8n valign=bottom width='1%'>&nbsp;коп.</td></tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="5%">Итого&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="5%">&nbsp;&nbsp;руб.&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="5%">&nbsp;коп.&nbsp;</td>
		<td class="t8n" align="right" valign="bottom" width="20%">&nbsp;"&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="1%">&nbsp;"&nbsp;</td>
		<td class="h8b" valign="bottom" width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="t8n" valign="bottom" width="4%">&nbsp;200&nbsp;</td>
		<td class="h8b" valign="bottom" width="3%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="1%">&nbsp;г.</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td class="t7n" style="text-align: justify;" valign="bottom">
	  С условиями приема указанной в платежном документе суммы, в т.ч. с суммой
		взимаемой платы за&nbsp;услуги
		банка&nbsp;ознакомлен&nbsp;и&nbsp;согласен.</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t6n" valign="bottom" width="50%">&nbsp;</td>
		<td class="t7n" valign="bottom" width="1%"><b>Подпись&nbsp;плательщика&nbsp;</b></td>
		<td class="h8b" valign="bottom" width="40%">&nbsp;</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
		<td class="spc" height="1">&nbsp;</td>
	</tr>
     	</tbody></table>
    </td>
    <td class="lineh" height="245" width="10">&nbsp;</td>
  </tr>
  <tr>
    <td class="t10b" align="center" height="285" valign="bottom" width="182">К в и т а н ц и я<br><br>Кассир<br>&nbsp;</td>
    <td class="linev" height="285" width="10">&nbsp;</td>
    <td height="285" valign="top">
      <table style="height: 285px;" align="center" border="0" cellpadding="0" cellspacing="0" width="468">
	<tbody><tr>
	  <td class="h8b" align="center" height="30" valign="bottom"><?php echo $payParams['firmName'];?></td>
	</tr>
	<tr>
	  <td class="t6n" align="center" height="10" valign="top">(наименование получателя платежа)</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td valign="bottom" width="30%">
		  <?php echo printBySymbol($payParams['inn']);?>
		</td>
		<td valign="bottom" width="10%">&nbsp;</td>
		<td valign="bottom" width="60%">
		  <?php echo printBySymbol($payParams['schet']);?>
		</td>
	      </tr>
	      <tr>
		<td class="t6n" align="center" valign="top">(ИНН получателя платежа)</td>
		<td class="t6n" valign="top">&nbsp;</td>
		<td class="t6n" align="center" valign="top">(номер счета получателя платежа)</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	  	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
	    <tbody><tr>
	      <td class="t8n" valign="bottom" width="10">в</td>
	      <td class="h8b" align="center" valign="bottom">
	      	<small>
	      		<?php echo $payParams['bank'];?>
	      	</small>
	      </td>
          <td class="t8n" align="right" valign="bottom" width="40">КПП&nbsp;</td>
		  <td valign="bottom" width="27%">
				<?php echo printBySymbol($payParams['kpp']);?>
	      </td>

	      <td class="t8n" align="right" valign="bottom" width="40">БИК&nbsp;</td>
	      <td valign="bottom" width="27%">
				<?php echo printBySymbol($payParams['bik']);?>
	      </td>
	    </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td class="t6n" align="center" valign="top">банк получателя платежа</td>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
	    </tr>
	  </tbody></table>
	</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t7n" valign="bottom">Номер&nbsp;кор./сч.&nbsp;банка&nbsp;получателя&nbsp;платежа</td>
		<td valign="bottom" width="60%">
		  <?php echo printBySymbol($payParams['korSchet']);?>
		</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="h8b" valign="bottom" width="100%">
			<?php
				echo 'Оплата заказа №'.$payParams['orderNum'].
				' от '.$this->Time->format(@$payParams['orderDate']).
				' на сумму '.$payParams['orderSum'].' руб. 00 коп.'
				?>
		</td>
		<td width="5%">&nbsp;</td>
	      </tr>
	      <tr>
		<td class="t6n" align="center" valign="top">(наименование платежа)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(номер лицевого счета (код) плательщика)</td>
		<td>&nbsp;</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="1%">Ф.И.О&nbsp;плательщика&nbsp;</td>
		<td class="h8b" valign="bottom"><?php echo @$payParams['payByName']; ?></td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="1%">Адрес&nbsp;плательщика&nbsp;</td>
		<td class="h8b" valign="bottom"><?php echo @$payParams['payByAddress']; ?></td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr><td class=t8n valign=bottom width='1%'>Сумма&nbsp;платежа&nbsp;</td>
	      <td class=h8b valign=bottom width='8%'><?php echo @$payParams['orderSum']; ?></td><td class=t8n valign=bottom width='1%'>&nbsp;руб.&nbsp;</td>
	      <td class=h8b valign=bottom width='8%'>00</td><td class=t8n valign=bottom width='1%'>&nbsp;коп.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Сумма&nbsp;платы&nbsp;за&nbsp;услуги&nbsp;</td><td class=h8b valign=bottom width='8%'>&nbsp;</td><td class=t8n valign=bottom width='1%'>&nbsp;руб.&nbsp;</td><td class=h8b valign=bottom width='8%'>&nbsp;</td><td class=t8n valign=bottom width='1%'>&nbsp;коп.</td></tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t8n" valign="bottom" width="5%">Итого&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="5%">&nbsp;&nbsp;руб.&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="5%">&nbsp;коп.&nbsp;</td>
		<td class="t8n" align="right" valign="bottom" width="20%">&nbsp;"&nbsp;</td>
		<td class="h8b" valign="bottom" width="8%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="1%">&nbsp;"&nbsp;</td>
		<td class="h8b" valign="bottom" width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td class="t8n" valign="bottom" width="4%">&nbsp;200&nbsp;</td>
		<td class="h8b" valign="bottom" width="3%">&nbsp;</td>
		<td class="t8n" valign="bottom" width="1%">&nbsp;г.</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr>
	  <td class="t7n" style="text-align: justify;" valign="bottom">С
условиями приема указанной в платежном документе суммы, в т.ч. с суммой
взимаемой платы за&nbsp;услуги
банка&nbsp;ознакомлен&nbsp;и&nbsp;согласен.</td>
	</tr>
	<tr>
	  <td>
	    <table border="0" cellpadding="0" cellspacing="0" width="100%">
	      <tbody><tr>
		<td class="t6n" valign="bottom" width="50%">&nbsp;</td>
		<td class="t7n" valign="bottom" width="1%"><b>Подпись&nbsp;плательщика&nbsp;</b></td>
		<td class="h8b" valign="bottom" width="40%">&nbsp;</td>
	      </tr>
	    </tbody></table>
	  </td>
	</tr>
	<tr><td class="spc" height="1">&nbsp;</td></tr>
      </tbody></table>
    </td>
    <td width="10">&nbsp;</td>
  </tr>
</tbody></table>
</td></tr>
<tr><td>
  &nbsp;<br>
</td>
</tr>
</tbody>
</table>
