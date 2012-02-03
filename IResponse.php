<?php
namespace Jamm\HTTP;

interface IResponse
{
	public function getStatusCode();

	/** @param int $status_code	 */
	public function setStatusCode($status_code);

	/**
	 * Set header for the response
	 * @param string $header
	 * @param string|numeric $value
	 */
	public function setHeader($header, $value);

	public function getHeader($header);

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
	 * Get Result of response - unpack value of body and headers
	 * @return bool|mixed
	 */
	public function getResult();
	
	/**
	 * Send headers and body to output
	 */
	public function Send();
	
	public function addUnserializer(ISerializer $Unserializer);
	
	public function getSerializationHeader();
	
	public function setSerializationHeader($serialization_header = 'Serialize');
	
	public function getSerializer();
	
	public function setSerializer(ISerializer $Serializer);
	
	public function getUnserializers();
}
