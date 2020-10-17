function submitPromoRedeem($form_selector) {
    //console.log($($form_selector).serialize());

    var user_id = window.localStorage.user_id;
    if (!user_id) {
        $("#promoModal").modal("hide");
        $("#login_popup").modal("show");
        return;
    }

    var requestObj = {};
    $.each($($form_selector).serializeArray(), function () {
        requestObj[this.name] = this.value;
    });

    $.when(getUserDetail()).done(function (response) {
        var user_data = response.data[0];

        //console.log(user_data);

        $.ajax({
            type: 'POST',
            url: link + '/api/cn/promotion_redeem',
            data: JSON.stringify(requestObj),
            crossDomain: true,
            contentType: false,
            processData: true,
            success: function (response) {
                if (response['code']) {
                    $("#promoModal").modal("hide");
                    window.alert(response.message);
                } else {
                    window.alert("There's an error occured. ");
                }
            }
        });
    });
}

function triggerModal($promo_id) {
    //console.log("triggerModal - " + $promo_id);

    getPromoDetail($promo_id).then(response => {
        var promotion = response.data[0];

        //console.log(promotion);

        if (promotion.sign_up) {
            $('#promo-modal-submit').hide();
        } else {
            $('#promo-modal-submit').show();
        }

        $("#promo-modal-id").val($promo_id);
        $("#promo-modal-title").html(promotion.name);
        $("#promo-modal-content").html(promotion.introduction);

        $("#promoModal").modal("show");
    });
}

function imageRedirectModal($promo_id) {

    getPromoDetail($promo_id).then(response => {
        var promotion = response.data[0];
        //console.log(promotion);

        var url = promotion.url;
        var displayType = promotion.display_method;

        if(displayType == "url" && url != ""){
            //window.location.assign(url);
            window.open(url);
        }else{

        }

    });
}

function fixed_promotion_list($limit = 3) {
    $('#promotion_fixed').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    filter.push({field: "promotion.disabled", value: 0, operator: "="});
    filter.push({field: "promotion.system", value: 1, operator: "="});
    filter.push({field: "promotion.start_at", value: getCurrentDate(), operator: "<"});
    filter.push({field: "promotion.end_at", value: getCurrentDate(), operator: ">"});

    var promotion_list = $.ajax({
        url: link + 'api/cn/promotion',
        header: getHeaders(),
        type: 'get',
        data: {
            filter: filter,
            sort: {
                field: 'promotion.created_at',
                sort: 'DESC'
            },
            label: 'promotion-list',
        },
        success: function (response) {
            var obj = {
                promotion_list: response.data
            };
        }
    });

    //console.log(promotion_list);

    $.when(promotion_list).done(function (promotion_list) {
        let obj = [];
        obj['promotion_list'] = promotion_list.data;

        //console.log(obj);

        var html = $.get('/cn/module/promotion_fixed_item.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#promotion_fixed').html(str);
        });
    });
}

function promotion_list($limit = 10) {
    $('#promotion_carousel').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    filter.push({field: "promotion.disabled", value: 0, operator: "="});
    filter.push({field: "promotion.system", value: 0, operator: "="});
    filter.push({field: "promotion.start_at", value: getCurrentDate(), operator: "<"});
    filter.push({field: "promotion.end_at", value: getCurrentDate(), operator: ">"});

    var promotion_list = $.ajax({
        url: link + 'api/cn/promotion',
        header: getHeaders(),
        type: 'get',
        data: {
            filter: filter,
            sort: {
                field: 'promotion.created_at',
                sort: 'DESC'
            },
            label: 'promotion-list',
        },
        success: function (response) {
            var obj = {
                promotion_list: response.data
            };
        }
    });

    //console.log(promotion_list);

    $.when(promotion_list).done(function (promotion_list) {
        let obj = [];
        obj['promotion_list'] = promotion_list.data;

        //console.log(obj);

        var html = $.get('/cn/module/promotion_item.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#promotion_carousel').html(str);
        });
    });
}

function getCurrentDate() {
    var dt = new Date();
    var currentDateTime = `${dt.getFullYear().toString().padStart(4, '0')}-${(dt.getMonth() + 1).toString().padStart(2, '0')}-${dt.getDate().toString().padStart(2, '0')}`;
    return currentDateTime
}

function retrieveUserInfo() {
    //console.log("retrieveUserInfo()");
    getUserDetail().then(response => {
        var user_data = response.data[0];
        // updateCurrentPoints(user_data);
        fixed_promotion_list($user_data);
        promotion_list(user_data);
    });
}

function getPromoDetail($id) {
    var user_id = window.localStorage.user_id;

    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/promotion',
        data: {
            filter: [
                {field: "promotion.id", value: $id, operator: "="}
            ],
            label: "getPromoDetail()"
        },
        crossDomain: true,
        contentType: false,
        processData: true
    });
}

function getUserDetail() {
    var user_id = window.localStorage.user_id;

    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/user',
        data: {
            filter: [
                {field: "user.id", value: user_id, operator: "="}
            ],
            label: "getUserDetail()"
        },
        crossDomain: true,
        contentType: false,
        processData: true
    });
}
