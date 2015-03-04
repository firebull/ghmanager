<?php
/*
 * Created on 02.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($configsList);
 include('loading_params.php');
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php echo $this->Form->create('GameTemplate', array('action' => 'linkConfigAndTagToPlugin')); ?>


	<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right" style="width: 150px;">Краткое название:
		</td>
		<td align="left"><?php echo $this->Form->input('Plugin.name',
									array(	'size'=> '40',
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Полное название (без версии):
		</td>
		<td align="left"><?php echo $this->Form->input('Plugin.longname',
									array(	'size'=> '40',
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Версия:
		</td>
		<td align="left"><?php echo $this->Form->input('Plugin.version',
									array(	'size'=> '20',
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Краткое описание:
		</td>
		<td align="left"><?php echo $this->Form->input('Plugin.shortDescription',
									array(	'size'=> '40',
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
		Описание:<br/>
		<?php echo $this->Form->input('Plugin.description',
									array(	  'type'=>'textarea',
				  						      'wrap'=>'off',
				  							  'style'=> 'width: 550px;
				  							  			 height: 200px;
											  			 padding-left: 10px;



														  ',
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
		<hr/>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">Конфиг (<?php echo count($this->data['Config']); ?>):
		</td>
		<td align="left"><?php echo $this->Form->input('Config',
									array(	'options' => $configsList,
											'div' => false,
											'label' => false,
											'style' => 'height: 200px; width: 400px;'
										 ));?>
		</td>
	</tr>

	<tr>
		<td></td>
		<td align="left">
			<?php
				foreach ( $this->data['Tag'] as $tag ) {
					echo $this->Html->link('-'.$tag['name'],'#', array(
														'id' => 'remove_tag_'.$tag['id'],
														'title' => 'Удалить тэг (пока не работает)'
														));
					echo ' ';

				}
			?>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">Тэги:
		</td>
		<td align="left">
			<?php echo $this->Form->input('Tag.tags',
											array(	'size' => '40',
													'id' => 'tags',
													'div' => false,
													'label' => false));
			?>
		</td>
	</tr>
	<tr>
			<td></td>
			<td align="left">
				<div id="pluginTags">
					<?php
						if(!empty($tagsList)){
							foreach ( $tagsList as $key => $value ) {
	       						echo $this->Html->tag('div','+'.$value, array(
	       															'id' => 'add_tag_'.$key,
	       															'onClick' => '$(function() { $("input#tags").val($("input#tags").val() + "'.$value.', "); });',
	       															'title' => 'Добавить тэг',
	       															'style' => 'display: inline; cursor: pointer;'
	       															));
	       						echo ' ';
							}
						}
					?>
				</div>
			</td>
	</tr>
	<tr>
			<td colspan="2" align="center"><?php
					echo $this->Form->input('Plugin.id', array('type'=>'hidden'));
					// Пока неисправят баг в jQuery, будем отсылать обычной кнопкой
					echo $this->Form->submit('Сохранить',
											array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));
					?>

			</td>
	</tr>
 </table>

<?php echo $this->Form->end();?>
