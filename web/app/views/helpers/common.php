<?php

class CommonHelper extends AppHelper {
	
	
	function niceDate($date, $format = 'full', $type = 'text')
	{
		
		$months = array (	'1'  => 'Января',
							'2'  => 'Февраля',
							'3'  => 'Марта',
							'4'  => 'Апреля',
							'5'  => 'Мая',
							'6'  => 'Июня',
							'7'  => 'Июля',
							'8'  => 'Августа',
							'9'  => 'Сентября',
							'10' => 'Октября',
							'11' => 'Ноября',
							'12' => 'Декабря'							
						);

		$today    = getdate();
		//$dateDiff = time() - strtotime($date);

		if ($format == 'full'){

			if ($type == 'text')
			{
				$parsedDate = getdate(strtotime($date));
			}
			else
			if ($type == 'unix')
			{
				$parsedDate = getdate($date);
			}

			if ( $today['year'] == $parsedDate['year']
					and
				 $today['mon'] == $parsedDate['mon']
				 	and
				 $today['mday'] == $parsedDate['mday']	 
			   )
			{
				$day = 'Сегодня в';		
			}			
			else {
				$day = sprintf('%1$02d', $parsedDate['mday']).' '.$months[$parsedDate['mon']];
			}

			if ($today['year'] != $parsedDate['year'])
			{
				$year = $parsedDate['year'];
			}
			else
			{
				$year = '';
			}

			$niceDay = $day.' '.$year;

			$nice = $niceDay.sprintf(' %1$02d:%2$02d', $parsedDate['hours'], $parsedDate['minutes']);

		}
		else
		if ($format == 'dig')
		{
			
			if ($type == 'text')
			{
				$nice = date("d.m.Y H:i", strtotime($date));
			}
			else
			if ($type == 'unix')
			{
				$nice = date("d.m.Y H:i", $date);
			}
		}
		else
		{
			$nice = $date; // Пустышка
		}
	

		return $nice;
	}

	function niceDateDiff($dateFrom = null, $dateTo = null)
	{
		$uDateFrom = strtotime($dateFrom);
		$uDateTo   = strtotime($dateTo);

		$uDateDiff = abs($uDateTo - $uDateFrom);

		if ($uDateDiff <= 120)
		{
			return 'Только что';
		}
		else
		if ($uDateDiff > 120 and $uDateDiff <= 300)
		{
			return 'Менее 5 минут';
		}
		else
		if ($uDateDiff > 300 and $uDateDiff <= 1800)
		{
			return 'Менее часа';
		}
		else
		if ($uDateDiff > 1800 and $uDateDiff <= 86400)
		{
			return 'Больше одного часа';
		}
		else
		if ($uDateDiff > 86400 and $uDateDiff <= 604800)
		{
			return 'Больше суток';
		}
		else
		if ($uDateDiff > 604800 and $uDateDiff <= 2592000)
		{
			return 'Больше недели';
		}
		else
		if ($uDateDiff > 2592000)
		{
			return 'Больше месяца';
		}
		else
		{
			return $uDateDiff;
		}

	}

	// Генерирование нломера личного счёта по ID клиента
    function userBill($userId = null)
    {

        if ($userId)
        {
                return sprintf('47%1$07d', $userId);
        }
        else
        {
                return false;
        }
    }

	function getLoadIndicator($cur = null, $max = null)
	{
		if ($max > 0)
		{
			$load = ($cur/$max)*100;
		}
		else
		{
			return 'players_load_free';
		}
		
								
		if ($load == 0){
			$div = 'players_load_free';
		}
		else
		if ($load >= 0 and $load <= 20){
			$div = 'players_load_20';
		}
		else
		if ($load > 20 and $load <= 40){
			$div = 'players_load_40';
		}
		else
		if ($load > 40 and $load <= 60){
			$div = 'players_load_60';
		}
		else
		if ($load > 60 and $load <= 80){
			$div = 'players_load_80';
		}
		else
		if ($load > 80 and $load < 100){
			$div = 'players_load_100';
		}
		else
		if ($load >= 100){
			$div = 'players_load_full';
		}

		return $div;	
	}

	function versionStatus($current = null, $userVersion = null, $type = null)
	{
		if (@$type == 'hl1')
		{
			$version = preg_split('/\//', $userVersion);
			$userVersion = $version[0];
		}

		if (!(is_null($current) or is_null($userVersion)))
		{
			if ($current == $userVersion)
			{
				return '<span style="color: #668237;">'.$userVersion.'</span>';
			}
			else
			{
				return "<a class='qlabs_tooltip_diagonal_right qlabs_tooltip_style_39 delay_100' href='#'>
							<span style='width: 400px;'>
								<strong>Версия вашего сервера устарела!</strong>
								Текущая версия: <i>$current</i>, рекомендуем обновить<br/>
								сервер как можно скорее.<br/>
								Для этого просто нажмите кнопку <i class='icon-retweet icon-white'></i> <i>Обновить</i><br/>
								и дождитесь завершения операции.
							</span>
							<i class='icon-warning-sign'></i> $userVersion
						</a>";
			}
		}
		else
		{
			return @$userVersion;
		}

	}

	function genSalt($length = 16)
	{
		$consonantes = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz123456789";
		$r = '';		
		for ($i = 0; $i < $length; $i++) {

				$r .= $consonantes{rand(0, strlen($consonantes) - 1)};
		}
		return $r;
	}

	/* Форматирование номера телефона */
	function parsePhoneNum($num = null)
	{
		if (!is_null($num))
		{

			if (preg_match('/^(?P<first>\d{1})(?P<code>\d{3})(?P<num1>\d{3})(?P<num2>\d{4})$/', $num, $matches))
			{
				if ($matches['first'] != 8)
				{
					$first = '+'.$matches['first'];
				}
				else
				{
					$first = $matches['first'];
				}

				return sprintf('%s (%d) %d-%d', $first, $matches['code'], $matches['num1'], $matches['num2']);
			}
		}
		else
		{
			return false;
		}
	}
		
}

?>