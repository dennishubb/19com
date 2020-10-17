var show_profile_setting=true;
var user_id=window.localStorage.user_id;
var global_user_data='';

function member_update_info($form){
	//////console.log($form.formSerialize());
	
	$action = $form.attr('action');
	
	var json_form_obj = new Object();
    var name;
	
	//TO GET NAME AND VALUE FROM FORM AND STRINGIFY
    $('#form').find(':input').each(function (key, value) {
        name = $(this).attr("name");
        json_form_obj[name] = $(this).val();
    });
    var formData = JSON.stringify(json_form_obj);
    ////console.log(formData);
	
	$.ajax({
        type:  'PUT',
        //url: getBackendHost() + $action,
        url: link + $action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
         data: formData,
        success: function (response, status, xhr) {
            ////console.log(response);

            obj = response;


            if (obj.code == 1) {
                //if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + '成功编辑资料');
				alert('成功编辑资料');
				$('#editProfileModal').modal('hide');
				//$('.modal-backdrop').remove();
            } 
            else {
							$('#editProfileModal').modal('hide');
							alert(obj.message)
						}
        },

        error: function () {
           alert('AJAX ERROR - update profile body');
		},
    });
}

function show_birthday_dropdown(){
	
}

//from category table,type=sport，parent_id==0
function get_main_sport_dropdown(selected_id,user_data=''){
	var dropdown='';
	var selected='';
	var first_cat_id='';
	
	var filter_array = [{
        field: 'parent_id',
        value: 0,
        operator: '=',
    }];
	
	filter_array.push({
        field: 'category.type',
        value: 'sport',
        operator: '='
    });
	
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/category',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data:  {filter: filter_array},
        success: function (response, status, xhr) {
            ////console.log(response);

            obj = response;
		
			
			if(obj.data.length==0){
				dropdown+='<option >---无记录---</option>';
			}
			else{
				$.each(obj.data,function(index,value){
					
					if (index==0)//record first id to show league list on page load
						first_cat_id=value.id;
					////////console.log(value.name_zh);
					if (value.id==selected_id)
						selected='selected';
					dropdown+='<option value='+value.id+' '+selected+'>'+value.display+'</option>';
					selected='';
					
					//currently set basketball as first since soccer no detail
					if (value.display=='篮球' || value.name=='Basketball'){
						first_cat_id=value.id;
					}
				});
			}
            $('#parent_sport_category').html(dropdown);
            $('#parent_sport_category').val(first_cat_id);
			
			//get_sub_sport_dropdown(selected_id);
			get_league_dropdown(first_cat_id,true);
			
			
			
				
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
	
}

function get_sub_sport_dropdown(parent_id){
	var dropdown='';
	var selected='';
	
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
	
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/category',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data:  {filter: filter_array},
        success: function (response, status, xhr) {
            ////console.log(response);

            obj = response;
			
			if(obj.data.length==0){
				dropdown+='<option >---无记录---</option>';
			}
			else{
				$.each(obj.data,function(index,value){
					////////console.log(value.name_zh);
					//if (value.id==selected_id)
						//selected='selected';
					dropdown+='<option value='+value.id+' '+selected+'>'+value.display+'</option>';
					selected='';
				});
			}
            $('#sub_sport_category').html(dropdown);
			
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
}

function get_league_dropdown(cat_id,default_entry=false,generateChart=false){
	var dropdown='';
	var selected='';
	
	var filter_array = [{
        field: 'league.category_id',
        value: cat_id,
        operator: '=',
    }];
	
	filter_array.push({
        field: 'has_event',
        value: '1',
        operator: '='
    });
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/league',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: {
            filter: filter_array,
			sort:{
					field: "use_count",
					sort: "desc"
				}
        },
        success: function (response, status, xhr) {
            ////console.log(response);

            obj = response;
			
			if(obj.data.length==0){
				dropdown+='<option >---无记录---</option>';
			}
			else{
				$.each(obj.data,function(index,value){
					////////console.log(value.name_zh);
					//if (value.id==selected_id)
						//selected='selected';
					dropdown+='<option value='+value.id+' '+selected+'>'+value.name_zh+'</option>';
					selected='';
				});
			}
			
			$('#league_list').html(dropdown);
			
			if (default_entry==true){ //if on page load
				var fullUri = getCurrentFullUri();
				if (fullUri.indexOf("tab=winrate") <= 0 && fullUri.indexOf("tab=prediction") <= 0)
					generateChartBar();
				else if (fullUri.indexOf("tab=winrate") >= 0)//call winrate table
					get_winrate_table();
				else if (fullUri.indexOf("tab=prediction") >= 0)//call prediction table
					get_prediction_table();
			}
			
			if (generateChart==true)
				generateChartBar();
			
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
}

function embed_reset_pw(){
	 var html = $.get(mainHost + 'cn/profile/reset-password.html', function (data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#resetpw').html(str);
		////console.log($('#resetpw').html())
    });
}

//get member detail for profile.html
function get_member_profile(tab='general'){
	
	//user_id=window.localStorage.user_id;
	
	 $.ajax({
        type: 'GET',
        url: link + '/api/cn/user',
       
		data:  {id: user_id},
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",
      
		success: function (response, status, xhr) {
			////console.log(response);
			var obj = response;
			
			if(response.code == 1){
				var user_data=obj.data;
				////console.log(user_data);

				global_user_data=user_data;


				var banner_image_url='/assets/images/user_level_banner/lvl';
				var birthday=user_data.birth_at;
				var d = new Date(birthday);
				var birthday_year=d.getFullYear();
				var birthday_month=d.getMonth()+1;
				var birthday_day=d.getDay();


				//update profile banner image
				banner_image_url=banner_image_url+user_data.level_id+'.jpg';
				$('#user_profile_bg').find('img').attr('src',banner_image_url);


				//update fields for 帳號設置
				$('#form_user_id').val(user_data.id);
				$('#form_username').val(user_data.username);
				$('#form_phone').val(user_data.phone);
				$('#form_name').val(user_data.name);
				$('#form_email').val(user_data.email);
				$('#form_address').val(user_data.address);
				$('#form_weibo').val(user_data.weibo);

				if (user_data.birth_at=='0000-00-00')
					user_data.birth_at=getCurrentDateTime().split(' ')[0]; //show current date
				$('[name="birth_at"]').val(user_data.birth_at);

				//construct inner body
				if (tab!='x'){ //x is when user click 帳號設置

					//eliminate decimal
					user_data.voucher=Math.round(user_data.voucher);
					user_data.points=Math.round(user_data.points);

					 var user_lvl_icon='';
					////console.log(user_data);

					user_lvl_icon='/assets/images/user_level_icon/lvl'+user_data.level_id+'_red.png';
					////console.log (user_lvl_icon)

					user_data.level_icon_red=user_lvl_icon;

					obj = {
						user_data:user_data
					};
					var html=$.get(mainHost+'cn/profile/profile-body-left.html',function (data) {
						var render = template.compile(data);
						var str = render(obj);

						//after finish left part, fill for right part
						$('#profile_body').prepend(str);
							var html2=$.get(mainHost+'cn/profile/profile-body-right.html',function (data) {
							var render = template.compile(data);
							var str = render(obj);

							$('#profile_body').append(str);

							//build inner body based on 3 tab
							if (tab=='general' || tab==null)
								get_profile_general(user_data);
							else if (tab=='winrate')
								get_profile_winrate(user_data);
							else if (tab=='prediction')
								get_profile_prediction(user_data);
						});
					});
				}

			}
			
            /*obj = {
                user_data:user_data
            };

			
            var html=$.get(mainHost+'cn/profile-body.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#profile_body').html(str);
           
				
            });*/
        },
		error: function () {
            alert('AJAX ERROR - get profile body');
        },
    });
}

function get_profile_general(user_data){
	  var filter_array = [{
        field: 'user_id',
        value: user_id,
        operator: '=',
    }];
	filter_array.push({
                    field: 'status',
                    value: 'predicted',
                    operator: '='
                });
				
	//user_id=window.localStorage.user_id;
	//member_prediction_datatable
	$.ajax({
        type: 'GET',
        url: link + '/api/cn/prediction',
       	data: {	  filter: filter_array,
				sort: 	{
							field: "prediction.created_at",
							sort: "desc"
						},
				limit: 20
		
		},
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            //console.log(response);
            obj = response;
          //console.log(obj.data);

			var prediction_table_data=[];
			var home_team_name='';
			var away_team_name='';
			var handicap='';
			var over_under='';
			var single='';
			var match_status='';
		
			//var data=data=obj.data;
            $.each(obj.data, function (index, value) {
				//////console.log(value.handicap_away);
				//<tr><td>2020-06-30 <br> 19:59:59</td><td>2020-06-30 <br> 19:59:59</td><td>莱斯特城</td><td>0.5</td><td>大2.5</td><td>主</td><td>300</td><td>未开赛</td><td>-</td></tr>
				
				if (value.home_team_data == undefined) {	
					return true;	
				}
								
				home_team_name=value.home_team_data.name_zh;
				away_team_name=value.away_team_data.name_zh;
				
				//CONSTRUCT HANDICAP START
				if (value.handicap_home==1){
					handicap=home_team_name + ' - 主 ' + value.event_data.handicap_home_bet + '/' + value.event_data.handicap_home_odds;
				}
				else if (value.handicap_away==1){
					handicap=away_team_name + ' - 客 '+ value.event_data.handicap_away_bet + '/' + value.event_data.handicap_away_odds;
				}
				else
					handicap='-';
				//CONSTRUCT HANDICAP END
				
				
				//CONSTRUCT OVER UNDER START
				if (value.over_under_home==1){
					over_under=home_team_name +' 主 ' + value.event_data.over_under_home_bet + '/' + value.event_data.over_under_home_odds;
				}
				else if (value.over_under_away==1){
					over_under=away_team_name + ' 客 '+ value.event_data.over_under_away_bet + '/' + value.event_data.over_under_away_odds;
				}
				else
					over_under='-';
				//CONSTRUCT OVER UNDER END
				
				//CONSTRUCT SINGLE START
				
				if (value.single_home==1){
					//single=home_team_name +' 主 ' + value.event_data.single_home ;
					single='主';
				}
				else if (value.single_away==1){
					//single=away_team_name + ' 客 ' + value.event_data.single_away ;
					single='客';
				}
				else if (value.single_tie==1){
					//single='和 ' + value.event_data.single_tie ;
					single='和 ';
				} else {
                    single = "-";
                }
				//CONSTRUCT SINGLE END
				
				//CONSTRUCT MATCH STATUS START  current date > match_at and ended = 0.
				/*if(value.event_data.ended==1)
					match_status='已结束'
				
				else if (value.event_data.ended==0){
					var d = new Date();
					var curr_datetime = d.getFullYear()  + "-" + ( (d.getMonth()+1).toString().padStart(2, '0') )  + "-" + d.getDate().toString().padStart(2, '0') +" " + d.getHours().toString().padStart(2, '0') + ":" + d.getMinutes().toString().padStart(2, '0')+ ":" + d.getSeconds().toString().padStart(2, '0');
					//////console.log(curr_datetime);
					
					var match_at=value.event_data.match_at;
					//////console.log(match_at);
					
					if (curr_datetime>match_at)//current date > match_at and ended = 0
						match_status='比赛中'
					else
						match_status='未开赛'
				}*/
				
				
                var final_win_amount = ""
                if (value.event_data.ended == 0 || value.status == "") {
                    match_status = '-'
                }else {
                    if ((value.win == 1) && (value.win_amount != 0.00)) {
                        match_status = '胜'
                        final_win_amount = '+' + value.win_amount
                      
                    } else if ((value.win == 0) && (value.win_amount != 0.00)) {
                        match_status = '败'
                        final_win_amount = '-' + value.win_amount
                    } else if ((value.win == 0) && (value.win_amount == 0.00)) {
                        match_status = '败'
                        final_win_amount = value.win_amount
                    } else if ((value.win == 1) && (value.win_amount == 0.00)) {
                        match_status = '胜'
                        final_win_amount = value.win_amount
                    }
                }
				//CONSTRUCT MATCH STATUS END
				
				
				
				prediction_table_data.push({
					created_at: value.created_at,
					match_at:value.event_data.match_at,
					handicap: handicap,
					over_under: over_under,
					single: single,
					win_amount: final_win_amount,
					match_status: match_status,
					league_name: value.league_data.name_zh,
					
				});
				
				
               
			});
			
			//////console.log(prediction_table_data);
			
			
			 
			obj = {
				user_data:user_data,
				table_length:obj.data.length,
                prediction_table_data:prediction_table_data
            };
//console.log(obj)
			
            var html=$.get(mainHost+'cn/profile/profile-body-general.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#profile_right').append(str);
				get_main_sport_dropdown(0,user_data);
				
				
				  
				//if newly signup, show profile setting
				if (getQueryString('signup')==1 && show_profile_setting==true ){
					
					//var a=document.getElementById("profileModal").click();
					$( "#profileModal" ).trigger( "click" );
					
					show_profile_setting=false;
					//////console.log('a');
				}
            });
		   
        },
        error: function () {
            //showAlert("Problem occurred while sending request.", "danger");
            //unblockUI();
			 alert('AJAX ERROR - get profile prediction table');
        }

    });
}

