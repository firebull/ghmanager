<?php
/*
 * Created on 24.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php
echo $html->charset();
?>
    <title>
      <?php __('GH Manager: '); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php


echo $html->meta('icon');
echo $html->css(array (
	//'cake.generic'
//	'js'
//	,'login'
	'ts-client'
//	,'ts-theme/jquery-ui'
//	,'jquery.cluetip'
//	,'ts-theme/tooltips_main.css'
//	,'ts-theme/tooltips_styles.css'
//	,'payment'
	,'ts-theme/teamserver.css'
	,'bootstrap/bootstrap.buttons'
	,'validationEngine.jquery'
	,'codemirror/codemirror'
	,'codemirror/monokai'
));
?>
	<?php
	// Скрипты в конец для более быстрой загрузки
	echo $scripts_for_layout;

	echo $this->Html->script(array (
		'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'
		,'codemirror/codemirror'
		,'codemirror/mode/clike'
	));

	echo "\n\n";

	?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
  </head>
  <body>
    <div id="container">
      <div id="header">
        <?php
	      		echo $html->image('ghmanager_272x42.png',
	      							array('title' => 'Game Hosting Manager',
	      								  'alt' => 'GH Manager',
	      								  'width' => '272',
	      								  'height' => '42'));
	      ?>
      </div>
      <div id="exit">
	      <?php
	      		echo $html->link('Выйти из панели', '/logout' );
	      ?>
      </div>
      <?php
      //echo $this->element('top_menu');
      ?>
      <div id="content">
			<table border="0" cellpadding="0" cellspacing="0" style="height: 100%; width: 100%;">
				<tr>
					<td valign="top" width="18%">
				      	<div id="leftfield">
						      	<div id="menu_servers_left">
									<?php echo $this->element('client_left_menu'); ?>
						      	</div>
						      	<div id="menu_admin_left">
						      	<?php
						      		if ($this->params['action'] == 'control')
 									{
 										echo $this->element('administration_left_menu');
 									}
						      	?>
						        </div>

				      	</div>
				     </td>
					 <td valign="top" rowspan="4"  width="82%">

				      	<div id="rightfield" style="margin-bottom: 10px;">
					  		<div id="flash"><?php echo $session->flash(); ?></div>
							<script type="text/javascript" language="javascript">
							$(function() {
								$('#flash').delay(5000).hide('clip');

							});
							</script>

						        <?php echo $content_for_layout; ?>


					      		<div id="debug">
							    <?php
							    	//pr($session);
							    	//pr($this->data);
							    	//echo $this->element('sql_dump');
							    ?>
					   			</div>
			      		</div>



			      	</td>
			     </tr>
			     <tr>
			     	<td valign="top">
			     		<?php   echo $this->element('profile');      ?>
			     	</td>
			     </tr>
			     <?php if (!empty($helpers)){ ?>
			      <tr>
			     	<td valign="top">
			     		<?php   echo $this->element('help');      ?>
			     	</td>
			     </tr>
			     <?php } ?>
			     <tr>
			     	<td valign="top">
			     		<?php   echo $this->element('support');      ?>
			     	</td>
			     </tr>
			     <tr>
			     	<td valign="top">
			     		<?php   echo $this->element('copyright');      ?>
			     	</td>
			     </tr>
			</table>


	   </div>

	    <div id="footer">

		</div>

    </div>

	<?php echo $this->element('loading'); ?>
	<?php
			echo $js->writeBuffer(); // Write cached scripts
	?>

	<?php
	// Скрипты в конец для более быстрой загрузки

	echo $this->Html->script(array (
		'pass'
		,'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.19/jquery-ui.min.js'
		,'jquery.tipTip.minified'
		,'jquery.showLoading.min.js'
		,'bootstrap-transition'
		,'bootstrap-collapse'
		,'bootstrap-dropdown'
		,'jquery.validationEngine.js'
		,'jquery.validationEngine-ru.js'
		,'jquery.showStatusTs'

	));

	echo "\n\n";

	?>

  </body>
</html>

