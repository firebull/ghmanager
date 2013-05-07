<?php
/*
 * Created on 04.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 if ($this->params['action'] != 'control')
 {
 $loadingShow = $js->get('#loading')->effect('fadeIn');
 $loadingHide = $js->get('#loading')->effect('fadeOut');
?>

        <div id='profile_compact'>
	        <div class='text'>
	        <span class="highlight3" style="font-size: 12px; font-weight: bold;">
	        <?php 
	        	echo $userinfo['User']['username'] ;
	        	if (!empty($userinfo['User']['steam_id'])){
	        		echo ' ('.$userinfo['User']['steam_id'].')';
	        	}
	        ?>
	        </span>
	        <?php	
	        	if (!empty($userinfo['User']['first_name']) or !empty($userinfo['User']['second_name'])){
	        		echo '<br/>';
	        		if (!empty($userinfo['User']['first_name'])){
	        			echo $userinfo['User']['first_name']." ";
	        		}
	        		
	        		echo $userinfo['User']['second_name'];
	        		
	        	}	
	        ?>

	        <?php
//	        if (!empty($userinfo['User']['guid'])){
//	        		echo ' GUID: <small>'.$userinfo['User']['guid'].'</small>';
//	        	}
	        ?>
	        <br/>
	        <?php echo $userinfo['User']['email'] ;?>
	        <br/>
	        <br/>
	        <?php 
				if (@$userinfo['User']['discount'] > 0)
				{
					echo 'Ваша скидка: '.$userinfo['User']['discount'].'%<br/>';
				}
			?>
	        <?php echo 'Баланс: '.$html->link(round(floatval($userinfo['User']['money']), 2).' руб.',
	        								  '#',
	        								  array('id' => 'depositLink',
	        								  		'title' => 'Пополнить баланс')
	        								  ) ;

	        	   $effect = $js->get('#deposit')->effect('slideIn');		
	        	   $event  = $js->request(array( 'controller'=>'orders',
	        	   							  'action'=>'makeDeposit'), 
	        	   						array('update' => '#deposit',	  
	        	   							  'before'=>$effect,
	        	   							   'complete'=>"$('#deposit').dialog({modal: true,position: ['center',180], show: 'highlight', width: 300});"));
	        	   
	        	   $js->get('#depositLink')->event('click', $event);				
	        ?>
	        <br/>
	        <?php
				
				$headerPrinted = false;
				$userIsTester = false;
		        foreach ( $userinfo['Group'] as $group ) {
       				if (strtolower($group['name']) != 'member'){
       					if ($headerPrinted === false){
       						echo "<br/>Ваши права доступа:";
       						echo "<ul style='padding-left: 15px; margin-left: 10px; margin-bottom: 0px;'>";
       						$headerPrinted = true;
       					}
       					
       					echo "<li>".$group['desc'].'</li>';
       					
       					// Определить ключ привязки к БетаТестерам
       					if (strtolower($group['name']) == 'betatesters'){
       						$userIsTester = true;
       					}
       				}	
				}
				if ($headerPrinted === true){
					echo "</ul>";
				}

			?>

		        <small>
		        	<br/>
		        	<?php
					//Изменение профиля
					echo $html->link('Изменить профиль', '#',
												array ('id'=>'profile_edit', 'escape' => false,
														'onClick'=>""));
					$effect = $js->get('#edit_profile')->effect('slideIn');		
					$event  = $js->request(array( 'controller'=>'users',
												  'action'=>'edit'), 
											array('update' => '#edit_profile',	  
												  'before'=>$effect.$loadingShow,
												   'complete'=>$loadingHide.";$('#edit_profile').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 650});"));
				
					$js->get('#profile_edit')->event('click', $event);
					?>
		        </small>
		        <?php // Выводим пароль, только если он есть
		        if (!empty($userinfo['User']['ftppassword'])){
		        	?>
		         | 
		        <small>
		        <?php
					//Изменение пароля FTP
					echo $html->link('Пароль FTP', '#',
												array ('id'=>'ftp_pass_edit', 'escape' => false,
														'onClick'=>""));
					$effect = $js->get('#ftp_pass')->effect('slideIn');		
					$event  = $js->request(array( 'controller'=>'users',
												  'action'=>'changeFtpPass'), 
											array('update' => '#ftp_pass',	  
												  'before'=>$effect.$loadingShow,
												   'complete'=>$loadingHide.";$('#ftp_pass').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 250});"));
				
					$js->get('#ftp_pass_edit')->event('click', $event);
					?>
		        
		        <?php } // вывод пароля 
		        ?>
		        </small>
		        <br/>
		        <small>
		        <?php
		        	// web-хостинг		   
		        	echo $html->link('Бесплатный Web-хостинг (Beta)', '#',
												array ('id'=>'web_hosting_info', 'escape' => false,
														'onClick'=>""));
					$effect = $js->get('#web_hosting')->effect('slideIn');		
					$event  = $js->request(array( 'controller'=>'servers',
												  'action'=>'webHosting'), 
											array('update' => '#web_hosting',	  
												  'before'=>$effect.$loadingShow,
												  'complete'=>$loadingHide.";$('#web_hosting').dialog({modal: true,position: ['center',80], show: 'clip', hide: 'clip', width: 480});"
												   ));
				
					$js->get('#web_hosting_info')->event('click', $event);
		        ?>
		        </small>
	        </div>
	    </div>
        <div id="edit_profile" style="display:none;" title="Изменить данные профиля" class="ui-widget-content ui-corner-all"></div>
		<div id="ftp_pass" style="display:none;" title="Пароль FTP" class="ui-widget-content ui-corner-all"></div>
		<div id="web_hosting" style="display:none;" title="Бесплатный web-хостинг" class="ui-widget-content ui-corner-all"></div>
		<div id="deposit" style="display:none;" title="Пополнить баланс" class="ui-widget-content ui-corner-all"></div>
<?php
}
?>


