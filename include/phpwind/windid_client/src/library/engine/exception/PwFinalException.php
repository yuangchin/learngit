<?php
/**
 * �������쳣������ϵͳcache��ֱ�ӱ��׳�
 * 
 * �������쳣������ϵͳcache��ֱ�ӱ��׳��������ڴ����쳣������ϵ�г������쳣��
 * ֧��i18n�Z�԰�����
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-13
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.net
 * @version $Id: PwFinalException.php 20274 2012-10-25 07:49:56Z yishuo $
 * @package wekit
 * @subpackage exception
 */
class PwFinalException extends WindFinalException {

	/**
	 * @param string $message
	 * @param array $vars
	 * @param int $code default 500
	 */
	public function __construct($message = 'default', $vars = array(), $code = 500) {
		$this->message = $this->buildMessage($message, $vars);
		$this->code = $code;
	}

	/**
	 * �����쳣��Ϣ
	 *
	 * @param string $message
	 * @param array $vars
	 * @return string
	 */
	public function buildMessage($message, $vars) {
		if (strpos($message, 'fianl.') !== 0) $message = 'final.' . $message;
		if (strpos($message, 'EXCEPTION:') !== 0) $message = 'EXCEPTION:' . $message;
		
		/* @var $resource WindLangResource */
		$resource = Wind::getComponent('i18n');
		if (null !== $resource) {
			$message = $resource->getMessage($message, $vars);
		}
		return $message;
	}
}

?>