<?php
Wind::import('WIND:filter.WindActionFilter');

/**
 * ϵͳĬ��ȫ��filter
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwBaseFilter.php 23656 2013-01-14 06:40:25Z jieyin $
 * @package src
 * @subpackage library.filter
 */
abstract class PwBaseFilter extends WindActionFilter {
	
	/**
	 * ��ʾ��Ϣ
	 *
	 * @param string $message ��Ϣ��Ϣ
	 * @param string $referer ��ת��ַ
	 * @param boolean $referer �Ƿ�ˢ��ҳ��
	 * @see WindSimpleController::showMessage()
	 */
	protected function showMessage($message = '', $referer = '', $refresh = false) {
		$this->errorMessage->addError('success', 'state');
		$this->errorMessage->addError($this->forward->getVars('data'), 'data');
		$this->showError($message, $referer, $refresh);
	}

	/**
	 * ��ʾ����
	 *
	 * @param string $error ��Ϣ��Ϣ
	 * @param string $referer ��ת��ַ
	 * @param boolean $referer �Ƿ�ˢ��ҳ��
	 */
	protected function showError($error = '', $referer = '', $refresh = false) {
		if ($referer) {
			$_referer = explode('#', $referer, 2);
			$referer = WindUrlHelper::createUrl($_referer[0], array(), 
				isset($_referer[1]) ? $_referer[1] : '');
		}
		$this->errorMessage->addError($referer, 'referer');
		$this->errorMessage->addError($refresh, 'refresh');
		$this->errorMessage->addError($error);
		//$errorAction && $this->getErrorMessage()->setErrorAction($errorAction);
		$this->errorMessage->sendError();
	}

	protected function forwardAction($action, $args = array(), $isRedirect = false, $immediately = true) {
		$this->forward->forwardAction($action, $args, $isRedirect, $immediately);
	}

	protected function forwardRedirect($url) {
		$this->forward->forwardRedirect($url);
	}

	protected function setTheme($theme, $package) {
		$this->forward->getWindView()->setTheme($theme, $package);
	}
}
?>