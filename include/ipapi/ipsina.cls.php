<?php
// 获取ip地址-sina
class ipsina{
	
	public $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip='; 
	public $cset = 'gb2312';
	
	// 获取数据
    //function getAddr($ip){}
	
	// 过滤处理
	function fill($addr){
		//1	14.216.0.0		14.222.255.255	中国	广东	东莞		电信
		//1	152.72.131.0	152.72.245.255	美国	威斯康星州	Racine
		$addr = preg_replace("/\d{1,3}([.][0-9]{1,3}){3,15}/",'',$addr);
		$addr = str_replace(array("1\t","\t\t"),"",$addr);
		return $addr;
	}
}

