<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * ����ͼ���ɷ�ʽ
 *
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author Jianmin Chen <sky_hold@163.com>
 * @version $Id: PwImageThumb.php 22380 2012-12-21 14:54:07Z jieyin $
 * @package lib.image
 */

class PwImageThumb {
	
	const TYPE_INTACT = 1; //�ȱ�����
	const TYPE_CENTER = 2; //���н�ȡ
	const TYPE_DENGBI = 3; //�ȱ����

	protected $image;

	protected $dstfile;
	protected $width;
	protected $height;
	protected $type;
	protected $quality = 90;
	protected $forcemode = 0;

	protected $thumbWidth;
	protected $thumbHeight;
	
	protected $imageCreateFunc;
	protected $imageCopyFunc;
	protected $imageFunc;

	public function __construct(PwImage $image) {
		$this->image = $image;
	}
	
	/**
	 * ��������ͼĿ���ַ
	 */
	public function setDstFile($dstfile) {
		$this->dstfile = $dstfile;
	}
	
	/**
	 * ���ÿ��
	 */
	public function setWidth($width) {
		$this->width = intval($width);
	}
	
	/**
	 * ���ø߶�
	 */
	public function setHeight($height) {
		$this->height = intval($height);
	}
	
	/**
	 * �������Է�ʽ <1.�ȱ����� 2.���н�ȡ 3.�ȱ����>
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * ����ͼƬ����
	 */
	public function setQuality($quality) {
		$quality > 0 && $this->quality = $quality;
	}
	
	/**
	 * �Ƿ�����ǿ��ģʽ <0.���ļ��ߴ�С������Ҫ��ʱ�������� 1.������>
	 */
	public function setForceMode($forcemode) {
		$this->forcemode = $forcemode;
	}
	
	/**
	 * ��������ͼ
	 */
	public function execute() {
		if (!$this->dstfile) {
			return -1;
		}
		if ($this->width <= 0 && $this->height <= 0) {
			return -2;
		}
		if (!$this->checkEnv()) {
			return -3;
		}
		if (($compute = $this->compute()) === false) {
			return -4;
		}
		$this->thumbWidth = $compute->canvasW;
		$this->thumbHeight = $compute->canvasH;

		$thumb = call_user_func($this->imageCreateFunc, $compute->canvasW, $compute->canvasH);
		if (function_exists('ImageColorAllocate')) {
			$black = ImageColorAllocate($thumb,255,255,255);
			if ($this->imageCreateFunc == 'imagecreatetruecolor' && function_exists('imagefilledrectangle')) {
				imagefilledrectangle($thumb, 0, 0, $compute->canvasW, $compute->canvasH, $black);
			} elseif ($this->imageCreateFunc == 'imagecreate' && function_exists('ImageColorTransparent')) {
				$bgTransparent = ImageColorTransparent($thumb, $black);
			}
		}
		call_user_func($this->imageCopyFunc, $thumb, $this->image->getSource(), $compute->dstX, $compute->dstY, $compute->srcX, $compute->srcY, $compute->dstW, $compute->dstH, $compute->srcW, $compute->srcH);
		$this->makeImage($thumb, $this->dstfile, $this->quality);
		imagedestroy($thumb);

		return true;
	}
	
	/**
	 * ѡ������ͼ���ɲ���
	 */
	public function compute() {
		switch ($this->type) {
			case self::TYPE_CENTER:
				$method = 'PwImageThumbCenterCompute';break;
			default:
				$method = 'PwImageThumbIntactCompute';
		}
		$compute = new $method($this->image, $this->width, $this->height, $this->forcemode);
		if ($compute->compute() === true) {
			return $compute;
		}
		return false;
	}
	
	/**
	 * ����ͼƬ
	 *
	 * @param resource $image ͼƬ����
	 * @param string $filename ͼƬ��ַ
	 * @param int $quality ͼƬ����
	 * return void
	 */
	public function makeImage($image, $filename, $quality = '90') {
		if ($this->image->type == 'jpeg') {
			call_user_func($this->imageFunc, $image, $filename, $quality);
		} else {
			call_user_func($this->imageFunc, $image, $filename);
		}
	}
	
	/**
	 * �������ͼ����Ҫ���Ƿ�����
	 *
	 * return bool
	 */
	public function checkEnv() {
		if (!$this->image->getSource()) {
			return false;
		}
		$this->imageFunc = 'image' . $this->image->type;
		if (!function_exists($this->imageFunc)) {
			return false;
		}
		if ($this->image->type != 'gif' && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
			$this->imageCreateFunc = 'imagecreatetruecolor';
			$this->imageCopyFunc = 'imagecopyresampled';
		} elseif (function_exists('imagecreate') && function_exists('imagecopyresized')) {
			$this->imageCreateFunc = 'imagecreate';
			$this->imageCopyFunc = 'imagecopyresized';
		} else {
			return false;
		}
		return true;
	}

