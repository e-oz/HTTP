<?php
namespace Jamm\HTTP;
class SerializerJSON implements ISerializer
{
	private $content_type = 'application/json;charset=utf-8';
	private $jsonp_callback_name;
	private $json_prefix = '';

	public function serialize($data)
	{
		if (PHP_VERSION_ID >= 50400)
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
		elseif (!empty($this->json_prefix))
		{
			$data = $this->json_prefix.$data;
		}
		return $data;
	}

	public function unserialize($data)
	{
		$err_level = error_reporting(0);
		$result    = json_decode($data, true);
		error_reporting($err_level);
		return $result;
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

	public function setJSONPrefix($prefix = ")]}',\n")
	{
		$this->json_prefix = $prefix;
	}
}
