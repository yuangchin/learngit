<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * pw������û���
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright 2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwHook.php 24919 2013-02-26 11:36:12Z jieyin $
 * @package wekit
 * @subpackage engine
 */
class PwHook {
	
	/**
	 * @var WindNormalViewerResolver
	 */
	private static $viewer = null;
	private static $__alias = '';
	private static $methods = array();
	private static $hooks = array();
	private static $prekeys = array();
	private static $prehooks = array();

	/**
	 * Ԥ���ѯ��ֵ��������ѯ����(�����Ż�����)
	 *
	 * @param array $keys ��ѯ��ֵ <����array('c_read_run', 'm_PwThreadDisplay')>
	 * @return void
	 */
	public static function preset($keys) {
		foreach ($keys as $key) {
			self::$prekeys[] = $key;
		}
	}

	/**
	 * ��ʼ��ע���б�
	 *
	 * @return void
	 */
	public static function initRegistry() {
		$data = Wekit::load('hook.srv.PwHookInjectService')->fetchInjectByHookName(self::$prekeys);
		foreach ($data as $key => $value) {
			self::$hooks[$key] = $value;
		}
		self::$prekeys = array();
	}

	/**
	 * ���ָ����չ���ȫ����չ����
	 *
	 * @param string $registerKey
	 * @return array
	 */
	public static function getRegistry($registerKey) {
		if (self::$prekeys) self::initRegistry();
		if (!isset(self::$hooks[$registerKey])) {
			self::$hooks[$registerKey] = Wekit::load('hook.srv.PwHookInjectService')->getInjectByHookName($registerKey);
		}
		if (isset(self::$prehooks[$registerKey])) {
			self::$hooks[$registerKey] = array_merge(self::$hooks[$registerKey], self::$prehooks[$registerKey]);
			unset(self::$prehooks[$registerKey]);
		}
		return self::$hooks[$registerKey];
	}

	/**
	 * �ֶ�ע�ṳ��
	 *
	 * @param string $registerKey ������
	 * @param array $inject ע����Ϣ <array('class' => 'SRV:forum.srv.PwThreadDisplay', 'method' => 'escapeSpace', 'loadway' => '...', ...)>
	 */
	public static function registerHook($registerKey, $inject) {
		self::$prehooks[$registerKey][] = $inject;
	}

	/**
	 * ģ����ͼhook��Ⱦ����
	 * 
	 * ģ��Hook����ʵ��{@example<pre>
	 * ��������д��ʵ����ģ���еĹ��ӹ���.
	 * <hook class='$a' method='display1' />
	 * �����д����������Ϊ:
	 * PwHook::display(array($a,'display1'),$args,$viewer);
	 * </pre>}
	 * <note>ע��: ����ģ���ǩ��ʽ����������ʾ���ø÷���.</note>
	 * @param string|array $callback
	 * @param array $args
	 * @param string $alias
	 * @param WindViewerResolver $viewer
	 */
	public static function display($callback, $args, $alias, $viewer) {
		if (!$callback || !is_array($args)) return;
		self::$viewer = $viewer;
		self::$__alias = $alias;
		call_user_func_array($callback, $args);
	}

	/**
	 * ������չ��ģ�����ݲ���ʾ
	 * 
	 * @param string $hookname ��չ�ӿ�����
	 * @param string $template ģ������
	 * @param boolean $optimi �Ƿ�ģ�建�浽���ӵ�alias�� 
	 * @param array $args �����б�
	 * @return void
	 */
	public static function template($hookname, $template, $optimi = true) {
		$args = func_get_args();
		unset($args[0], $args[1], $args[2]);
		self::segment($template, array_values($args), $hookname, $optimi ? self::$__alias : '');
	}

