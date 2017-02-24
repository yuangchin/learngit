<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('WIND:filter.WindActionFilter');

/**
 * PwHook action���ع���������ӿڶ���
 * 
 * ͨ���̳иýӿ�,����ʵ����Controller��ע����չʵ��.�ýӿ�Ĭ�ϵ���'run'����.
 * ֧�ֶ������չ.
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-2
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwBaseHookInjector.php 8692 2012-04-24 05:56:29Z jieyin $
 * @package src
 * @subpackage library.filter
 */
abstract class PwBaseHookInjector extends WindActionFilter {

	private $callback = 'run';
	private $args = array();
	/**
	 * @var PwBaseHookService
	 */
	protected $bp = null;

	/**
	 * @param WindForward $forward
	 * @param WindErrorMessage $errorMessage
	 * @param WindRouter $router
	 * @param array $args
	 */
	public function __construct($forward, $errorMessage, $router, $args = array()) {
		parent::__construct($forward, $errorMessage, $router);
		!empty($args[0]) && $this->callback = $args[0];
		isset($args[1]) && $this->bp = $args[1];
		if (count($args) > 2) {
			unset($args[0], $args[1]);
			$this->args = $args;
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		if (!method_exists($this, $this->callback)) return;
		$injector = call_user_func_array(array($this, $this->callback), $this->args);
		if ($injector) $this->bp->appendDo($injector);
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {}

}
?>