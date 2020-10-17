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
	
$.ajaxSetup({
    beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", window.localStorage.access_token);
    }
});

$( document ).ajaxComplete(function( event, xhr, settings ) {	
    if (xhr.responseJSON) {	
        if (xhr.responseJSON.code === 401) {	
			embed_login_popup()	
            $("#login_popup").modal("show");	
        }
		if (xhr.responseJSON.code === 403) {
			window.localStorage.removeItem('access_token');
			window.localStorage.removeItem('user_id');
			window.localStorage.removeItem('username');
			window.localStorage.removeItem('display_name');
			window.localStorage.removeItem('role_id');
			window.localStorage.removeItem('level_id');
			window.localStorage.removeItem('profile_thumbnail');
			
			window.location.href = '/cn';
		}
    }	
});

$(function seo() {
	var fullUri = getCurrentFullUri();
	var cat_id='';
	var article_id='';
	var sub_cat_id='';
	var call_seo=false;
	var api_name='site';
	
	/*
	//if index page
	if ($('#isindex').html()==1){
		var filter_array = [{
			field: 'site.id',
			value: 10,
			operator: '=',
		}];
		
		call_seo=true;
	}
	else if (fullUri.indexOf("/cn/category.php") >= 0){ //if category page
		cat_id = getQueryString('id');
		sub_cat_id=cat_id;//parent category sub_cat_id=cat_id
		 
		var filter_array = [{
			field: 'site.category_id',
			value: cat_id,
			operator: '=',
		}];
		filter_array.push({
			field: 'site.sub_category_id',
			value: sub_cat_id,
			operator: '='
		});
		call_seo=true;
	}
	else if (fullUri.indexOf("/cn/sub-category.php") >= 0){  //if sub category page
		 sub_cat_id = getQueryString('id');
		 var filter_array = [{
			field: 'site.sub_category_id',
			value: sub_cat_id,
			operator: '=',
		}];
		call_seo=true;
	}
	else if (fullUri.indexOf("/cn/article.php") >= 0){ //if article page
		article_id = getQueryString('id');
		var filter_array = [{
			field: 'article.id',
			value: article_id,
			operator: '=',
		}];
		
		call_seo=true;
		api_name='article';
	}
	*/
	/*if (fullUri.indexOf("category-all.html") >= 0){//zonghe page
		var filter_array = [{
			field: 'site.type',
			value: 'zonghe',
			operator: '=',
		}];
		
		call_seo=true;
	}
	if (fullUri.indexOf("category-inner.html") >= 0){//other sport category eg: swimming, badminton, pingpong
		cat_id = getQueryString('id');
		console.log(cat_id)
		
		var filter_array = [{
			field: 'site.category_id',
			value: cat_id,
			operator: '=',
		}];
		
		call_seo=true;
	}
	else if (fullUri.indexOf("video.html") >= 0){//video page
		var filter_array = [{
			field: 'site.type',
			value: 'video',
			operator: '=',
		}];
		
		call_seo=true;
	}
	if (call_seo==true){
	
		$.ajax({
				type:  'GET',
				//url: getBackendHost() + $action,
				url: link + '/api/cn/'+api_name,
				crossDomain: true,
				headers: getHeaders(),
				contentType: false,
				processData: true,
				// contentType: "charset=utf-8",
				//data:  {filter: filter_array},
				data: {filter: filter_array},
				success: function (response, status, xhr) {
					////console.log(response);
    
					obj = response;
					var keyword_arr=[];
	
					if (obj.data!=null){
	
						//document.head.innerHTML += ' <meta name="title" content="">';
						document.head.innerHTML += ' <meta name="description" content="">';
						document.head.innerHTML += ' <meta name="keywords" content="">';
	
						if (api_name=='article'){
							$('title').html(obj.data[0].seo_title);
							$('meta[name=description]').attr("content", obj.data[0].description);
							$('meta[name=keywords]').attr("content", obj.data[0].keywords);
							//$('meta[name=title]').html(obj.data[0].title);
							//$('meta[name=description]').html(obj.data[0].description);
							//$('meta[name=keywords]').html(obj.data[0].keywords);
						}
						else{
							$('title').html(obj.data[0].title);
	
							$('meta[name=description]').attr("content", obj.data[0].description);
							$('meta[name=keywords]').attr("content", obj.data[0].keywords);
	
							//$('meta[name=description]').html(obj.data[0].description);
							//$('meta[name=keywords]').html(obj.data[0].keywords);
						}
					}
	
				},
    
				error: function () {
				   alert('AJAX ERROR - get seo');
				},
			});
	}
	*/
});

//Header, call after every page load
$(function header() {
	
	embed_baidu_head();	
    var fullUri = getCurrentFullUri();
    var cat_id = getQueryString('id');

    //Get Header Body
    var header_body = $.get(mainHost + 'cn/module/header-container.html', function (data) {
        var render = template.compile(data);
        var str = render();
        //alert(str)
		
		$('.header').prepend(str);
    })

    //After get header body, call live carousel & menu
    $.when(header_body).done(function (header_body) {

        pre_header_live_carousel('default');
        menu_left();
        menu_right();
		
		//if (window.localStorage.user_id>0)
			//set_level_bg();

        //call sub menu, if have(article, category, sub-category)
        //if sub category and article, get parent id first, and then only call menu_sub_category

        if (fullUri.indexOf("sub-category") >= 0)
            get_category_parent_id(cat_id, 'category');

        else if (fullUri.indexOf("article") >= 0)
            get_category_parent_id(cat_id, 'article');

        else if (fullUri.indexOf("category") >= 0) //article and category both are burger menu and got sub menu
            menu_sub_category(cat_id);// if category.php, cat_id = parent_id

			
    });
});

function embed_baidu_head(){
	  var header_body = $.get(mainHost + 'cn/module/ga-baidu.html', function (data) {
        var render = template.compile(data);
        var str = render();
        //alert(str)
		
		//console.log(str)
		$('head').append(str);
    })
}

