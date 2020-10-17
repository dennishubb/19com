// window.localStorage.user_id = 30;
// window.localStorage.access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjozMCwiZXhwIjoxNTkxMDc0MjI1LCJpc3MiOiIxOWNvbSIsImlhdCI6MTU5MTA2NzAyNX0.684aglxDLIWP2ZQjSSxKZfrTTz9q52IP_pCgcUQ5o2A";

$saved_forms = [];

function addGiftRedeem($form_selector) {
    var unindexed_array = $($form_selector).serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    //console.log(indexed_array);

    postGiftRedeem(indexed_array).then(response => {
        //console.log(response);
        $("#exchangeModal").modal("hide");
        if (response.code == 1) {
            /*window.alert(response.message);
            window.location.href = "/cn/exchange.html";*/
            confirm(response.message).then(result => {
                window.location.href = "/cn/exchange.html";
            });
        } else if (response.code==401) {
            $("#login_popup").modal("show");
        } else {
            /*if (window.confirm(response.message)) {
                if (response.redirect) {
                    //console.log("redirect---");
                    window.location.href = "/cn/profile/index.html?complete_info=1";
                }
            }*/
            confirm(response.message).then(result => {
                const confirmed = result.confirmed
                if(!confirmed){
                        return;
                }
                else {
                    if (response.redirect) {
                        //console.log("redirect---");
                        window.location.href = "/cn/profile/index.html?complete_info=1";
                    }
                }
            });
        }
    });
}

function triggerModalProgress($selector, $modal_show = true) {
// $id = $($selector).attr('data-id');
    //console.log("triggerModalProgress()");

    getGiftRedeem().then(response => {

        //console.log(response);
		if (response.code==401) {
            $("#login_popup").modal("show");
        } 
		else{
			$('#progressModalBody').html('');

			var obj = {}
			obj["gift_redeems"] = response['data'];
			obj["total_page"] = response["totalPage"];
			obj["current_page"] = window.gift_redeem_page_no ? window.gift_redeem_page_no : 1;

			var html = $.get('/cn/module/exchange_progress_list.html', function (data) {
				var render = template.compile(data);
				var str = render(obj);

				$('#progressModalBody').html(str);

				if ($modal_show) $("#progressModal").modal("show");
			});
		}
    });
}

function triggerModalGiftRedeem($selector) {
    $id = $($selector).attr('data-id');
    //console.log("triggerModal() - " + $id);

    getGift($id).then(response => {
        let gift_data = response.data[0];
        //console.log(gift_data);

        var sizePicker = $('#modal_size');
        var colourPicker = $('#modal_colour');

        $("input[name=gift_id]").val($id);
        $("#modal_product_name").html(gift_data.name);

        sizePicker.empty();
        sizePicker.removeAttr('disabled');
        gift_data.size.forEach(size => {
            sizePicker.append($(`<option value="${size}">${size}</option>`));
        });

        if (gift_data.size.length == 0) sizePicker.attr('disabled', 'disabled');

        colourPicker.empty();
        colourPicker.removeAttr('disabled');
        gift_data.color.forEach(color => {
            colourPicker.append($(`<option value="${color}">${color}</option>`));
        });

        if (gift_data.color.length == 0) colourPicker.attr('disabled', 'disabled');

        $("#exchangeModal").modal("show");
    });
}

function filter_gift($form_id = $('#filter_gift')) {
    $form = $('#filter_gift').serializeArray();
    //console.log($form);

    $saved_forms = $form;

    exchange_list($form[0]['value'], $form[1]['value'], $form[2]['value'], $form[3]['value']);
}

function filter_gift_tags($tag) {
    $form = $('#filter_gift').serializeArray();
    //console.log($form);

    $saved_forms = $form;

    exchange_list($form[0]['value'], $form[1]['value'], $form[2]['value'], $form[3]['value'], $tag);
}

function sort_gift($selector) {
    $sort = $($selector).val();
    //console.log($sort);

    if ($saved_forms.length > 0) {
        exchange_list(
            $saved_forms[0]['value'],
            $saved_forms[1]['value'],
            $saved_forms[2]['value'],
            $saved_forms[3]['value'],
			0,
            $sort
        );
    } else {
        exchange_list(0, 0, 0, 0, 0, $sort);
    }
}

