<?PHP
$chid = empty($chid) ? 2 : max(0,intval($chid));
if(!in_array($chid,array(2,3))) $chid = 2;


#-----------------

$oL = new cls_archives(array(
'chid' => $chid,//模型id，必填
'url' => "?action=$action",//表单url，必填，不需要加入chid及pid
'pre' => 'a.',//默认的主表前缀
'where' => "a.mid='{$curuser->info['mid']}'",//sql中的初始化where，限定为自已的文档
'from' => "",//sql中的FROM部分
'select' => "",//sql中的SELECT部分
'cols' => 0,//默认为0，设为大于1则为多列文档模式，如图片列表(设定一个元素不需要索引行)
'coids' => array(19,),//手动设置允许类系
//'fields' => array(),//允许传入改装过的字段缓存
));
//头部文件及缓存加载
$oL->top_head();

//搜索项目 ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array(),));//keys留空则默认为array('a.subject' => '标题','a.mname' => '会员','a.aid' => '文档ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');

$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//搜索sql及filter字串处理
$oL->s_deal_str();

//批量操作项目 ********************


// 限额控制：
$style = " style='font-weight:bold;color:#F00'";
$exconfigs = cls_cache::cacRead('exconfigs',_08_EXTEND_SYSCACHE_PATH);
if(!($rules = @$exconfigs['yysx'])) cls_message::show('系统没有预约刷新规则。');
if(empty($curuser->info['grouptype14'])){
	$exconfigs = $exconfigs['fanyuan'][0];	
}else{
	$exconfigs = $exconfigs['fanyuan'][$curuser->info['grouptype14']];	
}
$msgstr = "【店铺推荐】信息能在会员空间首页显示。";
$oL->o_additem("ccid19",array('title'=>'店铺推荐')); 


if(!submitcheck('bsubmit')){
	
	//搜索区域 ******************
	backnav('tuijian',"tjchid$chid");
	$oL->guide_bm($msgstr,'fix');
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked',));//固定显示项
	//$oL->s_adv_point();//设置隐藏区
	$oL->s_view_array();
	$oL->s_footer();
	

	//显示列表区头部 ***************
	$oL->m_header();
	
	//设置列表项目，如果列表项中包含可设置项，需要在数据储存时，加入设置项的处理
	//分组，在先出现的列配置中加入：'group' =>'item,内容分隔符,索引分隔符',内容分隔符留空直接连接,索引行标题的分隔符留空则只使用第一个标记
	
	$oL->m_additem('selectid');
	$oL->m_additem('subject',array('len' => 40,));
	//$oL->m_additem('caid');
	$oL->m_additem('clicks',array('title'=>'点击',));
	foreach($oL->A['coids'] as $k){
		$oL->m_additem("ccid$k",array());
	}
	$oL->m_additem('valid');
	$oL->m_additem('refreshdate',array('type'=>'date',));	 
	$oL->m_additem('enddate',array('type'=>'date',));
	$oL->m_additem('info',array('type'=>'url','title'=>'更多','mtitle'=>'更多','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'编辑','mtitle'=>'详情','url'=>"?action=".($chid=='2' ? 'chuzu' : 'chushou')."add&aid={aid}",'width'=>40,));
	
	//$oL->m_addgroup('{shi}/{ting}','{shi}/{ting}');//请注意分组不能嵌套，每项只能参与一次分组
	//$oL->m_mcols_style("{selectid} &nbsp;{subject}<br>{shi}/{ting]/{chu}");//多列文档模式定义显示项目的组合样式,默认为："{selectid} &nbsp;{subject}"
	
	//显示索引行，多行多列展示的话不需要
	$oL->m_view_top();
	
	//全部列表区处理，如果需要定制，尽量使用类中的细分方法
	$oL->m_view_main();
	
	//显示列表区尾部
	$oL->m_footer();
	
	//显示批量操作区************
	$oL->o_header();
	
	//显示单选项
	//$oL->o_view_bools('单行标题',array('bool1','bool2',));
	$oL->o_view_bools();
	
	//显示整行项
	$oL->o_view_rows();
	
	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
}else{
	//预处理，未选择的提示
	$oL->sv_header();
	
	//批量操作项的数据处理
	$oL->sv_o_all();
	
	//结束处理
	$oL->sv_footer();
}
?>