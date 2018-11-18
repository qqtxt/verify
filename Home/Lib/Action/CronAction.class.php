<?php
defined('JETEE_PATH') or exit();
require_once(APP_PATH.'Lib/Vendor/ipip.class.php');

/**
* 轻量级pc端控制器  处理不用后台设置，不用登陆 不用模板引擎     这个控制器cli与thin都可以进
* @version 1.0.1 11:44 2016/5/28 
*/
class CronAction{
	/*配置 常驻内存*/
	private $url='';//同步数据到临时表的网址
	private $num=2000;//同步数量
	private $dealNum=2000;//处理时一次读多少条到内存处理  暂时等于同步数量  优化用到
	private $ipNum=100;//批量读取处理ip数
	private $delay=3;//延3秒钟 取数据
	private $lurl=array();//落地页
	private $ip=null;//取ip地址
	//计划任务
	public function index(){ //index.php trigger/1981  cli模式或 ?trigger=1981 thin模式进入
		common_config();
		/*配置 常驻内存*/
		$this->url=C('server_statistics');
		initCache();//使缓冲句柄常驻内存
		$this->ip=new Reader(APP_PATH.'Lib/Vendor/ip.ipdb');
		
		$cron_check=APP_PATH.'Runtime/Data/cron_check.txt';
		//记录启动时间  跟踪启动次数防重复启动
		file_put_contents(APP_PATH.'Runtime/Data/cron_start_check.txt',date('Y-m-d H:i:s')."\r\n",FILE_APPEND);
		//设置
		$sleep=10;//程序睡眠时间=10-运行时间(秒)  要和trigger_cron里的匹配  最短不休眠，最长休眠时间10
		if(PHP_SAPI!='cli'){
			ignore_user_abort(true);
			set_time_limit(0);
		}
		while(file_exists($cron_check) && file_get_contents($cron_check)!='exit'){
			file_put_contents($cron_check,$start=time());
			//执行任务
			//可以创建子进程
			if(PHP_SAPI=='cli' && function_exists('pcntl_fork')){
				$pid = pcntl_fork(); //创建子进程
				//父进程和子进程都会执行下面代码
				if ($pid == 0) {//子进程内为0
				
					$pid1 = pcntl_fork(); //创建子进程   多开条子线程处理ip 完结退出
					if($pid1 == 0){
						exit;
					}
					
					//子进程内执行计划任务
					G('start');
					$this->task();
					G('end');
					echo "\n".'['.$start.'|'.G('start','end',8).'|'.G('start','end','m').'|'.G('loadTime','end','m').']';
					exit;
				}elseif($pid == -1){//错误处理：创建子进程失败时返回-1.
					Log::write('创建子进程失败','ERR',3,'corn');
				}else{//主进程内pid为子进程id
					$pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
					if(!pcntl_wifexited($status)){//检查状态代码是不正常退出
						Log::write('子进程不正常退出','ERR',3,'corn');
					}					
				}
			}else{//执行计划任务
				try{
					G('start');
					$this->task();	
					
					G('end');
					if(PHP_SAPI=='cli'  && defined('APP_DEBUG'))echo "\n".'{task '.$start.'|'.G('start','end',8).'|'.G('start','end','m').'|'.G('loadTime','end','m').'}'."\n";
				}catch(Exception $e){file_put_contents('error.log',print_r($e,true),FILE_APPEND);}
			}
			//只睡眠$sleep-执行任务所发时间  执行时间超过睡眠时间不睡
			$spend=time()-$start;
			if($sleep-$spend>0){
				sleep($sleep-$spend);
			}
		}
	}	
	
	
	public function task(){//可以单独运行  运行时锁定   不会重复运行，多线程安全
		$cron_lock=intval(S('task_lock'));$now=time();
		if(PHP_SAPI=='cli'  && defined('APP_DEBUG'))echo "\n last lock time:".$cron_lock.' '.date('Y-m-d H:i:s',$cron_lock);
		if($cron_lock==0 || $cron_lock+300<$now){//防止出错  超过300秒直接运行
			if($cron_lock>0)Log::write('任务锁定超时，上次运行时间:'.date('Y-m-d H:i:s',$cron_lock),'ERR',3,'corn');
			S('task_lock',$now);
			
			//任务开始
			

			//任务结束
			S('task_lock',0);
		}
		
	}
	
}