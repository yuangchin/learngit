<?php

defined('WINDID_VERSION') || exit('Forbidden');

Wind::import('WINDID:library.image.WindidImage');

/**
 * �ϴ����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: WindidUpload.php 22500 2012-12-25 03:54:47Z gao.wanggao $
 * @package upload
 */

class WindidUpload {

	protected $bhv;		// �ϴ���Ϊ����
	protected $store;	// �����洢��

	public function __construct(WindidUploadAction $bhv) {
		$this->bhv = $bhv;
		$this->setStore();
	}

	public function getBehavior() {
		return $this->bhv;
	}
	
	/**
	 * ����Ƿ�����ϴ�
	 *
	 * @return bool|PwError
	 */
	public function check() {
		return $this->bhv->check();
	}
	
	/**
	 * ��ȡ���ϴ���������
	 *
	 * @return int
	 */
	public function getUploadNum() {
		return $this->bhv->getUploadNum();
	}
	
	/**
	 * ����ϴ��ļ��Ƿ���Ϲ涨
	 *
	 * @param PwUploadFile $file
	 * @return bool|PwError
	 */
	public function checkFile($file) {
		if (!$file->ext || !isset($this->bhv->ftype[$file->ext])) {
			return new WindidError(WindidError::UPLOAD_EXT_ERROR);
		}
		if ($file->size < 1) {
			return new WindidError(WindidError::UPLOAD_SIZE_LESS);
		}
		if ($file->size > $this->bhv->ftype[$file->ext] * 1024) {
			return new WindidError(WindidError::UPLOAD_SIZE_OVER);
		}
		return true;
	}
	
	/**
	 * ���ø����洢����
	 */
	public function setStore() {
		Wind::import('WINDID:service.config.srv.WindidStoreService');
		$srv = new WindidStoreService();
		$this->store = $srv->getStore();
	}
	
	/**
	 * �����ļ���
	 *
	 * @param string $filename
	 * @return string
	 */
	public function filterFileName($filename) {
		return preg_replace('/\.(php|asp|jsp|cgi|fcgi|exe|pl|phtml|dll|asa|com|scr|inf)$/i', ".scp_\\1" , $filename);
	}
	
	/**
	 * �ϴ�����������
	 *
	 * @return mixed
	 */
	public function execute() {
		$uploaddb = array();
		foreach ($_FILES as $key => $value) {
			if (!self::isUploadedFile($value['tmp_name']) || !$this->bhv->allowType($key)) {
				continue;
			}
			$file = new WindidUploadFile($key, $value);
			if (($result = $this->checkFile($file)) !== true) {
				return $result;
			}
			$file->filename = $this->filterFileName($this->bhv->getSaveName($file));
			$file->savedir = $this->bhv->getSaveDir($file);
			$file->source = $this->store->getAbsolutePath($file->filename, $file->savedir);
			
			if (!self::moveUploadedFile($value['tmp_name'], $file->source)) {
				return new WindidError(WindidError::UPLOAD_FAIL);
			}
			if (($result = $file->operate($this->bhv, $this->store)) !== true) {
				return $result;
			}
			if (($result = $this->saveFile($file)) !== true) {
				return $result;
			}
			$uploaddb[] = $file->getInfo();
		}
		return $this->bhv->update($uploaddb);
	}
	
	/**
	 * �����ļ�
	 *
	 * @param PwUploadFile $file
	 * @return bool|PwError
	 */
	public function saveFile($file) {
		if (($result = $this->store->save($file->source, $file->fileuploadurl)) !== true) {
			return $result;
		}
		if ($thumb = $file->getThumb()) {
			foreach ($thumb as $key => $value) {
				$this->store->save($value[0], $value[1]);
			}
		}
		return true;
	}
	
	/**
	 * ͳ�ƴ��ϴ���������
	 *
	 * @return int
	 */
	public static function countUploadedFile() {
		$i = 0;
		foreach ($_FILES as $key => $value) {
			if (self::isUploadedFile($value['tmp_name'])) $i++;
		}
		return $i;
	}
	
