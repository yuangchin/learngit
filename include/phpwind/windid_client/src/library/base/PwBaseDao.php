<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('WIND:dao.WindDao');

/**
 * phpwind dao�����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwBaseDao.php 24810 2013-02-21 10:32:03Z jieyin $
 * @package lib
 * @subpackage base.dao
 */

class PwBaseDao extends WindDao {
	
	protected $_table;
	protected $_pk = 'id';
	protected $_className = '';
	protected $_dataStruct = array();
	protected $_baseInstance = null;
	protected $_defaultBaseInstance = '';

	public function __construct() {
		$this->setDelayAttributes(array('connection' => array('ref' => 'db')));
	}

	/**
	 * ���õ�ǰdao�Ļ���DAO��
	 *
	 * @param PwBaseDao $instance
	 */
	public function setBaseInstance($instance) {
		$this->_baseInstance = $instance;
	}

	/**
	 * ��ȡ��ǰdao��Ļ���DAO��
	 * 
	 * ��ȡ��ǰdao��Ļ���DAO��,��baseInstanceû������ʱ,����defaultBaseInstance
	 * @throws Exception
	 * @return PwBaseDao
	 */
	public function getBaseInstance() {
		if (!$this->_baseInstance) {
			if (empty($this->_defaultBaseInstance)) {
				throw new Exception('This dao is error');
			}
			$this->_baseInstance = Wekit::loadDao($this->_defaultBaseInstance);
		}
		return $this->_baseInstance;
	}

	/**
	 * ��ȡ��ǰdao������
	 *
	 * @return string
	 */
	public function getTable($table = '') {
		!$table && $table = $this->_table;
		return $this->getConnection()->getTablePrefix() . $table;
	}
	
	/**
	 * ��ȡ��ǰdao���ֶνṹ
	 *
	 * @return array
	 */
	public function getDataStruct() {
		return $this->_dataStruct;
	}

	/**
	 * sql��װ,��������װ��`key`=value����ʽ����
	 *
	 * @param array $array ����װ������
	 * @return string
	 */
	public function sqlSingle($array) {
		return $this->getConnection()->sqlSingle($array);
	}

	/**
	 * sql��װ,��������װ��`key`=`key`+value����ʽ����
	 *
	 * @param array $array ����װ������
	 * @return string
	 */
	public function sqlSingleIncrease($array) {
		if (!is_array($array)) return '';
		$str = array();
		foreach ($array as $key => $val) {
			$key = $this->getConnection()->sqlMetadata($key);
			$str[] = $key . '=' . $key . '+' . $this->getConnection()->quote($val);
		}
		return $str ? implode(',', $str) : '';
	}
	
	/**
	 * sql��װ,��������װ������`key`=`key`|value��λ������ʽ����
	 *
	 * @param array $array ����װ������
	 * @return string
	 */
	public function sqlSingleBit($array) {
		if (!is_array($array)) return '';
		$str = array();
		foreach ($array as $key => $val) {
			if (!$val || !is_array($val)) continue;
			$key = $this->getConnection()->sqlMetadata($key);
			foreach ($val as $bit => $v) {
				$str[] = $key . '=' . $key . ($v ? '|' : '&~') . '(1<<' . ($bit-1) . ')';
			}
		}
		return $str ? implode(',', $str) : '';
	}

	/**
	 * sql��װ,��������װ��('a1','b1','c1'),('a2','b2','c2')����ʽ����
	 *
	 * @param array $array ����װ������
	 * @return string
	 */
	public function sqlMulti($array) {
		return $this->getConnection()->quoteMultiArray($array);
	}

	/**
	 * sql��װ,��������װ��('a1','b1','c1')����ʽ����
	 *
	 * @param array $array ����װ������
	 * @return string
	 */
	public function sqlImplode($array) {
		return $this->getConnection()->quoteArray($array);
	}

	/**
	 * ��װsql limit���ʽ��,��������װ��Ľ��
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	public function sqlLimit($limit, $offset = 0) {
		if (!$limit) return '';
		return ' LIMIT ' . max(0, intval($offset)) . ',' . max(1, intval($limit));
	}

	/**
	 * sql������,(sqlSingle, sqlSingleIncrease) `key`=value,`key`=`key`+value
	 *
	 * @param array $updateFields ���²������ֶ�
	 * @param array $IncreaseFields �����������ֶ�
	 * @param array $bitFields
	 * @return string
	 */
	public function sqlMerge($updateFields, $increaseFields, $bitFields = array()) {
		$sql = $etr = '';
		if ($updateFields) {
			$sql .= $this->sqlSingle($updateFields);
			$etr = ',';
		}
		if ($increaseFields) {
			$sql .= $etr . $this->sqlSingleIncrease($increaseFields);
			$etr = ',';
		}
		if ($bitFields) {
			$sql .= $etr . $this->sqlSingleBit($bitFields);
		}
		return $sql;
	}

