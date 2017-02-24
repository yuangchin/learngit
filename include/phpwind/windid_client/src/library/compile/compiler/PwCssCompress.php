<?php
Wind::import("LIB:compile.AbstractPwCompiler");
Wind::import("WIND:utility.WindFolder");
/**
 * css������
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwCssCompress.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wekit.compile.compiler
 */
class PwCssCompress extends AbstractPwCompiler {
	
	private $manifest = 'Manifest.xml';
	private $cssDevDir = 'css/dev';
	private $cssDir = 'css/build';

	/* (non-PHPdoc)
	 * @see AbstractPwCompiler::doCompile()
	 */
	public function doCompile() {
		$config = Wekit::load('APPCENTER:service.srv.PwInstallApplication')->getConfig('style-type');
		foreach ($config as $k => $v) {
			$dir = Wind::getRealDir('THEMES:' . $v[1]);
			$files = WindFolder::read($dir, WindFolder::READ_DIR);
			foreach ($files as $v) {
				$manifest = $dir . '/' . $v . '/' . $this->manifest;
				if (!WindFile::isFile($manifest)) continue;
				if (($r = $this->_doCss($dir . '/' . $v)) instanceof PwError) return $r;
			}
		}
	}
	
	/**
	 * @param string $stylePackage
	 * @param booelan $isManifestChanged
	 * @return boolean
	 */
	private function _doCss($stylePackage) {
		$file = $stylePackage . '/' . $this->manifest;
		$dir = $stylePackage . '/' . $this->cssDevDir;
		$_dir = $stylePackage . '/' . $this->cssDir;
		WindFolder::mkRecur($_dir);
		$files = WindFolder::read($dir, WindFolder::READ_FILE);
		foreach ($files as $v) {
			if (WindFile::getSuffix($v) === 'css') {
				$dev_css = $dir . '/' . $v; //�������ļ�
				$css = $_dir . '/' . $v; //������ļ�
				$data = WindFile::read($dir . '/' . $v);
				$_data = $this->_compress($data);
				if (WindFile::write($css, $_data) === false) return new PwError('STYLE:style.css.write.fail');
			}
		}
		return true;
	}

	/**
	 * ѹ��
	 *
	 * @param string $data
	 * @return string
	 */
	private function _compress($data) {
		//ȥ��ע�ͣ��ո��
		$data = preg_replace(array('/\s*([,;:\{\}])\s*/', '/[\t\n\r]/'), array('\\1', ''), 
			$data);
		preg_match_all('/\/\*#(.+?)#\*\//', $data, $matches);
		$data = preg_replace('/\/\*.+?\*\//', '', $data);
		return empty($matches[1]) ? $data : '/*' . implode('*/ /*', $matches[1]) . '*/' . $data;
	}
}

?>