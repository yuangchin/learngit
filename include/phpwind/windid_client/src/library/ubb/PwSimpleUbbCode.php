<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:ubb.config.PwUbbCodeConvertConfig');
Wind::import('SRV:credit.bo.PwCreditBo');

/**
 * ubbת��
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwSimpleUbbCode.php 24383 2013-01-29 10:09:39Z jieyin $
 * @package lib.utility
 */

class PwSimpleUbbCode {
	
	protected static $_code = array();
	protected static $_isSubstr = false;
	protected static $_hide = false;
	protected static $_emotion = null;
	
	/**
	 * ת������
	 *
	 * @param string $message Դ����
	 * @return string ת���������
	 */
	public static function convertParagraph($message) {
		if (($pos = strpos($message,"[paragraph]")) !== false && $pos < 10) {
			$message = str_replace('[paragraph]', '', $message);
		}
		return $message;
	}

	/**
	 * ת��ͬ��ubb��ǩ��html
	 *
	 * @param string $message Դ����
	 * @param mixed $tag Ҫת���ı�ǩ <��: 1.���� string u/b/ 2.��� array('u','b')>
	 * @return string ת���������
	 */
	public static function convertTag($message, $tag) {
		is_array($tag) || $tag = array($tag);
		foreach ($tag as $v) {
			$message = str_replace(array("[$v]", "[/$v]"), '', $message);
		}
		return $message;
	}
	
	/**
	 * ת��hr��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertHr($message) {
		return str_replace('[hr]', '', $message);
	}
	
	/**
	 * ת��list��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertList($message) {
		$message = preg_replace('/\[list=([aA1]?)\](.+?)\[\/list\]/is', '', $message);
		return str_replace(
			array('[list]', '[li]', '[/li]', '[/list]'),
			'',
			$message
		);
	}
	
	/**
	 * ת��font��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertFont($message) {
		$message = preg_replace("/\[font=([^\[\(&\\;]+?)\]/is", '', $message);
		return str_replace('[/font]', '', $message);
	}
	
	/**
	 * ת��color��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertColor($message) {
		$message = preg_replace("/\[color=([#0-9a-z]{1,15})\]/is", '', $message);
		return str_replace('[/color]', '', $message);
	}
	
	/**
	 * ת��backcolor��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertBackColor($message) {
		$message = preg_replace("/\[backcolor=([#0-9a-z]{1,10})\]/is", '', $message);
		return str_replace('[/backcolor]', '', $message);
	}
	
	/**
	 * ת��size��ǩ
	 *
	 * @param string $message ����
	 * @param int $maxSize ����������� <0.������>
	 * @return string
	 */
	public static function convertSize($message, $maxSize = 0) {
		$message = preg_replace("/\[size=(\d+)\]/is", '', $message);
		return str_replace('[/size]', '', $message); 
	}
	
	/**
	 * ת��email��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertEmail($message) {
		return preg_replace(
			array("/\[email=([^\[]*)\]([^\[]*)\[\/email\]/is", "/\[email\]([^\[]*)\[\/email\]/is"),
			array("<a href=\"mailto:\\1 \">\\2</a>", "<a href=\"mailto:\\1 \">\\1</a>"),
			$message
		);
	}
	
	/**
	 * ת��align��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertAlign($message) {
		$message = preg_replace("/\[align=(left|center|right|justify)\]/is", '', $message);
		return str_replace('[/align]', '', $message);
	}
	
	/**
	 * ת��glow��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function convertGlow($message) {
		return preg_replace("/\[glow=(\d+)\,([0-9a-zA-Z]+?)\,(\d+)\](.+?)\[\/glow\]/is", "\\4", $message);
	}
	
	/**
	 * ת��table��ǩ
	 *
	 * @param string $message ����
	 * @param int $max Ƕ��ʱ���������㼶
	 * @return string
	 */
	public static function convertTable($message, $max = 0) {
		$t = 0;
		while (self::hasTag($message, 'table')) {
			$message = preg_replace('/\[table(?:=(\d{1,4}(?:%|px)?)(?:,(#\w{6})?)?(?:,(#\w{6})?)?(?:,(\d+))?(?:,(\d+))?(?:,(left|center|right))?)?\](?!.*(\[table))(.*?)\[\/table\]/eis', "self::_pushCode('createTable', '\\8','\\1','\\2','\\3','\\4','\\5','\\6')", $message);
			if (++$t > $max) break;
		}
		return $message;
	}
	
