<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * pwӦ�ú�̨��ҳ
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: HomeController.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class HomeController extends AdminBaseController {

	/**
	 * ��̨��ҳ������
	 */
	public function run() {
		//TODO ��̨Ĭ����ҳ������չ֧��
		if (false != ($sendmail_path = ini_get('sendmail_path'))) {
			$sysMail = 'Unix Sendmail ( Path: ' . $sendmail_path . ')';
		} elseif (false != ($SMTP = ini_get('SMTP'))) {
			$sysMail = 'SMTP ( Server: ' . $SMTP . ')';
		} else {
			$sysMail = 'Disabled';
		}
		$db = Wind::getComponent('db');
		$sysinfo = array(
			'wind_version' => 'phpwind v' . NEXT_VERSION . ' ' . NEXT_RELEASE,
			'php_version' => PHP_VERSION, 
			'server_software' => str_replace('PHP/' . PHP_VERSION, '', 
				$this->getRequest()->getServer('SERVER_SOFTWARE')), 
			'mysql_version' => $db->getDbHandle()->getAttribute(PDO::ATTR_SERVER_VERSION), 
			'max_upload' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'Disabled', 
			'max_excute_time' => intval(ini_get('max_execution_time')) . ' seconds', 
			'sys_mail' => $sysMail);
		$this->setOutput($sysinfo, 'sysinfo');
	}

	/**
	 * ��ȡ������Ϣ֪ͨ
	 */
	public function noticeAction() {
		$notice = Wekit::load('APPCENTER:service.srv.PwSystemInstallation')->getNotice(
			$this->adminUser);
		$this->setOutput($notice, 'data');
		$this->showMessage('success');
	}
}

?>