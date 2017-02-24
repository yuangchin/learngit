<?php
/**
 * ���ļ�ΪPHPWind��WindID������򱾿ͻ��˷��͵�֪ͨ���սű������API��鿴
 * ����ˣ�{@link http://wiki.open.phpwind.com/index.php?title=WindID_API}
 * �ͻ��ˣ�{@link http://wiki.open.phpwind.com/index.php?title=WindID%E5%AE%A2%E6%88%B7%E7%AB%AF%E6%8E%A5%E5%8F%A3%E5%BC%80%E5%8F%91%E8%AF%B4%E6%98%8E}
 *
 * @package    PHPWIND
 * @subpackage WindID
 * @author     Wilson <Wilsonnet@163.com>
 * @copyright  Copyright (C) 2008 - 2013 08CMS, Inc. All rights reserved.
 */
defined('PW_EXEC') || exit('No Permission');
// ����һ��PHPWINDӦ��·��
define('_08_PHPWIND_CLIENT_PATH', _08_PHPWIND_PATH . 'windid_client' . DS);
define('_08_PHPWIND_CLIENT_SERVICE_BASE_PATH', _08_PHPWIND_CLIENT_PATH .'src' . DS . 'windid' . DS . 'service' . DS . 'base' . DS);

class pw_Windid_Client
{    
    /**
     * ʧ��״̬��־
     * 
     * @var string
     */ 
    const FAIL = 'fail';
    
    /**
     * �ɹ�״̬��־
     * 
     * @var string
     */
    const SUCCESS = 'success';
    
    /**
     * �û�����
     * 
     * @static
     */ 
    private static $userInstance = null;
    
    /**
     * ��ǰ�����֪ͨ����
     * 
     * @var array
     */ 
    protected $_config = array();
    
    /**
     * ϵͳ���ò���
     * 
     * @var array
     */
    protected $_mconfigs = array();
    
    # ��ʼ�����ò���
    public function __construct( $configs )
    {
        $this->_mconfigs = (array) $configs['mconfig'];        
        $this->_config = (array) $configs['config'];
        
        # ������²���������ʱ���Զ���ʼ��
        foreach(array('windidkey', 'time', 'clientid', 'operation', 'uid') as $key)
        {
            isset($this->_config[$key]) || $this->_config[$key] = 0;
        }
        
        # �����̨δ����ͨ��֤����ʱ
        if ( empty($this->_mconfigs['enable_pptin']) )
        {
            $this->_showError( self::FAIL );
        }
        # ����windid�ӿ���
        require_once (_08_PHPWIND_CLIENT_PATH . 'src' . DS . 'windid' . DS . 'WindidApi.php'); 
        # ���ļ���������ò��ûӰ�죬���ٷ��ṩ��ʾ��������ˣ�������ʱ����
        require_once (_08_PHPWIND_CLIENT_SERVICE_BASE_PATH . 'WindidUtility.php'); 
        isset($database) && $this->_config['db'] = $database;
        
        # ��֤��Ȩ�����δ����ͨ��֤���ܻ�����Կ����ʱ��Ϊͨ�Ż����ʧ��
        $appkey = pw_Windid_Utility::appKey(@$this->_mconfigs['pptin_appid'], $this->_config['time'], @$this->_mconfigs['pptin_key']);
		# ��֤����ʱ��ʾͨ��ʧ��
        if ( $appkey !== $this->_config['windidkey'] )
        {
            $this->_showError( self::FAIL );
        }
        
        $time = Pw::getTime();
        if (($time - $this->_config['time']) > @$this->_mconfigs['pptin_expire']) $this->_showError( 'timeout' );
    }
    
    # ��ʼ����Ӧ��
    public function run()
    {
	    try
	    {
            $notify = (include (_08_PHPWIND_CLIENT_SERVICE_BASE_PATH . 'WindidNotifyConf.php'));
            
            # ���windid�ӿ����и�֪ͨ����ʱ����֪ͨ����
            if ( !empty($notify[$this->_config['operation']]['method']) )
            {
                $function = $notify[$this->_config['operation']]['method'];
                # ֪ͨ��������trueʱ����ʾͨ�ųɹ�
                if ( (bool) call_user_func(array($this, '_' . $function)) )
                {
                    $this->_showMessage( self::SUCCESS );
                }
            }
	    }
	    catch (_08_ApplicationException $e)
	    {
	   		$this->_showError( self::FAIL );
	    }
        
        $this->_showError( self::FAIL );
    }
    
