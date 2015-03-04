<?php
/**
 * Valve RCON Class. - The php5 version of my original RCON class
 * Copyright (C) 2008  Shannon Wynter
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * ChangeLog
 * -----------
 * version 1.0, 2008-08-07, Shannon Wynter {@link http://fremnet.net/contact}
 *  - Initial release
 *
 * @version 1.0
 * @author Shannon Wynter {@link http://fremnet.net/contact}
 * @copyright Copyright &copy; 2006 Shannon Wynter (Fremnet)
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL 2.0 or greater
 * @package Fremnet
 * @subpackage Valve_RCON
 */
 
/**
 * Valve RCON Class
 *
 * <b>Synopsis:</b>
 *
 * <i>General Usage:</i>
 * <code>
 * include('valve_rcon.php');
 * $r = new ValveRcon('secret', '121.45.193.22', 27015, ValveRcon::PROTO_SOURCE);
 * $r->connect();
 * $r->authenticate();
 * print $r->execute('status');
 * print $r->execute('kick user');
 * $->disconnect();
 * </code>
 *
 * <i>Traditional Lazy Usage:</i>
 * <code>
 * include('valve_rcon.php');
 * $r = new ValveRcon('secret', '121.45.193.22', 27015, ValveRcon::PROTO_SOURCE);
 * print $r->execute('status');
 * </code>
 *
 * <i>Note: Traditional usage has performance penalties over multiple commands.</i>
 *
 * @version 1.0.0
 * @author Shannon Wynter (http://fremnet.net/contact)
 * @copyright Copyright (c) 2008, Shannon Wynter (Fremnet)
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL 2.0 or greater
 * @package Fremnet
 * @subpackage ValveRcon
 * ***************************************************
 * By Nikita Bulaev
 * Changed Class name from Valve_RCON to ValveRcon 
 * for partitial compability with CakePHP
 * ***************************************************
 */
class ValveRcon {

	/**#@+
	 * Constants required for setting which protocol the class is using
	 */
	/**
	 * Classic Protocol
	 */
	const PROTO_CLASSIC = 1;
	
	/**
	 * Source Protocol - The default mode of operation
	 */
	const PROTO_SOURCE  = 2;
	/**#@-*/

	/**#@+
	 * Constants used internally for talking to/from the server
	 */
	const SERVERDATA_EXECCOMMAND    = 2;
	const SERVERDATA_AUTH           = 3;

	const SERVERDATA_RESPONSE_VALUE = 0;
	const SERVERDATA_AUTH_RESPONSE  = 2;
	/**#@-*/
	
	private $password;
	private $protocol;
	private $host;
	private $port;

	/**
	 * Storage for the classic protocol challange
	 */
	private $challenge;

	/**
	 * How long to wait for UDP packets to become available
	 */
	private $classic_timeout        = 2;
	
	/**#@+
	 * Socket level timeouts
	 *
	 * @link http://au2.php.net/manual/en/function.stream-set-timeout.php
	 */
	private $timeout_seconds        = 0;
	private $timeout_microseconds   = 250000;
	/**#@-*/

	/**
	 * Socket storage
	 */
	private $socket;
	
	/**
	 * Incremented after each command is sent
	 */
	private $id                     = 0;
	
	/**
	 * Class Constructor
	 *
	 * @param string  $password             (''          ) The password to connect to the server with
	 * @param string  $host                 ('127.0.0.1' ) The host to connect to
	 * @param integer $port                 (27015       ) The port to connect on
	 * @param integer $protocol             (PROTO_SOURCE) The protocol to use
	 */
	public function __construct($password='', $host = '127.0.0.1', $port = 27015, $protocol = self::PROTO_SOURCE) {
		$this->host     = $host;
		$this->port     = $port;
		$this->password = $password;
		$this->protocol = $protocol;
	}

	/**
	 * Get, and Set while Disconnected
	 *
	 * Called by getter/setters that require the socket to be disconnected
	 * before setting.
	 *
	 * Call with both the name and the value to store a new value or, call with
	 * only the name to return the current value
	 *
	 * @throws Exception
	 * @param string  $name                                The name of the variable/function
	 * @param mixed   $value                (null        ) The value to store if there is one.
	 * @return mixed The current value
	 */
	private function get_set_disconnected($name, $value = null) {
		if (!is_null($value)) {
			if ($this->socket)
				throw new Exception("Cannot change $name while connected");
			$this->$name = $value;
		}
		return $this->$name;
	}
	
	/**
	 * Update Socket Timeout
	 *
	 * Called internally to update the socket timeout if the socket is connected
	 */
	private function update_socket_timeout() {
		if ($this->socket)
			stream_set_timeout($this->socket, $this->timeout_seconds, $this->timeout_microseconds);
	}

