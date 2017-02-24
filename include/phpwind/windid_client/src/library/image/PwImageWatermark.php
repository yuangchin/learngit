<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * ����ˮӡ
 *
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author Jianmin Chen <sky_hold@163.com>
 * @version $Id: PwImageWatermark.php 24724 2013-02-17 10:05:52Z jieyin $
 * @package lib.image
 */

class PwImageWatermark {

	protected $image;
	protected $water;
	
	protected $dstfile;			//Ŀ���ļ�
	protected $position;		//ˮӡλ�� 1-9 �ֱ�Ϊ�Ź���Ķ�Ӧλ��
	protected $transparency;	//ˮӡ͸����
	protected $quality;			//ͼƬ����
	protected $type;			//ˮӡ���� <1.ͼƬˮӡ 2.����ˮӡ>
	
	protected $file;

	protected $text;			//ˮӡ����
	protected $fontfamily;		//ˮӡ����
	protected $fontsize;		//�����С
	protected $fontcolor;		//������ɫ
	protected $fontfile;

	public function __construct(PwImage $image) {
		$this->image = $image;
	}
	
	public function setDstfile($file) {
		$this->dstfile = $file;
		return $this;
	}

	/**
	 * ����λ��
	 */
	public function setPosition($position) {
		$this->position = intval($position);
		return $this;
	}
	
	/**
	 * ����ˮӡ͸����
	 */
	public function setTransparency($transparency) {
		$this->transparency = intval($transparency);
		return $this;
	}
	
	/**
	 * ����ͼƬ����
	 */
	public function setQuality($quality) {
		$this->quality = intval($quality);
		return $this;
	}
	
	/**
	 * �������Է�ʽ <1.ͼƬˮӡ 1.����ˮӡ>
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}
	
	/**
	 * ����ˮӡͼƬ
	 */
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}

	/**
	 * ����ˮӡ����
	 */
	public function setText($text) {
		$this->text = $text;
		return $this;
	}

	/**
	 * ����ˮӡ����
	 */
	public function setFontfamily($fontfamily) {
		$this->fontfamily = $fontfamily;
		return $this;
	}

	/**
	 * ����ˮӡ�����С
	 */
	public function setFontsize($fontsize) {
		$this->fontsize = $fontsize;
		return $this;
	}

	/**
	 * ����ˮӡ������ɫ
	 */
	public function setFontcolor($fontcolor) {
		$this->fontcolor = $fontcolor;
		return $this;
	}
	
	public function getPosition($water) {
		if ($this->position >= 1 && $this->position <= 9) {
			$px = ($this->position - 1) % 3;
			$py = intval(($this->position - 1) / 3);
			switch ($px) {
				case 0:
					$offsetX = 5;break;
				case 1:
					$offsetX = ($this->image->width - $water->width) / 2;break;
				default:
					$offsetX = $this->image->width - $water->width - 5;
			}
			switch ($py) {
				case 0:
					$offsetY = 5;break;
				case 1:
					$offsetY = ($this->image->height - $water->height) / 2;break;
				default:
					$offsetY = $this->image->height - $water->height - 5;
			}
		} else {
			$offsetX = rand(5, $this->image->width - $water->width - 5);
			$offsetY = rand(5, $this->image->height - $water->height - 5);
		}
		return array($offsetX, $offsetY);
	}

	public function initWaterWay() {
		if ($this->type == 1) {
			$water = new PwImage(Wind::getRealDir('REP:mark') . '/' . $this->file);
			if (!$water->isImage() || !$water->getSource()) {
				return false;
			}
		} else {
			if (!$this->text || strlen($this->fontcolor) != 7) {
				return false;
			}
			empty($this->fontfamily) && $this->fontfamily = 'en_arial.ttf';
			empty($this->fontsize) && $this->fontsize = 12;
			$this->fontfile = Wind::getRealDir('REP:font') . '/' . $this->fontfamily;
			$temp = imagettfbbox($this->fontsize, 0, $this->fontfile, $this->text); //ȡ��ʹ�� TrueType ������ı��ķ�Χ
			$water = new stdClass();
			$water->width = $temp[2] - $temp[6];
			$water->height = $temp[3] - $temp[7];
			unset($temp);
		}
		return $water;
	}

	/**
	 * ��������ͼ
	 */
	public function execute() {
		if (!$this->image->getSource()) {
			return false;
		}
		if (!function_exists('image' . $this->image->type)) {
			return false;
		}
		if (($water = $this->initWaterWay()) === false) {
			return false;
		}
		list($offsetX, $offsetY) = $this->getPosition($water);

		$source = $this->image->getSource();

		if ($this->image->type == 'png') {
			imagealphablending($source, false);
			imagesavealpha($source, true);
		} else {
			imagealphablending($source, true);
		}
		/*
		if ($this->image->type != 'png') {
			$tmp = imagecreatetruecolor($this->image->width, $this->image->height);
			imagecopy($tmp, $source, 0, 0, 0, 0, $this->image->width, $this->image->height);
			$source = $tmp;
		}*/
		
		if ($this->type == 1) {
			$source = $this->doImage($source, $water, $offsetX, $offsetY, $this->transparency);
		} else {
			$source = $this->doText($source, $water, $offsetX, $offsetY);
		}
		$this->dstfile || $this->dstfile = $this->image->filename;
		$this->makeImage($this->image->type, $source, $this->dstfile, $this->quality);
		imagedestroy($source);
		return true;
	}

	public function doImage($source, $water, $offsetX, $offsetY, $transparency) {
		$ws = $water->getSource();
		if ($water->type == 'png') {
			//imagealphablending($source, true);
       	 	imagecolortransparent($source, imagecolorallocatealpha($source, 0, 0, 0, 0));
        	imagecopyresampled($source, $ws, $offsetX, $offsetY, 0, 0, $water->width, $water->height, $water->width, $water->height);
        	//imagecopy($source, $ws, $offsetX, $offsetY, 0, 0, $water->width, $water->height);
		} else {
			imagealphablending($ws, true);
			imagecopymerge($source, $ws, $offsetX, $offsetY, 0, 0, $water->width, $water->height, $transparency);
		}
		imagedestroy($ws);
		unset($water);
		return $source;
	}

	public function doText($source, $water, $offsetX, $offsetY) {
		$R = hexdec(substr($this->fontcolor, 1, 2));
		$G = hexdec(substr($this->fontcolor, 3, 2));
		$B = hexdec(substr($this->fontcolor, 5));
		//imagestring($sourcedb['source'],$w_font,$wX,$wY,$w_text,imagecolorallocate($sourcedb['source'],$R,$G,$B));
		if (strpos($this->fontfamily, 'ch') === 0 && strtoupper(Wekit::V('charset')) != 'UTF-8') {
			$this->text = WindConvert::WindConvert($this->text, 'UTF-8', Wekit::V('charset'));
		}
		imagettftext($source, $this->fontsize, 0, $offsetX, $offsetY + $water->height, imagecolorallocate($source, $R, $G, $B), $this->fontfile, $this->text);
		unset($water);
		return $source;
	}

	public function makeImage($type, $image, $filename, $quality = '90') {
		$makeimage = 'image' . $type;
		if (!function_exists($makeimage)) {
			return false;
		}
		if ($type == 'jpeg') {
			$makeimage($image, $filename, $quality);
		} else {
			$makeimage($image, $filename);
		}
		return true;
	}
}