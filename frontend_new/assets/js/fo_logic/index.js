//get member detail for profile.html

function get_fans_area(){
	 $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/fan_zone',
       
		//data:  {id: user_id},
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",
      
		success: function (response, status, xhr) {
			//////console.log(response);
			var obj = response;
		
			//////console.log(user_data);
			
            obj = {
                fans_area_data:obj.data
            };

			//////console.log(obj);
            var html=$.get(mainHost+'cn/homepage/index-fans-area.html',function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#fans_area').html(str);
           
				
            });
        },
		error: function () {
            alert('AJAX ERROR - get index fans area');
        },
    });
}


//match prediction carousel in index.html
function match_prediction_carousel(){
	 
	//category -> sub-category events, filter by 'ended' = 0, sort match_at ASC
	/*var filter_array=[{
					field: 'ended',
					value: '0',
					operator: '=',
				}];*/
	var  filter_array=[
                {
                    field: 'ended',
                    value: 0,
                    operator: '='
                }/*,
                {
                    field: 'prediction_end_at',
                    value: getCurrentDateTime(),
                    operator: '>' 
                }*/
            ]
	
   $.ajax({
        url:getBackendHost() + '/api/cn/event',
		
		data: {
            sort: {
                field: "match_at",
                sort: "asc"
            },
			
			filter:filter_array
        },
        type:'get',
      
      
		success: function (response, status, xhr) {
			////console.log(response);
			var data = response.data;
			var data_length=data.length;
			var total_page=Math.ceil(data_length/2);
			
			//////console.log(Math.ceil(data_length/2));
			////console.log(data_length)
			var arr=[];
			var sub = [];
			var data_arr=[];
			var temp=[];
			var count=0;
			
			if (data_length<6){
				
				$.each(data, function(index, key) {
					temp=[];
					//temp['chatroom_id'] = data[index].chatroom_id;
					temp['event_id'] = data[index].id;
					temp['home_name'] = data[index].home_team_data.name_zh;
					temp['away_name'] = data[index].away_team_data.name_zh;
					temp['league_name'] = data[index].league_data.name_zh;
					temp['match_at'] = data[index].match_at;
					
					
					if (data[index].home_team_upload_data)
						temp['home_logo_url'] = "/" + data[index].home_team_upload_data.url;
					else
						temp['home_logo_url'] = '/assets/images/default_no_image.png';
					
					
					if (data[index].away_team_upload_data)
						temp['away_logo_url'] = "/" + data[index].away_team_upload_data.url;
					else
						temp['away_logo_url'] = '/assets/images/default_no_image.png';
					
					data_arr[count]=temp;
					
					arr.push(temp);
				});
				
				////console.log(arr)
				
				obj = {
					total_page:data_length,
					match_prediction_data:arr,
					data_length:data_length
				};

				////console.log(obj)
			}
			
			else if (data_length>=6){
				//extract essential data and store all into sub array
				$.each(data, function(index, key) {
					//////console.log(data[index]);
					temp=[];
					//temp['chatroom_id'] = data[index].chatroom_id;
					temp['event_id'] = data[index].id;
					temp['home_name'] = data[index].home_team_data.name_zh;
					temp['away_name'] = data[index].away_team_data.name_zh;
					temp['league_name'] = data[index].league_data.name_zh;
					temp['match_at'] = data[index].match_at;
					
					
					if (data[index].home_team_upload_data)
						temp['home_logo_url'] = "/" + data[index].home_team_upload_data.url;
					else
						temp['home_logo_url'] = '/assets/images/default_no_image.png';
					
					
					if (data[index].away_team_upload_data)
						temp['away_logo_url'] = "/" + data[index].away_team_upload_data.url;
					else
						temp['away_logo_url'] = '/assets/images/default_no_image.png';
					
					data_arr[count]=temp;
					
					/*if (index>0 && isEven(count)==true){ //2,4,6,8,10
						////console.log('yes')
						arr.push(sub);
						sub=[];
						sub.push(temp);
						
					}
					
					else{ //0,1,3,5,7,9
						sub.push(temp);
					}*/
					
					count++;
				});
				////console.log(data_arr)
				
				count=1;
				index_arr=[];
				
				$.each(data_arr, function(index, key) {
					
					sub.push(data_arr[index])
					
					if (sub.length==2){
						arr.push(sub);
						sub=[];
					}
					
				});
				
				//if odd number
				if (sub.length>0){
					arr.push(sub);
					sub=[];
				}
				
				
				//insert last element if total row is odd number
				/*if (isEven(data_length)==false){	
					//////console.log(sub)
					//var last_element = data_arr[data_arr.length - 1];
					//////console.log(last_element)
					
					arr.push(sub);
				}
				
				if (data_length==2)//if only 2 records(1 array)
					arr.push(sub);*/
				
				////console.log(arr)
				
				
				obj = {
					total_page:total_page,
					match_prediction_data:arr,
					data_length:data_length
				};

				////console.log(obj)
				
			}
			
			var html=$.get(mainHost+'cn/homepage/match-prediction-carousel.html',function (data) {
					var render = template.compile(data);
					var str = render(obj);

					$('#match_prediction_carousel').html(str);
					
					checker(arr);
					//////console.log(str)
					//alert(str);
					//menu_right();
				});
        },
		error: function () {
            alert('AJAX ERROR - get carousel');
        },
    });
}

