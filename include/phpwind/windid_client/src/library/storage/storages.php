<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

return array(
	'local' => array(
		'name' => '���ش洢', 
		'alias' => 'local', 
		'managelink' => '',
		'description' => '���ش洢��������ͼƬ�Ƚ��洢�ڱ��ش����ϣ��洢λ���� conf/directory.php �ж��塣Ĭ�϶���λ��Ϊ /www/attachment, ȫ�ֿɷ��ʸ���·������Ϊ ATTACH', 
		'components' => array('path' => 'LIB:storage.PwStorageLocal')
	), 
	'ftp' => array(
		'name' => 'FTP Զ�̸����洢', 
		'alias' => 'ftp', 
		'managelink' => 'storage/ftp',
		'description' => 'FTP Զ�̸����洢', 
		'components' => array('path' => 'LIB:storage.PwStorageFtp')
	)
);