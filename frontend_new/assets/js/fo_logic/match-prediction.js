window.mpResponse = ""
window.userFavoriteResponse = ""
window.userPredictionNumber = ""
window.userPredictionEventIdAry = []
window.userPredictionResultAry = []
var unlockTopTenListByUserAry = []
window.selectedUnlockPredictionId = ""
window.selectedUnlockPredictionUserId = ""
    //var refreshTopTen = true
var event_id_from_carousel_index = getQueryString("event_id");

//===getCurrentDateTime===
function getCurrentDateTime() {
    var dt = new Date();
    var currentDateTime = `${
        dt.getFullYear().toString().padStart(4, '0')}-${
        (dt.getMonth()+1).toString().padStart(2, '0')}-${
        dt.getDate().toString().padStart(2, '0')} ${
        dt.getHours().toString().padStart(2, '0')}:${
        dt.getMinutes().toString().padStart(2, '0')}:${
        dt.getSeconds().toString().padStart(2, '0')
    }`
    return currentDateTime
}

//===category_menu_outside===
function category_menu_outside() {
    var obj = {};
    var html = $.get('/cn/match-prediction/category-menu-outside.html', function(data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#category-menu-outside').html(str);
    })
}

//===match_select_outside===
function match_select_outside() {
    var mp_carousel =
        $.ajax({
            url: link + '/api/cn/event',
            data: {
                filter: [{
                        field: 'ended',
                        value: 0,
                        operator: '='
                    }/*,
                    {
                        field: 'prediction_end_at',
                        value: getCurrentDateTime(),
                        operator: '>'
                    }*/
                ],
                sort: {
                    field: "match_at",
                    sort: "asc"
                }
            },
            type: 'GET',
            success: function(data) {
                var obj = {}
                obj["allMatch"] = data['data']
                if (event_id_from_carousel_index) {
                    window.match_id = event_id_from_carousel_index
                } else if (data['data'][0] != null) { //add null checking
                    window.match_id = data['data'][0].id
                }
                var html = $.get('/cn/match-prediction/match-select-outside.html', function(data) {
                    var render = template.compile(data);
                    var str = render(obj);
                    $('#match-select-outside').html(str);
                })
            },
            error: function() {
                alert('AJAX ERROR - get match swipper');
            },
        });
    $.when(mp_carousel).done(function(response) {
        window.mpResponse = response.data
        mpModal()
        if (!window.localStorage.access_token || !window.localStorage.user_id) main_area()
    });
}

//===top ten===
function getUnlockMatchList() {
    if (!window.localStorage.user_id) return top_ten()
    var unlocked = ""
    var checkFunc = $.ajax({
        url: link + '/api/cn/prediction_top_ten_unlock',
        data: {
            filter: [{
                    field: 'user_id',
                    value: window.localStorage.user_id,
                    operator: '='
                },
                {
                    field: 'event_id',
                    value: window.match_id,
                    operator: '='
                },
            ],
        },
        type: 'GET',
    }).then(response => {
        if (response.data.length > 0) {
            unlockTopTenListByUserAry = response['data']
            top_ten(unlockTopTenListByUserAry)
        } else top_ten()
    }, error => {
        alert('AJAX ERROR - get unlock top ten list');
    });
}

function checkLockCondition(data, unlock_list = null) {
    for (var eachTopTen of data) {
        if (unlock_list) {
            for (var list of unlock_list) {
                if (list.prediction_top_ten_id == eachTopTen.id) {
                    eachTopTen.unlockedFlag = "1";
                    break;
                } else eachTopTen.unlockedFlag = "2";
            }
        } else eachTopTen.unlockedFlag = "2";
    }
}

function top_ten(unlock_list = null) {
    var top_ten_table = $.ajax({
        url: link + '/api/cn/prediction_top_ten',
        data: {
            filter: [{
                field: 'event_id',
                value: window.match_id,
                operator: '='
            }],
        },
        type: 'GET',
        success: function(data) {
            var obj = {};
            var unlocked = ''
            obj["topTen"] = data['data'];
            checkLockCondition(obj["topTen"], unlock_list)
            var html = $.get('/cn/match-prediction/top-ten.html', function(data) {
                var render = template.compile(data);
                var str = render(obj);
                $('#top-ten').html(str);
            })
        },
        error: function() {
            alert('AJAX ERROR - get top ten prediction');
        },
    });
    $.when(top_ten_table).done(function() {
        unlock_modal();
    });
}