	/**
	 * ��tablename,�����ذ󶨺���
	 *
	 * @param string $sql ��Ҫ��tablename��sql���
	 * @param string $table Ĭ��Ϊ��ǰ��
	 * @return string
	 */
	protected function _bindTable($sql, $table = '') {
		$table === '' && $table = $this->getTable();
		return sprintf($sql, $table);
	}

	/**
	 * ��sql�еı���,�����ذ󶨺���
	 *
	 * @param string $sql ��Ҫ�󶨱���������sql���
	 * @return string
	 */
	protected function _bindSql($sql) {
		$args = func_get_args();
		return call_user_func_array('sprintf', $args);
	}

	/**
	 * ���˵�ǰ��ṹ
	 *
	 * @param array $array
	 * @param array $allow
	 * @return multitype:|unknown|multitype:unknown 
	 */
	protected function _filterStruct($array, $allow = array()) {
		if (empty($array) || !is_array($array)) return array();
		empty($allow) && $allow = $this->getDataStruct();
		if (empty($allow) || !is_array($allow)) return $array;
		$data = array();
		foreach ($array as $key => $value) {
			in_array($key, $allow) && $data[$key] = $value;
		}
		return $data;
	}

	/**
	 * ������ϲ�
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return multitype:Ambigous <multitype:, unknown> 
	 */
	protected function _margeArray($array1, $array2) {
		$result = array();
		foreach ($array1 as $key => $value) {
			$result[$key] = isset($array2[$key]) ? array_merge($value, $array2[$key]) : $value;
		}
		return $result;
	}

	protected function _get($id) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE %s=?', $this->getTable(), $this->_pk);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($id));
	}

	protected function _fetch($ids, $index = '', $fetchMode = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE %s IN %s ', $this->getTable(), $this->_pk, $this->sqlImplode($ids));
		$rst = $this->getConnection()->query($sql);
		return $rst->fetchAll($index, $fetchMode);
	}

	protected function _add($fields, $getId = true) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		if (($result = $this->getConnection()->execute($sql)) && $getId) {
			$result = $this->getConnection()->lastInsertId();
		}
		PwSimpleHook::getInstance($this->_class() . '_add')->runDo($result, $fields);
		return $result;
	}

	protected function _update($id, $fields, $increaseFields = array(), $bitFields = array()) {
		$fields = $this->_filterStruct($fields);
		$increaseFields = $this->_filterStruct($increaseFields);
		$bitFields = $this->_filterStruct($bitFields);
		if (!$fields && !$increaseFields && !$bitFields) {
			return false;
		}
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE %s=?', $this->getTable(), $this->sqlMerge($fields, $increaseFields, $bitFields), $this->_pk);
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($id));
		PwSimpleHook::getInstance($this->_class() . '_update')->runDo($id, $fields, $increaseFields);
		return $result;
	}

	protected function _batchUpdate($ids, $fields, $increaseFields = array(), $bitFields = array()) {
		$fields = $this->_filterStruct($fields);
		$increaseFields = $this->_filterStruct($increaseFields);
		$bitFields = $this->_filterStruct($bitFields);
		if (!$fields && !$increaseFields && !$bitFields) {
			return false;
		}
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE %s IN %s', $this->getTable(), $this->sqlMerge($fields, $increaseFields, $bitFields), $this->_pk, $this->sqlImplode($ids));
		$this->getConnection()->execute($sql);
		PwSimpleHook::getInstance($this->_class() . '_batchUpdate')->runDo($ids, $fields, $increaseFields);
		return true;
	}

	protected function _delete($id) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE %s=?', $this->getTable(), $this->_pk);
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($id));
		PwSimpleHook::getInstance($this->_class() . '_delete')->runDo($id);
		return $result;
	}

	protected function _batchDelete($ids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE %s IN %s', $this->getTable(), $this->_pk, $this->sqlImplode($ids));
		$this->getConnection()->execute($sql);
		PwSimpleHook::getInstance($this->_class() . '_batchDelete')->runDo($ids);
		return true;
	}

	protected function _class() {
		return $this->_className ? $this->_className : get_class($this);
	}
}