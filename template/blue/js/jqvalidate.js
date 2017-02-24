/**
* author : ahuing
* date   : 2015-04-10
* name   : jqValidate v1.04
* modify : 2016-4-22
 */
 !function($){function setRegExp(a,b){var c=/^(.+?)(\d+)-(\d+)$/,d=b.match(c);!(b in a.regType)&&d&&$.each(a.regType,$.proxy(function(a){var g,h,i,f=a.match(c);f&&f[1]==d[1]&&(g=this.regType[a].toString(),h=g.match(/\/[mgi]*/g)[1].replace("/",""),i=new RegExp("\\{"+f[2]+","+f[3]+"\\}","g"),g=g.replace(/\/[mgi]*/g,"/").replace(i,"{"+d[2]+","+d[3]+"}").replace(/^\//,"").replace(/\/$/,""),this.regType[b]=new RegExp(g,h),this.regTips.w[b]=this.regTips.w[a].replace(/(.*?)\d+(.+?)\d+(.*)/,"$1"+d[2]+"$2"+d[3]+"$3"))},a))}function Plugin(a){return this.each(function(){var b=$(this),c=b.data("jqvalidate"),d="object"==typeof a&&a;c||(b.data("jqvalidate",c=new Validate(this,d)),c.init()),"string"==typeof a?c[a]():"function"==typeof a&&a.call(this,c)})}var Validate,old;$('<link rel="stylesheet">').appendTo("head").attr("href",("undefined"!=typeof tplurl?tplurl:"")+"css/jqvalidate.css"),String.prototype.Tlength=function(){return this.replace(/[^\x00-\xff]/g,"aa").length},Validate=function(a,b){this.o=$.extend({},Validate.defaults,b),this._self=$(a)},Validate.defaults={submit:'[type="submit"]',vadmode:0,tipmode:0,tipTpl:"$1"},Validate.prototype={regTips:{w:{"*":"请填写此字段","*6-16":"6-16个字符！",n:"请填写数字！","n6-16":"6-16个数字！",s:"不能输入特殊字符！","s6-18":"请填写6到18位字符！",p:"邮政编码格式不对！",m:"手机号码格式不对！",e:"邮箱地址格式不对！",url:"请填写网址！"},e:"请填写邮箱地址",m:"请填写手机号码",s:"请填写字符和数字",p:"请填写邮政编码！",password:"请填写密码",ajax:"正在验证...",checkbox:"请至少选择$1项！",date:"请输入日期",error:"填写内容不正确",init:"请填写此字段",monitorTip:["还能输入","已经超出","个字符"],pass:"&nbsp;",radio:"请选择一项",recheck:"两次填写密码不一致","select-multiple":"按ctrl键进行多选","select-one":"请选择列表中的一项"},regType:{"*":/[\w\W]+/,"*6-16":/^[\w\W]{6,16}$/,n:/^\d+$/,"n6-16":/^\d{6,16}$/,s:/^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]+$/,"s6-18":/^[\u4E00-\u9FA5\uf900-\ufa2d\w\.\s]{6,18}$/,p:/^[0-9]{6}$/,m:/((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/,e:/^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,url:/^(\w+:\/\/)?\w+(\.\w+)+.*$/},setTip:function(a,b,c){var f,g,h,i,j,k,d=$(a),e=d.data();return 1==b||"undefined"==typeof b?(f=e.offset,g=f&&d.nextAll().length>0&&d.nextAll().eq(f-1)||d,b&&g||e.tipmode&&e.tipmode.length>1&&$(e.tipmode)||g.next()):(h=this.o,i=e[b]||c||this.regTips[b],j=e.group&&this._self.find("."+e.group),k=h.tipmode.length>1&&this._self.find(h.tipmode)||j||this.setTip(d),k.html(h.tipTpl.replace("$1",i)).add(a).removeClass("error pass ajax").addClass(b),"pass"==b)},resetForm:function(a){return(a&&$(a)||this.$fmItems).each($.proxy(function(a,b){var f,g,c=$(b),d=c,e=c.data();return e.group&&(d=this._self.find('[data-group="'+e.group+'"]').removeClass("error pass ajax").eq(-1)),d.removeClass("error pass ajax"),this.o.tipmode.length>1?($(this.o.tipmode).removeClass("error"),void 0):(f=this.setTip(d).removeClass("error init monitor pass ajax").addClass(1==this.o.tipmode&&!e.ignore&&"init"||"").html(this.o.tipTpl.replace("$1",e.init)),g=e.monitor,g&&$(1==g&&f||g).html(e.tip),void 0)},this)),a||this._self.removeClass("submitted").trigger("resetForm")[0].reset(),!0},validateBase:function(ele){var checkNum,rr,eleRegex,param,obj,$ele=$(ele),eleData=$ele.data(),eleDType=eleData.type,eleDTypeT=$.type(eleDType),_self=this._self;if(ele=$ele[0],!ele.value)return eleData.ignore&&this.resetForm(ele)||this.setTip(ele,"error",eleData["init"]);if("select-one"==ele.type)return this.setTip(ele,"pass");if(!isNaN(eleDType))return checkNum=_self.find('input[data-option="'+eleData.option+'"]:checked').length,rr=eleDType>checkNum,eleData.ignore&&0==checkNum?this.resetForm(ele):(0>eleDType&&(rr=checkNum>-eleDType||0==checkNum),this.setTip(ele,rr&&"error"||"pass",rr&&eleData["init"]));if("string"==eleDTypeT){if(eleRegex=this.regType[eleDType],/\/.+\//.test(eleDType)&&(eleRegex=eval(eleDType)),eleData.recheck)return ele.value===_self[0][eleData.recheck].value&&this.setTip(ele,"pass")||this.setTip(ele,"error",this.regTips.recheck);if(!eleRegex.test(ele.value.replace(/[^\x00-\xff]/g,"aa")))return this.setTip(ele,"error",this.regTips.w[eleDType]);if(eleData.url)return $ele.hasClass("ajax")||(param={},obj=this,eleData.value=param[eleData.alias||ele.name]=ele.value,obj.setTip(ele,"ajax"),_self.addClass("locked"),setTimeout(function(){$.ajax({url:eleData.url,type:eleData.ajaxType||"GET",dataType:eleData.ajaxDatatype||"json",data:param}).done(function(a){_self.removeClass("locked");var b=function(a,b){eleData.ajaxRes=[a,b],obj.setTip(ele,a,b),_self.hasClass("submitted")&&(_self.removeClass("submitted"),obj.validate())};$ele.hasEvent("ajaxDone")?$ele.trigger("ajaxDone",[a,b]):b(a.result,a.info)}).fail(function(){obj.setTip(ele,"error","服务器请求失败")})},300)),void 0}return this.setTip(ele,"pass")},validateForm:function(){var b,a=this._self;return a.addClass("submitted"),a.hasClass("locked")?!1:(b=!0,this._self.find("[data-type]").each($.proxy(function(c,d){var f,g,h,e=$(d);return e.data("value")!=d.value?f=this.validate(d):(g=e.data("ajaxRes"),f="pass"==g[0],this.setTip(d,g[0],g[1])),e.hasClass("group-last")&&(h=a.find('[data-group="'+e.data("group")+'"]').filter(".error")[0],h&&(f=this.validate(h))),!f&&b&&(b=!1,setTimeout(function(){e.trigger("focus")},0),1==this.o.vadmode||this.o.tipmode.length>1)?!1:void 0},this)),b&&a.hasEvent("validatePass")?(a.trigger("validatePass"),!1):(b&&a.trigger("submit"),!1))},validate:function(a){return this[a?"validateBase":"validateForm"](a)},init:function(){var a=this._self.addClass("jqValidate"),b=this;return a.find("input[data-type][data-option]").each(function(b,c){var d=$(c),e=d.data(),f=a.find('input[data-option="'+e.option+'"]');f.length>1&&(f.eq(-1).attr("data-type",e.type).data(e),d.removeAttr("data-type"))}),b.$fmItems=a.find("[data-type]"),b.$fmItems?(a.attr("novalidate","novalidate"),b.o.tipmode.length>1&&a.find(b.o.tipmode).addClass("tip single"),b.$fmItems.each(function(c,d){var k,l,m,n,o,p,q,e=$(d),f=e,g=e.data(),h=g.type,i="tip ",j=$.inArray(d.type,["text","password","select-one","textarea"])>=0;if("string"==$.type(h)&&setRegExp(b,h),isNaN(h)||(g["init"]=g["init"]||b.regTips[d.type].replace("$1",Math.abs(h)),0>h&&(g["init"]=g["init"].replace("少","多"))),k=g["init"]=g["init"]||b.regTips[d.type]||b.regTips[h]||b.regTips.w[h]||b.regTips["init"],null==g.tipmode&&j&&e.attr("data-tipmode",b.o.tipmode),null==g.vadmode&&j&&e.attr("data-vadmode",b.o.vadmode),g.group){if(a.find("."+g.group).length)return;f=a.find('[data-group="'+g.group+'"]').eq(-1).addClass("group-last"),i+=g.group}if(b.o.tipmode<=2&&(1!=b.o.tipmode||g.ignore||(i+=" init"),l=g.tipmode&&g.tipmode.length>1&&$(g.tipmode)||$("<div>").insertAfter(b.setTip(f,1)),l.addClass(i).html(b.o.tipTpl.replace("$1",k))),g.monitor){if(m=h.match(/^(.+?)(\d+)-(\d+)$/),!m)return;n=g.monitor,o=m[3],p=b.regTips.monitorTip[2],q=b.regTips.monitorTip[0]+'<b class="fco">'+o+"</b>"+p,l=1==n&&l||$(n).html(q),g.tip=q,e.on("input propertychange",function(){var a=d.value.Tlength()-o;l.html(b.regTips.monitorTip[0>=a?0:1]+'<b class="fco">'+(0>=a?-1:1)*a+"</b>"+p).add(this).removeClass("error pass ajax")}).on("focus",function(){l.addClass("monitor")})}}),a.on("click",b.o.submit,$.proxy(b.validateForm,b)).on("keypress",'input[type="text"]',function(a){return 13==a.keyCode?b.validate():void 0}).on("change","select[data-type], [data-url]",function(){a.removeClass("submitted"),b.validate(this)}).on("click","input[data-option]",function(){b.validate(a.find("input[data-option="+$(this).data("option")+"]").get(-1))}).on("focus","[data-tipmode=2]",function(){$(this).data("ignore")||b.setTip(this,"init")}).on("input propertychange","[data-vadmode=2]",function(){$(this).data("url")||b.validate(this)}).on("blur","[data-vadmode=0], [data-vadmode=1]",function(){$(this).data("url")&&this.value||b.validate(this)}).find(this.o.submit).prop("disabled",""),void 0):!1}},$.fn.hasEvent=function(a){var b=$.data(this[0],"events")||$._data(this[0],"events");return b&&b[a]||!1},old=$.fn.jqValidate,$.fn.jqValidate=Plugin,$.fn.jqValidate.Constructor=Validate,$.fn.jqValidate.noConflict=function(){return $.fn.jqValidate=old,this},$(window).on("load",function(){$(".jqValidate").each(function(){var a=$(this);Plugin.call(a,a.data())})})}(jQuery);