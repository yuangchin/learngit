<?php
// 获取ip地址-pcoln
class ippcoln{
	
	public $url = 'http://whois.pconline.com.cn/jsFunction.jsp?callback=jsShow&ip='; 
	public $cset = 'gb2312';
	
	// 获取数据
    //function getAddr($ip, $text=1){}
	
	// 过滤处理
	function fill($addr){
		//江苏省盐城市 联通',' 
		//if (window.jsShow){jsShow('江苏省盐城市 联通','');} 
		$arrText = array("jsShow('","');}");
		$addr = cls_ipAddr::getVal($addr, $arrText);
		$addr = str_replace("','",',',$addr);
		return $addr;
	}
}