	/**
	 * �������� 
	 * 
	 * @param string $message
	 * @return string
	 */
	public static function parseEmotion($message) {
		$message = preg_replace("/\[s:(.+?)\]/eis","self::_pushCode('createEmotion', '\\1')", $message);
		return $message;
	}
	
	/**
	 * �������� 
	 * 
	 * @param string $message
	 * @return string
	 */
	public static function parseAttachment($message, $config) {
		preg_match_all('/\[(attachment|p_w_upload|p_w_picpath)=(\d+)\]/is', $message, $matchs);
		if ($matchs[2]) {
			$config->removeAttach($matchs[2]);
			foreach ($matchs[2] as $key => $value) {
				$message = str_replace($matchs[0][$key], self::_pushCode('createAttachment', $value, $config), $message);
			}
		}
		return $message;
		//return $message = preg_replace('/\[(attachment|p_w_upload|p_w_picpath)=(\d+)\]/eis', "self::_pushCode('createAttachment', '\\2', \$config)", $message);
	}
	
	/**
	 * ת��img��ǩ
	 *
	 * @param string $message ����
	 * @param int $maxWidth ���������
	 * @param int $maxHeight ���߶�����
	 * @return string
	 */
	public static function parseImg($message, $maxWidth = 0, $maxHeight = 0) {
		return preg_replace("/\[img\]([^\<\r\n\"']+?)\[\/img\]/eis", "self::_pushCode('createImg', '\\1', '$maxWidth', '$maxHeight')", $message);
	}
	
	/**
	 * ת��url��ǩ
	 *
	 * @param string $message ����
	 * @param int $checkurl
	 * @return string
	 */
	public static function parseUrl($message, $checkurl = 0) {
		$searcharray = array(
			"/\[url=((https?|ftp|gopher|news|telnet|mms|rtsp|thunder)?[^\[\s]+?)(\,(1)\/?)?\](.+?)\[\/url\]/eis",
			"/\[url\]((https?|ftp|gopher|news|telnet|mms|rtsp|thunder)?[^\[\s]+?)\[\/url\]/eis"
		);
		$replacearray = array(
			"self::_pushCode('createUrl', '\\1', '\\5', '\\2', '\\4', '$checkurl')",
			"self::_pushCode('createUrl', '\\1', '\\1', '\\2', '0', '$checkurl')"
		);
		return preg_replace($searcharray, $replacearray, $message);
	}
	
	/**
	 * ת��code��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function parseCode($message) {
		return preg_replace("/\[code(\sbrush\:(.+?)\;toolbar\:(true|false)\;)?\](.+?)\[\/code\]/eis", "self::_pushCode('createCode', '\\4', '\\2', '\\3')", $message);
	}
	
	/**
	 * ת��post��ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parsePost($message, $config) {
		return preg_replace("/\[post\](.+?)\[\/post\]/eis","self::_pushCode('createPost', '\\1', \$config)", $message);
	}
	
	/**
	 * ת��hide��ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parseHide($message, $config) {
		return preg_replace("/\[hide=(.+?)\](.+?)\[\/hide\]/eis","self::_pushCode('createHide', '\\1', '\\2', \$config)", $message);
	}
	
	/**
	 * ת��sell��ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parseSell($message, $config) {
		return preg_replace("/\[sell=(.+?)\](.+?)\[\/sell\]/eis", "self::_pushCode('createSell', '\\1', '\\2', \$config)", $message);
	}
	
	/**
	 * ת��quote��ǩ
	 *
	 * @param string $message ����
	 * @return string
	 */
	public static function parseQuote($message) {
		return preg_replace("/\[quote(=(.+?)\,\d+)?\](.*?)\[\/quote\]/eis","self::_pushCode('createQoute', '\\3', '\\2')", $message);
	}
	
