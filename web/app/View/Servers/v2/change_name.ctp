<?php
/*
 * Created on 20.03.2015
 *
 * Made for project GHmanager(Git)
 * by Nikita Bulaev
 */

?>
<div id="changeServerName">
	<div class="ui active inverted dimmer" style="display: none;" id="changeServerNameLoader">
    	<div class="ui text loader">Выполняю</div>
  	</div>
<?php echo $this->Session->flash(); ?>
<?php

	echo $this->Form->create('Server', ['controller' => 'Servers',
	                                    'action'     => 'changeName',
	                                    'class'      => 'ui form']);
	echo $this->Form->input('id', ['type' => 'hidden']);

	echo $this->Form->input('name', ['div'   => 'field',
									 'label' => 'Имя сервера в панели',
									 'title' => 'Введите имя сервера в панели',
									]);

	if ($this->data['Type'][0]['name'] != 'voice')
	{
		echo $this->Form->input('nameInGame', [ 'div'   => 'field',
												'label' => 'Имя сервера в игре и мониторинге',
												'title' =>'Введите имя сервера в игре',
												]);
	}

	echo $this->Form->input('desc', ['type'  => 'textarea',
			  						 'wrap'  => 'on',
			  						 'div'   => 'field',
									 'label' => 'Краткое описание сервера',
									 'placeholder' => 'Краткое описание сервера, которое будет отображаться в мониторинге на нашем сайте',
									 'after' => '<small>70 символов максимум</small>'
								    ]);

	echo $this->Js->submit('Сохранить', ['update' => '#changeServerName',
			                             'class' => 'ui green submit button',
			                             'buffer' => false,
			                             'before' => '$("#changeServerNameLoader").show();']);


    echo $this->Form->end();
?>
</div>
