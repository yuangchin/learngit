<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * ��̨Ĭ�ϲ˵�������Ϣ,�˵����ø�ʽ���£�
 * һ���˵������ø�ʽ�а���: �˵�����, ·����Ϣ, �˵�ͼ��, �˵�tip, ���ڵ�, ��һ���˵�
 * �˵�:  'key' => array('�˵�����', 'Ӧ��·��', 'icon' , ' tip' ,'���ڵ�key', '��һ���˵�key'),
 *
 * <note>
 * 1. ���û����д��һ���˵���Ĭ�Ϸ����ڽڵ����.
 * 2. ���û�и��ڵ��򲢷�����'��һ���˵�֮��'.
 * 3. ���'���ڵ�','��һ���˵�'��û����ɢ��ķ����������.
 * </note>
 *
 * �ڵ㶨��: 'Key' => array('�ڵ�����', �Ӳ˵�, 'icon', 'tip' ,'���ڵ�key'),
 */
return array(
	/*========Ϊ����ʾ������̨�����˵��������=========*/
//	'offen' => array('����', array()),
//	'offen' => array('����', '', '', '', ''),


	/**=====���ÿ�ʼ�ڴ�=====**/
	'custom' => array('����', array()),
	'admin' => array('��ʼ��', array()),
	'config' => array('ȫ��', array()),
	'u' => array('�û�', array()),
	'contents' => array('����', array()),
	//'bbs' => array('��̳', array()),
	//'design' => array('�Ż�', array()),
	//'mobile' => array('�ֻ�', array()),
	'appcenter' => array('Ӧ��', array()),
	//'platform' => array('��ƽ̨', array()),

	'custom_set' => array('���ò˵�', 'custom/*', '', '', 'custom'),
	'admin_founder' => array('��ʼ�˹���', 'founder/*', '', '', 'admin'),
	'admin_auth' => array('��̨Ȩ��', 'auth,role/*', '', '', 'admin'),
	'admin_safe' => array('��̨��ȫ', 'safe/*', '', '', 'admin'),
	
	//'windid_windid' => array('WindID����', 'windid/windid/*', '', '', 'admin'),
	'windid_client' => array('�ͻ��˹���', 'windid/client/*', '', '', 'admin'),
	'windid_notify' => array('֪ͨ����', 'windid/notify/*', '', '', 'admin'),

	'windid_site' => array('վ������', 'windid/site/*', '', '', 'config'),
	'windid_regist' => array('ע������', 'windid/regist/*', '', '', 'config'),
	'windid_storage' => array('ͷ��洢����', 'windid/storage/*', '', '', 'config'),
	'windid_credit' => array('��������', 'windid/credit/*', '', '', 'config'),
	'windid_area' => array('������', 'windid/areadata/*', '', '', 'config'),
	'windid_school' => array('ѧУ��', 'windid/schooldata/*', '', '', 'config'),

	'windid_user' => array('�û�����', 'windid/user/*', '', '', 'u'),
	'windid_messages' => array('˽�Ź���', 'windid/messages/*', '', '', 'contents'),
	
	'platform_index'   => array('Ӧ�ù���', 'appcenter,app/app,develop,manage/*', '', '', 'appcenter'),

	//���ҵ����ã���ͳһ��������ϵͳ�滮����
	'_extensions' => array(
		//'config' => array('resource' => 'APPS:config.conf.configmenu.php'),//ȫ��
		//'nav' => array('resource' => 'APPS:nav.conf.navmenu.php'),
		//'credit' => array('resource' => 'APPS:credit.conf.creditmenu.php'),
		//'seo' => array('resource' => 'APPS:seo.conf.seomenu.php'),
		//'rewrite' => array('resource' => 'APPS:rewrite.conf.rewritemenu.php'),
		//'u' => array('resource' => 'APPS:u.conf.umenu.php'),//�û�
		//'tag'	=> array('resource' => 'APPS:tag.conf.tagmenu.php'),//����
		//'message' => array('resource' => 'APPS:message.conf.messagemenu.php'),//��Ϣ
		//'report' => array('resource' => 'APPS:report.conf.reportmenu.php'),//�ٱ�
		//'bbs' => array('resource' => 'APPS:bbs.conf.bbsmenu.php'),//��̳
		//'other' => array('resource' => 'ADMIN:conf.testmenu.php'),//��ʱ���Ż����ֻ�������
	
		//'backup' => array('resource' => 'APPS:backup.conf.backupmenu.php'),//��ʱ���Ż����ֻ�������

		//'word' => array('resource' => 'APPS:word.conf.wordmenu.php'),

		//'link' => array('resource' => 'APPS:link.conf.linkmenu.php'),//��Ӫ
		//'punch' => array('resource' => 'APPS:u.conf.punchmenu.php'),
		//'appcenter' => array('resource' => 'APPCENTER:conf.appcentermenu.php'),//Ӧ��
		//'medal'	=> array('resource' => 'APPS:medal.conf.medalmenu.php'),
		//'task'	=> array('resource' => 'APPS:task.conf.taskmenu.php'),
		//'vote'	=> array('resource' => 'APPS:vote.conf.votemenu.php'),
		//'announce'	=> array('resource' => 'APPS:announce.conf.announcemenu.php'),
		//'emotion' => array('resource' => 'APPS:emotion.conf.emotionmenu.php'),
		//'cron' => array('resource' => 'APPS:cron.conf.cronmenu.php'),
	),
);
/**=====���ý����ڴ�=====**/
