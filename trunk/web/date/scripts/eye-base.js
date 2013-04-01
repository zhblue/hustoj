
/********************************************
 * 模块名称：base
 * 功能说明：Javascript原型方法
 * 创 建 人：邵先军
 * 创建时间：2008-10-06
 * 修 改 人：
 * 修改时间：
 * ******************************************/
 
//=========================================字符串处理==============================================//

/// <summary>
/// 为JS验证方法提供获取中文长度.getChLength()属性
/// </summary>   
String.prototype.getChLength = function(){   
    return this.replace(/[\u4e00-\u9fa5]/g, '  ').length;
}
    
/// <summary>
/// 为JS验证方法提供获取双字节长度.GetDbLength()属性
/// </summary>   
String.prototype.getDcLength = function(){   
    return this.replace(/[^x00-xff]/g, '  ').length;
}

/// <summary>
/// 为JS验证方法提供.trim()方法
/// </summary>   
String.prototype.trim = function(){   
    return this.replace(/(^\s*)|(\s*$)/g, '');   
} 

/// <summary>
/// 为JS验证方法提供.ltrim()方法
/// </summary>   
String.prototype.ltrim = function(){   
    return this.replace(/(^\s*)/g, '');   
} 

/// <summary>
/// 为JS验证方法提供.rtrim()方法
/// </summary>   
String.prototype.rtrim = function(){   
    return this.replace(/(\s*$)/g, '');   
}  

/// <summary>
/// 判断输入内容是否为空
/// </summary>   
String.prototype.isNull = function(){  
    return this.trim().length == 0 ? true : false; 
}         

/// <summary>
/// 判断输入的字符是否为整数
/// </summary>    
String.prototype.isInteger = function(){     
    var reg = /^([0-9]|[1-9][0-9]*)$/; 
    return this.Regular(reg);
}

/// <summary>
/// 判断输入的字符是否为时间整数，0，00，01...
/// </summary>    
String.prototype.isTimeInt = function() {
var reg = /^([0-9]|[0-9][0-9]|[1-9][0-9]*)$/;
    return this.Regular(reg);
}    
   
/// <summary>
/// 判断输入的字符是否为双精度,包含2位小数
/// </summary>    
String.prototype.isDouble = function(){   
    var reg = /^[0-9]+(.[0-9]{1,2})?$/; 
    return this.Regular(reg);  
} 
   
/// <summary>
/// 判断输入的字符是否为英文字母
/// </summary>   
String.prototype.isLetter = function(){   
    var reg = /^[a-zA-Z]+$/;  
    return this.Regular(reg);
}         
 
/// <summary>
/// 判断输入的字符是否为中文
/// </summary>  
String.prototype.isChinese = function(){   
    var reg = /^[\u0391-\uFFE5]+$/; //[\u4E00-\u9FA5]
    return this.Regular(reg); 
} 

/// <summary>
/// 验证字符串是否半角和中文字符
/// </summary>
String.prototype.isHalfOrChinese = function(){ 
    var reg = /^[^\uFF00-\uFFFF]+$/;
    return this.Regular(reg); 
}    
  
/// <summary>
/// 验证字符串是否半角字符
/// </summary>
String.prototype.isHalfWidth = function(){ 
    var reg = /^[\u0000-\u00FF]+$/;
    return this.Regular(reg); 
}

/// <summary>
/// 验证字符串是否全角字符
/// </summary>
String.prototype.isFullWidth = function(){ 
    var reg = /^[\uFF00-\uFFFF]+$/;
    return this.Regular(reg); 
}

/// <summary>
/// 判断输入的字符是否为英文字母\数字\下划线
/// </summary>   
String.prototype.isVersion = function(){    
    var reg = /^([a-zA-Z_])([a-zA-Z0-9_.])*$/;  
    return this.Regular(reg);
}

/// <summary>
/// 判断输入的字符串，不包括单引号
/// </summary>
String.prototype.isString = function(){    
    var reg = /^[^']*$/; 
    return this.Regular(reg); 
}     
 
/// <summary>
/// 判断输入的email格式是否正确 
/// </summary>
String.prototype.isEmail = function(){   
    var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    return this.Regular(reg); 
} 
  
/// <summary>
/// 判断输入是否IP
/// </summary> 
String.prototype.isIP = function(){  
    var reg = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;   
    return this.Regular(reg); 
}

/// <summary>
/// 判断日期类型是否为年月日
/// </summary>
String.prototype.isDate = function() {
    var reg = /^((\d{2}(([02468][048])|([13579][26]))[\-\/\s]?((((0?[13578])|(1[02]))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\-\/\s]?((0?[1-9])|([1-2][0-9])))))|(\d{2}(([02468][1235679])|([13579][01345789]))[\-\/\s]?((((0?[13578])|(1[02]))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\-\/\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))?$/;
    return this.Regular(reg);
}

/// <summary>
/// 判断日期类型是否为年月日或年月日时分秒格式
/// </summary>
String.prototype.isDateTime = function() {
    var reg = /^((\d{2}(([02468][048])|([13579][26]))[\-\/\s]?((((0?[13578])|(1[02]))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\-\/\s]?((0?[1-9])|([1-2][0-9])))))|(\d{2}(([02468][1235679])|([13579][01345789]))[\-\/\s]?((((0?[13578])|(1[02]))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\-\/\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\-\/\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))(\s(((0?[0-9])|(1[0-9])|(2[0-3]))\:([0-5]?[0-9])((\s)|(\:([0-5]?[0-9])))))?$/;
    return this.Regular(reg);
}

/// <summary>
/// 判断输入的邮编(只能为六位)是否正确
/// </summary>   
String.prototype.isZIP = function(){  
    var reg = /^\d{6}$/;   
    return this.Regular(reg);  
} 

/// <summary>
/// 判断输入的身份证号(15，18位)是否正确
/// </summary>  
String.prototype.isIDCard = function(){  
    var reg = /(\d{15}$)|(\d{17}(?:\d|x|X)$)/; 
    return this.Regular(reg);  
} 

/// <summary>
/// 特殊字符编码
/// </summary>
String.prototype.charEncode  = function(){
    return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\'/g, '&apos;').replace(/\"/g, '&quot;').replace(/ /g, '&nbsp;');
}

/// <summary>
/// 特殊字符解码
/// </summary>
String.prototype.charDecode  = function(){
    return this.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&apos;/g, '\'').replace(/&quot;/g, '\"').replace(/&nbsp;/g, ' ');
}

/// <summary>
/// 公共正则表达式处理函数
/// </summary>
String.prototype.Regular = function(reg){
    var result = true;
    if(this.length > 0){       
        if(!reg.test(this)){   
            result = false;
        }  
    } 
    return result;
}

/// <summary>
/// 格式化日期
/// </summary>
/// <param name="date">日期对象</param>
Date.prototype.format = function(date) { 
    var o = { 
        "M+" : this.getMonth()+1,  //月份 
        "d+" : this.getDate(),     //日 
        "h+" : this.getHours(),    //小时 
        "m+" : this.getMinutes(),  //分 
        "s+" : this.getSeconds(),  //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S" : this.getMilliseconds() //毫秒 
    }; 
    
    if(/(y+)/.test(date)) {
        date = date.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
    
    for(var k in o){
        if(new RegExp("("+ k +")").test(date)){
            date = date.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length))); 
        }
    }
    
    return date;
}