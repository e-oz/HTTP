<?php
namespace Jamm\HTTP;
class Request implements IRequest
{
	private $method;
	private $headers;
	private $data;
	private $accept;
	private $protocol_version = '1.0';
	private $connection;
	/** @var ICookie[] */
	private $cookies;
	private $files;

	public function __construct()
	{
		$this->method = self::method_GET;
		$this->setHeader('Content-type', 'text/plain');
	}

	public function BuildFromInput()
	{
		$this->headers = $_SERVER;
		$this->accept  = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
		$this->method  = $_SERVER['REQUEST_METHOD'];
		switch ($this->method)
		{
			case self::method_HEAD:
			case self::method_GET:
				$this->data = $_GET;
				break;
			case self::method_POST:
			case self::method_PUT:
			default:
				if (!empty($_POST))
				{
					$this->data = $_POST;
				}
				else
				{
					$raw_data = file_get_contents('php://input');
					if (!empty($raw_data))
					{
						parse_str($raw_data, $parse_by_values);
						$value = current($parse_by_values);
						if (count($parse_by_values)==1 && $value==='')
						{
							$this->data = $raw_data;
						}
						else
						{
							$this->data = $parse_by_values;
						}
					}
					else
					{
						$this->data = $_GET;
					}
				}
		}
		if (!empty($_COOKIE))
		{
			foreach ($_COOKIE as $cookie_name => $cookie_value)
			{
				$this->addCookie(new Cookie($cookie_name, $cookie_value));
			}
		}
		$this->files = $_FILES;
	}

	public function addCookie(ICookie $Cookie)
	{
		$key                 = $this->getNewOrExistingKeyInArray($Cookie->getName(), $this->cookies);
		$this->cookies[$key] = $Cookie;
	}

	public function removeCookie($name)
	{
		if (empty($name)) return false;
		$key = $this->getNewOrExistingKeyInArray($name, $this->cookies);
		unset($this->cookies[$key]);
		return true;
	}

	public function getCookie($name)
	{
		if (empty($name)) return false;
		$key = $this->getNewOrExistingKeyInArray($name, $this->cookies);
		return isset($this->cookies[$key]) ? $this->cookies[$key] : NULL;
	}

	public function getCookies()
	{
		return $this->cookies;
	}

	public function getFilesArray()
	{
		return $this->files;
	}

	/**
	 * Return header from array by key, or all keys
	 * @param string $key
	 * @return null|array|mixed
	 */
	public function getHeaders($key = null)
	{
		if (!empty($key))
		{
			$key = $this->getNewOrExistingKeyInArray($key, $this->headers);
			return isset($this->headers[$key]) ? $this->headers[$key] : NULL;
		}
		else return $this->headers;
	}

	/**
	 * Get type of request method
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Return key or all the keys of request
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key = null)
	{
		if (empty($key)) return $this->data;
		else
		{
			return isset($this->data[$key]) ? $this->data[$key] : NULL;
		}
	}

	/**
	 * Return HTTP_ACCEPT header
	 * @return string
	 */
	public function getAccept()
	{
		return $this->accept;
	}

	/**
	 * Check, if this type is acceptable
	 * @param string $type
	 * @return bool
	 */
	public function isAcceptable($type)
	{
		if (empty($type) || (stripos($this->getAccept(), $type)!==false)) return true;
		return false;
	}

	public function setHeader($header, $value)
	{
		if (empty($header)) return false;
		if ($value===NULL || $value==='')
		{
			$this->removeHeader($header);
		}
		else
		{
			$header                 = $this->getNewOrExistingKeyInArray($header, $this->headers);
			$this->headers[$header] = $value;
		}
	}

	public function removeHeader($header)
	{
		$key = $this->getNewOrExistingKeyInArray($header, $this->headers);
		unset($this->headers[$key]);
	}

	private function getNewOrExistingKeyInArray($key, $array)
	{
		if (empty($array)) return $key;
		$keys    = array_keys($array);
		$low_key = strtolower($key);
		foreach ($keys as $existing_key)
		{
			if ($low_key===strtolower($existing_key))
			{
				return $existing_key;
			}
		}
		return $key;
	}

