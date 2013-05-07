<?php
/*
 * Created on 22.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
echo '<result ' .
	 'code="'.@$errno.
     '" action="'.@$action.
     '" shopId="'.@$shopId.
     '" invoiceId="'.@$invoiceId.'"';

if (@$message){
	
	echo ' techMessage="'.$message.'"';
	
}

echo ' />';
?>


