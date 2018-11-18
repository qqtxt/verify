/**
*js基础函数
* @link http://www.jetee.cn/
* @author jetee										
* @return void
* @version 0.0.1 11:09 2014/11/26
*/
j.empty=function( val )
{
  switch (typeof(val))
  {
    case 'string':
      return j.trim(val).length == 0 ? true : false;
      break;
    case 'number':
      return val == 0;
      break;
    case 'object':
		if(j.is_array(val)){
			return val.length ==0;
		}
		return val == null;
		break;
    case 'boolean':
      return val===false;
      break;
    default:
      return true;
  }
}
j.is_array = function(obj) { 
	return Object.prototype.toString.call(obj) === '[object Array]'; 
} 
j.trim=function( text )
{
  if (typeof(text) == "string")
  {
    return text.replace(/^\s*|\s*$/g, "");
  }
  else
  {
    return text;
  }
}
j.$=function(id){
	return document.getElementById(id);
}
j.post=function(url,data,callback,type){
	if(typeof(data)=='undefined' || data==''){
		data='is_ajax=1';
	}else if(typeof(data)=='string'){
		data+='&is_ajax=1';
	}else{
		data['is_ajax']=1;
	}
	return $.post(j.root+'/'+url,data,callback,type);	
}
 
//主要用于编码中文或其它参数用
j.euc=function ($param){
	return encodeURIComponent($param);
}
j.maxZIndex=function(){return Array.from(document.all).reduce(function(m,i){return Math.max(m,+window.getComputedStyle(i).zIndex||0)},0);}

 /**
*js基础函数 end
*/
j.ajaxStart=function(){
	if(j.NoAjaxLoading)return;
	//取最大z-index
	var max=j.maxZIndex();
	//隐藏
	if($)$('#css3-spinner').css('z-index',max+1).show();
};	
j.ajaxSuccess=function(){if(j.NoAjaxLoading)return;setTimeout(function(){if($)$('#css3-spinner').hide();} ,1000);};
			
/**
 * ajax post访问后 artDialog显示 
<script type="text/javascript" src="Public/dialog-min604.js"></script>
<link href="Public/dialog-min604.css" rel="stylesheet" />
 * @param url 	
 * @param data 发送数组 或串  name=qqtxt&pass=123
 * @param func onshow回调  显示出后执行
 * 返回的 data.follow_id  跟随的id决定是否为气泡
 * @return 返回json data={content:'',title:'',...}
 */
j.post_dialog=function (url,data,func){
	var d=dialog({
		id: 'loading',
		padding: 0,
		skin: 'loading',
		content:'<img style="width:32px;" src="'+j.images+'/loading.gif"/>'
	}).show();
	var out=setTimeout(function () {
		d.close().remove();
	}, 30000);
	data==undefined || data=='' ?data='':'';
	func==undefined || func=='' ?func=function(){}:'';
	j.post(url,data,function(data) {
		//消除loadding
		try{clearTimeout(out);}catch(err){}
		try{d.close().remove();}catch(err){}
		//组合参数
		var param={onshow:func};
		//记录返回参数供 onshow用
		j.post_dialog_data=data;
		for(i in data){
			param[i]=data[i];
		}
		//分为两种气泡 表单 bubble_dialog   form_dialog   气泡跟随的id根据返回的follow_id决定
		if(typeof(param['follow_id'])=='undefined'){
			try{j.dialog['form'].remove();}catch(e){}//先关已有的
			param['id']='form_dialog';
			follow_id=false;
		}else{
			try{j.dialog['bubble'].remove();}catch(e){}//先关已有的
			param['id']='bubble_dialog';
			follow_id=j.$(param.follow_id);
			delete param.follow_id;
		}
		if($.trim(data.title)==''){
			delete param.title;
		}
		if(param.showModal){
			delete param.showModal;
			j.dialog[follow_id ? 'bubble' :'form']=dialog(param).showModal(follow_id);
		}else j.dialog[follow_id ? 'bubble' :'form']=dialog(param).show(follow_id);
	},'json');
}

/**
*气泡提示
*把选中对象加上气泡提示 内容为attr('bubble_dialog')
* @link http://www.jetee.cn/
* @author jetee					
* @param jquery对象  $id
* @return dialog对象 
* @version 0.0.1 16:37 2014/11/21
*/
j.bubble_dialog=function($id){
	$id.focus(function(){
		try{j.dialog['bubble'].remove();}catch(e){}//先关已有的
		var $t=$(this);
		if(1 || $t.val()==''){
			j.dialog['bubble']=dialog({content:$t.attr('bubble_dialog'),quickClose:1,autofocus:0,id:'bubble_dialog'}).show($t[0]);
		}
	});
	$id.keydown(function(){
		try{j.dialog['bubble'].remove();}catch(e){}//关已有的
	});
	$id.blur(function(){
		try{j.dialog['bubble'].remove();}catch(e){}//关已有的
	});
}

