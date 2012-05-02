<?php
namespace Jamm\HTTP;

interface IResponse
{
	public function getStatusCode();

	/** @param int $status_code     */
	public function setStatusCode($status_code);

	/**
	 * Set header for the response
	 * @param string $header
	 * @param string|number $value
	 */
	public function setHeader($header, $value);

	public function getHeader($header);

	public function removeHeader($header);

	/**
	 * Get body of the response
	 * @return string
	 */
	public function getBody();

	/**
	 * Set body of the response
	 * @param $body
	 */
	public function setBody($body);

	public function getHeaders();

	public function setHeaders(array $headers);

	/**
	 * Send headers and body to output
	 */
	public function Send();

	/**
	 * @param ISerializer|NULL $Serializer
	 */
	public function setSerializer($Serializer);

	/**
	 * @return ISerializer|NULL
	 */
	public function getSerializer();

	public function isStatusError();

	public function setStatusReason($status_reason);

	public function getStatusReason();

	public function __toString();

	public function getProtocolVersion();

	public function setProtocolVersion($protocol_version);

	public function setCookie(ICookie $Cookie);

	public function getCookie($name);
}