    /**
     * ͨ�Ų��ԣ������ִ�иú�����֤��ͨ�ųɹ�
     * 
     * @return bool ͨ�ųɹ�����true���������쳣ʱ����false
     */ 
    protected function _test()
    {
        if(@WINDID_CONNECT == 'db')
        {
            try
            {
                $db = new PDO($this->_config['db']['dsn'], $this->_config['db']['user'], $this->_config['db']['pwd']);
                $row = $db->query("SHOW COLUMNS FROM {$this->_config['db']['tableprefix']}user");
            }
            catch(PDOException $e)
            {
                throw new _08_ApplicationException();
            }
        }
        # ���WINDID_CONNECTδ����Ҳ��Ϊͨ�Ų��ɹ�
		if( (false == defined('WINDID_CONNECT')) || empty($row) )
        {
			return false;
		}
        
        return true;
    }
    
    /**
     * ����û�
     * 
     * @return bool ��ӳɹ�����true�����򷵻�false
     */
    protected function _addUser()
    {
        $userinfo = $this->_getPwUser();
        $user = new cls_UserbaseDecorator( self::_getUserInstance() );
        # ���������һ�����룬��ʱ���û���¼ʱ�Զ�ͬ���޸�
        $flag = (bool) $user->synAddLocalUser(
            $userinfo['username'], 
            cls_string::Random(6), 
            $userinfo['email'],
            array(cls_Windid_Message::PW_UID => $userinfo['uid'])
        );
        unset($user);
        # ע��ɹ�ʱ����ͬ����¼
        if ($flag)
        {
            $this->_synLogin();
            return true;
        }
        
        return false;
    }
    
    /**
     * ͬ����¼
     * 
     * @return bool ִ�гɹ�����true�����򷵻�false
     */
    protected function _synLogin()
    {
        cls_HttpStatus::trace('P3P');
        $acuser = self::_getUserInstance();
        $userInfo = $this->_getPwUser();
		$db->select('mid, password')->from('#__members')->where(array('mname' => $userInfo['username']))->_and('checked = 1')->exec();
		if ( $cmember = $db->fetch() )
        {
            # ͬ����Ӧ�������ͻ����û�ID
            $acuser->activeuser($cmember['mid']);
            $acuser->updatefield(cls_Windid_Message::PW_UID, $userInfo['uid']);
            $acuser->updatedb();
			$acuser->autopush(); //�Զ�����
            
            # ִ�е�¼
			msetcookie(cls_Windid_Message::PW_UID_COOKIE, $userInfo['uid'], cls_Windid_Message::PW_UID_COOKIE_TIME); 
			$acuser->LoginFlag($cmember['mid'], $cmember['password']);
            return true;
		}
        
        return false;
    }
    
    /**
     * ͬ���ǳ�
     * 
     * @return bool ִ�гɹ�����true;
     */ 
    protected function _synLogout()
    {
        cls_HttpStatus::trace('P3P');
        cls_userinfo::LogoutFlag();
        return true;
    }
    
    /**
     * �༭�û�������Ϣ(���룬���䣬��ȫ����)
     * ע������Ҫ���û��´ε�¼ʱ�޸ģ���ȫ���Ȿϵͳ��ʱδ����
     * 
     * @todo �޸İ�ȫ�����Ժ�����Ҫ��ʱҪ������
     */ 
    protected function _editUser()
    {
        $userInfo = $this->_getPwUser();
        if (empty($userInfo)) return false;
        $actuser = self::_getUserInstance();
        $actuser->activeuserbyname($userInfo['username']);
        # ��ϵͳֻ��������޸ģ����������һ�ε�¼ʱ�޸�
        $actuser->updatefield('email', $userInfo['email']);
        $actuser->updatedb();
        return true;
    }
    
    /**
     * ����ͷ��
     * 
     * @todo ��ϵͳͷ���ֶβ��̶�����ʱ��������ͷ��ͨ��
     */ 
    protected function _uploadAvatar()
    {
        #$avatar = $this->_getPwUserAvatar();
    }
    
    /**
     * �༭�û�����(������ں�̨�����޸�ʱ�ͻᷢ���������~~)
     * 
     * @return bool �޸ĳɹ�����true����ȡ�����û���Ϣʱ����false
     * @todo        ��ʱ���Ըû���ͬ��
     */ 
    protected function _editCredit()
    {
    }
    
