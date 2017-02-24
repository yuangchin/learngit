<?php
/**
 * �������
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.net
 * @version $Id: PwError.php 17618 2012-09-07 04:01:22Z xiaoxia.xuxx $
 * @package wekit
 */
class PwError {

	protected $error = array();

	/**
	 * ���󴴽�
	 *
	 * @param string $error
	 * @param array $var ������Ϣ�е��滻����
	 * @param array $data ������Ϣ��صľ���ҵ������
	 */
	public function __construct($error = '', $var = array(), $data = array()) {
		$this->addError($error, $var, $data);
	}

	/**
	 * ��Ӵ�����Ϣ
	 *
	 * @param string $error ������Ϣ
	 * @param array $var ������Ϣ�а����Ĵ��������������ʽ��{key}=>'value',��message�д���{key}
	 * <pre>
	 *  ����message�д���һ����
	 *  login.error.pwd="�ʺŻ��������,�������Գ���{num}��"
	 *  �ڷ��ظ��������ʱ��
	 *  $error = new Pw('USER:login.error.pwd', array('{num}' => 5));
	 *  </pre>
	 * @param array $data ��������
	 * @return boolean
	 */
	public function addError($error, $var = array(), $data = array()) {
		if (!$error) return false;
		$tmp = new stdClass();
		$tmp->msg = $var ? array($error, $var) : $error;
		$tmp->data = $data;
		$this->error[] = $tmp;
		return true;
	}

	/**
	 * ��ȡ������Ϣ
	 * 
	 * @param boolean $isAll �Ƿ񷵻����еĴ�����Ϣ
	 * @return string
	 */
	public function getError($isAll = false) {
		if ($isAll !== false) {
			return $this->error;
		} else {
			$tmp = end($this->error);
			return $tmp ? $tmp->msg : '';
		}
	}
	
	/**
	 * ��ȡ������Ϣ����������
	 * 
	 * @return array
	 */
	public function getData() {
		$tmp = end($this->error);
		return $tmp ? $tmp->data : '';
	}
}