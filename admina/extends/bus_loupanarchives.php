<?PHP
/* 参数初始化代码 */
//$chid = 4;//必须定义，不接受从url的传参
$caid = in_array($caid,array(612,616)) ? $caid : 612;
$chid = $caid==616 ? 116 : 115; //echo "$caid,$chid";
$caid_str = empty($caid)?'':"&caid=".max(1,intval($caid));
//$chid = empty($chid) ? 0 : max(0,intval($chid));//接受外部传chid，但要做好限制

$oL = new cls_archives(array(
'chid' => $chid,//模型id，必填
'url' => "?entry=$entry$extend_str$caid_str",//表单url，必填，不需要加入chid及pid
'pre' => 'a.',//默认的主表前缀
'from' => $tblprefix.atbl($chid)." a INNER JOIN {$tblprefix}archives_$chid c ON c.aid=a.aid ",//sql中的FROM部分
'select' => "",//sql中的SELECT部分
'where' => "(c.leixing='0' OR c.leixing='1')", //楼盘条件
'cols' => 0,//默认为0，设为大于1则为多列文档模式，如图片列表(设定一个元素不需要索引行)
'orderby' => "a.vieworder,a.aid DESC",
//'fields' => array(),//允许传入改装过的字段缓存
));
//头部文件及缓存加载
$oL->top_head();
$oL->resetCoids($oL->A['coids']); //根据 房产参数设置,控制类系

//搜索项目 ****************************
$oL->s_additem('keyword',array('fields' => array('a.subject' => '楼盘名称','a.keywords'=>'关键词','a.mname' => '会员账号','a.aid' => '文档ID')));
$oL->s_additem('checked');
foreach($oL->A['coids'] as $k){
	$oL->s_additem("ccid$k",array());
	if($k==3) $oL->s_additem("ccid14",array());
}
$oL->s_additem('caid',array('hidden' => 1,));
$oL->s_additem('orderby');
$oL->s_additem('indays');
$oL->s_additem('outdays');

//搜索sql及filter字串处理
$oL->s_deal_str();


//批量操作项目 ********************
$oL->o_additem('delete');
$oL->o_additem('check');
$oL->o_additem('uncheck');
$oL->o_additem('static');
$oL->o_additem('nstatic');
$oL->o_additem('readd');
$oL->o_additem("ccid18");
$oL->o_additem("ccid41");
$oL->o_additem("ccid1");
$oL->o_addpushs();//推送项目
$oL->o_additem("leixing");

