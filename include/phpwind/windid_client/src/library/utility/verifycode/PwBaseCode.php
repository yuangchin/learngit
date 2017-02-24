<?php
 /**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ foxsee@aliyun.com
 * @version $Id: PwBaseCode.php 20486 2012-10-30 07:50:14Z gao.wanggao $ 
 * @package 
 */
 class PwBaseCode {
	/**
	 * ��֤�볤��
	 * 
	 * @var int
	 */
	public static $verifyLength = 4;
	
	/**
	 * 1.���� 2.��ĸ 3.����+��ĸ 4,����Ӽ���5.����6.�Զ������� 7����
	 * 
	 * @var int
	 */
	public static $verifyType = 3;
	
	public static $verifyWidth = 240;
	
	public static $verifyHeight = 60;
	
	public static $isRandBackground = false;
	
	public static $isRandGraph = false;
	
	public static $isRandFont = false;
	
	public static $isRandSize = false;
	
	public static $isRandAngle = false;
	
	public static $isRandColor = false;
	
	public static $isRandGif = false;
	
	public static $isRandDistortion = false;
	
	public static $askCode = '';
	
	public static $answerCode = '';
	
	protected static $verifyCode = '';
	
	protected static $path = 'REP:';
	
	public static function getCode() {
		if (in_array(self::$verifyType, array(4,6))) return self::$answerCode;
		return strtolower(self::$verifyCode);
	}
	
	/**
	 * ������֤��
	 * 
	 * @return void
	 */
	protected static function setRandCode() {
		switch (self::$verifyType) {
			case '1':
		   		$str = '1234567890';
		    	break;
		    case '2':
		   		$str = 'abcdefghjkmnpqrstuvwxyABCDEFGHJKLMNPQRSTUVWXY';
		    	break;
		    case '3':
		    default:
		   		$str = '3456789bcefghjkmpqrtvwxyzBCEFGHJKMPQRTVWXYZ';
		    	break;
		    case '5':
		   		$str = '��֮���Ա��������ϰ��Զ����������Ǩ��֮������ר����ĸ���ڴ��Ӳ�ѧ�ϻ������ɽ���巽�������������������ʽ���ֵ��̬���ױ�������������̨���û������ܺ���ݺ����ʼ��������Ͼ��ݼ���ҳ�����Կ�Ӣ��ƻ���Լ�Ͳ�ʡ���������ӵ۽�����ֲ������������ץ���縱����̸Χʳ��Դ�������ȴ����̻����������׳߲��зۼ������濼�̿�������ʧ��ס��֦�־����ܻ���ʦ������Ԫ����ɰ�⻻̫ģƶ�����ｭ��Ķľ����ҽУ���ص�����Ψ�们վ�����ֹĸ�д��΢�Է�������ĳ�����������൹�������ù�Զ���Ƥ����ռ����Ȧΰ��ѵ�ؼ��ҽ��ƻ���������ĸ�����ֶ���˫��������ʴ����˿Ůɢ��������Ժ�䳹����ɢ�����������������Ѫ��ȱ��ò�����ǳ���������������̴���������������Ͷ��ū����ǻӾഥ�����ͻ��˶��ٻ����δͻ�ܿ���ʪƫ�Ƴ�ִ����կ�����ȶ�Ӳ��Ŭ�����Ԥְ������Э�����ֻ���ì������ٸ�������������ͣ����Ӫ�ո���Ǯ��������ɳ�˳��ַ�е�ذ����İ��������۵��յ���ѽ�ʰɿ��ֽ�������������ĩ�����ӡ�伱�����˷�¶��Ե�������������Ѹ��������ֽҹ������׼�����ӳ��������ɱ���׼辧�尣ȼ��������ѿ��������̼��������ѿ����б��ŷ��˳������͸˾Σ������Ц��β��׳����������������ţ��Ⱦ�����������Ƽ�ֳ�����ݷô���ͭ��������ٺ������';
		   	 	break;
		   	case '4':
		    case '6':
		    	self::$verifyCode = self::_convert(self::$askCode);
		   		return true;
		    case '7': //Ŀǰֻ����Щ�����ļ�
		    	$str = '123456789BCEFGHJKMPQRTVWXYZ';
		    	break;		
		}
	  	$_tmp = Pw::strlen($str)-1;
	    $_num=0;
	    for($i = 0;$i < self::$verifyLength;$i++){
	        $_num = mt_rand(0, $_tmp);
	        $_code = Pw::substrs($str, 1,$_num, false); 
	        self::$verifyCode .=  self::_convert($_code);
	    }
	}
	
 	private static function _convert($text='') {
 		return Pw::convert($text, 'UTF-8');
		/*if ($text !== utf8_decode(utf8_encode($text))) {
			$text = WindConvert::convert($text, 'UTF-8', 'GBK');
		}
		return $text;*/
	}
	
 	/**
	 * ��ȡ��֤�뱳���ļ�
	 *
	 * @return array
	 */
	protected static function getVerifyBackground() {
		$_files = array();
		$_path = Wind::getRealDir(self::$path.'.bg.');
		$files = WindFolder::read($_path);
		foreach ($files AS $file) {
			if (is_file($_path .$file)) $_files[] = $_path .$file;
		}
		return $_files;
	}
	
	/**
	 * ��ȡ�����б�
	 *
	 * @return array
	 */
	protected static function getFontList() {
		$_path=Wind::getRealDir(self::$path.'.font');
		return WindFolder::read($_path, WindFolder::READ_FILE);
	}
	
	/**
	 * ��ȡӢ�������б�
	 *
	 * @return array
	 */
	protected static function getEnFontList() {
		$_fontList = array();
		$fontList = self::getFontList();
		foreach ($fontList AS $key=>$font) {
			if (strpos($font, 'en_')===0) {
				$_fontList[] = $font;
			}
		}
		return $_fontList ?  $_fontList : array('en_arial.ttf');
	}
	
	/**
	 * ��ȡ���������б�
	 *
	 * @return array
	 */
	protected static function getCnFontList() {
		$_fontList =array();
		$fontList = self::getFontList();
		foreach ($fontList AS $key=>$font) {
			if (strpos($font, 'cn_')===0) {
				$_fontList[] = $font;
			}
		}
		return $_fontList;
	}
	
 }
?>