# <?exit();?>
# 08cms InstallPack BasicData Dump
# Version: 08cms House 7.1
# Date: 2016-09-07
# --------------------------------------------------------
# Home: www.08cms.com
# --------------------------------------------------------


INSERT INTO #__sitemaps (ename,cname,xml_url,available,vieworder,issystem,tpl,ttl) VALUES ('baidu','Baidu新闻协议','baidu.xml','1','2','1','baidu.htm','1'),
('google','Google Sitemap','google.xml','1','1','1','google.htm','1'),
('google_col','Goolge_栏目','google_col.xml','1','3','0','google_col.htm','1'),
('google_arc','Goolge_文档','google_arc.xml','1','4','0','google_arc.htm','0'),
('baidu_mobile','百度移动Sitemap','baidu_mobile.xml','1','5','0','baidu_mobile.htm','12'),
('baidu_mob_push','百度主动推送','baidu_mob_push.xml','1','6','0','baidu_mob_push.htm','0');