function generateChartBar(){
	//get chart data and bar data from user_data
	
	//console.log(global_user_data)
	var chart_prediction_count=0;//预言次数  Number of Prophecies -  chart use prediction_participation_rate, display use prediction_count
	var chart_voucher=0;//神级兑换卷 God Level Exchange Volume - display use user.vouchers, chart use 100%
	var chart_participate_rate=0;//参与率 Participation Rate - prediction_participation_rate
	var chart_win_rate=0;//总胜率 total_win_rate - total_win_rate
	var chart_points=0;//战数 display use user.points, chart use 100%
	
	var bar_curr_month_winrate=0//单月胜率 Single month win rate - win_rate
	var bar_zhutui=0;//主推月胜率 Mainly promote monthly win rate - 0
	var bar_topten_time=0;//神准预言家 Prospective Prophet - bar use 'top_ten_rate', display use top_ten_count

	var display_chart_prediction_count=0;
	var display_chart_voucher=0;
	var display_chart_points=0;
	var display_bar_topten_time=0;
	
	////console.log($('#parent_sport_category').val())
	////console.log($('#league_list').val())
	var filter_array = [{
			field: 'user_id',
			//value: window.localStorage.user_id,
			value: user_id,
			operator: '=',
		},
		{
            field: 'league_id',
            value: $('#league_list').val(),
            //value: 1878,
            operator: '='
        },
		{
            field: 'category_id',
            value: $('#parent_sport_category').val(),
            operator: '='
        }
		
	];
	
	
	
	$.ajax({
		type: 'GET',
		//url: getBackendHost() + $action,
		url: link + '/api/cn/prediction_stats',
		crossDomain: true,
		headers: getHeaders(),
		contentType: false,
		processData: true,
		// contentType: "charset=utf-8",
		 data: {filter: filter_array},
		success: function (response, status, xhr) {
			////console.log(response);

			obj = response;
			var stat_data=obj.data[0]
			
			
			if (obj.code == 1 ) {
				//console.log(stat_data)
				
				if (stat_data){
					
					//预言次数  Number of Prophecies -  chart use prediction_participation_rate, display use prediction_count
					//chart_prediction_count=stat_data.prediction_participation_rate;
					chart_prediction_count=stat_data.top_ten_count;//top_ten_count
					display_chart_prediction_count=stat_data.prediction_count;
					
					//神级兑换卷 God Level Exchange Volume - display use user.vouchers, chart use 100%
					chart_voucher=100;
					display_chart_voucher=global_user_data.voucher;
					
					//参与率
					chart_participate_rate=stat_data.prediction_participation_rate;
					
					//总胜率
					chart_win_rate=stat_data.total_win_rate;
					
					//战数 display use user.points, chart use 100%
					chart_points=100;
					display_chart_points=global_user_data.points;
					
					
					//单月胜率 
					bar_curr_month_winrate=stat_data.win_rate; 
					 
					//神准预言家 Prospective Prophet - bar use 'top_ten_rate', display use top_ten_count
					bar_topten_time=stat_data.top_ten_rate ; 
					display_bar_topten_time=stat_data.top_ten_count ; 
				}
				
				//console.log(chart_points,chart_prediction_count,chart_voucher,chart_participate_rate,chart_win_rate,display_chart_prediction_count)
				//console.log(bar_curr_month_winrate,bar_zhutui,bar_topten_time,display_bar_topten_time)
				//show chart
				showChart(chart_points,chart_prediction_count,chart_voucher,chart_participate_rate,chart_win_rate,display_chart_prediction_count);//"战数", "预言次数", "神级兑换卷", "参与率", "总胜率"
				//showChart(100,0,100,200,0,6)
				//show bar
				showBar(bar_curr_month_winrate,bar_zhutui,bar_topten_time,display_bar_topten_time);
			}
		  
		},

		error: function () {
			showAlert("Problem occurred while sending request.", "danger", $error_selector);
		},
	});
		
	/*$.each(user_data, function (index, value) {
		////console.log(index,value)
		
		if (index=='prediction count')//预言次数
			chart_prediction_count=value;
		else if (index=='voucher')//神级兑换卷
			chart_voucher=value;
		else if (index=='prediction count')//参与率
			chart_participate_rate=value;
		else if (index=='win_rate')//总胜率
			chart_win_rate=value.replace("%","");
		else if (index=='points')//战数
			chart_points=value;
		else if (index=='prediction count')//单月胜率
			bar_curr_month_winrate=value;
		//else if (index=='prediction count')//主推月胜率
			//bar_zhutui=value;
		else if (index=='prediction count')//神准预言家
			bar_topten_time=value;
			
	});*/
	
	
}

