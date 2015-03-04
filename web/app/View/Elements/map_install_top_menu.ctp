<?php
/*
 * Created on 19.06.2010
 *
 * To change the template for this generated file go to
 */
 include('loading_params.php');
?>

<div id="map_install_top_menu">
	<ul class="top_menu">
		<?php
			if ( $mapTypeActive == 'installed'){
       				$class = 'active';
       			}
       			else
       			{
       				$class = 'nav';
       			}
		?>
		<li class="<?php echo $class; ?>">
       	<?php echo $this->Html->link( 'Установленные', '#',
										array (
												'id' => 'map_sort_link_installed',
												'title' => 'Просмотр и удаление установленных карт',
												'class' => 'tipTip',
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
		</li>
		<?php
			foreach ( $mapTypes as $mapType => $mapTypeName ) {
       			if ( $mapType == $mapTypeActive){
       				$class = 'active';
       			}
       			else
       			{
       				$class = 'nav';
       			}
       	?>
       	<li style="margin-left: 2px;">|</li>
       	<li class="<?php echo $class; ?>" style="margin-left: 2px;">
       	<?php

       	echo $this->Html->link($mapType, '#',
								   array (	'id' => 'map_sort_link_'.$mapType,
											'title' => "Установка карт '".$mapTypeName."'",
											'class' => 'tipTip',
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
		</li>
       	<?php
			}
		?>
	</ul>
</div>
<script type="text/javascript">
	$(function(){
		$(".tipTip").tipTip({maxWidth: "auto", delay: 100});
	});
</script>
<?php
		echo $this->Js->writeBuffer(); // Write cached scripts
?>
