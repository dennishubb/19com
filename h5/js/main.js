var api_domain = 'https://app.19.com';
var img_url = 'https://www.19.com';

function binduserinfo() {
    var euid = Cookies.get('euid');

    if (euid != undefined) {

        $.ajax({
            url: api_domain + '/service/user.php',
            type: 'post',
            data: {"action":"getuserinfo","euid":euid},
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },

            success: function (response, status, xhr) {
                if (response.status == 200) {
                    var user_data = response.user;
                    // var user_lvl_icon = '/assets/images/user_level_icon/lvl'+user_data.level_id+'_white.png';
                    
                    // $('#profile_summary_thumbnail').attr('src', user_data.image);
                    // $('#header_container_thumbnail').attr('src', user_data.image);

                    // $('#span_user_name').html(user_data.username);
                    // $('#after_username').html(user_data.username);
                    // $('#span_user_level').html(user_data.level);
                    // $('#img_user_level').attr('src', user_lvl_icon);
                    // $('#span_user_vouchers').html(parseInt(user_data.voucher));
                    // $('#span_total_points').html(parseInt(user_data.total_points));
                    // $('#span_current_points').html(parseInt(user_data.points));

                    window.localStorage.user_id = user_data.id;
                    window.localStorage.username = user_data.username;
                    window.localStorage.level = user_data.level;
                    window.localStorage.level_id = user_data.level_id;
                    window.localStorage.profile_thumbnail = user_data.image;
                    window.localStorage.voucher = parseInt(user_data.voucher);
                    window.localStorage.points = parseInt(user_data.points);
                    window.localStorage.weekly_points = user_data.weekly_points;
                    window.localStorage.total_points = parseInt(user_data.total_points);

                }
                else if (response.status == -201) {
                    Cookies.remove('euid');
                }
            },
            error: function () {
            },
        });
    }
}

