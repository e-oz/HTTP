<?php
namespace Jamm\HTTP;

class Response implements IResponse
{
	protected $status_code;
	protected $body;
	protected $headers;
	/** @var ISerializer */
	protected $Serializer;

	public function __construct($body = '', $status_code = 200)
	{
		$this->body        = $body;
		$this->status_code = $status_code;
	}

	public function getStatusCode()
	{
		return $this->status_code;
	}

	/** @param int $status_code     */
	public function setStatusCode($status_code)
	{
		$this->status_code = (int)$status_code;
	}

	/**
	 * Set header for the response
	 * @param string $header
	 * @param string|number $value
	 */
	public function setHeader($header, $value)
	{
		$this->headers[$header] = $value;
		if (strtolower($header)==='location' && ($this->status_code < 300 || $this->status_code > 399)) $this->setStatusCode(301);
	}

	public function getHeader($header)
	{
		return isset($this->headers[$header]) ? $this->headers[$header] : NULL;
	}

	/**
	 * Get body of the response
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Set body of the response
	 * @param $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}

	/**
	 * Send headers and body to output
	 */
	public function Send()
	{
		if (!empty($this->Serializer))
		{
			$body = $this->Serializer->serialize($this->body);
		}
		else
		{
			$body = $this->body;
			if (!is_scalar($body))
			{
				$body = 'Result of request should be serialized to send through. Specify in "ACCEPT" header type of acceptable method of serialization.';
				//Unsupported Media Type
				$this->status_code = 415;
			}
		}
		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
		header($protocol.' '.$this->getStatusCode());
		$headers = $this->getHeaders();
		if (!empty($headers))
		{
			foreach ($headers as $header_key => $header_value)
			{
				header($header_key.': '.$header_value);
			}
		}
		print $body;
	}

	public function setSerializer($Serializer)
	{
		$this->Serializer = $Serializer;
	}

	public function isStatusError()
	{
		//http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
		return ($this->status_code > 399);
	}

	/**
	 * @return \Jamm\HTTP\ISerializer|NULL
	 */
	public function getSerializer()
	{
		return $this->Serializer;
	}
}
