<?php
/*
 * Created on 26.03.2015
 *
 * File created for project GH Manager
 * by Nikita Bulaev
 */
 $message = $this->Session->flash();

 if (preg_match("/(error|warn|alert)/miu", $message)){
 	echo json_encode(['error' => strip_tags($message)]);
 } else {
 	echo json_encode(['info' => strip_tags($message)]);
 }
?>
