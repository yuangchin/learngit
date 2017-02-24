<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:image.PwImage');

/**
 * �ϴ��ļ�����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @package upload
 */
class PwUploadFile {

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
			'ext' => $this->ext,
			'thumb' => $this->_thumb
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
		$image = new PwImage($this->source);
		if (!$image->isImage()) {
			return new PwError('upload.content.error');
		}
		if ($image->ext != 'swf') {
			if (!$image->getSource() && $image->ext != 'bmp') {
				return new PwError('upload.content.error');
			}
			if ($bhv->allowThumb()/* && $upload['ext'] != 'gif'*/) {
				$this->makeThumb($image, $bhv->getThumbInfo($this->filename, $this->savedir), $store);
			}
			if ($bhv->allowWaterMark()) {
				$waterinfo = $bhv->getWaterMarkInfo();
				$this->watermark($image, $waterinfo);
				foreach ($this->_thumb as $value) {
					$this->watermark(new PwImage($value[0]), $waterinfo);
				}
			}
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
	public function makeThumb(PwImage $image, $thumbInfo, $store) {
		$quality = Wekit::C('attachment', 'thumb.quality');
		foreach ($thumbInfo as $key => $value) {
			$thumburl = $store->getAbsolutePath($value[0], $value[1]);
			PwUpload::createFolder(dirname($thumburl));
			$result = $image->makeThumb($thumburl, $value[2], $value[3], $quality, $value[4], $value[5]);
			if ($result === true && $image->filename != $thumburl) {
				$ts = $image->getThumb();
				$this->ifthumb |= (1 << $key);
				$this->_thumb[$key] = array($thumburl, $value[1] . $value[0], $ts->getThumbWidth(), $ts->getThumbHeight());
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
	public static function watermark(PwImage $image, $options = array()) {
		if (!in_array($image->type, array('gif', 'jpeg', 'png'))) return;
		$config = Wekit::C('attachment');
		if ($options) {
			foreach ($options as $key => $value) {
				$config['mark.' . $key] = $value;
			}
		}
		if ($image->type == 'gif' && !$config['mark.gif']) return;
		if ($image->width < $config['mark.limitwidth'] || $image->height < $config['mark.limitheight']) return;
		
		Wind::import('LIB:image.PwImageWatermark');
		$watermark = new PwImageWatermark($image);
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
	}
}
?>