	/**
	 * ת��flash��ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parseFlash($message, $config) {
		if ($config->isConvertFlash) {
			return preg_replace("/\[flash(=(\d+?)\,(\d+?)(\,(0|1))?)?\]([^\[\<\r\n\"']+?)\[\/flash\]/eis", "self::_pushCode('createPlayer','\\6','\\2','\\3','\\5','video')", $message);
		}
		return preg_replace("/\[flash(=(\d+?)\,(\d+?)(\,(0|1))?)?\]([^\[\<\r\n\"']+?)\[\/flash\]/eis", "self::_pushCode('createFlashLink','\\6')", $message);
	}
	
	/**
	 * ת�� wmv|mp3|rm ����Ƶý���ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parseMedia($message, $config) {
		if ($config->isConvertMedia == 2) {
			return preg_replace(
				array(
					"/\[(wmv|mp3)(=(0|1))?\]([^\<\r\n\"']+?)\[\/\\1\]/eis",
					"/\[(wmv|rm)(=([0-9]{1,3})\,([0-9]{1,3})\,(0|1))?\]([^\<\r\n\"']+?)\[\/\\1\]/eis"
				),
				array(
					"self::_pushCode('createPlayer','\\4','314','53','\\3','audio')",
					"self::_pushCode('createPlayer','\\6','\\3','\\4','\\5','video')"
				),
				$message
			);
		}
		return preg_replace(
			array(
				"/\[(mp3|wmv)(?:=[01]{1})?\]([^\<\r\n\"']+?)\[\/\\1\]/eis",
				"/\[(wmv|rm)(?:=[0-9]{1,3}\,[0-9]{1,3}\,[01]{1})?\]([^\<\r\n\"']+?)\[\/\\1\]/eis",
			),
			"self::_pushCode('createMediaLink','\\2')",
			$message
		);
	}

	public static function parseRemind($message, $remindUser) {
		return preg_replace('/@([\x7f-\xff\dA-Za-z\.\_]+)(?=\s?)/ie', "self::_pushCode('createRemind', '\\1', \$remindUser)", $message);
	}
	
	/**
	 * ת��iframe��ǩ
	 *
	 * @param string $message ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function parseIframe($message, $config) {
		return preg_replace("/\[iframe\]([^\[\<\r\n\"']+?)\[\/iframe\]/eis", "self::_pushCode('createIframe','\\1', \$config)", $message);
	}

	protected static function _init() {
		self::$_code = array();
		self::$_isSubstr = false;
		self::$_hide = false;
	}

	protected static function _pushCode() {
		$args = func_get_args();
		$length = array_push(self::$_code, $args);
		return "<\twind_code_" . ($length - 1) . "\t>";
	}
	
	/**
	 * ����������Ƿ������ǩ
	 *
	 * @param string $message ����
	 * @param string $tag ��ǩ
	 * @return bool
	 */
	public static function hasTag($message, $tag) {
		$startTag = '[' . $tag;
		$endTag = '[/' . $tag . ']';
		if (strpos($message, $startTag) !== false && strpos($message, $endTag) !== false) {
			return true;
		}
		return false;
	}
	
