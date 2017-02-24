<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * ��������ļ�
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return array(
	'pwWidget' => array(
		'path' => 'LIB:engine.component.PwWidget',
		'scope' => 'singleton'
	),
	'pwComponent' => array(
		'path' => 'LIB:engine.component.PwComponent',
		'scope' => 'singleton',
		'config' => array('resource' => 'CONF:pwcomponents.php'),		
	),
	'security' => array(
		'path' => 'WIND:security.WindXxtea',
		'scope' => 'singleton',
	),
	'windLogger' => array(
		'constructor-args' => array('0' => array('value' => 'DATA:log'), '1' => array('value' => '2'), '2' => array('value' => 10000))
	),
	'router' => array(
		'config' => array(
  			'routes' => array(
			    'pw' => array(
					'class' => 'LIB:route.PwCommonRoute',
				    'default' => true,
				),
  			),
		)
	),
	'windView' => array(
		'config' => array('themePackPattern' => '{pack}.{theme}.template')
	),
	'template' => array(
		'config' => array('resource' => 'CONF:compiler.php'),
	),
	'i18n' => array(
		'config' => array('path' => 'SRC:i18n', 'suffix' => '.lang'),
	),
	'db' => array(
		'config' => array('resource' => 'CONF:windid.database.php')
	),
	'windToken' => array(
		'path' => 'LIB:engine.extension.token.PwCsrfToken',
		'scope' => 'singleton',
	),
	'windCookie' => array(
		'path' => 'WIND:http.cookie.WindNormalCookie',
		'scope' => 'singleton',
	),
	'windiddb' => array(
		'path' => 'WIND:db.WindConnection',
		'scope' => 'singleton',
		'config' => array('resource' => 'CONF:windid.database.php')
	),
	'httptransfer' => array(
		'path' => 'WIND:http.transfer.WindHttpSocket',
		'scope' => 'prototype'
	),
	'storage' => array(
		'path' => 'LIB:storage.PwStorageLocal',
		'scope' => 'singleton'
	),
	'localStorage' => array(
		'path' => 'LIB:storage.PwStorageLocal',
		'scope' => 'singleton'
	),
	'fileCache' => array(
		'path' => 'LIB:engine.extension.cache.PwFileCache',
// 		'path' => 'WIND:cache.strategy.WindFileCache',
		'scope' => 'application',
		'config' => array(
			'dir' => 'DATA:cache',	//�����ļ���ŵ�Ŀ¼,ע��ɶ���д
			'suffix' => 'txt',	//�����ļ��ĺ�׺,Ĭ��Ϊtxt��׺
			'dir-level' => '0',	//�����ļ����Ŀ¼����Ŀ¼����,Ĭ��Ϊ0������Ŀ¼
			'security-code' => '',	//�̳���AbstractWindCache,��ȫ������
			'key-prefix' => 'pw_',	 //�̳���AbstractWindCache,����keyǰ׺
			'expires' => '0',	//�̳���AbstractWindCache,�������ʱ������
		),
	),
);