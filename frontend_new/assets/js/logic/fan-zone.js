
var link=getBackendHost();

function getFanZoneList() {//alert();

    var redirect_uri = 'fan-zone-list.html';
    datatable = $('#fanzone_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/fan_zone?sort[field]= fan_zone.sorting&sort[sort]=asc', //?search[type]=Member
                    headers: getHeaders(),
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
                    params: {

                       // search: {type: 'Admin'}
                    }
                },

            },
            pageSize: 20, // display 20 records per page
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            saveState: false,

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
            //input: $('#generalSearch'),
            delay: 400,
        },

        // columns definition
        columns: [
            /*{
                field: 'id',
                title: "<input type='checkbox' id='checkall'>",
                sortable: false,
                width: 20,

                template: function (data, i) {////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },*/
            {
                field: "fan_zone.sorting",
                title: "排序",
                width: 40,
                // callback function support for column rendering
                 template: function (data, i) {////console.log(data);
                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.sorting +
                        '</div>';

                    return output;
                }
            },
            {
                field: "fan_zone.url",
                title: "URL",
                width: 980,
                // callback function support for column rendering
				 template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div>' +
                        '<a href="fan-zone-edit.html?id=' + data.id + ' "class="kt-user-card-v2__name" style="color:#5867dd" >' + data.url + '</a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }
                
            },

            /*{
                field: "email",
                title: "电子邮件",
                autoHide: false,
                sortable: true,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.email +
                        '</div>';

                    return output;
                }
            },*/
          
           
            {
                field: "Actions",
                width: 80,
                title: "Actions",
                sortable: false,
                autoHide: false,
                overflow: 'visible',
                template: function (row) {
                    return '' +
                        '<div>' +
                        '<a href="fan-zone-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        //'<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';
                },
            }]
    });


}

function getFanZoneDetail(fan_id){
	$.ajax({
        type: 'GET',
        url: link + '/api/cn/fan_zone',
        data: {
            id: fan_id
        },
        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            //console.log(response);
            obj = response;
            ////console.log(obj.data);

            populateForm($("form"), obj.data);

            if(obj.data.upload_url) {
                $('#elm-image').attr('src','/'+obj.data.upload_url);
            }

            unblockUI();
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger");
            unblockUI();
        }

    });
}

