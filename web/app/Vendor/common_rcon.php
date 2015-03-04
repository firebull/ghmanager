<?php

class CommonRcon
{
	protected $host;
	protected $port;
    private $rconpassword = null;
    private $fp;
    private $last_ping = null;

	public function __construct($host, $port)
	{
		$this->host = $host;
    	$this->port = $port;
    	$this->connect();
	}
	
	public function set_password($pw) 
	{
    	$this->rconpassword = $pw;
	}
	
	public function execute($str)
	{
		if(!$this->rconpassword) 
		{
			return false;
		}
    	
		$this->send('rcon '.$this->rconpassword.' '.$str);
    	
		return $this->get_response();
    }

	private function send($str) 
	{
		fwrite($this->fp, "\xFF\xFF\xFF\xFF$str\x00");
	}

	private function get_response()
	{
		stream_set_timeout($this->fp, 0, 7e5);
		$s = '';
		$start = microtime(true);
	    
		do 
        {
			$read = fread($this->fp, 9999);
			$s .= substr($read, strpos($read, "\n") + 1);
			
			if(!isset($end))
    		{
				$end = microtime(true);
			}
    		
			$info = stream_get_meta_data($this->fp);

		} while(!$info['timed_out']);

		$this->last_ping = round(($end - $start) * 1000);
		return $s;
	}

	public function disconnect()
    {
		if(is_resource($this->fp)) 
    	{
			fclose($this->fp);
			return true;
		}
		return false;
	}

	public function connect()
	{
		if(is_resource($this->fp)) 
		{
			$this->disconnect();
		}

		$this->fp = fsockopen('udp://'.$this->host, $this->port, $errno, $errstr, 5);
		
		if(!$this->fp)
		{
			throw new Exception('RCON connect error: '.$errstr.' ('.$errno.')');
		}
    }
    
    public function authenticate()
	{

    }

    public function get_game_status() 
	{
		$this->send('getstatus');
        $response = $this->get_response();

        if(empty($response)) return false;

        list($dvarslist, $playerlist) = explode("\n", $response, 2);

		$dvarslist = explode("\\", $dvarslist);
		$dvars = array();
		
		for($i=1; $i<count($dvarslist); $i+=2) 
		{
			$dvars[$dvarslist[$i]] = $dvarslist[$i+1];
		}

		$playerlist = explode("\n", $playerlist);
		array_pop($playerlist);
		$players = array();
		
		foreach($playerlist as $value)
		{
			list($score, $ping, $name) = explode(" ", $value, 3);
			
			$players[] = array
			(
				'name' => substr($name, 1, -1),
				'score' => $score,
				'ping' => $ping
			);
		}
		
		return array($dvars, $players);
    }

	public function get_game_info() 
	{
		$this->send('getinfo');
		$response = $this->get_response();

		if(empty($response)) return false;

		$dvarslist = explode("\\", $response);
		$dvars = array();
		
		for($i=1; $i<count($dvarslist); $i+=2) 
		{
			$dvars[$dvarslist[$i]] = $dvarslist[$i+1];
		}

		return $dvars;
    }
    
	public function get_last_ping()
	{
		return $this->last_ping;
	}
}

?>
