check_auth();
var link = getBackendHost();

function check_auth() {
    if (window.localStorage.admin_access_token) {

    } else {
        if (getCurrentUri() !== "/cn/auth/login.html") {
            redirect_to_login();
        }
    }
}

$pageSize = 10;
$serverPaging = true;
$serverFiltering = true;
$serverSorting = true;
var curr_permissoin_id = [];

function changeVisible($val, $checkbox, $visible_form = $('#visible_form')) {
    $visible_form.find('input[name=id]').val($val);
    $visible_form.find('input[name=disabled]').val($checkbox.checked ? 0 : 1);

    //console.log($visible_form.formSerialize());

    ajax_submit($visible_form);
}

function buildDataTable($this, $data, dataCallback = null) {
    $this.KTDatatable({
        data: {
            type: 'remote',
            source: {
                read: {
                    url: getBackendHost() + $data.url,
                    method: ($data.method) ? $data.method : "GET",
                    params: ($data.params) ? $data.params : {},
                    headers: getHeaders(),
                    contentType: 'application/text',
                    map: function (raw) {
                        dataCallback && dataCallback(raw)
                        if (raw.code === -1) redirect_to_login();
                        if (typeof raw.data !== 'undefined') {
                            dataSet = raw.data;
                        } else if (typeof raw.datas !== 'undefined') {
                            dataSet = raw.datas;
                        } else if (typeof raw.list !== 'undefined') {
                            dataSet = raw.list;
                        } else if (typeof raw.detail !== 'undefined') {
                            dataSet = raw.detail;
                        }

                        return dataSet;
                    },
                },
            },
            //pageSize: ($data.page_size) ? $data.page_size : $pageSize,
            pageSize: 20,
            serverPaging: true, //($data.server_paging) ? $data.server_paging : $serverPaging,
            serverFiltering: true, //($data.server_filtering) ? $data.server_filtering : $serverFiltering,
            serverSorting: true, //($data.server_sorting) ? $data.server_sorting : $serverSorting,
            saveState: {
                cookie: false,
                webstorage: false
            }
        },
        sortable: ($data.sortable) ? $data.sortable : true,
        pagination: ($data.pagination) ? $data.pagination : true,
        search: {
            input: $(($data.search_element) ? $data.search_element : '#generalSearch'),
        },
        rows: {
            autoHide: false,
        },
        translate: {
            records: {
                processing: "处理中",
                noRecords: "沒有記錄",
            }
        },
        layout: {
            header: ($data.layout_header) ? $data.layout_header : false,
            scroll: true,
            footer: true
        },
        columns: $data.columns,
    });
}


function buildCustomTable($this, $data) {
    var content = '';

    content += '<table class="table table-bordered">';
    content += '<thead><tr>';
    $.each($data.columns, function (key, entry) {
        style_align = ((entry.align) ? 'class="text-' + entry.align + '"' : 'class="text-center"');
        style_width = ((entry.width) ? 'width="' + entry.width + '%"' : '');
        content += '<th ' + style_align + style_width + '>';
        content += '<span style="white-space: nowrap;">' + entry.title + '</span>';
        content += '</th>';
    });
    content += '</tr></thead>';
    content += '<tbody>';
    $data['r_url'] = $data['url'];
    ajax_retrieve_callback($data, function (response, status, xhr) {
        $.each(response.data, function (key, entry) {
            ////console.log("tbody - " + entry);
            content += '<tr>';
            $.each($data.columns, function (cKey, cEntry) {
                content += '<td class="text-center">';
                content += cEntry.template(entry, key);
                content += '</td>';
            });
            content += '</tr>';
        });
        content += '</tbody>';
        content += '</table>';
        $this.html(content);
    });
}

var tagify;

function buildTagify($selector, $remove_selector = "") {
    tagify = new Tagify(document.querySelector($selector));
    document.querySelector($remove_selector).addEventListener('click', tagify.removeAllTags.bind(tagify));
}

let editor;

