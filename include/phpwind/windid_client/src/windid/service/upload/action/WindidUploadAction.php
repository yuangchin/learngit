<?php
defined('WINDID_VERSION') || exit('Forbidden');

/**
 * �ϴ���������/�ӿ�
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: WindidUploadAction.php 21452 2012-12-07 10:18:33Z gao.wanggao $
 * @package upload
 */

abstract class WindidUploadAction {

	public $ftype = array();
	public $attachs = array();
	public $isLocal = false;
	
	/**
	 * ����Ƿ�����ʼ�ϴ���Ϊ
	 *
	 * @return bool
	 */
	public function check() {
		return true;
	}
	
	/**
	 * �������ϴ���¼�Ƿ���Ϊ��������ҵ���ϴ�
	 *
	 * @param string $key $_FILES�����е�key
	 * @return bool
	 */
	public function allowType($key) {
		return true;
	}
	
	/**
	 * ��ȡ�ϴ��ļ��洢��
	 *
	 * @param object $file �ϴ��ļ�����<PwUploadFile>
	 * @return string
	 */
	abstract public function getSaveName(WindidUploadFile $file);
	
	/**
	 * ��ȡ�ϴ��ļ��洢·��
	 *
	 * @param object $file �ϴ��ļ�����<PwUploadFile>
	 * @return string
	 */
	abstract public function getSaveDir(WindidUploadFile $file);
	
	/**
	 * �Ƿ�������ͼ
	 *
	 * @return bool
	 */
	public function allowThumb() {
		return false;
	}
	
	/**
	 * ����ͼ��������
	 *
	 * @param string $filename �ļ���
	 * @param string $dir �洢·��
	 * @return array ���� 
	 *	��:array(
	 *		array(0.����ͼ�ļ���, 1.����ͼ�洢��ַ, 2.���ƿ�, 3.���Ƹ�, 4.����ͼ���ɷ�ʽ(*), 5.ǿ������(*)),
	 *		array('abc.jpg', 'thumb/mini', 300, 300, 0, 0) ���ɶ������ͼʱ����������
	 *	)
	 * (*4).����ͼ���ɷ�ʽ <0.�ȱ����� 1.���н�ȡ 2.�ȱ����>
	 * (*5).ǿ������ <0.���ļ��ߴ�С������Ҫ��ʱ�������� 1.������>
	 */
	public function getThumbInfo($filename, $dir) {
		return array();
	}
	
	/**
	 * �Ƿ���ͼƬˮӡ
	 *
	 * @return bool
	 */
	public function allowWaterMark() {
		return false;
	}
	
	/**
	 * ��ȡˮӡ����
	 *
	 * @return array ����,����ѡ���������
	 *  ��:array(
	 *		'type'				=> 1,				<int, 1.ͼƬˮӡ 2.����ˮӡ>
	 *		'gif'				=> 1,				<bool, �Ƿ�ΪgifͼƬ��ˮӡ>
	 *		'limitwidth'		=> 200,				<bool, ���ƿ�>
	 *		'limitheight'		=> 200,				<bool, ���Ƹ�> 
	 *		'position'			=> 9,				<bool, 1-9,�Ź���λ��>
	 *		'transparency'		=> 85,				<bool, 0-100, ͸����>
	 *		'quality'			=> 85,				<bool, 0-100, ˮӡ����>
	 *		'file'				=> a.gif,			<string, ˮӡ�ļ�>
	 *		'text'				=> abc,				<string, ˮӡ����>
	 *		'fontfamily'		=> cn_witer.ttf,	<string, ����>
	 *		'fontsize'			=> 12,				<string, �ֺ�>
	 *		'fontcolor'			=> #aaa				<string, ��ɫ>
	 *   )
	 */
	public function getWaterMarkInfo() {
		return array();
	}
	
	/**
	 * �ϴ���ɣ�ҵ�����߼�
	 *
	 * @param array $uploaddb �ϴ��ļ��б�
	 * @return mixed
	 */
	abstract public function update($uploaddb);
	
	/**
	 * ����ϴ������б�
	 *
	 * @return array
	 */
	public function getAttachs() {
		return $this->attachs;
	}
	
	/**
	 * ����ϴ���������
	 *
	 * @return int
	 */
	public function getUploadNum() {
		return count($this->attachs);
	}
}
?>