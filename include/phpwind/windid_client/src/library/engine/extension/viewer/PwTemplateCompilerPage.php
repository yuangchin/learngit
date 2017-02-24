<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * page��ǩ����
 * 
 * ְ�𣺱���ģ��page��ǩ
 * ֧�ֲ������ͣ�<code>
 * ģ������,��ǰҳ,������,ÿҳ��ʾ������,url
 * <page tpl='' current='' count='' per='' url='read.php?tid=$tid&page=' args='' />
 * </code>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTemplateCompilerPage.php 3580 2012-05-25 03:34:14Z yishuo $
 * @package viewer
 * @subpackage compiler
 */
class PwTemplateCompilerPage extends AbstractWindTemplateCompiler {
	/**
	 * ��ҳģ��
	 *
	 * @var string
	 */
	protected $tpl = '';
	/**
	 * ��ҳ��תurl
	 *
	 * @var string
	 */
	protected $url = '';
	/**
	 * �ַ�������,�ܹ��ж���ҳ
	 *
	 * @var string
	 */
	protected $total = '0';
	/**
	 * �ַ�������,��ǰpage
	 *
	 * @var string
	 */
	protected $page = '1';
	/**
	 * �ַ�������,������
	 *
	 * @var string
	 */
	protected $count = '0';
	/**
	 * �ַ�������,ÿҳ��ʾ������
	 *
	 * @var string
	 */
	protected $per = '0';
	
	/**
	 * 
	 * URL����,http query
	 *
	 * @var array
	 * @deprecated
	 */
	protected $args = 'array()';
	
	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		empty($this->total) && $this->total = '0';
		empty($this->page) && $this->page = '1';
		empty($this->count) && $this->count = '0';
		empty($this->per) && $this->per = '0';
		
		$_return = array();
		$_return[] = '<?php $__tplPageCount=(int)' . $this->count . ';';
		$_return[] = '$__tplPagePer=(int)' . $this->per . ';';
		$_return[] = '$__tplPageTotal=(int)' . $this->total . ';';
		$_return[] = '$__tplPageCurrent=(int)' . $this->page . ';';
		$_return[] = 'if($__tplPageCount > 0 && $__tplPagePer > 0){';
		
		$_return[] = '$__tmp = ceil($__tplPageCount / $__tplPagePer);';
		$_return[] = '($__tplPageTotal !== 0 &&  $__tplPageTotal < $__tmp) || $__tplPageTotal = $__tmp;}';
		$_return[] = '$__tplPageCurrent > $__tplPageTotal && $__tplPageCurrent = $__tplPageTotal;';
		$_return[] = 'if ($__tplPageTotal > 1) {?>';
		$_return[] = $this->getTplContent();
		$_return[] = '<?php } ?>';
		return implode("\r\n", $_return);
	}

	/**
	 * ���pageҳģ������
	 *
	 * @return string|mixed
	 */
	private function getTplContent() {
		if (!$this->tpl) return '';
		list($pageFile) = $this->windViewerResolver->getWindView()->getViewTemplate($this->tpl);
		if (!is_file($pageFile)) {
			throw new WindViewException($pageFile, WindViewException::VIEW_NOT_EXIST);
		}
		$content = WindFile::read($pageFile);
		strpos($this->url, '?') !== false || $this->url .= '?';
		$url = '{@url:' . $this->url . '&page=$_page_i}{@' . $this->args . ' ? \'&\' . http_build_query(' . $this->args . ') : \'\'';
		$content = str_ireplace(array('{@$url', '{$url'), $url, $content);
		$_windTemplate = Wind::getComponent('template');
		$content = $_windTemplate->compileStream($content, $this->windViewerResolver);
		$arrPageTags = array('$total', '$page', '$count');
		$arrPageVars = array('$__tplPageTotal', '$__tplPageCurrent', '$__tplPageCount');
		return str_ireplace($arrPageTags, $arrPageVars, $content);
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('tpl', 'total', 'page', 'per', 'count', 'url', 'args');
	}
}
?>