if(!submitcheck('bsubmit')){
	
	//搜索区域 ******************
	$oL->s_header();
	//$oL->s_view_array(array('keyword','orderby','checked','ccid41'));//固定显示项
	//$oL->s_adv_point();//设置隐藏区
	$oL->s_view_array();
	$oL->s_footer();
	if(empty($fcdisabled2)) RelCcjs($chid,1,2,1);
	if(empty($fcdisabled3)) RelCcjs($chid,3,14,2);

	//显示列表区头部 ***************
	$oL->m_header();
	
	//设置列表项目
	//分组，在先出现的列配置中加入：'group' =>'item,内容分隔符,索引分隔符',内容分隔符留空直接连接,索引行标题的分隔符留空则只使用第一个标记
	
	$oL->m_additem('selectid');
    $oL->m_additem('aid',array('type'=>'other','title'=>'ID'));
    $oL->m_additem('subject',array('len' => 60,));
	foreach($oL->A['coids'] as $k){		
		//if(in_array($k,array(7,8))) $icon = 1;
		//else                        $icon = 0;
		if(in_array($k,array(1)))   $view = '';
		else                        $view = 'H';
		$oL->m_additem("ccid$k",array('view'=>$view));
	}	
	$oL->m_additem('checked',array('type'=>'bool','title'=>'审核','view'=>'H',));	
	$oL->m_additem('azxs',array('type'=>'ucount','title'=>'资讯','url'=>"?entry=extend&extend=zixuns_pid&pid={aid}",'func'=>'gethjnum','arid'=>'35','chid'=>1,'width'=>28,));
	$oL->m_additem('atps',array('type'=>'ucount','title'=>'相册','url'=>"?entry=extend&extend=xiangces_pid&pid={aid}",'func'=>'gethjnum','arid'=>'36','chid'=>7,'width'=>28,));
	$oL->m_additem('ayss',array('type'=>'ucount','title'=>'意向','url'=>"?entry=extend&extend=yixiangs&aid={aid}&chid=$chid",'func'=>'getjhnum','cuid'=>'3','chid'=>$chid,'width'=>28,));
	//$oL->m_additem('liuyan',array('type'=>'url','title'=>'留言','mtitle'=>'[{adps}]','url'=>"?entry=extend&extend=comments&aid={aid}&chid=$chid",'width'=>28,));
	$oL->m_additem('liuyan',array('type'=>'ucount','title'=>'留言','url'=>"?entry=extend&extend=comments&aid={aid}&chid=$chid",'func'=>'getjhnum','cuid'=>'1','chid'=>$chid,'width'=>28,));	
	$oL->m_additem('azbs',array('type'=>'ucount','title'=>'周边','url'=>"?entry=extend&extend=peitaos_pid&pid={aid}",'func'=>'gethjnum','arid'=>'35','chid'=>8,'width'=>28,));
    
    $archs = array(115=>array(117,119),116=>array(118,120));
    $oL->m_additem('aesfys',array('type'=>'ucount','title'=>'出售','url'=>"?entry=extend&extend=usedhouseheji&pid={aid}",'func'=>'gethjnum','arid'=>'36','chid'=>$archs[$chid][0],'width'=>28,));
    $oL->m_additem('aczfys',array('type'=>'ucount','title'=>'出租','url'=>"?entry=extend&extend=chuzuheji&pid={aid}",'func'=>'gethjnum','arid'=>'36','chid'=>$archs[$chid][1],'width'=>28,));
    
    #$oL->m_additem('weixin',array('type'=>'url','title'=>'微信','mtitle'=>'配置', 'url'=>"?entry=weixin_property&aid={aid}&cache_id=property",'width'=>28,));
	$oL->m_additem('weixin',array('type'=>'weixin','mcache'=>'property'));
	$oL->m_additem('adps',array('type'=>'url','title'=>'评分','mtitle'=>'查看','url'=>"?entry=extend&extend=lp_pingfen&aid={aid}&chid=$chid",'width'=>28,));
	$oL->m_additem('stpic',array('type'=>'url','title'=>'沙盘','mtitle'=>'沙盘','url'=>"?entry=extend&extend=sandtable&pid={aid}",'width'=>28,));
	$oL->m_additem('refreshdate',array('type'=>'date',));	
	$oL->m_additem('updatedate',array('type'=>'date','view'=>'H',));
	$oL->m_additem('clicks',array('title'=>'点击数','type'=>'input','width'=>50,'w' => 3,));
	$oL->m_additem('vieworder',array('type' => 'input','view'=>'','title'=>'排序','w' => 3,));
	
    $oL->m_additem('info',array('type'=>'url','title'=>'更多','mtitle'=>'更多','url'=>"?entry=extend&extend=archiveinfo&aid={aid}",'width'=>30,'view'=>'H',));
	$oL->m_additem('dj',array('type'=>'url','mtitle'=>'价格','url'=>"?entry=extend&extend=jiagearchive&aid={aid}&isnew=1",'width'=>60));
	$oL->m_additem('detail',array('type'=>'url','mtitle'=>'详情','url'=>"?entry=extend&extend=bus_loupanadd&aid={aid}&caid={caid}",'width'=>60));
	$oL->m_addgroup('{detail}&nbsp;{dj}','编辑');
	
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
	
	//显示推送位
	$oL->o_view_pushs();
	
	//显示整行项
	$oL->o_view_rows();
	//trbasic('<input type="checkbox" value="1" name="arcdeal[leixing]" class="checkbox">&nbsp;楼盘小区属性','','<select style="vertical-align: middle;" name="arcleixing">'.makeoption(array('0'=>'楼盘与小区','1'=>'楼盘','2'=>'小区')).'</select>','');

	$oL->o_footer('bsubmit');
	$oL->guide_bm('','0');
	
}else{
	//预处理，未选择的提示
	$oL->sv_header();
	
	//列表区中设置项的数据处理
	$oL->sv_e_additem('clicks',array());
    $oL->sv_e_additem('vieworder',array());
	$oL->sv_e_all();
	
	//批量操作项的数据处理
	$oL->sv_o_all();

	//结束处理
	$oL->sv_footer();
}
?>