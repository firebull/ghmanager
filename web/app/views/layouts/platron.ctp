<?xml version="1.0" encoding="utf-8"?>
<response>
	<?php

		$sigString = 'platron;';

		if (!empty($desc))
		{
			echo $this->Html->tag('pg_description', $desc);
			$sigString .= $desc.';';
		}

		/* Ошибки */
		if (!empty($errorCode))
		{
			echo $this->Html->tag('pg_error_code', $errorCode);
			$sigString .= $errorCode.';';
		}

		if (!empty($errorDesc))
		{
			echo $this->Html->tag('pg_error_description', $errorDesc);
			$sigString .= $errorDesc.';';
		}
		
		// Соль.
		if (empty($salt))
		{			
			$salt = $this->Common->genSalt();
		}

		echo $this->Html->tag('pg_salt', $salt);
		$sigString .= $salt.';';

		if (!empty($status))
		{
			echo $this->Html->tag('pg_status', $status);
			$sigString .= $status.';';
		}

		
		$sig = md5($sigString.@$sigKey);

		echo $this->Html->tag('pg_sig', @strtolower($sig));

	?>
</response>