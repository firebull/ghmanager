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
				echo $this->Html->link('Добавить новость', '#', array ('id' => 'add_news', 'title' => 'Добавить новость'));
			?>
		</td>
	</tr>
	</table>
<?php

foreach ( $news as $news_item ) {
	echo '<div class="news_item">';
	echo $this->Html->tag('h3', $news_item['Message']['title']);
	echo $this->Html->tag('small', strftime('%d %B %Y', strtotime($news_item['Message']['created'])), array ( 'style' => 'font-weight: bold; color: #777;'));
	echo $this->Html->tag('p', $this->Text->autoLinkUrls($news_item['Message']['body']));

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

			echo $this->Paginator->prev('«««', null, null, array('class' => 'disabled'));
			echo '&nbsp;&nbsp;';
			echo $this->Paginator->numbers();
			echo '&nbsp;&nbsp;';
			echo $this->Paginator->next('»»»', null, null, array('class' => 'disabled'));

		?>
		</td>
		<td width="50%" valign="bottom" align="left">
		<div id="news_loading" style="display: none;">
		<?php echo $this->Html->image('loading.gif', array('alt'=>'Загрузка новостей...', 'width'=>'16', 'height'=>'16')); ?> Загрузка новостей...
		</div>
		</td>
	</tr>
	</table>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>

