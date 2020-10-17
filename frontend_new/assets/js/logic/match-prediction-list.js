
var link=getBackendHost();
var search=0;//indicate whether user searched the table, for export csv
//////console.log(link);

function getCategoryDropdown(selected_id, parent_id, child_id = 0) {
  var selected = "";
  //var parent_id_to_pass='';
  var category_menu = "<option value=0>全部类别</option>";
  var filter_array = [
    {
      field: "parent_id",
      value: 0,
      operator: "=",
    },
  ];
  filter_array.push({
    field: "category.type",
    value: "sport",
    operator: "=",
  });
  $.ajax({
    type: "GET",
    url: link + "/api/cn/category",
    data: {
      filter: filter_array,
      sort: {
        field: "category.id",
        sort: "asc",
      },
      label: "getParentList",
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",
    success: function (response, status, xhr) {
      //////console.log(response);
      obj = response;
      ////console.log(obj.data);
      $.each(obj.data, function (index, value) {
        //////console.log(index+' -> '+value)
        if (value.id == selected_id) selected = "selected";
        else selected = "";
        category_menu +=
          "<option value=" +
          value.id +
          " " +
          selected +
          " >" +
          value.display +
          "</option>";
      });
      if (parent_id == 0)
        //if 0, pass 1 to get child menu
        parent_id = 1;
      $("#category_list").html(category_menu);
      getLeagueDropdown(parent_id, child_id);
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function getLeagueDropdown(category_id){
	var content='<option value="0" class="red">全部联赛</option>';
	var league_id;
	
	
	var filter_array = [{
        field: 'league.category_id',
        value: category_id,
        operator: '=',
    }];
	
	/*filter_array.push({
        field: 'has_event',
        value: 1,
        operator: '='
    });*/
	
	$.ajax({
        type: 'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/league',
		
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: {filter: filter_array},
		
        success: function (response, status, xhr) {
            //console.log(response);
			
            obj = response;
			//////console.log(obj.data);
			$.each(obj.data,function(index,value){
				//////console.log(value.name_zh);
				
				content+='<option value='+value.id+'>'+value.name_zh+'</option>';
			});
			
			$('#league_list').html(content);
			//$('#category_list').val(category_id);
			
        },
		
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function getPredictionListTable(pageNo, limit){
	
	var searchData = [];
	if(search == 1){
		searchData = filterEvent($('#main_form'), true);
	}
	
	$dataParams = {
        filter: searchData,
		page_number:pageNo,
		sort:{field:'event.id', sort:'desc'},
		limit:limit
    };
	
	$.ajax({
        type: 'GET',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/event',
        crossDomain: true,
        headers: getHeaders(),
        // contentType: "charset=utf-8",
        data: $dataParams,
        success: function (response, status, xhr) {
            //////console.log(response);
			
            obj = response;
			buildMainPageTable(obj);
			
			
        },
		
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
	
}


function filterEvent($form = "", returnflag = false) {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = [];

    $id = "";
    //indexed_array.push({field: "ended", value: 1, operator: "="});
    $.map(unindexed_array, function (n, i) {
        
		if (n['name']=='event.category_id' || n['name']=='event.league_id') { //category_id and league_id
			$field = n['name'];
            $value = n['value'];
            $operator = "=";
			
			if (n['value']>0){
				indexed_array.push({
					field: $field,
					value: $value,
					operator: $operator,
				});
				
			}
		}
		else{
			if (n['value']) {

				$field = n['name'];
				$value = n['value'];
				$operator = "=";

				if (n['name'].includes("_from")) { //match_at_from
					$field = $field.replace("_from", "");
					$operator = ">=";
					$value+=' 00:00:00'
				} else if (n['name'].includes("_to")) { //match_at_to
					$field = $field.replace("_to", "");
					$value+=' 23:59:59'
					$operator = "<=";
				} else if (n['name'].includes("_like")) { //not using
					$field = $field.replace("_like", "");
					$operator = "LIKE";
				} else if (n['name']=='category_id' || n['name']=='league_id'){
					if (n['value']==0)
						a=1;
					
				}
				
				indexed_array.push({
					field: $field,
					value: $value,
					operator: $operator,
				});
			}
		}
    });

    if (indexed_array['id']) { //id
        $id = indexed_array['id'];
        delete indexed_array['id'];
    }
	//////console.log(indexed_array);
	if(returnflag){
		return indexed_array;
	}else{
    	queryEvent(indexed_array, $id);
	}
}

function queryEvent($searchParams, $id = "") {
	//////console.log($searchParams);
    $dataParams = {
        filter: $searchParams
    };

    if ($id) {
        $dataParams['id'] = $id;
    }
	
	$dataParams['page_number'] = 1
	$dataParams['limit'] = $("#pagination_limit").val();

    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/event',
        data: $dataParams,

        crossDomain: true,
        headers: getHeaders(),

        success: function (response, status, xhr) {
            //alert("queryEvent - " + response.data.length);
			//////console.log(response.data);
			//////console.log(response.data.length);
			//////console.log(response);
			
            obj = response;
			
            buildMainPageTable(obj);
			msg='搜索成功！返回 '+response.data.length+' 条记录';
			showAlert(msg,'success',$(".message_output"));
			search=1;
          
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}




function buildMainPageTable(obj){
	
	var data = obj.data;
	
	//////console.log(data);
	//////console.log(data[0].winning_team.data.name_zh);
	var table_row='';
	var temp='';
	//var win_team='';
	
	var temp_handicap='';
	var temp_over_under='';
	var single_result='';
	
	var home_handicap='';
	var home_over_under='';

	var away_handicap='';
	var away_over_under='';

	var single_div='';
	
	$.each(data,function(index,value){
		////console.log(data[index]);
		//////console.log(data[index].result_data);
		
		if (data[index].result_data!=null){
			
			//HANDICAP让球
			home_handicap=data[index].handicap_home_bet+'/'+data[index].handicap_home_odds;
			away_handicap=data[index].handicap_away_bet+'/'+data[index].handicap_away_odds;
			
			//OVER UNDER大小
			home_over_under=data[index].over_under_home_bet+'/'+data[index].over_under_home_odds;
			away_over_under=data[index].over_under_away_bet+'/'+data[index].over_under_away_odds;
			
			//SINGLE独赢
			if (data[index].single_home==1){
				//single=home_team_name +' 主 ' + value.prediction_data.single_home ;
				single_result='主';
			}
			else if (data[index].single_away==1){
				//single=away_team_name + ' 客 ' + value.prediction_data.single_away ;
				single_result='客';
			}
			else if (data[index].single_tie==1){
				//single='和 ' + value.prediction_data.single_tie ;
				single_result='和 ';
			}
		}
		
		var league_data_name_zh='-';
		var home_team_data_name_zh='-';
		var away_team_data_name_zh='-';
		
		if (data[index].league_data!=null){
			if (data[index].league_data.name_zh!=null)
				league_data_name_zh=data[index].league_data.name_zh;
			
		}
		if (data[index].home_team_data!=null){
			if (data[index].home_team_data.name_zh!=null)
				home_team_data_name_zh=data[index].home_team_data.name_zh;
			
		}
		if (data[index].away_team_data!=null){
			if (data[index].away_team_data.name_zh!=null)
				away_team_data_name_zh=data[index].away_team_data.name_zh;
			
		}
		temp='<tr>';
		
		temp+='<td rowspan=2>' +data[index].id+'</td>';
		temp+='<td rowspan=2>' +data[index].created_at+'</td>';
		temp+='<td rowspan=2>' +data[index].match_at+'</td>';
		temp+='<td rowspan=2>' +league_data_name_zh+'</td>';
		
		temp+='<td  rowspan=2>'+data[index].category_data.display+'</td>';
		temp+='<td>'+home_team_data_name_zh+'</td>';
		temp+='<td>'+home_handicap+'</td>'; //HOME TEAM
		temp+='<td >'+home_over_under+'</td>'; //HOME TEAM
		
		
		//temp+='<td rowspan=2>'+single_result+'---</td>';
		
				
		if (data[index].category_data.display!='篮球'){
			single_div="<td rowspan=2 style='padding: 0px;'>"+
						"<div class='one-third'>主 - "+data[index].single_home+"</div><br>"+
						"<div class='one-third'>和 - "+data[index].single_tie+"</div><br>"+
						"<div class='one-third-last'>客 - "+data[index].single_away+"</div></td>";
			temp+=	single_div;
		}
		else
			temp+='<td >主 - '+data[index].single_home+'</td>'; 
		
		
		temp+='<td  rowspan=2> <a href="#.html" class="" data-toggle="modal" data-target="#prediction_member_list_modal"  onclick="getEventData('+data[index].id+');"> <i class="fa fa-search"></i>点击查看</a></td>';
		//temp+="<td  rowspan=2> <input type='checkbox' id='"+data[index].id+"' ></td>"; //this is 點擊索取
		
		
		temp+='</tr>';
		
		temp+="<tr>";
     	temp+='<td>'+away_team_data_name_zh+'</td>';//AWAY TEAM
		temp+='<td>'+away_handicap+'</td>';//AWAY TEAM
		temp+='<td>'+away_over_under+'</td>';//AWAY TEAM
		
		if (data[index].category_data.display=='篮球')
			temp+='<td >客 - '+data[index].single_away+'</td>'; 
		temp+='</tr>';
    
		
		
		table_row+=temp;
		
		temp='';
		home_handicap='';
		home_over_under='';
		
		away_handicap='';
		away_over_under='';
		single_result='';
		
		
	});
	
	/*table_row='<tr><td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td>';
	table_row+='<td>123</td></tr>';
	table_row+=table_row;*/
	
	$('#predictionlist_tbody').html(table_row);
	
	var pageNumber = parseInt(obj.pageNumber);
	var previous = pageNumber == 1 ? pageNumber : pageNumber - 1;
	var next = pageNumber == obj.totalPage ? pageNumber : pageNumber + 1;
	
	//<li class="link"><a href="#" class="prevLink">«</a></li>'
	
	var pagination_html = "<div>";
	pagination_html += "<span class='page-item "+(pageNumber == 1 ? 'disabled' : '')+"'><span class='page-link flaticon2-fast-back' style='font-size:0.6rem; color:#93a2dd; background:#f0f3ff; "+(pageNumber == 1 ? 'opacity:0.3;' : '')+"' onclick='getPredictionListTable(1, "+obj.numRecord+")'></span></span>";
	pagination_html += "<span class='page-item "+(pageNumber == 1 ? 'disabled' : '')+"'><span class='page-link flaticon2-back' style='font-size:0.6rem; color:#93a2dd; background:#f0f3ff; "+(pageNumber == 1 ? 'opacity:0.3;' : '')+"' onclick='getPredictionListTable("+previous+", "+obj.numRecord+")'></span></span>";
	for(var i = 1; i <= obj.totalPage; i++){
		//pagination_html += "<a class='page-link page-item' onclick='getPredictionListTable("+i+")' "+(pageNumber == i ? 'active' : '')+">"+i+"</a>";
		pagination_html += "<span class='page-item "+(pageNumber == i ? 'active' : '')+"'><span class='page-link' onclick='getPredictionListTable("+i+", "+obj.numRecord+")'>"+i+"</span></span>";
	}
	pagination_html += "<span class='page-item  "+((pageNumber == obj.totalPage || obj.totalRecord <= 0) ? 'disabled' : '')+"'><span class='page-link flaticon2-next' style='font-size:0.6rem; color:#93a2dd; background:#f0f3ff; "+((pageNumber == obj.totalPage || obj.totalRecord <= 0) ? 'opacity:0.3;' : '')+"'  onclick='getPredictionListTable("+next+", "+obj.numRecord+")'></span></span>";
	pagination_html += "<span class='page-item "+((pageNumber == obj.totalPage || obj.totalRecord <= 0) ? 'disabled' : '')+"'><span class='page-link flaticon2-fast-next' style='font-size:0.6rem; color:#93a2dd; background:#f0f3ff;  "+((pageNumber == obj.totalPage || obj.totalRecord <= 0) ? 'opacity:0.3;' : '')+"' onclick='getPredictionListTable("+obj.totalPage+", "+obj.numRecord+")'></span></span></div>";
	
	pagination_html += "<div>";
	pagination_html += "<select "+(obj.totalRecord <= 0 ? 'disabled' : '')+" style='margin-right:7px;' id='pagination_limit' onchange='getPredictionListTable(1, this.value)'>";
	pagination_html += "<option value='5' "+(obj.numRecord == 5 ? 'selected': '')+">5</option>";
	pagination_html += "<option value='10' "+(obj.numRecord == 10 ? 'selected': '')+">10</option>";
	pagination_html += "<option value='20' "+(obj.numRecord == 20 ? 'selected': '')+">20</option>";
	pagination_html += "<option value='30' "+(obj.numRecord == 30 ? 'selected': '')+">30</option>";
	pagination_html += "<option value='50' "+(obj.numRecord == 50 ? 'selected': '')+">50</option>";
	pagination_html += "<option value='100' "+(obj.numRecord == 100 ? 'selected': '')+">100</option>";
	pagination_html += "</select>";
	pagination_html += "<span class='page-info'>Showing "+obj.fromPage+" - "+(obj.toPage>0?obj.toPage:0)+" of "+(obj.totalRecord>0?obj.totalRecord:0)+"</span></div>";
	
	$("#prediction_pagination").attr("page_number", pageNumber);
	$("#prediction_pagination").html(pagination_html);
}

//get event and result data to display user list
function getEventData(event_id){
	
	var event_data;
	var prediction_data;
	var result_data;
	
	var filter_array = [{
        field: 'event.id',
        value: event_id,
        operator: '=',


    }];
	
		var getPrediction=$.ajax({
			type: 'GET',
			//url: getBackendHost() + $action,
			url: link + '/api/cn/prediction',
			data:{search:{event_id:event_id}},
			
			crossDomain: true,
			headers: getHeaders(),
			contentType: false,
			processData: true,
			// contentType: "charset=utf-8",
			
			
			success: function (response, status, xhr) {
				////console.log(response);
				
				obj = response;
				prediction_data=obj.data;
				//////console.log(event_data);
				//getPredictionUserList(event_id,event_data);
				
			},
			
			error: function () {
				showAlert("Problem occurred while sending request.", "danger", $error_selector);
			},
		});
		
		var getEvent=$.ajax({
			type: 'GET',
			//url: getBackendHost() + $action,
			url: link + '/api/cn/event',
			crossDomain: true,
			headers: getHeaders(),
			contentType: false,
			processData: true,
			// contentType: "charset=utf-8",
			data: {filter: filter_array},
			success: function (response, status, xhr) {
				//////console.log(response);
				
				obj = response;
				
				event_data=obj.data[0];
				
			},
			
			error: function () {
				showAlert("Problem occurred while sending request.", "danger", $error_selector);
			},
		});
		
		var getResult=$.ajax({
			type: 'GET',
			//url: getBackendHost() + $action,
			url: link + '/api/cn/result',
			data:{search:{event_id:event_id}},
			
			crossDomain: true,
			headers: getHeaders(),
			contentType: false,
			processData: true,
			// contentType: "charset=utf-8",
			
			
			success: function (response, status, xhr) {
				////console.log(response);
				
				
				obj = response;
				//result_data = obj.data; //wait dennis fix search event_id issue
				result_data = obj.data[0];
				
				
				//////console.log(result_data);
				//getPredictionUserList(event_id,event_data);
				
			},
			
			error: function () {alert('g');
				showAlert("Problem occurred while sending request.", "danger", $error_selector);
			},
		});
		
		 $.when(getPrediction,getEvent, getResult).done(function (getPrediction,getEvent, getResult) {
			 //////console.log(event_data);
			 //////console.log(result_data);
			 getPredictionUserList(event_id,prediction_data,result_data,event_data);
		 });
}

function getPredictionUserList(event_id,prediction_data,result_data,event_data){
		////console.log(event_id);
		////console.log(result_data);
		////console.log(prediction_data);
		//console.log(event_id);
		var wincolor='#18f500';
		var losecolor='#ff3030';
		var filter_array = [{
			field: 'event_id',
			value: event_id,
			operator: '=',
		}];

		filter_array.push({
	        field: 'prediction.status',
	        value: 'predicted',
	        operator: '='
		});
		
		var table='<table style="width: 100%;"><tr><th>会员账号</th><th>预测时间</th><th>让球</th><th>大小</th><th>独赢</th><th>总获得积分</th></tr>';
		$('#predict_user_datatable').html('Loading')
		 //table+='<tr><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td></tr></table>';
		//datatable = $('#predict_user_datatable').KTDatatable({});
		
		$.ajax({
			type:  'GET',
			//url: getBackendHost() + $action,
			url: link + '/api/cn/prediction',
			crossDomain: true,
			headers: getHeaders(),
			contentType: false,
			processData: true,
			// contentType: "charset=utf-8",
			
			data: {	filter: filter_array},
			success: function (response, status, xhr) {
				//console.log(response);

				obj = response;
				
				if (obj.data.length==0)
					table+='<tr><td colspan=6>无记录</td></tr>';
				else{
					$.each(obj.data,function(index,value){
						
						var handicap=get_handicap(obj.data[index],event_data,wincolor,losecolor);
						var over_under=get_over_under(obj.data[index],event_data,wincolor,losecolor);
						var single=get_single(obj.data[index],event_data,wincolor,losecolor);
						
						//else
							//handicap='logic error'; //one of handicap_home or handicap_away must be with value 1
						//console.log(index,value);
						table+='<tr>';
						table+='<td>'+value.user_data.username+'</td>';
						table+='<td>'+value.created_at+'</td>';
						table+='<td>'+handicap+'</td>';
						table+='<td>'+over_under+'</td>';
						table+='<td>'+single+'</td>';
						table+='<td>'+value.win_amount+'</td>';
						table+='</tr>';
					});
				}
				table+='</table>';
				//console.log(table);
				$('#predict_user_datatable').html(table)
				//get_sub_sport_dropdown(selected_id);
			},

			error: function () {
			   alert('AJAX ERROR - get main category');
			},
		});
    
	
}

//get handicap of each user after click main table's 查看
function get_handicap(data,event_data,wincolor,losecolor){
	var handicap='';
	var bgcolor='';
	var user_prediction='';
	var win_team='';
	var over_under='';
	
	var handicap_home_bet=event_data.handicap_home_bet;
	var handicap_home_odds=event_data.handicap_home_odds;
	var handicap_away_bet=event_data.handicap_away_bet;
	var handicap_away_odds=event_data.handicap_away_odds;
	
	////console.log(data)
	
	if (data.handicap_win==1)
		bgcolor='background-color:'+wincolor;//green
	else
		bgcolor='background-color:'+losecolor;//green
	
	if (data.handicap_home==1){ //if user bet for home in handicap 
		handicap=handicap_home_bet+'/'+handicap_home_odds; //exp: -0.5/1.00
		
	}
	else if (data.handicap_away==1){ //if user bet for away in handicap 
		handicap=handicap_away_bet+'/'+handicap_away_odds;
	}
	
	output ='<div style='+bgcolor+'>' + handicap +	'</div>';
	
	return output;
}

//get over under of each user after click main table's 查看
function get_over_under(data,event_data,wincolor,losecolor){
	var over_under='';
	var bgcolor='';
	var win_team='';

	var over_under_home_bet=event_data.over_under_home_bet;
	var over_under_home_odds=event_data.over_under_home_odds;
	var over_under_away_bet=event_data.over_under_away_bet;
	var over_under_away_odds=event_data.over_under_away_odds;
	
	/*
	if (result_data.over_under_home==1)
		win_team='home';
	else if (result_data.over_under_away==1)
		win_team='away';
	else
		win_team='Logic error';//over_under_home and over_under_away must be one 1 one 0
	*/
	
	if (data.over_under_win==1)
		bgcolor='background-color:'+wincolor;//green
	else
		bgcolor='background-color:'+losecolor;//red
	
	if (data.over_under_home==1){ //if user bet for home in over under 
		over_under=over_under_home_bet+'/'+over_under_home_odds; //exp: -0.5/1.00
		
	}
	else if (data.over_under_away==1){ //if user bet for away in over under 
		over_under=over_under_away_bet+'/'+over_under_away_odds;
	}
	
	output = '' +
		'<div style='+bgcolor+'>' +over_under +
		'</div>';

	return output;
}

//get single of each user after click main table's 查看
function get_single(data,event_data,wincolor,losecolor){
	var single='';
	var single_home=event_data.single_home;
	var single_away=event_data.single_away;
	var single_tie=event_data.single_tie;

	
	if (data.single_win==1)
		bgcolor='background-color:'+wincolor;//green
	else
		bgcolor='background-color:'+losecolor;//red
	
	if (data.single_home==1){
		//single=home_team_name +' 主 ' + value.event_data.single_home ;
		single='主 - '+single_home;
	}
	else if (data.single_away==1){
		//single=away_team_name + ' 客 ' + value.event_data.single_away ;
		single='客 - '+single_away;
	}
	else if (data.single_tie==1){
		//single='和 ' + value.event_data.single_tie ;
		single='和 - '+single_tie
	}
	
	output = '' +
		'<div style='+bgcolor+'>' +single +
		'</div>';

	return output;
}

function refreshPredictionPointTable(){
	var temp;
	$.ajax({
        type: 'GET',
        //url: getBackendHost() + $action,
        url: link +'/api/cn/prediction_points',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        //data: formData,
        success: function (response, status, xhr) {
            ////console.log(response);

			obj = response;
			////console.log(obj);
			
			$.each(obj.data,function(index,value){
				temp=parseInt(value.points) 
				$('input[name="'+value.id+'"]').val(temp);
			   ////console.log(value.id)
			   ////console.log(value.points)
			});
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function SavePredictionPoint($form){
	var json_form_obj = {
        "data": []
    };
	var subdata = {};
	$form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

   // //console.log("action : " + $action);
   // //console.log("method : " + $method);
   // //console.log("redirect : " + $redirect_uri);

	
    $('#prediction_point_form').find(':input').each(function (key, value) {        
		////console.log(key+'->'+$(this).attr("name")+'->'+$(this).val());
		
		subdata['id']=$(this).attr("name");
		subdata['points']=$(this).val();
		json_form_obj.data.push(subdata);
		 subdata = {};
       /* subdata['id'] = $(this).attr('id');
        subdata['disabled'] = disabled;
        ////console.log(subdata);
        json_form_obj.data.push(subdata);
        subdata = {};//reset subdata for new row
           */
    });
	 var formData = JSON.stringify(json_form_obj);
    ////console.log(formData);	
	
	$.ajax({
        type: ($method) ? $method : 'PATCH',
        //url: getBackendHost() + $action,
        url: link + $action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
            ////console.log(response);

            obj = response;

            if (obj.code == 1) {
				refreshPredictionPointTable();
				 showAlert("成功儲存", "success", $(".modal-message_output"));
                //if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + redirectMsg);
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $error_selector);
            }
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function export_csv(){
	
	var param='';
	if (search==1){ //if user searched the table
		var unindexed_array = $('#main_form').serializeArray();
		var indexed_array = [];
		
		var temp='';
		var id = "";
		var count=0;
		
		$.map(unindexed_array, function (n, i) {
			
			if (n['name']=='event.category_id' || n['name']=='event.league_id') { //category_id and league_id
				$field = n['name'];
				$value = n['value'];
				$operator = "=";
				
				if (n['value']>0){
					indexed_array.push({
						field: $field,
						value: $value,
						operator: $operator,
					});
					
					param+='&filter['+count+'][field]='+$field;
					param+='&filter['+count+'][value]='+$value;
					param+='&filter['+count+'][operator]='+$operator;
					count++;
				}
			}
			else{
				if (n['value']) {

					$field = n['name'];
					$value = n['value'];
					$operator = "=";

					if (n['name'].includes("_from")) { //match_at_from
						$field = $field.replace("_from", "");
						$operator = ">=";
						$value+=' 00:00:00'
					} else if (n['name'].includes("_to")) { //match_at_to
						$field = $field.replace("_to", "");
						$value+=' 23:59:59'
						$operator = "<=";
					} else if (n['name'].includes("_like")) { //not using
						$field = $field.replace("_like", "");
						$operator = "LIKE";
					} else if (n['name']=='category_id' || n['name']=='league_id'){
						if (n['value']==0)
							a=1;
						
					}
					
					indexed_array.push({
						field: $field,
						value: $value,
						operator: $operator,
					});
					
					param+='&filter['+count+'][field]='+$field;
					param+='&filter['+count+'][value]='+$value;
					param+='&filter['+count+'][operator]='+$operator;
					count++;
				}
			}
			
			//temp=$field + '=' + $value+'&';
			//param=param+temp;
			//filter[0][field]=match_at&filter[0][value]=2020-07-01&filter[0][operator]=>
			////console.log(temp);
		});
		////console.log(param)
		
	}
	
   param=param.substr(1);
	//console.log(param)
	
	
	window.open('/exportcsv.php?'+param);
}

function export_csv_old(){
	
	var table_content=$('#predictionlist_table').html();
	//var table_content = table_content.replace(/blue/g, "red");
	
	export2csv('predictionlist_table');
	
	//$_h = ['编号','创建日期时间','比赛日期时间','联赛名称','体育类别','队伍','让球','大小','独赢'];
	//$_d = [['1','2','3'],['4','5','6']];
	//export2csv($_h, $_d);
	
		/*let file = new Blob([$('#predictionlist_table').html()], {type:"application/vnd.ms-excel"});
		let url = URL.createObjectURL(file);
		let a = $("<a />", {
		  href: url,
		  download: "filename.xls"}).appendTo("body").get(0).click();
		  e.preventDefault();*/
}
