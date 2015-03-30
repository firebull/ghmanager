<?php
/*
 * Created on 20.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */

include('loading_params.php');

echo $this->Form->create('Server', ['action' => 'editConfigCommon',
                                    'class' => 'ui form']); ?>
<table>
	<tr>
		<td colspan="3">
		<?php
			// Config path and name
			if (!empty($config))
			{
				echo $this->Html->tag('b',
					                  sprintf('%s/%s', $config['path'], $config['name']));
			}

			echo $this->Form->input('configText',
			  						array('type' => 'textarea',
			  						      'wrap' => 'off',
			  							  'style'=> 'width: 650px;
			  							  			 height: 550px;
			  							  			 background-color: #4C5843;
										  			 color: #D6DBCE;
										  			 padding-left: 15px;
										  			 overflow-y: auto;',
										  'id'     => 'textEditor',
										  'class'  => 'field',
										  'escape' => false,
										  'div'    => false,
										  'label'  => false,
										  'value'  => @$result));
			/*echo $this->Html->tag('div', @$result, array('id' => 'textarea', 'style' => 'position: relative; width: 700px; height: 520px; margin: 0px; padding: 0px;'));*/
			echo $this->Form->input('id', array('type'=>'hidden'));
			echo $this->Form->input('configId', array('type'=>'hidden'));
			echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'write'));

		?>
		<br/>
		</td>
	</tr>
	<tr>
		<td style="width: 130px;">
			<?php
			echo $this->Js->submit('Сохранить',
								   ['url'=> ['controller' => 'Servers',
											 'action'     => 'editConfigCommon'],
									'id' => 'configSaveButton',
									'update'  => '#configEditor',
									'div'     => false,
									'class'   => 'btn btn-primary ui submit red button',
									'before'  => $loadingShow.';$("#configSaveButton").addClass("disabled");',
									'complete'=> $loadingHide,
									'buffer'  => false]);
			?>
		</td>
		<td colspan="2">

		<?php
		//Кнопка для переключения редактора
		if ($editorType === null or $editorType == 'extended') {
			$editorTypeButtonText = '<i class="icon-file file icon"></i> Включить обычный редактор';
			$editorTypeSwitch = 'simple';
		} else {
			$editorTypeButtonText = '<i class="icon-list-alt file text icon"></i> Включить расширенный редактор';
			$editorTypeSwitch = 'extended';
		}

		echo $this->Html->link( $editorTypeButtonText, '#',
							array ('id'=>'editor_type_switch',
								   'escape' => false,
								   'escape' => false,
								   'div' => false,
								   'label' => false,
								   'class' => 'btn ui button'));

		$event  = $this->Js->request(array('controller'=>'Servers',
									 'action'=>'editConfigCommon',
									 $this->data['Server']['id'],
									 $this->data['Server']['configId'],
									 'read', $editorTypeSwitch),
							   array('update' => '#configEditor',
									 'before' => $loadingShow.';$("#editor_type_switch").addClass("loading");',
									 'complete'=>$loadingHide,
									 'buffer'=>false));

		$this->Js->get('#editor_type_switch')->event('click', $event);

		/*
	  * Нельзя выводить кнопку "Создать из шаблона" для
	  * модов, плагинов и т.д. ,
	  * т.к. шаблон конфигов априоре может быть только
	  * для сервера.
	  */
		if (@$this->data['Server']['configType'] == 'server') {
		//Кнопка для создания конфига из шаблона
		echo $this->Html->link('<i class="icon-repeat icon recycle"></i> Создать из шаблона', '#',
							array ('id'=>'create_config_button',
								   'escape' => false,
								   'div' => false,
								   'label' => false,
								   'class' => 'btn ui button'));

		$event  = $this->Js->request(array('controller'=>'Servers',
									 'action'=>'editConfigCommon',
									 $this->data['Server']['id'],
									 $this->data['Server']['configId'],
									 'create'),
							   array('update' => '#configEditor',
									 'before' => $loadingShow.';$("#create_config_button").addClass("loading");',
									 'complete'=>$loadingHide,
									 'buffer'=>false));

		$this->Js->get('#create_config_button')->event('click', $event);
		}
	?>
		</td>
	</tr>
</table>
<?php echo $this->Form->end();?>

<script type="text/javascript">
	$(function() {

	<?php if (@$editorType == 'extended') { ?>
	    var editor;

	    editor = CodeMirror.fromTextArea(document.getElementById("textEditor"), {
								        lineNumbers: true,

								        matchBrackets: true,
								        mode: "text/x-csrc",
								        theme: "monokai",
								        onBlur: function() {
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
	  echo $this->Js->writeBuffer(); // Write cached scripts
?>
