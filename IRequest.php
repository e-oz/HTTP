<?php
namespace Jamm\HTTP;

interface IRequest
{
	public function BuildFromInput();

	/**
	 * Return header from array by key, or all keys
	 * @param string $key
	 * @return null|array|mixed
	 */
	public function getHeaders($key = null);

	public function setHeader($header, $value);

	/**
	 * Get type of request method
	 * @return string
	 */
	public function getMethod();

	/**
	 * Set the request method
	 * @param $method
	 * @return void
	 */
	public function setMethod($method);

	/**
	 * Return key or all the keys of request
	 * @param string $key
	 * @return null|array|string|numeric
	 */
	public function getData($key = null);

	/**
	 * Set array of data
	 * @param array $values
	 */
	public function setData(array $values);

	public function setDataKey($key, $value);

	/**
	 * Return HTTP_ACCEPT header
	 * @return string
	 */
	public function getAccept();

	public function SetAccept($accept);

	/**
	 * Check, if this type is acceptable
	 * @param string $type
	 * @return bool
	 */
	public function isAcceptable($type);

	/**
	 * Send request by URL. Pass $Response argument, if you need response
	 * @param $URL
	 * @param IResponse|null $Response
	 * @return bool|IResponse
	 */
	public function Send($URL, IResponse $Response = NULL);
}