function get_profile_winrate(user_data){
	
	
	
	var html=$.get(mainHost+'cn/profile/profile-body-winrate.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#profile_right').append(str);
				
				//fill in filter dropdown list
				get_main_sport_dropdown();//in this function call get_winrate_table(); after dropdown done
				$('#time').html(get_time_dropdown());
				
				
				
				
            });
}

function get_profile_prediction(user_data){
	//var time='<option value="-">---请选择---</option>    ' + get_time_dropdown();
	
	var html=$.get(mainHost+'cn/profile/profile-body-prediction.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#profile_right').append(str);
				
				//fill in filter dropdown list
				get_main_sport_dropdown();
				$('#time').html(get_time_dropdown());
				
				
				
            });
}

//time dropdown for win_rate and prediction table
function get_time_dropdown(){
	
	var since_year=parseInt('2020');
	var since_month=parseInt('06');
	var curr_datetime=getCurrentDateTime();
	//alert(curr_datetime);
	//alert (curr_datetime.split('-'));
	
	var curr_year=parseInt(curr_datetime.split('-')[0]);
	var curr_month=parseInt(curr_datetime.split('-')[1]);
	
	//FOR TESTING USAGE
	//curr_year=2022;
	//curr_month=06;
	
	var value=curr_year+'/'+curr_month;
	var value_arr=[];
	var temp='';
	var break_count=0;
	var month_loop='';
	var curr_yearmonth='';
	var dropdown='';
	
	//*******CONSTRUCT YEAR/TIME START**********
	//construct for year 2020, month> june
	if (curr_year>=since_year && curr_month>=since_month){
		
		if (curr_year>since_year) 
			month_loop=12;
		else 
			month_loop=curr_month;
		
		for (i=since_month;i<=month_loop;i++){
				
				
				temp='2020/'+i.toString().padStart(2, '0');
				////console.log(temp)
				
				value_arr.push(temp);
				
				break_count++;
				if (break_count>30){
					break_count=0;
					break;
				}
		}
	}
	
	//if year>2021
	if (curr_year>since_year){
		for (i=since_year;i<=curr_year;i++){
			if (i==2020)
				continue;
			else{
				//if not 2020, loop for whole month
				if (i<curr_year)
					month_loop=12;
				else
					month_loop=curr_month;
				
				for (j=1;j<=month_loop;j++){
					temp=i+'/'+j.toString().padStart(2, '0');
					value_arr.push(temp);
				}
			}
			
		}
	}
	
	//*******CONSTRUCT YEAR/TIME END**********
	
	//*******CONSTRUCT 	DROPDOWN LIST START**********
	var selected='';
	var temp='';
	value_arr.forEach(function (value, index) {
		////console.log(value, index);
		temp=curr_year+'/'+curr_month.toString().padStart(2, '0');
		
		if (value==temp){
			selected='selected';
			//alert()
		}
		////console.log(selected)
		dropdown+='<option value="'+value+'" '+selected+'>'+value+'</option>';
	});
	//*******CONSTRUCT 	DROPDOWN LIST END************
	////console.log(value_arr);
	////console.log(dropdown);
	
	return dropdown;
}

