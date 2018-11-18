<?php
defined('JETEE_PATH') or exit();
/*还没有测试*/
class SessionRedis//extends SessionHandler
{
	/** @var \Redis */
	protected $handler = null;
	protected $config  = array(
		/*'host'         => '127.0.0.1', // redis主机
		'port'         => 6379, // redis端口
		'password'     => '', // 密码
		'expire'       => 3600, // 有效期(秒)
		'timeout'      => 0, // 超时时间(秒)
		'persistent'   => true, // 是否长连接*/
		'select'       => 0, // 操作库
		'session_name' => 's_', // sessionkey前缀
	);

	public function __construct()
	{
		$this->config['host']=C('REDIS_HOST') ? C('REDIS_HOST') : '127.0.0.1';
		$this->config['port']=C('REDIS_PORT') ? C('REDIS_PORT') : 6379;
		$this->config['password']=C('REDIS_PASSWORD') ? C('REDIS_PASSWORD') : '';
		$this->config['expire']=ini_get('session.gc_maxlifetime');
		$this->config['timeout']= 0;
		$this->config['persistent']= true;

	}

	/**
	 * 打开Session
	 * @access public
	 * @param string $savePath
	 * @param mixed  $sessName
	 * @return bool
	 * @throws Exception
	 */
	public function open($savePath, $sessName)
	{
		$this->handler = new \Redis;
		// 建立连接
		$func = $this->config['persistent'] ? 'pconnect' : 'connect';
		$this->handler->$func($this->config['host'], $this->config['port'], $this->config['timeout']);

		if ('' != $this->config['password']) {
			$this->handler->auth($this->config['password']);
		}

		if (0 != $this->config['select']) {
			$this->handler->select($this->config['select']);
		}
		return true;
	}

	/**
	 * 关闭Session
	 * @access public
	 */
	public function close()
	{
		$this->handler->close();
		$this->handler = null;
		return true;
	}

	/**
	 * 读取Session
	 * @access public
	 * @param string $sessID
	 * @return string
	 */
	public function read($sessID)
	{
		return (string) $this->handler->get($this->config['session_name'] . $sessID);
	}

	/**
	 * 写入Session
	 * @access public
	 * @param string $sessID
	 * @param String $sessData
	 * @return bool
	 */
	public function write($sessID, $sessData)
	{
		if ($this->config['expire'] > 0) {
			return $this->handler->setex($this->config['session_name'] . $sessID, $this->config['expire'], $sessData);
		} else {
			return $this->handler->set($this->config['session_name'] . $sessID, $sessData);
		}
	}

	/**
	 * 删除Session
	 * @access public
	 * @param string $sessID
	 * @return bool
	 */
	public function destroy($sessID)
	{
		return $this->handler->delete($this->config['session_name'] . $sessID) > 0;
	}

	/**
	 * Session 垃圾回收
	 * @access public
	 * @param string $sessMaxLifeTime
	 * @return bool
	 */
	public function gc($sessMaxLifeTime)
	{
		return true;
	}
    /**
     * 打开Session 
     * @access public 
     */
    public function execute() {
        session_set_save_handler(array(&$this,"open"), 
                         array(&$this,"close"), 
                         array(&$this,"read"), 
                         array(&$this,"write"), 
                         array(&$this,"destroy"), 
                         array(&$this,"gc")); 
    }
}
