<?php
namespace Jamm\HTTP;

class SerializerJSON implements ISerializer
{
	public function serialize($data)
	{
		if (strpos(PHP_VERSION, '5.4')!==false)
		{
			return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		else
		{
			return json_encode($data);
		}
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
}
