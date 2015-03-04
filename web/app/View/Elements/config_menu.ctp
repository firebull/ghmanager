<div class="tabsTsBlock" id='configTabs'>
	<ul>

		<?php foreach ( $configs as $config ) { ?>
		<li id="button_config_<?php echo $config['id']; ?>"  title="<?php echo 'Просмотр конфига по пути: '.$config['path']."/".$config['name'];?>">


			<?php

				/*  Обрезать строку с именем конфига */
				if (strlen($config['name']) > 24)
				{
					$configName = preg_split('/\./', $config['name']);
					if (@count($configName) > 1)
					{
						$configExt = $configName[count($configName) - 1];
						$config['name'] = substr($config['name'], 0, strlen($config['name']) - strlen($configExt) -1);
						$config['name'] = substr($config['name'], 0, 20).'...'.$configExt;

					}
				}

				$loadingImage = $this->Html->image('loading_white.gif', array('alt'=>'Loading...', 'width'=>'24', 'height'=>'24', 'style' => 'padding: 1px; padding-top: 1px;'));

				echo $this->Html -> link( "<span class=a-menu-btn-text><strong>".$config['name']."</strong><br/>".
								 	"<small>".@$config['shortDescription']."&nbsp;</small></span>".
								 	"<span class=a-menu-btn-icon-right id='arrow_config_".$config['id']."'><span></span></span>".
								 	"<span class=a-menu-btn-loading id='loading_config_".$config['id']."'><span></span></span>", '#',
									array ('id'=>'config_'.$config['id'],
									'escape' => false,
									'class' => 'a-menu-btn'

									));
				$effect = $this->Js->get('#configEditor')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'servers',
											 'action'=>'editConfigCommon',
											 $id,
											 $config['id'],
											 'read'),
									   array('update' => '#configEditor',
											 'before' =>"$('#arrow_config_".$config['id']."').hide();".
											 			"$('#loading_config_".$config['id']."').show();".
											 			"$('.a-menu-btn').removeClass('a-menu-btn-active');".
											 			"upscaleHeightFromButton('#".$owner."', true);".
											 			"",
											 'complete' =>  "$('#loading_config_".$config['id']."').hide();".
											 				"$('#arrow_config_".$config['id']."').show();".
											 				"$('#config_".$config['id']."').addClass('a-menu-btn-active');"
											 ));

				$this->Js->get('#config_'.$config['id'])->event('click', $event);

			?>

		</li>
		<?php } ?>


	</ul>


</div>
<script>


	function upscaleHeightFromButton(owner, clear){
		var listHeight = $('#configTabs').outerHeight();

		if (clear === true){
				$('#configEditor').empty();
			}

		if ( listHeight < 500){
			$(owner).height(630);

			$('#configEditor').height(550);
		}
		else
		{
			$(owner).height(listHeight + 100);
			$('#configEditor').height(listHeight - 20);
			//$('head').append('<style type="text/css">.CodeMirror{ height: ' + (listHeight - 50) + ';}</style>)');
		}

	}

	function upscaleHeight(owner, clear){
		var listHeight = $('#configTabs').outerHeight();

		if (clear === true){
				$('#configEditor').empty();
			}

		if ( listHeight > 200){
			$(owner).height(listHeight + 100);
			$('#configEditor').height(listHeight - 20);
			//$('head').append('<style type="text/css">.CodeMirror{ height: ' + (listHeight - 50) + ';}</style>)');
		}

	}

	$(function() {
		upscaleHeight('#<?php echo @$owner;?>', false);
	});

</script>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
