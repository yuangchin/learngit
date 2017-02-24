<?php
Wind::import('WEKIT:engine.exception.PwException');

/**
 * �����쳣
 * 
 * �������İ����߷��񲻴���ʱ�׳��������쳣��ϵͳ������������쳣
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright 2003-2103 phpwind.com
 * @version $Id$
 * @package 
 */
class PwDependanceException extends PwException {

	/* (non-PHPdoc)
	 * @see PwException::buildMessage()
	 */
	public function buildMessage($message, $vars) {
		if (strpos($message, 'dependance.') !== 0) {
			$message = 'dependance.' . $message;
		}
		return parent::buildMessage($message, $vars);
	}
}

?>