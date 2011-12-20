<?php
namespace Jamm\HTTP;

class SerializerXML implements ISerializer
{
	private $xml_root_tag = 'response';
	private $array_item_name = 'item';
	private $array_item_attribute = 'ID';

	public function serialize($data)
	{
		if (!is_array($data)) $data = array($data);

		$XML_Element = new \SimpleXMLElement("<?xml version=\"1.0\"?><".$this->xml_root_tag."></".$this->xml_root_tag.">");

		$this->array_to_xml($data, $XML_Element);

		return $XML_Element->asXML();
	}

	protected function array_to_xml($data_array, \SimpleXMLElement $XML_Element)
	{
		foreach ($data_array as $key => $value)
		{
			if (is_array($value))
			{
				$this->array_to_xml($value, $XML_Element->addChild($key));
			}
			else
			{
				if (is_numeric($key))
				{
					$Child = $XML_Element->addChild($this->array_item_name, $value);
					$Child->addAttribute($this->array_item_attribute, $key);
				}
				else
				{
					$XML_Element->addChild($key, $value);
				}
			}
		}
	}

	public function unserialize($data)
	{
		$xml    = simplexml_load_string($data);
		$json   = json_encode($xml);
		$result = json_decode($json, TRUE);
		if (empty($result)) return false;
		if (key($result)==$this->xml_root_tag)
		{
			$result = $result[$this->xml_root_tag];
		}
		if (key($result)==$this->array_item_name) $result = $result[$this->array_item_name];
		return $result;
	}

	/**
	 * @return string
	 */
	public function getMethodName()
	{
		return 'XML';
	}

	public function getXmlRootTag()
	{
		return $this->xml_root_tag;
	}

	public function setXmlRootTag($xml_root_tag)
	{
		$this->xml_root_tag = $xml_root_tag;
	}
}
