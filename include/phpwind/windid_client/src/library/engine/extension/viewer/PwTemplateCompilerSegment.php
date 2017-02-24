<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * ��������ģ��Ƭ�α�ǩ����
 * <code>
 * <!--#foreach ($templateList as $key => $tmp) {#-->
 * 		<segment tpl='$tmp' args='array()' alias='batchForeach'/>
 * <!--#}#-->
 * </code>
 * 
 * ���Ͻ���batchtemp���ص�ģ�嶼���뵽batchForeach�����ļ��б��档
 * 
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTemplateCompilerSegment.php 7486 2012-04-06 09:30:48Z xiaoxia.xuxx $
 * @package wekit
 * @subpackage engine.extension.viewer
 */
class PwTemplateCompilerSegment extends AbstractWindTemplateCompiler {
	protected $alias = ''; //����������ı����ļ���
	protected $tpl = ''; //ģ���ļ�
	protected $args = ''; //���ݸ�ģ��Ĳ���
	protected $name = '';//���õ�ģ��Ƭ���еķ���
	
	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if (!$this->tpl) return $content;
		if (0 !== strpos($this->args, 'array')) {
			$this->args = 'array(' . $this->args . ')';
		}
		$this->args || $this->args = '""';
		$content = array();
		$content[] = '<?php';
		$content[] = 'PwHook::segment("' . $this->tpl . '", ' . $this->args . ', "' . $this->name . '", "' . $this->alias . '", $__viewer);';
		$content[] = '?>';
		return implode(" ", $content);
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('tpl', 'alias', 'args', 'name');
	}
}