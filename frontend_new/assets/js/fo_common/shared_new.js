// var link="http://19com_back/";
//var link="http://test.19com.backend:5280/";
var link = getBackendHost();
var mainHost = window.location.protocol + "//" + window.location.host + "/";
var pathname = window.location.pathname;

var news_limit = "";
var banner_id = "";
var burgermenu_header_title='';

//for header carousel
var basketball_arr=[1,3];
var football_arr=[1423,1461,1469,1449,1482,1877,1388,1827,1387];
var popular_arr=[1,3,1423,1461,1469,1449,1482,1877,1388,1827,1387];//current is combination of both basketball and football arr

$( document ).ajaxComplete(function( event, xhr, settings ) {	
    if (xhr.responseJSON) {	
        if (xhr.responseJSON.code === 401) {	
            embed_login_popup()	
            $("#login_popup").modal("show");	
        }	
    }	
});

function dim_bg(){   
    /*$('#alertFram').modal({
        backdrop: true
    })*/
    
    $('body').append('<div class="modal-backdrop fade show"></div>')
}

function light_bg(){     
    $('.modal-backdrop').remove();
}

alert = function(str)
{
    var yValue = document.documentElement.scrollTop || document.body.scrollTop;
    var shield = document.createElement("DIV"); 
    shield.id = "shield"; 
    shield.style.height = document.body.scrollHeight+"px";
    shield.style.background = "#fff"; 
    shield.style.textAlign = "center"; 
    shield.style.zIndex = "25";
    var alertFram = document.createElement("DIV"); 
    alertFram.id="alertFram"; 
    alertFram.style.position = "absolute"; 
    alertFram.style.left = "50%"; 
    alertFram.style.top = (yValue+200)+"px"; 
    alertFram.style.marginLeft = "-225px"; 
    alertFram.style.marginTop = "-75px"; 
    alertFram.style.width = "450px"; 
    alertFram.style.height = "150px";
    alertFram.style.textAlign = "center"; 
    //alertFram.style.lineHeight = "150px";
    alertFram.style.zIndex = "9999";
    
    //strHtml = "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\"  aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n";
    strHtml = "<div class=\"modal-dialog\" role=\"document\">\n";
    strHtml += "<div class=\"modal-content\">\n";
    strHtml += "<div class=\"modal-header\" style=\'padding: 5px;\'>\n";
    strHtml += "<h5 class=\"modal-title\" id=\"exampleModalLabel\">提示</h5></div>\n";
    strHtml += "<div class=\"modal-body\" style=\'padding:1rem 15%;\'>\n";
    strHtml += "<span style=\"color:grey\">"+str+"</span>\n";
    strHtml += "<div style=\"width: 100%;\">\n";
    strHtml += "<div>\n";
    strHtml += "<button style=\'background-color: rgb(237, 27, 52);height: 32px;border: 0px;border-radius: 8px;color: white;width: 140px;margin: 0 auto;margin-top: 15px;\' data-bb-handler=\"ok\" type=\"button\" onclick=\"light_bg();doOk()\">确认</button>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    alertFram.innerHTML = strHtml; 
    document.body.appendChild(alertFram); 
    document.body.appendChild(shield);
    
    //$('#alertFram').addClass('asas');
    //$('#shield').addClass('asas');
    
    this.doOk = function(){ 
    //alertFram.style.display = "none"; 
    //shield.style.display = "none";
    alertFram.remove();
    shield.remove();
    window.onscroll=function(){window.scrollTo();}
    } 
    alertFram.focus();
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);}
    dim_bg()
    document.body.onselectstart = function(){return false;}; 
} 
confirm = function(str)
{
    var yValue = document.documentElement.scrollTop || document.body.scrollTop;
    var shield = document.createElement("DIV"); 
    shield.id = "shield"; 
    shield.style.height = document.body.scrollHeight+"px";
    shield.style.background = "#fff"; 
    shield.style.textAlign = "center"; 
    shield.style.zIndex = "25";
    var alertFram = document.createElement("DIV"); 
    alertFram.id="confirmFram"; 
    alertFram.style.position = "absolute"; 
    alertFram.style.left = "50%"; 
    alertFram.style.top = (yValue+200)+"px"; 
    alertFram.style.marginLeft = "-225px"; 
    alertFram.style.marginTop = "-75px"; 
    alertFram.style.width = "450px"; 
    alertFram.style.height = "150px";
    alertFram.style.textAlign = "center"; 
    //alertFram.style.lineHeight = "150px";
    alertFram.style.zIndex = "9999";
    //strHtml = "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\"  aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n";
    strHtml = "<div class=\"modal-dialog\" role=\"document\">\n";
    strHtml += "<div class=\"modal-content\">\n";
    strHtml += "<div class=\"modal-header\" style=\'padding: 5px;\'>\n";
    strHtml += "<h5 class=\"modal-title\" id=\"exampleModalLabel\">提示</h5></div>\n";
    strHtml += "<div class=\"modal-body\" style=\'padding:1rem 15%;\'>\n";
    strHtml += "<span style=\"color:grey\">"+str+"</span>\n";
    strHtml += "<div style=\"width: 100%;\">\n";
    strHtml += "<div>\n";
    strHtml += "<button style=\"background-color: #797979;height: 32px;border: 0px;border-radius: 8px;color: white;width: 140px;margin: 0 auto;margin-top: 15px;margin-right: 10px;\' data-bb-handler=\"ok\" type=\"button\" onclick=\"light_bg();doCancel()\">取消</button>";
    strHtml += "<button style=\'background-color: rgb(237, 27, 52);height: 32px;border: 0px;border-radius: 8px;color: white;width: 140px;margin: 0 auto;margin-top: 15px;\' data-bb-handler=\"true\" type=\"button\" onclick=\"light_bg();doOk()\">确认</button>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    strHtml += "</div>\n";
    //strHtml += "</div>\n";
    alertFram.innerHTML = strHtml; 
    document.body.appendChild(alertFram); 
    document.body.appendChild(shield);
    alertFram.focus();
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);}
    dim_bg()
    document.body.onselectstart = function(){return false;};
    
    return new Promise((resolve, reject) => {
        this.doCancel = function(){
            //alertFram.style.display = "none"; 
            //shield.style.display = "none"; 
            alertFram.remove();
            shield.remove();
            window.onscroll=function(){window.scrollTo();}
            resolve({ confirmed: false })
        }
        this.doOk = function(){ 
            //alertFram.style.display = "none"; 
           // shield.style.display = "none";
            alertFram.remove();
            shield.remove();
            window.onscroll=function(){window.scrollTo();}
            resolve({ confirmed: true })
        } 
    })
}

