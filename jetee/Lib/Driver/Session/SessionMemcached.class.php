<?php
defined('JETEE_PATH') or exit();
/*还没有测试*/
class SessionMemcached// extends SessionHandler
{
    protected $handler = null;
    protected $config  = [
        'host'         => '127.0.0.1', // memcache主机
        'port'         => 11211, // memcache端口
        'expire'       => 3600, // session有效期
        'timeout'      => 1, // 连接超时时间（单位：毫秒）
        'session_name' => '', // memcache key前缀
        'username'     => '', //账号
        'password'     => '', //密码
    ];

    public function __construct()
    {
		$this->config['host']=C('MEMCACHE_HOST');
		$this->config['port']=C('MEMCACHE_PORT');
		$this->config['expire']=ini_get('session.gc_maxlifetime');
		$this->config['timeout']= 1;
		$this->config['username']=C('MEMCACHE_USERNAME');
		$this->config['password']=C('MEMCACHE_password');
    }
    /**
     * 打开Session
     * @access public
     * @param string    $savePath
     * @param mixed     $sessName
     */
    public function open($savePath, $sessName)
    {

        $this->handler = new \Memcached;
        // 设置连接超时时间（单位：毫秒）
        if ($this->config['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->config['timeout']);
        }
        // 支持集群
        $hosts = explode(',', $this->config['host']);
        $ports = explode(',', $this->config['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array) $hosts as $i => $host) {
            $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
        }
        $this->handler->addServers($servers);
        if ('' != $this->config['username']) {
            $this->handler->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $this->handler->setSaslAuthData($this->config['username'], $this->config['password']);
        }
        return true;
    }


    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->handler->quit();
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
		return (string) $this->handler->get($this->config['session_name'] . $sessID);
   }

    /**
     * 写入Session
     * @access public
     * @param string    $sessID
     * @param String    $sessData
     * @return bool
     */
    public function write($sessID, $sessData)
    {
		return $this->handler->set($this->config['session_name'] . $sessID, $sessData, $this->config['expire']);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     * @return bool
     */
    public function destroy($sessID)
    {
        return $this->handler->delete($this->config['session_name'] . $sessID);
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     * @return true
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
