<?php

/**
 * ��������map��
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwAcloudDataMapper.php 12670 2012-06-25 07:44:35Z yanchixia $
 * @package wekit.compile.acloud
 */
class PwAcloudDataMapper {
	private $src = '';
	private $charset = 'utf8';
	private $username = '';
	private $uid = 0;
	private $tid = 0;
	private $fid = 0;
	private $title = '';
	
	/**
	 * ��õ�ǰҳ��ʶ
	 * 
	 * @return string
	 */
	public function getSrc() {
		return $this->src;
	}

	/**
	 * ��ñ���
	 * 
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * ��õ�ǰ��¼�û���
	 * 
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * ��õ�ǰ��¼�û�ID
	 * 
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * ��õ�ǰ����ID
	 * 
	 * @return int
	 */
	public function getTid() {
		return $this->tid;
	}

	/**
	 * ��õ�ǰ���ID
	 * 
	 * @return int
	 */
	public function getFid() {
		return $this->fid;
	}

	/**
	 * ��õ�ǰ���ӱ���
	 * 
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * ���õ�ǰҳ���ʶ
	 * 
	 * @param string $src
	 */
	public function setSrc($src) {
		$this->src = $src;
	}

	/**
	 * ���õ�ǰʹ�ñ���
	 * 
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * ���õ�ǰ��¼�û���
	 * 
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * ���õ�ǰ��¼�û���
	 * 
	 * @param int $uid
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * ���õ�ǰ����ID
	 * 
	 * @param int $tid
	 */
	public function setTid($tid) {
		$this->tid = $tid;
	}

	/**
	 * ���õ�ǰ���ID
	 * 
	 * @param int $fid
	 */
	public function setFid($fid) {
		$this->fid = $fid;
	}

	/**
	 * ���õ�ǰ���ӱ���
	 * 
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
}