/**
*气泡提示
*把选中对象加上气泡提示 内容为attr('bubble_dialog')
* @link http://www.jetee.cn/
* @author jetee					
* @param jquery对象  $id
* @return dialog对象 
* @version 0.0.1 16:37 2014/11/21
*/
j.bubble_dialog_tip=function($id){
	$id.mouseover(function(){
		//try{j.dialog['bubble'].remove();}catch(e){}//先关已有的
		var $t=$(this);
		if(!j.dialog['bubble']){
			j.dialog['bubble']=dialog({content:$t.attr('bubble_dialog'),autofocus:0,id:'bubble_dialog',padding:10,skin:'j_bubble_dialog_tip'}).show($t[0]);
		}
	});
	$id.keydown(function(){
		try{j.dialog['bubble'].close().remove();j.dialog['bubble']=false;}catch(e){}//关已有的
	});
	$id.mouseout(function(){
		try{j.dialog['bubble'].close().remove();j.dialog['bubble']=false;}catch(e){}//关已有的
	});
}





j.cookie={
	set:function(name,value,expires,path,domain){
		if(typeof expires=="undefined"){
			expires=new Date(new Date().getTime()+1000*3600*24*365);
		}
		
		document.cookie=name+"="+escape(value)+((expires)?"; expires="+expires.toGMTString():"")+((path)?"; path="+path:"; path=/")+((domain)?";domain="+domain:"");
		
	},
	get:function(name){
		var arr=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
		if(arr!=null){
			return unescape(arr[2]);
		}
		return null;
	},
	clear:function(name,path,domain){
		if(this.get(name)){
		document.cookie=name+"="+((path)?"; path="+path:"; path=/")+((domain)?"; domain="+domain:"")+";expires=Fri, 02-Jan-1970 00:00:00 GMT";
		}
	}
}

/**
*单独显示一个
* @param param  参数
* @version 0.0.1 14:35 2014/12/23
*/
j.showModal=function(param){
	try{j.dialog['form'].close().remove();}catch(e){}//先关已有的	
	j.dialog['form']=dialog(param).showModal();
}
/**
*单独显示一个
* @param param  参数
* @version 0.0.1 14:35 2014/12/23
*/
j.showDialog=function(param){
	try{j.dialog['form'].close().remove();}catch(e){}//先关已有的	
	j.dialog['form']=dialog(param).show();
}

/**
*单独显示一个
* @param param  参数
* @version 0.0.1 14:35 2014/12/23
*/
j.showBubble=function(id,content){	
	try{j.dialog['bubble'].remove();}catch(e){}//先关已有的
	//id=j.$(id);
	j.dialog['bubble']=dialog({content:content,quickClose:1,autofocus:0,id:'bubble_dialog',padding:10,skin:'j_bubble_dialog_tip'}).show(id);
}

//W3C的stopPropagation()  IE的window.event 阻止冒泡
j.stopBubble=function(){
	var e = arguments.callee.caller.arguments[0] || window.event; 
	window.event?e.cancelBubble=true:e.stopPropagation(); 
}

//预加载
j.load_image=function(url){ 
  var img=new Image(); 
   img.src=url; 
}

/**
*指向头像显示认证情况
* @link http://www.jetee.cn/
* @author jetee					
* @param jquery对象  $id
* @return dialog对象 
* @version 0.0.1 16:01 2015/6/16
*/
j.bubble_cert=function($id){
	$id.mouseover(function(){
		var $t=$(this),$uid=$t.attr('uid');
		if(!j.dialog['bubble']){
			//根据用户uid获取认证
			j.post('Light/bubble_cert',{uid:$uid},function(data){
				j.dialog['bubble']=dialog({content:data,autofocus:0,id:'bubble_dialog',padding:10,skin:'bubble_cert'}).show($t[0]);
			});
		}
	});
	$id.keydown(function(){
		try{j.dialog['bubble'].close().remove();j.dialog['bubble']=false;}catch(e){}//关已有的
	});
	$id.mouseout(function(){
		try{j.dialog['bubble'].close().remove();j.dialog['bubble']=false;}catch(e){}//关已有的
	});
}


j.photo=function(id){
	layui.use('layer', function(){
		layui.layer.photos({
			photos: id
			,anim: 5
		}); 
	});	
};
j.user=function(id){
	j.win(j.root+'/Admin/Buyer/lists/keyword/'+id,'查看用户信息');	
};
j.shop=function(id){
	j.win(j.root+'/Admin/User/lists/keyword/'+id,'查看用户信息');	
};
j.task=function(id){
	j.win(j.root+'/user/task_info.html?tid='+id,'查看任务信息');	
};
j.order=function(id){
	j.win(j.root+'/user/order_info.html?toid='+id,'查看订单信息');	
};
j.view_order=function(id){
	j.win(j.root+'/user/view_order_info.html?toid='+id,'查看订单信息');	
};
j.process=function(id){
	j.win(j.root+'/Admin/Appeal_process/lists/aid/'+id,'申诉进程');	
};
j.win=function(url,title){
	layui.use('layer', function(){
		layer.open({
		  type: 2,
		  title: title,
		  shadeClose: true,
		  shade: false,
		  maxmin: true, //开启最大化最小化按钮
		  area: ['893px', '600px'],
		  content: url
		});	
	});
};