
var global_totalPage;
var global_prediction_table_data;
var row_per_page=10; //limitation, row per page
var data_length='';

//from category table,type=sport，parent_id==0
function get_main_sport_dropdown(selected_id){
	var dropdown='<option value="">---请选择---</option>';
	var selected='';
	
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
				dropdown='<option value="">---无记录---</option>';
			}
			else{
				$.each(obj.data,function(index,value){
					////////console.log(value.name_zh);
					if (value.id==selected_id)
						selected='selected';
					dropdown+='<option value='+value.id+' '+selected+'>'+value.display+'</option>';
					selected=''
				});
			}
			
            $('#parent_sport_category').html(dropdown);
			//get_sub_sport_dropdown(selected_id);
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
	
}

function get_league_dropdown(cat_id){
	var dropdown='<option value="">---请选择---</option>';
	var selected='';
	if (!cat_id) {
		dropdown='<option value="">---无记录---</option>';
		$('#league_list').html(dropdown);
		return ""
	}
	
	var filter_array = [{
        field: 'league.category_id',
        value: cat_id,
        operator: '=',
    }];
	
	$.ajax({
        type:  'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/league',
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
				dropdown='<option value="">---无记录---</option>';
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
			
        },

        error: function () {
           alert('AJAX ERROR - get main category');
		},
    });
}

//fill up filter dropdown lists
function get_filter(){
	var month_dropdown='<option value="">---请选择---</option>';
	var year_dropdown='<option value="">---请选择---</option>';

	month_dropdown+=get_month_dropdown();
	year_dropdown+=get_year_dropdown();
	
	get_main_sport_dropdown(0);//get main category 
	//get_league_dropdown();
	$('#month').html(month_dropdown);
	$('#year').html(year_dropdown);
}