	/**
	 * Host Getter/Setter
	 *
	 * Returns the current value of host, or replaces it with passed value
	 *
	 * @param string  $host                 (null        ) The new host name/ip
	 * @return string The current host
	 */
	public function host($host = null) {
		return $this->get_set_disconnected('host', $host);
	}
	
	/**
	 * Port Getter/Setter
	 *
	 * Returns the current value of port, or replaces it with passed value
	 *
	 * @param integer $port                 (null        ) The new port
	 * @return integer The current port
	 */
	public function port($port = null) {
		return $this->get_set_disconnected('port', $port);
	}
	/**
	 * Protocol Getter/Setter
	 *
	 * Returns the current value of protocol, or replaces it with passed value
	 *
	 * Defined constants:
	 * 	PROTO_CLASSIC, PROTO_SOURCE
	 *
	 * @param integer $protocol             (null        ) The new protocol
	 * @return integer The current protocol
	 */
	public function protocol($protocol = null) {
		return $this->get_set_disconnected('protocol', $protocol);
	}
	
	/**
	 * Classic Timeout Getter/Setter
	 *
	 * Returns the current value of the classic timeout, or replaces it
	 * with a passed value.
	 *
	 * You shouldn't need to tweak this, but it's here in case you do
	 *
	 * @param integer $timeout              (null        ) The new timeout
	 * @return integer The current timeout
	 */
	public function classic_timeout($timout = null) {
		if (!is_null($timeout))
			$self->classic_timeout = $timeout;
		return $self->classic_timeout;
	}

	/**
	 * Socket Timeout Setter/Getter
	 *
	 * Sets the socket timeout to the number of seconds + the number of microseconds if passed
	 * otherwise just returns the current settings
	 *
	 * @param integer $timeout_seconds      (null       ) The number of seconds
	 * @param integer $timeout_mircoseconds (null       ) The number of microseconds
	 * @return array (timeout_seconds, timeout_microseconds)
	 */
	public function socket_timeout($timeout_seconds = null, $timeout_microseconds = null) {
		// Default the other to zero if one is set
		if (is_null($timeout_seconds) and !is_null($timeout_microseconds))
			$timeout_seconds = 2;
		if (is_null($timeout_microseconds) and !is_null($timeout_seconds))
			$timeout_microseconds = 2000;

		// By this point, neither should be null, so checking either will do
		if (!is_null($timeout_seconds)) {
			$this->timeout_microseconds = $timeout_microseconds;
			$this->timeout_seconds      = $timeout_seconds;
			$this->update_socket_timeout();
		}

		// Return current values in an array
		return array(
				$this->timeout_seconds,
				$this->timeout_microseconds
		);
	}
	
	/**
	 * Connect
	 *
	 * Connect to the server
	 *
	 * @throws Exception
	 */
	public function connect() {
		if ($this->protocol == self::PROTO_CLASSIC)
			$this->socket = @fsockopen('udp://' . $this->host, $this->port, $errno, $errstr, 30);
		else
			$this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 30);

