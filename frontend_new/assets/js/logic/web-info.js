var config = {
    r_url: "/api/cn/site",
};

$(document).ready(function () {
    getAlert();
    ajax_retrieve_callback(
        config,
        function (response, status, xhr) {
            //console.log(response);

            populateForm($("form"), response.data);
            unblockUI();
        }
    );
});

function upload_pic_ajax($obj, $form) {
    $status = upload_pic($obj);

    if ($status) {
        local_media_ajax_submit($form);
    }
}

function local_media_ajax_submit($form, $error_selector = $(".message_output")) {

    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    $_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);

    // $formData = $form.formSerialize();

    clearAlert($error_selector);

    //console.log(getHost() + $action);
    // //console.log($_form);

    var upload_type = $form.find('input[name=type]').val();

    $.ajax({
        type: ($method) ? $method : 'POST',
        url: getHost() + $action,
        headers: getHeaders(),
        contentType: false,
        cache: false,
        processData: false,
        data: $formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {

                save_media(response.extra.toString(), $error_selector);

            } else {
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
        headers: getHeaders(),
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
                    case "PUT":
                        //console.log("--- enter save_media");
                        save_media($data.params.extra, $error_selector)
                        break;
                }

            } else {
                showAlert(response.message, "danger", $error_selector);
            }
        },
        error: function (resp) {
            //console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function save_media($extra_data, $error_selector = $(".message_output")) {

    //console.log("save_media - " + JSON.stringify($extra_data));

    $item = {
        tempfile: $extra_data,
    };

    var form_data = new FormData();
    form_data.append("tempfile", $extra_data);

    $redirect_uri = 'web-info.html';

    $.ajax({
        type: 'POST',
        url: getHost() + '/assets/php/media-save.php',
        headers: getHeaders(),
        contentType: false,
        cache: false,
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