    /**
     * ͬ���û�δ��˽��
     */ 
    protected function _editMessageNum()
    {
        global $mcharset;
        $db = _08_factory::getDBO();
        # ��ȡ����δ���Ի�
        $messages = $this->_getPwUserMessage();
        foreach($messages as &$message)
        {
            if (empty($message))
            {
                continue;
            }
            $message['last_message'] = unserialize($message['last_message']);
        
            $title = mb_substr($message['last_message']['content'], 0, 20, $mcharset);
            # ����û����˽��ID����������ͨ������˽�ŵİ취��ȡ˽��ID��Ȼ�󱣴�
            $message_info = WindidApi::api('message')->searchMessage(
                array('fromuid'=>$message['last_message']['from_uid'], 'keyword'=>$message['last_message']['content']),
                0, 1
            );
            $message_id = @$message_info[1][0]['message_id'];
            # ���ⲿ���͵�˽�ű��浽��ϵͳ
            $db->insert(
                '#__pms',
                array(
                    'fromuser' => $message['last_message']['from_username'], 
                    'fromid' => $message['last_message']['from_uid'], 
                    'toid' => $message['last_message']['to_uid'], 
                    'title' => $title, 
                    'content' => $message['last_message']['content'], 
                    'pmdate' => $message['modified_time'],
                    cls_Windid_Message::PW_MESSAGE_ID => (int)$message_id
                )
            )->exec();
        }
        
        unset($db);
    }
    
    /**
     * ɾ���û��������ûʵ������ɾ�����ܣ�ֻ��һ����ɾ��
     * 
     * @return bool ɾ���ɹ�����true�����򷵻�false
     */ 
    protected function _deleteUser()
    {
        $flag = false;
        if( $this->_config['uid'] > 1 )
        {
            $actuser = self::_getUserInstance();
            $actuser->activeuser( self::_getMidByPwUid( $this->_config['uid'] ) );
            $flag = $actuser->delete();
            unset($actuser);
        }        
        
        return ($flag ? true : false);
    }
    
    /**
     * ͨ��WINDID����˵��û�ID��ȡ��ϵͳ��Ӧ���û�ID
     * 
     * @param  int $uid WINDID������û�ID
     * @return int      ���ر�ϵͳ�û�ID
     * @since  1.0
     */
    protected static function _getMidByPwUid( $uid )
    {
        $user = self::_getUserInstance();
        $info = $user->getUserInfo('mid', cls_Windid_Message::PW_UID . ' = ' . (int)$uid);
        return $info['mid'];
    }
	
	/**
	 * ���������ԱID�����˻�ȡPW�Ļ�Ա��Ϣ
     * 
     * @param  string $type ����WindidUserApi����� getUser ��ͷ�ļ���API��׺
	 * @return array        ���ػ�ȡ�����û���Ϣ
     * 
     * @since  1.0
	 */
	protected function _getPwUser( $type = '' )
	{
        if ( !$this->_checkUID() )
        {
            $this->_showError( self::FAIL );
        }
        $api = WindidApi::api('user');
        return call_user_func( array($api, 'getUser' . @ucfirst($type)), $this->_config['uid'] );
	}
    
    /**
	 * ���������ԱID�����˻�ȡPW�Ļ�Աͷ��
     * 
     * @param  string $size ͷ���С��big-��middle-�У�small-С
	 * @return array        ���ػ�ȡ�����û�ͷ����Ϣ
     * 
     * @since  1.0
	 */
	protected function _getPwUserAvatar($size = 'middle')
	{
        if ( !$this->_checkUID() )
        {
            $this->_showError( self::FAIL );
        }
        $api = WindidApi::api('avatar');
        return $api->getAvatar($this->_config['uid'], $size);
	}
    
    /**
	 * ���������ԱID�����˻�ȡPW�Ķ���δ���Ի�
     * 
	 * @return array ���ػ�ȡ�����û�˽��
     * 
     * @since  1.0
	 */
    protected function _getPwUserMessage()
    {
        if ( !$this->_checkUID() )
        {
            $this->_showError( self::FAIL );
        }
        $api = WindidApi::api('message');
        return $api->getUnreadDialogsByUid($this->_config['uid']);
    }
    
    /**
     * ���UID�Ƿ�Ϸ�
     * 
     * @return bool ����Ϸ�����TRUE�����򷵻�FALSE
     */
    protected function _checkUID()
    {
        if ( empty($this->_config['uid']) || (isset($this->_config['uid']) && ($this->_config['uid'] <= 0)) )
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * ��ȡ�û�����
     * 
     * @return object �����û����ʵ�����
     */
    protected static function _getUserInstance()
    {
        if(! (self::$userInstance instanceof cls_userinfo) )
        {
            self::$userInstance = new cls_userinfo;
        }
        
        return self::$userInstance;
    }
	
    /**
     * ��ӡ������Ϣ
     * 
     * @param string $message Ҫ��ӡ����Ϣ
     * @since 1.0
     */ 
	protected function _showError($message = '', $referer = '', $refresh = false)
    {
		exit($message);
	}

    /**
     * ��ӡ��Ϣ
     * 
     * @param string $message Ҫ��ӡ����Ϣ
     * @since 1.0
     */ 
	protected function _showMessage($message = '', $referer = '', $refresh = false)
    {
		exit($message);
	}
    
    public function __call($name, $arguments)
    {
        $this->_showError( self::FAIL );
    }
}