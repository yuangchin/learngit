<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * controller ����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwBaseController.php 23973 2013-01-17 09:45:57Z jieyin $
 * @package lib.base.controller
 */
class PwBaseController extends WindController {

	/**
	 * ��ǰ�û���Ϣ
	 *
	 * @var PwUserBo $loginUser
	 */
	protected $loginUser;
	protected $_m;
	protected $_c;
	protected $_a;
	protected $_mc;
	protected $_mca;
	
	/*
	 * (non-PHPdoc) @see WindSimpleController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {

		$this->_m = $handlerAdapter->getModule();
		$this->_c = $handlerAdapter->getController();
		$this->_a = $handlerAdapter->getAction();
		$this->_mc = $this->_m . '/' . $this->_c;
		$this->_mca = $this->_mc . '/' . $this->_a;
		
		$this->loginUser = Wekit::getLoginUser();
		$this->setTheme('site', null);
	}


	/**
	 * ��ʾ��Ϣ
	 *
	 * @param string $message ��Ϣ��Ϣ
	 * @param string $referer ��ת��ַ
	 * @param boolean $referer �Ƿ�ˢ��ҳ��
	 * @see WindSimpleController::showMessage()
	 */
	protected function showMessage($message = '', $referer = '', $refresh = false) {
		$this->addMessage('success', 'state');
		$this->addMessage($this->forward->getVars('data'), 'data');
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
		$this->addMessage($referer, 'referer');
		$this->addMessage($refresh, 'refresh');
		parent::showMessage($error);
	}
	
	/*
	 * (non-PHPdoc) @see WindSimpleController::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$this->setTemplate($handlerAdapter->getController() . '_' . $handlerAdapter->getAction());
	}
	
	/*
	 * (non-PHPdoc) @see WindSimpleController::afterAction()
	 */
	public function afterAction($handlerAdapter) {
		$this->runDesign();
		$this->setOutput($this->loginUser, 'loginUser');
		$this->updateOnline();
		$this->setOutput($this->runCron(), 'runCron');
	}

	/**
	 * action Hook ע��
	 *
	 * @param string $registerKey ��չ�����
	 * @param PwBaseHookService $bp        	
	 * @throws PwException
	 * @return void
	 */
	protected function runHook($registerKey, $bp) {
		if (!$registerKey) return;
		if (!$bp instanceof PwBaseHookService) {
			throw new PwException('class.type.fail', 
				array(
					'{parm1}' => 'src.library.base.PwBaseController.runHook', 
					'{parm2}' => 'PwBaseHookService', 
					'{parm3}' => get_class($bp)));
		}
		if (!$filters = PwHook::getRegistry($registerKey)) return;
		if (!$filters = PwHook::resolveActionHook($filters, $bp)) return;
		$args = func_get_args();
		$_filters = array();
		foreach ($filters as $key => $value) {
			$args[0] = isset($value['method']) ? $value['method'] : '';
			$_filters[] = array('class' => $value['class'], 'args' => $args);
		}
		$this->resolveActionFilter($_filters);
	}
	
	/**
	 * �Ż����̿���
	 * Enter description here ...
	 */
	protected function runDesign() {
		$pageName = $unique = '';
		$pk = 0;
		if ($this->_mca == 'bbs/read/run') return true;//�����Ķ�ҳ��ReadController�ﴦ��
		$sysPage = Wekit::load('design.srv.router.PwDesignRouter')->get();
		if (!isset($sysPage[$this->_mca]))return false;
		list($pageName, $unique) = $sysPage[$this->_mca];
		$unique && $pk = $this->getInput($unique, 'get');
		if (!$pk) return false;
		Wind::import('SRV:design.bo.PwDesignPageBo');
    	$bo = new PwDesignPageBo();
    	$pageid = $bo->getPageId($this->_mca, $pageName, $pk);
		$pageid && $this->forward->getWindView()->compileDir = 'DATA:compile.design.'.$pageid;
		return true;
	}
	
	/**
	 * ��ҳ�󶨼ƻ�����
	 *
	 * @return string Ambigous string>
	 */
	protected function runCron() {
		$ishome = false;
		$homeRouter = Wekit::C('site', 'homeRouter');
		if (!$homeRouter) return '';
		$request = $this->getRequest();
		if ($this->_m == $homeRouter['m'] && $this->_c == $homeRouter['c'] && $this->_a == $homeRouter['a']) {
			$ishome = true;
		}
		unset($homeRouter['m'], $homeRouter['c'], $homeRouter['a']);
		foreach ($homeRouter as $k => $v) {
			if (!$k) continue;
			if ($request->getAttribute($k) != $v) $ishome = false;
		}
		if (!$ishome) return '';
		$time = Pw::getTime();
		$cron = Wekit::load('cron.PwCron')->getFirstCron();
		if (!$cron || $cron['next_time'] > $time) return '';
		return WindUrlHelper::createUrl('cron/index/run/');
	}

	/**
	 * ���߷���  	
	 */
	protected function updateOnline() {
		if ($this->loginUser->uid > 0 && $this->_mca == 'bbs/read/run') return false; //�����Ķ�ҳ��ReadController�ﴦ��
		if ($this->loginUser->uid > 0 && $this->_m == 'space') return false; //�ռ���spaceBaseController�ﴦ��
		$online = Wekit::load('online.srv.PwOnlineService');
		// $service->clearNotOnline(); // �ɼƻ���������
		if ($this->loginUser->uid > 0 && $this->_mca == 'bbs/thread/run') {
			$createdTime = $online->forumOnline($this->getInput('fid'));
		} else {
			$request = $this->getRequest();
			$clientIp = $request->getClientIp();
			$createdTime = $online->visitOnline($clientIp);
		}
		if (!$createdTime) return false;
		$dm = Wekit::load('online.dm.PwOnlineDm');
		$time = Pw::getTime();
		if ($this->loginUser->uid > 0) {
			$dm->setUid($this->loginUser->uid)->setUsername($this->loginUser->username)->setModifytime($time)->setCreatedtime($createdTime)->setGid($this->loginUser->gid)->setFid($this->getInput('fid', 'get'))->setRequest($this->_mca);
			Wekit::load('online.PwUserOnline')->replaceInfo($dm);
		} else {
			$dm->setIp($clientIp)->setCreatedtime($createdTime)->setModifytime($time)->setFid($this->getInput('fid', 'get'))->setTid($this->getInput('tid', 'get'))->setRequest($this->_mca);
			Wekit::load('online.PwGuestOnline')->replaceInfo($dm);
		}
	}

	/**
	 * �������
	 *
	 * ���õ�ǰҳ������Ҫ����������$type������ͣ�$theme�������·��
	 *
	 * @see WindSimpleController::setTheme()
	 * @param string $type �������(site,space,area...)
	 * @param string $theme ������
	 */
	protected function setTheme($type, $theme) {
		$config = Wekit::C('site');
		$themePack = $config['theme.' . $type . '.pack'];
		$themePack = 'THEMES:' . $themePack;
		// ���Ԥ��������ԱȨ��
		if ($style = Pw::getCookie('style_preview')) {
			list($s_theme, $s_type) = explode('|', $style, 2);
			if ($s_type == $type) {
				$theme = $s_theme;
				Wekit::C()->site->set('theme.' . $type . '.default', $theme);
			}
		}
		if (!$theme) $theme = $config['theme.' . $type . '.default'];
		parent::setTheme($theme, $themePack);
	}
}