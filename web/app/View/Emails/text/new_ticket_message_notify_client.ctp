<?php
/*
 * Created on 17.02.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
Тикет #<?php echo $ticket['id'].' "'.$ticket['title'].'"'; ?>

В тикете создано сообщение:
***************************
<?php echo $ticket['text'];?>

***************************

Вы можете полностью прочитать все сообщения 
тикета и написать ответ в панели управления 
по адресу:

https://panel.teamserver.ru/supportTickets

Пожалуйста, не отвечайте на это письмо,
пишите ответы в Панели управления.

<?php
if (!empty($servers)) {	
	?>
Серверы:
***************************
<?php
	foreach ($servers as $server) {
		echo "#".@$server['id']."\n";
		echo "Слотов: ".@$server['slots']."\n";
		echo @$server['address'].":".@$server['port']."\n";
		echo "Статус: ".@$server['status'].": ".@$server['statusDescription']."\n";
		echo "***************************\n";
	}
?>
***************************
<?php	
}
?>

