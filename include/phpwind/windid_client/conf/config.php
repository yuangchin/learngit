<?php
if(empty($mconfigs)) $mconfigs = cls_cache::Read('mconfigs');
return array( 
	'connect' => $mconfigs['pptout_connect'],  //dbΪ��������  httpԶ������  ��Ϊdb����ͬʱ����database.php������ݿ�����
	'serverUrl' => $mconfigs['pptin_url'],  //����˷��ʵ�ַ. ��:http://www.phpwind.net
	'clientId' => $mconfigs['pptin_appid'],   //�ÿͻ�����WindID���id
	'clientKey' => $mconfigs['pptin_key'],  //ͨ����Կ���뱣����WindID���һ��
	'charset' => $mconfigs['pptout_charset'],	   //�ͻ���ʹ�õ��ַ�����
);
?>