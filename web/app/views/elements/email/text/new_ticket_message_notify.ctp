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

<?php
if (!empty($servers)){	
	?>
Серверы:
***************************
<?php
	foreach ($servers as $server){
		echo "#".@$server['id']."\n";
		echo "Слотов: ".@$server['slots']."\n";
		echo @$server['address'].":".@$server['port']."\n";
		echo "Статус: ".@$server['status'].": ".@$server['statusDescription']."\n";
		echo "***************************\n";
	}
?>

<?php	
}
?>
Клиент:
***************************
<?php 
echo @$user['second_name']." ".@$user['first_name']."\n";
echo @$user['username']."\n";
echo @$user['email']."\n";
echo @$user['last_ip']."\n";
echo @$user['created']."\n";
?>
***************************
