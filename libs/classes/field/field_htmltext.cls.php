<?php
/* 
** 不同类型的字段的配置，使用方法汇总
** 对cls_fieldconfig的同名方法的扩展样例 ：public static function ex_demo()
*/
!defined('M_COM') && exit('No Permission');
class cls_field_htmltext extends cls_fieldconfig{
	
	# 表单之不同类型字段组合编辑区块
    public static function _fm_custom_region(){
		self::_fm_notnull();
		self::_fm_regular();
		self::_fm_guide();
		self::_fm_mode();
		self::_fm_min_max();
		self::_fm_editor_height();
		parent::_fm_autoCompression();
		self::_fm_rpid();
		self::_fm_filter();
		self::_fm_wmid();
		self::_fm_cfgs();
		
	}
	# 储存之不同类型字段的数据处理
    public static function _sv_custom_region(){
		self::$newField['mode'] = ( empty(self::$fmdata['mode']) ? 0 : (int) self::$fmdata['mode'] );
		foreach(array('min','max') as $key){
			self::$newField[$key] = max(0,intval(self::$fmdata[$key]));
			self::$newField[$key] = empty(self::$newField[$key]) ? '' : self::$newField[$key];
		}
	}
	# 表单之编辑器模式
    protected static function _fm_mode(){
		$Value = empty(self::$oldField['mode']) ? 0 : (int) self::$oldField['mode'];
		trbasic('编辑器显示模式','',makeradio('fmdata[mode]',array(0 => '常规编辑器',1 => '简易编辑器', 2 => '基本编辑器'),$Value),'');
	}
	# 表单之输入值字节长度限制
    protected static function _fm_min_max(){
		$ValueMin = empty(self::$oldField['min']) ? '' : self::$oldField['min'];
		$ValueMax = empty(self::$oldField['max']) ? '' : self::$oldField['max'];
		trrange('输入值字节长度限制', array('fmdata[min]',$ValueMin,'','&nbsp; -&nbsp; ',5, 'validate' => makesubmitstr('fmdata[min]',0,'int')),
								array('fmdata[max]',$ValueMax,'','',5, 'validate' => makesubmitstr('fmdata[max]',0,'int')));
	}
	# 表单之文本编辑器高度
    protected static function _fm_editor_height(){
		$Value = self::$isNew ? 500 : self::$oldField['editor_height'];
		trbasic('编辑器显示高度', 'fmdata[editor_height]', (int)$Value, 'text',array('guide'=>'单位：像素'));
        
		$auto_page_size = self::$isNew ? 5 : @self::$oldField['auto_page_size'];
		trbasic('默认自动分页大小', 'fmdata[auto_page_size]', (int)$auto_page_size, 'text',array('guide'=>'单位：KB'));
	}
	
	# 表单之提交前过滤
    protected static function _fm_filter(){
		$Value = self::$isNew ? 0 : self::$oldField['filter'];
    	trbasic('提交前过滤','fmdata[filter]',makeoption(array(0=>'不过滤', 1=>'保护性过滤HTML'),$Value),'select');
	}
	
	protected static function _fm_regular(){
	    $Value = self::$isNew ? 0 : self::$oldField['regular'];
		trbasic('不良关键字显示提示','fmdata[regular]',$Value,'radio',array('guide'=>'仅发布或修改时js显示提示，其它限制请自定义处理，或和标签调用一起使用。'));
	}
	
	
	
	
}