//check if match carousel display or not
function checker(arr){
	//////console.log(arr)
	var match_carousel_checker=$('.exist_checker').html();
		
	if (match_carousel_checker==null && arr.length>0){
		//////console.log('fxk');
		match_prediction_carousel();
	}
	//alert(abc.length)
	
	//$.trim($('#swiper-wrapper').html()).length
}

//--get shooter and ranking begins
var filterByEPLCurrent = [
  {
    field: 'league_id',
    value: 1423,
    operator: "="
	},
	{
    field: 'current',
    value: 1,
    operator: "="
  }
]
var filterByNBACurrent = [
  {
    field: 'league_id',
    value: 1,
    operator: "="
	},
	{
    field: 'current',
    value: 1,
    operator: "="
  }
]

function getSeasonIdForRankShooter() {
	var getEPLSeasonId =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_list',
			data: {
				filter: filterByEPLCurrent,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
			success: function (data) {
				var obj = {
					getEPLSeasonId: data['data']
				};
			},
			error: function () {
				alert('AJAX ERROR - get index season id EPL');
			},
	});
	var getNBASeasonId =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_list',
			data: {
				filter: filterByNBACurrent,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
			success: function (data) {
				var obj = {
					getNBASeasonId: data['data']
				};
			},
			error: function () {
				alert('AJAX ERROR - get index season id NBA');
			},
	});
	$.when(getEPLSeasonId, getNBASeasonId).done(function (getEPLSeasonId, getNBASeasonId) {
		var EPLSeasonId = getEPLSeasonId[0]['data'][0]['season_id'];
		var NBASeasonId = getNBASeasonId[0]['data'][0]['season_id'];
		getShooterRanking(EPLSeasonId,NBASeasonId)
	});
}
var sortForRanking = [
  {
    field: "position",
    sort: "asc"
  }
]

var sortForEPLShooter = [
  {
    field: "rating",
    sort: "desc"
  }
]

var sortForNBAShooter = [
  {
    field: "points",
    sort: "desc"
  }
]

function getShooterRanking(EPLSeasonId, NBASeasonId) {
	var filterByEPL = [
		{
			field: 'season_id',
			value: EPLSeasonId,
			operator: "="
		}
	]
	var filterByNBA = [
		{
			field: 'season_id',
			value: NBASeasonId,
			operator: "="
		},
		{
			field: 'scope',
			value: 6,
			operator: "="
		}
	]
	var soccerRankingList =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_ranking_soccer',
			data: {
				filter: filterByEPL,
				sort: sortForRanking,
				limit:10,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
			success: function (data) {
				var obj = {
						soccerRankingList: data['data']
				};
			},
			error: function () {
				alert('AJAX ERROR - get index soccer ranking list');
			},
	});

	var soccerShooterList =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_player_stats_soccer',
			data: {
				filter: filterByEPL,
				sort: sortForEPLShooter,
				limit:10,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
			success: function (data) {
				var obj = {
						soccerShooterList: data['data']
				};
			},
			error: function () {
				alert('AJAX ERROR - get index soccer shooter list');
			},
	});

	var basketballRankListing =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_ranking_basketball',
			data: {
				filter: filterByNBA,
				sort: sortForRanking,
				limit:10,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
			}).then(response => {
				if (response.data < 1) {
					var filter = [
						{
							field: 'season_id',
							value: NBASeasonId,
							operator: "="
						},
						{
							field: 'scope',
							value: 5,
							operator: "="
						}
					]
					return $.ajax({
						url:getBackendHost() + '/api/cn/season_ranking_basketball',
						data: {
							filter: filter,
							sort: sortForRanking,
							limit:10,
						},
						type:'GET',
						headers: getHeaders(),
						contentType: false,
					}).then(response => response.data)
				}else {
					return response.data
				}
	});
	

	var basketballShooterListing =
	$.ajax({
			url:getBackendHost() + '/api/cn/season_player_stats_basketball',
			data: {
				filter: filterByNBA,
				sort: sortForNBAShooter,
				limit:10,
			},
			type:'GET',
			headers: getHeaders(),
			contentType: false,
	}).then(response => {
		if (response.data < 1) {
			var filter = [
				{
					field: 'season_id',
					value: NBASeasonId,
					operator: "="
				},
				{
					field: 'scope',
					value: 5,
					operator: "="
				}
			]
			return $.ajax({
				url:getBackendHost() + '/api/cn/season_player_stats_basketball',
				data: {
					filter: filter,
					sort: sortForNBAShooter,
					limit:10,
				},
				type:'GET',
				headers: getHeaders(),
				contentType: false,
			}).then(response => response.data)
		}else {
			return response.data
		}
	})

	$.when(soccerRankingList, soccerShooterList, basketballRankListing, basketballShooterListing).done(function (soccerRankingList, soccerShooterList, basketballRankListing, basketballShooterListing) {
			obj = [];
			obj['soccerRankingList'] = soccerRankingList[0]['data'];
			obj['soccerShooterList'] = soccerShooterList[0]['data'];
			obj['basketballRankListing'] = basketballRankListing;
			obj['basketballShooterListing'] = basketballShooterListing;
			var html = $.get('/cn/homepage/ranking-shooter.html', function (data) {
					var render = template.compile(data);
					var strSub = render(obj);
					$('#shooter-ranking-table').html(strSub);
			});
	});
}

//--get shooter and ranking ends