//get category parent id
function get_category_parent_id(id, type) {

    var filter_array = [{
        field: type + '.id',
        value: id,
        operator: '=',
    }];


    //article get caterogy_id
    //sub-category get parent_id
    $.ajax({
        url: link + '/api/cn/' + type,
        data: {
            filter: filter_array,
            label: "get_category_parent_id",
        },
        type: 'get',


        success: function (response, status, xhr) {
            //////console.log(response);
            var obj = response;
            var parent_id = '';
            var sub_cat_display = ''; //use to display as menu header title

            if (type == 'article'){
                parent_id = obj.data[0].category_id;
				sub_cat_display = obj.data[0].category;
			}
            else if (type == 'category') {
                parent_id = obj.data[0].parent_id;
                sub_cat_display = obj.data[0].display;
            }
            menu_sub_category(parent_id, sub_cat_display);

        },
        error: function () {
            alert('AJAX ERROR - get category parent id');
        },
    });
}

function set_level_bg(){
	var level_id=window.localStorage.level_id;
	var level_bg_url='';
	
	if (level_id<=0)
		level_id=1
	
	var filter_array = [{
        field: 'level.id',
        value:  level_id,
        operator: '=',
    }];
	
	$.ajax({
        url: link + '/api/cn/level',
        data: {
            filter: filter_array
           
        },
        type: 'get',


        success: function (response, status, xhr) {
            //console.log(response);
            var obj = response;
           
			if (obj.data[0]){
				level_bg_url='/'+obj.data[0].upload_url;
				//$('.profile_bg').find('img').attr('src',level_bg_url);
				$('#user_profile_bg').find('img').attr('src',level_bg_url);
				
			}
			
			
			////console.log($('.profile_bg').find('img'))

        },
        error: function () {
            alert('AJAX ERROR - get category parent id');
        },
    });
}

/*window.setInterval(function(){
	  /// call your function here
	  alert('osh');
}, 3000);*/

//header carousel, get season id first
function pre_header_live_carousel(default_chosen){
	
	var league_id='';
	
	var filter_array=[
                {
                    field: 'current',
                    value: 1,
                    operator: '=' 
                }
            ]
	if (default_chosen=='default'){
		league_id=1; //default NBA //1878
		
		filter_array.push({
                    field: 'league_id ',
                    value: league_id,
                    operator: '='
                })
	}
	
	else{
		var league_id=$('#banner_dropdown').val();
		
		if (league_id=='basketball'){
				filter_array.push({
                    field: 'league_id ',
                    value:basketball_arr,
                    operator: 'IN'
                })
		}
		else if (league_id=='football'){
				filter_array.push({
                    field: 'league_id ',
                    value: football_arr,
                    operator: 'IN'
                })
		}
		else if (league_id=='popular'){
				filter_array.push({
                    field: 'league_id ',
                    value:popular_arr,
                    operator: 'IN'
                })
		}
		else{
				filter_array.push({
                    field: 'league_id ',
                    value: league_id,
                    operator: '='
                })
		}
	}
	
	
			
	$.ajax({
        url: link + '/api/cn/season_list',
        //data: {search: {parent_id: parent_id}},
        data: {
            filter: filter_array,
			
			/*sort:{
					field: "category.sorting",
					sort: "asc"
				}*/
        },
        type: 'get',


        success: function (response, status, xhr) {
            //console.log(response.data);

            var obj = response;
			var season_id_arr=[];
			$.each(obj.data, function (index, key) {
				season_id_arr.push(obj.data[index]['season_id'])
			});
			
			header_live_carousel(season_id_arr,default_chosen);
			//console.log(season_id_arr)
			//console.log(default_chosen)

        },
        error: function () {
            console.log('AJAX ERROR - get_season_id');
        },
    });
}

//header carousel, main
function header_live_carousel(season_id_arr,default_chosen) {
	//alert(default_chosen);
    //default_chosen='basketball';
	
	var league='';
	var selected='';
	var season_id='';
	//console.log(selected)
	
	if (default_chosen=='default'){
		selected=1;//default NBA
		league='NBA';
	}
	else{
		selected=$('#banner_dropdown').val();
		//league=$('#banner_dropdown').html();
		league=$("option:selected", '#banner_dropdown').text();//choose selected's text
	}
	
	if(season_id_arr.length==1){
		
		var filter_array = [{
			field: 'season_id',
			value: season_id_arr[0],
			operator: '=',
		}];
	}
	else if(season_id_arr.length>1){
		var filter_array = [{
			field: 'season_id',
			value: season_id_arr,
			operator: 'IN',
		}];
	}
	
		//console.log(selected)
	
	
	//if select certain league
	if (selected!='popular' && selected!='football' && selected!='basketball'){
		selected=parseInt(selected);
		
		if (basketball_arr.includes(selected)){
			filter_array.push({
				field: 'status',
				value: [10, 12, 14],
				operator: 'NOT IN'
			});
		}
		else if (football_arr.includes(selected)){
			filter_array.push({
				field: 'status',
				value: [8, 11, 12],
				operator: 'NOT IN'
			});
		}
	}
	else if (selected=='basketball'){ //if select basketball
		filter_array.push({
				field: 'status',
				value: [10, 12, 14],
				operator: 'NOT IN'
			});
	}
	else if (selected=='football'){ //if select football
		filter_array.push({
				field: 'status',
				value:[8, 11, 12],
				operator: 'NOT IN'
			});
	}
	else if (selected=='popular'){ //if select football
		filter_array.push({
				field: 'status',
				value:[8, 11, 12,10, 12, 14],
				operator: 'NOT IN'
			});
	}
	
	
	
	//console.log(filter_array)
			
	$.ajax({
        url: link + '/api/cn/season_matches',
        //data: {search: {parent_id: parent_id}},
        data: {
            filter: filter_array,
			
			/*sort:{
					field: "category.sorting",
					sort: "asc"
				}*/
        },
        type: 'get',


        success: function (response, status, xhr) {
            //console.log(response.data);

            var obj = response;
			//console.log(obj.data)
			
			var data_arr=[];
			var sub=[];
			var temp=[];
			var count=0;
			$.each(obj.data, function (index, key) {
				 
				if (index>0 && index%3==0){//add new entry into array
					
					
					data_arr[count]=sub;
					sub=[]; 
					count++;
				}
				else{
					//
				}
				
				temp['id'] = obj.data[index].id;
				temp['home_team_name'] = obj.data[index].home_team_name;
				temp['home_score'] = obj.data[index].home_score;
				temp['away_team_name'] = obj.data[index].away_team_name;
				temp['away_score'] = obj.data[index].away_score;
				
				sub.push(temp);
				//console.log(temp);
				temp=[];
			});
			
			//leftover push to array too
			if (sub.length>0)
				data_arr[count]=sub;
			
			//console.log(data_arr)
			
			var obj = {
				league:league,
				data_arr: data_arr,
			};
			//console.log(obj)
			
			window.localStorage.banner_curr_league=selected;//for highlight dropdown list purpose, will remove once selected
			var html = $.get(mainHost + 'cn/module/header-live-carousel.html', function (data) {
				var render = template.compile(data);
				var str = render(obj);

				$('#header_live_carousel').html(str);
				return str;
				
				
				
			})
        },
        error: function () {
            console.log('AJAX ERROR - get_season_id');
        },
    });
	
	

}

