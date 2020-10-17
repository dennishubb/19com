var $newGamingLeague = null
var $newGameTeam = null
var leaguePicker = $('#league_id')
var homeTeamPicker = $('#home_team_id')
var awayTeamPicker = $('#away_team_id')
var $error_selector = $(".message_output")
var $redirect_uri = 'match-result.html'
function appendSoccerRoundList() {
    for (var i = 1; i < 61; i++) {
        roundSelector.append($(`<option class="soccer-option" value="${i}">${i}</option>`))
    }
}

function appendBasketballRoundList() {
    roundSelector.append($(`<option  class="basketball-option" value="常规赛">常规赛</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="季后赛第1轮">季后赛第1轮</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="季后赛第2轮">季后赛第2轮</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="东区决赛">东区决赛</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="西区决赛">西区决赛</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="半决赛">半决赛</option>`))
    roundSelector.append($(`<option  class="basketball-option" value="总决赛">总决赛</option>`))
}

function getSelectedIds() {
    var datatable = $('#main-datatable').KTDatatable()

    return datatable
        .getSelectedRecords()
        .find('td[data-field="id"] input[type=checkbox]')
        .toArray()
        .map(function(input) {
            return parseInt($(input).val())
        })
}

function submitBatchUpdate() {
    var selectedIds = getSelectedIds();
    var modalDatatable = $('#modal-datatable').KTDatatable()
    var form = document.getElementById('modal-form');
    var formData = new FormData(form);
    var data = {};
    formData.forEach(function(value, key) {
        data[key] = value;
    });
    var parsedData = selectedIds.map(function(id) {
        return {
            id,
            title: data[`title[${id}]`],
            content: data[`content[${id}]`],
            active_at: data[`active_at[${id}]`],
            category: data[`category[${id}]`],
            tags: data[`tags[${id}]`] ? JSON.parse(data[`tags[${id}]`]).map(tag => tag.value) : [],
            seo_title: data[`seo_title[${id}]`],
            description: data[`description[${id}]`],
            keywords: data[`keywords[${id}]`],
            disabled: data[`disabled[${id}]`] == "on" ? 0 : 1
        }
    })

    batchUpdateEvent(parsedData).then(function() {
        $('#batch_edit_modal').modal('hide');
        var datatable = $("#main-datatable").KTDatatable();
        datatable.load()
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    })
}
var $selectedId = null;
var $flagUpdate = null;
var $selectedResultId = null;

