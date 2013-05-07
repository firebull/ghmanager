<?php
/*
 * Created on 20.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 //pr($result);

 include('../loading_params.php');
?>
<script type="text/javascript">
	//$('#textarea').empty();
</script>
<?php echo $form->create('Server', array('action' => 'editConfigCommon')); ?>
<table>
	<tr>
		<td colspan="3">


<?php
	  echo $form->input('configText',
	  						array('type'=>'textarea',
	  						      'wrap'=>'off',
	  							  'style'=> 'width: 650px;
	  							  			 height: 550px;
	  							  			 background-color: #4C5843;
								  			 color: #D6DBCE;
								  			 padding-left: 15px;
								  			 overflow-y: auto;



											  ',
								  'id' => 'textEditor',
								  'escape'=>false,
								  'div' => false,
								  'label' => false,
								  'value'=>@$result));
	  /*echo $html->tag('div', @$result, array('id' => 'textarea', 'style' => 'position: relative; width: 700px; height: 520px; margin: 0px; padding: 0px;'));*/
	  echo $form->input('id', array('type'=>'hidden'));
	  echo $form->input('configId', array('type'=>'hidden'));
	  echo $form->input('action', array('type'=>'hidden', 'value'=>'write'));

?>
		</td>
</tr>
<tr>
		<td style="width: 130px;">
			<?php


			echo $js->submit('Сохранить',
				array(
					'url'=> array(
									'controller'=>'Servers',
									'action'=>'editConfigCommon'
					 ),
					'id' => 'configSaveButton',
					'update' => '#configEditor',
					'class' => 'btn btn-primary',
					'before' => $loadingShow,
					'complete'=>$loadingHide,
					'buffer' => false));
			?>


		</td>
		<td colspan="2">

		<?php
			/*
		  * Нельзя выводить кнопку "Создать из шаблона" для
		  * модов, плагинов и т.д. ,
		  * т.к. шаблон конфигов априоре может быть только
		  * для сервера.
		  */
			if (@$this->data['Server']['configType'] == 'server')
			{
			//Кнопка для создания конфига из шаблона
			echo $html->link('<i class="icon-repeat"></i> Создать из шаблона', '#',
								array ('id'=>'create_config_button',
									   'escape' => false,
									   'div' => false,
									   'label' => false,
									   'class' => 'btn'));

			$event  = $js->request(array('controller'=>'Servers',
										 'action'=>'editConfigCommon',
										 $this->data['Server']['id'],
										 $this->data['Server']['configId'],
										 'create'),
								   array('update' => '#configEditor',
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide,
										 'buffer'=>false));

			$js->get('#create_config_button')->event('click', $event);
			}
		?>

		<?php
			//Кнопка для переключения редактора
			if (is_null($editorType) or $editorType == 'extended')
			{
				$editorTypeButtonText = '<i class="icon-file"></i> Включить обычный редактор';
				$editorTypeSwitch = 'simple';
			}
			else
			{
				$editorTypeButtonText = '<i class="icon-list-alt"></i> Включить расширенный редактор';
				$editorTypeSwitch = 'extended';
			}

			echo $html->link( $editorTypeButtonText, '#',
								array ('id'=>'editor_type_switch',
									   'escape' => false,
									   'escape' => false,
									   'div' => false,
									   'label' => false,
									   'class' => 'btn'));

			$event  = $js->request(array('controller'=>'Servers',
										 'action'=>'editConfigCommon',
										 $this->data['Server']['id'],
										 $this->data['Server']['configId'],
										 'read', $editorTypeSwitch),
								   array('update' => '#configEditor',
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide,
										 'buffer'=>false));

			$js->get('#editor_type_switch')->event('click', $event);
			?>
		</td>
	</tr>
</table>
		<?php echo $form->end();?>

<script type="text/javascript">
	$(function() {

	<?php if (@$editorType == 'extended') { ?>
	    var editor;

	    editor = CodeMirror.fromTextArea(document.getElementById("textEditor"), {
								        lineNumbers: true,

								        matchBrackets: true,
								        mode: "text/x-csrc",
								        theme: "monokai",
								        onBlur: function(){
								        	// Необходимо сохранить данные в форму перед отправкой
								        	// Чтобы не потерять данные, заблокировать кнопку сохранения
								        	// перед сбросом данных в textarea
											$("#configSaveButton").val('Подождите...');
											$("#configSaveButton").attr('disabled','disabled');
											editor.save();
											$("#configSaveButton").removeAttr('disabled');
											$("#configSaveButton").val('Сохранить');

								        },
								      });
	<?php } ?>

	});


</script>

<?php
	  echo $js->writeBuffer(); // Write cached scripts
?>
