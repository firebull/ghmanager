<?php

$readbleTime = array('24h' => 'за сутки',
					 '7d'  => 'за неделю',
					 '1m'  => 'за месяц');

if (!empty($graphs)){

?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
<?php
foreach ( $graphs as $period => $path ) {
	?>
	<tr>
		<td style="border:none;"><?php echo $this->Html->tag('strong',
													   'Игроков '.@$readbleTime[$period],
													   array('class' => 'highlight3'));?>
													</td>
	</tr>
	<tr>
		<td style="border:none;">
		<?php
			echo $this->Html->image($path.'?'.date('d-m-Y-H-i'), array( 'alt' => 'График загруженности '.@$readbleTime[$period],
											'title' => 'График загруженности '.@$readbleTime[$period],
											'width' => 260,
											'height' => 130
										   ));
		?>
		</td>
	</tr>
	<?php
}
?>
</table>
<small>Графики обновляются каждые 15 минут</small>
<br/>
<?php
}
?>
