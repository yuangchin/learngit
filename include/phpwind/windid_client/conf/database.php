<?php
/**
 * 1.��ΪWindID�ͻ������������ӵķ����WindID���ݿ�
 * 2.��Ϊ����˻����ϵͳ��Ҳ������WindID���ݿ���phpwind�ֿ����
 */

/*���WindID���ݿ���phpwind���ݿ������ͬ���ã���ע�ʹ���*/
if(empty($mconfigs)) $mconfigs = cls_cache::Read('mconfigs');
return array(
    //���ݿ��ַ|����|�˿�
	'dsn' => "mysql:host={$mconfigs['pptin_dbhost']};dbname={$mconfigs['pptin_dbname']};port={$mconfigs['pptin_port']}",  
    //���ݿ��û���
	'user' => $mconfigs['pptin_dbuser'],	
    //���ݿ�����									 
	'pwd' => $mconfigs['pptin_dbpwd'],	
    //���ݿ���뷽ʽ										 
	'charset' => ($mconfigs['pptout_charset'] == 'utf-8' ? 'utf8' : $mconfigs['pptout_charset']),
    //��ǰ׺
	'tableprefix' => $mconfigs['pptin_dbpre']									 
);

?>