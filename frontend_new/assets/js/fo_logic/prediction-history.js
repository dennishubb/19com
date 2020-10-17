window.prediction_history_page_no = ""
window.selectedHeaderId = ""
window.selectedClassName = ""

function get_main_sport_dropdown(selected_id) {
    var dropdown = '<option value="">---请选择---</option>';
    var selected = '';

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
        type: 'GET',
        url: link + '/api/cn/category',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        data: { filter: filter_array },
        success: function(response, status, xhr) {
            obj = response;
            if (obj.data.length == 0) {
                dropdown = '<option value="">---无记录---</option>';
            } else {
                $.each(obj.data, function(index, value) {
                    if (value.id == selected_id)
                        selected = 'selected';
                    dropdown += '<option value=' + value.id + ' ' + selected + '>' + value.display + '</option>';
                    selected = ''
                });
            }
            $('#parent_sport_category').html(dropdown);
        },
        error: function() {
            alert('AJAX ERROR - get main category');
        },
    });
}

function get_league_dropdown(cat_id) {
    var dropdown = '<option value="">---请选择---</option>';
    var selected = '';
    if (!cat_id) {
        dropdown = '<option value="">---无记录---</option>';
        $('#league_list').html(dropdown);
        return ""
    }
    var filter_array = [{
        field: 'league.category_id',
        value: cat_id,
        operator: '=',
    }];

    $.ajax({
        type: 'GET',
        url: link + '/api/cn/league',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        data: { filter: filter_array },
        success: function(response, status, xhr) {
            obj = response;
            if (obj.data.length == 0) {
                dropdown = '<option value="">---无记录---</option>';
            } else {
                $.each(obj.data, function(index, value) {
                    dropdown += '<option value=' + value.id + ' ' + selected + '>' + value.name_zh + '</option>';
                    selected = '';
                });
            }
            $('#league_list').html(dropdown);
        },
        error: function() {
            alert('AJAX ERROR - get main category');
        },
    });
}

//fill up filter dropdown lists
function get_filter() {
    var month_dropdown = '<option value="">---请选择---</option>';
    var year_dropdown = '<option value="">---请选择---</option>';

    month_dropdown += get_month_dropdown();
    year_dropdown += get_year_dropdown();
    get_main_sport_dropdown(0); //get main category
    //get_league_dropdown();
    $('#month').html(month_dropdown);
    $('#year').html(year_dropdown);
}

var sortByItem = [{
        field: "prediction.created_at",
        sort: "desc"
    }]
    //get match history table