function get_winrate_table(search=0){
	var filter_array = [{
        field: 'prediction_rate.user_id',
        value: user_id,
        operator: '=',
    }];
	
	var val_year='';
	var val_month='';
	
	if (search==1){
		
		$('#form').find(':input').each(function (key, value) {
			name = $(this).attr("name");
			val = $(this).val();
			////console.log(name + '->' + val);
			
			if (name!='search_button'){
				if (val!='---请选择---' && val!='-'){
					
					if (name=='time'){
						val_year=parseInt(val.split('/')[0]);
						val_month=parseInt(val.split('/')[1]);
						
						filter_array.push({
							field: 'prediction_rate.month',
							value: val_month,
							operator: '='
						});
						filter_array.push({
							field: 'prediction_rate.year',
							value: val_year,
							operator: '='
						});
					}
					else{
						filter_array.push({
							field: name,
							value: val,
							operator: '='
						});
					}
					
				}
			}
		});
		
		
		
		//console.log(filter_array)
	}
	
	//default filter current year/month, and first result from category API
	else{
		val = $('#time').val();
		val_year=parseInt(val.split('/')[0]);
		val_month=parseInt(val.split('/')[1]);
		
		////console.log($('#time').val())
		////console.log($('#parent_sport_category').val())
		
		filter_array.push({
			field: 'prediction_rate.month',
			value: val_month,
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.year',
			value: val_year,
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.category_id',
			value: $('#parent_sport_category').val(),
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.league_id',
			value: $('#league_list').val(),
			operator: '='
		});
		////console.log(filter_array)
	}
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: getBackendHost() + '/api/cn/prediction_rate',
        //url: "http://test.19com.backend2:5280/" + '/api/cn/prediction_rate',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        
		data: {	filter: filter_array,
				sort: 	{
							field: "prediction_rate.sorting",
							sort: "asc"
						},
		
		},
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;
			
			
			$.each(obj.data,function(index,value){
				
				if (value.type=='handicap')
					value.display='让分';
				else if (value.type=='over_under')
					value.display='大小分';
				else if (value.type=='single')
					value.display='独赢';
				else if (value.type=='total')
					value.display='总胜场';

				value.rate=value.rate+'%';
			});
			var winrate_arr=obj.data;
			var data_length=obj.data.length;
			
			obj1 = {
					winrate_arr: winrate_arr,
					table_length:data_length
				};
			//////console.log(obj);	
			
			var html = $.get(mainHost + 'cn/profile/winrate_table.html', function (data) {
				var render = template.compile(data);
				var str = render(obj1);

				//////console.log($('#winrate_table'))
				$('#winrate_table').html(str);
				////console.log($('#winrate_table').html())
				////console.log(winrate_arr);

			});
			
            
			//get_sub_sport_dropdown(selected_id);
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
}

