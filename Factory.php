<?php
namespace Jamm\HTTP;
trait Factory
{
	protected function getNewResponse()
	{
		return new Response();
	}

	protected function getNewRequest()
	{
		return new Request();
	}

	protected function getNewSerializer()
	{
		return new SerializerJSON();
	}
}