function logout() {
    confirm('确定登出？').then(result => {
        const confirmed = result.confirmed
        if(!confirmed){
                return;
        }
        else {
            Cookies.remove("euid");
            window.localStorage.removeItem('user_id');
            window.localStorage.removeItem('username');
            window.localStorage.removeItem('level');
            window.localStorage.removeItem('level_id');
            window.localStorage.removeItem('profile_thumbnail');
            window.localStorage.removeItem('voucher');
            window.localStorage.removeItem('points');
            window.localStorage.removeItem('weekly_points');
            window.localStorage.removeItem('total_points');
            
            window.location.href = "/cn";
        }
    });
}

template.defaults.imports.detectMedia = function (upload_url, width = 0, height = 0) {
    var images = ['jpg', 'jpeg', 'png'];    
    var videos = ['mp4'];   
    var image = true;   
    if (upload_url) {   
        if (upload_url.toLowerCase().includes(images)) {    
            // images type  
            image = 1;  
        } else if (upload_url.toLowerCase().includes(videos)) { 
            // videos type  
            image = 0;  
        }   
    }   
    return image;   
};  


template.defaults.imports.html2String = function (html, $limit = 20) {
    if (html) {
        html = html.replace(/\\"/g, '"');
        var element = $(`<div>${html}</div>`);
        element.find('a,iframe').each(function () {
            $(this).replaceWith(``);
        });
        var new_html = element.text();

        return new_html.substring(0, $limit);
    } else {
        return "";
    }
};

template.defaults.imports.dateFormat = function (date, format) {
    var format_date = new Date(date.replace(/\s/, 'T'));
    var day = (format_date.getDate() < 10 ? '0' : '') + format_date.getDate();
    var month = ((format_date.getMonth() + 1) < 10 ? '0' : '') + (format_date.getMonth() + 1);
    var year = format_date.getFullYear();
    var hour = format_date.getHours();
    var minutes = format_date.getMinutes();
    var seconds = format_date.getSeconds();

    if (format == "yyyy-MM-dd") {
        return year + "-" + month + "-" + day;
    } else if (format == "time") {
        return hour + ":" + minutes + ":" + seconds;
    } else if (format == "timestamp") {
        return time_ago(date);
    } else {
        return "NaN";
    }
};

template.defaults.imports.timestamp = function (value) {
    return value * 1000
};

template.defaults.imports.toInt = function ($value) {
    return Math.round($value);
};

//use in match_prediction_carousel()
function isEven(value) {
    if (value % 2 == 0)
        return true;
    else
        return false;
}

function getHeaders() {
    return {Authorization: window.localStorage.access_token};
}

function check_auth(redirect = true) {
    if (window.localStorage.access_token && window.localStorage.user_id) {
    } else {
        if (redirect) {
            window.location.href = "/cn/index.php";
        } else {
            $("#login_popup").modal("show");
            return true;
        }
    }
}
//--feedback form begins
function refreshFeedbackCaptcha() {
    $('#feedback-img-captcha').attr('src','/cn/scripts/get-captcha.php');
}
function addTypeClass(typeId) {
    $(".feedback-type").removeClass("active")
    $("#"+typeId).addClass("active")
}
function submitFeedbackForm() {
    var type = $('.feedback-type.active').html()
    var message = $("#feedback-message").val()
    var email = $("#feedback-email").val()
    var captcha = $("#feedback-captcha-input").val()
    var formData = new FormData();
    formData.append("type", type);
    formData.append("message", message);
    formData.append("email", email);
    formData.append("captcha", captcha);

    $.ajax({
        type: "POST",
        url:'/cn/scripts/feedback.php',
        data: formData,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    }).then(response => {
        response = JSON.parse(response)
        alert(response.statusMsg)
        refreshFeedbackCaptcha();
    }, error => {
        alert('AJAX ERROR - create feedback');
    });
}
function clearFeedbackFrom() {
    $(".feedback-type").removeClass("active")
    $("#feedback-message").val("")
    $("#feedback-email").val("")
}

function time_ago(time) {

    switch (typeof time) {
        case 'number':
            break;
        case 'string':
            time = +new Date(time);
            break;
        case 'object':
            if (time.constructor === Date) time = time.getTime();
            break;
        default:
            time = +new Date();
    }
    var time_formats = [
        [60, '秒', 1], // 60
        [120, '1 分钟前', '1 minute from now'], // 60*2
        [3600, '分钟', 60], // 60*60, 60
        [7200, '1 小时前', '1 hour from now'], // 60*60*2
        [86400, '小时', 3600], // 60*60*24, 60*60
        [172800, '昨天', '明天'], // 60*60*24*2
        [604800, '天', 86400], // 60*60*24*7, 60*60*24
        [1209600, '一个星期前', '下个星期'], // 60*60*24*7*4*2
        [2419200, '星期', 604800], // 60*60*24*7*4, 60*60*24*7
        [4838400, '一个月前', '下个月'], // 60*60*24*7*4*2
        [29030400, '月', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
        [58060800, '去年', '明年'], // 60*60*24*7*4*12*2
        [2903040000, '年', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
        [5806080000, '一个世纪前', '下个世纪'], // 60*60*24*7*4*12*100*2
        [58060800000, '世纪', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
    ];
    var seconds = (+new Date() - time) / 1000,
        token = '前',
        list_choice = 1;

    if (seconds == 0) {
        return '现在'
    }
    if (seconds < 0) {
        seconds = Math.abs(seconds);
        token = '现在起';
        list_choice = 2;
    }
    var i = 0,
        format;
    while (format = time_formats[i++])
        if (seconds < format[0]) {
            if (typeof format[2] == 'string')
                return format[list_choice];
            else
                return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
        }
    return time;
}

function getCurrentDateTime() {
    var dt = new Date();
    var currentDateTime = `${
        dt.getFullYear().toString().padStart(4, '0')}-${
        (dt.getMonth() + 1).toString().padStart(2, '0')}-${
        dt.getDate().toString().padStart(2, '0')} ${
        dt.getHours().toString().padStart(2, '0')}:${
        dt.getMinutes().toString().padStart(2, '0')}:${
        dt.getSeconds().toString().padStart(2, '0')
        }`
    return currentDateTime
}

//get total days of month from a certain year/month
function get_days_of_month(year, month){
	var dt = new Date(year, month, 0).getDate();  
	return dt;
}

//get dropdown list of month
function get_month_dropdown(){
	var  month_dropdown='<option value="01" >一月</option>    '+
						'<option value="02">二月</option>    '+
						'<option value="03">三月</option>        '+
						'<option value="04">四月</option>        '+
						'<option value="05">五月</option>            '+
						'<option value="06">六月</option>          '+
						'<option value="07">七月</option>          '+
						'<option value="08">八月</option>      '+
						'<option value="09">九月</option>'+
						'<option value="10">十月</option>    '+
						'<option value="11">十一月</option>  '+
						'<option value="12">十二月</option>  ';
	
	return month_dropdown;
}

//get dropdown list of year
function get_year_dropdown(before=2,after=2,selected){ //EG CURRENT YEAR = 2020, WILL RETURN FROM YEAR 2020-BEFORE, TO 2020+AFTER
	var year_dropdown='';
	var display_year='';
	var dt = new Date();
	var this_year=parseInt(dt.getFullYear().toString());
	
	var j=0;
	var temp=before;
	
	//CONSTRUCT YEARS BEFORE CURRENT YEAR
	for (i=before;i>=1;i--){
		
		display_year=this_year-temp;
		year_dropdown+='<option value='+display_year+'  >'+display_year+'</option> '
		
		temp--;	
	}
	
	//CONSTRUCT CURRENT YEAR
	year_dropdown+='<option value='+this_year+' >'+this_year+'</option> '
	
	//CONSTRUCT YEARS AFTER CURRENT YEAR
	temp=1;
	for (i=1;i<=after;i++){
		
		display_year=this_year+temp;
		year_dropdown+='<option value='+display_year+' >'+display_year+'</option> '
		
		temp++;	
		j++;
		
		if (j>10)
			break
	}
	//////console.log(year_dropdown);
	//alert(year_dropdown)
	return year_dropdown;
						
	
}

function get_time_dropdown(){
	
	var  time='<option value="00:00-01:00">00:00-01:00</option>    '+
			  '<option value="01:00-02:00">01:00-02:00</option>    '+
			  '<option value="02:00-03:00">02:00-03:00</option>        '+
			  '<option value="03:00-04:00">03:00-04:00</option>        '+
			  '<option value="04:00-05:00">04:00-05:00</option>            '+
			  '<option value="05:00-06:00">05:00-06:00</option>          '+
			  '<option value="06:00-07:00">06:00-07:00</option>          '+
			  '<option value="07:00-08:00">07:00-08:00</option>      '+
			  '<option value="07:00-08:00">07:00-08:00</option>'+
			  '<option value="09:00-10:00">09:00-10:00</option>    '+
			  '<option value="10:00-11:00">10:00-11:00</option>  '+
			   '<option value="11:00-12:00">11:00-12:00</option>  '+
			   '<option value="12:00-13:00">12:00-13:00</option>  '+
			   '<option value="13:00-14:00">13:00-14:00</option>  '+
			   '<option value="14:00-15:00">14:00-15:00</option>  '+
			   '<option value="15:00-16:00">15:00-16:00</option>  '+
			   '<option value="16:00-17:00">16:00-17:00</option>  '+
			   '<option value="17:00-18:00">17:00-18:00</option>  '+
			   '<option value="18:00-19:00">18:00-19:00</option>  '+
			   '<option value="19:00-20:00">19:00-20:00</option>  '+
			   '<option value="20:00-21:00">20:00-21:00</option>  '+
			   '<option value="21:00-22:00">21:00-22:00</option>  '+
			   '<option value="22:00-23:00">22:00-23:00</option>  '+
			   '<option value="23:00-23:59">23:00-23:59</option>  ';
	return time;
			 
}