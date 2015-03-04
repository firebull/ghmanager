<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 * НЕ РАБОТАЕТ!!! НЕДОДЕЛАНО!!!
 */
 
 echo $ajax->form(array('type' => 'post',
    'options' => array(
        'model'=>'User',
        'update'=>'input_field',
        'url' => array(
            'controller' => 'Users',
            'action' => 'search'
        )
    )
));
?>
	<select name="string" style="width: 267px;" onchange="this.form.submit()">
					<option value="username">Login</option>
					<option value="second_name">SName</option>
	</select>
	<div id="input_field">
	
	</div>
<?php echo $ajax->submit('Submit', array('url'=> array('controller'=>'users', 'action'=>'search'), 'update' => 'input_field'));?>
</form>