function get_prediction_table(search=0){
	var filter_array = [{
        field: 'prediction_rate.user_id',
        value: user_id,
        operator: '=',
    }];
	
	filter_array.push({
			field: 'prediction_rate.type',
			value: 'total',
			operator: '='
		});
		
	if (search==1){
		
		$('#form').find(':input').each(function (key, value) {
			name = $(this).attr("name");
			val = $(this).val();
			////console.log(name + '->' + val);
			
			if (name!='search_button'){
				if (val!='---请选择---' && val!='-'){
					
					if (name=='time'){
						val_year=parseInt(val.split('/')[0]);
						val_month=parseInt(val.split('/')[1]);
						
						filter_array.push({
							field: 'prediction_rate.month',
							value: val_month,
							operator: '='
						});
						filter_array.push({
							field: 'prediction_rate.year',
							value: val_year,
							operator: '='
						});
					}
					else{
						filter_array.push({
							field: name,
							value: val,
							operator: '='
						});
					}
					
				}
			}
		});
		
		
		
		////console.log(filter_array)
	}
	//default filter current year/month, and first result from category API
	else{
		val = $('#time').val();
		val_year=parseInt(val.split('/')[0]);
		val_month=parseInt(val.split('/')[1]);
		
		////console.log($('#time').val())
		////console.log($('#parent_sport_category').val())
		
		filter_array.push({
			field: 'prediction_rate.month',
			value: val_month,
			//value: 6,
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.year',
			value: val_year,
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.category_id',
			value: $('#parent_sport_category').val(),
			//value: 2,
			operator: '='
		});
		filter_array.push({
			field: 'prediction_rate.league_id',
			value: $('#league_list').val(),
			//value: 2,
			operator: '='
		});
		////console.log(filter_array)
	}
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: getBackendHost() + '/api/cn/prediction_rate',
		//url: "http://test.19com.backend2:5280/" + '/api/cn/prediction_rate',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        //data:  {filter: filter_array},
		data: {	filter: filter_array,
				sort: 	{
							field: "prediction_rate.sorting",
							sort: "asc"
						},
		
		},
        success: function (response, status, xhr) {
           //console.log(response);
			var prophet='-';
			var arr=[];
            obj = response;
			
			if (obj.data.length>0){
				
				/*本 赛 季 胜 率
				season_rate / top_ten_season_rate
				单 月 最 低 预 测 次 数
				total_count / top_ten_prediction_count
				单 月 总 胜 率
				rate / top_ten_rate*/
				
				var season_rate=Number(obj.data[0].season_rate).toFixed(2); 
				var top_ten_season_rate=Number(obj.data[0].top_ten_season_rate).toFixed(2); 
				var total_count=obj.data[0].total_count;
				var top_ten_prediction_count=obj.data[0].top_ten_prediction_count;
				var rate=Number(obj.data[0].rate).toFixed(2); 
				var top_ten_rate=Number(obj.data[0].top_ten_rate).toFixed(2); 
				
				var row1Prophet='X';
				var row2Prophet='X';
				var row3Prophet='X';
				
				if (season_rate>=top_ten_season_rate)
					row1Prophet='O';
				if (total_count>=top_ten_prediction_count)
					row2Prophet='O';
				if (rate>=top_ten_rate)
					row3Prophet='O';
				


				var arr = [ {display: '本赛季胜率', 
							 columnVal1: season_rate+'%',
							 columnVal2:top_ten_season_rate+'%',
							 prophet:row1Prophet},
							 
							 {display: '单月最低预测次数', 
							 columnVal1: total_count,
							 columnVal2:top_ten_prediction_count,
							 prophet:row2Prophet},
							 
							 {display: '单月总胜率', 
							 columnVal1:rate+'%',
							 columnVal2:top_ten_rate+'%',
							 prophet:row3Prophet}	 
							 
							 /*{display: '主推最低预测次数', 
							 columnVal1:rate+'%',
							 columnVal2:top_ten_rate+'%',
							 prophet:row3Prophet}
							 
							 {display: '主推胜率', 
							 columnVal1:rate+'%',
							 columnVal2:top_ten_rate+'%',
							 prophet:row3Prophet}			
							 
							 */
						   ];
			
				//console.log(arr);
			}
			
			var prediction_arr=arr;
			var data_length=obj.data.length;
			
			obj1 = {
					prediction_arr: prediction_arr,
					table_length:data_length
				};
			//console.log(obj1);	
			var html = $.get(mainHost + 'cn/profile/prediction_table.html', function (data) {
				var render = template.compile(data);
				var str = render(obj1);

				$('#prediction_table').html(str);
				////console.log(str);
				
				display_prediction_msg();//show message for 下期评选日 and 评选期間
			});
			
            
			//get_sub_sport_dropdown(selected_id);
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
}

//show message for 下期评选日 and 评选期間
function display_prediction_msg(){
	
	var display_month='';
	var selected_time=$('#time').val();
	var selected_year=selected_time.split('/')[0];
	var selected_month=selected_time.split('/')[1];
	var selected_total_days=get_days_of_month(selected_year,selected_month);
	
	var selected_date=selected_year+'-'+selected_month;
	var date = new Date(selected_date);
	var next_month=date.getMonth()+2;
	
	if (next_month==13)
		next_month=1;
	
	//console.log(next_month)
	
	var curr_datetime=getCurrentDateTime();
	//var curr_year=curr_datetime.split('-')[0];
	//var curr_month=curr_datetime.split('-')[1];
	var curr_day=curr_datetime.split('-')[1];
	
	//var next_month=parseInt(curr_datetime.split('-')[1])+1;
	
	//下期评选日 - 小于2号显示当月
	if (curr_day<=2)
		display_month=selected_month;
	else //下期评选日 - 大于二号显示下个月
		display_month=next_month;
	
	display_month=parseInt(display_month);
	
	$('#curr_month').html(display_month);
	
	//评选期間 - 显示用户挑选的月分
	var msg=selected_month+'/1~'+selected_month+'/'+selected_total_days;
	$('#next_month').html(msg);
}

function change_password($form){
	//$action = $form.attr('action');
	
	var json_form_obj = new Object();
    var name;
	////console.log($action)
	//TO GET NAME AND VALUE FROM FORM AND STRINGIFY
	$('#reset_pw_user_id').val(user_id);
	
    $('#reset_pw_form').find(':input').each(function (key, value) {
        name = $(this).attr("name");
        json_form_obj[name] = $(this).val();
		
		////console.log(name,$(this).val())
    });
	var formData = JSON.stringify(json_form_obj);
    ////console.log(formData);
	
	if (json_form_obj['password']!=json_form_obj['confirmpw'])
		alert('密码不一致');
	else{
		$.ajax({
			type: 'PUT',
			//url: getBackendHost() + $action,
			url: link + '/api/cn/user',
			crossDomain: true,
			headers: getHeaders(),
			contentType: false,
			processData: false,
			// contentType: "charset=utf-8",
			data: formData,
			success: function (response, status, xhr) {
				//console.log(response);

				obj = response;

				if (obj.code == 1) {
					alert('成功重置密码');
					$('#resetpw').modal('hide');
					//$('.modal-backdrop').remove();
					//localStorage.clear();
					//redirect_to('/cn/');
				}
			   /* if (obj.code == 1) {
					if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + '成功编辑资料');
				} else if (obj.code == -1) {
					redirect_to_login();
				} else {
					showAlert(obj.message, "danger", $error_selector);
				}*/
			},

			error: function () {
				showAlert("Problem occurred while sending request.", "danger", $error_selector);
			},
		});
	}
}


//this one for preview only, when file onchange display uploaded image to crop
function local_media_ajax_submit() {
	
	$_form = document.getElementById('user_image_form');
    var formData = new FormData($_form);

	var file2 = $('#file2')[0].files[0];
	formData.append('file', file2);
	
	$.ajax({
        type: 'POST',
        //url: 'http://fdcb6912.ngrok.io/assets/php/media-meta.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url:  getHost() +'/assets/php/media-meta.php',
        contentType: false,
        cache: false,
        processData: false,
         data: formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            //var temp_image_url='/'+obj.url;
            var temp_image_url='/upload/media/user_image/_temp/'+obj.name;
			//console.log(temp_image_url)
			$('#current_thumbnail').attr('src',temp_image_url)
			cropper_destroy();
			show_cropper(temp_image_url);
			//$('#image').attr('src','http://test.19com/upload/media/5f72a0218b83e11d0ee9fe0cb4a74ee7.png')
			

        },
        error: function (resp) {
            //console.log(resp);
            ////console.log(resp);
            alert("Problem occurred while sending request.");
        },
    });
}

