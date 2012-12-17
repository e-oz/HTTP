<?php
namespace Jamm\HTTP;
interface ISerializer
{
	/**
	 * @param mixed $data
	 * @return string
	 */
	public function serialize($data);

	public function unserialize($data);

	/**
	 * @return string
	 */
	public function getMethodName();

	public function getContentType();
}
