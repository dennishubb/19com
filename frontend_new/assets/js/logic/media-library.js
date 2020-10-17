$media_action = '/api/cn/upload';

$(document).ready(function () {
    getAlert();

    // Retrieving Uploads
    ajax_retrieve_callback(
        config,
        function (response, status, xhr) {
            //console.log(response);
            populateMediaLibrary(response);

            unblockUI();
        }
    );

    // Retrieving Categories
    ajax_retrieve_callback(
        {
            r_url: "/api/cn/category",
            params: {
                search: {
                    parent_id: 0,
                    disabled: 0
                },
            }
        },
        function (response, status, xhr) {
            //console.log(response);

            categories = response.data;

            $(".media-category, #select-category").html('');

            $("#select-category").append(
                $('<option></option>')
                    .attr('value', "")
                    .text("全部类型")
            );

            $.each(categories, function (key, entry) {
                $(".media-category, #select-category").append(
                    $('<option></option>')
                        .attr('value', entry.id)
                        .text(entry.display)
                );
            });
            $('.media-category, #select-category').selectpicker('refresh');

            unblockUI();

        }
    );

});


var config = {
    r_url: "/api/cn/upload",
    params: {
        sort: {
            field: 'upload.created_at',
            sort: 'desc'
        },
        label: 'media-library',
    }
};

function filterMediaCategory() {
    $.ajax({
        url: getBackendHost() + config.r_url,
        type: 'GET',
        data: {
            search: {
                type: current_media,
                category_id: current_category
            },
            filter: [
                {
                    field: "upload.name",
                    value: "%" + current_search + "%",
                    operator: "LIKE",
                }
            ],
            sort: {
                field: 'upload.created_at',
                sort: 'desc'
            },
            label: 'media-library',
        },
        crossDomain: true,
        contentType: false,
        processData: true,
        success: function (response, status, xhr) {
            //console.log(response);

            if (response.code == -1) {
                redirect_to_login();
                return;
            }

            $('#gallery-tile-wrapper').nanogallery2('destroy');
            populateMediaLibrary(response);

            unblockUI();
        },
        error: function () {
            alert('Problem occurred while sending request.');
        }
    });
}

var current_search = "";

function searchMedia($obj) {
    var searchValue = $($obj).val();
    current_search = searchValue;

    filterMediaCategory();
}

var current_media = "";

function filterMedia($obj) {
    var filterValue = $($obj).val();
    current_media = filterValue;

    filterMediaCategory();
}

var current_category = "";

function filterCategory($obj) {
    var filterValue = $($obj).val();
    current_category = filterValue;

    filterMediaCategory();
}

function filterByItem() {
    current_search = $("#searchGallery").val();
    current_media = $("#filterMedia").val();
    filterMediaCategory();
}

function populateMediaLibrary($resp) {
    var resp_item = [];

    $('#gallery-tile-wrapper').html("");

    $.each($resp.data, function (i, entry) {
        if (entry && entry.url) {

            var description = '文件名：' + entry.name + '、文件类型：' + entry.type + '、上传于：' + entry.created_at + '、\r\n文件大小：' + entry.size + ' KB、分辨率：' + entry.resolution + ' 像素';

            var name = entry.alt;

            resp_item.push(
                {
                    id: entry.id,
                    src: '../../' + entry.url,
                    srct: '../../' + entry.url,
                    title: name,
                    description: description,
                    tags: entry.type + '_' + entry.category_id,
                    customData: {
                        id: entry.id,
                        type: entry.type,
                        category: entry.category_id,
                    }
                }
            );

        } else {
            //console.log("missing id : " + entry.id);
        }

    });

    // $('#gallery-tile-wrapper').nanogallery2('destroy');
    $('#gallery-tile-wrapper').nanogallery2({
        items: resp_item,
        galleryDisplayMode: 'fullContent',
        thumbnailWidth: 246,
        thumbnailHeight: 246,
        thumbnailAlignment: 'left',
        thumbnailSelectable: true,
        // gallerySorting: 'reverse',
        thumbnailToolbarImage: {topRight: 'info'},
    });
}


function selectAll() {
    var selected = [];
    var datas = $("#gallery-tile-wrapper").nanogallery2('data');
    datas.items.forEach(function (item) {
        selected.push(item);
    });
    $("#gallery-tile-wrapper").nanogallery2('itemsSetSelectedValue', selected, true);
}

function deselectAll() {
    $("#gallery-tile-wrapper").nanogallery2('itemsSetSelectedValue', getSelected(), false);
}

function getSelected() {
    var selected = [];
    var datas = $("#gallery-tile-wrapper").nanogallery2('data');
    datas.items.forEach(function (item) {
        if (item.selected) {
            selected.push(item);
        }
    });
    return selected;
}

function getSelectedIds() {
    var selected = [];
    $.each(getSelected(), function (i, entry) {
        selected.push(entry.customData.id);
    });
    return selected;
}