function after_media_meta($data) {
	
	//console.log($data);
	var json_form_obj = {
      "image_data":{},
	 
	};
	var extra=$data.media_meta_data.extra;
	var pic_url='/'+$data.media_meta_data.url;
	
	json_form_obj['id']=user_id;
	json_form_obj['url'] =$('input[name="url"]').val();
	json_form_obj.image_data.url=$data.media_meta_data.url;
	json_form_obj.image_data.name=$data.media_meta_data.name;
	json_form_obj.image_data.type=$data.media_meta_data.type;
	json_form_obj.image_data.size=$data.media_meta_data.filesize;
	////console.log($data);
	
	json_form_obj.extra=$data.extra;
    ////console.log("after_media_meta + "+image_size);
    ////console.log(JSON.stringify(json_form_obj));
	////console.log(link + '/api/cn/promotion');
	
	var formData = JSON.stringify(json_form_obj);
	//console.log(formData);
	 $.ajax({
        type: 'PUT',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/user',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
			////console.log(image_size);
			//console.log(response);
			
            obj = response;
			
			
			media_save(obj,extra,pic_url);
        },
		
        error: function () {
           alert("Problem occurred while sending request.");
        },
    });
}

function media_save(obj,extra,pic_url){
	//var fan_id= getQueryString('id');
	var msg='';
	var redirect_url='';
	
	//console.log(obj)
	$('#tempfile').val(extra);
	$_form = document.getElementById('user_image_form');
    var formData = new FormData($_form);
	
    //formData.append('tempfile', obj.extra.extra)
	
	 $.ajax({
        type: 'POST',
       // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
	    //url: getHost()+'/assets/php/media-meta.php',
        url:  '/assets/php/media-save.php',
         data: formData,
        crossDomain: true,
        contentType: false,
        processData: false,
		 success: function (response, status, xhr) {
           //console.log(response);
			//console.log('yeah');
            obj = response;
			
			
			window.localStorage.profile_thumbnail = pic_url;
			alert('成功设置新头像！');
			redirect_to(getCurrentFullUri());
			
		
           // if (big_indicator==true && medium_indicator==true && small_indicator==true)
			//	addPromo_all($form,$data,$error_selector = $(".message_output"));
		//redirect_to("fan-zone-edit.html?id="+promo_id+"&alert-success=" + msg);
        },
        error: function (resp) {
           
            ////console.log(resp);
             alert("Problem occurred while sending request.");
        },
    });
	
    
}