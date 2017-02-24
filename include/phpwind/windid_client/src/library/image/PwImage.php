<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:image.PwImageThumb');

/**
 * image ����
 *
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author Jianmin Chen <sky_hold@163.com>
 * @version $Id: PwImage.php 22380 2012-12-21 14:54:07Z jieyin $
 * @package lib.image
 */

class PwImage {
	
	public $filename;	//�ļ���ַ
	public $ext;		//��׺��
	public $width;		//�ļ����
	public $height;		//�ļ��߶�
	public $type;		//�ļ�����
	
	protected $_source = null;
	protected $_thumb;
	protected $_exts = array('jpg', 'jpeg', 'jpe', 'jfif');

	public function __construct($filename) {
		$this->filename = $filename;
		$this->ext = $this->getExt($filename);
		$this->parse();
	}
	
	/**
	 * ����ͼƬ
	 *
	 * return void
	 */
	public function parse() {
		/*
		if (function_exists('read_exif_data') && in_array($this->ext, $this->_exts)) {
			$datatemp = @read_exif_data($this->filename);
			$this->width = $datatemp['COMPUTED']['Width'];
			$this->height = $datatemp['COMPUTED']['Height'];
			$this->type = 2;
		}
		if (!$this->width) {
			list($this->width, $this->height, $this->type) = @getimagesize($this->filename);
		}*/
		list($this->width, $this->height, $this->type) = @getimagesize($this->filename);
		$typeMap = array(
			1 => 'gif',
			2 => 'jpeg',
			3 => 'png',
			6 => 'bmp'
		);
		$this->type = isset($typeMap[$this->type]) ? $typeMap[$this->type] : '';
	}
	
	/**
	 * �ж��Ƿ�Ϊ������ͼ��
	 *
	 * return bool
	 */
	public function isImage() {
		return empty($this->type) ? false : true;
	}
	
	/**
	 * ��ȡ��ͼ��ı�ʶ��
	 *
	 * return resource
	 */
	public function getSource() {
		if ($this->_source === null) {
			if (!$this->type || !function_exists('imagecreatefrom' . $this->type)) {
				$this->_source = false;
			} else {
				$imagecreatefromtype = 'imagecreatefrom' . $this->type;
				$this->_source = $imagecreatefromtype($this->filename);
			}
		}
		return $this->_source;
	}
	
	/**
	 * ��ȡ�ļ���׺
	 *
	 * @param string $filename �ļ���
	 * return string
	 */
	public function getExt($filename) {
		return strtolower(substr(strrchr($filename, '.'), 1));
	}
	
	/**
	 * ���»���ͼƬ(��ֹ�Ƿ�ͼƬ��ɹ���)
	 */
	public function repaint() {
		if (!$source = $this->getSource()) return false;
		$imagefun = 'image' . $this->type;
		if (!function_exists($imagefun)) return false;
		if ($this->type == 'jpeg') {
			return call_user_func($imagefun, $source, $this->filename, 100);
		} else {
			return call_user_func($imagefun, $source, $this->filename);
		}
	}

	/**
	 * ��������ͼ
	 *
	 * @param string $thumbUrl ����ͼ��ַ
	 * @param int $thumbWidth ���
	 * @param int $thumbHeight �߶�
	 * @param int $quality ͼƬ����
	 * @param int $thumbType ����ͼ���ɷ�ʽ <1.�ȱ����� 2.���н�ȡ 3.�ȱ����>
	 * @param int $forceMode ǿ������ <0.���ļ��ߴ�С������Ҫ��ʱ�������� 1.������>
	 * return mixed
	 */
	public function makeThumb($thumbUrl, $thumbWidth, $thumbHeight, $quality = 0, $thumbType = 0, $forceMode = 0) {
		$this->_thumb = new PwImageThumb($this);
		$this->_thumb->setDstFile($thumbUrl);
		$this->_thumb->setWidth($thumbWidth);
		$this->_thumb->setHeight($thumbHeight);
		$this->_thumb->setQuality($quality);
		$this->_thumb->setType($thumbType);
		$this->_thumb->setForceMode($forceMode);
		$result = $this->_thumb->execute();
		return $result;
	}

	public function getThumb() {
		return $this->_thumb;
	}
}