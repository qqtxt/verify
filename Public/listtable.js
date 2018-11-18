var listTable = new Object;
listTable.app = "";
listTable.act = "lists";
listTable.filter = new Object;
//绝对网址
listTable.url = '';
listTable.root=j.root;
//忽视
listTable.list_act = listTable.act;


listTable.post=function(data,callback,type){
	url=this.url+'/'+this.app+(this.act==''?'':'/'+this.act);
	if(typeof(data)=='undefined' || data==''){
		data='is_ajax=1';
	}else if(typeof(data)=='string'){
		data+='&is_ajax=1';
	}else{
		data['is_ajax']=1;
	}
	return $.post(this.root+url,data,callback,type);	
}
/**
 * 创建一个可编辑区
 */
listTable.edit = function(obj, act, id)
{
  var $msie = /msie/.test(navigator.userAgent.toLowerCase());
  var tag = obj.firstChild.tagName;
  if (typeof(tag) != "undefined" && tag.toLowerCase() == "input"){
    return;
  }
  /* 保存原始的内容 */
  var org = obj.innerHTML;
  var val = $msie ? obj.innerText : obj.textContent;

  /* 创建一个输入框 */
  var txt = document.createElement("INPUT");
  txt.value = (val == 'N/A') ? '' : val;
  txt.style.width = (obj.offsetWidth + 50) + "px" ;

  /* 隐藏对象中的内容，并将输入框加入到对象中 */
  obj.innerHTML = "";
  obj.appendChild(txt);
  txt.focus();

  /* 编辑区输入事件处理函数 */
  txt.onkeypress = function(e){
	var evt = (typeof e == "undefined") ? window.event : e;
	evt.stopPropagation();
	var srcElement = document.all ? evt.srcElement : evt.target;
    var obj =srcElement;

    if (evt.keyCode == 13){
		obj.blur();
		return false;
    }

    if (evt.keyCode == 27){	
		obj.parentNode.innerHTML = org;
    }
  }

  /* 编辑区失去焦点的处理函数 */
  txt.onblur = function(e){
  	listTable.act=act;
    if ($.trim(txt.value).length > 0 && val!=txt.value){
		listTable.post(
			{
				val:$.trim(txt.value),
				id:id
			}, 
			function(res){
				if (!res.status){
					alert(res.data);
				}
				obj.innerHTML = (res.status) ? res.data : org;
			},
			"json"
		);
    }
    else{
      obj.innerHTML = org;
    }
  }
}

/**
 * 切换状态
 */
listTable.toggle = function(obj, act, id)
{
  this.act=act;
  var val = (obj.src.match(/yes.gif/i)) ? 0 : 1;
		this.post(
		{
			val:val,
			id:id
		},
		function(res){
			if (!res.status){
				alert(res.data);
			}
			if (res.status){
				obj.src = (res.data > 0) ? 'Public/admin/images/yes.gif' : 'Public/admin/images/no.gif';
			}					
		},					
		"json"
	);
}

/**
* 一般用于修改状态
* @param str  act  
* @param int  id
* @param val  val  修改的值   					 可选  默认为1
* @param obj  obj  根据obj找前一td显示为已通过   可选
* @param text text  根据obj找前一td显示为已通过  可选
* @param function  callback  时间到了运行
* @return void
*/
listTable.modify = function(act,id,val,obj,text){
  this.act=act;
  if(val==undefined) val = 1;
  this.post({val:val,id:id},function(res){
		if(!res.status){alert(res.data);}
		if(res.status  && typeof(obj)=='function'){
			obj(res);return;
		}
		if(res.status && obj ){
			if(!text)text='已通过';
			$(obj).parentsUntil("td").parent().prev().text(text);
		}
	},"json");
}
/**
 * 切换排序方式
 */
listTable.sort = function(sort_by, sort_order)
{
	this.act=this.list_act;
	var args ={
				sort_by:sort_by
			};
	this.filter.sort_by == sort_by ? (args.sort_order= this.filter.sort_order == "DESC" ? "ASC" : "DESC") : args.sort_order= "DESC";
	for (var i in this.filter){
		if (typeof(this.filter[i]) != "function" &&  i != "sort_order" && i != "sort_by" && $.trim(this.filter[i])!=''){
			args[i]=this.filter[i];
		}
	}
	this.filter['page_size'] = this.getPageSize();
	listTable.post(
		args,
		listTable.listCallback,
		"json"
	);
}

/**
 * 翻页
 */
listTable.gotoPage = function(page){
  if (page != null) this.filter['page'] = page;
  if (this.filter['page'] > this.pageCount) this.filter['page'] = 1;
  this.filter['page_size'] = this.getPageSize();
  this.loadList();
}

/**
 * 载入列表
 */
listTable.loadList = function(){
	this.act=this.list_act;
	var args=this.compileFilter();
	listTable.post(args,this.listCallback,	"json");
}

/**
 * 删除列表中的一个记录
 */
listTable.remove = function(id, cfm, opt){
  opt == null ?  opt = "del" :'';
  if (confirm(cfm))
  {
	var args=this.compileFilter();
	this.act=opt;
	args['id']=id;
	this.post(args,this.listCallback,	"json");
  }
}
listTable.listCallback = function(result){
	if (!result.status){
		alert(result.data);
	}
	else{
		try{
			$('#listDiv').html(result.data);
				
			if (typeof result.filter == "object"){
				listTable.filter = result.filter;
			}
			listTable.pageCount = result.page_count;
		}
		catch (e){
		  alert(e.message);
		}
	}
}

listTable.getPageSize = function()
{
	var ps = 15;
	var reg = /^\d+$/;
	var pageSize = $("#pageSize");
	if (pageSize){
		reg.test(pageSize.val()) ? ps = pageSize.val() : '';
		$.cookie('admin_page_size', ps, {expires: 365, path: '/'});
		return ps;
	}
}
listTable.compileFilter = function(){
  var args = new Object;
  for (var i in this.filter){
    if (typeof(this.filter[i]) != "function" && typeof(this.filter[i]) != "undefined"){
		args[i]= this.filter[i];
    }
  }
  return args;
}

listTable.gotoPageFirst = function(){
  if (this.filter.page > 1){
    this.gotoPage(1);
  }
}

listTable.gotoPagePrev = function(){
  if (this.filter.page > 1){
    this.gotoPage(this.filter.page - 1);
  }
}

listTable.gotoPageNext = function(){
  if (this.filter.page < this.pageCount){
    this.gotoPage(parseInt(this.filter.page) + 1);
  }
}

listTable.gotoPageLast = function(){
  if (this.filter.page < this.pageCount){
    this.gotoPage(this.pageCount);
  }
}

listTable.changePageSize = function(e){
  var evt = (typeof e == "undefined") ? window.event : e;
    if (evt.keyCode == 13){
        this.gotoPage();
        return false;
    };
}



listTable.selectAll = function(obj, chk){
  if (chk == null){
    chk = 'checkboxes';
  }
  var elems = obj.form.getElementsByTagName("INPUT");

  for (var i=0; i < elems.length; i++)
  {
    if (elems[i].name == chk || elems[i].name == chk + "[]")
    {
      elems[i].checked = obj.checked;
    }
  }
}