function buildCkeditor($selector) {
    return ClassicEditor
        .create(document.querySelector($selector), {
            //plugins: [ Essentials, Paragraph, Bold, Italic, Alignment, Font ],     // <--- MODIFIED
            //toolbar: [ 'bold', 'italic', 'alignment' ],                    // <--- MODIFIED
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'font',
                'fontSize',
                'fontFamily',
                'fontColor',
                'fontBackgroundColor',
                'bulletedList',
                'numberedList',
                '|',
                'indent',
                'outdent',
                '|',
                //'imageUpload',
                'ckfinder',
                //'Code',
                'blockQuote',
                'insertTable',
                //'mediaEmbed',
                'Alignment',
                'Table',
                'TableCellProperties',
                'TableProperties',
                'TableToolbar',
                'undo',
                'redo',
            ],
            image: {
                toolbar: [
                    'imageStyle:full',
                    'imageStyle:side',
                    '|',
                    'imageTextAlternative'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },
            ckfinder: {
                // Upload the images to the server using the CKFinder QuickUpload command.
                uploadUrl: 'http://19com_front/assets/js/ckfinder/core/php/connector.php?command=QuickUpload&type=Files&responseType=json',
            },
            /*mediaEmbed: {
                previewsInData: true,
            },
            simpleUpload: {
                uploadUrl: "/assets/php/media-meta.php",
                headers: {
                    ...getHeaders()
                }
            },*/
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
                console.error(error);
            }
        );
}

function getAlert($selector = $(".message_output")) {
    if (getQueryString('alert-success')) {
        $selector.html('<div class="alert alert-success alert-text">' + getQueryString('alert-success') + '</div>');
    } else if (getQueryString('alert-danger')) {
        $selector.html('<div class="alert alert-danger alert-text">' + getQueryString('alert-danger') + '</div>');
    } else if (getQueryString('alert-warning')) {
        $selector.html('<div class="alert alert-warning alert-text">' + getQueryString('alert-warning') + '</div>');
    } else if (getQueryString('alert-info')) {
        $selector.html('<div class="alert alert-info alert-text">' + getQueryString('alert-info') + '</div>');
    }
}

function showAlert($message, $type = "danger", $selector = $(".message_output")) {
    if ($type == 'success') {
        $selector.html('<div class="alert alert-success alert-text">' + $message + '</div>');
    } else if ($type == 'danger') {
        $selector.html('<div class="alert alert-danger alert-text">' + $message + '</div>');
    } else if ($type == 'warning') {
        $selector.html('<div class="alert alert-warning alert-text">' + $message + '</div>');
    } else if ($type == 'info') {
        $selector.html('<div class="alert alert-info alert-text">' + $message + '</div>');
    }
}

function clearAlert($selector = $(".message_output")) {
    $selector.html('');
}