	/**
	 * ת��ubb��ǩ
	 *
	 * @param string $message
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function convert($message, $length, PwUbbCodeConvertConfig $config = null) {
		is_null($config) && $config = new PwUbbCodeConvertConfig();
		self::_init();
		self::hasTag($message, 'code') && $message = self::parseCode($message);
		$message = self::convertTag($message, array('u', 'b', 'i', 'sub', 'sup', 'strike', 'blockquote'));
		$message = self::convertHr($message);
		$message = self::convertList($message);
		$message = self::convertFont($message);
		$message = self::convertColor($message);
		$message = self::convertBackColor($message);
		$message = self::convertSize($message);
		$message = self::convertEmail($message);
		$message = self::convertAlign($message);
		$message = self::convertGlow($message);
		
		strpos($message, '[s:') !== false && $message = self::parseEmotion($message);
		$message = self::parseAttachment($message, $config);
		self::hasTag($message, 'img') && $message = self::parseImg($message, 700, 700);
		self::hasTag($message, 'url') && $message = self::parseUrl($message);
		self::hasTag($message, 'flash') && $message = self::parseFlash($message, $config);
		$config->remindUser && $message = self::parseRemind($message, $config->remindUser);
		$config->isConvertMedia && $message = self::parseMedia($message, $config);
		$config->isConvertIframe && self::hasTag($message, 'iframe') && $message = self::parseIframe($message, $config);
		$config->isConvertPost && self::hasTag($message, 'post') && $message = self::parsePost($message, $config);
		$config->isConvertHide && self::hasTag($message, 'hide') && $message = self::parseHide($message, $config);
		$config->isConvertSell && self::hasTag($message, 'sell') && $message = self::parseSell($message, $config);
		self::hasTag($message, 'quote') && $message = self::parseQuote($message);
		$config->isConvertTable && $message = self::convertTable($message, $config->isConvertTable);
		$message = self::convertParagraph($message);
		list($message) = self::_subConvert($message, $length);

		return $message;
	}

	public static function isSubstr() {
		return self::$_isSubstr || self::$_hide;
	}

	protected static function _subConvert($message, $maxlen) {
		$str = '';
		$length = 0;
		$array = preg_split('/<\twind_code_(\d+)\t>/is', $message, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($array as $key => $value) {
			if ($key % 2 == 0) {
				list($value, $strlen) = self::_substrs($value, $maxlen);
			} else {
				$args = self::$_code[$value];
				$method = array_shift($args);
				array_unshift($args, $maxlen);
				list($value, $strlen) = call_user_func_array(array(self, $method), $args);
			}
			$str .= $value;
			$maxlen -= $strlen;
			$length += $strlen;
			if ($maxlen <= 0 || self::$_isSubstr) break;
		}
		return array($str, $length);
	}

	protected static function _substrs($message, $length) {
		$strlen = Pw::strlen($message);
		if ($strlen > $length) {
			$message = Pw::substrs($message, $length);
			$strlen = $length;
			self::$_isSubstr = true;
		}
		return array($message, $strlen);
	}
	
	/**
	 * ���ɱ���html��ǩ
	 *
	 * @param int $key �������
	 * @return string ����html
	 */
	public static function createEmotion($length, $key) {
		is_null(self::$_emotion) && self::$_emotion = Wekit::cache()->get('all_emotions');
		isset(self::$_emotion['name'][$key]) && $key = self::$_emotion['name'][$key];
		$emotion = isset(self::$_emotion['emotion'][$key]) ? self::$_emotion['emotion'][$key] : current(self::$_emotion['emotion']);
		$html = "<img src=\"" . Wekit::url()->images . "/emotion/" . $emotion['emotion_folder'] . '/' . $emotion['emotion_icon'] . "\" />";
		return array($html, 1);
	}
	
	/**
	 * ���ɸ���html��ǩ
	 *
	 * @param int $aid ����id
	 * @return string ����html
	 */
	public static function createAttachment($length, $aid, $config) {
		return array($config->getAttachHtml($aid), 4);
	}
	
	/**
	 * ����img��ǩ
	 *
	 * @param string $path ͼƬ��ַ
	 * @param int $maxWidth ���������
	 * @param int $maxHeight ���߶�����
	 * @param string $original ԭͼ��ַ
	 * @return string ͼƬhtml
	 */
	public static function createImg($length, $path, $maxWidth = 0, $maxHeight = 0, $original = '') {
		return self::_substrs('[ͼƬ]', $length);
	}

	/**
	 * ����a��ǩ
	 *
	 * @param string $url ���ӵ�ַ
	 * @param string $name ��������
	 * @param string $protocol ����Э��ͷ
	 * @param int $isdownload �����Ƿ�Ϊ������ʽ
	 * @param int $checkurl
	 * @return string
	 */
	public static function createUrl($length, $url, $name, $protocol, $isdownload = 0, $checkurl = 0) {
		list($name, $strlen) = self::_subConvert($name, $length);
		!$protocol && $url = 'http://' . $url;
		$attributes = '';
		$isdownload && $attributes .= ' class="down"';
		$html = "<a href=\"$url\" target=\"_blank\"{$attributes}>$name</a>";
		return array($html, $strlen);
	}
	
	/**
	 * ����code��ǩ����
	 *
	 * @param string $str ����
	 * @param string $brush �����﷨
	 * @param string $toolbar �Ƿ��й�����
	 * @return string
	 */
	public static function createCode($length, $str, $brush, $toolbar) {
		$str = str_replace(array('&amp;lt;', '&amp;gt;'), array('&lt;', '&gt;'), $str);
		return self::_substrs($str, $length);
	}
	
