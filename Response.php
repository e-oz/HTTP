<?php
namespace Jamm\HTTP;

class Response implements IResponse
{
	protected $status_code;
	protected $body;
	protected $headers;
	/** @var ISerializer */
	protected $Serializer;
	/** @var ISerializer[] */
	protected $unserializers;
	protected $serialization_header = 'Serialize';

	public function __construct($body = '', $status_code = 200)
	{
		$this->body        = $body;
		$this->status_code = $status_code;
		$this->setDefaultUnserializers();
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

	protected function setDefaultUnserializers()
	{
		if (class_exists(__NAMESPACE__.'\\SerializerJSON'))
		{
			$this->addUnserializer(new SerializerJSON());
		}
		if (class_exists(__NAMESPACE__.'\\SerializerPHP'))
		{
			$this->addUnserializer(new SerializerPHP());
		}
		if (class_exists(__NAMESPACE__.'\\SerializerXML'))
		{
			$this->addUnserializer(new SerializerXML());
		}
	}

	public function addUnserializer(ISerializer $Unserializer)
	{
		$this->unserializers[$Unserializer->getMethodName()] = $Unserializer;
	}

	/**
	 * Set header for the response
	 * @param string $header
	 * @param string|numeric $value
	 */
	public function setHeader($header, $value)
	{
		$this->headers[$header] = $value;
		if ($header==='Location' && ($this->status_code==200 || empty($this->status_code))) $this->setStatusCode(301);
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

		$serialization_method = $this->getHeader($this->serialization_header);
		if (!empty($serialization_method))
		{
			if (isset($this->unserializers[$serialization_method]))
			{
				return $this->unserializers[$serialization_method]->unserialize($this->body);
			}
			else
			{
				trigger_error('Unserializer not found for method '.$serialization_method, E_USER_WARNING);
				return $this->body;
			}
		}
		return $this->body;
	}

	/**
	 * Set body of the response
	 * @param $body
	 */
	public function setBody($body)
	{
		if (!is_scalar($body))
		{
			if (!empty($this->Serializer))
			{
				$this->body = $this->Serializer->serialize($body);
				$this->setHeader($this->serialization_header, $this->Serializer->getMethodName());
			}
			else
			{
				trigger_error('Not scalar value should be serialized, but serializer does not exists', E_USER_WARNING);
				$this->body = serialize($body);
			}
		}
		else
		{
			$this->body = $body;
		}
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

	public function getSerializationHeader()
	{
		return $this->serialization_header;
	}

	public function setSerializationHeader($serialization_header = 'Serialized')
	{
		$this->serialization_header = $serialization_header;
	}

	public function getSerializer()
	{
		return $this->Serializer;
	}

	public function setSerializer(ISerializer $Serializer)
	{
		$this->Serializer = $Serializer;
	}

	public function getUnserializers()
	{
		return $this->unserializers;
	}
}
