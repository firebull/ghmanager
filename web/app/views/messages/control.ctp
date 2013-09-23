<?php
/*
 * Created on 05.06.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
?>
<div class="news">
	
	<table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-bottom: 2px solid #FF8214;">
	<tr>
		<td width="80%"  ><h2 style="margin: 10px 0px 0px 0px;">Новости хостинга</h2></td>
		<td valign="bottom" align="right">
			<?php 
				echo $html->link('Добавить новость', '#', array ('id' => 'add_news', 'title' => 'Добавить новость'));
			?>
		</td>
	</tr>
	</table>
<?php

foreach ( $news as $news_item ) {
	echo '<div class="news_item">';
	echo $html->tag('h3', $news_item['Message']['title']); 
	echo $html->tag('small', strftime('%d %B %Y', strtotime($news_item['Message']['created'])), array ( 'style' => 'font-weight: bold; color: #777;')); 
	echo $html->tag('p', $text->autoLinkUrls($news_item['Message']['body']));
	
	echo '</div>';  
}


?>
</div>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td width="35%" valign="bottom" align="left">
		<!-- Shows the next and previous links -->
		<?php
		
			$this->Paginator->options(array(
										    'update' => '#news',
										    'evalScripts' => true,
										    'before' => $this->Js->get('#news_loading')->effect('fadeIn', array('buffer' => false)),
    										'complete' => $this->Js->get('#news_loading')->effect('fadeOut', array('buffer' => false)),
										));
										
			echo $paginator->prev('«««', null, null, array('class' => 'disabled'));
			echo '&nbsp;&nbsp;';
			echo $paginator->numbers();
			echo '&nbsp;&nbsp;';
			echo $paginator->next('»»»', null, null, array('class' => 'disabled'));
			
		?>
		</td>
		<td width="50%" valign="bottom" align="left">
		<div id="news_loading" style="display: none;">
		<?php echo $html->image('loading.gif', array('alt'=>'Загрузка новостей...', 'width'=>'16', 'height'=>'16')); ?> Загрузка новостей...
		</div>
		</td>
	</tr>
	</table>
<?php 
	echo $js->writeBuffer(); // Write cached scripts 
?>

