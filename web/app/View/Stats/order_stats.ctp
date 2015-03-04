<?php

	$maxSum = round(max($stat['payed']));

	$leadNubmder = substr($maxSum,0,1) + 1;

	$mult = strlen($maxSum) - 1;

	$m = '1';
	for ($i=0; $i < $mult; $i++) { 
		$m .= '0';
	}

	$maxSum = $leadNubmder*$m;
	$yStep = $maxSum/5;

?>

<!-- css bar graph -->
<div class="css_bar_graph">
	
	<!-- y_axis labels -->
	<ul class="y_axis">
		<li><?php echo $yMax = $maxSum; ?></li>
		<li><?php echo $yMax = ($yMax - $yStep); ?></li>
		<li><?php echo $yMax = ($yMax - $yStep); ?></li>
		<li><?php echo $yMax = ($yMax - $yStep); ?></li>
		<li><?php echo $yMax = ($yMax - $yStep); ?></li>
		<li>0</li>
	</ul>

	<!-- x_axis labels -->
	<ul class="x_axis">
<?php

		foreach ($stat['payed'] as $month => $sum) {
			$date = date('m.Y', strtotime($month));
			echo $this->Html->tag('li',$date);
		}

?>
	</ul>

	<!-- graph -->
	<div class="graph">
		<!-- grid -->
		<ul class="grid">
			<li><!-- 60 --></li>
			<li><!-- 45 --></li>
			<li><!-- 30 --></li>
			<li><!-- 15 --></li>
			<li><!-- 15 --></li>
			<li class="bottom"><!-- -0 --></li>
		</ul>
	
		<!-- bars -->
		<!-- 250px = 100% -->
		<ul>
			<?php 
				
				$i = 1;
				$left_1 = '0';
				$left_2 = '35';
				foreach ($stat['payed'] as $month => $sum) {

					$sumPayed = $sum;
					$sumGot   = $stat['got'][$month];
					$payedHeight = round(($sumPayed/$maxSum)*250);
					$gotHeight = round(($sumGot/$maxSum)*250);

?>
					<li class="bar green" style="height: <?php echo $payedHeight; ?>px; left: <?php echo $left_1 = $left_1 + 90;?>px;"><div class="top"></div><div class="bottom"></div><span><?php echo $sumPayed; ?></span></li>

					<li class="bar orange" style="height: <?php echo $gotHeight; ?>px; left: <?php echo $left_2 = $left_2 + 90;?>px;"><div class="top"></div><div class="bottom"></div><span><?php echo $sumGot; ?></span></li>

<?php

				}	
?>

			
		</ul>


	</div>






	<!-- graph label -->
	<div class="label"><span>Статистика: </span>Платежи в месяц</div>

</div>