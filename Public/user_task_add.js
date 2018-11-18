/*********增加任务页 task_add*********/	
(function($,j,layui){
	j.taobaoShow=function(){//显示淘宝店铺
		$('.shopname-list').hide();$('.shopname-list').first().show();$('.sid:checked').prop('checked',false);
	};
	j.jdShow=function(){
		$('.shopname-list').hide();$('.shopname-list').eq(1).show();$('.sid:checked').prop('checked',false);
	};
	j.pinShow=function(){
		$('.shopname-list').hide();$('.shopname-list').eq(2).show();$('.sid:checked').prop('checked',false);
	};
	j.openSpecTask=function(){//特别任务
		$('.shopname-list').hide();$('.shopname-list').eq(2).show();$('.sid:checked').prop('checked',false);
	};
	
	//任务添加保存第一步 任务类型与店铺
	j.doTaskStep1=function (that){
		if(!$('.sid:checked').val()){
			j.layer_msg('请先选择店铺');
			return false;
		}
		j.taskStep1Data=$(that).serialize();	
		$('#task_step1').hide();
		$('#task_step2').show();
		if($('#wrap_task_type:checked').length==1){//是刷单
			j.step2_show(0);//显示刷单
		}else j.step2_show(1);
	};
	j.doTaskStepUp2=function (that){
		$('#task_step2').hide();
		$('#task_step1').show();
	};
	j.doTaskStepUp3=function (){
		$('#task_step3').hide();
		$('#task_step2').show();
	};
	j.doTaskStep2Chk=function(that){
		var checked=$('#wrap_task_type:checked').length;
		//先检查
		if(checked && !$('.is_taobaoke:checked').length){
			j.layer_msg('请选择该宝贝是否已加入淘宝客推广计划',600);
			return;
		}
		if(checked &&!$('.is_postage:checked').length){
			j.layer_msg('请选择商品是否包邮',300);
			return;
		}
		if(!$('.sort_style:checked').length){
			j.layer_msg('请选择定位目标商品排序',300);
			return;
		}
		
		j.doTaskStep2That=that;
		$('#MChkKeyword').modal('show');
		$('#MChkKeyword').on('shown.bs.modal', function () {//在调用 show 方法后触发
			//关键字
			var offSet='',keyStr='<div class=col-sm-6>请核对你设置的关键词是否有错别字:</div>';
			$('.goods_keyword').each(function(i){
				offSet=(i>0) ? ' col-sm-offset-6' :'';
				keyStr+='<div class="col-sm-6'+offSet+'"><span class="text-danger j_w">'+j.htmlspecialchars(this.value)+'</span><input class=modal_chk type=checkbox title="核对" value=1></div>';
			});
			$('.modal_chk_box').html(keyStr);
			//其它
			var goods_name=j.htmlspecialchars($('.goods_name').val());//商品名称
			var list_price=j.htmlspecialchars($('.list_price').val())+'元';//搜索列表展示价
			var goods_price=j.htmlspecialchars($('.goods_price').val())+'元';//商品成交价
			var store_name=j.htmlspecialchars($('.sid').attr('title'));//店铺名称
			var sort=j.htmlspecialchars($('.sort_style:checked').attr('title'))+'，约'+parseInt($('.receive_num').val())+'人收货';//排序方式
			var tmp=$('#MChkKeyword i');
			tmp.eq(0).text(goods_name);
			tmp.eq(1).text(list_price);
			tmp.eq(2).text(goods_price);
			tmp.eq(3).text(store_name);
			tmp.eq(4).text(sort);
			$('#MChkKeyword img').attr('src',j.root+'/Uploads/'+(task?'task':'tmp')+'/'+$('.goods_pic').val());
			layui.use('form', function(){var form = layui.form;form.render();}); 
		});
		return;
	};
	j.doTaskStep2ChkDo=function(){
		if($('.modal_chk:checked').length!=$('.modal_chk').length) 
			j.layer_msg('请确认浏览关键字是否有错别字！',300);
		else{
			j.doTaskStep2();
		}
	};	
	j.doTaskStep2=function(){//提交任务后显示任务信息
		if(j.doTaskStep2Lock!=undefined && j.doTaskStep2Lock) return;
		j.doTaskStep2Lock=1;
		that=j.doTaskStep2That;
		//把所有未选中的收藏商品   收藏店铺   加购物车设置为0  提交才不会错
		var $all=$('.j_chk');
		$all.each(function(i){
			var th=$(this);
			if(th.is(':checked')==false){
				th.val('0');
				th.prop('checked',true);
			}
		});
		//提交任务
 		$.post(j.root+"?m=Pc&a=ajaxTaskAdd&thin=200",$(that).serialize()+'&'+j.taskStep1Data,function(data){
			$all.each(function(i){//还原
				var th=$(this);
				if(th.val()=='0'){
					th.val('1');
					th.removeProp("checked");
				}
			});
			j.doTaskStep2Lock=0;
			$('#MChkKeyword').modal('hide');//关闭确认
			
			if(!data.status){
				j.layer_msg(data.data,300);//保存失败
				return;
			}
			$('#task_step2').hide();
			$('#task_step3').show();
			j.calcPay(data.id);
			j.getUserMoney();
		},'json');
		
	};
	j.doTaskStep3=function(){//付款并发布任务
		if(j.taskPayId==undefined){
			j.layer_msg('付款失败,请重新发布任务',300);
			return;
		}
		if(j.int100($('.user_money').text())<j.int100($('.j_pay').text())){
			j.layer_msg('余额不足，请先充值',300);
			return;
		}
		$.post(j.root+"?m=Pc&a=pay&thin=200",{id:j.taskPayId},function(data){
			if(!data.status){
				j.layer_msg(data.data,300);//保存失败
				return;
			}
			$('#task_step3').hide();
			$('#task_step4').show();
		},'json');
	};
	//增加附加商品
	j.add_more_goods=function(){
		if($(".more_goods").length==3){
			j.layer_msg('最多加2个附加商品');
		}else{
			var str='<div class="more_goods">'+$(".more_goods:first").html()+'</div>';
			var parser = new DOMParser();
			var doc = parser.parseFromString(str, 'text/html');
			doc.querySelector('.camera-area').innerHTML = '<input type="hidden" name="goods_pic[]" class="save goods_pic"/><input type="file" class="fileToUpload" required accept="image/*" /><br/><p class="thumb_template"></p><span class="upload_progress"></span>';
			str=doc.querySelector('.more_goods').outerHTML;
			$(".more_goods:last").after(str.replace('col-sm-1 hidden','col-sm-1'));
			j.fileUpload($(".more_goods:last .camera-area"));
		}
	};
	j.del_more_goods=function(that){
		$(that).parent().parent().parent().remove();
	};
	j.delUpUp=function(that,type){//删除第三步 好评
		var parent=$(that).parent(),index=0,num=0;
		var top=parent.parent();
		if(top.is('.type-ipt-wrap')==false){top=top.parent();}
		var top_up=top.parent();
		if(type==2){//文字
			var panel=top.parent().parent();
			//当前index
			index=parseInt(parent.find('font').text())-1;
			//移除对应 文字框
			panel.find('.com-kw-ct-ipt-wrap').eq(index).remove();
			top.remove();
			//序号
			top_up.children('.type-ipt-wrap').each(function(i){
				$(this).find('.sk-num>font').text(i+1);
			});
			panel.find('#wrap-kwtype-opt>.com-kw-ct-ipt-wrap').each(function(i){
				$(this).find('font').text(i+1);
			});
		}
		else if(type==3){//图片
			var panel=top.parent().parent();
			//当前index
			index=parseInt(parent.find('font').text())-1;
			//移除对应 图片框
			$('#wrap-pictype-opt>.pic-type-panel').eq(index).remove();
			top.remove();
			//序号
			top_up.children('.type-ipt-wrap').each(function(i){
				$(this).find('.sk-num>font').text(i+1);
			});
			panel.find('#wrap-pictype-opt>.pic-type-panel').each(function(i){
				$(this).find('font').text(i+1);
			});
		}else{
			top.remove();
		}
		
	};
	
	j.step2_show=function($type){//0刷单  1浏览  切换 	 j_required  除浏览任务打开评价必填
		if($type){//1浏览
			$('.view_hide').hide();
			$('.view_show').show();
			if($('.type-check:checked').length==0){
				$('.type-check:first').click();
			}
			$('.task_required').attr('required',false);
			$('.view_required').attr('required',true);
			$('.type-check').attr('disabled','disabled');
		}else{
			$('.view_hide').show();
			$('.view_show').hide();
			if($('.type-check:checked').length){
				$('.type-check:checked').click();
			}
			$('.task_required').attr('required',true);
			$('.view_required').attr('required',false);
			$('.type-check').removeAttr('disabled');
		}
	};
	
    var page = {
    	init: function(){
            this.initShowAllAddress();
            this.initAddPthp();
            this.initChkType();
            this.initAddTextCmt();
            this.initAddPicCmt();
			this.initSelectArea();
			this.initJTab();
			this.initSetFav();
			this.initStartDate();
    	},
		initStartDate:function(){//发布时间
			var cbk=function(e){
				var start_hour=j.$('start_hour'),end_hour=j.$('end_hour');
				if(j.int(start_hour.value)>=j.int(end_hour.value))
					j.$('end_hour').setCustomValidity("开始时间不能大于等于结束时间");
				else end_hour.setCustomValidity("");
			};
			$('#start_hour').on('blur',cbk);
			$('#end_hour').on('blur',cbk);
		},
		initShowAllAddress: function(){//商品所在地  显示选择地址
			var $addressPanel  =  $('.alladdress');
			var $address       =  $('.goodsaddress');
			var $upaddress     =  $('.upaddress');
			var $addresswrap   =  $('.address-wrap');
			
			$address.mouseenter(function(){
				$addressPanel.show();
				$(this).find('.up-down-trian').css('background', 'url('+j.root+'/Public/images/updowntriangle.png) 0 -7px no-repeat');
			});
			$addresswrap.mouseleave(function(){
				$addressPanel.hide();
				$address.find('.up-down-trian').css('background', 'url('+j.root+'/Public/images/updowntriangle.png) 0 0 no-repeat');
			});

			$addressPanel.delegate('a', 'click', function(){
				var text = $(this).text();
				$upaddress.attr('value', text);
				$address.find('span:first').text(text);
				$address.attr('title', text);
				$addressPanel.hide();
			});
		},
		initChkType:function(){//好评任务  如果没选一个及缩展要检查必填
			//如果是点开图文好评  保存图片好评部分  复制用
			//if(that.is(':checked') && that.is('.type-check:last')){}
			if(!page.picHtml)page.picHtml=$('#wrap-pictype-opt').children('.pic-type-panel').html();
			//特别任务保存搜索引导上传部分
			if($('.findPicWrap').length && !page.findPicHtml)page.findPicHtml=$('.findPicWrap').html();

			var typeCheck=$('.type-check');
			typeCheck.on('change', function(e) {//选择任务好评类型时  切换
				var that=$(this);
				//如果没选一个
				if($('.type-check:checked').length==0){
					typeCheck[0].setCustomValidity("必须选择一种好评任务发布！");
				}else{
					typeCheck[0].setCustomValidity("");
				}
				
				var panel=that.parent().parent().children('.type-ipt-panel');
				 if (that.is(':checked')) {
                    panel.show();
					panel.find(".j_required").attr('required',true);
					panel.find(".j_chk:first").each(function(){//还原错误信息
						var validationMessage=$(this).attr('data-validationMessage');$(this).removeAttr('data-validationMessage');
						if(validationMessage)this.setCustomValidity(validationMessage);
					});
					panel.find(".fav-flow-num-ipt").each(function(){						
						var validationMessage=$(this).attr('data-validationMessage');$(this).removeAttr('data-validationMessage');
						if(validationMessage)this.setCustomValidity(validationMessage);
					});
                } else {
                    panel.hide();
					panel.find(".j_required").attr('required',false);
					panel.find(".j_chk:first").each(function(){//保存错误信息
						if(this.validationMessage)$(this).attr('data-validationMessage',this.validationMessage);
						this.setCustomValidity("");
					});
					panel.find(".fav-flow-num-ipt").each(function(){						
						if(this.validationMessage)$(this).attr('data-validationMessage',this.validationMessage);
						this.setCustomValidity("");
					});
                }
			});
		},
		initSetFav:function(){//设置了收藏量要选收藏项  收藏商品
			$('.fav-flow-num-ipt').on('blur', function(e) {
				var that=$(this);
				var firs=that.parent().children('.j_chk:first')[0];
				if(j.int(this.value)!=0 && that.prevAll('.j_chk:checked').length==0){//有值且没选中
					firs.setCustomValidity("设置了收藏加购任务量必须选择收藏加购类型！");
				}else firs.setCustomValidity("");
				//加车与收藏任务不能大于浏览任务				
				if(j.int(this.value)>j.int(that.parent().children('.flow-num-ipt').val())){
					this.setCustomValidity("收藏加购任务量不能大于浏览任务量！");
				}else{this.setCustomValidity('');}
			});
			$('.flow-num-ipt').on('blur', function(e) {
				that=$(this).parent().children('.fav-flow-num-ipt');
				if(j.int(that.val())>j.int(this.value)){
					that[0].setCustomValidity("收藏加购任务量不能大于浏览任务量！");
				}else{that[0].setCustomValidity('');}
			});
			$('.j_chk').on('change', function(e) {
				var that=$(this);
				var paren=that.parent();
				var firs=paren.children('.j_chk:first')[0];
				var fav=j.int(paren.children('.fav-flow-num-ipt').val());
				if(that.is(':checked') || fav==0){//选中或没值
					firs.setCustomValidity("");
				}else if(fav!=0 && paren.children('.j_chk:checked').length==0){//有值且没选中
					firs.setCustomValidity("设置了收藏加购任务量必须选择收藏加购类型！");
				}
			});
		},
		initMoveFav:function(){//移除收藏事件   复制的好评要先删后加
			$('.flow-num-ipt').off('blur');
			$('.fav-flow-num-ipt').off('blur');
			$('.j_chk').off('change');
		},
		initAddPthp:function(){//普通好评复制		
			$('#com-add-sk').on('click', function(e) {
				page.initMoveFav();
				var wrap=$('#j_pt_hp').parent();
				//看是否超过5个
				if(wrap.find('.type-ipt-wrap').length==5){
					j.layer_msg('最多只能增加5项');
					return;
				}
				//获取模板 去除不要的
				var str='<div class="type-ipt-wrap">'+$('#j_pt_hp').html()+'</div>';
				str=str.replace(/<a class="badge badge-primary".+a>/,'');				
				wrap.append(str);
				wrap.find('.type-ipt-wrap:last-child .sk-num').append('<span class="delsk" onclick="j.delUpUp(this,1);">删 除</span>');
				page.initSetFav();
			});
		},
		initAddTextCmt:function(){//文字好评任务复制
			$('#kw-add-sk').on('click', function(e) {
				page.initMoveFav();
				var wrap=$('#j_text_cmt_wrap').parent();
				var len=wrap.find('.type-ipt-wrap').length;
				//看是否超过5个
				if(len==5){
					j.layer_msg('最多只能增加5项');
					return;
				}
				//获取模板 去除不要的
				var str='<div class="type-ipt-wrap">'+$('#j_text_cmt_wrap').html()+'</div>';
				str=str.replace(/<a.+a>/,'').replace('<font>1</font>','<font>'+(len+1)+'</font>');	
				wrap.append(str);
				wrap.find('.type-ipt-wrap:last-child .sk-num').append('<span class="delsk" onclick="j.delUpUp(this,2);">删 除</span>');
				
				//评论textarea复制
				wrap=$('#wrap-kwtype-opt');
				str='<div class="com-kw-ct-ipt-wrap">'+wrap.find('.com-kw-ct-ipt-wrap').html()+'</div>';
				wrap.append(str.replace(/>1</g,'>'+(len+1)+'<'));
				page.initSetFav();
			});
		},
		initAddPicCmt:function(){//图片好评任务复制
			$('#pic-add-sk').on('click', function(e) {
				page.initMoveFav();
				var wrap=$('#j_wrap_pic_part1');
				var len=wrap.children('.type-ipt-wrap').length;
				//看是否超过5个
				if(len==5){
					j.layer_msg('最多只能增加5项');
					return;
				}
				//获取模板 去除不要的
				var str='<div class="type-ipt-wrap">'+$('#j_pic_cmt_wrap').html()+'</div>';
				str=str.replace(/<a.+a>/,'').replace('<font>1</font>','<font>'+(len+1)+'</font>');	
				wrap.append(str);
				wrap.find('.type-ipt-wrap:last-child .sk-num').append('<span class="delsk" onclick="j.delUpUp(this,3);">删 除</span>');
				
				//上传图片部分复制
				wrap=$('#wrap-pictype-opt');
				str='<div class="pic-type-panel">'+page.picHtml+'</div>';
				wrap.append(str.replace(/>1</g,'>'+(len+1)+'<'));
				//绑定上传图片
				j.fileUpload(wrap.find(".pic-type-panel:last-child .camera-area"));
				page.initSetFav();
				
			});
		},
		/**
		* 地域限制 选择地区  显示已选
		* @return void
		*/
		initSelectArea:function(){
			$('#j_area input').click(function(){
				var chk=$(this).prop('checked');
				var id=this.id;
				//如果是区域
				if(id.length==3){
					var inputs=$(this).parent().next().find('input');
					if(chk){
						inputs.prop('checked',true);
					}else{
						inputs.prop('checked',false);
					}		
				}
				//显示已选
				var str='';
				$('#j_area .i>input:checked').each(function(i){
					str+= (str=='' ? '' : '，' ) +$(this).attr('title');
				})
				$('.showarea').text(str);
			});
		},
		/**
		* 显示千人千面设置 子选项
		* @return void
		*/
		initJTab:function(){
			$('.j_tab').click(function(){
				var that=$(this);
				var chk=that.prop('checked');
				var panel=that.parent().next('.j_panel');
				if(chk){
					panel.show();
				}else{
					panel.hide();
					panel.find('input').prop('checked',false);	
					if(that.is('.region')) $('.showarea').text('');
				}
			});
		}
	};	
	page.init();	
	layui.use('form', function(){//切换垫付任务 浏览任务
	  var form = layui.form;
	  form.on('radio(radioFilter)', function(data){
		  $('.tasktype:checked').prop('checked',false);
		 // $('.sid:checked').prop('checked',false);
		  $('.shopname-list').hide();
		  if(data.value==1){//浏览任务
			$('.tasktype-list').first().hide();
			$('.tasktype-list').last().show();
			//$('.tasktype-list').last().find('input:first').click();
			$('#step3').hide();//第三步：选择平台返款模式
		  }else{
			$('.tasktype-list').last().hide();		  
			$('.tasktype-list').first().show();
			//$('.tasktype-list').first().find('input:first').click();
			$('#step3').show();
		  }
	  });
	  //千人千面开关
	  form.on('switch(switchLimitAll)', function(data){
		if(this.checked){
			$('#switchLimitAll').show();
		}else{
			$('#switchLimitAll').hide();
		}
	  });

	});
	layui.use('laydate', function(){
		layui.laydate.render({
		  elem: '#laydate' //指定元素
		});
	});
	//上传
	j.fileUpload($(".camera-area"));
	
})(jQuery,j,layui);