function menu_left() {
    var fullUri = getCurrentFullUri();
    var obj = {};

    var filter_array = [{
        field: 'disabled',
        value: 0,
        operator: '=',


    }];
    filter_array.push({
        field: 'category.type',
        value: 'sport',
        operator: '='
    });

    filter_array.push({
        field: 'category.parent_id',
        value: '0',
        operator: '='
    });

    var menu = {
        0: {display: '赛事预测', url: '/cn/match-prediction', name: 'match_prediction', target: ''}
    };

    var menu_behind = {
        0: {display: '综合', url: '/cn/category-all.php', name: 'category-all', target: ''},
        1: {display: '视频', url: '/cn/video.php', name: 'video', target: ''},
        //2: {display: '资料库', url: '', name: 'ziliaoku', target: ''},

    };

    $.ajax({
        url: link + '/api/cn/category',
        //data: {search: {disabled: 0,type:'sport'}},
        data: {filter: filter_array},
        type: 'get',


        success: function (response, status, xhr) {
            ////console.log(response);
            var obj = response;
            var menu_index = 1;
            //menu_json=obj.data

            $.each(obj.data, function (index, key) {
                if (obj.data[index].display == '足球' || obj.data[index].display == '篮球' || obj.data[index].display == '电竞' || obj.data[index].display == '台球') {

                    if (obj.data[index].url.length <= 0) { //if no url set, hardcode url
                        obj.data[index].url = '/cn/category.php?id=' + obj.data[index].id;
                    }
                    //obj.data[index].url='test.html?id='+obj.data[index].id;


                    //check url inner or outer
                    if (obj.data[index].url.indexOf('.com') >= 0)
                        obj.data[index].target = '_blank';
                    else
                        obj.data[index].target = '';
					
					//to replace empty space to _, for menu highlight purpose
					if (obj.data[index].name!=null){
						var temp_name = obj.data[index].name;
						var replaced_name=temp_name.replace(/ /g, '_');
						////console.log(replaced_name)
						
						obj.data[index].name = replaced_name;
					}

                    //push to menu
                    menu[menu_index] = obj.data[index];

                }
                menu_index++;
            });

            $.each(menu_behind, function (index, key) {
                menu[menu_index] = menu_behind[index];
                menu_index++;
            });

           // //console.log(menu);
            obj = {
                menu: menu
            };


            //DECIDE WHICH MENU TO CALL
            var menu_type = 'menu-default.html';
            var menu_btn_css = 'none';

            //var burger_menu_arr=['sub_category','match-prediction','article','category_other'];
            //if (burger_menu_arr.includes("Mango");)
            if (fullUri.indexOf("sub-category.php") >= 0 || fullUri.indexOf("sub-category-inner.php") >= 0 || fullUri.indexOf("match-prediction") >= 0 || fullUri.indexOf("article") >= 0 || fullUri.indexOf("category_other") >= 0) {
                menu_type = 'menu-burger.html';
                menu_btn_css = 'block';

            }


            var html = $.get(mainHost + 'cn/module/' + menu_type, function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#menu_left').html(str);
                $('.menu_btn').css('display', menu_btn_css);

                //////console.log(str);

                //menu_right();

                //put here instead of function header coz of ajax asyncronous issue
                if (fullUri.indexOf("match-prediction") >= 0)//match prediction use burger menu, but no sub menu
                    show_menu_sub_header_title('赛事预测');//straight update the menu header title
					
				//HIGHLIGHT MAIN MENU, put here instead of menu-default.html coz of ajax asyncronous issue
				if (menu_type=='menu-default.html'){
					highlight_current_menu(menu);////console.log('dd')
				}
            });
        },
        error: function () {
            alert('AJAX ERROR - get menu');
        },
    });
}

function menu_right() {

    user_id = window.localStorage.user_id;

    if (user_id > 0) {

        $('#before_login_div').css('display', 'none');
        $.ajax({
            url: getBackendHost()+ '/api/cn/user',
            data: {
                search: {id: user_id}

            },
            type: 'get',

            success: function (response, status, xhr) {
               // //console.log(response);
                var obj = response;
				
				if(response.code == 1){
					var user_data = obj.data[0];
					var user_lvl_icon='';
					////console.log(user_data);

					user_lvl_icon='/assets/images/user_level_icon/lvl'+user_data.level_id+'_white.png';
					////console.log (user_lvl_icon)

					user_data.level_icon_white=user_lvl_icon;

					obj = {
						user_data: user_data
					};

					var html = $.get(mainHost + 'cn/profile/profile-summary.html', function (data) {
						var render = template.compile(data);
						var str = render(obj);

						$('#profile-summary').html(str);
						$('#username').html(user_data.username);
						//embed_login_popup();
						//alert(str);
						//menu_right();

					});
				}

            },
            error: function () {
                alert('AJAX ERROR - get menu right');
            },
        });
    } else {
        $('#username').css('display', 'none');
        $('#logout_div').css('display', 'none');
        $('.profile_btn').css('display', 'none');

		 embed_login_popup();
       
    }
}

