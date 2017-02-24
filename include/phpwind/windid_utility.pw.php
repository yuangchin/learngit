<?php
/**
 * ��չWindidUtility API
 *
 * @package    PHPWIND
 * @subpackage WindID
 * @author     Wilson <Wilsonnet@163.com>
 * @copyright  Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
defined('PW_EXEC') || exit('No Permission');
require_once (_08_PHPWIND_CLIENT_SERVICE_BASE_PATH . 'WindidUtility.php'); 
class pw_Windid_Utility extends WindidUtility
{
    /**
     * ����APP KEY
     * �˺���Ϊ�˼���PHPWIND9.0��9.0.1�����汾����չ
     **/
    public static function appKey($apiId, $time, $secretkey, $get = array(), $post = array())
    {
//        if (NEXT_VERSION === '9.0')
//        {
//            return md5(md5($apiId.'||'.$secretkey).$time);
//        }
        
        # PHPWIND 9.0.1 �����㷨
        if (empty($get) || empty($post))
        {
            $get = $_GET;
            $post = $_POST;
        }
        if (isset($get['operation']) && ($get['operation'] !== '999'))
        {
            unset($get['operation']);
        }
        if (isset($post['operation']) && ($post['operation'] !== '999'))
        {
            unset($post['operation']);
        }
		$array = array('windidkey', 'clientid', 'time', '_json', 'jcallback', 'csrf_token', 'Filename', 'Upload', 'token', 'domain');
		$str = '';
		ksort($get);
		ksort($post);
		foreach ($get AS $k=>$v) {
			if (in_array($k, $array)) continue;
            $str .=$k.$v;
		}
		foreach ($post AS $k=>$v) {
			if (in_array($k, $array)) continue;
            $str .=$k.$v;
		}
        
		return md5(md5($apiId.'||'.$secretkey).$time.$str);
    }
}