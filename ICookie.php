<?php
namespace Jamm\HTTP;
interface ICookie
{
	public function setDomain($domain);

	public function getDomain();

	public function setExpire($expire = 0);

	public function getExpire();

	public function setHttpOnly($http_only = false);

	public function getHttpOnly();

	public function setName($name);

	public function getName();

	public function setPath($path);

	public function getPath();

	public function setSecure($secure = false);

	public function getSecure();

	public function setValue($value);

	public function getValue();

	public function getHeader();
}
