<?php
/*
 * Created on 04.06.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
 //pr($news);
 include('loading_params.php');

if (!empty($news_item))
{
	// Если нет связи с клиентом, значит нет статуса прочитанно
	if( empty($news_item['User'])){
?>
	<div class="news">
		<h2 style="border-bottom: 2px solid #FF8214;">Новости хостинга</h2>
<?php

		echo '<div class="news_item">';
		echo $this->Html->tag('h3', $news_item['Message']['title']);
		echo $this->Html->tag('small', strftime('%d %B %Y', strtotime($news_item['Message']['created'])), array ( 'style' => 'font-weight: bold; color: #777;'));
		echo $this->Html->tag('p', $news_item['Message']['body']);

		echo '</div>';

	echo $this->Html->link('Ещё новости', '#',
								array ( 'id'  => 'more_news', 'escape' => false,
										'title'=>'Показать другие новости',
									    'onClick' => ""

						));
	$effect = $this->Js->get('#news')->effect('slideIn');
	$event  = $this->Js->request(array('controller' => 'Messages', 'action' => 'index'),
					   array('update' => '#news',
							 'before'=>$effect.$loadingShow,
							 'complete'=>$loadingHide));

	$this->Js->get('#more_news')->event('click', $event);

	echo ' | ';
	echo $this->Html->link('Скрыть', '#', array ('id' => 'close_news', 'title' => 'Больше не показывать эту новость'));
	$hideEvent  = $this->Js->request(array('controller' => 'Messages', 'action' => 'hide', $news_item['Message']['id'] ));
	$this->Js->get('#close_news')->event('click', 'newsHide();'.$hideEvent);
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
		echo $this->Js->writeBuffer(); // Write cached scripts
}
?>
