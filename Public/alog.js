/**  以adv.net/public下为准
 * alog
 * @version 1.0
 * @copyright www.baidu.com
 *
 * @file 前端统计框架，支持并行多个统计模块
 * @author 王集鹄(WangJihu,http://weibo.com/zswang)
 *         张军(ZhangJun08,http://weibo.com/zhangjunah)
 *         梁东杰(LiangDongjie,http://weibo.com/nedj)
 */

(function (win, doc) {
    var objectName = win.alogObjectName || 'A_Log';
    var oldObject = win[objectName];
    if (oldObject && oldObject.defined) { // 避免重复加载
        return;
    }
	
    /*<ie>*/
    var ie = win.attachEvent && !window.opera;
    /**
     * 是否IE
     */

    /**
     * 点击javascript链接的时间
     */
    var clickJsLinkTime;
    /*</ie>*/

    /**
     * 起始时间
     */
    var startTime = (oldObject && oldObject.l) || (+new Date());

		
    /**
     * id编码
     */
    var guid = 0;

    /**
     * 正在加载的脚本
     */
    var loadScripts = {};

    /**
     * 模块列表
     */
    var modules = {
        alog: {
            /**
             * 模块名
             */
            name: 'alog',
            /**
             * 是否声明
             */
            defined: true,
            /**
             * 模块实例
             */
            instance: entry
        }
    };
	
    var cookie = {};
    cookie.set = function(a, b, d) {//d.I毫秒
        var f;
        d && d.I && (f=new Date()) && (f.setTime(f.getTime() + d.I));
        document.cookie = a + "=" + b + (d&&d.domain ? "; domain=" + d.domain: "") + (d&&d.path ? "; path=" + d.path: "") + (f ? "; expires=" + f.toUTCString() : "") + (d&&d.ib ? "; secure": "")
    };
    cookie.get = function(a) {
        return (a = RegExp("(^| )" + a + "=([^;]*)(;|$)").exec(document.cookie)) ? a[2] : false
    };
	var local = {};
	local.set = function(a, b, d) {
        try {//防无痕模式
			var f = new Date;
			f.setTime(f.getTime() + (d || 315360E6));
            win.localStorage ? (b = f.getTime() + "|" + b, win.localStorage.setItem(a, b)) : cookie.set(a,b,{I:315360E6});
        } catch(g) {}
    };
    local.get = function(a) {
        if (win.localStorage) {
            if (a = win.localStorage.getItem(a)) {
                var b = a.indexOf("|"),
                d = a.substring(0, b) - 0;
                if (d && d > (new Date).getTime()) return a.substring(b + 1)
            }
        }else return cookie.get(a);
        return false;
    };
    local.remove = function(a) {
        if (win.localStorage) win.localStorage.removeItem(a);
        else cookie.set(a,'',{I:-315360E6});
    };
	var _GET=function(name) { 
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = win.location.search.substr(1).match(reg); 
		if (r != null) return unescape(r[2]); return null; 
	};
	
	if(_GET('Alog_Sid')){
		win.Alog_Sid =_GET('Alog_Sid');
	}
    /**
     * 一次访问标识
     */
    var sid =win.Alog_Sid ||
        ((+new Date()).toString(36) + Math.random().toString(36).substr(2, 3));
	//!local.get('alog_id') && local.set('alog_id',sid);
    /**
     * 处理入口
     *
     * @param {Object} params 配置项
     */
    function entry(params) {
        var args = arguments;
        var moduleName;
        var requires;
        var creator;

        if (params === 'define' || params === 'require') {
            // 校正参数调用
            for (var i = 1; i < args.length; i++) {
                switch (typeof args[i]) {
                    case 'string':
                        moduleName = args[i];
                        break;
                    case 'object':
                        requires = args[i];
                        break;
                    case 'function':
                        creator = args[i];
                        break;
                }
            }

            if (params === 'require') {
                if (moduleName && !requires) {
                    requires = [moduleName];
                }
                moduleName = null;
            }

            // 如果是引用，这产生临时模块名
            moduleName = !moduleName ? '#' + (guid++) : moduleName;
            var module;
            if (modules[moduleName]) {
                module = modules[moduleName];
            }
            else {
                module = {};
                modules[moduleName] = module;
            }

            // 避免模块重复定义
            if (!module.defined) {
                module.name = moduleName;
                module.requires = requires;
                module.creator = creator;
                if (params === 'define') {
                    module.defining = true;
                }
                clearDeps(module);
            }
            return;
        }

        if (typeof params === 'function') {
            params(entry);
            return;
        }

        /**
         * @example
            ```js
            alog('hunter.send', 'pageview');
            alog('monkey.send', 'pageview');
            alog('send', 'pageview'); // alog('default.send', 'pageview');
            ```
         */

        // 'hunter.send' -> [1]=>'hunter', [2]=>'send'
        String(params).replace(/^(?:([\w$_]+)\.)?(\w+)$/,
					//hunter.send   hunter 			send 
            function (all, 			trackerName, 	method) {
                args[0] = method;
                command.apply(entry.tracker(trackerName), args);
            }
        );
    }

    /**
     * 监听列表
     */
    var alogListeners = {};
    /**
     * 追踪器字典
     */
    var trackers = {};

    /**
     * 页面关闭中
     */
    var closing;

    /**
     * 默认追踪器
     */
    var defaultTracker;

    /**
     * 加载模块
     *
     * @param {string} moduleName 模块名
     */
    function loadModules(moduleName) {
        var modulesConfig = defaultTracker.get('alias') || {};
        var scriptUrl = modulesConfig[moduleName] || (moduleName + '.js');
        if (loadScripts[scriptUrl]) {
            return;
        }
        loadScripts[scriptUrl] = true;
        var scriptTag = 'script';
        var scriptElement = doc.createElement(scriptTag);
        var lastElement = doc.getElementsByTagName(scriptTag)[0];
        scriptElement.async = !0;
        scriptElement.src = scriptUrl;
        lastElement.parentNode.insertBefore(scriptElement, lastElement);
    }

    /**
     * 处理依赖关系
     *
     * @param {module} module 模块
     */
    function clearDeps(module) {
        if (module.defined) {
            return;
        }

        var defined = true;
        var params = [];
        var requires = module.requires;
        if (requires) {
            for (var i = 0; i < requires.length; i++) {
                var moduleName = requires[i];
                var deps = modules[moduleName] = (modules[moduleName] || {});
                if (deps.defined || deps === module) {
                    params.push(deps.instance);
                }
                else {
                    defined = false;
                    if (!deps.defining) { // 已经存在定义
                        loadModules(moduleName);
                    }
                    deps.waiting = deps.waiting || {};
                    deps.waiting[module.name] = module;
                }
            }
        }
        if (defined) {
            module.defined = true;
            if (module.creator) {
                module.instance = module.creator.apply(module, params);
            }
            clearWaiting(module);
        }
    }

    /**
     * 清理等待依赖项加载的模块
     *
     * @param {Module} module 模块对象
     */
    function clearWaiting(module) {
        for (var moduleName in module.waiting) {
            if (module.waiting.hasOwnProperty(moduleName)) {
                clearDeps(module.waiting[moduleName]);
            }
        }
    }

    /**
     * 获取时间戳
     *
     * @param {Date} now 当前时间
     * @return {number} 返回时间戳
     */
    function timestamp(now) {
        return (now || new Date()) - startTime;
    }

    /**
     * 绑定事件
     *
     * @param {HTMLElement=} element 页面元素，没有指定则为 alog 对象
     * @param {string} eventName 事件名
     * @param {Function} callback 回调函数
     * @example
        ```js
        alog.on('report', function (data) { data.tt = +new Date; });
        ```
     */
    function on(element, eventName, callback) {
        if (!element) {
            return;
        }

        if (typeof element === 'string') {
            callback = eventName;
            eventName = element;
            element = entry;
        }

        try {
            if (element === entry) {
                alogListeners[eventName] = alogListeners[eventName] || [];
                alogListeners[eventName].unshift(callback);
                return;
            }
            if (element.addEventListener) {
                element.addEventListener(eventName, callback, false);
            }
            else if (element.attachEvent) {
                element.attachEvent('on' + eventName, callback);
            }
        }
        catch (ex) {}
    }

    /**
     * 注销事件绑定
     *
     * @param {HTMLElement} element 页面元素
     * @param {string} eventName 事件名
     * @param {Function} callback 回调函数
     */
    function un(element, eventName, callback) {
        if (!element) {
            return;
        }

        if (typeof element === 'string') {
            callback = eventName;
            eventName = element;
            element = entry;
        }

        try {
            if (element === entry) {
                var listener = alogListeners[eventName];
                if (!listener) {
                    return;
                }
                var i = listener.length;
                while (i--) {
                    if (listener[i] === callback) {
                        listener.splice(i, 1);
                    }
                }
                return;
            }
            if (element.removeEventListener) {
                element.removeEventListener(eventName, callback, false);
            }
            else {
                element.detachEvent && element.detachEvent('on' + eventName, callback);
            }
        }
        catch (ex) {}
    }

    /**
     * 触发事件
     *
     * @param {string} eventName 事件名 "error"、"close"
     * @return {Object} 返回当前实例
     * @example
        ```js
        alog.fire('click', { element: document.getElementById('save') });
        ```
     */
    function fire(eventName) {
        var listener = alogListeners[eventName];
        if (!listener) {
            return;
        }
        var items = [];
        var args = arguments;
        for (var i = 1, len = args.length; i < len; i++) {
            items.push(args[i]);
        }

        var result = 0;
        var j = listener.length;
        while (j--) {
            if (listener[j].apply(this, items)) {
                result++;
            }
        }
        return result;
    }

    /**
     * 上报数据
     *
     * @param {string} url 目标链接
     * @param {Object} data 上报数据
     */
    function report(url, data) {
        if (!url || !data) {
            return;
        }

        // @see http://jsperf.com/new-image-vs-createelement-img
        var image = doc.createElement('img');

        var items = [];
        for (var key in data) {
            if (data[key]) {
                items.push(key + '=' + encodeURIComponent(data[key]));
            }
        }

        var name = 'img_' + (+new Date());
        entry[name] = image;
        image.onload = image.onerror = function () {
            entry[name] =
                image =
                image.onload =
                image.onerror = null;
            delete entry[name];
        };

        image.src = url + (url.indexOf('?') < 0 ? '?' : '&') + items.join('&');
    }

    /**
     * 字段名使用简写
     *
     * @param {Object} protocolParameter 字段名对照表，如果为null表示不上报
     * @param {Object} data 待处理的数据
     * @return {Object} 返回处理后的数据
     */
    function runProtocolParameter(protocolParameter, data) {
        if (!protocolParameter) {
            return data;
        }
        var result = {};
        for (var p in data) {
            if (protocolParameter[p] !== null) {
                result[protocolParameter[p] || p] = data[p];
            }
        }
        return result;
    }

    /**
     * 执行命令
     */
    function command() {
        var args = arguments;
        var method = args[0];

        if (this.created || /^(on|un|set|get|create)$/.test(method)) {
            var methodFunc = Tracker.prototype[method];
            var params = [];
            for (var i = 1, len = args.length; i < len; i++) {
                params.push(args[i]);
            }
            if (typeof methodFunc === 'function') {
                methodFunc.apply(this, params);
            }
        }
        else { // send|fire // 实例创建以后才能调用的方法
            this.argsList.push(args);
        }
    }

    /**
     * 合并两个对象
     *
     * @param {Object} a 对象1
     * @param {Object} b 对象2
     * @return {Object} 返回合并后的对象
     */
    function merge(a, b) {
        var result = {};
        for (var p in a) {
            if (a.hasOwnProperty(p)) {
                result[p] = a[p];
            }
        }
        for (var q in b) {
            if (b.hasOwnProperty(q)) {
                result[q] = b[q];
            }
        }
        return result;
    }

    /**
     * 追踪器构造器
     *
     * @param {string} name 追踪器名称
     */
    function Tracker(name) {
        this.name = name;
        this.fields = {
            protocolParameter: {
                postUrl: null,
                protocolParameter: null
            }
        };
        this.argsList = [];
        this.alog = entry;
    }

    /**
     * 获取追踪器
     *
     * @param {string} trackerName 追踪器名称，如果为 '*' 则获取全部追踪器
     * @return {Object|Array} 返回追踪器对象
     */
    function getTracker(trackerName) {
        trackerName = trackerName || 'default';
        if (trackerName === '*') {
            var result = [];
            for (var p in trackers) {
                if (trackers.hasOwnProperty(p)) {
                    result.push(trackers[p]);
                }
            }
            return result;
        }
        return (trackers[trackerName] =
            trackers[trackerName] || new Tracker(trackerName));
    }
    /**
     * 创建追踪器
     *
     * @param {Object} fields 字段列表
     */
    Tracker.prototype.create = function (fields) {
        if (this.created) {
            return;
        }

        if (typeof fields === 'object') {
            this.set(fields);
        }
        this.created = new Date();
        this.fire('create', this);
        var args;
        while (args = this.argsList.shift()) {
            command.apply(this, args);
        }
    };

    /**
     * 发送日志数据
     *
     * @param {string} hitType 数据类型
     * @param {Object} fieldObject 发送数据
     */
    Tracker.prototype.send = function (hitType, fieldObject) {
        var data = merge({
            ts: timestamp().toString(36),
            t: hitType,
            sid: sid
        }, this.fields);

        if (typeof fieldObject === 'object') {
            data = merge(data, fieldObject);
        }
        else {
            var args = arguments;
            switch (hitType) {
                case 'pageview':
                    // [page[, title]]
                    if (args[1]) {
                        data.page = args[1];
                    }
                    if (args[2]) {
                        data.title = args[2];
                    }
                    break;
                case 'event':
                    // eventCategory, eventAction[, eventLabel[, eventValue]]
                    if (args[1]) {
                        data.eventCategory = args[1];
                    }
                    if (args[2]) {
                        data.eventAction = args[2];
                    }
                    if (args[3]) {
                        data.eventLabel = args[3];
                    }
                    if (args[4]) {
                        data.eventValue = args[4];
                    }
                    break;
                case 'timing':
                    // timingCategory, timingVar, timingValue[, timingLabel]
                    if (args[1]) {
                        data.timingCategory = args[1];
                    }
                    if (args[2]) {
                        data.timingVar = args[2];
                    }
                    if (args[3]) {
                        data.timingValue = args[3];
                    }
                    if (args[4]) {
                        data.timingLabel = args[4];
                    }
                    break;
                case 'exception':
                    // exDescription[, exFatal]
                    if (args[1]) {
                        data.exDescription = args[1];
                    }
                    if (args[2]) {
                        data.exFatal = args[2];
                    }
                    break;
                default:
                    return;
            }
        }
        this.fire('send', data);
        report(this.fields.postUrl, runProtocolParameter(this.fields.protocolParameter, data));
    };

    /**
     * 设置字段值
     *
     * @param {string} name 字段名
     * @param {Any} value 字段值
     */
    Tracker.prototype.set = function (name, value) {
        if (typeof name === 'string') {
            if (name === 'protocolParameter') {
                value = merge({
                    postUrl: null,
                    protocolParameter: null
                }, value);
            }
            this.fields[name] = value;
        }
        else if (typeof name === 'object') {
            for (var p in name) {
                if (name.hasOwnProperty(p)) {
                    this.set(p, name[p]);
                }
            }
        }
    };

    /**
     * 获取字段值
     *
     * @param {string} name 字段名
     * @param {Function} callback 回调函数
     * @return {Object} 返回当前实例
     */
    Tracker.prototype.get = function (name, callback) {
        var result = this.fields[name];
        if (typeof callback === 'function') {
            callback(result);
        }
        return result;
    };

    /**
     * 触发事件
     *
     * @param {string} eventName 事件名
     * @return {Object} 返回当前实例
     */
    Tracker.prototype.fire = function (eventName) {
        var items = [this.name + '.' + eventName];

        var args = arguments;
        for (var i = 1, len = args.length; i < len; i++) {
            items.push(args[i]);
        }
        return fire.apply(this, items);
    };

    /**
     * 绑定事件
     *
     * @param {string} eventName 事件名
     * @param {Function} callback 回调函数
     */
    Tracker.prototype.on = function (eventName, callback) {
        entry.on(this.name + '.' + eventName, callback);
    };

    /**
     * 注销事件
     *
     * @param {string} eventName 事件名
     * @param {Function} callback 回调函数
     */
    Tracker.prototype.un = function (eventName, callback) {
        entry.un(this.name + '.' + eventName, callback);
    };

    entry.name = 'alog';
    entry.sid = sid;
    entry.defined = true;
    entry.timestamp = timestamp;
    entry.un = un;
    entry.on = on;
    entry.fire = fire;
    entry.tracker = getTracker;
    entry._GET = _GET;
    entry.cookie = cookie;

    entry('init');

    defaultTracker = getTracker();
    defaultTracker.set('protocolParameter', {
        modules: null
    });

    if (oldObject) {
        // 处理临时alog对象
        var items = [].concat(oldObject.p || [], oldObject.q || []);
        oldObject.p = oldObject.q = null; // 清理内存
        for (var p in entry) {
            if (entry.hasOwnProperty(p)) {
                oldObject[p] = entry[p];
            }
        }
        entry.p = entry.q = { // 接管之前的定义
            push: function (args) {
                entry.apply(entry, args);
            }
        };

        // 开始处理缓存命令
        for (var i = 0; i < items.length; i++) {
            entry.apply(entry, items[i]);
        }
    }
    win[objectName] = entry;

    /*<ie>*/
    if (ie) {
        on(doc, 'mouseup', function (e) {
            var target = e.target || e.srcElement;
            if (target.nodeType === 1 && /^ajavascript:/i.test(target.tagName + target.href)) {
                clickJsLinkTime = new Date();
            }
        });
    }
    /*</ie>*/

    /**
     * 页面关闭时处理方法
     */
    function unloadHandler() {
        /*<ie>*/
        // @see http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
        // Click an anchor that refers to another document.
        // 修复 IE 中点击 `<a href="javascript:">...</a>` 也会触发 beforeunload 事件的问题
        if (ie && (new Date() - clickJsLinkTime < 50)) {
            return;
        }
        /*</ie>*/

        if (closing) {
            return;
        }
        closing = true;

        var sleepCount = 0;
        for (var p in trackers) {
            if (trackers.hasOwnProperty(p)) {
                var tracker = trackers[p];
                if (tracker.created) {
                    sleepCount += tracker.fire('unload');
                }
            }
        }

        if (sleepCount) {// isSleep
            var isSleep = new Date();
            while ((new Date() - isSleep) < 100) {
            }
        }
    }
    on(win, 'beforeunload', unloadHandler);
    on(win, 'unload', unloadHandler);
})(window, document);

