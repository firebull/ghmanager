<?php
/*
 * Created on 16.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */

?>

Кто-то, возможно, вы, зарегистрировался в Административной панели GH Manager
Если вы получили это письмо по ошибке, пожалуйста, не принимайте никаких действий.
Иначе нажмите на ссылку, чтобы подтвердить регистрацию (Click on the link below to complete registration):

<?php echo $html->link('https://panel.teamserver.ru/users/verify/t:'.$hash.'/n:'.$username,
								'https://panel.teamserver.ru/users/verify/t:'.$hash.'/n:'.$username); ?>
