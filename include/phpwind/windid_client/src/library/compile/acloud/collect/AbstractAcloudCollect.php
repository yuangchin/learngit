<?php

/**
 * ģ������ռ�
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AbstractAcloudCollect.php 6816 2012-03-26 12:03:38Z xiaoxia.xuxx $
 * @package wekit.compile.acloud.collect
 */
abstract class AbstractAcloudCollect {
	/**
	 * ��Ҫ�ռ���action
	 *
	 * @var array
	 */
	protected $collectActions = array('run');
	/**
	 * �ռ�ģ��
	 * 
	 * @param PwAcloudDataMapper $dataMapper
	 * @param array ģ���еı���
	 */
	abstract public function collect(PwAcloudDataMapper $dataMapper, $template);
	
	/**
	 * ����ģ���Ƿ���Ҫ�ռ�����
	 *
	 * @param string $a
	 * @return boolean
	 */
	public function isCollect($a) {
		return in_array(strtolower($a), $this->collectActions);
	}
}