<?php
namespace Jamm\HTTP;

class Connection
{
	/** @var resource */
	private $resource;
	private $type;
	private $host;
	private $port;

	/**
	 * @param resource $resource
	 * @param string $host
	 * @param string $port
	 * @param string $type
	 */
	public function __construct($resource = null, $host = '', $port = '', $type = 'keep-alive')
	{
		$this->resource = $resource;
		$this->host     = $host;
		$this->port     = $port;
		$this->type     = $type;
	}

	public function isKeepAlive()
	{
		return (strtolower($this->type)=='keep-alive');
	}

	public function getResource()
	{
		return $this->resource;
	}

	public function setResource($resource)
	{
		$this->resource = $resource;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}

	public function __destruct()
	{
		if (!$this->isKeepAlive())
		{
			$this->close();
		}
	}

	public function close()
	{
		if (!empty($this->resource))
		{
			fclose($this->resource);
		}
		$this->resource = NULL;
	}
}