	/**
	 * Set the request method
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
		if ($this->method!=self::method_GET) $this->setHeader('Content-type', 'application/x-www-form-urlencoded');
	}

	public function setDataKey($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function SetAccept($accept)
	{
		$this->accept = $accept;
		$this->setHeader('ACCEPT', $accept);
	}

	/**
	 * Send request by URL. Pass $Response argument, if you need response
	 * @param $URL
	 * @param IResponse|null $Response
	 * @return bool|IResponse
	 */
	public function Send($URL, IResponse $Response = NULL)
	{
		if (!empty($Response))
		{
			$Serializer = $Response->getSerializer();
			if (!empty($Serializer) && empty($this->accept))
			{
				/** @var ISerializer $Serializer */
				$this->SetAccept($Serializer->getMethodName());
			}
		}
		$url_data = parse_url($URL);
		if (empty($url_data['port']))
		{
			switch ($url_data['scheme'])
			{
				case 'https':
				case 'ssl':
					$url_data['port'] = 443;
					break;
				case 'http':
				default:
					$url_data['port'] = 80;
			}
		}
		if ($url_data['scheme']=='https' && stripos($url_data['host'], 'ssl://')===false)
		{
			$socket_host = 'ssl://'.$url_data['host'];
		}
		else
		{
			$socket_host = $url_data['host'];
		}
		if (!$this->createConnection($socket_host, $url_data['port'], $errno, $errstr))
		{
			trigger_error('Can not connect to '.$socket_host.' port '.$url_data['port'].PHP_EOL.$errstr, E_USER_NOTICE);
			return false;
		}
		$is_get_query = ($this->method===self::method_GET || $this->method===self::method_HEAD);
		$data         = $this->getData();
		if (!empty($data))
		{
			if (is_array($data))
			{
				if (PHP_VERSION_ID >= 50400)
				{
					$data = http_build_query($data, null, '&', PHP_QUERY_RFC3986);
				}
				else
				{
					$data = str_replace(
						array('+', '%7E'),
						array('%20', '~'),
						http_build_query($data));
				}
			}
			if ($is_get_query)
			{
				if (!empty($url_data['query']))
				{
					$url_data['query'] .= '&'.$data;
				}
				else
				{
					$url_data['query'] = $data;
				}
			}
		}
		$path = (isset($url_data['path']) ? $url_data['path'] : '/')
				.(isset($url_data['query']) ? '?'.$url_data['query'] : '')
				.(isset($url_data['fragment']) ? '#'.$url_data['fragment'] : '');
		$this->setHeader('Host', NULL);
		if (!$this->getHeaders('Connection'))
		{
			$this->setHeader('Connection', 'Close');
		}
		$this->writeToConnection($this->method.' '.$path.' HTTP/'.$this->protocol_version."\r\n");
		$this->writeToConnection("Host: {$url_data['host']}\r\n");
		if (!$is_get_query)
		{
			$this->setHeader('Content-Length', strlen($data));
		}
		$this->sendHeaders();
		$this->writeToConnection("\r\n");
		if (!$is_get_query && !empty($data))
		{
			$this->writeToConnection($data);
		}
		if (!empty($Response))
		{
			return $this->ReadResponse($Response);
		}
		else return true;
	}

	protected function sendHeaders()
	{
		if (!empty($this->headers))
		{
			foreach ($this->headers as $header_name => $header_value)
			{
				$this->writeToConnection("$header_name: $header_value\r\n");
			}
		}
		$this->sendCookies();
	}

	protected function sendCookies()
	{
		if (!empty($this->cookies))
		{
			foreach ($this->cookies as $Cookie)
			{
				$this->writeToConnection('Set-Cookie: '.$Cookie->getHeader()."\r\n");
			}
		}
	}

	protected function createConnection($host, $port, &$errno, &$errstr)
	{
		$this->connection = fsockopen($host, $port, $errno, $errstr);
		if (!$this->connection) return false;
		return true;
	}

	protected function writeToConnection($data)
	{
		if (!fwrite($this->connection, $data))
		{
			trigger_error('Can not write to connection!', E_USER_WARNING);
			return false;
		}
		return true;
	}

	/**
	 * @param IResponse $Response
	 * @return IResponse
	 */
	protected function ReadResponse(IResponse $Response)
	{
		//read headers
		$status_header = '';
		$headers       = array();
		while (!$this->feof($this->connection))
		{
			$header = trim($this->fgets($this->connection));
			if (!empty($header))
			{
				if (empty($status_header))
				{
					$status_header = $header;
					continue;
				}
				if (strpos($header, ':')!==false)
				{
					$header                    = explode(':', $header);
					$headers[trim($header[0])] = trim($header[1]);
				}
				else $headers[] = $header;
			}
			else break;
		}
		$Response->setHeaders($headers);
		if (!empty($status_header))
		{
			$status_header = explode(' ', $status_header, 3);
			$Response->setStatusCode(intval(trim($status_header[1])));
			if (isset($status_header[2]))
			{
				$Response->setStatusReason(trim($status_header[2]));
			}
		}
		//read body
		$body = '';
		while (!$this->feof($this->connection)) $body .= $this->fread($this->connection, 4096);
		$this->fclose($this->connection);
		$this->connection = NULL;
		if (!empty($body))
		{
			$Serializer = $Response->getSerializer();
			if (!empty($Serializer))
			{
				$body = $Serializer->unserialize($body);
			}
			$Response->setBody($body);
		}
		return $Response;
	}

	protected function fgets($handler)
	{
		return fgets($handler);
	}

	protected function feof($handler)
	{
		return feof($handler);
	}

	protected function fread($handler, $length)
	{
		return fread($handler, $length);
	}

	protected function fclose($handler)
	{
		return fclose($handler);
	}

	/**
	 * Set body of the request
	 * @param array|string $value
	 */
	public function setData($value)
	{
		$this->data = $value;
	}

	public function setProtocolVersion($protocol_version = '1.0')
	{
		$this->protocol_version = $protocol_version;
	}
}