function getChecked(selectedId, selectedResultId = null) {
    $selectedId = selectedId
    $selectedResultId = selectedResultId;
    clearAlert();
    var selectedData = tableDataRaw.find(function(row) {
        return row.id == selectedId
    })
    if (selectedData.category_id != "1") {
        $('.single-bet-tie').hide()
    }
    //home_team_id
    $('#home_team_id').html(selectedData.home_team_data.name_zh);
    //away_team_id
    $('#away_team_id').html(selectedData.away_team_data.name_zh);
    if ($selectedResultId) {
        selectedData.result_data.handicap_home ? $('#handicap-result-home').click() : ""
        selectedData.result_data.handicap_away ? $('#handicap-result-away').click() : ""
        selectedData.result_data.over_under_home ? $('#over-under-result-home').click() : ""
        selectedData.result_data.over_under_away ? $('#over-under-result-away').click() : ""
        selectedData.result_data.single_home ? $('#single-result-home').click() : ""
        selectedData.result_data.single_away ? $('#single-result-away').click() : ""
        if (selectedData.category_id == "1") selectedData.result_data.single_tie ? $('#single-result-tie').click() : ""
    }
    /*else{
            $('#handicap-result-away').click();
            $('#handicap-result-home').click();
            $('#over-under-result-away').click()
            $('#over-under-result-home').click()
            $('#single-result-tie').click();
            $('#single-result-home').click();
        }*/
    //handicap_home_bet handicap_home_odds
    $('#handicap-result-home').val(selectedData.handicap_home_bet + '/' + selectedData.handicap_home_odds);
    $('#handicap-result-home-label').html(selectedData.handicap_home_bet + '/' + selectedData.handicap_home_odds);
    //handicap_away_bet handicap_away_odds
    $('#handicap-result-away').val(selectedData.handicap_away_bet + '/' + selectedData.handicap_away_odds);
    $('#handicap-result-away-label').html(selectedData.handicap_away_bet + '/' + selectedData.handicap_away_odds);
    //single_home_bet over_under_home_odds
    $('#over-under-result-home').val(selectedData.over_under_home_bet + '/' + selectedData.over_under_home_odds);
    $('#over-under-result-home-label').html(selectedData.over_under_home_bet + '/' + selectedData.over_under_home_odds);
    //over_under_away_bet over_under_away_odds
    $('#over-under-result-away').val(selectedData.over_under_away_bet + '/' + selectedData.over_under_away_odds);
    $('#over-under-result-away-label').html(selectedData.over_under_away_bet + '/' + selectedData.over_under_away_odds);
    //single_home
    $('#single-result-home').val(selectedData.single_home);
    $('#single-result-home-label').html("主 " + selectedData.single_home);
    //single_tie
    if (selectedData.category_id == "1") {
        $('#single-result-tie').val(selectedData.single_tie);
        $('#single-result-tie-label').html("和 " + selectedData.single_tie);
    }
    //single_away
    $('#single-result-away').val(selectedData.single_away);
    $('#single-result-away-label').html("客 " + selectedData.single_away);
    $('#pop-up-modal').modal('show');
}

function addFiltering(paramName, value) {
    var datatable = $("#main-datatable").KTDatatable();
    datatable.setDataSourceParam('search[' + paramName + ']', value)
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params['search[' + paramName + ']']
}

function removeSort(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params['sort[' + paramName + ']']
}

