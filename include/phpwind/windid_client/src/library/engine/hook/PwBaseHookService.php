<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * pw��չ����
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwBaseHookService.php 23093 2013-01-06 04:04:36Z jieyin $
 * @package wekit
 * @subpackage engine.hook
 */
abstract class PwBaseHookService {

	/**
	 * �ⲿע���������չʵ�ֵļ���
	 *
	 * @var array
	 */
	protected $_do = array();
	protected $_srv;
	protected $_key = array();
	protected $_ready = false;
	
	/**
	 * ���캯����Ĭ���������ڴ˹����µ���չ����
	 *
	 * @param string $hookKey ���ӵ㣬Ĭ��Ϊ����
	 * @param object $srv
	 * @return void
	 */
	public function __construct($hookKey = '') {
		!$hookKey && $hookKey = get_class($this);
		$this->setHook($hookKey);
	}

	public function setSrv($srv) {
		$this->_srv = $srv;
	}

	public function setHook($hookKey, $pre = 'm') {
		$this->_key[] = $pre . '_' . $hookKey;
	}

	protected function _prepare() {
		if ($this->_ready) {
			return !empty($this->_do);
		}
		!$this->_srv && $this->_srv = $this;
		foreach ($this->_key as $key => $hookKey) {
			if (!$hooks = PwHook::getRegistry($hookKey)) continue;
			if (!$map = PwHook::resolveActionHook($hooks, $this->_srv)) continue;
			foreach ($map as $key => $value) {
				$this->appendDo(Wekit::getInstance($value['class'], $value['loadway'], array($this->_srv)));
			}
		}
		$this->_ready = true;
		return !empty($this->_do);
	}

	/**
	 * ָ����չ����Ľӿ���(�����)
	 * 
	 * �ó��󷽷�����һ�����Ͷ���{@see PwBaseHookService::appendDo}
	 * ע�뵽�÷������չ����Ϊ������.
	 * @return string
	 */
	abstract protected function _getInterfaceName();

	/**
	 * Ϊ��ǰ���������չ����
	 * 
	 * ͨ�����ø÷���,��÷�����ע����չ����,�ο�{@see PwHookInjector::preHandle}ʵ��.
	 * @param object $do ��չ����
	 * @return void
	 */
	public function appendDo($do) {
		$instanceN = $this->_getInterfaceName();
		if ($do instanceof $instanceN) {
			$this->_do[] = $do;
		}
	}

	/**
	 * Ϊ����ע�����չ��������ָ������;
	 * ģʽ:ȫ������,��״̬
	 *
	 * @param string $method ������
	 * @return void
	 */
	public function runDo($method) {
		if (!$this->_prepare()) return;
		$args = array_slice(func_get_args(), 1);
		foreach ($this->_do as $key => $_do) {
			call_user_func_array(array($_do, $method), $args);
		}
	}

	/**
	 * Ϊ����ע�����չ��������ָ������;
	 * ģʽ:����һ����������(������true)ʱ���ж�����
	 *
	 * @param string $method ������
	 * @return true|PwError����
	 */
	public function runWithVerified($method) {
		if (!$this->_prepare()) return true;
		$args = array_slice(func_get_args(), 1);
		foreach ($this->_do as $key => $_do) {
			if (($result = call_user_func_array(array($_do, $method), $args)) !== true) return $result;
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
	public function runWithFilters($method, $value) {
		if (!$this->_prepare()) return $value;
		$args = array_slice(func_get_args(), 1);
		foreach ($this->_do as $key => $_do) {
			$args[0] = $value;
			$value = call_user_func_array(array($_do, $method), $args);
		}
		return $value;
	}

	/**
	 * ��ȡ��ǰ�����ĳһ�����Ե�ֵ;
	 *
	 * @param string $var ������
	 * @return mixed
	 */
	public function getAttribute($var) {
		if (!property_exists($this, $var)) return false;
		$result = $this->$var;
		if (func_num_args() > 1) {
			$args = array_slice(func_get_args(), 1);
			$result = $this->_getAttribute($result, $args);
		}
		return $result;
	}

	public function getHookKey() {
		return $this->_key[0];
	}

	/**
	 * ���ص�ǰ������ж�Ӧ�����Ե�ֵ
	 *
	 * @param mixed $result
	 * @param array $attributes
	 * @return mixed
	 */
	private function _getAttribute($result, $attributes) {
		foreach ($attributes as $value) {
			if (is_array($result)) {
				$result = $result[$value];
			} elseif (is_object($result) && property_exists($result, $value)) {
				$result = $result->$value;
			} else {
				return false;
			}
		}
		return $result;
	}
}
?>