function AddFanZone($form){

    $_form = document.getElementById('form');
    var formData = new FormData($_form);

    var object = {};
    formData.forEach(function(value, key){
        object[key] = value;
    });

    var jsonData = JSON.stringify(object);

    $.ajax({
        type: 'POST',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/fan_zone',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: jsonData,
        success: function (response, status, xhr) {
            ////console.log(image_size);
            //console.log(response);

            obj = response;

            var msg='成功添加新纪录';
            var redirect_url='fan-zone-list.html?alert-success='+msg;

            redirect_to(redirect_url);
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
    
}

function EditFanZone($form){
	var fan_id= getQueryString('id');
    $form = $($form);
 
    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    //$redirect_uri = $form.attr('data-redirect')+'?id='+fan_id;
    $redirect_uri = $form.attr('data-redirect');

    $_form =document.getElementById('form');
    formData = new FormData($_form);
	
	//var form_data = {};
	var temp=0;
	
	var json_form_obj={};
    json_form_obj['id'] =fan_id;
    json_form_obj['sorting'] =$('input[name="sorting"]').val();
	json_form_obj['url'] =$('input[name="url"]').val();
    json_form_obj['upload_url'] =$('input[name="upload_url"]').val();

    var formData = JSON.stringify(json_form_obj);
	//console.log(formData);
	
	// var file = $('#file')[0].files[0];
	// //console.log(file)
	
    clearAlert();

    $.ajax({
        type: ($method) ? $method : 'PUT',
        //url: getBackendHost() + $action,
        url: link + $action,
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

                var message = '成功编辑活动';
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);

            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $('.message_output'));
            }
        },
		
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function edit_picture($form){
	local_media_ajax_submit($form,'edit',$('.message_output'));		
	
}

function local_media_ajax_submit($form,action_type,$error_selector = $(".message_output")) {
	
	$form = $($form);
	$_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);
	
	////console.log(formData);
	$.ajax({
        type: 'POST',
        //url: 'http://fdcb6912.ngrok.io/assets/php/media-meta.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url:  getHost() +'/assets/php/media-meta.php',
        contentType: false,
        cache: false,
        processData: false,
         data: $formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {
				
				if (action_type=='add'){
					after_media_meta(	$form,
									{
										media_meta_data: obj.data,
										extra:obj.extra,
										method: "POST",
										
										params: {
											extra: response.extra,
										}
									},
									action_type,
									$error_selector
					);
				}
				else if (action_type=='edit'){
					//alert();
					edit_after_media_meta(	$form,
									{
										media_meta_data: obj.data,
										extra:obj.extra,
										method: "PUT",
										
										params: {
											extra: response.extra,
										}
									},
									action_type,
									$error_selector
					);
				}
				
            } else {
                showAlert(response.message, "danger", $error_selector);
            }


        },
        error: function (resp) {
            //console.log(resp);
            ////console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function after_media_meta($form,$data,action_type,$error_selector = $(".message_output")) {
	
	var json_form_obj = {
      "image_data":{},
	 
	};
	
	json_form_obj['url'] =$('input[name="url"]').val();
	json_form_obj.image_data.url=$data.media_meta_data.url;
	json_form_obj.image_data.name=$data.media_meta_data.name;
	json_form_obj.image_data.type=$data.media_meta_data.type;
	json_form_obj.image_data.size=$data.media_meta_data.filesize;
	//console.log($data);
	
	json_form_obj.extra=$data.extra;
    ////console.log("after_media_meta + "+image_size);
    ////console.log(JSON.stringify(json_form_obj));
	////console.log(link + '/api/cn/promotion');
	
	var formData = JSON.stringify(json_form_obj);
	
	 $.ajax({
        type: 'POST',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/fan_zone',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
			////console.log(image_size);
			//console.log(response);
			
            obj = response;
			
			
			media_save($form,obj,action_type,formData);
        },
		
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
	
	
}

function media_save($form,obj,action_type,formData){
	var fan_id= getQueryString('id');
	var msg='';
	var redirect_url='';
	$('#tempfile').val(obj.extra.extra);
	
	$form = $($form);
	$_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);
	
    //formData.append('tempfile', obj.extra.extra)
	
	 $.ajax({
        type: 'POST',
       // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
	    //url: getHost()+'/assets/php/media-meta.php',
        url:  '/assets/php/media-save.php',
         data: $formData,
        crossDomain: true,
        contentType: false,
        processData: false,
		 success: function (response, status, xhr) {
           //console.log(response);
			//console.log('yeah');
            obj = response;
			 
			if (action_type=='add'){
				msg='成功添加新纪录';
				redirect_url='fan-zone-list.html?alert-success='+msg;
			}
			else if (action_type=='edit'){
				msg='成功编辑纪录';
				redirect_url='fan-zone-edit.html?id='+fan_id+'&alert-success='+msg;
			}
			
			
		redirect_to(redirect_url);
           // if (big_indicator==true && medium_indicator==true && small_indicator==true)
			//	addPromo_all($form,$data,$error_selector = $(".message_output"));
		//redirect_to("fan-zone-edit.html?id="+promo_id+"&alert-success=" + msg);
        },
        error: function (resp) {
           
            ////console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
	
    
}


function edit_after_media_meta($form,$data,action_type,$error_selector = $(".message_output")) {
	var fan_id= getQueryString('id');
	var json_form_obj = {
      "image_data":{},
	 
	};
	
	json_form_obj['id'] =fan_id;
	json_form_obj['url'] =$('input[name="url"]').val();
	json_form_obj.image_data.url=$data.media_meta_data.url;
	json_form_obj.image_data.name=$data.media_meta_data.name;
	json_form_obj.image_data.type=$data.media_meta_data.type;
	json_form_obj.image_data.size=$data.media_meta_data.filesize;
	//console.log($data);
	
	json_form_obj.extra=$data.extra;
    ////console.log("after_media_meta + "+image_size);
    ////console.log(JSON.stringify(json_form_obj));
	////console.log(link + '/api/cn/promotion');
	
	var formData = JSON.stringify(json_form_obj);
	
	 $.ajax({
        type: 'PUT',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/fan_zone',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: formData,
        success: function (response, status, xhr) {
			////console.log(image_size);
			//console.log(response);
			
            obj = response;
			
			
			media_save($form,obj,action_type,formData);
        },
		
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
	
	
}

function deleterecord(del_id, $redirect_uri, api = 'fan_zone') {

    var status = confirm('确定要删除这项纪录？');

    if (status == true) {
        //$redirect_uri = 'member-list.html';
		
		if (del_id=='-')
			del_id=getQueryString('id');
		
        $('#delete_form').find('input').val(del_id);

        $.ajax({
            type: 'DELETE',
            url: link + '/api/cn/' + api,
            crossDomain: true,
            headers: getHeaders(),
            contentType: 'application/json',
            processData: false,
            // contentType: "charset=utf-8",

            data: JSON.stringify({id: del_id}),

            success: function (response, status, xhr) {
                //console.log(response);
                obj = response;
                if (obj.code == 1) {
                    var message = '成功删除';
                    if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);
                } else if (obj.code == -1) {
                    redirect_to_login();
                } else {
                    showAlert(obj.message, "danger", $(".message_output"));
                }
            }

        });
    }

}
