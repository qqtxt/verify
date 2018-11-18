//封装聊天
var jChat={
	imInitLock:false//初始化im锁定中
	,imHeartOn:false//心跳是否开启
	,imOk:false		//是否已初始化im
	,ws:{readyState:0}		//连接对象
	,data:{			//实际未用,解说用  发送（与接收）对象
		 type:0		//传送类型 1通报自己uid  2传输消息用户向服务器或相反  3检查自己4检查对方是否在线 
		,uid:0		//uid   通报上线时用
		,mine:null	//mine对象   通报上线为空
		,to:null	//to对象     通报上线为空
	}
	,jsonSend:function(data){
		console.log(data);
		this.ws.send(JSON.stringify(data));
	}
	,createSocket:function(me){//用户上线通报自己uid  创建连接
		that=this;
		this.ws=new WebSocket("wss://zc.ma863.com:2848");
		//连接时用户上线  通报自己上线 uid  
		this.ws.onopen = function() {
			if(!that.imHeartOn) that.heart();
			that.jsonSend({type:1,uid:me});//通报自己
			//that.isConnected(20,jetee.to);//检查客服是否在线
		};
		this.ws.onmessage = function(e) {//消息监控
			var msg=JSON.parse(e.data);
			if(msg.type && msg.type==4){
			   if(msg.status==0){
				   layui.use('layer', function(){
					   layui.layer.msg('对方现在不在线，上线后可以看到留言。');
				   });
			   }
			   return;
			}
			//console.log(msg);
			that.showMessage(msg);
	   };
	}
	/**
	*26秒1次心跳
	*/
	,heart:function() {
		var that=this;
		if(that.ws.readyState==1){
			that.imHeartOn=true;
			that.jsonSend({type:5});
			setTimeout(function(){
				that.heart();
			},26000);
		}else{
			that.imHeartOn=false;
		}
	}
	/**
	* 发送信息到服务器中转  如果连接没创建先创建
	* @param arr mine 	mine对象
	* @param arr to 	to对象
	* @return boolean
	*/
	,sendMessage:function(mine,to) {
		//如果服务器未连接 每隔二秒重连 重发
		if(this.ws.readyState!=1){
			this.createSocket(mine.id);
			that=this;
			setTimeout(function(){
				that.sendMessage(mine,to);
			},2000);
			return false;
		}
		//判断用户是否登陆
		if(parseInt(mine.id)!=mine.id || mine.id==0){
		   layui.use('layer', function(){
			   layui.layer.msg('发送失败，请先登陆！');
		   });
			return false;
		}
		that.jsonSend({type:2,mine:mine,to:to});
		return true;
	}
	/**
	* 检查uid是否能接收信息 与在线有区别
	* @param int $type  3检查自己  4检查客服
	* @param str $to    uid  
	* @return boolean
	*/
	,isConnected:function(type,mine,to){
		that.jsonSend({type:type,mine:mine,to:to});
	}
	/**
	* 取后端数据 显示聊天  如果为客服要特殊处理
	* @param str $msg 	layim,一条聊天记录
	* @return boolean
	*/
	,showMessage:function(msg){
		layui.use('layim', function(layim){
			layim.getMessage(msg);
		});
	}
	/**
	* 初始化im 成功后会创建websocket
	* @param str username,avatar,id		对方id等
	* @return boolean
	*/	
	,initIm:function(){
		if(!j.chk_show_login() || this.imInitLock) return;//锁定中说明正在初始化
		this.imInitLock=true;
		that=this;
		layui.use('layim', function(layim){
			var quick_html='';
			layim.on('ready', function(res){
				that.imInitLock=false;
				that.imOk=true;
				$.get(j.root+'?m=Light&a=ajaxGetQuickReply&thin=200',function(data){//get取快捷回复
					layui.each(data,function(index,item){
						quick_html+='<li class="quick">'+item+'</li>'; 
					});
				},'json');
				var cache =  layui.layim.cache();					
				if(that.ws.readyState!=1)	that.createSocket(cache.mine.id);
			});
			//如果登陆用户为客服 取最近联系
			layim.config({
				init: 		  {url: j.root+'?m=Light&a=ajaxGetLastChat&thin=200'}
				,tool: 		  [{alias:'quick',title:'快捷回复',icon:'&#xe64e;'}]//扩展工具栏
				,uploadImage: {url:j.root+'?m=Light&a=ajaxUploadImage&thin=200',type: 'post'}//上传图片接口
				,uploadFile:  {url:j.root+'?m=Light&a=ajaxUploadFile&thin=200',type: 'post'}//上传文件接口
				,copyright:   true
				,title:       '我的IM'
			});

			//监听发送消息
			layim.on('sendMessage', function(data){
				return jChat.sendMessage(data.mine,data.to);
			});

			//监听快捷回复
			layim.on('tool(quick)',function(insert,send){
				layer.open({
					title: '快捷回复'
					,content: '<ul>'+quick_html+'</ul>'
					,btn:0
					,success:function(layero,index){
						$(layero).find(".quick").on("click",function(){
							insert($(this).text());//把内容保存到对话框
							send();//立即发送
							layer.close(index); //关闭弹窗
						});
					}
				});   
			});

		});
	}
	/**
	* 创建与谁的聊天面板(如果已创建打开窗口)  未初始化im，会先初始化
	* @param str username,avatar,id		对方id等
	* @return boolean
	*/	
	,chat:function(username,avatar,id){
		if(!j.chk_show_login()) return;
		if(!this.imOk){//未初始化
			this.initIm();
			setTimeout(function(){jChat.chat(username,avatar,id);},100);
			return;
		}
		layui.use('layim', function(layim){
			//打开聊天面板
			layim.chat({
				name:  username
				,type: 'friend' //聊天类型
				,avatar:avatar 
				,id:id
			});
		});
	}
	/**
	* 未初始化im,检查   有新消息初始化im,初始化过程创建websocket连接，自动接收消息
	*/	
	,chkChatNew:function(){
		if(this.imOk || !j.chk_show_login(false)) return;
		j.ajaxStatus=false;
		$.get(j.root+'?m=Light&a=ajaxChkChatNew&thin=200',function(data){
			if(data.status)
				jChat.initIm();
		},'json');
	}
};
//导入im的地方 自动检查新消息
$(function(){
	setTimeout(function(){jChat.chkChatNew();},3000);
});




 