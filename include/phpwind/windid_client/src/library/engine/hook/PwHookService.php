<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * pw��չ����
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwHookService.php 8692 2012-04-24 05:56:29Z jieyin $
 * @package wekit
 * @subpackage engine.hook
 */
class PwHookService extends PwBaseHookService {
	
	protected $_interface;

	/**
	 * ���캯����Ĭ���������ڴ˹����µ���չ����
	 *
	 * @param string $hookKey ���ӵ㣬Ĭ��Ϊ����
	 * @param string $interface
	 * @param object $srv
	 * @return void
	 */
	public function __construct($hookKey, $interface, $srv = '') {
		parent::__construct($hookKey);
		$this->setSrv($srv);
		$this->_interface = $interface;
	}

	/**
	 * ָ����չ����Ľӿ���(�����)
	 * 
	 * �ó��󷽷�����һ�����Ͷ���{@see PwBaseHookService::appendDo}
	 * ע�뵽�÷������չ����Ϊ������.
	 * @return string
	 */
	protected function _getInterfaceName() {
		return $this->_interface;
	}
}
?>