$(function(){
    $("body").append(`<div class="loading">
        <div>
            <div></div>
            <div>加载中...</div>
        </div>
    </div>`);

    // Start search box
    $(".search_box #search_box").click(function(){
        $(".search_box #header_search").toggleClass("active");
    });

    $(document).bind('touchstart', function (e) {
        if ($(e.target)[0].id != "search_box" && $(e.target)[0].id != "header_search") {
            $(".search_box #header_search").removeClass("active");
        }

        if($(e.target).attr("class") != "report_btn"){
            $(".report_him .report_listing").stop().slideUp(300);
        }

        if($(e.target).id != "social_more"){
            $("#social_more .article_social_listing").stop().slideUp(300);
        }
    });
    // End search box
    
    $("#watch_scorer_btn").click(function(){
        $("#global_menu_show").hide();
        $("#global_menu").removeClass("active");
        $(this).toggleClass("active");
        $("#watch_scorer_show").stop().slideToggle(300);
        if($(this).hasClass("active"))
            $("html, body").addClass("noscroll");
        else
            $("html, body").removeClass("noscroll");
    });
    
    $("#global_menu").click(function(){
        $("#watch_scorer_show").hide();
        $("#watch_scorer_btn").removeClass("active");
        $(this).toggleClass("active");
        $("#global_menu_show").stop().slideToggle(300);
        if($(this).hasClass("active"))
            $("html, body").addClass("noscroll");
        else
            $("html, body").removeClass("noscroll");
    });

    if($(".login_register_tab").length > 0){
        $(".login_register_tab a").click(function(e){
            e.preventDefault();
            
            $(".login_register_tab a").removeClass("active");
            $(this).addClass("active");
            var tabid = $(this).data('id');
            var left = $(this).index() * 100;
            if(left > 0){
                $(".login_register_tab .tab_bg").css("left", left+"%");
            }
            else{
                $(".login_register_tab .tab_bg").removeAttr("style");
            }

            $(".my_login_container").hide();
            $("#my_"+tabid).stop().fadeIn(300);
        });

        if(window.location.hash == "#login"){
            $("#global_menu").trigger("click");
        }
    }

    // Start main menu
    $("#global_menu_categories a").click(function(e){
        e.preventDefault();
        var thisData = $(this).data("id");
        $("#global_menu_categories a, .global_menu_list").removeClass("active");
        $(this).addClass("active");
        $(".global_menu_list."+thisData).addClass("active");
    });

    $(".global_menu_list a").click(function(){
        $(".global_menu_list a").removeClass("active");
        $(this).addClass("active");
        $("#global_menu").removeClass("active");
        $("#global_menu_show").stop().slideUp(300);
    });
    // End main menu

    // Start sub menu
    if($(".submenu_container .swiper-container").length > 0){
        var submenuSwiper = new Swiper('.submenu_container .swiper-container', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            freeMode: true
        });
        setTimeout(function(){
            submenuSwiper.update();
        }, 500);
    }
    // End sub menu

    //User register
    $('#register_form').submit(function (e){
        e.preventDefault();
        $('.loading').show();
        var form = $(this);
        var url = api_domain + '/service/user.php';
        $.ajax({
            method: "GET",
            url: url,
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            data: form.serialize(),
            success: function(data)
            {
                if(data['status'] == 200){
                    alert('注册成功');
                    Cookies.set('euid', data.euid, { expires: 30, path: '/' });
                    location.reload();
                }else{
                    document.getElementById('register_form').reset();
                    alert(data.message);
                    $(".loading").hide();
                }
            }
        });
    });

    $('#login_form').submit(function (e){
        e.preventDefault();
        $('.loading').show();
        var keeplogin = document.getElementById('keeplogin').checked ;
        var form = $(this);
        var url = api_domain + '/service/user.php';
        $.ajax({
            method: "GET",
            url: url,
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            data: form.serialize(),
            success: function(data)
            {
                if(data['status'] == 200){
                    Cookies.set('euid', data.euid, { expires: 30, path: '/' });
                    alert('登录成功');
                    location.reload();
                }else{
                    document.getElementById('login_form').reset();
                    alert(data.message);
                    $(".loading").hide();
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });
    });


    $("#reset_account_setting").click(function (e) {
        document.getElementById('account_setting').reset();
    })
	$("#reset_change_pw").click(function (e) {
        document.getElementById('change_pw').reset();
    })

    $("#account_setting").submit(function (e) {
        e.preventDefault();
        $('.loading').show();
        var euid = decodeURIComponent(getCookie('euid'));
        var name = document.getElementsByName('username')[0].value;
        var email = document.getElementsByName('email')[0].value;
        var address = document.getElementsByName('address')[0].value;
        var weibo = document.getElementsByName('weibo')[0].value;
        var phone = document.getElementsByName('phone')[0].value;//2020-01-01
        var birth_at = document.getElementsByName('dob-year')[0].value + '-' + document.getElementsByName('dob-month')[0].value + '-' + document.getElementsByName('dob-day')[0].value;
        var url = api_domain + '/service/user.php';
        $.ajax({
            method: "POST",
            url: url,
            data: {euid:euid,name:name,email:email,address:address,weibo:weibo,phone:phone,birth_at:birth_at,action:'update_userinfo'},
            success: function(data)
            {
                console.log(data);
                if(data!=null || data != ''){
                    if(data['status'] == 200){
                        alert(data['message']);
                    }
                }
                $('.loading').hide();
                location.reload();
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });

    });
	
	$("#change_pw").submit(function (e) {
        e.preventDefault();
        $('.loading').show();
        var euid = decodeURIComponent(getCookie('euid'));
        var current_password = document.getElementsByName('current_password')[0].value;
        var new_password = document.getElementsByName('new_password')[0].value;
        var new_password_confirm = document.getElementsByName('new_password_confirm')[0].value;
		//console.log(current_password,new_password,new_password_confirm)
		
		if (new_password!=new_password_confirm){
			alert('密码与确认密码不一致');
			$('.loading').hide();
			return false;
		}
		
		
        var url = api_domain + '/service/user.php';
        $.ajax({
            method: "POST",
            url: url,
            data: {euid:euid,password:new_password,action:'update_password'},
            success: function(data)
            {
                console.log(data);
                if(data!=null || data != ''){
                    if(data['status'] == 200){
                        alert(data['message']);
                    }
                }
                $('.loading').hide();
                location.reload();
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });

    });
	
	$("#logout").click(function (e) {
        var confirm_logout= confirm('确定登出？');
		
        if(!confirm_logout){
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
			window.location = 'index.php';
        }
		
	});
    binduserinfo();
});

Array.prototype.max = function() {
    return Math.max.apply(null, this);
};

function numberAddCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=0'
}

function updateLeaguest(target){
    if(target == 'winchanges') {
        $("#profile_winchanges_leaguest").empty();
    }
    else {
        $("#profile_prophet_leaguest").empty();
    }

    if(target == 'winchanges'){
        var categoryId = document.getElementById("profile_winchanges_category").value;
    }else{
        var categoryId = document.getElementById("profile_prophet_category").value;
    }
    var url = api_domain+'/service/match.php';
    $.ajax({
        method: "GET",
        url: url,
        async: false,
        data:{action:'get_leagues',category_id:categoryId} ,
        success: function(data)
        {
            if(data != null || data != '') {
                data.forEach(function (item) {
                    if(target == 'winchanges') {
                        $('#profile_winchanges_leaguest').append('<option value="' + item['id'] + '">' + item['name_zh'] + '</option>');
                    }else {
                        $('#profile_prophet_leaguest').append('<option value="' + item['id'] + '">' + item['name_zh'] + '</option>');
                    }
                });
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

function getMyProphet(){
    var url = api_domain+'/service/prediction.php';
    var cid = document.getElementById('profile_prophet_category').value ;
    var lid = document.getElementById('profile_prophet_leaguest').value ;
    var year = document.getElementById('profile_prophet_year').value ;
    var month =document.getElementById('profile_prophet_month').value;
    var euid = decodeURIComponent(getCookie('euid'));
    $("#my_prophet_result").empty();
    $.ajax({
        method: "POST",
        url: url,
        data:{action:'get_prediction_history',category_id:cid,league_id:lid,year:year,month:month,euid:euid} ,
        success: function(data)
        {
            console.log(data);
            if(data != null || data != '') {
                var listData = data['list'];
                for (const [key, value] of Object.entries(listData)) {
                    var match_at = value['match_at'].split(" ");
                    var created_at = value['created_at'].split(" ");
                    var handicap = value['handicap'].split(" ");
                    console.log('key: '+ key + " data: " + created_at[0]);
                    $('#my_prophet_result').append('<tr> <td>'+ created_at[0] +' <div>'+ created_at[1] +'</div></td>'+
                        '<td>'+match_at[0]+' <div>'+match_at[1]+'</div></td>'+
                        '<td>'+value['handicap']+'</td> <td>'+value['over_under']+'</td> <td>'+value['single']+'</td> <td>'+value['win_amount']+'</td> <td>'+ value['status']+'</td>'+
                        '</tr>');
                }
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}