function populateForm($form, $dataSet = "") {
    var promises = []
    $form.find('input,textarea').each(function (index, element) {
        $name = element.name;
        $type = element.type;
        $value = ($dataSet[$name]) ? $dataSet[$name] : '';

        switch ($type) {
            case 'hidden':
                if ($(element).attr('data-type') == "image") {
                    if ($value) {
                        element.value = $dataSet[$name];
                        $img_path = getHost() + $dataSet[$name];
                        $('img[name=elm-' + $name + ']').attr('src', $img_path);
                    }
                } else {
                    if ($name == 'id') {
                        element.value = getQueryString('id');
                    }
                }
                break;
            case 'checkbox':
                if ($name == "disabled") $value = !$value;
                setCheckbox(element, $value);
                break;
            case 'text':
                element.value = $value;

                if ($(element).attr('data-type') == "tagify") {
                    buildTagify('#' + $(element).attr('id'), '#tags_remove');
                }
                break;
            case 'textarea':
                element.value = $value.replace(/\\"/g, '"');
                if ($(element).attr('data-type') == "ckeditor") {
                    promises.push(buildCkeditor('#' + $(element).attr('id')));
                }
                break;
            case 'number' :
                element.value = $value;
                break;
        }

        // //console.log("i : " + index + " | e : " + element + " | t : " + $type + " | n : " + $name);
    });
    return Promise.all(promises)

}

function setCheckbox($selector, status) {
    if (status) {
        $($selector).attr('checked', 'checked');
    } else {
        $($selector).removeAttr('checked');
    }
}

function getHeaders() {
    return {Authorization: window.localStorage.admin_access_token};
}

function upload_pic($obj) {
    if ($obj.files.length > 0) {
        $('img[name=elm-' + $($obj).attr('name') + ']').attr('src', window.URL.createObjectURL($obj.files[0]));
        return true;
    } else {
        return false;
    }
}

function ajax_submit($form, $error_selector = $(".message_output"), $overrideFormData = null) {
    // Update CKEditor to <textarea> upon submit
    if (typeof editor !== 'undefined') {
        editor.updateSourceElement();
    }

    //console.log("ajax_submit");
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    //console.log("action : " + $action);
    //console.log("method : " + $method);
    //console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));

    //console.log($_form);
    //tag and draft by bc
    $formData = $overrideFormData ? $overrideFormData : new FormData($_form);
    var data = {};
    //$formData.forEach(function(value, key){
    //data[key] = value;
    //});
    $formData.forEach(function (value, name) {
        if (value == "true" || value == "false" || value == "on" || value == "off") {
            // if boolean
            data[name] = (value == "true" || value == "on") ? false : true;
        } else {
            // normal
            if (name == "id") {
                data[name] = parseInt(value);
            } else {
                data[name] = value;
            }
        }
    });

    var tags = []
    if (tagify && tagify.value) {
        tags = tagify.value.map(function (v) {
            return v.value
        });
        data.tags = tags;
    }
    //tag and draft by bc
    clearAlert();

    $.ajax({
        type: ($method) ? $method : 'GET',
        url: getBackendHost() + $action,
        crossDomain: true,
        headers: getHeaders(),
        //contentType: false,
        processData: false,
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (response, status, xhr) {
            //console.log(response);
            obj = response;
            if (obj.code == 1) {

                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + obj.status);//bc
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

function ajax_retrieve_callback($config, $success_cb) {
    ////console.log("ajax_retrieve_callback --- ");
    $.ajax({
        type: ($config.method) ? $config.method : 'GET',
        url: getBackendHost() + $config.r_url,
        headers: getHeaders(),
        data: ($config.params) ? $config.params : {},
        success: function (response, status, xhr) {
            ////console.log(response);
            if (response.code == -1) {
                redirect_to_login();
                return;
            }

            $success_cb(response, status, xhr);
        },
        error: function () {
            alert('Problem occurred while sending request.');
        }
    });
}

function deleteItem($val, $delete_form = $('#delete_form')) {
    var status = confirm('确定要删除这项纪录？');

    if (status == true) {
        $delete_form.find('input').val($val);
        var formData = JSON.stringify($("#myForm").serializeArray());
        ajax_submit($delete_form);
    }
}

//to be obsolete? try search in all files
function setPermission(selected_id, selected_action) {
    var confirmMsg;
    var disabled;
    var method;
    var action = '/api/cn/user';
    var json_form_obj = {
        "data": []
    };
    var subdata = {};
    var formData;

    if (selected_action == 'bulk_blacklist' || selected_action == 'bulk_whitelist') {
        method = 'PATCH';
        if (selected_action == 'bulk_blacklist')
            disabled = 1;
        else
            disabled = 0;

        //console.log(selected_id);

        //construct formData
        $.each(selected_id, function (index, value) {
            subdata['id'] = value;
            subdata['disabled'] = 1;
            ////console.log(subdata);
            json_form_obj.data.push(subdata);
            subdata = {};//reset subdata for new row
        });

        formData = JSON.stringify(json_form_obj);
        //console.log(formData);

    } else if (selected_action == 'bulk_delete') {
        method = 'DELETE';
    }

    $.ajax({
        type: method,
        //url: getBackendHost() + $action,
        url: link + action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            /*
           if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + obj.message);
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $error_selector);
            }*/
        },

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}