	/**
	 * ����post��ǩ����
	 * 
	 * @param stirng $str ����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function createPost($length, $str, $config) {
		self::$_hide = true;
		return array('<span>[�˴����ݻظ���ɼ�]</span>', 9);
	}
	
	/**
	 * ����hide��ǩ����
	 *
	 * @param int $cost ��Ҫ�Ļ���
	 * @param stirng $str ���ص�����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function createHide($length, $cost, $str, $config) {
		self::$_hide = true;
		return array('<span>[�˴����ݼ���]</span>', 6);
	}
	
	/**
	 * ����sell��ǩ����
	 *
	 * @param int $cost ��Ҫ�Ļ���
	 * @param stirng $str ���ص�����
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function createSell($length, $cost, $str, $config) {
		self::$_hide = true;
		list($cost, $credit) = explode(',', $cost);
		$creditBo = PwCreditBo::getInstance();
		$cname = isset($creditBo->cType[$credit]) ? $creditBo->cType[$credit] : current($creditBo->cType);
		return array('<span>[���������ۼ� ' . $cost . ' ' . $cname . '���������ʾ����]</span>', 16);
	}
	
	/**
	 * ����quote��ǩ����
	 *
	 * @param stirng $str ���ص�����
	 * @return string
	 */
	public static function createQoute($length, $str, $username) {
		if ($username) return self::_substrs('', $length);
		return self::_subConvert($str, $length);
	}
	
	/**
	 * ���ɲ�����
	 *
	 * @param stirng $url url��ַ
	 * @param int $width ���
	 * @param int $height �߶�
	 * @param int $auto �Ƿ�Ϊ�Զ�����<1.�� 2.��>
	 * @param string $type ���������� <��ѡ: audio|video>
	 * @return string
	 */
	public static function createPlayer($length, $url, $width = 0, $height = 0, $auto = 0, $type = 'video') {
		return self::_substrs($type == 'audio' ? '[����]' : '[��Ƶ]', $length);
	}
	
	/**
	 * ���� flash ����
	 *
	 * @param string $url
	 * @return string
	 */
	public static function createFlashLink($length, $url) {
		return self::_substrs('[��Ƶ]', $length);
	}
	
	/**
	 * ������Ƶ����
	 *
	 * @param string $url
	 * @return string
	 */
	public static function createMediaLink($length, $url) {
		return self::_substrs('[��Ƶ]', $length);
	}
	
	public static function createRemind($length, $username, $uArray) {
		list($html, $strlen) = self::_substrs('@' . $username, $length);
		isset($uArray[$username]) && $html = '<a href="' . WindUrlHelper::createUrl('space/index/run', array('uid' => $uArray[$username])) . '">@' . $username . '</a>';
		return array($html, $strlen);
	}

	/**
	 * ����iframe��ǩ����
	 *
	 * @param string $url
	 * @param object $config ubbת������
	 * @return string
	 */
	public static function createIframe($length, $url, $config) {
		list($name, $strlen) = self::_substrs($url, $length);
		return array("<a target=\"_blank\" href=\"$url \">$name</a>", $strlen);
	}
	
	/**
	 * ����table��ǩ����
	 *
	 * @param string $text
	 * @param int $width ���
	 * @param string $bgColor ����ɫ
	 * @param string $borderColor �߿�ɫ
	 * @param int $borderWidth �߿��С
	 * @return string
	 */
	public static function createTable($length, $text, $width = '', $bgColor = '', $borderColor = '', $borderWidth = '', $align = '') {
		return self::_substrs('[���]', $length);
		//����ʾ�������
		$text = trim(str_replace(array('\\"', '<br />'), array('"', "\n"), $text));
		$text = preg_replace(
			array('/(\[\/td\]\s*)?\[\/tr\]\s*/is', '/\[(tr|\/td)\]\s*\[td(=(\d{1,2}),(\d{1,2})(,(\d{1,3}(\.\d{1,2})?(%|px)?))?)?\]/eis'),
			array('<br />', "self::createTd('\\1','\\3','\\4','\\6','$tdStyle')","<tr><td{$tdStyle}>"),
			$text
		);
		$text = str_replace('[tr]', '', $text);
		$text = str_replace("\n", '<br />', $text);

		return self::_substrs($text, $length);
	}
	
	/**
	 * ����td��ǩ
	 *
	 * @param string $tag ��ǩ <tr|td>
	 * @param int $col ����
	 * @param int $row ����
	 * @param int $width ���
	 * @param string $tdStyle ��ʽ
	 * @return string
	 */
	public static function createTd($tag, $col, $row, $width, $tdStyle = '') {
		return $tag == 'tr' ? '' : ' ';
	}
}