function unlock_modal() {
    var obj = {};
    var html = $.get('/cn/match-prediction/unlock-modal.html', function(data) {
        var render = template.compile(data);
        var str = render(obj);

        $('#unlock_popup').html(str);
    })
}

function member_list_modal() {
    if (!window.selectedUnlockPredictionUserId) alert("该用户预测不存在！")
    var unlockFunc =
        $.ajax({
            url: link + '/api/cn/prediction',
            data: {
                filter: [{
                        field: 'user_id',
                        value: window.selectedUnlockPredictionUserId,
                        operator: '='
                    },
                    {
                        field: 'event_id',
                        value: window.match_id,
                        operator: '='
                    }
                ],
            },
            type: 'GET',
            headers: getHeaders(),
            contentType: false
        }).then(response => {
            return $.get('/cn/match-prediction/member-list-modal.html').then(html => {
                return {
                    html,
                    response
                }
            })
        }, error => {
            alert('AJAX ERROR - get match top ten prediction detail');
        }).then(res => {
            $('#member_list_more').html("");
            const { html, response } = res
            var obj = {}

            //obj = response['data'][0]
            if (response['data'][0]) {
                obj = response['data'][0]
                var render = template.compile(html);
                var str = render(obj);
                $('#member_list_more').html(str);
                $('#member_list_more').modal("show")
                    //getUnlockMatchList()
                    //$("#top-ten").load(location.href+" #top-ten >*","");
            }
            //else if((!response['data'][0]) && (refreshTopTen == true)){
            else if (!response['data'][0]) {
                $('#member_list_more').modal("hide")
                //$('.modal-backdrop').remove();
                alert("该神级预言家预测已被删除！")
                    //refreshTopTen = false
                getUnlockMatchList()
                $("#top-ten").load(location.href + " #top-ten >*", "");
            }
        })
}

