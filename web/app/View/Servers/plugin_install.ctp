<?php
/*
 * Created on 09.08.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');
 //echo $this->element('sql_dump');
 //var_dump(@$response);
?>
<cake:nocache>
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<div class="icons">
	<ul class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="margin-bottom: 8px;">
	<table width="96%" border="0" cellpadding="1" cellspacing="1" align="center">
	<?php
	// Если мод не установлен, то предлагаем сначала установить его
	// либо получаем подтверждение от пользователя, что он уже
	// установил мод самостоятельно

	/*
	 * TODO:
	 * Выводить все привязанные к шаблону плагины:
	 * уже инсталированные без ссылки, другие с ссылкой.
	 * При инсталляции другой версии, делать привязку
	 * только на неё в контроллере.
	 */

	if ( empty($installedMod) ) {
	?>
	<tr>
		<td colspan="2" style="border-bottom: 1px dotted #E3A345;">
			<p>
			<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
			Прежде, чем устанавливать плагины, необходимо установить мод сервера.
			Щелкните по имени мода ниже, либо подтвердите, что уже установили мод самостоятельно.
			</p>
		</td>
	</tr>
	<?php
	}
		$i = 0;
		foreach ($modsList as $mod):
		if ($i == 0) {

			echo "<tr>\n";
		}

	?>

		<td valign="top"><li><?php
		$modPlugins = '';
		if (!empty($mod['Mod']['description'])) { // Выводить или нет tooltip
				$linkClass = 'qlabs_tooltip_diagonal_right qlabs_tooltip_style_1';

				if (!empty($mod['Plugin'])) {
					$modPlugins = "\n\nПосле установки мода будут доступны следущие плагины:<br/>";

					foreach ( $mod['Plugin'] as $modPlugin ) {

						$modPlugins .= ' -&nbsp;<i>'.$modPlugin['longname'].' '.$modPlugin['version'].'</i>'."\n";

					}

				}


			} else {
				$linkClass = 'noTooltip';
			}




		if ( empty($installedMod) or !in_array(@$mod['Mod']['id'], @$installedMod)) {
			//Ссылка  для установки мода
			echo '<span class="ui-icon ui-icon-plusthick" style="float: left; margin-right: .3em;"></span>';
			$modDesc = '<span><strong>'.$mod['Mod']['longname'].'</strong><pre>'.@$mod['Mod']['description'].@$modPlugins.'</pre></span>';
			echo $this->Html->link( $modDesc.'<nobr>'.$mod['Mod']['longname'].'&nbsp;'.$mod['Mod']['version'], '#',
								array ('id' => 'mod_install_link_'.$mod['Mod']['id'],
									   'rel'=> '#mod_desc_'.$mod['Mod']['id'],
									   'class'=> $linkClass,
									   'escape' => false));
			$effect = $this->Js->get('#plugin_install')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Servers',
										 'action'=>'pluginInstall', $serverId, $mod['Mod']['id'],'mod'),
								   array('update' => '#plugin_install',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#mod_install_link_'.$mod['Mod']['id'])->event('click', $event);



		?>

		<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
		<small>
		<?php


			//Ссылка самостоятельной для установки мода
			echo $this->Html->link('Я установил по FTP', '#',
								array ('id'=>'mod_install_link_user_'.$mod['Mod']['id'], 'escape' => false));
			$effect = $this->Js->get('#plugin_install')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Servers',
										 'action'=>'pluginInstall', $serverId, $mod['Mod']['id'],'mod','user'),
								   array('update' => '#plugin_install',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#mod_install_link_user_'.$mod['Mod']['id'])->event('click', $event);



		?>
		</small>
		)
		<?php
		} // if
		elseif (in_array(@$mod['Mod']['id'], @$installedMod)) {

			$modDesc = '<span><strong>Переустановить мод</strong><pre>'.@$mod['Mod']['description'].@$modPlugins.'</pre></span>';
			echo '<span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em; clear: right;"></span>';
			echo $this->Html->link(
									$modDesc.'<nobr>'.$mod['Mod']['longname'].'&nbsp;'.$mod['Mod']['version'], '#',
									array ('id' => 'mod_install_link_'.$mod['Mod']['id'],
										   'rel'=> '#mod_desc_'.$mod['Mod']['id'],
										   'class'=> $linkClass,
										   'escape' => false));
				$effect = $this->Js->get('#plugin_install')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'Servers',
											 'action'=>'pluginInstall', $serverId, $mod['Mod']['id'],'mod'),
									   array('update' => '#plugin_install',
											 'before'=>$effect.$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#mod_install_link_'.$mod['Mod']['id'])->event('click', $event);
		}

		?>
		<br>
		</nobr>
		</li>

		</td>
	<?php

		if ($i == 1) {

		echo "</tr>\n";
			$i--;
			$i--;
		}

		$i++;

		endforeach;

	// Если мод установлен, выводим список плагинов к этому моду
	if ( !empty($pluginsList) ) {
	?>
	<tr>
		<td colspan="2" style="border-top: 1px solid #E3A345;"></td>
	</tr>
	<?php
		$i = 0;
		$allTags = array();
		foreach ($pluginsList as $plugin):
		if ($i == 0) {

			echo "<tr>\n";
		}

	?>
		<td><?php

			$pluginDesc = '';

			if (!empty($plugin['Plugin']['description'])) { // Выводить или нет tooltip
				$linkClass = 'qlabs_tooltip_diagonal_right qlabs_tooltip_style_1';
			} else {
				$linkClass = 'noTooltip';
			}

			$tagsClasses = ' taggedPlugin';

			foreach ( $plugin['Tag'] as $tag ) {
       			$tagsClasses .= ' tag_'.$tag['id'];
       			$allTags[$tag['id']] = $tag;
			}
			if (@$plugin['Plugin']['installed'] ==1) {
				//Ссылка для удаления плагина
				?>
				<li>
					<div class="ui-state-error" style="border: none; width: 300px;">
				<?php

				//Ссылка  для удаления плагина
				echo $this->Html->link('<span class="ui-icon ui-icon-close" style="float: left; margin-right: 0em;"></span>', '#',
									array ('id'=>'plugin_delete_link_'.$plugin['Plugin']['id'],
									'onClick' => 'ConfirmDelete'.$plugin['Plugin']['id'].'();',
									'title' => 'Удалить плагин',
									'escape' => false));

				$event  = $this->Js->request(array('controller'=>'Servers',
											 'action'=>'pluginDelete', $serverId, $plugin['Plugin']['id']),
									   array('update' => '#plugin_install',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));


				?>
					</div>
					<div id="update_off_confirm_<?php echo $plugin['Plugin']['id']; ?>" title="Подвердите удаление плагина <?php echo $plugin['Plugin']['longname']; ?>" style="display: none;">
							<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
								<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
								Плагин будет удалён. Все изменённые конфигурационные файлы (.cfg и .ini) будут переименованы в файлы с расширением .old.
							</div>
					</div>
					<script type="text/javascript">
					function ConfirmDelete<?php echo $plugin['Plugin']['id']; ?>() {

						$('#update_off_confirm_<?php echo $plugin['Plugin']['id']; ?>').dialog({
												resizable: false,
												height:220,
												width: 400,
												modal: true,
												buttons: {

														'Подтверждаю': function() {
														<?php echo $event;?>;

														$(this).dialog('close');
													},
													'Нет!': function() {
														$(this).dialog('close');
													}
												}
											});

						}
				</script>
				<?php

				if (@$plugin['Plugin']['description']) {

					$pluginDesc =  "<span><strong>Переустановить плагин</strong><pre>".@$plugin['Plugin']['description']."</pre></span>";

				}

				//Ссылка  для переустановки плагина
				$icon = '<div class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;clear: right;"></div>';
				echo $this->Html->link(
									@$pluginDesc.'<nobr><div class="'.$tagsClasses.'">'.$icon.$plugin['Plugin']['longname'].'</div></nobr>', '#',
									array ('id'=>'plugin_install_link_'.$plugin['Plugin']['id'],
									'class' => $linkClass,
									'escape' => false));
				$effect = $this->Js->get('#plugin_install')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'Servers',
											 'action'=>'pluginInstall', $serverId, $plugin['Plugin']['id'],'plugin'),
									   array('update' => '#plugin_install',
											 'before'=>$effect.$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#plugin_install_link_'.$plugin['Plugin']['id'])->event('click', $event);

				?>
				</li>
				<?php
			} else {
				?>
				<li>
				<?php
				if (@$plugin['Plugin']['description']) {

					$pluginDesc =  "<span><strong>".$plugin['Plugin']['longname']."</strong><pre>".@$plugin['Plugin']['description']."</pre></span>";

				}
				//Ссылка  для установки плагина
				$icon = '<div class="ui-icon ui-icon-plusthick" style="float: left; margin-right: .3em;"></div>';
				echo $this->Html->link(
									@$pluginDesc.'<nobr><div class="'.$tagsClasses.'">'.$icon.$plugin['Plugin']['longname'].' '.$plugin['Plugin']['version'].'</div></nobr>', '#',
									array ('id'=>'plugin_install_link_'.$plugin['Plugin']['id'],
									'class' => $linkClass,
									'rel' =>'#plugin_desc_'.$plugin['Plugin']['id'],
									'escape' => false));
				$effect = $this->Js->get('#plugin_install')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'Servers',
											 'action'=>'pluginInstall', $serverId, $plugin['Plugin']['id'],'plugin'),
									   array('update' => '#plugin_install',
											 'before'=>$effect.$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#plugin_install_link_'.$plugin['Plugin']['id'])->event('click', $event);

				?>
				</li>
				<?php

			}


		?>

		</td>
	<?php

		if ($i == 1) {

		echo "</tr>\n";
			$i--;
			$i--;
		}

		$i++;

		endforeach;
	 }
	 ?>
	</table>
	</ul>
	</div>

	<?php if (!empty($allTags)) {?>
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 3px; padding: 0 .7em;">
	<small>
	<?php
		$tags = '';
		foreach ( $allTags as $tag ) {
			$tags .= $this->Html->tag('div', $tag['name'], array(
												'id' => 'show_tag_'.$tag['id'],
												'class' => 'pluginTag',
												'title' => 'Показать плагины группы \''.$tag['name'].'\'',
												'style' => 'display: inline; ' .
														   'cursor: pointer; ' .
														   'text-decoration: underline; '
												));
			$tags .= ', ';
			$this->Js->get('#show_tag_'.$tag['id'])->event('click', 'highlightPlugins("'.$tag['id'].'");');
		}

		$tags .= $this->Html->tag('div', 'все', array(
												'id' => 'show_tag_all',
												'class' => 'pluginTag',
												'title' => 'Показать все плагины.',
												'style' => 'display: inline; ' .
														   'cursor: pointer; ' .
														   'text-decoration: underline; '
												));
		$this->Js->get('#show_tag_all')->event('click', '$(function() { $(".taggedPlugin").removeClass("highlight4"); $(".pluginTag").removeClass("highlight2");});');

		echo $tags;
	?>
	</small>
	</div>
	<?php } ?>

	<?php if ( !empty($pluginsList) ) { ?>
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
			<p style="padding: 0px; margin: 5px; "><?php


			//Ссылка самостоятельной для установки мода
			echo '<span class="ui-icon ui-icon-transferthick-e-w" style="float: left; margin-right: .3em;"></span>';
			echo $this->Html->link('<span>Если вы самостоятельно установили или удалили плагины через FTP, то нажмите сюда, чтобы синхронизировать
			этот список.<br/><br/> Это необходимо для того, чтобы вы могли редактировать настройки плагинов из панели.</span>' .
							'Синхронизировать список плагинов', '#',
								array ('id'=>'plugins_resync_link',
									   'class' => 'qlabs_tooltip_diagonal_right qlabs_tooltip_style_39',
									   'escape' => false));
			$effect = $this->Js->get('#plugin_install')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Servers',
										 'action'=>'pluginResync', $serverId),
								   array('update' => '#plugin_install',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#plugins_resync_link')->event('click', $event);



		?>
			</p>
	</div>
	<?php } ?>
	<div class="ui-state-error ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<small>
			При установке новой версии мода или плагина, если уже установлена старая,
			новая версия будет записана поверх старой, без сохранения ваших настроек!
			Сохраните их через FTP или с помощью редактора файлов конфигурации, если требуется!
			</small>
			</p>
	</div>
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<small>
			Обратите внимание, что мы предлагаем к установке проверенные и протестированные моды и плагины.
			Тем не менее, при выходе обновления сервера, есть вероятность работы плагина с ошибками и
			может потребоваться самостоятельная ручная установка по FTP, т.к. у нас занимает
			некоторое время на тестирование и добавление новой версии. Но вы всегда можете написать нам и поторопить =)
			<br/>При ручной установке здесь будет отображаться версия, установленная первоначально!
			</small>
			</p>
	</div>
	<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
	?>
</cake:nocashe>
<script type="text/javascript">
	function highlightPlugins(tag) {

	 	$(function() {

	 		$('.taggedPlugin').removeClass('highlight4');
	 		$('.pluginTag').removeClass('highlight2');
	 		$('.tag_' + tag).addClass('highlight4');
	 		$('#show_tag_' + tag).addClass('highlight2');

	 	});

	}

</script>
<?php
		echo $this->Js->writeBuffer(); // Write cached scripts
?>
