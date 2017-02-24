<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('WIND:utility.WindCookie');

/**
 * �������
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: Pw.php 24747 2013-02-20 03:13:43Z jieyin $
 * @package library
 */
class Pw {

	/**
	 * ȡ��ָ�����Ƶ�cookieֵ
	 *
	 * @param string $name cookie����
	 * @param string $pre cookieǰ׺,Ĭ��Ϊnull��û��ǰ׺
	 * @return boolean
	 */
	public static function getCookie($name) {
		$pre = Wekit::C('site', 'cookie.pre');
		$pre && $name = $pre . '_' . $name;
		return WindCookie::get($name);
	}

	/**
	 * ����cookie
	 *
	 * @param string $name cookie����
	 * @param string $value cookieֵ,Ĭ��Ϊnull
	 * @param string|int $expires ����ʱ��,Ĭ��Ϊnull���Ựcookie,���ŻỰ������������
	 * @param string $pre cookieǰ׺,Ĭ��Ϊnull��û��ǰ׺
	 * @param boolean $httponly
	 * @return boolean
	 */
	public static function setCookie($name, $value = null, $expires = null, $httponly = false) {
		$path = $domain = null;
		if ('AdminUser' != $name) {
			$path = Wekit::C('site', 'cookie.path');
			$domain = Wekit::C('site', 'cookie.domain');
		}
		$pre = Wekit::C('site', 'cookie.pre');
		$pre && $name = $pre . '_' . $name;
		$expires && $expires += self::getTime();
		return WindCookie::set($name, $value, false, $expires, $path, $domain, false, $httponly);
	}