//get match history table
function get_match_history_table(search=0,sorting_arr='',match_at_direction='fa-sort-down'){//search=1 means call from user's search
	//user_id=window.localStorage.user_id;
	
	var filter_array = [{
        field: 'ended',
        value: 1,
        operator: '=',
    }];
	
	if (sorting_arr==''){
		sorting_arr={
			field: 'match_at',
			sort: 'desc',
		};
	}
	
		
	/*filter_array.push({
        field: 'user_id',
        value: user_id,
        operator: '='
    });*/
	
	//if search criteria
	if (search==1){
		var name='';
		var val='';
		var year;
		
		$('#form').find(':input').each(function (key, value) {
			name = $(this).attr("name");
			val = $(this).val();
			if ( name=='match_at_year' && (val) ){
				filter_array.push({
					field: 'YEAR(event.match_at)',
					value: val,
					operator: '='
				});
			}
			if ( name=='match_at_month' && (val)){
				filter_array.push({
					field: 'MONTH(event.match_at)',
					value: val,
					operator: '='
				});
			}
			
			if (name!='search_button' && name!='match_at_year' && name!='match_at_month'){
				if (val){
					filter_array.push({
						field: name,
						value: val,
						operator: '='
					});
				}
			}
				
			////console.log(name+'/'+val);
		});
	}
	////console.log(filter_array)
	$.ajax({
        type: 'GET',
				//url: link + '/api/cn/prediction',
				url: link + '/api/cn/event',
       
		data:  {	
					filter: filter_array,
					 sort: sorting_arr
				},
				
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",
      
		success: function (response, status, xhr) {
			////console.log(response);
			var obj = response;
			//////console.log(obj.data.length);
			
			var total_row=obj.data.length;
			
			var total_page=Math.ceil(total_row/row_per_page);//10 rows per page
			
			var prediction_table_data=[];
			var home_team_name='';
			var away_team_name='';
			var handicap='';
			var over_under='';
			var single='';
			var match_status='';
			var page_row='';
			var winning_team_name = '';
			var i=1;
			
			
			$.each(obj.data, function (index, value) {
				//console.log(value);
				//<tr><td>2020-06-30 <br> 19:59:59</td><td>2020-06-30 <br> 19:59:59</td><td>莱斯特城</td><td>0.5</td><td>大2.5</td><td>主</td><td>300</td><td>未开赛</td><td>-</td></tr>
				
				home_team_name=value.home_team_data.name_zh;
				away_team_name=value.away_team_data.name_zh;
				if (value.result_data){
					//CONSTRUCT HANDICAP START
					if (value.result_data.handicap_home==1){
						handicap=home_team_name + ' 主 ' + value.handicap_home_bet + '/' + value.handicap_home_odds;
					}
					else if (value.result_data.handicap_away==1){
						handicap=away_team_name + ' 客 '+ value.handicap_away_bet + '/' + value.handicap_away_odds;
					}
					//CONSTRUCT HANDICAP END
					
					
					//CONSTRUCT OVER UNDER START
					if (value.result_data.over_under_home==1){
						over_under=home_team_name +' 主 ' + value.over_under_home_bet + '/' + value.over_under_home_odds;
					}
					else if (value.result_data.over_under_away==1){
						over_under=away_team_name + ' 客 '+ value.over_under_away_bet + '/' + value.over_under_away_odds;
					}
					//CONSTRUCT OVER UNDER END
					
					//CONSTRUCT SINGLE START
					if (value.result_data.single_home==1){
						//single=home_team_name +' 主 ' + value.event_data.single_home ;
						single='主';
						winning_team_name = home_team_name;
					}
					else if (value.result_data.single_away==1){
						//single=away_team_name + ' 客 ' + value.event_data.single_away ;
						single='客';
						winning_team_name = away_team_name;
					}
					else if (value.result_data.single_tie==1){
						//single='和 ' + value.event_data.single_tie ;
						single='和 ';
						winning_team_name = ""
					}
				//CONSTRUCT SINGLE END
				
				//電競 only have 獨贏
					if (value.category_data!=null){
						if (value.category_data.display=='电竞' || value.category_data.name=='Gaming'){
							handicap='-';
							over_under='-';
						}
					}
						
				}
				//CONSTRUCT MATCH STATUS START  current date > match_at and ended = 0.
				if(value.ended==1)
					match_status='已结束'
				
				else if (value.ended==0){
					var d = new Date();
					var curr_datetime = d.getFullYear()  + "-" + ( (d.getMonth()+1).toString().padStart(2, '0') )  + "-" + d.getDate().toString().padStart(2, '0') +" " + d.getHours().toString().padStart(2, '0') + ":" + d.getMinutes().toString().padStart(2, '0')+ ":" + d.getSeconds().toString().padStart(2, '0');
					//////console.log(curr_datetime);
					
					var match_at=value.match_at;
					//////console.log(match_at);
					
					/*if (curr_datetime>match_at)//current date > match_at and ended = 0
						match_status='比赛中'
					else
						match_status='未开赛'*/
				}
				//CONSTRUCT MATCH STATUS END
				
				//CONSTRUCT row_page_no START
					page_row=Math.ceil(i/row_per_page);//get page number for that certain row
				//CONSTRUCT row_page_no END
				
				prediction_table_data.push({
					event_id:value.id,
					match_at:value.match_at,
					league_name:value.league_data.name_zh,
					home_team:value.home_team_data.name_zh,
					away_team:value.away_team_data.name_zh,
					win_team:winning_team_name,
					handicap: handicap,
					over_under: over_under,
					single: single,
					match_status: '已结束',
					page_row:page_row
				});
				
				
               i++;
			});
			global_prediction_table_data=prediction_table_data;
			global_totalPage =total_page;
			data_length=obj.data.length;
			//////console.log(total_page);
			
			
			obj = {
				totalPage:total_page,
				curr_page:1,//set page 1 to active
				match_at_direction:match_at_direction,
				table_length:data_length,
				prediction_table_data:prediction_table_data
            };

            var html=$.get(mainHost+'cn/match-history-body.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#match_history_table_body').html(str);
           
				
            });
        },
		error: function () {
            alert('AJAX ERROR - get match history');
        },
    });
}

