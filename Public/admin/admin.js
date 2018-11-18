(function (j,$,layui) {
	//公用 
	//原生js
	j.$=function(id) { return document.getElementById(id);}
	j._GET=function(name) { 
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
		var r = window.location.search.substr(1).match(reg); 
		if (r != null) return unescape(r[2]); return null; 
	}
	j.htmlspecialchars=function(str){
		if(str==''){
			str = str.replace(/&/g, '&amp;');  
			str = str.replace(/</g, '&lt;');  
			str = str.replace(/>/g, '&gt;');  
			str = str.replace(/"/g, '&quot;');  
			str = str.replace(/'/g, '&#039;');  
		}
		return str;  
	};
	j.param=function(k,v){//存取参数
		k='params_'+k;
		return v==undefined ? localStorage.getItem(k) : localStorage.setItem(k, v);
	}
	j.session=function(k,v){//存取localStorage
		return v==undefined ? sessionStorage.getItem(k) : sessionStorage.setItem(k,v);
	}
	j.int100=function(num){//把文字转为数字*100  便于比较大小  失败返回0
		if(num==0 || num==null || isNaN(num)) return 0; else return parseInt(parseFloat(num)*100);
	};
	j.div100=function(num){//把数字/100
		if(num==0 || num==null || isNaN(num)) return 0; else return num/100;
	};
	j.int=function(num){
		if(num==0 || num==null || isNaN(num)) return 0; else return parseInt(num);
	};
	j.float=function(num){
		if(num==0 || num==null || isNaN(num)) return 0; else return parseFloat(num);
	};
	j.dateAdd=function(timestamp){//加多少秒
		var date=new Date().getTime()+timestamp*1000;
		date=new Date(date);
		return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()
	};
	//主要用于编码中文或其它参数用
	j.euc=function ($param){
		return encodeURIComponent($param);
	};	
	j.isWap=function(){//是否手机
		return is_wap=(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))return 1;else return 0;})(navigator.userAgent||navigator.vendor||window.opera);
	};
	j.isRequired = function() {return 'required' in document.createElement('input');}; 
	j.isWebkit = function() {return document.body.style.WebkitBoxShadow !== undefined;}; 
	j.isIE=function(){if(!!window.ActiveXObject || "ActiveXObject" in window) return true;return false;};
	/**********需要jquery等库********/
	j.maxZIndex=function(){
		return j.getMaxZIndex();
		return Array.from(document.all).reduce(function(m, i){return Math.max(m, +window.getComputedStyle(i).zIndex || 0)}, 0);
	};
	j.getMaxZIndex=function () {
		var maxZ = Math.max.apply(null, 
		　　$.map($('body *'), function(e,n) {
		  　　if ($(e).css('position') != 'static')
			　　return parseInt($(e).css('z-index')) || -1;
		}));
		return maxZ;
	};
	j.ajaxStart=function(){
		if(j.NoAjaxLoading)return;	
		$('#css3-spinner').css('z-index',j.maxZIndex()+1).show();
	};	
	j.ajaxSuccess=function(){if(j.NoAjaxLoading)return;setTimeout(function(){$('#css3-spinner').hide();} ,300);};
	j.layer_msg=function($msg,$maxWidth,$timeout){
		layui.use('layer', function(layer){setTimeout(function(){layer.msg($msg,{maxWidth:$maxWidth ? $maxWidth: 180});},$timeout===undefined ? 0  :$timeout);});
	};	
	j.scrollToEnd=function(){//滚动到底部
		$(document).scrollTop($(document).height()-$(window).height()); 
	}	
	j.photo=function(id){
		layui.use('layer', function(){
			layui.layer.photos({
				photos: id
				,anim: 5
			}); 
		});	
	};
	//判断是否是url
	j.is_url=function(url){
		return url.search(/^https*:\/\//)>-1;
	};
	//在给定元素中创建input,选中复制,移除
	j.copy=function(str,obj){
		var txt = document.createElement("INPUT");
		txt.value = str;
		txt.style.width =  "12px" ;
		obj.appendChild(txt);
		txt.focus();
		txt.select();
		document.execCommand("Copy");
		obj.removeChild(txt);
		j.layer_msg('复制成功');
	};	
	j.fuzhi=function(){
		if($('#fuzhi').length)
		$("#fuzhi").click(function () {
		  var e = document.getElementById("turl");
		  e.select();
		  document.execCommand("Copy");
		  j.layer_msg('复制成功');
		});
		if($('#fuzhi1').length)
		$("#fuzhi1").click(function () {
		  var e = document.getElementById("turl1");
		  e.select();
		  document.execCommand("Copy");
		  j.layer_msg('复制成功');
		});
	};
	j.qcode=function(){
		if($('#ad_qcode').length)
			$('#ad_qcode').qrcode({
				text: $("#turl").val(),
				width: 120, 
				height: 120,
				background: "#fff",
				foreground: "black" 
			});
		if($('#ad_qcode1').length)
			$('#ad_qcode1').qrcode({
				text: $("#turl1").val(),
				width: 120, 
				height: 120,
				background: "#fff",
				foreground: "black" 
			});
	};
	j.tooltip=function($eml,$title){
		if(j.tooltip_lock===undefined || j.tooltip_lock===0)
			layui.use('layer', function(layer){
				layer.tips($title, $eml,{tips:[1,'#3595CC'],time:4000});j.tooltip_lock=1;
				setTimeout(function(){j.tooltip_lock=0;},1000);
			});
	};	
	j.reload=function(){
		setTimeout(function(){
			location.reload();
		},1000);
	};
	j.win=function(that,id){//在h+打开子窗口
		$(id,parent.document)[0].href=that.href;
		$(id,parent.document)[0].click();
		return false;
	};
	j.minWin=function(url,title,wh){
		layui.use('layer', function(){
			layer.open({
			  type: 2,
			  title: title,
			  skin: 'layui-layer-rim',
			  shadeClose: true,
			  shade: 0.5,
			  maxmin: true, //开启最大化最小化按钮
			  area: wh || ['893px', '500px'],
			  content: url
			});	
		});
	};
	/**********后台用********/
	j.doUserInfo=function(that){
		$.post(j.root+"?m=Pc&a=ajaxDoUserInfo&thin=200",$(that).serialize(),function(data){
			j.layer_msg(data.data);
		},'json');
	};
	j.logout=function(){
		$.post(j.root+"/Admin/Index/ajaxLogout",function(data){
			if(data.status){
				var msg='退出登陆成功';
				setTimeout(function(){location=j.root+'/Admin/index/login.html';},800);
			}else  	var msg='退出登陆失败';			
			j.layer_msg(msg);
		},'json');
	};
	//自动调整iframe调试
	j.setIframeHeight=function(iframe) {
		if (iframe) {
			var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
			if (iframeWin.document.body) {
				iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
			}
		}
	};
	j.upload=function(that){
		jQuery(that).parent().prev('.fileToUpload').click();
	};
	j.fileUpload=function(id){//上传图片
		id.fileUpload({
			"url": j.root+"?m=Light&a=ajaxUploadPic&thin=200",
			"is_multi"	: "false",
			"is_del"	: "false",
			"file"		: "myFile",
			"preComplete":function(evt){
				if(evt.target.responseText==''){
					j.layer_msg('上传失败,请重试');
					return false;
				}
				return true;
			},
			"complete":function(that){
				$(that).find('.upload_progress').hide();
			}
		});
	};
	$(function(){
		$(self).ajaxStart(function(){j.ajaxStart();});
		$(self).ajaxError(function(){j.ajaxSuccess();});
		$(self).ajaxStop(function(){j.ajaxSuccess();});
		$(self).ajaxSuccess(function(){j.ajaxSuccess();});
	});
	/**********本站用********/
})(jetee,jQuery,layui);

if(self!=top){//子窗口  检查帐户变动刷新
	var $account_cookie=$.cookie('account');
	var mySetInterval=setInterval(function(){
		if($account_cookie!=$.cookie('account')){
			clearInterval(mySetInterval);
			setTimeout(function(){location.reload();},Math.ceil(Math.random()*1500));
		}
	},150);
}

