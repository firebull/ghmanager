<?php
/*
 * Created on 19.06.2010
 *
 * To change the template for this generated file go to
 */
 include('loading_params.php');
?>

<div id="map_install_top_menu">
	<div class="ui pointing menu">
		<?php
			if ( $mapTypeActive == 'installed'){
       				$class = 'red active item';
       			}
       			else
       			{
       				$class = 'item';
       			}
		?>
       	<?php echo $this->Html->link( 'Установленные', '#',
										array (
												'id' => 'map_sort_link_installed',
												'title' => 'Просмотр и удаление установленных карт',
												'class' => $class.' tipTip',
							  					'escape' => false
												)

								);

			 $effect = $this->Js->get('#map_install')->effect('slideIn');
			 $event  = $this->Js->request(array (
										'controller'=>'Servers',
										'action'=>'mapInstall',
										$serverId,
										'all',
										'installed'
										),
							   array(	'update' => '#map_install',
									 	'before'=>$loadingShow,
									 	'complete'=>$loadingHide
									 	));

			$this->Js->get('#map_sort_link_installed')->event('click', $event);
		?>
		<?php
			foreach ( $mapTypes as $mapType => $mapTypeName ) {
       			if ( $mapType == $mapTypeActive){
       				$class = 'red active item';
       			}
       			else
       			{
       				$class = 'item';
       			}
       	?>
       	<?php

       	echo $this->Html->link($mapType, '#',
								   array (	'id' => 'map_sort_link_'.$mapType,
											'title' => "Установка карт '".$mapTypeName."'",
											'class' => $class.' tipTip',
							  				'escape' => false));

		$effect = $this->Js->get('#map_install')->effect('slideIn');
		$event  = $this->Js->request(array (
										'controller'=>'Servers',
										'action'=>'mapInstall',
										$serverId,
										'all',
										$mapType
										),
							   array(	'update' => '#map_install',
									 	'before'=>$loadingShow,
									 	'complete'=>$loadingHide
									 	));

		$this->Js->get('#map_sort_link_'.$mapType)->event('click', $event);
		?>
       	<?php
			}
		?>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$(".tipTip").tipTip({maxWidth: "auto", delay: 100});
	});
</script>
<?php
		echo $this->Js->writeBuffer(); // Write cached scripts
?>
