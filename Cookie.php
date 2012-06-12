<?php
namespace Jamm\HTTP;

class Cookie implements ICookie
{
	private $name, $value;
	private $expire = 0;
	private $path, $domain;
	private $secure = false;
	private $http_only = true;

	public function __construct($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $http_only = true)
	{
		$this->name      = $name;
		$this->value     = $value;
		$this->expire    = $expire;
		$this->path      = $path;
		$this->domain    = $domain;
		$this->secure    = $secure;
		$this->http_only = $http_only;
	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
	}

	public function getDomain()
	{
		return $this->domain;
	}

	public function setExpire($expire = 0)
	{
		$this->expire = $expire;
	}

	public function getExpire()
	{
		return $this->expire;
	}

	public function setHttpOnly($http_only = true)
	{
		$this->http_only = $http_only;
	}

	public function getHttpOnly()
	{
		return $this->http_only;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setSecure($secure = false)
	{
		$this->secure = $secure;
	}

	public function getSecure()
	{
		return $this->secure;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getHeader()
	{
		return rawurlencode($this->name).'='.rawurlencode($this->value)
				.!empty($this->expire) ? '; expires='.gmdate('D, d M Y H:i:s \G\M\T', $this->expire) : ''
				.isset($this->path) ? '; path='.$this->path : ''
				.isset($this->domain) ? '; domain='.$this->domain : ''
				.!empty($this->secure) ? '; secure' : ''
				.!empty($this->http_only) ? '; httponly' : '';
	}
}
