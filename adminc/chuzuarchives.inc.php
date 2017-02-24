<?PHP
$chid = 2;
$ispid4 = empty($ispid4) ? 0 : 1; // ispid4相关判断为：经纪公司查看属下经纪人房源相关代码
$mname = empty($ispid4) ? 0 : intval(@$mname);
$valid = isset($valid) ? intval($valid) : '-1';

$type = 'chuzu';
$coids = array(1,2,3,9,12,14,19,44);
//判断是经纪人还是经纪公司，返回sql的条件语句：other_sql、whrstr、
$userInfo = isCompany($ispid4,$curuser);
$other_sql = $userInfo['otherSql'];
$wherestr = $userInfo['whereStr'];
$namearr = $userInfo['agentNameArr'];

# 清空CK插件ID与名称，如果升级该脚本时请继承下去
cleanCookies(array('fyid', 'lpmc'), true);

$oL = new cls_archives(array(
'chid' => $chid,//模型id，必填
'url' => "?action=$action&ispid4=$ispid4",//表单url，必填，不需要加入chid及pid
'pre' => 'a.',//默认的主表前缀
'where' => $wherestr,//sql中的初始化where，限定为自已的文档
//'from' => "",//sql中的FROM部分
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql中的FROM部分
'select' => "a.*,c.*",//sql中的SELECT部分
'cols' => 0,//默认为0，设为大于1则为多列文档模式，如图片列表(设定一个元素不需要索引行)
'coids' => $coids,//手动设置允许类系
//'fields' => array(),//允许传入改装过的字段缓存
));
//头部文件及缓存加载
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //重设/忽略 类系处理
if($curuser->info['mchid']==1) resetCoids($oA->coids, array(19)); //去掉某些类系

//搜索项目 ****************************
//s_additem($key,$cfg)
$oL->s_additem('keyword',array('fields' => array('a.subject' => '标题','a.mname' => '会员','a.aid' => '文档ID','a.lpmc'=>'小区名称'),));//keys留空则默认为array('a.subject' => '标题','a.mname' => '会员','a.aid' => '文档ID')
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('checked',array());
$oL->s_additem('yuyue',array());//预约筛选

$ord_opt = array(
	0 => array('-排序方式-','refreshdate DESC'),
	1 => array('置顶(优先)','a.ccid9 DESC'),
	2 => array('推荐(优先)','a.ccid19 DESC'),
	3 => array('价格(降)','a.zj DESC'),
	4 => array('价格(升)','a.zj ASC'),
	
	7 => array('添加时间(降)','a.aid DESC'),
	8 => array('添加时间(升)','a.aid ASC'),
	
	9 => array('点击(降)','a.clicks DESC'),
	10 => array('点击(升)','a.clicks ASC'),
);

