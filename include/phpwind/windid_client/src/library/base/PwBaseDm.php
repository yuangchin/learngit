<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('COM:dao.WindDao');

/**
 * phpwind dm�����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwBaseDm.php 20005 2012-10-22 09:39:05Z peihong.zhangph $
 * @package lib.base.dm
 */

abstract class PwBaseDm {
	
	protected $_data = array();
	protected $_increaseData = array();
	protected $_bitData = array();
	protected $_status = array();
	
	/**
	 * ��ȡ������Ϣ
	 *
	 * @return array
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * ��ȡ������������Ϣ
	 *
	 * @return array
	 */
	public function getIncreaseData() {
		return $this->_increaseData;
	}
	
	/**
	 * ��ȡλ�����������Ϣ
	 *
	 * @return array
	 */
	public function getBitData() {
		return $this->_bitData;
	}
	
	/**
	 * ��ȡ���кϲ������ݣ���������ʱ(insert)����
	 *
	 * @return array
	 */
	public function getSetData($increase = true, $bit = true) {
		$data = $this->_data;
		if ($increase && $this->_increaseData) $data = array_merge($data, $this->_increaseData);
		if ($bit && $this->_bitData) {
			foreach ($this->_bitData as $key => $value) {
				$p = 0;
				foreach ($value as $k => $v) {
					if ($v) $p |= 1 << ($k-1);
				}
				$data[$key] = $p;
			}
		}
		return $data;
	}

	final public function beforeAdd() {
		isset($this->_status['add']) || $this->_status['add'] = $this->_beforeAdd();
		return $this->_status['add'];
	}

	final public function beforeUpdate() {
		isset($this->_status['update']) || $this->_status['update'] = $this->_beforeUpdate();
		return $this->_status['update'];
	}
	
	/** 
	 * ��ȡdata�е�����
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getField($field) {
		return isset($this->_data[$field]) ? $this->_data[$field] : null;
	}

	/**
	 * �������ǰ�Ĳ���
	 * 
	 * @return boolean
	 */
	abstract protected function _beforeAdd();
	
	/**
	 * ��������ǰ�Ĳ���
	 * 
	 * @return boolean
	 */
	abstract protected function _beforeUpdate();
}