<?php
namespace Jamm\HTTP;

class Request implements IRequest
{
	protected $method;
	protected $headers;
	protected $data;
	protected $accept;

	const method_GET = 'GET';
	const method_POST = 'POST';
	const method_PUT = 'PUT';
	const method_DELETE = 'DELETE';

	/**
	 * @param bool $parse - parse current input to object's variables (input request)
	 * @return \Jamm\HTTP\Request
	 *
	 */
	public function __construct($parse = false)
	{
		$this->method = self::method_GET;
		if ($parse) $this->BuildFromInput();
		$this->setHeader('Content-type', 'text/plain');
	}

	public function BuildFromInput()
	{
		$this->headers = $_SERVER;
		$this->accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
		$this->method = $_SERVER['REQUEST_METHOD'];
		switch ($this->method)
		{
			case 'HEAD':
			case 'GET':
				$this->data = $_GET;
				break;
			case 'POST':
				$this->data = $_POST;
				break;
			default:
				parse_str(file_get_contents('php://input'), $this->data);
		}
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
	 * @return null|array|string|numeric
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
		$this->headers[$header] = $value;
	}

	/**
	 * Set the request method
	 * @param $method
	 * @return void
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
	}

	/**
	 * Send request by URL. Pass $Response argument, if you need response
	 * @param $URL
	 * @param IResponse|null $Response
	 * @return bool|IResponse
	 */
	public function Send($URL, IResponse $Response = NULL)
	{
		$url_data = parse_url($URL);
		$fp = fsockopen($url_data['host'], 80);
		if (!$fp) return false;
		$path = (isset($url_data['path']) ? $url_data['path'] : '/').
				(isset($url_data['query']) ? '?'.$url_data['query'] : '');
		$data = $this->getData();
		if (!empty($data) && is_array($data)) $data = http_build_query($data);

		if ($this->method==self::method_GET)
		{
			fwrite($fp, $this->method." $path?$data HTTP/1.0\r\n");
		}
		else
		{
			fwrite($fp, $this->method." $path HTTP/1.0\r\n");
			fwrite($fp, "Content-Length: ".strlen($data)."\r\n");
		}
		fwrite($fp, "Host: {$url_data['host']}\r\n");
		foreach ($this->getHeaders() as $header_name => $header_value)
		{
			fwrite($fp, "$header_name: $header_value\r\n");
		}

		fwrite($fp, "Connection: Close\r\n\r\n");

		if ($this->method!=self::method_GET)
		{
			fwrite($fp, $data);
		}
		if (!empty($Response)) return $this->ReadResponse($fp, $Response);
		else return true;
	}

	/**
	 * @param \resource $fresource
	 * @param IResponse $response
	 * @return IResponse
	 */
	protected function ReadResponse($fresource, IResponse $response)
	{
		//read headers
		$status_header = '';
		$headers = array();
		while (!feof($fresource))
		{
			$header = trim(fgets($fresource));
			if (!empty($header))
			{
				if (empty($status_header)) $status_header = $header;
				if (strpos($header, ':')!==false)
				{
					$header = explode(':', $header);
					$headers[trim($header[0])] = trim($header[1]);
				}
				else $headers[] = $header;
			}
			else break;
		}
		$response->setHeaders($headers);
		if (!empty($status_header))
		{
			$status_header = explode(' ', $status_header);
			$response->setStatusCode(intval(trim($status_header[1])));
		}

		//read body
		$body = '';
		while (!feof($fresource)) $body .= fread($fresource, 4096);
		fclose($fresource);

		if (!empty($body)) $response->setBody($body);

		return $response;
	}

	/**
	 * Set array of data
	 * @param array $values
	 */
	public function setData(array $values)
	{
		$this->data = $values;
	}
}