$oL->s_additem('orderby', array('options'=>$ord_opt));
$oL->s_additem('shi',array('type'=>'field',));
$oL->s_additem('ting',array('type'=>'field',));
$oL->s_additem('wei',array('type'=>'field',));
$oL->s_additem('zxcd',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('fl',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('szlc',array('type'=>'field','pre'=>'c.'));
$oL->s_additem('mj',array());
$oL->s_additem('zj',array());
$oL->s_additem('valid');
foreach($oL->A['coids'] as $k){
    if($curuser->info['mchid']==1 && $k==19) continue;
    $oL->s_additem("ccid$k",array());
    if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('indays');
$oL->s_additem('outdays');

//当房龄选择不详时，去掉房龄的筛选条件
if(!empty($fl) && $fl == -1) unset($oL->oS->wheres['fl']);

//搜索sql及filter字串处理
$oL->s_deal_str(); 
$oL->filterstr .= "&mname=$mname";

//搜索区域下方显示信息的数据
$usedhouseLimitInfo = userCenterDisplayMes($curuser,$chid);


//批量操作项目 ********************
$oL->o_additem('delete');//删除
$oL->o_additem("transfer",array('namearr'=>$namearr)); //房源转移
if(!$ispid4){
	if(in_array($valid,array(1))) $oL->o_additem('readd',array('limit'=>$usedhouseLimitInfo['otherData']['refreshRemainNum'],'time'=>0,'fieldname'=>'refreshes','title'=>'立即刷新'));//1440,刷新，time时间间隔为(分钟),
	if(in_array($valid,array(0))) $oL->o_additem('valid',array('days'=>0));//上架，days设置上架的天数，0则为无限期
	if(in_array($valid,array(1,-1))) $oL->o_additem('unvalid');//下架
	$curuser->info['mchid'] == 2 && $oL->o_additem("ccid19",array('title'=>'店铺推荐')); 
}

if(!submitcheck('bsubmit')){
	if(!$ispid4){
	    //出租管理页面头部滑动栏目
        slidingColumn($type,$valid);
		$oL->guide_bm($usedhouseLimitInfo['message'],'fix');
	}
	
	//显示列表区头部 ***************

	//搜索区域 ******************
	
	$oL->s_header(); 
	$oL->s_view_array(array('keyword',));//固定显示项
	if($ispid4){
		echo "<select style=\"vertical-align: middle;\" name=\"mname\">".makeoption($namearr,$mname)."</select> ";
	}
	$oL->s_view_array(array('checked','ccid9','ccid19','yuyue'));//固定显示项
	$oL->s_adv_point();//设置隐藏区
	$oL->s_view_array(array('shi','ting','wei','zxcd','fl','szlc','mj','zj'));
	echo "<br/>";
	$oL->s_view_array();
    
	//含导出excel
	$oL->s_footer_ex("?action=export_excel_items&chid=$chid&filename=chuzu".(empty($ispid4)?'':"&ispid4=".$ispid4),array('sql'=>$other_sql));
	
	if(empty($fcdisabled2)) RelCcjs($chid,1,2,1);
	if(empty($fcdisabled3)) RelCcjs($chid,3,14,2);
	
    //房东信息显示触发JS:$oL->landlordClickJs($curuser)
    $oL->m_header($oL->landlordClickJs($curuser).((empty($mname) ? '' : $namearr[$mname]." - "))."出租房源管理");
    //$oL->m_header((empty($mname) ? '' : $namearr[$mname]." - ")."出售房源管理");
	
	//设置列表项目，如果列表项中包含可设置项，需要在数据储存时，加入设置项的处理
	//分组，在先出现的列配置中加入：'group' =>'item,内容分隔符,索引分隔符',内容分隔符留空直接连接,索引行标题的分隔符留空则只使用第一个标记
	
	$oL->m_additem('selectid',array('view'=>''));
	
	$oL->m_additem("ccid1",array('side'=>'L'));
	$oL->m_additem('csubject',array('len' => 40,'view'=>'L'));
	$oL->m_addgroup('[{ccid1}]{csubject}','房源标题/小区名称<br>其它信息');//请注意分组不能嵌套，每项只能参与一次分组

        $oL->m_additem('clicks',array('title'=>'点击',));
        $oL->m_additem('cyuyue',array('url'=>"?action=yyshuaxin&aid={aid}&chid=3",'width'=>40,));
        $oL->m_addgroup('{clicks}<br>{cyuyue}','点击<br>预约');

        $oL->m_additem('enddate',array('type'=>'date','title'=>'到期时间',));
        $oL->m_additem("ccid44",array());
        $oL->m_addgroup('{enddate}<br>{ccid44}','到期时间<br>物业类型');

        $oL->m_additem("ccid9",array('url'=>'?action=zding&aid={aid}'));
        $oL->m_additem("ccid19",array());
        $oL->m_addgroup('{ccid9}<br>{ccid19}','置顶<br>推荐');


    $oL->m_additem('refreshdate',array('type'=>'date',));
	$oL->m_additem("zxcd",array('type'=>'field',));
	$oL->m_addgroup('{refreshdate}<br>{zxcd}','刷新日期<br>装修程度');

	$oL->m_additem("zj",array('mtitle'=>'{zj}元/月'));
	$oL->m_additem("fkfs",array('type'=>'field')); 

    $oL->m_addgroup('{zj}<br>{fkfs}','价格<br>付款');

    $oL->m_additem('atps',array('type'=>'ucount','title'=>'房源图片','url'=>"?action=xiangce_fy&pid={aid}",'func'=>'gethjnum','arid'=>'38','chid'=>121,'width'=>28,));
	$oL->m_additem('checked',array('mtitle'=>'审核','type'=>'bool',));
	$oL->m_additem('valid',array('mtitle'=>'上架',));
	$oL->m_addgroup('{checked}<br>{valid}','审核<br>上架');

	$oL->m_additem('info',array('type'=>'url','title'=>'更多','mtitle'=>'更多','url'=>"?action=archiveinfo&aid={aid}",'width'=>40,));
	$oL->m_additem('detail',array('type'=>'url','title'=>'编辑','mtitle'=>'详情','url'=>"?action=chuzuadd&aid={aid}&chid=$chid&ispid4=$ispid4",'width'=>40,));
	$oL->m_addgroup('{detail}<br>{info}','编辑<br>更多');
	
	//显示索引行，多行多列展示的话不需要
	$oL->m_view_top(); 
	
	//全部列表区处理，如果需要定制，尽量使用类中的细分方法
	$oL->m_view_main_fy(array('trclass'=>'bg bg2'), $curuser->info['mchid']); 
	
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
	$oL->sv_header(array(9,));
	
	//批量操作项的数据处理
	$oL->sv_o_all();
	
	//结束处理
	$oL->sv_footer();
}
?>