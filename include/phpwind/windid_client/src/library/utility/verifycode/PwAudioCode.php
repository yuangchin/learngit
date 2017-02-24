<?php
Wind::import('LIB:utility.verifycode.PwBaseCode');
 /**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ foxsee@aliyun.com
 * @version $Id: PwAudioCode.php 20894 2012-11-16 07:07:59Z jieyin $ 
 * @package 
 */
class PwAudioCode extends PwBaseCode{
	
	private static $_audioPath = '';
	
	private static $_audioVerify = '';
	
	public static function init() {
		self::setRandCode();
		self::_setAudioPath();
		self::_getMP3Audio();
		self::_setRandGraph();
	}
	
	public static function outAudio() {
		header("Pragma:no-cache");
		header("Cache-control:no-cache");
		header("Content-type: audio/mpeg");
		header('Content-Length: ' . strlen(self::$_audioVerify));
		echo self::$_audioVerify;
		exit;
	}
	
	private static function _setAudioPath() {
		self::$_audioPath = Wind::getRealDir(self::$path.'audio');
	}
	
	private static function _setRandGraph() {
		$startpos = 4;	//û��ID3V2��ǩ
		if (stripos($audioData, 'ID3') !== false) {
			$startpos = 24; //ID3V2ͷ��֡
			($pos = stripos(self::$_audioVerify, '3DI')) !== false && $startpos = $pos + 14; //��׼ȷ�Ļ�ȡ��Ƶ����
		}
		$dataLength = strlen(self::$_audioVerify) - $startpos - 128;	//ĩ128���ֽ���MP3��ʽ��ID3V1��ǩ
		for ($i = $startpos; $i < $dataLength; $i += 32) {
			$ord = ord(self::$_audioVerify[$i]);
			if ($ord < 17 || $ord > 111) continue;
			self::$_audioVerify[$i] = chr($ord);//chr($ord + rand(-1, 1));����
		}
		return self::$_audioVerify;
	}
	
	
	private static function _getMP3Audio() {
		self::$_audioVerify ='';
		$_len = Pw::strlen(self::$verifyCode);
	 	for($i = 0;$i < $_len;$i++){
	 		$_code = strtoupper(self::$verifyCode[$i]); 
	       	self::$_audioVerify .= WindFile::read(self::$_audioPath.'/'.$_code.'.mp3', WindFile::READ);
	    }
	}
}
?>