//to show sub category menu, sub_cat_display=sub category name selected, only use in sub-category.php
function menu_sub_category(parent_id = 1, sub_cat_display = '') {

    var fullUri = getCurrentFullUri();
    if (fullUri.indexOf("category-all") >= 0 || (fullUri.indexOf("category-inner") >= 0 && !(fullUri.indexOf("sub-category-inner") >= 0))){
		parent_id=0;	
		var category_arr=[1,2,3,4];
	}
	
    var filter_array = [{
        field: 'parent_id',
        value: parent_id,
        operator: '=',


    }];
    filter_array.push({
        field: 'category.type',
        value: 'sport',
        operator: '='
    });
	
	 if (fullUri.indexOf("category-all") >= 0 || (fullUri.indexOf("category-inner") >= 0 && !(fullUri.indexOf("sub-category-inner") >= 0))){
		filter_array.push({
			field: 'category.id',
			value: category_arr,
			operator: 'NOT IN'
		});		
		 
	}
	//console.log(filter_array)
	
    $.ajax({
        url: link + '/api/cn/category',
        //data: {search: {parent_id: parent_id}},
        data: {
            filter: filter_array,
            label: "menu_sub_category",
			
			sort:{
					field: "category.sorting",
					sort: "asc"
				}
        },
        type: 'get',


        success: function (response, status, xhr) {
            console.log(response.data);

            var obj = response;

            $.each(obj.data, function (index, key) {

                if (obj.data[index].url.length <= 0) { // if no url
                    if (fullUri.indexOf("category-all") >= 0 || (fullUri.indexOf("category-inner") >= 0 && !(fullUri.indexOf("sub-category-inner") >= 0))){
						obj.data[index].url = '/cn/category-inner.php?id=' + obj.data[index].id;
					}
					else if (fullUri.indexOf("sub-category-inner.php") >= 0)
						obj.data[index].url = '/cn/sub-category.php?id=' + obj.data[index].id;
					else{
						if (obj.data[index].parent_id <= 0)//if record parent id=0
							obj.data[index].url = '/cn/category.php?id=' + obj.data[index].id;
						else
							obj.data[index].url = '/cn/sub-category.php?id=' + obj.data[index].id;
					}
                }

				
				
                //check url inner or outer
                if (obj.data[index].url.indexOf('.com') >= 0)
                    obj.data[index].target = '_blank';
                else
                    obj.data[index].target = '';
            });

            //GENERATE BURGER MENU TITLE START
			if (response.data[0]){ //take sub-menu firts record data to show burger menu title
				
				burger_menu_title_parent_id = response.data[0].parent_id;
				var burger_title_url='/cn/category.php?id='+burger_menu_title_parent_id;
				
				var burger_menu_title = '<a href='+burger_title_url+'>'+ response.data[0].parent_category+ '</a>';
				
				if ((fullUri.indexOf("sub-category.php") >= 0 || fullUri.indexOf("sub-category-inner.php") >= 0) && sub_cat_display.length > 0)
					burger_menu_title = '<a href='+burger_title_url+'>'+ burger_menu_title + '</a>' +' <i class="fas fa-chevron-right"></i> ' + sub_cat_display;
            }
			else{ //if no sub menu record(pingpong, swimming, badmintn, others)
				var burger_title_url='/cn/category-all.php?id='+parent_id;
				burger_menu_title='<a href='+burger_title_url+'>'+ sub_cat_display+ '</a>';
				
			}
			//GENERATE BURGER MENU TITLE END


            ////console.log(obj.data);
            obj = {
                sub_menu: obj.data
            };

            var html = $.get(mainHost + 'cn/module/menu-sub.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('.category_menu_outside ').html(str);
                show_menu_sub_header_title(burger_menu_title);
				sub_header_title=burger_menu_title;//use to call show_menu_sub_header_title again when menu_left and menu_right done
				
				//HIGHLIGHT SUB MENU, put here instead of menu-sub.html coz of ajax asyncronous issue
				highlight_current_sub_menu();
                //alert(str);
                //menu_right();
            });
        },
        error: function () {
            alert('AJAX ERROR - get sub menu');
        },
    });
}

//show header title for burger menu
function show_menu_sub_header_title(display) {
    var fullUri = getCurrentFullUri();

    if (fullUri.indexOf("category_other") >= 0){//category_other not in use now
        burgermenu_header_title='其他类别';
		$('#burgermenu_header_title').html('其他类别');
	}
    else {
        burgermenu_header_title=display;
		$('#burgermenu_header_title').html(display);
    }
	
	////console.log(burgermenu_header_title)
	$('#burgermenu_header_title').html(burgermenu_header_title);
	
}

/*
function wait(ms){
   var start = new Date().getTime();
   var end = start;
   while(end < start + ms) {
     end = new Date().getTime();
  }
  
}*/

//to embed login popup box to every page, call from menu_right
function embed_login_popup() {
    var html = $.get(mainHost + 'cn/module/login-popup.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#login_popup').html(str);
        refreshCaptcha('login');//default is login
		embed_forget_pw(); //embed forget password
		embed_forget_acc(); //embed forget account
        //alert(str);
        //menu_right();

    });
}

//to embed forget password modal
function embed_forget_pw() {
    var html = $.get(mainHost + 'cn/module/forget-password.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);
		
        $('#forgotpw').html(str);
		refreshCaptcha('forgetpw');
    });
}

//to embed forget account modal
function embed_forget_acc() {
    var html = $.get(mainHost + 'cn/module/forget-account.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#forgotac').html(str);
		refreshCaptcha('forgetac');
    });
}

