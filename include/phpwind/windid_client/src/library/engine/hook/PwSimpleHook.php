<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * pw��չ����
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSimpleHook.php 20816 2012-11-12 06:47:39Z jieyin $
 * @package wekit
 * @subpackage engine.hook
 */
class PwSimpleHook {
	
	private static $_instance = array();
	protected $_do = array();

	/**
	 * ���캯����Ĭ���������ڴ˹����µ���չ����
	 *
	 * @param string $hookKey ���ӵ㣬Ĭ��Ϊ����
	 * @param string $interface
	 * @param object $srv
	 * @return void
	 */
	private function __construct($hookKey) {
		if (!$hooks = PwHook::getRegistry('s_' . $hookKey)) return;
		if (!$map = PwHook::resolveActionHook($hooks)) return;
		foreach ($map as $key => $value) {
			$this->appendDo(Wekit::getInstance($value['class'], $value['loadway']), $value['method']);
		}
	}
	
	/**
	 * ��ȡ����ʵ������
	 *
	 * @param string $hookKey ������
	 * @return PwSimpleHook
	 */
	public static function getInstance($hookKey) {
		if (!isset(self::$_instance[$hookKey])) {
			self::$_instance[$hookKey] = new self($hookKey);
		}
		return self::$_instance[$hookKey];
	}

	public function appendDo($do, $method) {
		if ($method && method_exists($do, $method)) {
			$this->_do[] = array($do, $method);
		}
	}
	
	public function runDo() {
		if (!$this->_do) return;
		$args = func_get_args();
		foreach ($this->_do as $key => $_do) {
			call_user_func_array($_do, $args);
		}
	}

	/**
	 * Ϊ����ע�����չ��������ָ������;
	 * ģʽ:����һ����������(������true)ʱ���ж�����
	 *
	 * @param string $method ������
	 * @return true|PwError����
	 */
	public function runWithVerified() {
		if (!$this->_do) return true;
		$args = func_get_args();
		foreach ($this->_do as $key => $_do) {
			if (($result = call_user_func_array($_do, $args)) !== true) return $result;
		}
		return true;
	}

	/**
	 * Ϊ����ע�����չ��������ָ������;
	 * ģʽ:���϶��´���$value����
	 *
	 * @param string $method ������
	 * @param mixed $value ���ݵ�ֵ
	 * @return mixed ������ֵ
	 */
	public function runWithFilters($value) {
		if (!$this->_do) return $value;
		$args = func_get_args();
		foreach ($this->_do as $key => $_do) {
			$args[0] = $value;
			$value = call_user_func_array($_do, $args);
		}
		return $value;
	}
}
?>