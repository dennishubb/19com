function getSelectedIds() {
    var datatable = $('#main-datatable').KTDatatable();

    return datatable
        .getSelectedRecords()
        .find('td[data-field="user_id"] input[type=checkbox]')
        .toArray()
        .map(function (input) {
            return parseInt($(input).val())
        });
}

function populateUserSelection() {
    $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/user',
        crossDomain: true,
        headers: getHeaders(),
        data: {filter: [{field: "user.role_id", value: "1", operator: "!="}]},
        contentType: false,
        processData: true
    }).then(
        response => {
            var userSelect = $('#users_select');

            //console.log(response.data);

            var users = response.data;
            users.forEach(user => {
                userSelect.append($(`<option value="${user.id}">${user.id}-${user.username}</option>`))
            });

            userSelect.selectpicker('refresh');
        }
    );
}

function filterAdjustment($form = "#filter_form") {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = [];

    //console.log(unindexed_array);

    var points_after_operator = "";
    var voucher_after_operator = "";

    indexed_array.push({field: "latest", value: 1, operator: "="});
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
            } else if (n['name'].includes("_operator")) {
                if (n['name'].includes("points")) {
                    points_after_operator = $.trim($value);
                } else if (n['name'].includes("voucher")) {
                    voucher_after_operator = $.trim($value);
                }
                $field = false;
                $value = false;
                $operator = false;
            }

            if ($field && $value && $operator) {
                indexed_array.push({
                    field: $field,
                    value: $value,
                    operator: $operator,
                });
            }

        }
    });

    //console.log(points_after_operator);
    //console.log(voucher_after_operator);

    var datatable = $("#main-datatable").KTDatatable();
    datatable.API.params = {};

    $.map(indexed_array, function (n, i) {
        if (n['field'].includes("points_after")) {
            if (points_after_operator === "-") {
                n['value'] = points_after_operator + n['value'];
            }
        } else if (n['field'].includes("voucher_after")) {
            if (voucher_after_operator === "-") {
                n['value'] = voucher_after_operator + n['value'];
            }
        }

        datatable.setDataSourceParam('filter[' + i + '][field]', n['field']);
        datatable.setDataSourceParam('filter[' + i + '][value]', n['value']);
        datatable.setDataSourceParam('filter[' + i + '][operator]', n['operator']);
    });

    //console.log("filterAdjustment : " + JSON.stringify(indexed_array));

    datatable.reload();
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params[paramName]
}

function massageAdjustment($form = "#adjustment_form", $redirect_url = "/cn/backend-admin/point-management.html", $error_selector = $(".message_output")) {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = {data: []};

    var ids = [];
    var points_op = unindexed_array.find(x => x.name === 'points_operator')['value'];
    var voucher_op = unindexed_array.find(x => x.name === 'voucher_operator')['value'];
    var points_val = ($.trim(points_op).toString() == "+" ? '' : $.trim(points_op).toString()) + unindexed_array.find(x => x.name === 'points')['value'];
    var voucher_val = ($.trim(voucher_op).toString() == "+" ? '' : $.trim(voucher_op).toString()) + unindexed_array.find(x => x.name === 'voucher')['value'];

    $.map(unindexed_array, function (n, i) {
        //console.log(n);
        if (n['name'].includes("users[]")) {
            ids.push(n['value']);
        }
    });

    //console.log(ids);
    //console.log(points_val);
    //console.log(voucher_val);

    if(points_val === "") points_val = 0;
    if(voucher_val === "") voucher_val = 0;

    ids.forEach(function (id) {
        indexed_array.data.push({
            user_id: id,
            points_id: 1,
            points: points_val,
            voucher_id: 2,
            voucher: voucher_val,
            remark: ""
        });
    });

    if (indexed_array.data.length > 0) {

        //console.log(indexed_array);
        batchCreate(indexed_array).then(function (response) {
            //console.log(response);

            if (response.code == 1) {

                if (response.redirect) redirect_to($redirect_url + "?alert-success=success");

            } else {

                showAlert(response.message, "danger", $error_selector);

            }
        });

    } else {

        showAlert("User not selected", "danger", $error_selector);

    }
}

function getUserHistory(user_id = "") {
    var modal_datatable = $("#modal-datatable").KTDatatable();

    modal_datatable.setDataSourceParam('filter[0][field]', 'user_id');
    modal_datatable.setDataSourceParam('filter[0][value]', user_id);
    modal_datatable.setDataSourceParam('filter[0][operator]', '=');

    modal_datatable.setDataSourceParam('sort[field]', 'adjustment.created_at');
    modal_datatable.setDataSourceParam('sort[sort]', 'desc');

    modal_datatable.load();
}


function batchCreate(parsedData, method = "PATCH", url = "/api/cn/adjustment") {
    return $.ajax({
        type: method,
        url: getBackendHost() + url,
        data: JSON.stringify(parsedData),
        crossDomain: true,
        headers: getHeaders(),
        contentType: 'application/json',
        processData: false
    });
}

function filterEvent($form = "") {
    var unindexed_array = $($form).serializeArray();
    var indexed_array = [];

    $id = "";

    indexed_array.push({field: "latest", value: 1, operator: "="});
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

function unique(array) {
    return $.grep(array, function (el, index) {
        return index === $.inArray(el, array);
    });
}