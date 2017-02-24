<?php
//---------------------------------------------------------
//�����ص�xml����
//---------------------------------------------------------

class XmlParseUtil{
	
	function openapiXmlToMap($xml, $charset) {
		$hashMap = array();
		$stringDOM = new DOMDocument();
		try{
			@$stringDOM->loadXML($xml);
		}
		catch(Exception $e){
			print_r($e);
		}

		$root = $stringDOM->documentElement; //��ȡXML���ݵĸ�

		$children = $root->childNodes; //���$node�������ӽڵ�

		foreach($children as $e) //ѭ����ȡÿһ���ӽڵ�
		{
			if($e->nodeType == XML_ELEMENT_NODE) //����ӽڵ�Ϊ�ڵ��������ú�������
			{
				$value= iconv("UTF-8",$charset,$e->nodeValue); //ע��Ҫת��������ģ���ΪXMLĬ��ΪUTF-8��ʽ   
				$hashMap[$e->nodeName] = $value;
			
			}
		}
		return  $hashMap;
	}
	
	
}


?>