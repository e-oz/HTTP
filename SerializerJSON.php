<?php
namespace Jamm\HTTP;

class SerializerJSON implements ISerializer
{
	private $content_type = 'application/json;charset=utf-8';
	private $jsonp_callback_name;

	public function serialize($data)
	{
		if (strpos(PHP_VERSION, '5.4')!==false)
		{
			$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		else
		{
			$data = json_encode($data);
		}
		if (!empty($this->jsonp_callback_name))
		{
			$data = $this->jsonp_callback_name.'('.$data.')';
		}
		return $data;
	}

	public function unserialize($data)
	{
		return json_decode($data, true);
	}

	/**
	 * @return string
	 */
	public function getMethodName()
	{
		return 'JSON';
	}

	public function getContentType()
	{
		return $this->content_type;
	}

	public function setContentType($content_type)
	{
		$this->content_type = $content_type;
	}

	public function getJSONPCallbackName()
	{
		return $this->jsonp_callback_name;
	}

	public function setJSONPCallbackName($jsonp_callback_name)
	{
		$this->jsonp_callback_name = $jsonp_callback_name;
	}
}