(function(win, doc) {
    var objectName = win.alogObjectName || 'A_Log';
    var A_Log = win[objectName] = win[objectName] || function() {
        win[objectName].l = win[objectName].l || +new Date;
        (win[objectName].q = win[objectName].q || []).push(arguments);
    };
	var trackerName='speed';
    A_Log('define', trackerName, function() {
        var tracker = A_Log.tracker(trackerName);
        var timestamp = A_Log.timestamp; // 获取时间戳的函数，相对于alog被声明的时间
		var getOs=function (){
			var nav=navigator.userAgent.toLowerCase(),plat=navigator.platform;
			if(nav){
				if(nav.match(/windows phone/i))  return "Windows Phone";
				if(nav.match(/windows/i)){
					if(nav.indexOf("windows nt 10") > -1 || nav.indexOf("windows 10") > -1) return "Win10";
					if(nav.indexOf("windows nt 6.3") > -1 || nav.indexOf("windows 8.1") > -1) return "Win8.1";
					if(nav.indexOf("windows nt 6.2") > -1 || nav.indexOf("windows 8") > -1) return "Win8";
					if(nav.indexOf("windows nt 6.1") > -1 || nav.indexOf("windows 7") > -1) return "Win7";
					if(nav.indexOf("windows nt 6.0") > -1 || nav.indexOf("windows vista") > -1) return "WinVista";
					if(nav.indexOf("windows nt 5.2") > -1 || nav.indexOf("windows 2003") > -1) return "Win2003";
					if(nav.indexOf("windows nt 5.1") > -1 || nav.indexOf("windows xp") > -1) return "WinXP";
					if(nav.indexOf("windows nt 5.0") > -1 || nav.indexOf("windows 2000") > -1) return "Win2000";
					return "otherWin";
				} 	
				if(nav.match(/android/i)) return "Android";
				if(nav.match(/iphone/i))  return "iPhone";
				if(nav.match(/ipad/i))  return "iPad"; 
				if(nav.match(/ipod/i))  return "iPod"; 
			}
			if(plat && (plat == "Mac68K" || plat == "MacPPC" || plat == "Macintosh" || plat == "MacIntel"))  return "Mac";
			return "other";
		};
		var getRefer=function(){
			try{
				var refer=document.referrer;
				if (refer != '') {
					domain = refer.match(/http[s]?:\/\/www\.([^\/]+)\//)[1];
					var kwArr = [
					["so.com","q","360"],
					["google.com.tw","q","谷歌台湾"],
					["google.com","q","谷歌"],
					["google.com.hk","q","谷歌香港"],
					["baidu.com","wd|word","百度"],
					["soso.com","w","搜搜"],
					["yahoo.com","q","雅虎"],
					["bing.com","q","bing"],
					["sogou.com","query","搜狗"],
					["youdao.com","q","有道" ]
					];
					for(var item in kwArr){
						var $dm = kwArr[item][0];
						var $kw = kwArr[item][1];
						var $name = kwArr[item][2];
						if (domain == $dm){
							eval("var reg = /[?&](" + $kw + ")=([^&]*)(&|$)/ig;");
							var keywordCollection = reg.exec(refer);
							while (keywordCollection != null) {
								keyword = RegExp.$2 + ",";
								keywordCollection = reg.exec(refer);
							}
							if (keyword != '') {
								keyword = keyword.substring(0, keyword.length - 1);
							}
							return [$name,decodeURI(keyword)];
						}
					}
				}
				return ['',''];
			}catch(e){
				return ['',''];
			}
		};
		var getBrower=function() {
			var ua = navigator.userAgent.toLowerCase();//alert(ua);
	　　　　if(/micromessenger/.test(ua)) return 'weixin'; // 微信内置浏览器
			if(/mqqbrowser/.test(ua) && / qq/.test(ua)) return 'QQ App'; // QQ浏览器
			if(/mqqbrowser/.test(ua)) return 'qq'; // QQ浏览器
			if(/metasr/.test(ua)) return 'sogou'; // 搜狗浏览器
			if(/bidubrowser/.test(ua)) return 'Baidu';// 百度浏览器
			if(/baiduboxapp/.test(ua)) return 'Baidu App';// 百度浏览器
			if(/ucweb/.test(ua)) return 'uc'; // UC浏览器
			if(/360se/.test(ua)) return '360';// 360浏览器
			if(/lbbrowser/.test(ua)) return 'lb'; // 猎豹浏览器
			if(window.opr)return 'Opera';
			if(window.chrome)  return 'Chrome'; // Chrome浏览器
			if(/firefox/.test(ua)) return 'Firefox'; // 火狐浏览器
			if(/safari/.test(ua) && !/chrome/.test(ua)) return 'Safari';  // safire浏览器
			if(/msie 6.0/.test(ua)) return 'IE6'; // IE6
			if(/msie 7.0/.test(ua)) return 'IE7'; // IE7
			if(/msie 8.0/.test(ua)) return 'IE8'; // IE8
			if(/msie 9.0/.test(ua)) return 'IE9'; // IE9
			if(/msie 10.0/.test(ua)) return 'IE10'; // IE10
			if(/msie 11.0/.test(ua) || /rv:11.0/.test(ua)) return 'IE11'; // IE11
			return 'other';
		}		
		var getData=function(){
			var begain=new Date();
			var re=getRefer();
			var $return={
					f: self!=top ? 1 : 0,//是否子窗口
					uid: A_Log._GET('Alog_Uid') || '',//有传参访客标识 直接用
					vid: A_Log._GET('Alog_Vid') || '',//有访问标识   直接用
					b1: tracker.get('b1') || '',//页头加载时间
					b2: tracker.get('b2') || '',
					//drt: timestamp(tracker.get('domready')),
					su:	A_Log._GET('Alog_Su') || (doc.referrer ? doc.referrer:''),//来源
					ds: (win.screen.width || 0) + "x" + (win.screen.height || 0),//分辩率
					cl: win.screen.colorDepth + "-bit" || 0,//颜色
					u:	doc.location.href,//访问url
					os:	getOs(),//操作系统
					lan: navigator.language || navigator.browserLanguage || navigator.systemLanguage || navigator.userLanguage || "",
					bs: getBrower(),//浏览器
					r1: re[0],//来源  百度
					r2: re[1],//来源 关键字
					//up: A_Log.cookie.get('alog_update_page')//重复打开次数
				};
			//console.log((new Date())-begain);
			return $return;
		};
		
        if(!win.alog_No3) tracker.on('record', function(type, json) {
            var data = getData();
            data['p'] = JSON.stringify(json);
            tracker.send(type, data);//50为 json提交下单  
        });
		
       if(!win.alog_No2) tracker.on('unload', function(){
            tracker.send('2',getData());//2关闭时提交   不能改
        });
        /*配置字段，不需要上报
		tracker.set('protocolParameter', {
            headend: null,
            bodyend: null,
            domready: null
        });*/
        tracker.create({
            postUrl: 'http://zktj.net/index.gif'
        });
		
        if(!win.alog_No1) tracker.send('1', getData());//1是访问提交   不能改
		
		//A_Log.cookie.set('alog_update_page',parseInt(A_Log.cookie.get('alog_update_page') || 0)+1);
		//console.log(A_Log.cookie.get('alog_update_page'));
        return tracker;
    });
	//console.log('speed ok');
})(window, document);