function refreshCaptcha(type) {
    var new_captcha = getBackendHost() + '/api/auth/get-captcha';

    if (type == 'login')
        $('#login-img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
    else if (type == 'signup')
        $('#signup-img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
	else if (type == 'forgetpw')
        $('#forgetpw-img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
	else if (type == 'forgetac')
        $('#forgetac-img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');

}

//login function
function login($form) {

    ////console.log($form.formSerialize());
    $.ajax({
        type: 'POST',
        url: getBackendHost() + '/api/auth/login',
        data: $form.formSerialize(),
        crossDomain: true,
        xhrFields: {
			withCredentials: true
        },
        success: function (response, status, xhr) {
            obj = response;

            if (obj.code == 1) {
                userdata = response.data.user;
                window.localStorage.access_token = userdata.token;
                window.localStorage.user_id = userdata.id;
                window.localStorage.username = userdata.username;
                window.localStorage.display_name = userdata.alias;
                window.localStorage.role_id = userdata.role_id;
                window.localStorage.level_id = userdata.level_id;
				
				var profile_thumbnail='';
				
				if (userdata.upload_id<=0)//if no upload profile pic, use default one
					profile_thumbnail='/assets/images/default_user_image.png';
				else
					profile_thumbnail='/'+userdata.upload_url;

				window.localStorage.profile_thumbnail = profile_thumbnail;
				
				redirect_to('/cn');
				
				//set_thumbnail();
                //if (obj.redirect) redirect_to('/cn');
            } else {

                alert(obj.message);
                $('#img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
            }
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function set_thumbnail(){
	$.ajax({
        type: 'GET',
        url: link + '/api/cn/user',
       
		data:  {id:  window.localStorage.user_id},
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",
      
		success: function (response, status, xhr) {
			////console.log(response);
			var obj = response;
			var user_data=obj.data;
			
			if (user_data.upload_id<=0)//if no upload profile pic, use default one
				profile_thumbnail='/assets/images/default_user_image.png';
			else
				profile_thumbnail='/'+user_data.upload_url;
			
			window.localStorage.profile_thumbnail = profile_thumbnail;

        },
		error: function () {
            alert('AJAX ERROR - get profile body');
        },
    });
}

function signup(formID) {

    var signup_form = $('#' + formID);
    var method = signup_form.attr('method');
    var action = signup_form.attr('action');

    //////console.log(signup_form.attr('method'));
    //////console.log(signup_form.attr('action'));
    //////console.log(signup_form.formSerialize());

    pw = $('#signup_password').val();
    confirm_pw = $('#signup_confirm_password').val();

    if (pw != confirm_pw) {
        alert('密码不一致');

    } else {
        $.ajax({
            type: method,
            url: getBackendHost() + action,
            data: signup_form.formSerialize(),
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
            success: function (response, status, xhr) {
                //////console.log(response);

                obj = response;

                if (obj.code == 1) {
                    //login(signup_form);
                    login(signup_form);//if succesfully signup, straoght login

                } else {

                    alert(obj.message);
                    $('#img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
                }
            },
            error: function () {
                showAlert("Problem occurred while sending request.", "danger", $error_selector);
            },
        });
    }
}

function forget_pw(){
	var json_form_obj = new Object();
    var name;
	////console.log($action)
	//TO GET NAME AND VALUE FROM FORM AND STRINGIFY
	$('#reset_pw_user_id').val(window.localStorage.user_id);
	
    $('#forgetpw_form').find(':input').each(function (key, value) {
        name = $(this).attr("name");
       
	    if (name!='undefine' && name!=undefined)
			json_form_obj[name] = $(this).val();
		
		////console.log(name,$(this).val())
    });
	
	json_form_obj['captcha'] = $('#forgetpw_captcha').val();
	json_form_obj['type'] = 'password';
	
	//json_form_obj['phone']=888;
	var formData = JSON.stringify(json_form_obj);
	////console.log(formData)
	
	$.ajax({
            type: 'POST',
            url: getBackendHost() + '/api/cn/forget_password',
            data: formData,
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
            success: function (response, status, xhr) {
                //console.log(response);

                obj = response;
					
                if (obj.code == 1) {
                    //login(signup_form);
                    //login(signup_form);//if succesfully signup, straoght login
					
					$('#verification_code').css('display', 'block');
					$('#new_password').css('display', 'block');
					$('#confirm_new_password').css('display', 'block');
					$('#verification_button').css('display', 'block');
					$('#msg_sent_span').css('display', 'block');
					$('#sms_button').css('display', 'none');
					
					$('#forgetpw_user_id').val(obj.user_id);

					
					/*$('#forgotpw').modal('hide');
					$('.modal-backdrop').remove();
					$('#login_popup').modal('toggle');*/
                } else {

                    alert(obj.message);
                    $('#img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
                }
            },
            error: function () {
                showAlert("Problem occurred while sending request.", "danger", $error_selector);
            },
        });

	
}

function reset_pw(){
	var json_form_obj = new Object();
	var verification_code= $('#verification_code').val();
	var new_pw=$('#new_password').val();
	var confirm_new_pw=$('#confirm_new_password').val();
	
	if (verification_code.length==0)
		alert('请输入验证码');
	else if (new_pw!=confirm_new_pw)
		alert('密码不一致');
	else{
		
		$('#forgetpw_form').find(':input').each(function (key, value) {
			name = $(this).attr("name");
		   
			if (name=='user_id')
				json_form_obj[name] = $(this).val();
			
			////console.log(name,$(this).val())
		});
		
		json_form_obj['verification_code'] = verification_code;
		json_form_obj['new_password'] = new_pw;
		var formData = JSON.stringify(json_form_obj);
		////console.log(formData)
	
		$.ajax({
				type: 'POST',
				url: getBackendHost() + '/api/cn/reset_password',
				data: formData,
				success: function (response, status, xhr) {
					////console.log(response);

					obj = response;
						
					if (obj.code == 1) {
						alert('密码重置成功，请重新登入');
						window.location.href = "/cn";
					} else {

						alert(obj.message);
						$('#img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
					}
				},
				error: function () {
					showAlert("Problem occurred while sending request.", "danger", $error_selector);
				},
			});
	}
}

function forget_acc(){
	
	//cincai ajax call see pass parameter same with forgetpw or not, then show msg and redirect to login modal
	var json_form_obj = new Object();
    var name;
	////console.log($action)
	//TO GET NAME AND VALUE FROM FORM AND STRINGIFY
	
	
    $('#forgetac_form').find(':input').each(function (key, value) {
        name = $(this).attr("name");
       
	    if (name!='undefine' && name!=undefined)
			json_form_obj[name] = $(this).val();
		
		////console.log(name,$(this).val())
    });
	
	json_form_obj['captcha'] = $('#forgetac_captcha').val();
	json_form_obj['type'] = 'account';
	
	//json_form_obj['phone']=888;
	var formData = JSON.stringify(json_form_obj);
	////console.log(formData)
	
	$.ajax({
            type: 'POST',
            url: getBackendHost() + '/api/cn/forget_password',
            data: formData,
			crossDomain: true,
			xhrFields: {
				withCredentials: true
			},
            success: function (response, status, xhr) {
                //console.log(response);

                obj = response;
					
                if (obj.code == 1) {
                    alert('用戶名已发送到您手机，请重新登入。');
					//redirect_to('/cn/index.php?forgetac=1');
					$('#forgetac_msg_sent_span').css('display', 'block');
					$('#forgetac_close_button').css('display', 'block');
					$('#forgetac_sms_button').html('再度送出短信');
                } else {

                    alert(obj.message);
                    $('#img-captcha').attr('src', getBackendHost() + '/api/auth/get-captcha');
                }
            },
            error: function () {
                showAlert("Problem occurred while sending request.", "danger", $error_selector);
            },
        });
}

function logout() {
    confirm('确定登出？').then(result => {
        const confirmed = result.confirmed
        if(!confirmed){
                return;
        }
        else {
            window.localStorage.removeItem('access_token');
            window.localStorage.removeItem('user_id');
            window.localStorage.removeItem('username');
            window.localStorage.removeItem('display_name');
            window.localStorage.removeItem('role_id');
            window.localStorage.removeItem('level_id');
            window.localStorage.removeItem('profile_thumbnail');
            window.location.href = "/cn";
        }
    });
}

//use in match_prediction_carousel()
function isEven(value) {
    if (value % 2 == 0)
        return true;
    else
        return false;
}


function sendMail() {
    var link = "mailto:looping.tai@gmail.com"
             + "?cc=myCCaddress@example.com"
             + "&subject=" + escape("This is my subject")
            // + "&body=" + escape(document.getElementById('myText').value)
             + "&body=123123" 
    ;

    window.location.href = link;
}

//Footer
$(function footer() {//sendMail() 

    var footerList = {
        0: {display: '关于我们', url: '/cn/about_us.html'},
        1: {display: '免责申明', url: '/cn/disclaimer.html'},
        2: {display: '常见问题', url: '/cn/QA.html'},
        3: {display: '意见反馈', url: '/cn/feedback.html'},
    };
    obj = {
        footerList: footerList
    };

    var html = $.get(mainHost + 'cn/module/footer.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);
        //alert(str)
        $('.footer').html(str);
    })
});


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
//--feedback form ends

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


function dim_bg(){	 
	/*$('#alertFram').modal({
		backdrop: true
	})*/
	
	$('body').append('<div class="modal-backdrop fade show"></div>')
}

function light_bg(){	 
	$('.modal-backdrop').remove();
}

$(document).on('click','body .modal-backdrop',function(){
	if ($('#alertFram'))
		$('#alertFram').remove();
	
	if ($('#confirmFram'))
		$('#confirmFram').remove();
	$('#shield').remove();
	
	$('.modal-backdrop').remove();
	
	window.onscroll=function(){window.scrollTo();}
});

//===============OLD FUNCTION BELOW======================


function oldheader() {
    var mainMenuList = ['首页'];
    var sportCategories = [];

    var mainList = $.ajax({
        url: link + 'api/cn/get-sport-categories',
        type: 'get',
        success: function (data) {
            $.each(data.categories, function (index, key) {
                if (key.id != 5)//exclude 新浪
                    mainMenuList.push(data['categories'][index]['title']);
                sportCategories.push(data['categories'][index]['title']);
            });
            mainMenuList.push('热门新闻', '即时比分');

            var obj = {
                mainMenuList: mainMenuList,
                sportCategories: sportCategories

            };
        }
    });

    var scheduleList = $.ajax({
        url: link + 'api/cn/get-season',
        type: 'get',
        success: function (data) {
            var obj = {
                scheduleList: data['seasons']
            };
        }

    });

    $.when(mainList, scheduleList).done(function (mainList, scheduleList) {
        obj = [];
        obj['mainMenuList'] = mainMenuList;
        obj['scheduleList'] = scheduleList[0]['seasons'];
        obj['sportCategories'] = sportCategories;

        var html = $.get('/cn/header.html', function (data) {
            var render = template.compile(data);
            var strSub = render(obj);
            $('#header').html(strSub);

            if ($.trim($('#header_ul').html()) == '') { //if header not load
                header();
            }

        });//////console.log(obj);
        //$('#sports_detail').append('123456');
    });
}

//Banner
function banner() {
    var getUrl = window.location;
    var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[0];

    var str = window.location.search;
    var id = str.replace("?id=", "");

    if (id == "1") {
        banner_id = 1;
    } else if (id == "2") {
        banner_id = 2;
    } else if (id == "3") {
        banner_id = 3;
    } else if (id == "4") {
        banner_id = 4;
    }

    if (pathname == "/cn/") {
        banner_id = 0;
    } else if (pathname == "/cn/news/") {
        banner_id = 5;
    }
//var image_route="assets/images";

//alert(baseUrl);
    var banner_image = [];
    var temp_normal_url;
    var temp_mobile_url;
    var obj = {};
    var banner_json = [];

    $.ajax({
        url: link + '/api/cn/get-banner-list',
        type: 'get',
        data: {
            id: banner_id
        },
        success: function (data) {
            $.each(data.banners, function (index, key) {
                banner_image.push(key.image);
            });

            $.each(banner_image, function (index, key) {
                temp_normal_url = baseUrl + key.normal;
                temp_mobile_url = baseUrl + key.mobile;

                data.banners[index].image.normal = temp_normal_url;
                data.banners[index].image.mobile = temp_mobile_url;
            });

            banner_json = data.banners;

            obj = {
                banner_json: banner_json
            };

            var html = $.get(mainHost + 'cn/banner.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#banner').html(str);
            });
        }
    });
}

//Homepage schedule_tab
function schedule_tab() {
    var obj = {};
    var html = $.get(mainHost + 'cn/schedule_tab.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);
        $('#schedule_tab').html(str);
    })
}

//Homepage Sports Content- 新闻精选
function home_sports() {
    $.ajax({
        url: link + 'api/cn/get-sport-list',
        type: 'get',
        data: {
            limit: 12
        },
        success: function (data) {
            var obj = {
                items: data['sports']
            };

            var html = $.get(mainHost + 'cn/sports_list.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#sports_list').html(str);
            });
        }
    });
}

//Homepage for News content - 最新推荐 and livescore
function home_news() {
    var get_shooter = $.ajax({
        url: link + '/api/cn/get-shooter',
        type: 'get',
        data: {
            limit: 10,
        },
        success: function (data) {
            var obj = {
                shooters: data['shooters']
            };
            return obj;
        }
    })

    var get_ranking = $.ajax({
        url: link + '/api/cn/get-ranking',
        type: 'get',
        data: {
            season_id: 82,
            limit: 10,
        },
        success: function (data) {

            var obj = {
                rankings: data['rankings']
            };
            return obj;
        }
    })
    var get_news_list = $.ajax({
        url: link + 'api/cn/get-news-list',
        type: 'get',
        data: {
            limit: 6
        },
        success: function (data) {

            var obj = {
                news: data['news']
            };
            return obj;
        }
    })
    var get_schedule = $.ajax({
        url: link + 'api/cn/get-season',
        type: 'get',

        success: function (data) {

            var obj = {
                schedule: data['seasons']['season']
            };
            return obj;
        }
    })

    $.when(get_shooter, get_ranking, get_news_list, get_schedule).done(function (get_shooter, get_ranking, get_news_list, get_schedule) {
        obj = [];
        obj['shooters'] = get_shooter[0]['shooters'];
        obj['rankings'] = get_ranking[0]['rankings'][0];
        obj['news'] = get_news_list[0]['news'];
        obj['schedules'] = get_schedule[0]['seasons']['season'];
        var html = $.get(mainHost + 'cn/news_list.html', function (data) {
            var render = template.compile(data);
            var strSub = render(obj);
            $('#news_list').html(strSub);
        });
    });

}


//News Tab Content 热门新闻
function news() {
    $.ajax({
        url: link + 'api/cn/get-news-list',
        type: 'get',
        success: function (data) {

            var obj = {
                items: data['news']
            };

            var html = $.get('news_content.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#news_content').html(str);
            });
        }
    });
}

//Sports Tab Content- 新闻精选
function sports() {
    var variable = "id"
    var sport_id = getQueryString(variable)

    var get_sport = $.ajax({
        url: link + 'api/cn/get-sport-list?limit=31&id=' + sport_id,
        type: 'get',
        success: function (data) {
            var obj = {
                sports: data['sports']
            };
            return obj;
        }
    })
    var banner_image = [];
    var get_news = $.ajax({
        url: link + 'api/cn/get-news-list?limit=4',
        type: 'get',

        success: function (data) {
            // $.each(data.news, function(index, key) {
            //     var tempurl=baseUrl+key.image;
            //     banner_image.push(tempurl);
            //     data.news[index].image=tempurl;
            // });

            var obj = {
                news: data['news']
            };
            return obj;
        }
    })

    $.when(get_sport, get_news).done(function (get_sport, get_news) {
        obj = [];
        obj['news'] = get_news[0]['news'];
        obj['sports'] = get_sport[0]['sports'];
        var html = $.get('content.html', function (data) {
            var render = template.compile(data);
            var strSub = render(obj);
            $('#content').html(strSub);
        });
    });
}

//Sports Detail- News Content
function sports_detail() {
    var variable = "id";
    var id = getQueryString(variable);

    var sport_detail = $.ajax({
        url: link + 'api/cn/get-sport-detail',
        type: 'get',
        data: {
            id: id
        },
        success: function (data) {

            var temp = data['sport']['content'];
            var obj = {

                sport: data['sport']
            };
        }
    });

    var other_link = $.ajax({
        url: link + 'api/cn/get-sport-list',
        type: 'get',
        data: {
            limit: 4
        },
        success: function (data) {
            var obj = {
                other_link: data['sports']
            };
        }
    });


    $.when(sport_detail, other_link).done(function (sport_detail, other_link) {
        // Handle both XHR objects

        obj = [];
        obj['sport'] = sport_detail[0]['sport'];
        obj['other_link'] = other_link[0]['sports'];

        var html = $.get('sports_detail.html', function (data) {
            var render = template.compile(data);
            var strSub = render(obj);
            $('#sports_detail').html(strSub);

        });
        //$('#sports_detail').append('123456');
    });
}

//News Detail - News Content
function news_detail() {
    var variable = "id";
    var id = getQueryString(variable);
    var news_detail = $.ajax({
        url: link + 'api/cn/get-news-detail',
        type: 'get',
        data: {
            id: id
        },
        success: function (data) {
            ////console.log(data);
            var obj = {
                news: data['news']
            };
        }
    });


    var other_link = $.ajax({
        url: link + 'api/cn/get-sport-list',
        type: 'get',
        data: {
            limit: 4
        },
        success: function (data) {
            var obj = {
                other_link: data['sports']
            };
        }
    });

    $.when(news_detail, other_link).done(function (news_detail, other_link) {
        // Handle both XHR objects

        obj = [];
        obj['news'] = news_detail[0]['news'];
        obj['other_link'] = other_link[0]['sports'];
        //////console.log(obj);
        var html = $.get('news_detail.html', function (data) {
            var render = template.compile(data);
            var strSub = render(obj);
            $('#news_detail').html(strSub);

        });
        //$('#sports_detail').append('123456');
    });
}

//Hot News
function hot_news() {
    $.ajax({
        url: link + 'api/cn/get-news-list',
        type: 'get',
        data: {
            limit: 4
        },
        success: function (data) {
            var obj = {
                news: data['news']
            };
            //////console.log(obj);
            var html = $.get('/cn/hot_news.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#hot_news').html(str);
            });
        }
    });
}


//LIVESCORE, RANKING, SHOOTER
//===Header===
function scheduleSharedHeader(season_title, season_id, from) {
    $.get('/cn/schedule_shared_header.html', function (data) {
        var obj = {
            tabList: ['赛程', '积分榜', '射手榜'],
            tabUrlList: [mainHost + 'cn/schedule', mainHost + 'cn/ranking', mainHost + 'cn/shooter'],
            season_title: season_title,
            season_id: season_id,
            from: from

        };
        var render = template.compile(data);
        var str = render(obj);

        $("#schedule_shared_header").html(str);
        // ////console.log(str);
    })
}

//===Schedule Carousel===
function scheduleCarousel(season_id) {
    var current_tab;
    var temp;

    $.ajax({
        url: link + 'api/cn/get-season-options',
        type: 'get',
        data: {
            season_id: season_id
        },
        success: function (data) {

            //GET CURRENT ROUND/DATE TO SET CLASS ACTIVE
            if (getCurrentFullUri().indexOf("search") >= 0) { //if user clicked on certain round/date
                current_tab = getQueryString('search');
            } else { //else get last round/date as default

                var json_length = Object.keys(data['season_options']['options']).length; //get json total length

                if (data['season_options']['indicator'] == '日期') {
                    temp = data['season_options']['options'][json_length - 1];//get last element value
                    current_tab = temp['month'];
                } else if (data['season_options']['indicator'] == '轮次') {
                    temp = data['season_options']['options'][json_length - 1];//get last element value
                    current_tab = temp['round'];
                }
                //////console.log(current_tab);
            }

            var obj = {
                season: data['season_options'],
                season_id: season_id,
                current_tab: current_tab
            };

            //////console.log(obj);
            var html = $.get('schedule_carousel.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);
                $('#schedule_carousel').html(str);

            })


        }
    });
}