//get content after user click cha kan
function get_editor_prediction(id){
	
	$('#editorModal').html("");

    /*var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 11;
    }*/

    var editor_prediction = $.ajax({
        url: link + 'api/cn/event',
        type: 'get',
        data: {
            id: id
        },
        success: function (response) {
            var obj = {
                editor_prediction: response.data
            };
			
        }
    });

    $.when(editor_prediction).done(function (editor_prediction) {
        let obj = [];
        obj['editor_prediction'] = editor_prediction.data;
		obj.editor_note=obj['editor_prediction']['editor_note'];
		//////console.log(obj['editor_prediction']['editor_note']);
		////console.log(obj.editor_note);
		
		if(obj.editor_note){
                obj.editor_note = obj.editor_note.replace(/\\"/g, '"');
                var element = $(`<div>${obj.editor_note}</div>`);
                element.find('a').each(function() {
                    const allowedTypes = ['avi','flv','mov','mp4','mpeg']
                    const href = $(this).attr('href') || ''
                    const videoData = href.split('.')
                    const type = videoData[videoData.length - 1]
                    const isAllowed = !!_.find(allowedTypes, t => t === type)
                    if(!type || !isAllowed)
                        return
										$(this).replaceWith(`<div><iframe class="centerClass" src="${href}"></iframe></div>`)
								});
								element.find('img').each(function() {
									$(this).width("100%").height(300);
									$(this).addClass( "centerClass" );
								});
                obj.editor_note = element.html()
            }
        var html = $.get('/cn/match-editor-modal.html', function (data) {
            var render = template.compile(data);
						var str = render(obj);
            $('#editorModal').html(str);
            var html_content = editor_prediction.data.content;
        });
    });
    return editor_prediction;
	
}

//pagination for match_history_table
function pagination(page_no){
	
	var proceed=false;
	
	if (page_no=='next'){
		//alert(page_no);
		
		var page_no = parseInt($( ".active a" ).html())+1;
		//////console.log(page_no);
		
		if (page_no<=global_totalPage)
			proceed=true;
	}
	
	else if (page_no=='prev'){
		//alert(page_no);
		var page_no = parseInt($( ".active a" ).html())-1;
		////console.log(page_no);
		
		if (page_no>0)
			proceed=true;
	}
	
	else{
		//////console.log(global_prediction_table_data);
		//////console.log(global_totalPage);
		proceed=true;
	}
	//////console.log(global_totalPage);
	
	if (proceed==true){
		obj = {
			totalPage:global_totalPage,
			curr_page:page_no,//set page 1 to active
			table_length:data_length,
			prediction_table_data:global_prediction_table_data
		 };

		 var html=$.get(mainHost+'cn/match-history-body.html',function (data) {
			 var render = template.compile(data);
			 var str = render(obj);

			 $('#match_history_table_body').html(str);
		
			
		 });
	}
}

//sorting for match_history_table

function sorting(th,sort_column){
	
	var sort_direction='';
	var match_at_direction='fa-sort-down';//default is down
	var th_html=$("#"+th).html();//alert( th_html );
	
	if (th_html.indexOf("fa-sort-down") >= 0){//current is desc, now chg to asd
		sort_direction='asc';
		match_at_direction='fa-sort-up';
	}
	else{
		sort_direction='desc';
		match_at_direction='fa-sort-down';
	}
	
	////console.log(th_html)
	var sorting_arr={
			field: sort_column,
			sort: sort_direction,
		};
	get_match_history_table(0,sorting_arr,match_at_direction);
	
	/*if (th_html.indexOf("fa-sort-down") >= 0)//current is desc, now chg to asd
		new_html= th_html.replace("fa-sort-down", "fa-sort-up");
	else //current is asc, now chg to desc
		new_html= th_html.replace("fa-sort-up", "fa-sort-down");
		
	$("#"+th).html(new_html);//change arrow direction	*/
}

function sorting_old(th,sort_column){
	
	
	var th_html=$("#"+th).html();//alert( th_html );
	var curr_page_no = parseInt($( ".active a" ).html());
	
	var next_sort_type='';
	var next_arrow='';
	var new_html='';
	var prediction_table_data=[];
	
	
	$.each(global_prediction_table_data, function (index, value) {
		
		if (value.page_row==curr_page_no){//only get current page record for sorting
			prediction_table_data.push(global_prediction_table_data[index]);
		}
			
	});
	
	if (th_html.indexOf("fa-sort-down") >= 0){//current is desc, now chg to asd
		next_sort_type='asc';//Ascending should be upwards
		next_arrow='fa-sort-up';
		new_html= th_html.replace("fa-sort-down", "fa-sort-up");
		
		if (sort_column=='match_at')
			prediction_table_data.sort((a,b) => (a.match_at > b.match_at) ? 1 : ((b.match_at > a.match_at) ? -1 : 0));
		else if (sort_column=='match_status')
			prediction_table_data.sort((a,b) => (a.match_status > b.match_status) ? 1 : ((b.match_status > a.match_status) ? -1 : 0));
	}
	else{ //current is asc, now chg to desc
		sort_type='desc'; 
		next_arrow='fa-sort-down';
		new_html= th_html.replace("fa-sort-up", "fa-sort-down");
		
		if (sort_column=='match_at')
			prediction_table_data.sort((a,b) => (a.match_at < b.match_at) ? 1 : ((b.match_at < a.match_at) ? -1 : 0));
		else if (sort_column=='match_status')
			prediction_table_data.sort((a,b) => (a.match_status < b.match_status) ? 1 : ((b.match_status < a.match_status) ? -1 : 0));
	}
	
	//////console.log(next_arrow + '-' + prediction_table_data);
	
	obj = {
		totalPage:global_totalPage,
		curr_page:curr_page_no,
		table_length:data_length,
		prediction_table_data:prediction_table_data
	};

	var html=$.get(mainHost+'cn/match-history-body.html',function (data) {
		 var render = template.compile(data);
		 var str = render(obj);

		 $('#match_history_table_body').html(str);
		
		 //////console.log(str);
		$("#"+th).html(new_html);//change arrow direction	
		
	});
}

function openEditorModal(event_id) {
	const promise = get_editor_prediction(event_id)
	promise.then(
		$("#editorModal").modal("show")
	)
}

