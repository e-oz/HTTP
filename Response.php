<?php
namespace Jamm\HTTP;

class Response implements IResponse
{
	protected $status_code;
	protected $body;
	protected $headers;
	protected $serialize_method;

	const serialize_JSON = 'JSON';
	const serialize_XML = 'XML';
	const serialize_PHP = 'PHP';

	const header_Serialized = 'API-Serialized';

	public function __construct($body = '', $status_code = 200)
	{
		$this->body = $body;
		$this->status_code = $status_code;
		$this->serialize_method = self::serialize_JSON;
	}

	public function getStatusCode()
	{
		return $this->status_code;
	}

	/** @param int $status_code	 */
	public function setStatusCode($status_code)
	{
		$this->status_code = (int)$status_code;
	}

	/**
	 * Set header for the response
	 * @param string $header
	 * @param string|numeric $value
	 */
	public function setHeader($header, $value)
	{
		$this->headers[$header] = $value;
		if ($header==='Location' && $this->status_code==200) $this->setStatusCode(301);
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
	 * Get Result of response - unpack value of body and headers
	 * @return bool|mixed
	 */
	public function getResult()
	{
		if ($this->getStatusCode() >= 400) return false;

		if (($serialization_method = $this->getHeader(self::header_Serialized)))
		{
			$this->serialize_method = $serialization_method;
			return $this->unserialize($this->body);
		}
		else return $this->body;
	}

	/**
	 * Set body of the response
	 * @param $body
	 */
	public function setBody($body)
	{
		if (!is_scalar($body))
		{
			$this->body = $this->serialize($body);
			$this->setHeader(self::header_Serialized, $this->serialize_method);
		}
		else $this->body = $body;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}

	public function serialize($content)
	{
		switch ($this->serialize_method)
		{
			case self::serialize_JSON:
				return json_encode($content);
			default:
				return serialize($content);
		}
	}

	public function unserialize($content)
	{
		switch ($this->serialize_method)
		{
			case self::serialize_JSON:
				return json_decode($content, true);
			default:
				return unserialize($content);
		}
	}

	/**
	 * Send headers and body to output
	 */
	public function Send()
	{
		$headers = $this->getHeaders();
		if (!empty($headers))
		{
			foreach ($headers as $header_key => $header_value)
			{
				header($header_key.': '.$header_value, true, $this->status_code);
			}
		}
		print $this->body;
	}
}
