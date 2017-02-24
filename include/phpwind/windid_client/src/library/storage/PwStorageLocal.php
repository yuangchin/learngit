<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * �ϴ����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwStorageLocal.php 24383 2013-01-29 10:09:39Z jieyin $
 * @package upload
 */

class PwStorageLocal {

	/**
	 * ��ȡweb��ַ
	 *
	 * @param string $path ��Դ洢��ַ
	 * @param int $ifthumb �Ƿ��ȡ����ͼ
	 * @return string
	 */
	public function get($path, $ifthumb) {
		$dir = '';
		if ($ifthumb & 2) {
			$dir = 'thumb/mini/';
		} elseif ($ifthumb & 1) {
			$dir = 'thumb/';
		}
		return Wekit::url()->attach . '/' . $dir . $path;
	}
	
	/**
	 * ��ȡ���ص�ַ
	 *
	 * @param string $path
	 * @return string �ļ���ʵ�洢·��
	 */
	public function getDownloadUrl($path) {
		return ATTACH_PATH . $path;
	}
	
	/**
	 * �洢����,�����Զ�̴洢���ǵ�ɾ�������ļ�
	 *
	 * @param string $source ����Դ�ļ���ַ
	 * @param string $filePath �洢���λ��
	 * @return bool
	 */
	public function save($source, $filePath) {
		return true;
	}
	
	/**
	 * ��ȡ�����ϴ�ʱ�洢�ڱ��ص��ļ���ַ
	 *
	 * @param string $filename �ļ���
	 * @param string $dir Ŀ¼��
	 * @return string
	 */
	public function getAbsolutePath($filename, $dir) {
		return ATTACH_PATH . $dir . $filename;
	}
	
	/**
	 * ɾ������
	 *
	 * @param string $path ������ַ
	 */
	public function delete($path, $ifthumb = 0) {
		Pw::deleteFile(ATTACH_PATH . $path);
		if ($ifthumb) {
			($ifthumb & 1) && Pw::deleteFile(ATTACH_PATH . 'thumb/' . $path);
			($ifthumb & 2) && Pw::deleteFile(ATTACH_PATH . 'thumb/mini/' . $path);
		}
		return true;
	}
}
?>