function save_media($extra_data, $error_selector = $(".message_output")) {

    //console.log("save_media - " + JSON.stringify($extra_data));

    $item = {
        tempfile: $extra_data,
    };

    var form_data = new FormData();
    form_data.append("tempfile", $extra_data);

    $redirect_uri = 'media-library-v2.html';

    $.ajax({
        type: 'POST',
        url: getHost() + '/assets/php/media-save.php',
        contentType: false,
        cache: false,
        headers: getHeaders(),
        processData: false,
        data: form_data,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {
                if (obj.message) {
                    redirect_to($redirect_uri + "?alert-success=" + obj.message);
                } else {
                    redirect_to($redirect_uri);
                }
            }
        },
        error: function (resp) {
            //console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function appendFormdata(FormData, data, name) {
    name = name || '';
    if (typeof data === 'object') {
        $.each(data, function (index, value) {
            if (name == '') {
                appendFormdata(FormData, value, index);
            } else {
                appendFormdata(FormData, value, name + '[' + index + ']');
            }
        })
    } else {
        FormData.append(name, data);
    }
}

function delete_media($extra_data, $error_selector = $(".message_output")) {

    var form_data = new FormData(),
        form_object = {
            filename: $extra_data
        };

    appendFormdata(form_data, form_object);

    $.ajax({
        type: 'POST',
        url: getHost() + '/assets/php/media-batch-delete.php',
        contentType: false,
        cache: false,
        headers: getHeaders(),
        processData: false,
        data: form_data,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {
                if (obj.message) {
                    redirect_to($redirect_uri + "?alert-success=" + obj.message);
                } else {
                    redirect_to($redirect_uri + "?alert-success=successful delete");
                }
            } else {
                if (obj.message) {
                    showAlert(obj.message, "danger", $error_selector);
                } else {
                    showAlert("Action unable to complete", "danger", $error_selector);
                }
            }
        },
        error: function (resp) {
            //console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function local_media_ajax_submit($form, $error_selector = $(".message_output")) {

    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    $_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);

    clearAlert($error_selector);

    //console.log(getHost() + $action);
    //console.log($_form);


    $.ajax({
        type: ($method) ? $method : 'POST',
        url: getHost() + $action,
        contentType: false,
        cache: false,
        processData: false,
        data: $formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {
                backend_media_ajax_submit(
                    {
                        url: $media_action,
                        method: "POST",
                        redirect: $redirect_uri,
                        params: {
                            category_id: $formData.get("category"),
                            alt: response.data.alt,
                            extension: response.data.extension,
                            filesize: response.data.filesize,
                            name: response.data.name,
                            resolution: response.data.resolution,
                            type: response.data.type,
                            url: response.data.url,
                            md5: response.data.md5,
                            extra: response.extra.toString(),
                        }
                    },
                    $error_selector
                );
            } else {
                //console.log("[0] - " + response.message);
                showAlert(response.message, "danger", $error_selector);
            }


        },
        error: function (resp) {
            //console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });

}

function backend_media_ajax_submit($data, $error_selector = $(".message_output")) {

    $action = $data.url;
    $method = $data.method;
    $redirect_uri = $data.redirect;

    //console.log(getBackendHost() + $action);

    $.ajax({
        type: ($method) ? $method : 'POST',
        url: getBackendHost() + $action,
        crossDomain: true,
        contentType: 'application/json',
        processData: false,
        data: JSON.stringify($data.params),
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {

                switch ($method) {
                    case "POST":
                        save_media($data.params.extra, $error_selector)
                        break;
                }

            } else {
                showAlert(response.message, "danger", $error_selector);
                return;
            }
        },
        error: function (resp) {
            //console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function change_category($edit_modal) {
    $($edit_modal).modal('show');
}

function bulk_delete() {
    var status = confirm("确认删除？有些媒体是文章所上传的照片。");
    return status;
}

function bulkMediaEdit($form, selected_action, $error_selector = $(".message_output")) {
    //console.log("bulkMediaEdit - " + selected_action);

    var json_request = {data: []};
    var item = {};

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    //console.log("action : " + $action);
    //console.log("method : " + $method);
    //console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);

    if (selected_action == "bulk_edit_category") {

        category = $formData.get('edit_category');
        alt = $formData.get('alt');
        $.each(getSelectedIds(), function (i, entry) {
            //console.log(entry);

            item['id'] = entry;
            item['category_id'] = category;
            item['alt'] = alt;

            json_request.data.push(item);
            item = {};
        });

    } else if (selected_action == "bulk_delete") {

        $.each(getSelectedIds(), function (i, entry) {
            //console.log(entry);

            item['id'] = entry;

            json_request.data.push(item);
            item = {};
        });

        json_request['action'] = "delete";

    }

    var formData = JSON.stringify(json_request);
    //console.log(formData);

    clearAlert();

    $.ajax({
        type: ($method) ? $method : 'PATCH',
        url: getBackendHost() + $action,
        // url: link + $action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {

                if (response.extra.action == "delete") {
                    delete_media(obj.urls);
                    return;
                }

                if (obj.redirect && obj.message) {
                    redirect_to($redirect_uri + "?alert-success=" + obj.message);
                } else {
                    redirect_to($redirect_uri);
                }
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                if (obj.message) {
                    showAlert(obj.message, "danger", $error_selector);
                }
            }
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });

}
