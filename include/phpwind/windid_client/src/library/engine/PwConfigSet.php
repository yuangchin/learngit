<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * ���ù���
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-6
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwConfigSet.php 24407 2013-01-30 03:39:59Z jieyin $
 * @package src
 * @subpackage service.config.bo
 */
class PwConfigSet {

	protected $namespace = 'site';
	protected $config = array();

	/**
	 * @param string $namespace
	 */
	public function __construct($namespace = '') {
		$namespace && $this->namespace = $namespace;
	}

	/**
	 * ����һ������ѡ��
	 *
	 * @param string $name ������
	 * @param mixed $value ����ֵ
	 * @param string $descrip ����
	 * @return PwConfigSet
	 */
	public function set($name, $value, $descrip = null) {
		$this->config[$name] = array('name' => $name, 'value' => $value, 'descript' => $descrip);
		return $this;
	}

	/**
	 * ���ص�ǰ���õ�ֵ
	 * 
	 * @param string $name
	 */
	public function get($name) {
		return isset($this->config[$name]) ? $this->config[$name]['value'] : '';
	}

	/**
	 * �����ݳ־û������ݿ�
	 */
	public function flush() {
		Wekit::C()->setConfigs($this->namespace, $this->config);
	}
	
	public function getAll() {
		return $this->config;
	}
}
?>