<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * ubbת������
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUbbCodeConvertConfig.php 21135 2012-11-29 02:10:03Z jieyin $
 * @package lib.utility
 */

class PwUbbCodeConvertConfig {

	public $isConvertPost = false;
	public $isConvertHide = false;
	public $isConvertSell = false;
	public $isConverImg = true;
	public $isConvertMedia = 0;	//meaia��ǩ������ʽ <0.������ 1.�����������ɲ����� 2.���������ɲ�����>
	public $isConvertFlash = 0;	//flash��ǩ������ʽ <0.�����������ɲ����� 1.���������ɲ�����>
	public $isConvertTable = 0;	//table���� ֵΪtableǶ�׽����������ֵ
	public $isConvertIframe = 0;	//iframe��ǩ������ʽ <0.������ 1.������ֻ�������� 2.����������iframe>
	public $remindUser = array(); //�ᵽ����(@admin)

	public $cvtimes = 20;
	public $imgWidth = 400;
	public $imgHeight = 400;
	public $imgLazy = false;
	public $maxSize = 0;

	public function setRemindUser($user) {
		is_array($user) || $user = self::_splitArray($user);
		$this->remindUser = $user;
	}

	public function setImgLazy($isLazy) {

		$this->imgLazy = empty($isLazy) ? false : true;
	}
	
	public function isPost() {
		return false;
	}

	public function isLogin() {
		return false;
	}

	public function isAuthor() {
		return false;
	}

	public function checkCredit($cValue, $cType) {
		return false;
	}

	public function isBuy() {
		return false;
	}

	public function getSellInfo() {
		return array();
	}

	public function getUserCredit($cType) {
		return 0;
	}

	public function getAttachHtml($aid) {
		return '';
	}

	protected static function _splitArray($array) {
		$a = explode(',', $array);
		$l = count($a);
		$l % 2 == 1 && $l--;
		$r = array();
		for ($i = 0; $i < $l; $i+=2) {
			$r[$a[$i+1]] = $a[$i];
		}
		return $r;
	}
}