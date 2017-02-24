<?php
defined('WEKIT_VERSION') || exit('Forbidden');
@set_time_limit('800');
/**
 * �ϴ����
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwStorageFtp.php 22526 2012-12-25 07:18:36Z yishuo $
 * @package upload
 */

class PwStorageFtp {

	private $_config;
	private $_ftp = null;

	public function __construct() {
		$this->_config = Wekit::C('attachment');
	}
	
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
		return $this->_config['ftp.url'] . '/' . $dir . $path;
	}
	
	/**
	 * ��ȡ���ص�ַ
	 *
	 * @param string $path
	 * @return string �ļ���ʵ�洢·��
	 */
	public function getDownloadUrl($path) {
		return $this->get($path, 0);
	}
	
	/**
	 * �洢����,�����Զ�̴洢���ǵ�ɾ�������ļ�
	 *
	 * @param string $source ����Դ�ļ���ַ
	 * @param string $filePath �洢���λ��
	 * @return bool
	 */
	public function save($source, $filePath) {
		$this->_getFtp()->upload($source, $filePath);
		Pw::deleteFile($source);
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
		return DATA_PATH . 'upload/' . Pw::time2str(WEKIT_TIMESTAMP, 'j') . '/' . str_replace('/', '_', $dir) . $filename;
	}
	
	/**
	 * ɾ������
	 *
	 * @param string $path ������ַ
	 */
	public function delete($path, $ifthumb = 0) {
		$this->_getFtp()->delete($path);
		if ($ifthumb) {
			($ifthumb & 1) && $this->_getFtp()->delete('thumb/' . $path);
			($ifthumb & 2) && $this->_getFtp()->delete('thumb/mini/' . $path);
		}
		return true;
	}

	public function _getFtp() {
		if ($this->_ftp == null) {
			Wind::import('WIND:ftp.WindSocketFtp');
			$this->_ftp = new WindSocketFtp(array(
				'server' => $this->_config['ftp.server'],
				'port' => $this->_config['ftp.port'],
				'user' => $this->_config['ftp.user'],
				'pwd' => $this->_config['ftp.pwd'],
				'dir' => $this->_config['ftp.dir'],
				'timeout' => $this->_config['ftp.timeout'],
			));
		}
		return $this->_ftp;
	}
}
?>