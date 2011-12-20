<?php
namespace Jamm\HTTP;

class SerializerJSON implements ISerializer
{
	public function serialize($data)
	{
		return json_encode($data);
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
