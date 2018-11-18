(function (j,$,layui) {
	//公用 
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
	j.photo=function(id){
		layui.use('layer', function(){
			layui.layer.photos({
				photos: id
				,anim: 5
			}); 
		});	
	};
	j.scrollToEnd=function(){//滚动到底部
		$(document).scrollTop($(document).height()-$(window).height()); 
	}
	
	j.dateAdd=function(timestamp){//加多少秒
		var date=new Date().getTime()+timestamp*1000;
		date=new Date(date);
		return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate()
	}
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
	j.checkPassword=function(that) {
		if (that.value != $("#reg_password").val()){
			that.oninvalid();
			//j.layer_msg("两次输入不匹配");
			return false;
		}else return true;
	};
	j.doEditPassword=function(that){//修改密码
		$.post(j.root+"?m=Pc&a=ajaxDoEditPassword&thin=200",$(that).serialize(),function(data){
			j.layer_msg(data.data);	
		},'json');
	};
	j.doReg=function(){//注册
		$.post(j.root+"?m=Light&a=ajaxDoReg&thin=200",$("#reg_form").serialize(),function(data){
			if(data.status){
				if($('#type').val()=='1'){
					j.layer_msg('注册成功，请下载app',300);
					setTimeout(function(){
						j.reload();
					},3000);
				}else{
						j.layer_msg(data.data);
						setTimeout(function(){
						location=j.root+"/user/center";
					},1000);
				}
			}			
		},'json');
	};
	j.doLogin=function(){//登陆
		$.post(j.root+"?m=Light&a=ajaxDoLogin&thin=200",$("#login_form").serialize(),function(data){
			if(data.status==true){
				j.layer_msg('登陆成功,正在跳转...');
				setTimeout(function(){location=j.root+"/user/center"},800);
			}else
				j.layer_msg(data.data);	
		},'json');
	};
	//查看反馈
	j.feedback_list=function(that){
		$(".open-small-chat").click(function() {
			$(this).children().toggleClass("fa-comments").toggleClass("fa-remove"),
			$(".small-chat-box").toggleClass("active");
			if($(".small-chat-box").hasClass("active")){
				$('#small-chat-content').scrollTop($('#small-chat-content')[0].scrollHeight);
				//新消息清零
				var newNum=parseInt($("#small-chat>.badge").text());
				if(newNum>0){
					$.post(j.root+"?m=Pc&a=ajaxDoFeedbackClearNew&thin=200",function(data) {
						if(data.status) $("#small-chat>.badge").text('0');
					},'json');
				}
			}
		});
	};
	
	//发送反馈
	j.feedback_send=function(that){
		var msg=$.trim($('#chat_msg').val());
		if(msg=='') return false;
		$.post(j.root+"?m=Light&a=ajaxDoFeedbackSend&thin=200", {msg:msg}, function(data) {
			if(data.status){
				$('#small-chat-content').html(data.data);
				$('#small-chat-content').scrollTop($('#small-chat-content')[0].scrollHeight);
				$('#chat_msg').val('');
			}else{
				j.layer_msg(data.data);
			}
		},'json');
	};
	//倒计时
	j.countdown=function(dom,time){
		$(dom).attr('disabled',true);
		$(dom).text(time+'秒后重发'); 
		var my=setInterval(function(){
				$(dom).text((time-- -1)+'秒后重发'); 
			}, 1000);
		setTimeout(function(){
			clearInterval(my);
			$(dom).attr('disabled',false);
			$(dom).text('发送验证码');
		},time*1000);
	};
	j.doForget=function(){
		var phone=$("#forget_phone").val();
		var verfiy=$.trim($("#forget_verfiy").val());
		if (!(/^1[34578]\d{9}$/.test(phone))) {
			j.layer_msg("手机号码不正确");
			return false;
		}else if(verfiy==''){
			j.layer_msg("请填写验证码");
			return false;
		}
		$.post(j.root+"?m=Light&a=ajaxDoForget&thin=200", {phone:phone,verfiy:verfiy}, function(data) {
			if(data.status){
				$('#forgetModal .modal-body').text(data.data);
				$('#forgetModal').modal({keyboard: true});
			}else{
				j.layer_msg(data.data);
			}
		},'json');
		return false;
	};
	j.sendForgetVerfiy=function(that){//忘记密码发送验证码
		var phone=$("#forget_phone").val();
		if (phone == "") {
			j.layer_msg("请填写手机号");
			return false;
		}else if (!(/^1[34578]\d{9}$/.test(phone))) {
			j.layer_msg("手机号码不正确");
			return false;
		}
		$.post(j.root+"?m=Light&a=ajaxForgetSendPhone&thin=200", {phone:phone}, function(data) {
			j.countdown(that,j.sms_send_gap);
			j.layer_msg(data.data);
		},'json');		
	};
	j.sendVerfiy=function(that){//发送手机验证码
		if ($.trim($("#reg_phone").val()) == "") {
			j.layer_msg("请填写手机号");
			return false;
		}else if (!(/^1[34578]\d{9}$/.test($("#reg_phone").val()))) {
			j.layer_msg("手机号码不正确");
			return false;
		}else if($.trim($("#reg_num_verfiy").val()) == ""){
			j.layer_msg("请输入验证码");
			return false;
		}
		$.post(j.root+"?m=Light&a=ajaxSendPhone&thin=200", {phone: $("#reg_phone").val(),verify:$("#reg_num_verfiy").val()}, function(data) {
			if(data.status)
				j.countdown(that,j.sms_send_gap);
			j.layer_msg(data.data);
		},'json');
		
	};
	j.sendEmailVerfiy=function(that){//发送邮箱验证码
		if ($.trim($("#reg_phone").val()) == "") {
			j.layer_msg("请填写邮箱");
			return false;
		}else if($.trim($("#reg_num_verfiy").val()) == ""){
			j.layer_msg("请输入验证码");
			return false;
		}
		$.post(j.root+"?m=Light&a=ajaxSendEmailVerfiy&thin=200", {email: $("#reg_phone").val(),verify:$("#reg_num_verfiy").val()}, function(data) {
			if(data.status)
				j.countdown(that,j.email_gap_time);
			j.layer_msg(data.data);
		},'json');
		
	};

	j.chk_login=function(){
		$.post(j.root+"?m=Pc&a=ajaxChkLogin&thin=200",function(data){
			if(data.title){
				j.showLogin(data);
			}else
				$('#user').html(j.get_login_before());
		},'json');
	};
	j.logout=function(){
		$.post(j.root+"?m=Light&a=ajaxDoLogout&thin=200",function(data){
			if(data.status){
				var msg='退出登陆成功';
				setTimeout(function(){location=j.root+'/user/login.html';},800);
			}else  	var msg='退出登陆失败';			
			j.layer_msg(msg);
		},'json');
	};
	j.send_email_verfiy=function(){
		$.post(j.root+"?m=Light&a=ajaxSendEmailCaptcha&thin=200","email="+$("#reg_phone").val(),function(data){
			j.layer_msg(data.content,400);
		},'json');
	};
	j.showLogin=function(data){//登陆成功 显示登陆后状态
		j.is_login=true;
		$('#user').html(j.get_login_end(data));
	};

	//判断是否是url
	j.is_url=function(url){
		return url.search(/^https*:\/\//)>-1;
	};
	j.doUserInfo=function(that){
		$.post(j.root+"?m=Pc&a=ajaxDoUserInfo&thin=200",$(that).serialize(),function(data){
			j.layer_msg(data.data);
		},'json');
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
	
	//添加店铺
	j.store_add=function (){
		layui.use('layer', function(){
			j.store_add_index=layui.layer.open({
			  type: 2,
			  title: '增加绑定店铺',
			  shadeClose: false,
			  shade: 0.8,
			  area: ['400px', '90%'],
			  content: j.root+'/user/store_add' //iframe的url
			}); 
		});
		
	}	
	j.close_store_add=function(){
		layui.use('layer', function(){
			layui.layer.close(j.store_add_index);
		});
	};

	j.toMoney=function(gold){
		if(gold<5000){
			j.layer_msg('佣金金额不够50元');
		}else if(confirm('佣金转本金'+j.div100(gold)+'元')){
			$.post(j.root+"?m=pc&a=toMoney&thin=200",function(data){
				j.layer_msg(data.data);
				if(data.status){
					j.reload();
				}
			},'json');
		}
		
	};	
	j.release=function(id,type){//释放黑名单
		$.post(j.root+"?m=pc&a=release&thin=200",{id:id,type:type},function(data){
			j.layer_msg(data.data);
			if(data.status){
				j.reload();
			}
		},'json');
	}
	j.batchRelease=function(that,type){//批量释放黑名单
		$.post(j.root+"?m=pc&a=batchRelease&thin=200&type="+type,$(that).serialize(),function(data){
			j.layer_msg(data.data);
			if(data.status){
				j.reload();
			}
		},'json');
	}

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
	//商家操作订单返款
	j.backMoney=function(toid,that,uid){
		layui.use('layer', function(layer){
			layer.confirm('返款押金多退少补，是否确定返款？', {
			  title:'确认',
			  btn: ['确定','取消']
			}, function(index){
				layer.close(index);
				var price=$(that).parent().prev().children('input').val();
				$.post(j.root+"?m=pc&a=backMoney&thin=200",{'toid':toid,'price':price,'uid':uid},function(data){
					j.layer_msg(data.data);
					if(data.status){
						$(that).removeClass('btn-primary').prop('onclick',null);
					}
				},'json');
			}, function(){
			});
		});
	};
	j.getQQ=function(uid){
		$.post(j.root+"?m=pc&a=getQQ&thin=200",{'uid':uid},function(data){
			layui.use('layer', function(layer){layer.alert(data.qq);});
		},'json');
	}
	j.getPhone=function(uid){
		$.post(j.root+"?m=pc&a=getPhone&thin=200",{'uid':uid},function(data){
			layui.use('layer', function(layer){layer.alert(data.phone);});
		},'json');
	}
	j.addBlack=function(buy_uid){//商家拉黑刷手
		layui.use(['layer','laydate'], function(layer,laydate){
			layer.alert(
			'<div class="form-horizontal">\
				<div class="form-group">\
					<label class="col-xs-3 control-label text-danger">* 类型</label>\
					<div class="col-xs-9">\
						<select id="black_type" class="form-control m-b" name="type">\
							<option value="0">请选择类型</option>\
							<option value="1">评价被删除</option>\
							<option value="2">淘宝客</option>\
							<option value="3">操作不规范</option>\
							<option value="4">未按要求做单</option>\
							<option value="5">恶意退款</option>\
							<option value="6">其它</option>\
						</select>\
					</div>\
				</div>\
				<div class="form-group has-success">\
					<label class="col-sm-3 control-label">结束时间</label>\
					<div class="col-sm-9">\
						<input id="black_time" type="text" class="form-control j_date" placeholder="到期自动解除,不选默认两个月">\
					</div>\
				</div>\
				<div class="form-group">\
					<label class="col-sm-3 control-label">说明：</label>\
					<div class="col-sm-9">\
						<textarea id="black_note" name="note" class="form-control" aria-required="true" rows="3" placeholder="请简单描述拉黑原因，非必填" ></textarea>\
					</div>\
				</div>\
			</div>',{area: ['420px', '350px'],title:'加入黑名单' ,yes:function(index, layero){
				var type=$('#black_type').val(),end_time=$('#black_time').val(),note=$('#black_note').val();
				if(type>6 || type<1){alert('请选择拉黑类型');return false;}
				$.post(j.root+"?m=pc&a=blacklist&thin=200",{'buy_uid':buy_uid,'type':type,'end_time':end_time,'note':note},function(data){
					if(data.status){
						layer.close(index);j.layer_msg(data.data);
					}else{
						alert(data.data);
					}
				},'json');
			}});
			laydate.render({
			  elem: '.j_date'
			});
		});
	};
	j.getUserMoney=function(){//取用户余额
		$.post(j.root+"?m=Pc&a=getUserMoney&thin=200",function(data){
			if(data.status){
				$('.user_money').text(j.div100(data.data));
			}else j.layer_msg(data.data,300);
		},'json');
	};
	j.calcPay=function(id){//计算与更新任务支付
		//保存任务id
		j.taskPayId=id;
		$.post(j.root+"?m=Pc&a=calc&thin=200",{id:id},function(data){
			var c=data.data.commission,t=data.data.task_num,d=data.data.deposit,calc=data.data.calc,type=data.data.type;
			var $tbody='',m,n;
			if(type==0){
				$('#j_head').html('<th>分类</th><th>费用明细</th><th>小计</th><th>数量</th><th>合计</th>');
			}else $('#j_head').html('<th>费用明细</th>');
			for(var i=0;i<3;i++){
				//如果有数量才生成
				if((i==0 && t.common) || (i==1 && t.text)  || (i==2 && t.pic))
					$tbody+='<tr align=center>'+
									($tbody=='' ? '<td rowspan=10>佣金</td>' :'')+
									'<td align=left>\
										<div class=row>\
											<div class="col-sm-10 col-sm-offset-2">\
												<p>基础佣金：'+j.div100(c.base)+'元/单</p>'+
												(i==1?'<p><span class="text-warning">文字好评</span>：+'+j.div100(c.price_cmt_text)+'元/单</p>':(i==2?'<p><span class="text-warning">图文好评</span>：+'+j.div100(c.price_cmt_pic)+'元/单</p>':''))+
												(c.price_more_commission ? '<p>加赏佣金：+'+j.div100(c.price_more_commission)+'元/单</p>' :'')+
												(c.price_more_goods ? '<p>多商品费用：'+j.div100(c.price_more_goods)+'元/单</p>' :'')+
												'<p>平台返款服务费：'+j.div100(c.price_service)+'元/单</p>'+
												(c.price_buyer ? '<p>买号属性增值费：'+j.div100(c.price_buyer)+'元/单</p>' :'')+
												(c.price_express ? '<p>快递费(空包)：'+j.div100(c.price_express)+'元/单</p>' :'')+
											'</div>\
										</div>\
									</td>\
									<td>'+j.div100(m=i==0?calc.base:(i==1? calc.base+c.price_cmt_text : calc.base+c.price_cmt_pic ))+'元</td>\
									<td>'+(n=i==0?t.common:(i==1? t.text : t.pic ))+'单</td>\
									<td>'+j.div100(m)+' x '+n+' = '+j.div100(m*n)+'元</td>\
						   </tr>';
			}
			if(t.view>0) $tbody+='<tr align=center><td '+(type==0? 'colspan=4':'')+' >访客流量费：'+t.view+' 个 x '+
				(type==0 || type==1 && !c.price_more_commission ?  j.div100(c.price_view)+' 元/个 ' : '('+j.div100(c.price_view)+' 元/个 + 加赏 '+j.div100(c.price_more_commission)+' 元/个 )')+
				' = '+j.div100(calc.total_view)+' 元</td></tr>';
			if(t.fav>0) $tbody+='<tr align=center><td colspan=4 >其中收藏流量费：'+t.fav+' 个 x '+j.div100(c.price_fav)+' 元/个 = '+j.div100(calc.total_fav)+' 元</td></tr>';
			$('.j_tbody').html($tbody);		
			$('.j_total_commission').html('合计： 单数'+(type==0? t.common+t.text+t.pic :t.view )+'单　佣金<span class="badge badge-success">'+j.div100(calc.total_commission)+'</span>元');
			
			if(type==0){
				$('.wrap_yajing').show();$('.j_total').show();
				if(d.price_mail)
					$('.j_yajing').html('<p>'+(t.common+t.text+t.pic)+' 单 x （'+j.div100(d.principle)+' 元/单 + 运费押金 '+j.div100(d.price_mail)+' 元/单） = '+j.div100(calc.total_principle)+' 元</p><p>(邮费按用户实际支付金额返还，如有剩余将在任务完成后自动返还。)</p>');
				else
					$('.j_yajing').html('<p>'+(t.common+t.text+t.pic)+' 单 x '+j.div100(d.principle)+' 元/单 = '+j.div100(calc.total_principle)+' 元</p>');			
				$('.j_total').html('总计： 佣金<span class="badge badge-warning">'+j.div100(calc.total_commission)+'</span>元 + 返款押金<span class="badge badge-warning">'+j.div100(calc.total_principle)+'</span>元  = <span class="badge badge-success">'+j.div100(calc.total)+'</span>元');
			}else{$('.wrap_yajing').hide();$('.j_total').hide();}
			
			$('.j_pay').text(j.div100(calc.total));
			
		},'json');
	};
	j.doTaskPay=function(){//付款并发布任务
		if(j.taskPayId==undefined){
			j.layer_msg('付款失败,请重新发布任务',300);
			return;
		}
		if(j.int100($('.user_money').text())<j.int100($('.j_pay').text())){
			j.layer_msg('余额不足，请先充值',300);
			return;
		}
		$.post(j.root+"?m=Pc&a=pay&thin=200",{id:j.taskPayId},function(data){
			j.layer_msg(data.data,300);
			if(!data.status){
				return;
			}			
			j.reload();
		},'json');
	};
	j.noteSave=function(that,type){
		type?'':type=0;
		$.post(j.root+"?m=Pc&a=order_note_save&thin=200&type="+type,$(that).serialize(),function(data){
			j.layer_msg(data.data,300);
		},'json');
		return false;
	};
	j.win=function(that,id){//在当前打开子窗口
		$(id,parent.document)[0].href=that.href;
		$(id,parent.document)[0].click();
		return false;
	};
	j.withdraw=function(tid){//商家撤消任务及退款
		$.post(j.root+"?m=Pc&a=withdraw&thin=200",{'tid':tid},function(data){
			if(data.status){
				layui.use('layer', function(layer){
					layer.alert(data.data,{title:'撤消未接单任务成功' ,yes:function(index, layero){j.reload();}});
				});
			}else j.layer_msg(data.data,300);
		},'json');	
	};
	j.delTask=function(tid){//删除任务
		$.post(j.root+"?m=Pc&a=delTask&thin=200",{tid:tid},function(data){
			j.layer_msg(data.data,300);
			if(!data.status){
				return;
			}			
			j.reload();
		},'json');
	};
	j.finishOrder=function(id,that){
		$.post(j.root+"?m=Pc&a=finishOrder&thin=200",{toid:id},function(data){
			j.layer_msg(data.data,300);
			if(!data.status)return;
			/*$(that).css('background-color','#ccc');
			$(that).css('border-color','#ccc');
			$(that).prop('onclick',null);*/
			j.reload();
		},'json');
	}
	j.regType=function(){//注册类型
		var is_wap=(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))return 1;else return 0;})(navigator.userAgent||navigator.vendor||window.opera);
		
		
		var recommend=j.int(j._GET('recommend')),type=is_wap;
		if(recommend>0){
			$('#parent_id').val(recommend);//prop('disabled',true)
		}
		if(type){
			$('#type').val('1');
			$('#type_reg').text('买手注册');//'+j.root+'/rqy.apk
		}else $('#type_reg').text('商家注册');
	};
	j.chkRecharge=function(callback){
		$.post(j.root+"?m=Pc&a=chkRecharge&thin=200",function(data){			
			if(!data.status){
				j.layer_msg('您有一笔充值正在审核中,如紧急情况请与客服联系！',480);
				return;
			}
			callback();
		},'json');
	}
	j.recharge=function(type){//提交转账信息 0微信支付宝  1银行卡
		layui.use('layer', function(layer){
			j.rechargeIndex=layer.open({skin:'j_alert',title:'提交转账信息',content:'<div class="ibox">\
			<div class="ibox-title"><h5>我的账户信息</h5></div><div class="ibox-content">\
			<form role="form" class="form-horizontal" onsubmit="j.doRecharge(this);return false;">\
				<div class="form-group">\
					<div class="col-xs-12">\
						<select id="bank_type" name="bank_type" class="form-control m-b" required oninvalid=setCustomValidity("请选择转账'+(type?'银行':'类型')+'") oninput=setCustomValidity("") >\
							<option value="0">请选择转账'+(type?'银行':'类型')+'</option>'+(type?typeOption1:typeOption)+'</select>\
					</div>\
				</div>\
				<div class="form-group has-success">\
					<div class="col-xs-12">\
						<input id="bank_code" name="bank_code" type="text" class="form-control" '+(type?' placeholder="转出银行卡号" maxlength="19" pattern="^[0-9]{16,19}$" oninvalid=setCustomValidity("请输入正确的转出银行卡号") ':' placeholder="转出帐号" oninvalid=setCustomValidity("请输入正确的转出帐号") ')+' oninput=setCustomValidity("")   required >\
					</div>\
				</div>\
				<div class="form-group has-success">\
					<div class="col-xs-12">\
						<input id="true_name" name="true_name" type="text" class="form-control" '+(type?'  placeholder="转出银行卡姓名" maxlength="20"  oninvalid=setCustomValidity("请输入正确的转出银行卡姓名") ':' placeholder="转出昵称" oninvalid=setCustomValidity("请输入正确的转出昵称") ')+'oninput=setCustomValidity("") required >'+(type?'<span class="help-block m-b-none">填写你转出银行卡开户账号的姓名，方便财务核对，不要填手机号</span>':'')+'</div>\
				</div>\
				<div class="form-group has-success">\
					<div class="col-xs-12">\
						<input id="money" name="money" type="number" class="form-control"  placeholder="转账金额（元）" min="1" step="0.01" required oninvalid=setCustomValidity("请输入正确的转账金额") oninput=setCustomValidity("") >\
						<span class="help-block m-b-none">（充值1次提交1次即可，恶意反复提交将处罚或封号）</span>\
					</div>\
				</div>\
				<div class="form-group">\
					<div class="col-xs-12 text-center">\
						<button class="btn btn-primary" type="submit">立即提交</button>\
					</div>\
				</div></form></div></div>',area: ['420px', '468px'],btn:null,closeBtn: 2});
		});
	};
	j.ajaxStart=function(){
		if(j.NoAjaxLoading)return;
		//取最大z-index
		var max=100;
		$('.modal,.layui-layer').each(function(){
			current=$(this).css('z-index');
			max<current ? max=current:'';
		});
		//隐藏
		$('#css3-spinner').css('z-index',max+1).show();
	};	
	j.ajaxSuccess=function(){if(j.NoAjaxLoading)return;setTimeout(function(){$('#css3-spinner').hide();} ,800);};
	j.layer_msg=function($msg,$maxWidth,$timeout){
		layui.use('layer', function(layer){setTimeout(function(){layer.msg($msg,{maxWidth:$maxWidth ? $maxWidth: 180});},$timeout===undefined ? 0  :$timeout);});
	};
	j.isRequired = function() {return 'required' in document.createElement('input');}; 
	j.isWebkit = function() {return document.body.style.WebkitBoxShadow !== undefined;}; 
	j.isIE=function(){if(!!window.ActiveXObject || "ActiveXObject" in window) return true;return false;};
	j.maxZIndex=function(){return Array.from(document.all).reduce(function(m,i){return Math.max(m,+window.getComputedStyle(i).zIndex||0)},0);}
	
	$(self).ajaxStart(function(){j.ajaxStart();});
	$(self).ajaxError(function(){j.ajaxSuccess();});
	$(self).ajaxStop(function(){j.ajaxSuccess();});
	$(self).ajaxSuccess(function(){j.ajaxSuccess();});
	if(!j.isRequired()){
		alert('请使用谷歌浏览器!');
		j.logout();
	}
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
	//取提醒数据  被申诉未处理数量   待返款数量  
	j.alertNum=function(){
		//子窗口 且(超时 或 子窗口有 )  
		if(parent!=self && $('#appeal_num',parent.document).length)
			var alertTime=j.float(parent.alertTime);
			var nowTime=new Date().getTime();
			if(nowTime-alertTime>180000 ||$('.back_num').length)
			$.post(j.root+"?m=Pc&a=alertNum&thin=200",function(data){
				if(data.status){
					$('#appeal_num',parent.document).text(data.appealNum).removeClass('hide');
					$('.back_num',parent.document).text(data.backNum).removeClass('hide');
					$('.back_num').text(data.backNum).removeClass('hide');
					parent.alertTime=new Date().getTime();
				}
			},'json');
	}

	
	//用户中心推广 与复制
	j.qcode(); j.fuzhi();
	setTimeout(j.alertNum,300);	
})(j,jQuery,layui);

