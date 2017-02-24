<?php
/**
 * �ٶȱ༭������
 * 
 * @since     nv50
 *
 * @author    Wilson <Wilsonnet@163.com>
 * @copyright Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */

defined('_08CMS_APP_EXEC') || exit('No Permission');
class _08_M_Ueditor_Base extends _08_Models_Base
{
    private $type = 'image';
    
    private $params = array();
    
    private $configs = array();
    
    /**
     * ��ȡ����
     * 
     * @return string         ���ظ�ʽ�����JSON����
     */
    public function config()
    {
        return $this->_formatData($this->getConfigs());
    }
    
    /* �г�ͼƬ */
    public function listimage()
    {
        $this->type = 'image';
        $path = M_ROOT . $this->_mconfigs['dir_userfile'] . DS . 'image';
        $images = _08_FileSystemPath::map(array($this, 'getFiles'), $path);        
        $result = $this->_listStatus($images);        
        return $this->_formatData($result);
    }
    
    /* �г��ļ� */
    public function listfile()
    {
        $this->type = 'file';
        $path = M_ROOT . $this->_mconfigs['dir_userfile'] . DS . 'file';
        $files = _08_FileSystemPath::map(array($this, 'getFiles'), $path);
        $result = $this->_listStatus($files);
        return $this->_formatData($result);
    }
    
    /**
     * ��ȡ�ļ����ڵ��ļ�
     * 
     * @param  object $item  �ļ�����ڵ�
     * @return array  $files ���ػ�ȡ�����ļ���Ϣ���飬��ȡʧ��ʱ���ؿ�����
     * 
     * @since  nv50
     */
    public function getFiles( $item )
    {
        $file = array();
        $localfiles = cls_atm::getLocalFilesExts($this->type);
        $ext = substr(strrchr($item->getFilename(), '.'), 1);
        if ( $ext && array_key_exists($ext, $localfiles) )
        {
            $file['url'] = cls_url::localToUrl($item->getPathname());
            $file['mtime'] = $item->getMTime();
        }
        
        return $file;
    }
    
    /**
     * �б�״̬
     * 
     * @param  array $list   �б�����
     * @return array $status �����б�״̬����
     * 
     * @since  nv50
     */
    protected function _listStatus( array $files )
    {
        $files = array_filter($files);
        /* ��ȡָ����Χ���б� */
        $list = cls_Array::limit($files, $this->params['start'], $this->params['end']);
        if ( empty($list) )
        {
            $status = array(
                "state" => "no match file",
                "list" => array(),
                "start" => $this->params['start'],
                "total" => 0
            );
        }
        else
        {
            $status = array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $this->params['start'],
                "total" => count($list)
            );
        }
        
        return $status;
    }
    
    public function __construct()
    {
        parent::__construct();
        /* ��ȡ���� */
        $this->params = array();
        if ( isset($this->_get['size']) )
        {
            $this->params['size'] = (int)$this->_get['size'];
        }
        else
        {
            $this->params['size'] = 20;
        }
        
        if ( isset($this->_get['start']) )
        {
            $this->params['start'] = (int)$this->_get['start'];
        }
        else
        {
            $this->params['start'] = 0;
        }
        
        $this->params['end'] = $this->params['start'] + $this->params['size'];
        
        $file = _08_FilesystemFile::getInstance();
        $file->_fopen( _08_OUTSIDE_PATH . "ueditor:config.json", 'r');
        $this->configs = $file->_fread();
        $this->configs = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $this->configs), true);
    }
    
    /**
     * ��ȡ������Ϣ
     * 
     * @param array ���ػ�ȡ����������Ϣ
     * 
     * @since nv50
     */
    public function getConfigs()
    {
        # ͼƬ�ļ�����
        $imageTypes = cls_atm::getLocalFilesExts('image');
        $this->configs['imageAllowFiles'] = array_keys($imageTypes);
        $this->_setType($this->configs['imageAllowFiles']);
        $this->configs['imageManagerAllowFiles'] = $this->configs['imageAllowFiles'];
        $this->configs['imageMaxSize'] = $this->configs['scrawlMaxSize'] = $this->configs['catcherMaxSize'] = $this->_getMaxSize($imageTypes);
        
        # ��Ƶ��FLASH�ļ�����
        $videoTypes = array_merge(cls_atm::getLocalFilesExts('media'), cls_atm::getLocalFilesExts('flash'));
        $this->configs['videoAllowFiles'] = array_keys($videoTypes);
        $this->_setType($this->configs['videoAllowFiles']);
        $this->configs['videoMaxSize'] = $this->_getMaxSize($videoTypes);
        
        # �����ļ�����
        $fileTypes = cls_atm::getLocalFilesExts('file');
        $this->configs['fileAllowFiles'] = array_keys($fileTypes);
        $this->_setType($this->configs['fileAllowFiles']);
        $this->configs['fileManagerAllowFiles'] = $this->configs['fileAllowFiles'];
        $this->configs['fileMaxSize'] = $this->_getMaxSize($fileTypes);
        return $this->configs;
    }
    
    /**
     * �������ͣ����������Ʊ����չ��׺����
     * 
     * @param  array $typeValues Ҫ���õ���������
     * 
     * @since  nv50
     */
    protected function _setType( array &$typeValues )
    {
        foreach ( $typeValues as &$value ) 
        {
            $value = '.' . $value;
        }
    }
    
    protected function _getMaxSize( array $typeValues )
    {
        $max = 0;
        foreach ( $typeValues as &$value ) 
        {
            if ( $value['maxsize'] > $max )
            {
                $max = $value['maxsize'];
            }
        }
        return $max;
    }
    
    /**
     * ��ʽ������
     * 
     * @param  mixed  $result Ҫ��ʽ��������
     * @return string         ���ظ�ʽ�����JSON����
     */
    protected function _formatData( $result )
    {
        return _08_Documents_JSON::encode($result);
    }
    
    public function __call($name, $argc)
    {
        return $this->_formatData(array( 'state'=> '�����ַ����' ));
    }
}