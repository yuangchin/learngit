


=== �ӿ�ʹ�� ���� ================================================================================

��ϵͳ�����ṩ���Žӿڣ���������ṩ�̣����κ���ҵ������
��ӿڸı䣬��ϵͳ��������˾�ƻ����������ӿڻ�����ش���
��ӿ��иı䵼�µ��������ʧ����ϵͳ�����κ����Σ�
������������ṩ�̣���ϵ��������Žӿ�����ʺţ�

=== ���Žӿ� ����/���� ===========================================================================

- ������ڣ���������->���Žӿڹ���->�ӿ�����->
- ������ڣ���������->���Žӿڹ���->
      ���ܣ����ͼ�¼/��ֵ�����/���ŷ���/�ӿ�����

=== ���Žӿ� ����/ʹ�� ===========================================================================

$sms = new cls_sms();

- ���ͺ�����$sms->sendSMS($mobiles,$content,$type='scom')
    $sms->sendSMS($mobiles,$content); //Ĭ��,��ͨ��Ա����,����Ա���;
	$sms->sendSMS($mobiles,$content,'sadm'); //����Ա��̨����,(��������);
	$sms->sendSMS($mobiles,$content,'ctel'); //�ֻ���֤(������½,��������Ȩ��,ÿ��һ������,����70������);
	$sms->sendSMS($mobiles,$content,'1234'); //1234=��Աid(����),��$mid���û����Ͳ������,(!!!)���÷��͵ĵط�����ƺ�Ȩ��,����,�����$mid�����
- ����˵����
    mobiles: �ֻ�����,array/string(Ӣ�Ķ��ŷֿ�)
    content: 255���ַ�����
    type: ���ͷ�ʽ 
- ���أ�array($flag,$msg);
    $flag: ���, 1Ϊ�ɹ���
	$msg: ��ʾ��Ϣ

=== �ļ�Ŀ¼�ͽӿ� ˵�� ============================================================================

- Ŀ¼�ṹ ---------------------

sms/api_0test.php  [���̲���] �ӿ�
sms/api_dxqun.php  [����Ⱥ]   �ӿ�
sms/api_emay.php   [������ͨ] �ӿ�(webservice����)
sms/api_emhttp.php [������ͨ] �ӿ�(http����)
sms/api_eshang8.php[E������]  �ӿ�
sms/api_winic.php  [�ƶ�����] �ӿ� 
sms/api_***.php    ������չ�� �ӿ�
sms/basic_cfg.php  �����ļ�
sms/cer_code.js    ajax���÷���֤���js
sms/extra_act.php  ĳЩ�ӿ� ����չ����
sms/readme.txt     ˵���ļ�
/libs/classes/api/sms.cls.php �ֻ����Žӿ� ������

- �ӿ�api˵�� -------------------

 ------ sms_0test.php  [���̲���] 
���Խӿ�,���ڲ���ϵͳ��������,
����������ᷢ����,��дһ���ļ���¼��ʾ������ 

 ------ sms_emay.php   [������ͨ] �����г����� 
���÷�ʽ (Web Services + soap) 
http://www.emay.cn/

 ------ sms_emhttp.php   [������ͨ] �����г����� 
���÷�ʽ (Http + Post) 
http://www.emay.cn/

 ------ sms_winic.php  [�ƶ�����] �����ܲ� [php168(qibosoft)v7ʹ��] [08cms-���нӿ�]
���÷�ʽ (Http + Post) 
http://www.winic.org/index.asp

 ------ sms_dxqun.php ����Ⱥ - �㽭������ [php168(qibosoft)v7ʹ��]
���÷�ʽ (Http + Post) 
http://www.dxqun.com/

 ------ sms_eshang8.php E������ - ɽ��ʡ������ [08cms-���нӿ�]
���÷�ʽ (Http + Get) --- ����xml
http://www.eshang8.cn
http://sms.eshang8.cn/

 ------ sms_bucp.php ���ǿƼ� - ���� [δʵ��]
http://www.bucp.net/

 ------ ��������
www.41186.com

 ------ ���ݹ���
sms.gysoft.cn

 ------ ��ʱ��
www.xhsms.com

=== ��չ�ӿ�/�����淶 ============================================================================

1. ѡ�ýӿڼ����÷�ʽ������ϵͳҪ�ʺ�win/linuxƽ̨������Ҫѡ֧�ֿ�ƽ̨���÷�ʽ(��http��Web Services)�Ľӿڣ�
   [2013-08-13]��Ϊ���Է��㣬���˿���ʱ�������Ƽ�http+Post����Ϊ��ѡ��
2. �������ӿ�api˵�����У�ѡȡ�˼��ֵ��͵ĵ��÷�ʽ�����ӵĽӿڿ��Բο���
3. ���ڣ�������һ���ӿڣ���basic_cfg.php������һ�����ã������õ������Ϊ��myapi����
4. ����һ����sms_myapi�������ļ�sms/api_myapi.php�У�
   ʵ�ַ�����sendSMS($mobiles,$content)�������getBalance()������
5. ͳһ����ֵ��array($flag,$msg)��ʽ; $flagΪ���, 1Ϊ�ɹ���-1Ϊʧ�ܣ�$msg: Ϊ��ʾ��Ϣ�������$msgΪ������

=== ���ݿ�-�ṹ��� =====================================================================================

ALTER TABLE  `cms_members` ADD  `sms_charge` mediumint(8) DEFAULT '0' COMMENT  '�������(��)'

CREATE TABLE cms_sms_recharge (
  cid mediumint(8) NOT NULL auto_increment COMMENT '�Զ�ID',
  mid mediumint(8) NOT NULL default '0' COMMENT '��ԱID',
  mname varchar(15) character set gb2312 NOT NULL COMMENT '��Ա��',
  stamp int(10) NOT NULL default '0' COMMENT 'ʱ��',
  ip varchar(48) default NULL COMMENT 'ip',
  cnt int(11) NOT NULL default '0',
  msg varchar(255) default NULL COMMENT '��Ϣ',
  note varchar(255) default NULL,
  PRIMARY KEY  (cid)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk COMMENT='���ų�ֵ��¼';

CREATE TABLE `cms_sms_sendlogs` (
  `cid` mediumint(8) NOT NULL auto_increment COMMENT '�Զ�ID',
  `mid` mediumint(8) NOT NULL default '0' COMMENT '��ԱID',
  `mname` varchar(15) character set gb2312 NOT NULL COMMENT '��Ա��',
  `stamp` int(10) NOT NULL default '0' COMMENT 'ʱ��',
  `ip` varchar(48) default NULL COMMENT 'ip',
  `tel` varchar(255) default NULL COMMENT '����',
  `msg` varchar(255) default NULL COMMENT '��Ϣ',
  `res` varchar(255) default NULL COMMENT '���',
  `api` varchar(24) default NULL COMMENT '�ӿ�/���ͷ�ʽ',
  `cnt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk COMMENT='���ŷ��ͼ�¼';

=== End End  =====================================================================================

