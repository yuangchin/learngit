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
		'avatarmanagelink' => '',
		'description' => '���ش洢��������ͼƬ�Ƚ��洢�ڱ��ش����ϡ�Ĭ�϶���λ��Ϊ attachment', 
		'components' => array('path' => 'WINDID:library.storage.WindidStorageLocal')
	), 
	'ftp' => array(
		'name' => 'FTP Զ�̸����洢', 
		'alias' => 'ftp', 
		'managelink' => 'windid/storage/ftp/',
		'avatarmanagelink' => 'windid/storage/ftp/',
		
		'description' => 'FTP Զ�̸����洢', 
		'components' => array('path' => 'WINDID:library.storage.WindidStorageFtp')
	)
);