//===Breadscrumbs===
function scheduleSharedBreadcrumbs(season_title, tabValue) {
    $.get('/cn/schedule_breadcrumbs.html', function (data) {
        var obj = {
            season_title: season_title,
            tabValue: tabValue

        };
        var render = template.compile(data);
        var str = render(obj);

        $("#breadcrumbs").html(str);
        //////console.log(obj);
    })
}

//===Table Schedule===
function scheduleTableSchedule(season_id, season_title) {
    var search_keyword = '';
    var last_index;
    var indicator = getSeasonDetail(season_id, 'indicator');//determine month or round

    //TO SET INDICATOR TO DETERMINE ROUND OR MONTH
    if (indicator == '日期')
        indicator = 'month';
    else if (indicator == '轮次')
        indicator = 'round';


    //DETERMINED ROUND/DATE TO PASS TO API
    if (getCurrentFullUri().indexOf("search") >= 0) //IF THERE IS SEARCH KEYWORD
        search_keyword = getQueryString('search');

    else { //ELSE, SET TO DEFAULT

        //get last index of options
        last_index = getSeasonOptionLength(season_id, 'get-season-options', 'season_options', 'options') - 1;

        //user last_index to get latest month and set it as search_keyword
        search_keyword = getSeasonDetail(season_id, 'options', '', last_index, indicator);
    }


    var dataObj = {
        'season_id': season_id,
    };
    dataObj[indicator] = search_keyword;
    // ////console.log(dataObj);

    $.ajax({
        url: link + 'api/cn/get-schedule',
        type: 'get',
        data: dataObj,
        // data: {
        //     season_id:season_id,
        //     round:search_keyword,
        //     month:search_keyword
        // },

        success: function (data) { //////console.log(data);
            var obj = {
                tableheader: ['时间', '轮次', '状态', '主队', '比分', '客队'],
                tableheaderclass: ['competing-time', 'rounds', 'status', 'team home-team', 'score', 'team away-team'],
                season_title: season_title,
                schedule_detail: data['schedule'],
                is_round: data['is_round']
            };
            // ////console.log(obj);
            var html = $.get('table_schedule.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);
                $("#table_schedule").html(str);
            });

        }
    });
}

