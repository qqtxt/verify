j=(function (j,$,layui) {	
	//二级数组，常用的数据库模板解析  返回html串
	layui.use('laytpl', function(laytpl){
		j.fetch=function(tpl,rows){
			return laytpl(tpl).render(rows);
		}

	});
	j.timeAgo=function(time, onlyDate){
      var that = this
      ,arr = [[], []]
      ,stamp = new Date().getTime() - new Date(time).getTime();
      
      //返回具体日期
      if(stamp > 1000*60*60*24*8){
        stamp =  new Date(time);
        arr[0][0] = that.digit(stamp.getFullYear(), 4);
        arr[0][1] = that.digit(stamp.getMonth() + 1);
        arr[0][2] = that.digit(stamp.getDate());
        
        //是否输出时间
        if(!onlyDate){
          arr[1][0] = that.digit(stamp.getHours());
          arr[1][1] = that.digit(stamp.getMinutes());
          arr[1][2] = that.digit(stamp.getSeconds());
        }
        return arr[0].join('-') + ' ' + arr[1].join(':');
      }
      
      //30天以内，返回“多久前”
      if(stamp >= 1000*60*60*24){
        return ((stamp/1000/60/60/24)|0) + '天前';
      } else if(stamp >= 1000*60*60){
        return ((stamp/1000/60/60)|0) + '小时前';
      } else if(stamp >= 1000*60*2){ //2分钟以内为：刚刚
        return ((stamp/1000/60)|0) + '分钟前';
      } else if(stamp < 0){
        return '未来';
      } else {
        return '刚刚';
      }
    };
	
	j.submitForm=function(act,form,id){//通用提交表单
		if(!j.chk_show_login()) return;
		$.post(j.root+"?m=Light&a="+act+"&thin=200",$(form).serialize(),function(data){
			if(data.status){
				$(id).modal('hide');
			}
			j.layer_msg(data.data);
		},'json');
	};

	
	//获取链接或文字二维码
	j.qcode=function(elm,text,width,height){//如 j.qcode('#elm','http://www.ma863.com',150,150);
		$(elm).qrcode({
			text: text,
			width: width, 
			height: height,
			background: "#fff",
			foreground: "black" 
		});
	};

	
	j.update_verify=function(that){//更新验证码
		$(that).attr('src','index.php?m=Light&a=verify_cod&thin=200&radom='+Math.floor(Math.random()*100000));
	}

	j.chk_show_login=function(show){//检查登陆标记 如果未登陆显示登陆对话框
		if(!j.is_login){
			if(show===undefined || show){//未定义 或
				j.login();
				j.layer_msg('您还未登陆,请先登陆');
			}
			return false;
		}
		return true;
	}
	j.doReg=function(){//注册
		$.post(j.root+"?m=Light&a=ajaxDoReg&thin=200",$("#reg_form").serialize(),function(data){
			if(data.status){
				location=j.root+"/user/center";
				return;
			}
			j.layer_msg(data.data);
		},'json');
	};
	j.checkPassword=function(that) {
		if (that.value != $("#password").val()){
			that.oninvalid();
			//j.layer_msg("两次输入不匹配");
			return false;
		}else return true;
	}
	j.login=function(){
		$('#loginModal').modal('show');
		$('#loginModal img').click();
		$('#login_password').val('');
	};
	j.doLogin=function(){//登陆
		$.post(j.root+"?m=Light&a=ajaxDoLogin&thin=200",$("#login_form").serialize(),function(data){
			if(data.status==true){
				location=j.root+"/user/center"
			}
			j.layer_msg(data.data);	
		},'json');
	};
	j.chk_login=function(){//ajax检查是否登陆
		$.post(j.root+"?m=Pc&a=ajaxChkLogin&thin=200",function(data){
			if(data.status){
				location(j.root+"/user/center.html");
			}
		},'json');
	};
	j.logout=function(){
		$.post(j.root+"?m=Light&a=ajaxDoLogout&thin=200",function(data){
			if(data.status){
				j.is_login=false;
				$('#user').html(j.get_login_before());
			}
			j.layer_msg(data.data);	
		},'json');
	};
	j.send_email_verfiy=function(){
		$.post(j.root+"?m=Light&a=ajaxSendEmailCaptcha&thin=200","email="+$("#reg_phone").val(),function(data){
			j.layer_msg(data.data,400);
		},'json');
	};
	j.send_phone_verfiy=function(){
		if ($.trim($("#reg_phone").val()) == "") {
			j.layer_msg("请填写手机号");
			return false;
		}else if (!(/^1[34578]\d{9}$/.test($("#reg_phone").val()))) {
			j.layer_msg("手机号码不正确");
			return false;
		}else if($.trim($("#reg_num_verfiy").val()) == ""){
			j.layer_msg("请输入图文验证码");
			return false;
		}
		$.post(j.root+"?m=Light&a=ajaxSendPhone&thin=200",{phone: $("#reg_phone").val(),verify:$("#reg_num_verfiy").val()},function(data){
			j.layer_msg(data.data,400);
		},'json');
	};
	
	
	j.showLogin=function(data){//登陆成功 显示登陆后状态
		j.is_login=true;
		j.user=data;		
		$('#user').html(j.get_login_end(data));
	};
	
	
	
	//判断是否是url
	j.is_url=function(url){
		return url.search(/^https*:\/\//)>-1;
	};
	
	

/**********公用********/
//自动调整iframe调试
	j.setIframeHeight=function(iframe) {
		if (iframe) {
			var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
			if (iframeWin.document.body) {
				iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
			}
		}
	};
	j.tooltip=function(eml,$title){		
		j.tooltip_timeout=600000;//多少秒后关闭		
		if(j.tooltip_lock===undefined || j.tooltip_lock===0)
			layui.use('layer', function(layer){
				var index=layer.tips($title, eml,{tips:[1,'#3595CC'],time:j.tooltip_timeout});
				j.tooltip_lock=1;
				$(eml).unbind('mouseout');
				$(eml).bind('mouseout',function(){layer.close(index);});
				setTimeout(function(){j.tooltip_lock=0;},1000);
			});
	};
	j.ajaxStart=function(){
		if(j.ajaxStatus!==undefined && !j.ajaxStatus){//j.ajaxStatus=false   不显ajax状态
			return;
		}
		//取最大z-index
		var max=0;
		$('.modal,.layui-layer').each(function(){
			current=$(this).css('z-index');
			max<current ? max=current:'';
		});
		$('#css3-spinner').css('z-index',max+1).show();
	};
	j.ajaxSuccess=function(){setTimeout(function(){$('#css3-spinner').hide();j.ajaxStatus=true;} ,800);};
	j.layer_msg=function($msg,$maxWidth,$timeout){
		layui.use('layer', function(layer){setTimeout(function(){layer.msg($msg,{maxWidth:$maxWidth ? $maxWidth: 180});},$timeout===undefined ? 0  :$timeout);});
	};
	j.isWebkit = function() {return document.body.style.WebkitBoxShadow !== undefined;}; 
	j.isIE=function(){if(!!window.ActiveXObject || "ActiveXObject" in window) return true;return false;};
	
	$(self).ajaxStart(function(){j.ajaxStart();});
	$(self).ajaxError(function(){j.ajaxSuccess();});
	$(self).ajaxStop(function(){j.ajaxSuccess();});
	$(self).ajaxSuccess(function(){j.ajaxSuccess();});

	layui.use('util', function(){layui.util.fixbar();});
	return j;
})(j,jQuery,layui);


