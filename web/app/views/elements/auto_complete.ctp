<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 */
?>
<?php echo $form->create($modelName, array('url' => array('controller'=>$modelName.'s', 'action'=>'control'))); ?>
	<?php echo $ajax->autoComplete($string, '/'.$modelName.'s/autoComplete/'.$string.'/'.$modelName); ?>
</form>
