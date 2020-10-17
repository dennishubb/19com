var search_event=0;//indicate whether user searched the table, for export csv
var search_prediction=0;//indicate whether user searched the table, for export csv

function getDataList() {
    queryEvent([],'',true);
    queryPrediction([],'',true);
}

function updateLeague($obj) {
    $('#select_league,#select_league_2').html('');
    $('#select_league,#select_league_2').selectpicker("refresh");

    $('#select_league,#select_league_2').val('0').selectpicker("refresh");

    var category_id = $($obj).val();

    queryLeague([
        {
            field: 'league.category_id',
            value: category_id,
            operator: '=',
        }
    ]);
}

function queryLeague($searchParams = []) {
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/league',
        data: {
            filter: $searchParams
        },

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,

        success: function (response, status, xhr) {
            //console.log("queryLeague - " + response.data.length);
            obj = response;

            buildLeagueSelect(response.data);
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function buildLeagueSelect($response_data) {
    $("#select_league,#select_league_2").html('');
    $("#select_league,#select_league_2").append(
        $(`<option value="">-</option>`)
    );
    $.each($response_data, function (key, entry) {
        $("#select_league,#select_league_2").append(
            $('<option></option>')
                .attr('value', entry.id)
                .text(entry.name_zh)
        );
    });
    $('#select_league,#select_league_2').selectpicker('refresh');
}

function clearEventForm($form = "") {
    $($form).resetForm();
    $('.selectpicker').selectpicker('refresh');

    filterEvent('');
}

function clearPredictionForm($form = "") {
    $($form).resetForm();
    $('.selectpicker').selectpicker('refresh');

    filterPrediction('');
}

function filterEvent($form = "") {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = [];

    $id = "";

    indexed_array.push({field: "ended", value: 1, operator: "="});
    $.map(unindexed_array, function (n, i) {
        if (n['value']) {

            $field = n['name'];
            $value = n['value'];
            $operator = "=";

            if (n['name'].includes("_from")) {
                $field = $field.replace("_from", "");
                $operator = ">=";
            } else if (n['name'].includes("_to")) {
                $field = $field.replace("_to", "");
                $operator = "<=";
            } else if (n['name'].includes("_like")) {
                $field = $field.replace("_like", "");
                $value = "%" + n['value'] + "%";
                $operator = "LIKE";
            }

            indexed_array.push({
                field: $field,
                value: $value,
                operator: $operator,
            });
        }
    });

    //console.log("filterEvent : " + JSON.stringify(indexed_array));

    if (indexed_array['id']) {
        $id = indexed_array['id'];
        delete indexed_array['id'];
    }

    queryEvent(indexed_array, $id);
}

function queryEvent($searchParams = [{field: "ended", value: 1, operator: "="}], $id = "",$default=false) {

    $dataParams = {
        filter: $searchParams,
        sort: {
            field: "prediction_end_at",
            sort: "asc"
        }
    };

    if ($id) {
        $dataParams['id'] = $id;
    }

    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/event',
        data: $dataParams,

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,

        success: function (response, status, xhr) {
            ////console.log("queryEvent - " + response.data);
            obj = response;
//console.log($dataParams)
//console.log(obj.data)
            $fmt_data = [];
            if (!Array.isArray(obj.data)) {
                $fmt_data.push(obj.data);
            } else {
                $fmt_data = obj.data
            }
		
            buildMainPageTable($fmt_data);
			
			if ($default==false)
				search_event=1;
            // getUserList(event_id = 0);
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function filterPrediction($form = "") {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = [];

    $id = "";

    $.map(unindexed_array, function (n, i) {
        if (n['value']) {

            $field = n['name'];
            $value = n['value'];
            $operator = "=";

            if (n['name'].includes("_from")) {
                $field = $field.replace("_from", "");
                $operator = ">=";
            } else if (n['name'].includes("_to")) {
                $field = $field.replace("_to", "");
                $operator = "<=";
            } else if (n['name'].includes("_like")) {
                $field = $field.replace("_like", "");
                $value = "%" + n['value'] + "%";
                $operator = "LIKE";
            }

            indexed_array.push({
                field: $field,
                value: $value,
                operator: $operator,
            });
        }
    });

    //console.log("filterPrediction : " + JSON.stringify(indexed_array));

    if (indexed_array['id']) {
        $id = indexed_array['id'];
        delete indexed_array['id'];
    }

    queryPrediction(indexed_array, $id);
}

function queryPrediction($searchParams = [], $id = "",$default=false) {

    $dataParams = {
        filter: $searchParams,
        sort: {
            field: "prediction.created_at",
            sort: "desc"
        }
    };

    if ($id) {
        $dataParams['id'] = $id;
    }

    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/prediction',
        data: $dataParams,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,

        success: function (response, status, xhr) {
            //console.log("queryPrediction - " + response.data);
            obj = response;

            $fmt_data = [];
            if (!Array.isArray(obj.data)) {
                $fmt_data.push(obj.data);
            } else {
                $fmt_data = obj.data
            }
            buildSubPageTable($fmt_data);
			
			if ($default==false)
				search_prediction=1;
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function queryUserPrediction($searchParams = [], $id = "") {

    $dataParams = {
        filter: $searchParams,
        sort: {
            field: "prediction.created_at",
            sort: "desc"
        }
    };

    if ($id) {
        $dataParams['id'] = $id;
    }

    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/prediction',
        data: $dataParams,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,

        success: function (response, status, xhr) {
            //console.log("queryPrediction - " + response.data);
            obj = response;

            $fmt_data = [];
            if (!Array.isArray(obj.data)) {
                $fmt_data.push(obj.data);
            } else {
                $fmt_data = obj.data
            }

            buildModalTable($fmt_data);
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function buildMainPageTable(data) {

    //console.log('data- ',data);
    var table_data = '';
    var row = '';


    var temp_handicap = '';
    var temp_over_under = '';

    var single_result = '';
    var home_handicap = '';
    var home_over_under = '';
    var away_handicap = '';
    var away_over_under = '';

    $.each(data, function (index, entry) {
        single_result = '';
        home_handicap = '';
        home_over_under = '';
        away_handicap = '';
        away_over_under = '';

        if (!entry.result_data) {
            return;
        }

        temp_handicap = entry.result_data.handicap_odds + '/' + entry.result_data.handicap_bet; //exp: -0.5/1.00
        temp_over_under = entry.result_data.over_under_odds + '/' + entry.result_data.over_under_bet; //exp: 大2.5/2.00


        if (entry.result_data.handicap_home == 1) {
            home_handicap = temp_handicap;
        } else if (entry.result_data.handicap_away == 1) {
            away_handicap = temp_handicap;
        }

        if (entry.result_data.over_under_home == 1) {
            home_over_under = temp_over_under;
        } else if (entry.result_data.over_under_away == 1) {
            away_over_under = temp_over_under;
        }

        if (entry.result_data.single_home == 1) {
            single_result = '主';
        } else if (entry.result_data.single_away == 1) {
            single_result = '客';
        }

        row = '';
        row += '<tr>';

        row += '<td rowspan="2" class="text-center">' + entry.id + '</td>';
        row += '<td rowspan="2">' + entry.created_at + '</td>';
        row += '<td rowspan="2">' + entry.match_at + '</td>';
        row += '<td rowspan="2">' + entry.prediction_end_at + '</td>';
        row += '<td rowspan="2" class="text-center">' + entry.league_data.name_zh + entry.round + '</td>';
        row += '<td rowspan="2" class="text-center">' + entry.category_data.display + '</td>';

        var win_data = entry.winning_team_data;
        if (win_data) {
            row += '<td class="text-center"> ' + entry.home_team_data.name_zh + ' </td>';
            row += '<td class="text-center"> ' + home_handicap + ' </td>';
            row += '<td class="text-center"> ' + home_over_under + ' </td>';
            row += '<td rowspan="2" class="text-center"> ' + single_result + ' </td>';
        } else {
            row += '<td class="text-center"> ' + entry.home_team_data.name_zh + ' </td>';
            row += '<td class="text-center"> ' + home_handicap + ' </td>';
            row += '<td class="text-center"> ' + home_over_under + ' </td>';
            row += '<td rowspan="2" class="text-center"> ' + single_result + ' </td>';
        }

        var element = $(`<div>${entry.editor_note}</div>`);
        element.find('img').remove();

        row += '<td rowspan="2" class="text-center"> ' + element.html() + ' </td>';

        if (entry.prediction_count) {
            row += '<td rowspan="2" class="text-center">';
            row += '<a href="javascript:getUserData(' + entry.id + ');">';
            row += entry.prediction_count;
            row += '</a>';
            row += '</td>';
        } else {
            row += '<td rowspan="2" class="text-center">';
            row += '<a href="javascript:getUserData(' + entry.id + ');">';
            row += '0';
            row += '</a>';
            row += '</td>';
            // row += '<td rowspan="2" class="text-center"> 0 </td>';
        }

        if (entry.prediction_win_count) {
            row += '<td rowspan="2" class="text-center">';
            row += '<a href="javascript:getUserData(' + entry.id + ',1);">';
            row += entry.prediction_win_count;
            row += '</a>';
            row += '</td>';
        } else {
            row += '<td rowspan="2" class="text-center"> - </td>';
        }

        if (entry.bonus_distribution) {
            row += '<td rowspan="2" class="text-center"> ' + entry.bonus_distribution + ' </td>';
        } else {
            row += '<td rowspan="2" class="text-center"> - </td>';
        }

        if (entry.comment_count) {
            row += '<td rowspan="2" class="text-center"> ' + entry.comment_count + ' </td>';
        } else {
            row += '<td rowspan="2" class="text-center"> - </td>';
        }

        row += '</tr>';


        row += '<tr>';
        row += '<td class="text-center"> ' + entry.away_team_data.name_zh + ' </td>';
        row += '<td class="text-center"> ' + away_handicap + ' </td>';
        row += '<td class="text-center"> ' + away_over_under + ' </td>';
        row += '</tr>';


        table_data += row;

    });

    $('#prediction_history_tbody').html(table_data);
}

function buildSubPageTable(data) {
    var table_data = '';
    var row = '';
    $.each(data, function (index, entry) {
        handicap = 'X';
        over_under = 'X';
        single = 'X';

		if (entry.handicap_home == 1 || entry.handicap_away == 1) {
			handicap = 'O';
		}
		
        if (entry.over_under_home == 1 || entry.over_under_away == 1) {
            over_under = 'O';
        }

        if (entry.single_home == 1 || entry.single_away == 1 || entry.single_tie == 1) {
            single = 'O';
        }

        row = '';

        row += '<tr>';
        row += '<td class="text-center">' + entry.id + '</td>';
        if (entry.category_data)
        row += '<td class="text-center">' + entry.category_data.display + '</td>';
        else
        row += '<td class="text-center"></td>';
        if (entry.league_data && entry.event_data)
        row += '<td class="text-center">' + entry.league_data.name_zh + ' ' + entry.event_data.round + '</td>';
        else
        row += '<td class="text-center"></td>';
        row += '<td class="text-center">' + entry.user_data.username + '</td>';
        row += '<td class="text-center">' + entry.created_at + '</td>';
        row += '<td class="text-center font-weight-bold">' + handicap + '</td>';
        row += '<td class="text-center font-weight-bold">' + over_under + '</td>';
        row += '<td class="text-center font-weight-bold">' + single + '</td>';
        row += '<td class="text-center">' + entry.win_amount + '</td>';
        row += '</tr>';


        table_data += row;

    });

    $('#user_prediction_history_tbody').html('');
    $('#user_prediction_history_tbody').html(table_data);
}

function buildModalTable(data) {

    var wincolor = '#18f500';
    var losecolor = '#ff3030';

    //console.log(data);
    var table_data = '';
    var row = '';

    $.each(data, function (index, entry) {
        row = '';

        handicap = 'X';
        over_under = 'X';
        single = 'X';

        if (entry.handicap_home == 1 || entry.handicap_away == 1) {
            handicap = 'O';
        }

        if (entry.over_under_home == 1 || entry.over_under_away == 1) {
            over_under = 'O';
        }

        if (entry.single_home == 1 || entry.single_away == 1 || entry.single_tie == 1) {
            single = 'O';
        }

        row += '<tr>';
        row += '<td class="text-center">';
        row += '<a href="javascript:void(0);" onclick="showUserHistory(`username`,`' + entry.user_data.username + '`);">';
        row += entry.user_data.username;
        row += '</a>';
        row += '</td>';
        row += '<td class="text-center">' + entry.created_at + '</td>';
        row += '<td class="text-center">' + handicap + '</td>';
        row += '<td class="text-center">' + over_under + '</td>';
        row += '<td class="text-center">' + single + '</td>';
        row += '<td class="text-center">' + entry.win_amount + '</td>';
        row += '</tr>';


        table_data += row;
    });

    $('#user_modal_history_tbody').html('');
    $('#user_modal_history_tbody').html(table_data);
}

function getUserData(event_id, win = "") {
    //console.log("getUserData(" + event_id + ")");

    // $('.nav-tabs a[href="#tab_2"]').tab('show');
    // getUserList(event_id);

    $('#prediction_list_modal').modal('show');
    getUserList(event_id, win);
}


function showUserHistory(key, value) {

    //console.log(key + " | " + value);

    $params = [{field: key, value: value, operator: '='}];

    $.when(queryPrediction($params)).done(function (i) {
        $('#prediction_list_modal').modal('hide');
        $('.nav-tabs a[href="#tab_2"]').tab('show');
    });
}


function getUserList(event_id = 0, win = "") {

    //console.log("getUserList(" + event_id + " | " + win + ")");

    var searchObj = [];

    if (event_id != 0) {
        //console.log("event_id != 0");
        searchObj.push({
            field: 'event_id',
            value: event_id,
            operator: '=',
        });
    }

    if (win) {
        //console.log("win - true");
        searchObj.push({
            field: 'win',
            value: 1,
            operator: '=',
        });
    }

    queryUserPrediction(searchObj);

}

function export_csv(){
	
	////console.log(type+'->'+search_event)
	////console.log(type+'->'+search_prediction)
	
	var param='';
	var form_name='';
	if (search_event==1){ //if user searched the table
		
		var unindexed_array = $('#search_event_form').serializeArray();
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
				if (n['value']) {alert();

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
					} else if (n['name'].includes("_like")) {
						$field = $field.replace("_like", "");
						$value = "%" + n['value'] + "%";
						$operator = "LIKE";
					}else if (n['name']=='category_id' || n['name']=='league_id'){
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
	
	
	
	var sort='sort[field]=prediction_end_at&sort[sort]=asc';
	window.open('/exportcsv-match-history.php?type=match&param='+sort+'&'+param);
	
	
}

function export_csv_member_history($form = ""){
	
	////console.log(type+'->'+search_event)
	////console.log(type+'->'+search_prediction)
	
	var param='';
	var form_name='';
	if (search_prediction==1){ //if user searched the table
		
		var unindexed_array = $($form).serializeArray();
		var indexed_array = [];

		var temp='';
		var count=0;
		
		$.map(unindexed_array, function (n, i) {////console.log(n,i);
			
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
					} else if (n['name'].includes("_like")) {
						$field = $field.replace("_like", "");
						$value = "%" + n['value'] + "%";
						$operator = "LIKE";
					}else if (n['name']=='category_id' || n['name']=='league_id'){
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
	//console.log('csv prediction ->'+param)
	
	
	
	var sort='sort[field]=prediction.created_at&sort[sort]=desc';
	window.open('/exportcsv-match-history.php?type=member_history&param='+sort+'&'+param);
	
	
}

