<?php
/*
 * Created on 28.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 //pr($status);
  if(isset($status)) {
    echo $this->Js->object($status);
  }
  else
  {
  	echo "{}";
  }
?>