	public function getThumbWidth() {
		return $this->thumbWidth;
	}

	public function getThumbHeight() {
		return $this->thumbHeight;
	}
}

abstract class PwImageThumbCompute {

	public $width;	//�������ƿ�
	public $height;	//�������Ƹ�

	public $srcX;	//Դͼ����ʼx����
	public $srcY;	//Դͼ����ʼy����
	public $srcW;	//Դͼ��ѡ�п��
	public $srcH;	//Դͼ��ѡ�и߶�

	public $dstX;	//Ŀ��ͼ����ʼx����
	public $dstY;	//Ŀ��ͼ����ʼy����
	public $dstW;	//Ŀ��ͼ����ƿ��
	public $dstH;	//Ŀ��ͼ����Ƹ߶�

	public $canvasW;	//�������
	public $canvasH;	//�����߶�
	
	protected $image;
	protected $force = 0;
	
	public function __construct($image, $width, $height, $force = 0) {
		$this->image = $image;
		$this->width = $width;
		$this->height = $height;
		$this->force = $force;
	}

	public function isSmall() {
		return ($this->image->width <= $this->width && $this->image->height <= $this->height);
	}

	public function isWider() {
		return ($this->image->width/$this->width > $this->image->height/$this->height);
	}

	abstract public function compute();
}

/**
 * �ȱ������㷨
 */
class PwImageThumbIntactCompute extends PwImageThumbCompute {
	
	public function compute() {

		$this->srcX = 0;
		$this->srcY = 0;
		$this->srcW = $this->image->width;
		$this->srcH = $this->image->height;

		$this->dstX = 0;
		$this->dstY = 0;

		if ($this->width > 0 && $this->height > 0) {
			if ($this->isSmall()) {
				if (!$this->force) return false;
				$this->dstW = $this->image->width;
				$this->dstH = $this->image->height;
			} elseif ($this->isWider()) {
				$this->dstW = $this->width;
				$this->dstH = $this->getThumbHeight();
			} else {
				$this->dstH = $this->height;
				$this->dstW = $this->getThumbWidth();
			}
		} elseif ($this->width > 0 && $this->image->width > $this->width) {
			$this->dstW = $this->width;
			$this->dstH = $this->getThumbHeight();
		} elseif ($this->height > 0 && $this->image->height > $this->height) {
			$this->dstH = $this->height;
			$this->dstW = $this->getThumbWidth();
		} else {
			if (!$this->force) return false;
			$this->dstW = $this->image->width;
			$this->dstH = $this->image->height;
		}
		$this->canvasW = $this->dstW;
		$this->canvasH = $this->dstH;

		return true;
	}

	public function getThumbWidth() {
		return round($this->image->width/$this->image->height * $this->height);
	}

	public function getThumbHeight() {
		return round($this->image->height/$this->image->width * $this->width);
	}
}

/**
 * ���н�ȡ�㷨
 */
class PwImageThumbCenterCompute extends PwImageThumbCompute {

	public function compute() {
		if ($this->width > 0 && $this->height > 0) {

		} elseif ($this->width > 0) {
			$this->height = $this->width;
		} elseif ($this->height > 0) {
			$this->width = $this->height;
		} else {
			return false;
		}
		if ($this->isSmall()) {
			if (!$this->force) return false;
			$this->srcX = 0;
			$this->srcY = 0;
			$this->srcW = $this->image->width;
			$this->srcH = $this->image->height;
		} elseif ($this->isWider()) {
			$this->srcW = round($this->width/$this->height * $this->image->height);
			$this->srcH = $this->image->height;
			$this->srcX = round(($this->image->width - $this->srcW) / 2);
			$this->srcY = 0;
		} else {
			$this->srcW = $this->image->width;
			$this->srcH = round($this->height/$this->width * $this->image->width);
			$this->srcX = 0;
			$this->srcY = round(($this->image->height - $this->srcH) / 2);
		}
		$this->dstW = min($this->srcW, $this->width);
		$this->dstH = min($this->srcH, $this->height);
		$this->dstX = round(($this->width - $this->dstW) / 2);
		$this->dstY = round(($this->height- $this->dstH) / 2);
		
		$this->canvasW = $this->width;
		$this->canvasH = $this->height;
		
		return true;
	}
}