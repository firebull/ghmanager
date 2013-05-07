<?php
/*
 * Created on 04.10.2011
 */

?>
<div class="helper" style="margin-top: 10px;">
	<table border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 10px;">
		<tr>
			<td  style="text-align: right; padding-right: 5px;">
			Форум:<br/>
			Wiki:<br/>
			email:
			</td>
			<td  style="text-align: left;">
			<?php echo $html->link('forum.ghmanager.com', 'http://forum.ghmanager.com',
														 array ('target' => '_blank'));?>
			<br/>
			<?php echo $html->link('wiki.ghmanager.com', 'http://wiki.ghmanager.com',
														 array ('target' => '_blank'));?>
			<br/>
			<?php echo $text->autoLinkEmails('support@dummy');?>
			</td>
		</tr>
	</table>
	<table border="0" cellpadding="0" cellspacing="0" style="padding-top: 5px;" align="center">
		<tr>
			<td>
			<?php $vklogo = $html->image('icons/Vkontakte_Simple_30x30.png',
									 array( 'alt' => 'Мы Вконтакте',
											'title' => 'Мы Вконтакте',
											'width' => 30,
											'height' => 30
											)
									);

			echo $html->link( $vklogo,
							  'http://vkontakte.ru/teamservergroup',
							  array ('target' => '_blank',
							  		 'escape' => false));

			?>
			</td>
			<td style="padding-left: 10px;">
			<?php $twitterLogo = $html->image('icons/facebook.png',
									 array( 'alt' => 'Мы на Facebook',
											'title' => 'Мы на Facebook',
											'width' => 30,
											'height' => 30
											));

				echo $html->link( $twitterLogo,
							  'http://www.facebook.com/TeamServer',
							  array ('target' => '_blank',
							  		 'escape' => false));
			?>
			</td>
			<td style="padding-left: 10px;">
			<?php $twitterLogo = $html->image('icons/twitter.png',
									 array( 'alt' => 'Наш Twitter',
											'title' => 'Наш Twitter',
											'width' => 30,
											'height' => 30
											));

				echo $html->link( $twitterLogo,
							  'http://www.twitter.com/ru_TeamServer',
							  array ('target' => '_blank',
							  		 'escape' => false));
			?>
			</td>
		</tr>
	</table>
</div>


