# <?exit();?>
# 08cms InstallPack BasicData Dump
# Version: 08cms House 7.1
# Date: 2016-09-07
# --------------------------------------------------------
# Home: www.08cms.com
# --------------------------------------------------------


INSERT INTO #__mguides (mgid,ename,cname,posmsg,content,createdate,updatedate,mid,mname) VALUES ('2','mem_static','静态空间设置提示','会员中心管理-->个人资料-->静态空间','1. 当您的空间设置了静态目录，请尽快生成静态空间，静态空间访问速度更快，并有可能以静态目录为子域名来访问您的空间。<br>\r\n2. 当您维护更新了空间内容，只有更新空间后才能及时显示更新后的内容。如果静态空间有页面无法打开，请更新静态。','1358852300','1367898869','1','admin'),
('3','sms_insite','站内短信','会员中心管理-->短消息-->收件箱','站内短信不能发送到手机，仅供本系统内会员之间发送或接收消息。','1358852320','1367898869','1','admin'),
('7','cur_notes','积分兑换说明','会员中心管理-->财务管理-->现金积分互兑','会员可将现有的积分与现金之间互相兑换。','1359526914','1367898869','1','admin'),
('8','sms_mobile','手机短信提示','会员中心管理-->财务管理-->手机短信','','1359526988','1367898869','1','admin'),
('12','pay_notes','现金支付注释','会员中心管理-->财务管理-->现金支付','在线支付全自动到帐，其它支付需管理员操作给会员充值网站。','1359681462','1367898869','1','admin'),
('16','mchid12_note','品牌商发布提示','(商品,新闻)发布提示','您还未录入公司名称，<a href=\"?action=memberinfo\" style=\"color:red\">点击录入公司名称。</a>','1367834842','1367898869','1','admin'),
('17','mchid3_note','认证电话号码提示(经纪公司)','房源(出售,出租)发布(等)','您还未认证电话号码，<a href=\"?action=mcerts\" style=\"color:red\">点击认证电话号码。</a>','1367831915','1367898869','1','admin'),
('18','mchid11_note','装修公司发布提示','(案例,新闻)发布提示','您还未录入公司名称，<a href=\"?action=memberinfo\" style=\"color:red\">点击录入公司名称。</a>','1367834842','1367898869','1','admin'),
('19','mchidx_info','录入电话号码提示(公用)','招聘发布等','您还未录入电话号码，<a href=\"?action=memberinfo\" style=\"color:red\">点击录入电话号码。</a>','1370306733','0','1','admin'),
('20','mchidx_cert','认证电话号码提示(公用)','招聘发布等','您还未认证电话号码，<a href=\"?action=mcerts\" style=\"color:red\">点击认证电话号码。</a>','1370306781','1370310449','1','admin'),
('21','mem_certification','会员认证','会员中心->基本信息->会员认证','该提示信息在：后台>系统设置>>会员中心>>会员中心注释>>mem_certification  可修改','1404870721','1404870926','1','admin'),
('27','house_fxlink_note','推广说明','楼盘分销-推广链接-推广说明','(推广说明) 邀请好友注册会员，可以和好友一起赚佣金','1414632904','1426153732','1','admin'),
('28','house_fxlink_rule','邀请规则','楼盘分销-推广链接','1. (邀请规则) AAA。<br>\r\n2. BBB。','1414632946','1414633404','1','admin');