function get_prediction_history_table(search = 0) { //search=1 means call from user's search
    user_id = window.localStorage.user_id;
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

    //if search criteria
    if (search == 1) {
        var name = '';
        var val = '';
        var year;

        $('#form').find(':input').each(function(key, value) {
            name = $(this).attr("name");
            val = $(this).val();
            if (name == 'match_at_year' && (val)) {
                filter_array.push({
                    field: 'YEAR(event.match_at)',
                    value: val,
                    operator: '='
                });
            }
            if (name == 'match_at_month' && (val)) {
                filter_array.push({
                    field: 'MONTH(event.match_at)',
                    value: val,
                    operator: '='
                });

            }
            if (name != 'search_button' && name != 'match_at_year' && name != 'match_at_month') {
                if (val) {
                    filter_array.push({
                        field: name,
                        value: val,
                        operator: '='
                    });
                }
            }
        });
    }
    $.ajax({
        type: 'GET',
        url: link + '/api/cn/prediction',
        //url: link + '/api/cn/event',
        data: {
            filter: filter_array,
            sort: sortByItem,
            limit: 10,
            page_number: window.prediction_history_page_no ? window.prediction_history_page_no : 1
        },
        crossDomain: true,
        contentType: false,
        success: function(response, status, xhr) { 
            var obj = response;
            var prediction_table_data = [];
            var home_team_name = '';
            var away_team_name = '';
            var handicap = '';
            var over_under = '';
            var single = '';
            var match_status = '';
            var winning_team_name = '';
            var total_page = obj.totalPage;
            var positiveAmount = []
              //console.log(obj.data);
            $.each(obj.data, function(index, value) {
                if (value.home_team_data == undefined) {    
                    return true;    
                }
                
                home_team_name = value.home_team_data.name_zh;
                away_team_name = value.away_team_data.name_zh;

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
                
                if (value.single_home == 1) { //console.log(value.id)
                    //single=home_team_name +' 主 ' + value.event_data.single_home ;
                    single = '主';
                    winning_team_name = home_team_name;
                } else if (value.single_away == 1) {
                    //single=away_team_name + ' 客 ' + value.event_data.single_away ;
                    single = '客';
                    winning_team_name = away_team_name;
                } else if (value.single_tie == 1) {
                    //single='和 ' + value.event_data.single_tie ;
                    single = '和 ';
                    winning_team_name = ""
                } else {
                    single = "-";
                    winning_team_name = "";
                }
                //CONSTRUCT SINGLE END
                //console.log(value.id,single)

                //CONSTRUCT MATCH STATUS START  current date > match_at and ended = 0.
                var final_win_amount = ""
                if (value.event_data.ended == 0 || value.status == "") {
                    match_status = '-'
                }else {
                    if ((value.win == 1) && (value.win_amount != 0.00)) {
                        match_status = '胜'
                        final_win_amount = '+' + value.win_amount
                        positiveAmount.push(value.id)
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
                prediction_table_data.push({
                    predictionId: value.id,
                    created_at: value.created_at,
                    match_at: value.event_data.match_at,
                    league_name: value.league_data.name_zh,
                    handicap: handicap,
                    over_under: over_under,
                    single: single,
                    win_amount: final_win_amount,
                    match_status: match_status,
                });
            });
            obj = {
                totalPage: total_page,
                current_page: window.prediction_history_page_no ? window.prediction_history_page_no : 1,
                prediction_table_data: prediction_table_data
            };
            //console.log(obj)
            var html = $.get(mainHost + 'cn/prediction-history/prediction-history-body.html', function(data) {
                var render = template.compile(data);
                var str = render(obj);
                $('#prediction_history_table_body').html(str);
                if (window.selectedClassName == "fa-sort-up") {
                    $('#' + window.selectedHeaderId).removeClass("fa-sort-down")
                    $('#' + window.selectedHeaderId).addClass("fa-sort-up")
                } else if (window.selectedClassName == "fa-sort-down") {
                    $('#' + window.selectedHeaderId).removeClass("fa-sort-up")
                    $('#' + window.selectedHeaderId).addClass("fa-sort-down")
                }
                if (positiveAmount != null && positiveAmount.length) {
                    for (var i = 0; i < positiveAmount.length; i++) {
                        $("#winAmount-" + positiveAmount[i]).addClass("redFont")
                    }
                }
            });
        },
        error: function() {
            alert('AJAX ERROR - get prediction history');
        },
    });
}

function sortByItemFunc(sortField, sortValue, headerId) {
    var sortBy = ""
    if (sortValue == "fa-sort-down") sortBy = "desc"
    if (sortValue == "fa-sort-up") sortBy = "asc"

    /*for (var i = 0; i < sortByItem.length; i++) {
        if (sortByItem[i].field == sortField) {
            if(sortByItem[i].sort == sortBy) {
                break
            }
            else {
                sortByItem[i].sort = sortBy 
                break
            }
        }
    }*/
    sortByItem = {
        field: sortField,
        sort: sortBy
    }
    window.selectedHeaderId = headerId
    window.selectedClassName = sortValue

    $.when(sortByItemFunc).done(function() {
        get_prediction_history_table()
    });
}

function sortingBy(headerId, fieldName) {
    $('#' + headerId).toggleClass("fa-sort-down fa-sort-up")
    var itemClass = $('#' + headerId).prop("classList")[1]
    sortByItemFunc(fieldName, itemClass, headerId)
}