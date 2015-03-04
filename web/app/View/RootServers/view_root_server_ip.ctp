<?php
/*
 * Created on 20.01.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
  foreach ( $ipList as $ip ) {
        if (empty($ip['used']))
        {
            echo $ip['ip']." (".$ip['type'].")<br/>\n";
        }
        else
        {
            echo '<s>'.$ip['ip']."</s> (".$ip['type']."): #".$ip['used']."<br/>\n";
        }
     		  
	}
 
 
?>
