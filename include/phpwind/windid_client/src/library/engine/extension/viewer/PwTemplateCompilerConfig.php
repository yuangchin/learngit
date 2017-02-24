<?php
Wind::import('WIND:viewer.AbstractWindTemplateCompiler');
/**
 * config��ǩ������
 * 
 * ʹ�÷���Ϊ<code>{@C:namespace.name}������{@C:site.info.url}</code>
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTemplateCompilerConfig.php 18618 2012-09-24 09:31:00Z jieyin $
 * @package wekit
 * @subpackage engine.extension.viewer
 */
class PwTemplateCompilerConfig extends AbstractWindTemplateCompiler {

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		$content = substr($content, 4, -1);
		if (!$content) return '';
		list($namespace, $name) = explode('.', $content . '.', 2);
		return '<?php echo Wekit::C("' . $namespace . '", "' . trim($name, '.') . '"); ?>';
	}

}

?>