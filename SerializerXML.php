<?php
namespace Jamm\HTTP;

class SerializerXML implements ISerializer
{
	private $xml_root_tag = 'response';
	private $array_item_name = 'item';
	private $array_item_attribute = 'ID';
	private $content_type = 'text/xml;charset=utf-8';

	public function serialize($data)
	{
		if (!is_array($data)) $data = array($data);
		$XML_Element = new \SimpleXMLElement("<?xml version=\"1.0\"?><".$this->xml_root_tag."></".$this->xml_root_tag.">");
		$this->array_to_xml($data, $XML_Element);
		return $XML_Element->asXML();
	}

	protected function array_to_xml($data_array, \SimpleXMLElement $XML)
	{
		$consecutive_counter = 0;
		foreach ($data_array as $key => $value)
		{
			if (is_array($value))
			{
				if (!is_numeric($key))
				{
					$subnode = $XML->addChild($key);
					$this->array_to_xml($value, $subnode);
				}
				else
				{
					$subnode = $XML->addChild($XML->getName().'_'.$this->array_item_name);
					if ($key!=$consecutive_counter)
					{
						$subnode->addAttribute($this->array_item_attribute, $key);
					}
					$this->array_to_xml($value, $subnode);
				}
			}
			else
			{
				if (!is_numeric($key))
				{
					$XML->addAttribute($key, $value);
				}
				else
				{
					$subnode = $XML->addChild($this->array_item_name, str_replace('&', '&amp;', $value));
					if ($key!=$consecutive_counter)
					{
						$subnode->addAttribute($this->array_item_attribute, $key);
					}
				}
			}
			if (is_numeric($key))
			{
				$consecutive_counter++;
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
		$this->fix_json_xml_array($result, $this->xml_root_tag);
		return $result;
	}

	private function fix_json_xml_array(&$xmlarr, $parent_name = '', $pre_parent_name = '')
	{
		foreach ($xmlarr as $key=> &$value)
		{
			if (is_array($value))
			{
				if ($key==='@attributes')
				{
					unset($xmlarr[$key]);
					$xmlarr = array_merge($xmlarr, $value);
				}
				elseif ($key===$parent_name)
				{
					unset($xmlarr[$key]);
					$xmlarr = array_merge($xmlarr, $value);
				}
				elseif ($key===$parent_name.'_'.$this->array_item_name)
				{
					unset($xmlarr[$key]);
					$xmlarr = array_merge($xmlarr, $value);
				}
				elseif ($key===$pre_parent_name.'_'.$this->array_item_name)
				{
					unset($xmlarr[$key]);
					$xmlarr = array_merge($xmlarr, $value);
				}
				elseif ($key===$this->array_item_name)
				{
					unset($xmlarr[$key]);
					$xmlarr = array_merge($xmlarr, $value);
				}
				else
				{
					$this->fix_json_xml_array($value, $key, $parent_name);
				}
			}
			else
			{
				if ($key===$parent_name)
				{
					unset($xmlarr[$key]);
					$index          = $this->getNextFreeIndexOfArray($xmlarr);
					$xmlarr[$index] = $value;
					array_multisort($xmlarr);
				}
			}
		}
	}

	private function getNextFreeIndexOfArray($array)
	{
		foreach (array_keys($array) as $key)
		{
			if (is_numeric($key))
			{
				if (!isset($array[$key+1]))
				{
					return $key+1;
				}
			}
		}
		return 0;
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

	public function getContentType()
	{
		return $this->content_type;
	}

	public function setContentType($content_type)
	{
		$this->content_type = $content_type;
	}
}