function handleBatchDelete(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();
    batchDeleteEvent(selectedIds)
        .then(function(response) {
            datatable.load();
            if (response.code == 1) showAlert(response.status, "success", $error_selector);
            else showAlert(response.status, "danger", $error_selector);
        }).catch(function(e) {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function handleBatchHotNews(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchHotNewsEvent(selectedIds)
        .then(function(response) {
            datatable.load();
            if (response.code == 1) showAlert(response.status, "success", $error_selector);
            else showAlert(response.status, "danger", $error_selector);
        }).catch(function(e) {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function handleBatchRecycled(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchRecycledEvent(selectedIds)
        .then(function(response) {
            datatable.load();
            if (response.code == 1) showAlert(response.status, "success", $error_selector);
            else showAlert(response.status, "danger", $error_selector);
        }).catch(function(e) {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function showBulkActionPrompt(action) {
    var msg = '';

    if (action == 'bulk_delete') {
        msg = '确定批量删除？';
    } else if (action == '') {
        msg = '请选择功能';
    } else if (action == 'bulk_hotNews') {
        msg = '确定批量设定热门新闻？';
    } else if (action == 'bulk_recycled') {
        msg = '确定批量转至回收站？';
    }

    return confirm(msg);
}

function postEvent(eventId) {
    getEvent(eventId)
        .then(response => {
            var event = response.data
            event.draft = 0;
            return editEvent(event);
        })
        .then(response => {
            ////console.log(response);
            obj = response;
            if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=Event%20posted");
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                window.scrollTo(0, 0);
                showAlert(obj.message, "danger", $error_selector);
            }
        })
        .catch(function(error) {
            window.scrollTo(0, 0);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function editEvent(event) {
    return $.ajax({
        type: 'PUT',
        url: getBackendHost() + '/api/cn/event?id=' + event.id,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: JSON.stringify(event)
    });
}

function getEvent(eventId) {
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/event?id=' + eventId,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function updateEvent(event) {
    return callAjaxFunc('PUT', event,
        '/api/cn/event')
}

function createTutorial(event) {
    return callAjaxFunc('PUT', event,
        '/api/cn/tutorial?id=1')
}

function createEvent(event) {
    return callAjaxFunc('POST', event,
        '/api/cn/event')
}

function batchUpdateEvent(event) {
    return callAjaxFunc('PATCH', {
            data: event
        },
        '/api/cn/event'
    )
}

function draft_ajax_submit($form) {
    var formData = new FormData(document.getElementById($form.attr('id')));
    formData.append('draft', 1);
    ajax_submit($form, $(".message_output"), formData);
}

function uploadImageCall(file) {
    if (file == null)
        return Promise.resolve({
            code: 1
        })
    var formData = new FormData();
    formData.append('file', file)
    formData.append('type', file.type)
    formData.append('folder', 'match-prediction')
    return $.ajax({
        type: "POST",
        url: "/assets/php/media-meta.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}

function saveMetaFilePath(path) {
    if (path == null)
        return Promise.resolve({
            code: 1
        })
    var formData = new FormData();
    formData.append('tempfile', path)
    return $.ajax({
        type: "POST",
        url: "/assets/php/media-save.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}

function batchDeleteEvent(eventIds) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    eventIds.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/event'
    )
}

function batchHotNewsEvent(eventIds) {
    return callAjaxFunc(
        'PATCH', {
            data: eventIds.map(eventId => {
                return {
                    id: eventId,
                    hot: 1
                }
            })
        },
        '/api/cn/event'
    )
}

function batchRecycledEvent(eventIds) {
    return callAjaxFunc(
        'PATCH', {
            data: eventIds.map(eventId => {
                return {
                    id: eventId,
                    status: "delete"
                }
            })
        },
        '/api/cn/event'
    )
}

function getAllMainCategories() {
    return callAjaxFunc(
        'GET', {},
        '/api/cn/category?search[type]=sport&search[parent_id]=0&search[disabled]=0'
    )
}

function getAllLeague(category_id) {
    return callAjaxFunc(
        'GET', {},
        '/api/cn/league?search[league.category_id]=' + category_id + '&sort[field]= use_count &sort[sort]=desc'
        //'/api/cn/league?search[category_id]='+category_id
    )
}

function getAllTeam(league_id, category_id) {
    return callAjaxFunc(
        'GET', {},
        '/api/cn/team?search[league_id]=' + league_id + '&search[category_id]=' + category_id + '&limit=100&sort[field]= use_count &sort[sort]=desc'
    )
}

function callAjaxFunc(method, data, url) {
    return $.ajax({
        type: method,
        url: getBackendHost() + url,
        data: JSON.stringify(data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function confirmMatchResult(selections) {
    var selectedData = tableDataRaw.find(function(row) {
        return row.id == $selectedId
    })
    selections.originalData = selectedData
    $('#confirm_home_team').html($('#home_team_id').html());
    $('#confirm_away_team').html($('#away_team_id').html());

    if (selections.home_team.handicap.selected) {
        if ($selectedResultId) {
            $('#confirm_home_handicap').html(selections.originalData.handicap_home_bet + "/" + selections.originalData.handicap_home_odds)
        } else {
            $('#confirm_home_handicap').html(selections.home_team.handicap.value)
        }
        $('#confirm_home_handicap').show()
        $('#confirm_away_handicap').hide()
    }
    if (selections.away_team.handicap.selected) {
        if ($selectedResultId) {
            $('#confirm_away_handicap').html(selections.originalData.handicap_away_bet + "/" + selections.originalData.handicap_away_odds)
        } else {
            $('#confirm_away_handicap').html(selections.away_team.handicap.value)
        }
        $('#confirm_away_handicap').show()
        $('#confirm_home_handicap').hide()

    }
    if (selections.away_team.over_under.selected) {
        if ($selectedResultId) {
            $('#confirm_away_over_under').html(selections.originalData.over_under_away_bet + "/" + selections.originalData.over_under_away_odds)
        } else {
            $('#confirm_away_over_under').html(selections.away_team.over_under.value)
        }
        $('#confirm_away_over_under').show()
        $('#confirm_home_over_under').hide()
    }
    if (selections.home_team.over_under.selected) {
        if ($selectedResultId) {
            $('#confirm_home_over_under').html(selections.originalData.over_under_home_bet + "/" + selections.originalData.over_under_home_odds)
        } else {
            $('#confirm_home_over_under').html(selections.home_team.over_under.value)
        }
        $('#confirm_home_over_under').show()
        $('#confirm_away_over_under').hide()
    }
    if (selections.home_team.single.selected) {
        if ($selectedResultId) {
            $('#confirm_home_single').html('主' + selections.originalData.single_home)
        } else {
            $('#confirm_home_single').html(selections.home_team.single.value)
        }
        $('#confirm_home_single').show()
        $('#confirm_away_single').hide()
        $('#confirm_tie_single').hide()
    }
    if (selections.away_team.single.selected) {
        if ($selectedResultId) {
            $('#confirm_away_single').html('客' + selections.originalData.single_away)
        } else {
            $('#confirm_away_single').html(selections.away_team.single.value)
        }
        $('#confirm_away_single').show()
        $('#confirm_home_single').hide()
        $('#confirm_tie_single').hide()
    }
    if (selections.tie.selected) {
        if ($selectedResultId) {
            $('#confirm_tie_single').html("和" + selections.originalData.single_tie)
        } else {
            $('#confirm_tie_single').html(selections.tie.value)
        }
        $('#confirm_tie_single').show()
        $('#confirm_away_single').hide()
        $('#confirm_home_single').hide()
    }
    $('#pop-up-modal').modal('hide');
    $('#pop-up-confirmation').modal('show');
}

function backKey() {
    $('#pop-up-confirmation').modal('hide');
    $('#pop-up-modal').modal('show');
}

function finalConfirm(selections) {
    var selectedId = $selectedId;
    var resultPop = {
        event_id: selectedId,
        handicap_home: selections.home_team.handicap.selected ? 1 : 0,
        handicap_away: selections.away_team.handicap.selected ? 1 : 0,
        over_under_home: selections.home_team.over_under.selected ? 1 : 0,
        over_under_away: selections.away_team.over_under.selected ? 1 : 0,
        single_home: selections.home_team.single.selected ? 1 : 0,
        single_away: selections.away_team.single.selected ? 1 : 0,
        single_tie: selections.tie.selected ? 1 : 0,
    }

    if (selections.home_team.handicap.selected) {
        resultPop.handicap_odds = selections.originalData.handicap_home_odds
        resultPop.handicap_bet = selections.originalData.handicap_home_bet
    } else if (selections.away_team.handicap.selected) {
        resultPop.handicap_odds = selections.originalData.handicap_away_odds
        resultPop.handicap_bet = selections.originalData.handicap_away_bet
    }

    if (selections.home_team.over_under.selected) {
        resultPop.over_under_odds = selections.originalData.over_under_home_odds
        resultPop.over_under_bet = selections.originalData.over_under_home_bet
    } else if (selections.away_team.over_under.selected) {
        resultPop.over_under_odds = selections.originalData.over_under_away_odds
        resultPop.over_under_bet = selections.originalData.over_under_away_bet
    }

    if (selections.home_team.single.selected) {
        resultPop.single_odds = selections.originalData.single_home
    } else if (selections.away_team.single.selected) {
        resultPop.single_odds = selections.originalData.single_away
    } else if (selections.tie.selected) {
        resultPop.single_odds = selections.originalData.single_tie
    }
    if ($selectedResultId) {
        method = 'PUT';
        API = '/api/cn/result?id=' + $selectedResultId
    } else {
        method = 'POST';
        API = '/api/cn/result'
    }
    callAjaxFunc(method, resultPop, API)
        .then(response => {
            if (response.code == 1) {
                if (response.redirect) redirect_to($redirect_uri + "?alert-success=" + response.status);
            }
        })
}

var leaguePicker = $('#league_id');
var roundSelector = $('#round');
var roundLabel = $('#round-label');
var oldEditor = []
var usedImages = []
var allImgAry = []
var homeTeamPicker = $('#home_team_id');
var awayTeamPicker = $('#away_team_id');

function getImgUrl(contentData) {
    return data = Array.from(new DOMParser().parseFromString(contentData, 'text/html')
            .querySelectorAll('img'))
        .map(img => img.getAttribute('src'))
}

function removeCharacter(contentData) {
    var newData = []
    contentData.forEach(function(value, key) {
        newData.push(value.replace(/\\"/g, ''))
    });
    return newData
}

function getRemovedImages(allImages, usedImages) {
    var deletedImages = _.uniq(allImages);
    _.pullAll(deletedImages, usedImages);
    return deletedImages;
}

function deleteUnusedImage(image) {
    if (!image)
        return Promise.resolve()

    var formData = new FormData();
    formData.append('filename', image)
    return $.ajax({
        type: "POST",
        url: "/assets/php/media-delete.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}

/*function getPredictionUserList(event_id){
    var wincolor='#18f500';
    var losecolor='#ff3030';

    datatable = $('#predict_user_datatable').KTDatatable({
            // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/prediction', 
                    method:'GET',
                    params: {
                        search:{event_id:event_id}
                        }
                },
            },
            pageSize: 20, // display 20 records per page
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            saveState:false,
        },

            // layout definition
            layout: {
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
                input: $('#generaSearch'),
                delay: 400,
            },
            // columns definition
            columns: [
                {
                    field: "username",
                    title: "会员账号",
                    width: 70,
                    // callback function support for column rendering
                    template: function (data, i) {
                    var output = '' +
                        '<div >' + data.user_data.username  +
                        '</div>';

                    return output;
                }
                },
                {
                    field: "prediction.created_at",
                    title: "预测时间",
                    autoHide: false,
                    width: 80,
                    // callback function support for column rendering
                    template: function (data, i) {
                    var output = '' +
                        '<div >' + data.created_at +
                        '</div>';

                    return output;
                }
                },
                {
                    field: 'handicap_win',
                    title: '让球',
                autoHide: false,
                    width: 70,
                template: function (data, i) {
                    var handicap='';
                    var bgcolor='';

                    var handicap_home_bet=data.event_data.handicap_home_bet;
                    var handicap_home_odds=data.event_data.handicap_home_odds;
                    var handicap_away_bet=data.event_data.handicap_away_bet;
                    var handicap_away_odds=data.event_data.handicap_away_odds;

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
                    output = '' +
                        '<div style='+bgcolor+'>' + handicap +
                        '</div>';

                    return output;
                }
                },
                {
                    field: 'over_under_win',
                    title: '大小',
                width: 70,
                    autoHide: false,
                template: function (data, i) {
                    var over_under='';
                    var bgcolor='';
                    var win_team='';

                    var over_under_home_bet=data.event_data.over_under_home_bet;
                    var over_under_home_odds=data.event_data.over_under_home_odds;
                    var over_under_away_bet=data.event_data.over_under_away_bet;
                    var over_under_away_odds=data.event_data.over_under_away_odds;

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
                },
                {
                    field: 'single_win',
                    title: '独赢',
                width: 80,
                    autoHide: false,
                template: function (data, i) {//////console.log(data);
                        var single='';
                    var single_home=data.event_data.single_home;
                    var single_away=data.event_data.single_away;
                    var single_tie=data.event_data.single_tie;
                    
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
                }
                ]
        });
}

*/

//get event and result data to display user list
function getEventData(event_id) {
    var event_data;
    var filter_array = [{
        field: 'event.id',
        value: event_id,
        operator: '=',
    }];
    var getEvent = $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/event',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        data: { filter: filter_array },
        success: function(response, status, xhr) {
            obj = response;
            event_data = obj.data[0];
        },
        error: function() {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
    $.when(getEvent).done(function(getEvent) {
        getPredictionUserList(event_id, event_data);
    });
}

function getPredictionUserList(event_id, event_data) {
    var wincolor = '#18f500';
    var losecolor = '#ff3030';
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

    var table = '<table class="modal_predictionUserList"style="width: 100%;"><tr><th>会员账号</th><th>预测时间</th><th>让球</th><th>大小</th><th>独赢</th></tr>';
    $('#predict_user_datatable').html('Loading')
    $.ajax({
        type: 'GET',
        //url: getBackendHost() + $action,
        url: getBackendHost() + '/api/cn/prediction',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        data: { filter: filter_array },
        success: function(response, status, xhr) {

            obj = response;
            if (obj.data.length == 0)
                table += '<tr><td colspan=6>无记录</td></tr>';
            else {
                $.each(obj.data, function(index, value) {
                    var handicap = get_handicap(obj.data[index], event_data, wincolor, losecolor);
                    var over_under = get_over_under(obj.data[index], event_data, wincolor, losecolor);
                    var single = get_single(obj.data[index], event_data, wincolor, losecolor);

                    table += '<tr>';
                    table += '<td>' + value.user_data.username + '</td>';
                    table += '<td>' + value.created_at + '</td>';
                    table += '<td>' + handicap + '</td>';
                    table += '<td>' + over_under + '</td>';
                    table += '<td>' + single + '</td>';
                    //table+='<td>'+value.win_amount+'</td>';
                    table += '</tr>';
                });
            }
            table += '</table>';
            $('#predict_user_datatable').html(table)
                //get_sub_sport_dropdown(selected_id);
        },

        error: function() {
            alert('AJAX ERROR - get main category');
        },
    });


}

//get handicap of each user after click main table's 查看
function get_handicap(data, event_data, wincolor, losecolor) {
    var handicap = '';
    var bgcolor = '';
    var user_prediction = '';
    var win_team = '';
    var over_under = '';

    var handicap_home_bet = event_data.handicap_home_bet;
    var handicap_home_odds = event_data.handicap_home_odds;
    var handicap_away_bet = event_data.handicap_away_bet;
    var handicap_away_odds = event_data.handicap_away_odds;

    ////console.log(data)

    if (data.handicap_win == 1)
        bgcolor = 'background-color:' + wincolor; //green
    else
        bgcolor = 'background-color:' + losecolor; //green

    if (data.handicap_home == 1) { //if user bet for home in handicap 
        handicap = handicap_home_bet + '/' + handicap_home_odds; //exp: -0.5/1.00

    } else if (data.handicap_away == 1) { //if user bet for away in handicap 
        handicap = handicap_away_bet + '/' + handicap_away_odds;
    }

    output = '<div style=' + bgcolor + '>' + handicap + '</div>';

    return output;
}

//get over under of each user after click main table's 查看
function get_over_under(data, event_data, wincolor, losecolor) {
    var over_under = '';
    var bgcolor = '';
    var win_team = '';

    var over_under_home_bet = event_data.over_under_home_bet;
    var over_under_home_odds = event_data.over_under_home_odds;
    var over_under_away_bet = event_data.over_under_away_bet;
    var over_under_away_odds = event_data.over_under_away_odds;

    /*
    if (result_data.over_under_home==1)
        win_team='home';
    else if (result_data.over_under_away==1)
        win_team='away';
    else
        win_team='Logic error';//over_under_home and over_under_away must be one 1 one 0
    */

    if (data.over_under_win == 1)
        bgcolor = 'background-color:' + wincolor; //green
    else
        bgcolor = 'background-color:' + losecolor; //red

    if (data.over_under_home == 1) { //if user bet for home in over under 
        over_under = over_under_home_bet + '/' + over_under_home_odds; //exp: -0.5/1.00

    } else if (data.over_under_away == 1) { //if user bet for away in over under 
        over_under = over_under_away_bet + '/' + over_under_away_odds;
    }

    output = '' +
        '<div style=' + bgcolor + '>' + over_under +
        '</div>';

    return output;
}

//get single of each user after click main table's 查看
function get_single(data, event_data, wincolor, losecolor) {
    var single = '';
    var single_home = event_data.single_home;
    var single_away = event_data.single_away;
    var single_tie = event_data.single_tie;


    if (data.single_win == 1)
        bgcolor = 'background-color:' + wincolor; //green
    else
        bgcolor = 'background-color:' + losecolor; //red

    if (data.single_home == 1) {
        //single=home_team_name +' 主 ' + value.event_data.single_home ;
        single = '主 - ' + single_home;
    } else if (data.single_away == 1) {
        //single=away_team_name + ' 客 ' + value.event_data.single_away ;
        single = '客 - ' + single_away;
    } else if (data.single_tie == 1) {
        //single='和 ' + value.event_data.single_tie ;
        single = '和 - ' + single_tie
    }

    output = '' +
        '<div style=' + bgcolor + '>' + single +
        '</div>';

    return output;
}

//add new league
function addNewLeague() {
    var sportType = 4
    var newLeague = prompt("请输入联赛")
    if (newLeague) {
        window.alert("成功新增联赛");
    }
    if (newLeague != null) {
        $newGamingLeague = newLeague
        return addedNewLeague(newLeague).then(leagueDropdownGaming)
    }
    return Promise.resolve()
}

function addedNewLeague(newLeague) {
    var data = {
        name_en: newLeague,
        name_zh: newLeague,
        name_zht: newLeague,
        shortname_en: newLeague,
        shortname_zh: newLeague,
        shortname_zht: newLeague,
        category_id: 4
    }
    return callAjaxFunc('POST', data,
        '/api/cn/league')
}

//edit league
function editGameLeague() {
    var leagueId = $("#league_id option:selected").val();
    var leagueText = $("#league_id option:selected").text();
    if (!leagueId) return alert("请选择联赛！")
    var editedLeague = prompt("确定修改"+leagueText+"联赛?")
    if (editedLeague) {
        window.alert("成功修改联赛");
    }
    if (editedLeague != null) {
        $newGamingLeague = editedLeague
        return editedGameLeague(editedLeague,leagueId).then(leagueDropdownGaming)
    }
    return Promise.resolve()
}

function editedGameLeague(editLeague, leagueId) {
var data = {
    name_en: editLeague,
    name_zh: editLeague,
    name_zht: editLeague,
    shortname_en: editLeague,
    shortname_zh: editLeague,
    shortname_zht: editLeague,
    id:leagueId
}
return callAjaxFunc('PUT', data,
    '/api/cn/league')
}

//Gaming league dropdown list
function leagueDropdownGaming() {
    return getAllLeague(4).then(response => {
        leaguePicker.empty();
        leaguePicker.selectpicker('refresh');
        leaguePicker.append($(`<option value="">无</option>`))
        response.data.forEach(league => {
            leaguePicker.append($(`<option value="${league.id}">${league.name_zh}</option>`))
            if ($newGamingLeague == league.name_zh) {
                leaguePicker.find(`option[value="${league.id}"]`).attr('selected', '1')
            }
        })
        leaguePicker.selectpicker('refresh');
    })
}

//add new home team gaming
function addGameTeam() {
    var sportType = 4
    var newGameTeam = prompt("请输入队伍")
    if (newGameTeam) {
        window.alert("成功新增队伍");
    }
    if (newGameTeam != null) {
        $newGameTeam = newGameTeam
        return addedGameTeam(newGameTeam).then(homeTeamDropdownGaming).then(awayTeamDropdownGaming)
    }
    return Promise.resolve()
}

function addedGameTeam(newGameTeam) {
    var data = {
        name_en: newGameTeam,
        name_zh: newGameTeam,
        name_zht: newGameTeam,
        shortname_en: newGameTeam,
        shortname_zh: newGameTeam,
        shortname_zht: newGameTeam,
        category_id: 4
    }
    return callAjaxFunc('POST', data,
        '/api/cn/team')
}

//edit new home team gaming
function editGameTeam() {
    var homeTeamId = $("#home_team_id option:selected").val();
    var homeTeamText = $("#home_team_id option:selected").text();
    if (!homeTeamId) return alert("请选择联赛！")
    var editedTeam = prompt("确定修改"+homeTeamText+"队伍?")
    if (editedTeam) {
        window.alert("成功修改队伍");
    }
    if (editedTeam != null) {
        $editedTeam = editedTeam
        return editedGameTeam(editedTeam,homeTeamId)
        .then(homeTeamDropdownGaming(homeTeamId))
        .then(awayTeamDropdownGaming)
    }
    
    return Promise.resolve()
}

function editedGameTeam(editedTeam,homeTeamId) {
    var data = {
        name_en: editedTeam,
        name_zh: editedTeam,
        name_zht: editedTeam,
        shortname_en: editedTeam,
        shortname_zh: editedTeam,
        shortname_zht: editedTeam,
        id: homeTeamId
    }
    return callAjaxFunc('PUT', data,
        '/api/cn/team')
}

function homeTeamDropdownGaming(teamid=0) {
    return getAllTeam(0, 4).then(response => {
        homeTeamPicker.empty();
        homeTeamPicker.selectpicker('refresh');
        homeTeamPicker.append($(`<option value="">无</option>`))
        response.data.forEach(team => {
            homeTeamPicker.append($(`<option value="${team.id}">${team.name_zh}</option>`))
            if ($newGameTeam == team.name_zh) {
                homeTeamPicker.find(`option[value="${team.id}"]`).attr('selected', '1')
            }
        })
        homeTeamPicker.selectpicker('refresh');
        
            
    })
}

function awayTeamDropdownGaming() {
    return getAllTeam(0, 4).then(response => {
        awayTeamPicker.empty();
        awayTeamPicker.selectpicker('refresh');
        awayTeamPicker.append($(`<option value="">无</option>`))
        response.data.forEach(team => {
            awayTeamPicker.append($(`<option value="${team.id}">${team.name_zh}</option>`))
        })
        awayTeamPicker.selectpicker('refresh');
    })
}

function clearList (){
    leaguePicker.empty();
    leaguePicker.selectpicker('refresh');
    leaguePicker.append($(`<option value="">无</option>`))
    homeTeamPicker.empty();
    homeTeamPicker.selectpicker('refresh');
    homeTeamPicker.append($(`<option value="">请选择联赛</option>`))
    awayTeamPicker.empty();
    awayTeamPicker.selectpicker('refresh');
    awayTeamPicker.append($(`<option value="">请选择联赛</option>`))
}

function deleteEsportLeague() {
    var deleteLeague = $("#league_id").val();
    var LeagueText = $("#league_id option:selected").text();
    var confirmDelete = ""
    if (deleteLeague) {
        confirmDelete = confirm("确认删除 "+LeagueText+" ？")
    }
    
    if (deleteLeague != null && confirmDelete) {
        return callAjaxFunc('DELETE', {
            id: deleteLeague
        },
        '/api/cn/league')
        .then(response => {
            //console.log(response);
            obj = response;
            if (obj.code != 1) {
                showAlert(obj.message, "danger", $error_selector);
            } 
              
            
        })
        .then(leagueDropdownGaming)
    }
}

function deleteEsportTeam() {
    var deleteTeam = $("#home_team_id").val();
    var teamText = $("#home_team_id option:selected").text();
    var confirmDelete = ""
    if (deleteTeam) {
        confirmDelete = confirm("确认删除 "+teamText+" ？")
    }
    
    if (deleteTeam != null && confirmDelete) {
        return callAjaxFunc('DELETE', {
            id: deleteTeam
        },
        '/api/cn/team') 
        .then(response => {
            //console.log(response);
            obj = response;
            if (obj.code != 1) {
                showAlert(obj.message, "danger", $error_selector);
            } 
              
            
        })
        .then(homeTeamDropdownGaming)
        .then(awayTeamDropdownGaming)
    }
}

