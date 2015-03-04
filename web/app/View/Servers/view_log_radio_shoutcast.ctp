<?php
/*
 * Created on 08.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('loading_params.php');
?>
<div id="shoutcast_logs">
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<h3>Выберите лог для просмотра:</h3>

	<script type="text/javascript">
		$(function() {

			var loading = '<?php echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16')); ?> Подождите...'

			$("#tabs").tabs({spinner: loading, selected: '-1',
				ajaxOptions: {
					error: function(xhr, status, index, anchor) {
						$(anchor.hash).html("Ошибка загрузки файла. Попробуйте чуть позже.");
					}
				}
			});
		});
	</script>

	<div class="log_files" style="margin-bottom: 20px;">

		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Помощь</a></li>
				<li>

				<?php
	       		echo $this->Html->link("<span>shoutcast.log</span>",
	       						array('action'=>'printLogRadioShoutcast',
	       							  $id,
	       							  'main'),
	       						array('escape' => false)

	       						);
	       		?>
	       		</li>
	       		<li>

				<?php
	       		echo $this->Html->link("<span>shoutcast_w3c.log</span>",
	       						array('action'=>'printLogRadioShoutcast',
	       							  $id,
	       							  'w3c'),
	       						array('escape' => false)

	       						);
	       		?>
	       		</li>


			</ul>
			<div id="tabs-1">
				Щелкните по логу, который хотели бы просмотреть.
			</div>
		</div>
	</div>

</div>
