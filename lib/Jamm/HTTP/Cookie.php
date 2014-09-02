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
		$this->expire    = is_numeric($expire)?gmdate('D, d M Y H:i:s \G\M\T', $this->expire):$this->expire;
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
				.(empty($this->expire) ? '; expires='.$this->expire : '')
				.(isset($this->path) ? '; path='.$this->path : '')
				.(isset($this->domain) ? '; domain='.$this->domain : '')
				.(!empty($this->secure) ? '; secure' : '')
				.(!empty($this->http_only) ? '; httponly' : '');
	}

    /**
     * @param $cookieString String
     * @return bool|Cookie
     */
    public static function FromString($cookieString)
    {
        if(preg_match_all('/([^;,\s]*?)=([^;]*)/', $cookieString, $preg)){
            $params = array();
            $cookieName = $preg[1][0];
            $cookieValue = $preg[2][0];
            for($i = 1; $i != count($preg[0]); $i++)
                $params[$preg[1][$i]] = $preg[2][$i];

            return new static(
                $cookieName,
                $cookieValue,
                isset($params['expires'])?$params['expires']:0,
                isset($params['path'])?$params['path']:'/',
                isset($params['domain'])?$params['domain']:'',
                isset($params['secure'])?$params['secure']:false
            );
        }
        else
            return false;
    }
}