		if (!$this->socket)
			throw new Exception("Не удалось подключиться к серверу: $errstr ($errno)");
		$this->update_socket_timeout();
	}

	/**
	 * Disconnect
	 *
	 * Disconnect from the server
	 *
	 * @throws Exception
	 */
	public function disconnect() {
		if (!$this->socket)
			throw new Exception('Socket not opened');
		fclose($this->socket);
		unset($this->socket);
	}

	/**
	 * Authenticate
	 *
	 * In the case of source, connect and send password
	 * In the case of classic, connect and ask the server to say hi back
	 *
	 * @throws Exception
	 */
	public function authenticate() {
		if (!$this->socket)
			throw new Exception('Socket not opened');
		if ($this->protocol == self::PROTO_CLASSIC) {
			$this->challenge_rcon();
			$this->classic_write('echo hi');
			if (preg_match('/Bad rcon_password/', $this->classic_read()))
			{
				throw new Exception("Неверный пароль");
			}
		} else {
			$packet_id = $this->source_write(self::SERVERDATA_AUTH, $this->password);

			$result = $this->source_read(null, 2);
			if (!empty($result) && $result[1]['id'] == -1)
			{
				throw new Exception("<br/>Ошибка аутентификации, проверьте пароль и параметры сервера.");
			}
			else
			if (empty($result))
			{
				throw new Exception("<br/>Сервер не ответил.");
			}
		}
	}

	/**
	 * Execute
	 *
	 * This is the big function, this right here is the portal to the whole
	 * entire complex world that this class represents
	 *
	 * It doesn't matter which type of rcon you are using, be it classic or source.
	 * To get somewhere in life all you have to do is execute($command)
	 *
	 * {@link Valve_RCON See Valve_RCON for a synopsis}
	 *
	 * @param string  $command                            The rcon command to send to the server
	 * @return string The results of that command
	 */
	public function execute($command) { 
		// Traditionally people seem to ignore the fact that this is now a TCP protocol not the original udp version
		$auto_disconnect = false;
		if (!$this->socket) {
			// Automatically manage the connection if there wasn't one created before now
			$auto_disconnect = true;
			$this->connect();
			$this->authenticate();
		}

		if (@$this->protocol == self::PROTO_CLASSIC) {
			// Use classic read/write commands to get the job done
			$this->classic_write($command);
			$result = $this->classic_read();
		} else {
			// Use the newer source commands, sorry about the mess, it's more flexable this way
			$command = '"' . trim(str_replace(' ', '" "', $command)) . '"';
			$id = $this->source_write(self::SERVERDATA_EXECCOMMAND, $command);
	
			// Read the packets and re-assemble them
			$packets = $this->source_read($id);
			$result = $this->assemble_packets($packets);
			if (!empty($result[$id]['string1'])){
				$result = $result[$id]['string1'];
			}
			else
			{
				throw new Exception("Получен пустой ответ от сервера.\nПроверьте соответствие пароля в панели и в конфиге.\nНапишите нужный пароль в панели (кнопка с шестеренкой) и сохраните его.");
			}
			
		}
		// Remmember what I said above about managing the connection because of lazy people?
		if ($auto_disconnect) {
			$this->disconnect();
		}
		return $result;
	}

	/**
	 * Assemble Packets
	 *
	 * By default source packets are not very useful. If you pass an array of them
	 * to this function it will do it's best to aggrogate the packets into one
	 * concise response.
	 *
	 * <b>Return array structure</b>
	 * <code>
	 * array(
	 *	'Request ID' => array(
	 *		'id'       => 'Request ID',
	 *		'response' => 'Response code',
	 *		'string1'  => 'The response string',
	 *		'string2'  => 'The null string'
	 *	), ...
	 * )
	 * </code>
	 * 
	 * @throws Exception
	 * @param array   $packets                            The packets to be assembled
	 * @return array
	 */
	protected function assemble_packets($packets) {
		if (!is_array($packets))
			throw new Exception('Wrong answer from server: incorrect packets.');

		foreach ($packets as $packet) {
			if (isset($result[$packet['id']])) {
				$result[$packet['id']]['id']        = $packet['id'];
				$result[$packet['id']]['response']  = $packet['response'];
				$result[$packet['id']]['string1']   .= $packet['string1'];
				$result[$packet['id']]['string2']   .= $packet['string2'];
			} else
				$result[$packet['id']] = $packet;
		}
		return $result;
	}

	/**
	 * Source Read
	 *
	 * Read data from servers using the source protocol
	 * Optionally ignores all packets that don't match the ID passed
	 * Optionally can speed up the process slightly (a few MS nothing more) by only
	 * waiting for a specific number of packets - useful if you KNOW how many packets
	 * your command is going to return, but generally not recommended.
	 * <b>Return array structure</b>
	 * <code>
	 * array(
	 *	array(
	 *		'id'       => 'Request ID',
	 *		'response' => 'Response code',
	 *		'string1'  => 'The response string',
	 *		'string2'  => 'The null string'
	 *	), ...
	 * )
	 * </code>
	 * 
	 * @throws Exception
	 * @param integer $expected_id          (null       ) The id of the request/response
	 * @param integer $expected_packets     (null       ) The expected number of packets
	 * @return array
	 */
	protected function source_read($expected_id = null, $expected_packets = null) {
		if (!$this->socket)
			throw new Exception('Socket not opened');

		// Pessimistic return value
		$result = false;

		// While we can successfully read from the network
		while ((is_null($expected_packets) or ($expected_packets > 0)) and ($raw_size = fread($this->socket, 4))) {
			// A little hack to help speed it up, where we know the number of packets to expect
			$expected_packets--;

			// Unpacks the raw_header into $size
			extract(unpack('V1size',$raw_size));

			// Read the packet in
			$raw_packet = fread($this->socket, $size);
			// Sometimes strange things happen - make sure we have all of it.
			while (strlen($raw_packet) < $size)
				$raw_packet .= fread($this->socket, $size - strlen($raw_packet));

			// Unpack the data structure, and extract it for use as variable (eg: $request_id)
			extract($packet = unpack('V1id/V1response/a*string1/a*string2', $raw_packet), EXTR_PREFIX_ALL, 'request');

			// Ignore packets that are not related - Hopefully we'll never see a packet we didn't want...
			if (!is_null($expected_id) and $request_id != $expected_id)
				continue;

			$result[] = $packet;
		}
		return $result;
	}

	/**
	 * Source Write
	 *
	 * Write data to servers using the source protocol
	 *
	 * Defined constants:
	 * 	SERVERDATA_EXECCOMMAND, SERVERDATA_AUTH
	 *
	 * @throws Exception
	 * @param integer $serverdata                         One of the above two constants...
	 * @param string  $string1              (''         ) The string to send to the server
	 * @param string  $string2              (''         ) The docs say this should stay blank
	 * @return integer The id of this request
	 */
	protected function source_write($serverdata, $string1='', $string2='') {
		if (!$this->socket)
			throw new Exception('Socket not opened');

		$id = ++$this->id;
	
		// We should never get this many packets out in a single request, but in case we do...
		if ($id > 4294967295)
			$id = $this->id = 1;

			// Build the packet
		$packet = pack("VV", $id, $serverdata) . $string1 . chr(0) . $string2 . chr(0);
		$packet = pack("V", strlen($packet)) . $packet;

		// Send the packet
		fwrite($this->socket, $packet, strlen($packet));

		// Request ID
		return $id;
	}

  /**
	 * challenge RCON
	 *
	 * Called internally by Authenticate to get the rcon challenge for this session
	 *
	 * @throws Exception
	 */
	private function challenge_rcon() {
		if (!$this->socket)
			throw new Exception('Socket not opened');
		if (!$this->protocol == self::PROTO_CLASSIC)
			throw new Exception('Incorrect protocol');
		fwrite($this->socket, "\xff\xff\xff\xffchallenge rcon\x00");
		
		$this->challenge = substr($this->classic_read(false),15);
	}

	/**
	 * Smart UDP Read
	 *
	 * Face it, when it comes to reading UDP packets, PHP is rather dumb
	 * This gives us a slightly smarter udp read that waits for the socket to be unblocked
	 * and keeps reading until there's no more waiting data.
	 *
	 * @throws Exception
	 * @return string
	 */
	protected function smart_udp_read() {
		if (!$this->socket)
			throw new Exception('Socket not opened');

		$string_length = $timer = 0;
		$data  = '';

		// Wait for the socket to be ready and the data to appear - until classic_timeout
		while (strlen($data) == 0) {
			if ($timer < $this->classic_timeout) {
				$data .= fgets($this->socket, 2);
				usleep(1);
				$timer++;
			} else
				return 0;
		}
		// Keep reading until the length recorded matches the actual length - in hopes
		// that unread_bytes will keep up :)
		while ($string_length < strlen($data)) {
			$socket_status = socket_get_status($this->socket);
			$string_length = strlen($data);
			$data .= fgets($this->socket,$socket_status['unread_bytes'] + 1);
		}
		return $data;
	}
	
	/**
	 * Classic Read
	 *
	 * Read data from servers using the classic protocol
	 * Optionally strips that extra character from the front of the return.
	 * Not entirely sure it's a good idea to do that - as it does serve a purpose...
	 *
	 * @throws Exception
	 * @param boolean $strip_first          (true       ) Strip that extra byte from the front
	 * @return string
	 */
	protected function classic_read($strip_first = true) {
		if (!$this->socket)
			throw new Exception('Socket not opened');

		$response = '';

		while ($data = $this->smart_udp_read()) {
			// Is it a split-split packet
			if ($data[0] == "\xFE") {
				// How many of these split-split packets are we reading?
				$packet_count = ord($data[8]) & 15;
				$packets = array();
				// Read in the split-split packets
				for ($i = 0; $i < $packet_count; $i++) {
					// We already have the first one, skip re-reading it
					if ($i != 0)
						$data = $this->smart_udp_read();
					$index = ord($data[8]) >> 4;
					// Strip off the split-split packet header
					$packets[$index] = substr($data, 9);
				}
				// Re-assemble the split-split packets
				$data = '';
				foreach ($packets as $packet)
					$data .= $packet;
				}
			$response .= $data;
		}
		// Trim the first 4 or 5 bytes, trim, then strip out non-ascii..
		return preg_replace('/[^\s\x20-\x7E]l?/', '', trim(substr($response, $strip_first ? 5 : 4)));
	}

	/**
	 * Classic Write
	 *
	 * Write data to servers using the classic protocol
	 *
	 * @throws Exception
	 * @param string  $string                             The string to send to the servr
	 */
	protected function classic_write($string) {
		if (!$this->socket)
			throw new Exception('Socket not opened');
		if (!$this->challenge)
			throw new Exception('There is no challenge');
		fwrite($this->socket, "\xff\xff\xff\xffrcon " . $this->challenge . ' "' . $this->password . '" ' . $string . "\x00");
	}
}