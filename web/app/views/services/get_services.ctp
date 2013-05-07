<?php
/*
 * Created on 05.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
//pr($servicesList);
?>
<?php
  if(isset($servicesList)) {
    echo $this->Js->object($servicesList);
  }
?>