	/**
	 * ����ģ��Ƭ�Σ�����ģ��Ƭ�η���name�����ļ���
	 * ģ���ļ��������������������ʽ��
	 * <pre>
	 * 1����һ�֣�
	 * <hook-action name="hook1" args='a,b,c'>
	 * <div>i am from hook1 {$a} |{$b}|{$c}</div>
	 * </hook-action>
	 * ���Ͻ��ᱻ����ɣ�
	 * function templateName_hook1($a, $b, $c){
	 * }
	 * 2���ڶ��֣�
	 * <hook-action name="hook2">
	 * <div> i am from hook2 {$data} </div>
	 * </hook-action>
	 * ���Ͻ������ɣ�
	 * function templateName_hook2($data){
	 * }
	 * 3�������֣�
	 * <div> i am from segment {$data}</div>
	 * ���Ͻ��ᱻ����ɣ�
	 * function templateName($data) {
	 * }
	 * 
	 * ģ���ǩ��
	 * <segment alias='' name='' args='' tpl='' />
	 * tpl�ļ��е�ģ�����ݰ����������ֹ��򱻱���֮�󣬽��ᱣ�浽__segment_alias�ļ���
	 * ���÷������ݣ�
	 * tpl_name������,���funcû��д������÷���Ϊtpl������Ϊtpl_func,�������Ϊargs
	 * </pre>
	 *
	 * @param string $template
	 * @param array $args
	 * @param string $func
	 * @param string $alias
	 * @param WindViewResolve $viewer
	 * @return 
	 */
	public static function segment($template, $args, $func = '', $alias = '', $viewer = null) {
		if ($viewer instanceof WindViewerResolver) self::$viewer = $viewer;
		$_prefix = str_replace(array(':', "."), '_', $template);
		$alias = '__segment_' . strtolower($alias ? $alias : $_prefix);
		list($templateFile, $cacheCompileFile) = self::$viewer->getWindView()->getViewTemplate(
			$template);
		$pathinfo = pathinfo($cacheCompileFile);
		$cacheCompileFile = $pathinfo['dirname'] . '/' . $alias . '.' . $pathinfo['extension'];
		$_method = strtoupper($func ? $_prefix . '_' . $func : $_prefix);
		if (WIND_DEBUG) {
			WindFolder::mkRecur(dirname($cacheCompileFile));
			WindFile::write($cacheCompileFile, '', WindFile::READWRITE);
		} else {
			if (!function_exists($_method) && is_file($cacheCompileFile)) {
				include $cacheCompileFile;
			}
			if (function_exists($_method)) {
				call_user_func_array($_method, $args);
				return;
			}
		}
		
		if (!$content = self::_resolveTemplate($templateFile, strtoupper($_prefix))) return;
		$_content = array();
		foreach ($content as $method => $_item) {
			$_tmpArgs = '';
			foreach ($_item[1] as $_k) {
				$_tmpArgs .= '$' . trim($_k) . ',';
			}
			$windTemplate = Wind::getComponent('template');
			$_content[] = '<?php if (!function_exists("' . $method . '")) {function ' . $method . '(' . trim(
				$_tmpArgs, ',') . '){?>';
			$_content[] = $windTemplate->compileStream($_item[0], self::$viewer);
			$_content[] = '<?php }}?>';
		}
		
		WindFolder::mkRecur(dirname($cacheCompileFile));
		WindFile::write($cacheCompileFile, implode("\r\n", $_content), WindFile::APPEND_WRITE);
		include $cacheCompileFile;
		call_user_func_array($_method, $args);
	}

	/**
	 * ���ָ����չ���ȫ����չ����
	 * 
	 * ���˵�ǰ���ڵ�����{@see $filters},����filter�ı��ʽ������˵�ǰ��Ҫ��ִ�е�����filter,
	 * ������.�÷�����{@see PwBaseController::runHook}�б�ʹ��.��ǰ���ʽ��������֧��,
	 * <i>request,service(PwBaseHookService)</i>{@example <code>
	 * ����: 'expression' => 'special.get==1'
	 * ��ø÷�����ȥ�ж�request,get�����е�special��ֵ�Ƿ����1,
	 * ���Ϊtrue��ע��ù�����,���Ϊfalse��ע��ù�����,��expression������ʱ,
	 * ����Ϊ���κ������¶�ע��ù�����.
	 * </code>}
	 * @param array $filters
	 * @param PwBaseHookService $service
	 * @return array
	 */
	public static function resolveActionHook($filters, $service = null) {
		$_filters = array();
		foreach ((array) $filters as $filter) {
			if (empty($filter['class'])) continue;
			if (!is_file(Wind::getRealPath($filter['class']))) continue;
			if (!empty($filter['expression'])) {
				$v1 = '';
				list($n, $p, $o, $v2) = WindUtility::resolveExpression($filter['expression']);
				switch (strtolower($n)) {
					case 'service':
						$call = array($service, 'getAttribute');
						break;
					case 'config':
						$call = array(self, '_getConfig');
						break;
					case 'global':
						$call = array('Wekit', 'getGlobal');
						break;
					default:
						$call = array(self, '_getRequest');
						break;
				}
				$v1 = call_user_func_array($call, explode('.', $p));
				if (!WindUtility::evalExpression($v1, $v2, $o)) continue;
			}
			$_filters[] = $filter;
		}
		return $_filters;
	}