function getCurrentDate() {
    var dt = new Date();
    var currentDateTime = `${dt.getFullYear().toString().padStart(4, '0')}-${(dt.getMonth() + 1).toString().padStart(2, '0')}-${dt.getDate().toString().padStart(2, '0')}`;
    return currentDateTime
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

function exchange_list($category_id = 0, $sub_category_id = 0, $range_from = 0, $range_to = 0, $tag = 0, $sort = "ASC") {
    $('#exchange_list').html("");

    var filter = [{field: "gift.disabled", value: 0, operator: "="}];

    filter.push({field: "gift.start_at", value: getCurrentDate(), operator: "<="});
    filter.push({field: "gift.end_at", value: getCurrentDate(), operator: ">="});

    if ($category_id != 0) {
        filter.push({field: "gift.category_id", value: $category_id, operator: "="});
    }
    if ($sub_category_id != 0) {
        filter.push({field: "gift.sub_category_id", value: $sub_category_id, operator: "="});
    }
    if ($range_from) {
        filter.push({field: "gift.points", value: $range_from, operator: ">="});
    }
    if ($range_to) {
        filter.push({field: "gift.points", value: $range_to, operator: "<="});
    }

    if ($tag) {
        filter.push({field: "gift.hot_category", value: "%" + $tag + "%", operator: "LIKE"});
    }

    var exchange_list = $.ajax({
        url: link + 'api/cn/gift',
        type: 'get',
        data: {
            filter: filter,
            sort: {
                field: 'gift.points',
                sort: $sort
            },
            label: 'exchange-list',
        },
        success: function (response) {
            var obj = {
                exchange_list: response.data
            };
        }
    });

    //console.log(exchange_list);

    $.when(exchange_list).done(function (exchange_list) {
        let obj = [];
        obj['exchange_list'] = exchange_list.data;
        if ($('select[name=category_id] :selected').val() != 0) {
            obj['category'] = $('select[name=category_id] :selected').text();
        }
        if ($('select[name=sub_category_id] :selected').val() != 0) {
            obj['sub_category'] = $('select[name=sub_category_id] :selected').text();
        }

        // obj['sub_category'] = "Sub Category";
        obj['category_id'] = ($category_id != 0) ? true : false;
        obj['sub_category_id'] = ($sub_category_id != 0) ? true : false;

        obj['sort_by'] = $sort;
        obj['hot_category_tags'] = exchange_list.hot_category_tags;

        //console.log(obj);

        var html = $.get('/cn/module/exchange_list.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#exchange_list').html(str);

            $('select[name=sorting]').val($sort);

            // //console.log("size : " + exchange_list.hot_category_tags.length);

            var tags_html = "";
            // obj['hot_category_tags'].forEach(function (entry, index) {
            //     tags_html += "<div style='margin-left: 2px;margin-right: 2px;'>";
            //     tags_html += "<a href='javascript:filter_gift_tags(`" + entry.toString() + "`)'>#" + entry + "</a>";
            //     tags_html += "</div>";
            // });
            $.each(obj['hot_category_tags'], function (key, value) {
                tags_html += "<div style='margin-left: 2px;margin-right: 2px;'>";
                tags_html += "<a href='javascript:filter_gift_tags(`" + value.toString() + "`)'>#" + value + "</a>";
                tags_html += "</div>";
            });
            //console.log(tags_html);

            $('#tags_area').html(tags_html);

        });
    });

}

function retrieveUserInfo() {
    //console.log("retrieveUserInfo()");
    getUserDetail().then(response => {
        var user_data = response.data[0];

        updateCurrentPoints(user_data);
    });
}

function updateCurrentPoints(user_data) {
    //console.log("updateCurrentPoints() - " + JSON.stringify(user_data));
    var pointField = $('#user_current_points');

    pointField.html(Math.round(user_data.points));
}

function update_category($id = $('#gift_category')) {
    var categoryPicker = $('select[name=category_id]');

    categoryPicker.empty();
    categoryPicker.append($(`<option value="0">全部</option>`));
    getAllMainCategories().then(response => {
        response.data.forEach(category => {
            categoryPicker.append($(`<option value="${category.id}">${category.display}</option>`));
        });

        update_sub_category(categoryPicker.val());
    });
}

function update_sub_category($parent_id, $id = $('#gift_sub_category')) {
    var subCategoryPicker = $('select[name=sub_category_id]');

    subCategoryPicker.empty();
    subCategoryPicker.append($(`<option value="0">全部</option>`));

    //console.log($parent_id);

    if ($parent_id == 0) return;

    getSubCategory($parent_id).then(response => {
        response.data.forEach(category => {
            subCategoryPicker.append($(`<option value="${category.id}">${category.display}</option>`));
        });
    });
}

function getGiftRedeem() {
    var user_id = window.localStorage.user_id;

	var filter = [{
        field: 'user_id',
        value: user_id,
        operator: '=',
    }];
	
    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/gift_redeem',
        data: {
			filter: filter,
            limit: 10,
            page_number: window.gift_redeem_page_no ? window.gift_redeem_page_no : 1
        },
		
        crossDomain: true,
        contentType: false,
        processData: true
    });
}

function getGift(gift_id) {
    var user_id = window.localStorage.user_id;

    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/gift',
        data: {
            filter: [
                {field: "gift.id", value: gift_id, operator: "="}
            ]
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

function getAllMainCategories() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=gift&search[parent_id]=0'
    )
}

function getSubCategory($parent_id) {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=gift&search[parent_id]=' + $parent_id
    )
}

function postGiftRedeem($data) {
    return $.ajax({
        type: "POST",
        url: getBackendHost() + '/api/cn/gift_redeem',
        data: JSON.stringify($data),
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
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
