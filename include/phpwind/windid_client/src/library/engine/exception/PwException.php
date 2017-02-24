<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-13
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.net
 * @version $Id: PwException.php 22155 2012-12-19 09:39:53Z yishuo $
 * @package wekit
 * @subpackage exception
 */
class PwException extends WindActionException {

	/**
	 * @param string $message
	 * @param array $vars
	 * @param int $code
	 */
	public function __construct($message, $vars = array(), $code = 0) {
		$message = $this->buildMessage($message, $vars);
		$this->setError(new WindErrorMessage($message));
		$this->code = $code;
	}

	/**
	 * ����exception code���ع������쳣��Ϣ����
	 * 
	 * @param string $message �û��Զ������Ϣ
	 * @param array $vars �쳣��Ϣ�еı���ֵ
	 * @return string ��װ����쳣��Ϣ
	 */
	public function buildMessage($message, $vars) {
		if (strpos($message, 'EXCEPTION:') !== 0) {
			$message = 'EXCEPTION:' . $message;
		}
		return array($message, $vars);
	}
}

?>