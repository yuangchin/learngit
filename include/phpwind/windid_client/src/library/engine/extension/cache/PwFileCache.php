<?php
Wind::import('WIND:cache.AbstractWindCache');

/**
 * �ļ�����ʵ��
 *	���Ļ���
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
 */
class PwFileCache extends AbstractWindCache {
	/**
	 * ����Ŀ¼
	 *
	 * @var string
	 */
	private $cacheDir;
	
	/**
	 * �����׺
	 *
	 * @var string
	 */
	private $cacheFileSuffix = 'txt';
	
	/**
	 * ������Ŀ¼�ĳ���
	 *
	 * @var int
	 */
	private $cacheDirectoryLevel = 0;
	
	/**
	 * ���滺��Ŀ¼�б�
	 *
	 * ����û��Ѿ����ʹ�ͳһ�����棬���ֱ�ӴӸ��б��л�ȡ�þ���ֵ�������¼��㡣
	 *
	 * @var array
	 */
	private $cacheFileList = array();
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::setValue()
	 */
	protected function setValue($key, $value, $expires = 0) {
		return WindFile::savePhpData($key, $value, 'w');
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::addValue()
	 */
	protected function addValue($key, $value, $expires = 0) {
		return WindFile::savePhpData($key, $value, 'w');
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::getValue()
	 */
	protected function getValue($key) {
		if (!is_file($key)) return null;
		return include $key;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::deleteValue()
	 */
	protected function deleteValue($key) {
		return WindFile::savePhpData($key, '', 'w');
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::clear()
	 */
	public function clear() {
		return WindFolder::clearRecur($this->getCacheDir());
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::buildData()
	 */
	protected function buildData($value, $expires = 0, IWindCacheDependency $dependency = null) {
		$data = array(
			self::DATA => $value,
			self::EXPIRE => $expires ? $expires + time() : 0,
			);
		return $data;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::buildSecurityKey()
	 */
	protected function buildSecurityKey($key) {
		$key = parent::buildSecurityKey($key);
		if (false !== ($dir = $this->checkCacheDir($key))) return $dir;
		$_dir = $this->getCacheDir();
		if (0 < ($level = $this->getCacheDirectoryLevel())) {
			$_subdir = substr(md5($key), 0, $level);
			$_dir .= '/' . $_subdir;
			WindFolder::isDir($_dir) || WindFolder::mk($_dir);
		}
		$filename = $key . '.' . $this->getCacheFileSuffix();
		$this->cacheFileList[$key] = ($_dir ? $_dir . '/' . $filename : $filename);
		return $this->cacheFileList[$key];
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::formatData()
	 */
	protected function formatData($key, $value) {
		if (!$value) return false;
		if (!$value[self::EXPIRE] || $value[self::EXPIRE] >= time()) {
			return $value[self::DATA];
		}
		$this->delete($key);
		return false;
	}
	
	/**
	 * ���û���Ŀ¼
	 *
	 * @param string $dir ����Ŀ¼��������<b>��д�ɶ�</b>Ȩ��
	 */
	public function setCacheDir($dir) {
		$_dir = Wind::getRealPath($dir, false, true);
		WindFolder::mkRecur($_dir);
		$this->cacheDir = realpath($_dir);
	}
	
	/**
	 * ��û���Ŀ¼
	 *
	 * @return string $cacheDir �������õĻ���Ŀ¼
	 */
	private function getCacheDir() {
		return $this->cacheDir;
	}
	
	/**
	 * ���û����ļ��ĺ�׺
	 *
	 * @param string $cacheFileSuffix �����ļ��ĺ�׺��Ĭ��Ϊtxt
	 */
	public function setCacheFileSuffix($cacheFileSuffix) {
		$this->cacheFileSuffix = $cacheFileSuffix;
	}
	
	/**
	 * ��û����ļ��ĺ�׺
	 *
	 * @return string $cacheFileSuffix �����ļ��ĺ�׺
	 */
	private function getCacheFileSuffix() {
		return $this->cacheFileSuffix;
	}
	
	/**
	 * ���û����ŵ�Ŀ¼����Ŀ¼�ĳ���
	 *
	 * @param int $cacheDirectoryLevel ��ֵ�����������Ŀ¼���ӻ���Ŀ¼�ĳ��ȣ���СΪ0��������Ŀ¼�������Ϊ32��md5ֵ�32����ȱʡΪ0
	 */
	public function setCacheDirectoryLevel($cacheDirectoryLevel) {
		$this->cacheDirectoryLevel = $cacheDirectoryLevel;
	}
	
	/**
	 * ���ػ����ŵ�Ŀ¼����Ŀ¼�ĳ���
	 *
	 * ��ֵ�����������Ŀ¼���ӻ���Ŀ¼�ĳ��ȣ���СΪ0��������Ŀ¼�������Ϊ32��md5ֵ�32����ȱʡΪ0
	 *
	 * @return int $cacheDirectoryLevel
	 */
	public function getCacheDirectoryLevel() {
		return $this->cacheDirectoryLevel;
	}
	
	/* (non-PHPdoc)
	 * @see AbstractWindCache::setConfig()
	*/
	public function setConfig($config) {
		parent::setConfig($config);
		$this->setCacheDir($this->getConfig('dir'));
		$this->setCacheFileSuffix($this->getConfig('suffix', '', 'txt'));
		$this->setCacheDirectoryLevel($this->getConfig('dir-level', '', 0));
	}

	/**
	 * �Ƿ񻺴�key�Ѿ����ڻ�������б���
	 *
	 * <ul>
	 * <li>�������key�Ѿ��ڻ�������б���,�򽫻�ֱ�ӷ��ش��ڵ�ֵ</li>
	 * <li>����������򷵻�false.</li>
	 * </ul>
	 *
	 * @param string $key  �����Ļ���key
	 * @return string|boolean ��������򷵻ر������ֵ������������򷵻�false;
	 */
	private function checkCacheDir($key) {
		return isset($this->cacheFileList[$key]) ? $this->cacheFileList[$key] : false;
	}
}