	/**
	 * ���ܷ���
	 *
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	public static function encrypt($str, $key = '') {
		$key || $key = Wekit::C('site', 'hash');
		/* @var $security IWindSecurity */
		$security = Wind::getComponent('security');
		return base64_encode($security->encrypt($str, $key));
	}
	
	/**
	 * ���ܷ���
	 *
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	public static function decrypt($str, $key = '') {
		$key || $key = Wekit::C('site', 'hash');
		/* @var $security IWindSecurity */
		$security = Wind::getComponent('security');
		return $security->decrypt(base64_decode($str), $key);
	}

	/**
	 * ������ܴ洢
	 *
	 * @param string $pwd
	 * @return string
	 */
	public static function getPwdCode($pwd) {
		return md5($pwd . Wekit::C('site', 'hash'));
	}

	/**
	 * ��ȡ�ַ�������
	 *
	 * @param string $string
	 * @return string
	 */
	public static function strlen($string) {
		return WindString::strlen($string, Wekit::V('charset'));
	}

	/**
	 * �ַ�����ȡ
	 *
	 * @param string $string
	 * @param int $length
	 * @param int $start
	 * @param bool $dot
	 */
	public static function substrs($string, $length, $start = 0, $dot = true) {
		if (self::strlen($string) <= $length) return $string;
		return WindString::substr($string, $start, $length, Wekit::V('charset'), $dot);
	}
	
	/**
	 * �������WindCode���ַ���
	 *
	 * @param string $text
	 * @param bool $stripTags
	 */
	public static function stripWindCode($text,$stripTags = false) {
		$pattern = array();
		if (strpos($text, '[post]') !== false && strpos($text, '[/post]') !== false) {
			$pattern[] = '/\[post\].+?\[\/post\]/is';
		}
		if (strpos($text, '[img]') !== false && strpos($text, '[/img]') !== false) {
			$pattern[] = '/\[img\].+?\[\/img\]/is';
		}
		if (strpos($text, '[hide=') !== false && strpos($text, '[/hide]') !== false) {
			$pattern[] = '/\[hide=.+?\].+?\[\/hide\]/is';
		}
		if (strpos($text, '[sell') !== false && strpos($text, '[/sell]') !== false) {
			$pattern[] = '/\[sell=.+?\].+?\[\/sell\]/is';
		}
		$pattern[] = '/\[[a-zA-Z]+[^]]*?\]/is';
		$pattern[] = '/\[\/[a-zA-Z]*[^]]\]/is';
	
		$text = preg_replace($pattern, '', $text);
		$stripTags && $text = strip_tags($text);
		return $text;
	}

	/**
	 * ��������json����
	 *
	 * @param mixed $value ��Ҫ���ܵ�����
	 * @param string $charset �ַ�����
	 * @return string ���ܺ������
	 */
	public static function jsonEncode($value) {
		return WindJson::encode($value, Wekit::V('charset'));
	}

	/**
	 * ��json��ʽ���ݽ���
	 *
	 * @param string $value �����ܵ�����
	 * @param string $charset ���ܺ��ַ�������
	 * @return mixed ���ܺ������
	 */
	public static function jsonDecode($value) {
		return WindJson::decode($value, true, Wekit::V('charset'));
	}
	
	/**
	 * ��������׵�ת����json��ʽ
	 *
	 * @param array $var
	 * @return string
	 */
	public static function array2str($var) {
		if (empty($var) || !is_array($var)) return '{}';
		$str = '';
		foreach ($var as $k => $v) {
			$str .= "'" . WindSecurity::escapeHTML($k) . "' : " . (is_array($v) ? self::array2str($v) : "'" . WindSecurity::escapeHTML($v) . "'") . ",";
		}
		return '{' . rtrim($str, ',') . '}';
	}
	
	/**
	 * ������(A)���ҳ�ָ����ֵ���Ӽ�
	 *
	 * @param array $var ����(A)
	 * @param array $vkeys ָ����ֵ
	 * @return array
	 */
	public static function subArray($var, $vkeys) {
		if (!is_array($var) || !is_array($vkeys)) return array();
		$result = array();
		foreach ($vkeys as $key) {
			if (isset($var[$key])) $result[$key] = $var[$key];
		}
		return $result;
	}
	
	/**
	 * ҳ��תsql
	 *
	 * @param int $page ��ҳ
	 * @param int $perpage ÿҳ��ʾ��
	 * @return array <1.start 2.limit>
	 */
	public static function page2limit($page, $perpage = 10) {
		$limit = intval($perpage);
		$start = max(($page - 1) * $limit, 0);
		return array($start, $limit);
	}

	/**
	 * ��ʱ���ִ�ת������ʱ��ʱ�������
	 *
	 * @param string $str ��ʽ���õ�ʱ�䴮
	 * @return int
	 */
	public static function str2time($str) {
		$timestamp = strtotime($str);
		if ($timezone = Wekit::C('site', 'time.timezone')) $timestamp -= $timezone * 3600;
		return $timestamp;
	}

	/**
	 * ʱ���ת�ַ���
	 *
	 * @example Y-m-d H:i:s  2012-12-12 12:12:12
	 * @param int $timestamp ʱ���
	 * @param string $format ʱ���ʽ
	 * @param int $sOffset ʱ�����ֵ
	 * @return string
	 */
	public static function time2str($timestamp, $format = 'Y-m-d H:i') {
		if (!$timestamp) return '';
		if ($format == 'auto') return self::_time2cpstr($timestamp);
		if ($timezone = Wekit::C('site', 'time.timezone')) $timestamp += $timezone * 3600;
		return gmdate($format, $timestamp);
	}

	protected static function _time2cpstr($timestamp) {
		$current = self::getTime();
		$decrease = $current - $timestamp;
		if ($decrease < 0) return self::time2str($timestamp);
		if ($decrease < 60) return $decrease . '��ǰ';
		if ($decrease < 3600) return ceil($decrease / 60) . '����ǰ';
		$decrease = self::str2time(self::time2str($current, 'Y-m-d')) - self::str2time(self::time2str($timestamp, 'Y-m-d'));
		if ($decrease == 0) return self::time2str($timestamp, 'H:i');
		if ($decrease == 86400) return '����' . self::time2str($timestamp, 'H:i');
		if ($decrease == 172800) return 'ǰ��' . self::time2str($timestamp, 'H:i');
		if (self::time2str($timestamp, 'Y') == self::time2str($current, 'Y')) return self::time2str($timestamp, 'm-d H:i');
		return self::time2str($timestamp);
	}

	/**
	 * ��ȡ��������ʱ���ֵ
	 *
	 * @return int
	 */
	public static function getTime() {
		return WEKIT_TIMESTAMP;
	}
	
	/**
	 * ��ȡ�������ʱ���
	 *
	 * @return int
	 */
	public static function getTdtime() {
		return self::str2time(self::time2str(WEKIT_TIMESTAMP, 'Y-m-d'));
	}
	
	/**
	 * ��ȡͼƬ·��
	 *
	 * @param string $path
	 * @param int $thumb  0:û������ͼ/1������ͼ/2:��������ͼ
	 * @param bool $isLocal �Ƿ�ǿ��ʹ�ñ��ش洢 (Ĭ���Զ�ѡ��)
	 * @return string
	 */
	public static function getPath($path, $ifthumb = 0, $isLocal = false) {
		$storage = Wind::getComponent($isLocal ? 'localStorage' : 'storage');
		return $storage->get($path, $ifthumb);
	}
	
	/**
	 * ��ȡ�û�ͷ���ַ
	 *
	 * @param int $uid
	 * @param string $size <m.��ͷ�� s.Сͷ��>
	 * @return string
	 */
	public static function getAvatar($uid, $size = 'middle') {
		$file = $uid . (in_array($size, array('middle', 'small')) ? '_' . $size : '') . '.jpg';
		return Wekit::C('site', 'avatarUrl') . '/avatar/' . self::getUserDir($uid) . '/' . $file;
	}
	
	/**
	 * ��ȡ�û�ͷ��洢Ŀ¼
	 *
	 * @param int $uid
	 * @return string
	 */
	public static function getUserDir($uid) {
		$uid = sprintf("%09d", $uid);
		return substr($uid, 0, 3) . '/' . substr($uid, 3, 2) . '/' . substr($uid, 5, 2);
	}
	
	/**
	 * ɾ������
	 *
	 * @param string $path ������Ե�ַ
	 * @param int $ifthumb ����ͼ
	 * @param bool $isLocal �Ƿ�ǿ��ʹ�ñ��ش洢 (Ĭ���Զ�ѡ��)
	 * @return bool
	 */
	public static function deleteAttach($path, $ifthumb = 0, $isLocal = false) {
		$storage = Wind::getComponent($isLocal ? 'localStorage' : 'storage');
		return $storage->delete($path, $ifthumb);
	}
	
	/**
	 * ɾ�������ļ�
	 *
	 * @param string $filename �ļ����Ե�ַ
	 * @return bool
	 */
	public static function deleteFile($filename) {
		return WindFile::del(WindSecurity::escapePath($filename, true));
	}

	/**
	 * ����html checked
	 *
	 * @param boolean $var
	 * @return string
	 */
	public static function ifcheck($var) {
		return $var ? ' checked' : '';
	}

	/**
	 * ����html selected
	 *
	 * @param boolean $var
	 * @return string
	 */
	public static function isSelected($var) {
		return $var ? ' selected' : '';
	}

	/**
	 * ����html current
	 *
	 * @param boolean $var
	 * @return string
	 */
	public static function isCurrent($var) {
		return $var ? ' current' : '';
	}

	/**
	 * ����ת��
	 *
	 * @param string $string �����ַ���
	 * @param string $fromEncoding ԭ����
	 * @return string
	 */
	public static function convert($string, $toEncoding, $fromEncoding = '') {
		!$fromEncoding && $fromEncoding = Wekit::V('charset');
		return WindConvert::convert($string, $toEncoding, $fromEncoding);
	}

	/**
	 * ����Ƿ�����
	 *
	 * @param int $time lastvisit
	 * @return bool
	 */
	public static function checkOnline($time) {
		$onlinetime = $pre = Wekit::C('site', 'onlinetime');
		if ($time + $onlinetime * 60 > self::getTime()) {
			return true;
		}
		return false;
	}
	
	/**
	 * λ����ȶ�
	 *
	 * @param int $status ״̬��
	 * @param int $b �ȶ�λ��
	 * @param int $len �ȶ�λ��
	 * @return int
	 */
	public static function getstatus($status, $b, $len = 1) {
		return $status >> --$b & (1 << $len) - 1;
	}
	
	public static function windid($api) {
		if (defined('WINDID_IS_NOTIFY')) {
			$cls[$api] = PwWindidStd::getInstance($api);
		} else {
			$cls[$api] = WindidApi::api($api);
		}
		return $cls[$api];
	}
	
	/**
	 * ����ָ����KEY�ռ���ά�б��и�key��ֵ
	 * 
	 * �磺�ж�ά����
	 * $a = array(array('uid' => 1, 'username' => 'xxx'), array('uid' => 2, 'username' => 'test'));
	 * var_export(Pw::collectByKey($a, 'uid'));
	 * //�����
	 * array(1, 2);
	 * �����һ��Ԫ���и�ֵ�����ڣ����ռ�
	 * 
	 * ע��ֻ�����ڶ�ά����
	 * 
	 * @param array $data ���ռ��Ķ�ά�б�
	 * @param string $key ��Ҫ�ռ���
	 * @return array
	 */
	public static function collectByKey($data, $key) {
		if (!is_array($data) || !$key || empty($data)) return array();
		$_collect = array();
		foreach ($data as $_item) {
			if (is_array($_item) && isset($_item[$key])) {
				$_collect[] = $_item[$key];
			}
		}
		return $_collect;
	}
	
	/**
	 * ����ָ����key��˳����������
	 * 
	 * �磺�ж�ά����
	 * $a = array(
	 * 	1 => array('id' => 1, 'username' => 'test1'), 
	 * 	3 => array('id' => 3, 'username' => 'test3'), 
	 * 	2 => array('id' => 2, 'username' => 'test2'),
	 *  4 => array('username' => 'test4'),
	 *  5 => array('id' => '', 'username' => 'test5'),
	 *  6 => array('id' => 1, 'username' => 'test6'),
	 *);
	 * var_export(Pw::orderByKeys($a, 'id', array(3,2,1)));
	 * //������£�
	 * array(
	 *   5 => array('id' => '', 'username' => 'test5'),
	 * 	 3 => array('id' => 3, 'username' => 'test3'),
	 * 	 2 => array('id' => 2, 'username' => 'test2'),
	 * 	 1 => array('id' => 1, 'username' => 'test1'),
	 *   6 => array('id' => 1, 'username' => 'test6'),
	 * );
	 * ���ĳһάԪ��û������key���Ԫ�ؽ����ᱻ���������KEY��ֵΪ�գ����Ԫ��ֵ�������ź������ǰ�˸���ԭ˳�����
	 * ע��ֻ�����ڶ�ά����
	 *
	 * @param array $data
	 * @param sring $key
	 * @param array $orders
	 * @return array
	 */
	public static function orderByKeys($data, $key, $orders) {
		if (!is_array($data) || !$key || !is_array($orders) || empty($data) || empty($orders)) return array();
		$_newData = $_tmp =  array();
		foreach ($data as $_k => $_v) {
			if (!isset($_v[$key])) continue;
			if (empty($_v[$key])) {
				$_newData[$_k] = $_v;
				continue;
			}
			if (!isset($_tmp[$_v[$key]])) $_tmp[$_v[$key]] = array();
			$_tmp[$_v[$key]][$_k] = $_v;
		}
		foreach ($orders as $_o) {
			if (!isset($_tmp[$_o])) continue;
			foreach ($_tmp[$_o] as $_k => $_v) {
				$_newData[$_k] = $_v;
			}
		}
		return $_newData;
	}
	
	/**
	 * ��дin_array
	 *
	 * @param int|string $value
	 * @param array $array
	 * @return bool
	 */
	public static function inArray($value, $array) {
		return is_array($array) && in_array($value, $array);
	}
}