function unlockTopTenList() {
    unlock_data = {
        prediction_top_ten_id: window.selectedUnlockPredictionId,
        event_id: window.match_id,
        user_id: window.localStorage.user_id

    }
    if (!window.selectedUnlockPredictionId || !window.match_id || !window.localStorage.user_id) return ""
    var unlockFunc = $.ajax({
        type: "POST",
        url: link + 'api/cn/prediction_top_ten_unlock',
        data: JSON.stringify(unlock_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    }).then(response => {
        if ((response.code != 1) && (response.code != 401) && (response.code != 404)) {
            alert(response.message)
        } else if (response.code == 404) {
            $('#unlock_popup').modal('hide');
            //$('.modal-backdrop').remove();
            //alert(response.message)
            alert("该神级预言家预测已被删除！")
            getUnlockMatchList()
            $("#top-ten").load(location.href + " #top-ten >*", "");
        } else {
            $('#unlock_popup').modal('hide');
            $('.modal-backdrop').remove();
            member_list_modal();
        }
    }, error => {
        alert('AJAX ERROR - unlock top ten prediction');
    });
}

//===match prediction===
function main_area() {
    if (!window.match_id) return ""
    var mp_table = $.ajax({
        url: link + '/api/cn/event',
        data: {
            filter: [{
                field: 'event.id',
                value: window.match_id,
                operator: '='
            }],
        },
        type: 'GET',
        success: function(data) {
            var obj = data['data'][0];
            if (obj.editor_note) {
                obj.editor_note = obj.editor_note.replace(/\\"/g, '"');
                var element = $(`<div>${obj.editor_note}</div>`);
                element.find('a').each(function() {
                    const allowedTypes = ['avi', 'flv', 'mov', 'mp4', 'mpeg']
                    const href = $(this).attr('href') || ''
                    const videoData = href.split('.')
                    const type = videoData[videoData.length - 1]
                    const isAllowed = !!_.find(allowedTypes, t => t === type)
                    if (!type || !isAllowed)
                        return
                    $(this).replaceWith(`<div style="position: relative; height: 0; padding-bottom: 50%;"><iframe src="${href}" style="position: absolute; width: 640px; height: 360px; top: 0; left: 10%; right: 10%" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe></div>`)
                });
                obj.editor_note = element.html()
            }
            window.message_chatroom_id = obj.chatroom_id
            if (window.userPredictionResultAry) {
                window.userPredictionResultAry.forEach(function(userPredictedResult) {
                    var resultAry = userPredictedResult.split("-")
                    var result_event_id = resultAry[0]
                    if (result_event_id == window.match_id) {
                        obj.resultAry = JSON.stringify(resultAry)
                    }
                })
            }
            var html = $.get('/cn/match-prediction/main-area.html', function(data) {
                var render = template.compile(data);
                var str = render(obj);
                $('#main-area').html(str);
            })
        },
        error: function() {
            alert('AJAX ERROR - get match prediction');
        },
    });
    $.when(mp_table).done(function() {
        getUnlockMatchList();
        message()
    });
}

//===modal===
function mpModal() {
    event_ids = []
    home_team_data = []
    away_team_data = []
    if (window.mpResponse) {
        window.mpResponse.forEach(function(keys) {
            event_ids.push(keys.id)
            home_team_data.push(keys.home_team_data)
            away_team_data.push(keys.away_team_data)
        })
    }
    var mpModal =
        $.ajax({
            url: link + '/api/cn/prediction',
            data: {
                filter: [{
                        field: 'user_id',
                        value: window.localStorage.user_id,
                        //value: window.localStorage.user_id,
                        operator: '='
                    },
                    {
                        field: 'event_id',
                        value: event_ids,
                        operator: 'IN'
                    },
                ],
                sort: {
                    field: "prediction.created_at",
                    sort: "desc"
                }
            },
            type: 'GET',
            headers: getHeaders(),
            contentType: false
        }).then(response => {
            if (response['data']) {
                window.userPredictionEventIdAry = []
                window.userPredictionResultAry = []
                response['data'].forEach(function(keys, values) {
                    if(keys.status!='predicted') window.userPredictionEventIdAry.push(keys.event_id + "-" + keys.id)
                    window.userPredictionResultAry.push(
                        keys.event_id + "-" +
                        "handicap_home:" + keys.handicap_home + "-" +
                        "handicap_away:" + keys.handicap_away + "-" +
                        "over_under_home:" + keys.over_under_home + "-" +
                        "over_under_away:" + keys.over_under_away + "-" +
                        "single_home:" + keys.single_home + "-" +
                        "single_away:" + keys.single_away + "-" +
                        "single_tie:" + keys.single_tie
                    )
                    if (home_team_data) {
                        home_team_data.forEach(function(key, value) {
                            if (keys.event_data.home_team_id == key.id) {
                                keys.home_team_data = key
                            }
                        });
                    }
                    if (away_team_data) {
                        away_team_data.forEach(function(key, value) {
                            if (keys.event_data.away_team_id == key.id) {
                                keys.away_team_data = key
                            }
                        });
                    }
                    //for over_under_home
                    homeAry = keys.event_data.over_under_home_bet.split("");
                    if (homeAry[0] == "大" || homeAry[0] == "小") {
                        keys.over_under_home_bet_size = homeAry[0]
                        homeAry.shift()
                        keys.over_under_home_bet_detail = homeAry.join("")
                    } else {
                        keys.over_under_home_bet_detail = keys.event_data.over_under_home_bet
                    }
                    //for over_under_away
                    awayAry = keys.event_data.over_under_away_bet.split("");
                    if (awayAry[0] == "大" || awayAry[0] == "小") {
                        keys.over_under_away_bet_size = awayAry[0]
                        awayAry.shift()
                        keys.over_under_away_bet_detail = awayAry.join("")
                    } else {
                        keys.over_under_away_bet_detail = keys.event_data.over_under_away_bet
                    }
                    //for single bet choice
                    //if (keys.single_home) keys.single_detail = keys.home_team_data.name_zh
                    //else if (keys.single_away) keys.single_detail = keys.away_team_data.name_zh
                    if (keys.single_home) {
                        keys.single_detail = "主"
                        keys.single_detail_bet = "home"
                    } else if (keys.single_away) {
                        keys.single_detail = "客"
                        keys.single_detail_bet = "away"
                    } else if (keys.single_tie) {
                        keys.single_detail = "和"
                        keys.single_detail_bet = "tie"
                    }
                    /*else {
                        keys.single_detail = "-"
                        keys.single_detail_bet = "home"
                    }*/
                });
            }
            if (response['data'] == null && !response['data'].length) {
                window.userPredictionEventIdAry = []
                window.userPredictionResultAry = []
            }
            if (response['data'] != null && response['data'].length) {
                response['data'] = response['data'].filter(item => {
                    return item.status != 'predicted'
                })
            }
            return $.get('/cn/match-prediction/mp-modal.html').then(html => {
                return {
                    html,
                    response
                }
            })
        }, error => {
            alert('AJAX ERROR - get match my_mpModal');
        }).then(res => {
            const { html, response } = res
            var obj = {}
            obj["matchPredictions"] = response['data']
            $("#userPredictionNumber").text(response['data'].length)
            var render = template.compile(html);
            var str = render(obj);
            $('#my_mpModal').html(str);
            main_area()
        })
}

function deleteUserPrediction(itemId) {
    if (!itemId) return ""

    var delete_item_func =
        callAjaxFunc('DELETE', {
                id: itemId
            },
            '/api/cn/prediction'
        )
    $.when(delete_item_func).done(function() {
        $("#my_mpModal .modal-body").load(location.href + " #my_mpModal>*", function() {
            mpModal()
            $("#my_mpModal").modal("show");
        });
    });
}

function deleteFav(itemId) {
    if (!itemId) return ""
    return callAjaxFunc('DELETE', {
            id: itemId
        },
        '/api/cn/prediction_user_favourite'
    )
}

function storeFav(user_fav) {
    if (!user_fav) return ""
    var user_fav_data = user_fav.split("-");
    fav_data = {
        prediction_id: user_fav_data[2],
        user_id: window.localStorage.user_id,
        prediction_type: user_fav_data[0],
        prediction_bet: user_fav_data[1],
    }
    $.ajax({
        type: "POST",
        url: link + 'api/cn/prediction_user_favourite',
        data: JSON.stringify(fav_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        error: function() {
            alert('AJAX ERROR - user favorite');
        },
    });
}

function getUserFavorite() {
    return callAjaxFunc(
        'GET', {},
        '/api/cn/prediction_user_favourite'
    )
}

function callAjaxFunc(method, data, url) {
    return $.ajax({
        type: method,
        url: link + url,
        data: JSON.stringify(data),
        crossDomain: true,
        headers: getHeaders(),
        //headers: { Authorization: token },
        contentType: false,
        processData: false
    });
}

function editUserPrediction(event_id) {
    window.match_id = event_id
    $('#my_mpModal').modal('hide');
    $('.modal-backdrop').remove();

    main_area()
    $(".match_prediction_p2 ").load(location.href + " .match_prediction_p2 >*", "");
    $('html,body').animate({ scrollTop: $(".match_prediction_p2 ").offset().top }, 'slow');
}

function updateUserPrediction(flag) {
    if (check_auth(0)) return ""
    var up_predictionId = ""
    var method = ""
        //if (flag.handicap && flag.over_under && flag.single){
    update_data = {
        event_id: window.match_id,
        user_id: window.localStorage.user_id,
        handicap_home: flag.handicap == "handicap_home" ? 1 : 0,
        handicap_away: flag.handicap == "handicap_away" ? 1 : 0,
        over_under_home: flag.over_under == "over_under_home" ? 1 : 0,
        over_under_away: flag.over_under == "over_under_away" ? 1 : 0,
        single_home: flag.single == "single_home" ? 1 : 0,
        single_away: flag.single == "single_away" ? 1 : 0,
        single_tie: flag.single == "single_tie" ? 1 : 0,
    }
    if (window.userPredictionEventIdAry) {
        window.userPredictionEventIdAry.forEach(function(key, value) {
            var newKey = key.split("-")
            if (newKey[0] == window.match_id) {
                up_predictionId = newKey[1]
            }
        })
    }
    if (up_predictionId) {
        update_data.id = up_predictionId
        method = "PUT"
    } else {
        method = "POST"
    }
    var updateFunc = $.ajax({
        type: method,
        url: link + 'api/cn/prediction',
        data: JSON.stringify(update_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    }).then(response => {
        if (response.code == 1) {
            alert("已加入预测选单！\r\n记得点选右上角 “查看” 按钮，并确认送出选项哟～")
        } else {
            alert(response.message)
        }
        flag = {}
        $("#my_mpModal .modal-body").load(location.href + " #my_mpModal>*", function() {
            mpModal()
        });
    }, error => {
        alert('AJAX ERROR - update user prediction');
    });
    //}
}

function updateUserPredictionFromTopTen(flag) {
    if (check_auth(0)) return ""
    var up_predictionId = ""
    var method = ""

    flag.forEach(function(keys) {
        if ((keys == "handicap_home") || (keys == "handicap_away")) flag.handicap = keys
        if ((keys == "over_under_home") || (keys == "over_under_away")) flag.over_under = keys
        if ((keys == "single_home") || (keys == "single_away") || (keys == "single_tie")) flag.single = keys
    })
    update_data = {
        event_id: window.match_id,
        user_id: window.localStorage.user_id,
        handicap_home: flag.handicap == "handicap_home" ? 1 : 0,
        handicap_away: flag.handicap == "handicap_away" ? 1 : 0,
        over_under_home: flag.over_under == "over_under_home" ? 1 : 0,
        over_under_away: flag.over_under == "over_under_away" ? 1 : 0,
        single_home: flag.single == "single_home" ? 1 : 0,
        single_away: flag.single == "single_away" ? 1 : 0,
        single_tie: flag.single == "single_tie" ? 1 : 0,
    }
    if (window.userPredictionEventIdAry) {
        window.userPredictionEventIdAry.forEach(function(key, value) {
            var newKey = key.split("-")
            if (newKey[0] == window.match_id) {
                up_predictionId = newKey[1]
            }
        })
    }
    if (up_predictionId) {
        update_data.id = up_predictionId
        method = "PUT"
    } else {
        method = "POST"
    }
    var updateFunc = $.ajax({
        type: method,
        url: link + 'api/cn/prediction',
        data: JSON.stringify(update_data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    }).then(response => {
        if (response.code == 1) {
            alert("已加入预测选单！\r\n记得点选右上角 “查看” 按钮，并确认送出选项哟～")
        } else {
            alert(response.message)
        }
        flag = {}
        $("#my_mpModal .modal-body").load(location.href + " #my_mpModal>*", function() {
            mpModal()
        });
        $('#member_list_more').modal('hide');
        //$('.modal-backdrop').remove();
        main_area()
        $(".match_prediction_p2 ").load(location.href + " .match_prediction_p2 >*", "");
    }, error => {
        alert('AJAX ERROR - update user prediction top ten');
    });
}

function promptSuccess() {
    $('#my_mpModal').modal('hide');
    $('.modal-backdrop').remove();
    var predictedArray = []
    if (window.userPredictionEventIdAry) {
        window.userPredictionEventIdAry.forEach(function(key, value) {
            var newKey = key.split("-")
            predictedArray.push(newKey[1])
        })
        updatePredictedStatus(predictedArray)
    }
}

function updatePredictedStatus(predictedArray) {
    if (predictedArray.length < 1) return alert("请先预测！")
    var item = {}
    var update_data = { data: [] };
    $.each(predictedArray, function(i, entry) {
        item['id'] = entry;
        item['status'] = 'predicted'
        update_data.data.push(item);
        item = {};
    });
    var update_item_func =
        $.ajax({
            type: "PATCH",
            url: link + 'api/cn/prediction',
            data: JSON.stringify(update_data),
            crossDomain: true,
            headers: getHeaders(),
            contentType: false,
            processData: false,
        }).then(response => {
            if (response.status == 'failed') alert(response.message)
            else alert("已成功送出所有预测选项！")
        }, error => {
            alert('AJAX ERROR - update predicted status');
        }).then(_ => {
            mpModal()
        })
}

function closeTutorialButton() {
    $("#tut_popup").removeClass("in");
    $(".modal-backdrop").remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
    $("#tut_popup").hide();
}

function checkFirstTimeEnter() {
    var matchPredictionPageIsVisited= window.localStorage.matchPredictionPageIsVisited
    var matchPredictionPageIsVisitedForUser = window.localStorage[`matchPredictionPageIsVisitedForUser-${window.localStorage.user_id}`]
    if (!matchPredictionPageIsVisited && !window.localStorage.user_id) {
        $("#tut_popup").modal("show")
        window.localStorage.matchPredictionPageIsVisited = true
    }else if (!matchPredictionPageIsVisitedForUser && window.localStorage.user_id) {
        $("#tut_popup").modal("show")
        window.localStorage[`matchPredictionPageIsVisitedForUser-${window.localStorage.user_id}`] = true
    }
}