	/**
	 * �ж��Ƿ����������ϴ��ļ�
	 *
	 * @param string $tmp_name
	 * @return bool
	 */
	public static function isUploadedFile($tmp_name) {
		if (!$tmp_name || $tmp_name == 'none') {
			return false;
		} elseif (function_exists('is_uploaded_file') && !is_uploaded_file($tmp_name) && !is_uploaded_file(str_replace('\\\\', '\\', $tmp_name))) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * �ƶ��ϴ��ļ�
	 *
	 * @param string $tmp_name Դ�ļ�
	 * @param string $filename �ƶ�����ļ���ַ
	 * @return bool
	 */
	public static function moveUploadedFile($tmp_name, $filename) {
		if (strpos($filename, '..') !== false || strpos($filename, '.php.') !== false || preg_match("/\.php$/i", $filename)) {
			exit('illegal file type!');
		}
		self::createFolder(dirname($filename));
		if (function_exists("move_uploaded_file") && @move_uploaded_file($tmp_name, $filename)) {
			@chmod($filename, 0777);
			return true;
		}
		if (self::copyFile($tmp_name, $filename)) {
			return true;
		}
		return false;
	}
	
	/**
	 * �����ļ�
	 *
	 * @param string $srcfile Դ�ļ�
	 * @param string $dstfile Ŀ���ļ���ַ
	 * @return bool
	 */
	public static function copyFile($srcfile, $dstfile) {
		if (@copy($srcfile, $dstfile)) {
			@chmod($dstfile, 0777);
			return true;
		}
		if (is_readable($srcfile)) {
			file_put_contents($dstfile, file_get_contents($srcfile));
			if (file_exists($dstfile)) {
				@chmod($dstfile, 0777);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * ����Ŀ¼
	 *
	 * @param string $path
	 */
	public static function createFolder($path) {
		if (!is_dir($path)) {
			self::createFolder(dirname($path));
			@mkdir($path);
			@chmod($path, 0777);
			@fclose(@fopen($path . '/index.html', 'w'));
			@chmod($path . '/index.html', 0777);
		}
	}

	public function __call($methodName, $args) {
		if (!method_exists($this->bhv, $methodName)) {
			return false;
		}
		$method = new ReflectionMethod($this->bhv, $methodName);
		if ($method->isPublic()) {
			return call_user_func_array(array(&$this->bhv, $methodName), $args);
		}
		return false;
	}
}


/**
 * �ϴ��ļ�����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @package upload
 */
class WindidUploadFile {

	public $key;
	public $id;
	public $attname;
	public $name;
	public $size;
	public $type = 'zip';
	public $ifthumb = 0;
	public $filename;
	public $savedir;
	public $fileuploadurl = '';
	public $ext;
	
	protected $_thumb = array();

	public function __construct($key, $value) {
		list($t, $i) = explode('_', $key);
		$this->id = intval($i);
		$this->attname = $t;
		$this->name = $value['name'];
		$this->size = intval($value['size']);
		$this->ext  = strtolower(substr(strrchr($this->name, '.'), 1));
	}

	public function getInfo() {
		return array(
			'id' => $this->id,
			'attname' => $this->attname,
			'name' => $this->name,
			'size' => $this->size,
			'type' => $this->type,
			'ifthumb' => $this->ifthumb,
			'fileuploadurl' => $this->fileuploadurl,
			'ext' => $this->ext
		);
	}
	
	/**
	 * �Ƿ���ͼƬ
	 *
	 * @return bool
	 */
	public function isImage() {
		return in_array($this->ext, array('gif','jpg','jpeg','png','bmp','swf'));
	}
	
	/**
	 * �Ƿ����ı�
	 *
	 * @return bool
	 */
	public function isTxt() {
		return $this->ext == 'txt';
	}
	
	/**
	 * ��������
	 *
	 * @param PwUploadAction $bhv �ϴ���Ϊ
	 * @param object �洢����
	 * @return bool|PwError
	 */
	public function operate($bhv, $store) {
		$this->size = ceil(filesize($this->source) / 1024);
		$this->fileuploadurl = $this->savedir . $this->filename;

		if ($this->isImage()) {
			return $this->operateImage($bhv, $store);
		}
		if ($this->isTxt()) {
			return $this->operateTxt();
		}
		return true;
	}
	
	/**
	 * ͼƬ����
	 *
	 * @param PwUploadAction $bhv �ϴ���Ϊ
	 * @param object �洢����
	 * @return bool|PwError
	 */
	public function operateImage($bhv, $store) {
		$image = new WindidImage($this->source);
		if (!$image->isImage()) {
			return new WindidError(WindidError::UPLOAD_CONTENT_ERROR);
		}
		if ($image->ext != 'swf') {
			if (!$image->getSource() && $image->ext != 'bmp') {
				return new WindidError(WindidError::UPLOAD_CONTENT_ERROR);
			}
			if ($bhv->allowThumb()/* && $upload['ext'] != 'gif'*/) {
				$this->makeThumb($image, $bhv->getThumbInfo($this->filename, $this->savedir), $store);
			}
			/*if ($bhv->allowWaterMark()) {
				$waterinfo = $bhv->getWaterMarkInfo();
				$this->watermark($image, $waterinfo);
				foreach ($this->_thumb as $value) {
					$this->watermark(new WindidImage($value[0]), $waterinfo);
				}
			}*/
			$this->type = 'img';
		}
		return true;
	}
	
	/**
	 * �ı�����
	 *
	 * @return bool|PwError
	 */
	public function operateTxt() {
		/*
		if (preg_match('/(onload|submit|post|form)/i', readover($source))) {
			P_unlink($source);
			showUploadMsg('upload_content_error');
		}*/
		$this->type = 'txt';
		return true;
	}
	
	/**
	 * ��������ͼ
	 *
	 * @param PwImage $image ͼƬ����
	 * @param array $thumbInfo ����ͼ����
	 * @param object �洢����
	 */
	public function makeThumb(WindidImage $image, $thumbInfo, $store) {
		$quality = 80;
		foreach ($thumbInfo as $key => $value) {
			$thumburl = $store->getAbsolutePath($value[0], $value[1]);
			WindidUpload::createFolder(dirname($thumburl));
			$result = $image->makeThumb($thumburl, $value[2], $value[3], $quality, $value[4], $value[5]);
			if ($result === true && $image->filename != $thumburl) {
				$this->ifthumb |= (1 << $key);
				$this->_thumb[] = array($thumburl, $value[1] . $value[0]);
			}
		}
	}

	public function getThumb() {
		return $this->_thumb;
	}
	
	/**
	 * ͼƬ����ˮӡ
	 *
	 * @param PwImage $image ͼƬ����
	 * @param array $options ���ɷ�������
	 */
	/*public static function watermark(WindidImage $image, $options = array()) {
		if (!in_array($image->type, array('gif', 'jpeg', 'png'))) return;
		$config = Windid::C('attachment');
		if ($options) {
			foreach ($options as $key => $value) {
				$config['mark.' . $key] = $value;
			}
		}
		if ($image->type == 'gif' && !$config['mark.gif']) return;
		if ($image->width < $config['mark.limitwidth'] || $image->height < $config['mark.limitheight']) return;
		
		Wind::import('LIB:image.PwImageWatermark');
		$watermark = new WindidImageWatermark($image);
		$watermark->setPosition($config['mark.position'])
			->setType($config['mark.type'])
			->setTransparency($config['mark.transparency'])
			->setQuality($config['mark.quality']);

		if ($config['mark.type'] == 1) {
			$watermark->setFile($config['mark.file']);
		} else {
			$watermark->setText($config['mark.text'])
				->setFontfamily($config['mark.fontfamily'])
				->setFontsize($config['mark.fontsize'])
				->setFontcolor($config['mark.fontcolor']);
		}
		$watermark->execute();
	}*/
}
?>