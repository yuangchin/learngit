<?php
/**
 * ������Ϣ����ӿ�
 * 
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: WindidConfigBo.php 21452 2012-12-07 10:18:33Z gao.wanggao $
 * @package windid.config
 */
class WindidConfigBo {

	protected static $_instance = array();
	protected $_data = array();

	/**
	 * ���캯��
	 *
	 * @param array $array
	 */
	public function __construct($array = array()) {
		$sub = array();
		foreach ($array as $key => $value) {
			if (strpos($key, '.') !== false) {
				list($k1, $k2) = explode('.', $key, 2);
				if (strlen($k1) && strlen($k2)) {
					isset($sub[$k1]) || $sub[$k1] = array();
					$sub[$k1][$k2] = $value;
				}
			} else {
				$this->_data[$key] = $value;
			}
		}
		foreach ($sub as $key => $value) {
			$this->_data[$key] = new self($value);
		}
	}

	/**
	 * ��������Ϣת��Ϊ����
	 *
	 */
	public function toArray() {
		$array = array();
		foreach ($this->_data as $key => $value) {
			if ($value instanceof self) {
				$array[$key] = $value->toArray();
			} else {
				$array[$key] = $value;
			}
		}
		return $array;
	}

	/**
	 * ħ������
	 *
	 * @param string $name �ƶ�������
	 * @return mixed
	 */
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * ���������Ϣ���ƶ��������ֵ
	 *
	 * @param string $name ����ȡ��������
	 * @param mixed $default ���������ֵʱ���ص�ȱʡֵ��Ĭ��Ϊnull
	 * @return mixed
	 */
	public function get($name, $default = null) {
		$result = $default;
		if (array_key_exists($name, $this->_data)) {
			$result = $this->_data[$name];
		}
		return $result;
	}

	/**
	 * ��ȡ������Ϣ
	 *
	 * @param string $namespace ��ȡָ�����������Ϣ
	 * @return WindidConfig
	 */
	static public function getInstance($namespace = 'global') {
		if (!isset(self::$_instance[$namespace])) {
			$config = self::getConfig($namespace);
			self::$_instance[$namespace] = new self($config);
		}
		return self::$_instance[$namespace];
	}

	/**
	 * ��ȡ������Ϣ
	 *
	 * @param string $namespace ��ȡָ�����������Ϣ
	 * @return array
	 */
	static public function getConfig($namespace) {
		$array = Windid::load('config.WindidConfig')->getConfig($namespace);
		return self::_formatConfig($array);
	}

	/**
	 * ��ʽ����������Ϣ
	 *
	 * @param array $array
	 * @return array
	 */
	static private function _formatConfig($array) {
		$temp = array();
		foreach ($array as $key => $value) {
			$temp[$value['name']] = ($value['vtype'] == 'string') ? $value['value'] : unserialize($value['value']);
		}
		return $temp;
	}
}