	/**
	 * @param string $key
	 * @param string $method get/post
	 * @return mixed
	 */
	private static function _getRequest($key, $method = 'get') {
		if (!$key) return '';
		switch (strtolower($method)) {
			case 'get':
				return Wind::getApp()->getRequest()->getGet($key);
			case 'post':
				return Wind::getApp()->getRequest()->getPost($key);
			default:
				return Wind::getApp()->getRequest()->getRequest($key);
		}
	}

	/**
	 * ��ȡ��������Լ��
	 */
	private static function _getConfig($var) {
		if (func_num_args() > 1) {
			$args = array_slice(func_get_args(), 1);
			return Wekit::C($var, implode('.', $args));
		}
		return '';
	}

	/**
	 * ����ģ�����ݲ�����
	 * 
	 * �������ģ�����ݽ���Ϊ��������{@example <pre>
	 * ����ģ�����ݽ�����Ϊ:
	 * <hook-action name="testHook" args='a,c'>
	 * <div>
	 * hi, i am testHook
	 * </div>
	 * </hook-action>
	 * <hook-action name="testHook1">
	 * <div>
	 * hi, i am testHook
	 * </div>
	 * </hook-action>
	 * 
	 * $content = array(
	 * 'testHook' => array('content', array('a','c')),
	 * 'testHook1' => array('content', array('data'))
	 * );
	 * </pre>}
	 * @param string $template
	 * @return array
	 */
	private static function _resolveTemplate($template, $_prefix) {
		if (false === ($content = WindFile::read($template))) throw new PwException(
			'template.path.fail', 
			array('{parm1}' => 'wekit.engine.hook.PwHook._resolveTemplate', '{parm2}' => $template));
		
		self::$methods = array();
		$content = preg_replace_callback('/<(\/)?hook-action[=,\w\s\'\"]*>(\n)*/i', array(self, '_pregContent'), $content);
		$content = explode("</hook-action>", $content);
		$_content = array();
		$_i = 0;
		//�����ģ����ֻ��һ��Ƭ��û��ʹ��hook-action����÷�����������Ϊ��ģ�����ƣ����ܵĲ���Ϊ$data
		if (count(self::$methods) == 0) {
			$_content[$_prefix] = array($content[0], array('data'));
		} else {
			$_i = 0;
			foreach (self::$methods as $method) {
				$key = $method['name'] ? $_prefix . '_' . strtoupper($method['name']) : $_prefix . '_' . ($_i + 1);
				$args = $method['args'] ? explode(',', $method['args']) : array('data');
				$_content[$key] = array($content[$_i], $args);
				$_i++;
			}
		}
		return $_content;
	}

	/**
	 * ����hook-action��ǩ�е�����
	 * 
	 * �ñ�ǩ֧���������ԣ��ֱ��ǣ�
	 * <ul>
	 * <li>name: ��������ĸ�Ƭ�ε�function����</li>
	 * <li>args: �������Ƭ���н��ܵĲ�����ȱʡ������½���ʹ��data��Ϊ����</li>
	 * </ul>
	 * PwHook::$methods��ÿһ��Ԫ�ض�����name��args������Ԫ��
	 * 
	 * @param string $content
	 * @return string
	 */
	private static function _pregContent($content) {
		if (isset($content[1]) && $content[1] == '/') return "</hook-action>";
		preg_match('/(?<=name=([\'\"]))(.*?)(?=\1)/ie', $content[0], $match1);
		preg_match('/(?<=args=([\'\"]))(.*?)(?=\1)/ie', $content[0], $match2);
		self::$methods[] = array('name' => $match1[0], 'args' => $match2[0]);
		return '';
	}
}
?>