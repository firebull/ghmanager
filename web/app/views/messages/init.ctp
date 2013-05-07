<?php
/*
 * Created on 04.06.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
 //pr($news);
 include('../loading_params.php');
 
 // Если нет связи с клиентом, значит нет статуса прочитанно
 if( empty($news_item['User'])){
?>
<div class="news">
	<h2 style="border-bottom: 2px solid #FF8214;">Новости хостинга</h2>
<?php

	echo '<div class="news_item">';
	echo $html->tag('h3', $news_item['Message']['title']); 
	echo $html->tag('small', strftime('%d %B %Y', strtotime($news_item['Message']['created'])), array ( 'style' => 'font-weight: bold; color: #777;')); 
	echo $html->tag('p', $news_item['Message']['body']);
	
	echo '</div>';  

echo $html->link('Ещё новости', '#',
							array ( 'id'  => 'more_news', 'escape' => false,
									'title'=>'Показать другие новости',
								    'onClick' => ""
								
					));
$effect = $js->get('#news')->effect('slideIn');		
$event  = $js->request(array('controller' => 'Messages', 'action' => 'index'), 
				   array('update' => '#news',	  
						 'before'=>$effect.$loadingShow,
						 'complete'=>$loadingHide));

$js->get('#more_news')->event('click', $event);

echo ' | ';
echo $html->link('Скрыть', '#', array ('id' => 'close_news', 'title' => 'Больше не показывать эту новость'));
$hideEvent  = $js->request(array('controller' => 'Messages', 'action' => 'hide', $news_item['Message']['id'] ));						 
$js->get('#close_news')->event('click', 'newsHide();'.$hideEvent);
?>
</div>
<?php 
 }
 else
 {
?>
<script type="text/javascript" language="javascript">
	$(function() {
		newsHide();
		});
</script>
<?php 	
 }
	echo $js->writeBuffer(); // Write cached scripts 
?>