//===Ranking Table===
function rankingContent(season_id, season_title) {
    var content_link;
    $.ajax({
        url: link + '/api/cn/get-ranking',
        type: 'get',
        data: {
            season_id: season_id,
        },
        success: function (data) {
            var obj = {
                items: data['rankings'],
                rule: data['rule'],
                season_title: season_title
            };

            if (obj['items'][1]) {
                content_link = 'cn/ranking/more_content.html';
            } else {
                content_link = 'cn/ranking/content.html';
            }
            var html = $.get(mainHost + content_link, function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#content').html(str);
            });
        }
    });
}

//===Shooter Table===
function shooterContent(season_id, page_number) {
    $.ajax({
        url: link + '/api/cn/get-shooter',
        type: 'get',
        data: {
            season_id: season_id,
            limit: 50,
            page_number: page_number
        },
        success: function (data) {
            var obj = {
                items: data['shooters'],
                totalPage: data['totalPage'],
                season_id: season_id
            };
            var html = $.get('content.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#content').html(str);
            });
        }
    });
}

//===Get options json length in get-season-options===
function getSeasonOptionLength(season_id, api_name, param1, param2) {
    var content;
    var returnVal;
    var options_length = 0;

    $.ajax({
        url: link + 'api/cn/' + api_name,
        type: 'get',
        data: {
            season_id: season_id
        },
        async: false,
        success: function (data) {
            content = data;
        }
    });

    returnVal = Object.keys(content[param1][param2]).length;
    return returnVal;

}

//===Get Certain Value from get-season-options===
function getSeasonDetail(season_id, firstkey, secondkey = '', secondKeyIndex = 0, thirdkey = '') {
    var content;
    var returnVal;

    $.ajax({
        url: link + 'api/cn/get-season-options',
        type: 'get',
        data: {
            season_id: season_id
        },
        async: false,
        success: function (data) {
            content = data;
        }
    });
    //////console.log(content['season_options'][firstkey][4]);
    ////console.log(content['season_options']['indicator']);


    if (firstkey == 'options') //eg: get default month/round
        returnVal = content['season_options'][firstkey][secondKeyIndex][thirdkey];
    else if (firstkey == 'season_detail')//eg:get title
        returnVal = content['season_options'][firstkey][secondkey];
    else if (firstkey == 'indicator')
        returnVal = content['season_options'][firstkey];

    return returnVal;

}


//Float Menu
/*
$(function floatMenu () {
    var obj = {
    };
    var html=$.get(mainHost+'cn/float_menu.html',function (data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#float_menu').html(str);

        $("#gotoTop").click(function(){
            $("html,body").animate({scrollTop:0},900);
            return false;
        });
    })
});*/

