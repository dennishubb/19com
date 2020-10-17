var link = getBackendHost();
var checkall_indicator = true;
var systemPromoAry = [];
var allPromoId = {}

////console.log(link);

function getPromoList() {

    var redirect_uri = 'member-list.html';
	
    datatable = $('#promo_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/promotion?sort[field]= promotion.id&sort[sort]=desc', //?search[type]=Member
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET'

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
            scroll: true,
            height: 550,
            footer: true
        },


        // column sorting
        sortable: true,

        pagination: true,

        search: {
            input: $('#generalSearch'),
            delay: 400,
        },

        rows: {
            autoHide: false,
        },

        // columns definition
        columns: [{
                field: 'id',
                title: "<input type='checkbox' id='checkall' >",
                sortable: false,
                width: 20,

                template: function(data, i) { ////console.log(data);
                    if (data.system == "1") {
                        systemPromoAry.push(data.id.toString())
                    }
                    return output = "<input type='checkbox' id=" + data.id + ">";
                }
            },
            {
                field: "sorting",
                title: "排序",
                sortable: true,
                width: 50,
                template: function(data, i) { ;
                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.sorting +
                        '</div>';

                    return output;
                }
            },
            {
                field: "promotion.name",
                title: "活动名称",
                sortable: true,
                width: 80,
                // callback function support for column rendering
                template: function(data, i) { ////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +
                        '<div>' +
                        '<a href="promotion-edit.html?id=' + data.id +
                        '"class="kt-user-card-v2__name" style="color:#5867dd">' + data.name + '</a>' +
                        '</div>' +
                        '</div>';
                    return output;
                }


            },
            {
                field: 'disabled',
                title: '狀態',
                width: 55,
                sortable: false,
                template: function(row) {
                    var status = { 0: { 'checked': 'checked="checked"' }, 1: { 'checked': '' } };

                    html = '';

                    html += '<span class="kt-switch kt-switch--success">';
                    html += '<label>';
                    html += '<input name="disabled" type="checkbox" onchange="changeVisible(' + row.id + ',this);" ' + status[row.disabled].checked + '>';
                    html += '<span></span>';
                    html += '</label>';

                    return html;
                    // return '<span class="kt-badge kt-badge--inline kt-badge--pill kt-badge-dark ' + status[row.is_visible].class + '">' + status[row.is_visible].title + '</span>';
                }
            },
            {
                field: "promotion.type",
                title: "活动类型",
                sortable: true,
                width: 50


            }, {
                field: "level_id",
                title: "参与等级",
                sortable: true,
                autoHide: false,
                /*template: function (data, i) {////console.log(data);
                    var display=data.level_id.replace(/"/g, "");
                    
                    var output = '' +
                        '<div class="kt-user-card-v2">' + display+
                        '</div>';

                    return output;
                }*/
            },
            {
                field: 'sign_up',
                title: "参加方式",
                sortable: true,
                autoHide: false,
                width: 50,
                template: function(data, i) {
                    var display;

                    display = (data.sign_up == 0) ? '手动' : '自动'; //  会员手动申请 & 系统自动申请
                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
                }
            },
            {
                field: "limitation",
                title: "限制次数",
                sortable: true,
                autoHide: false,
                template: function(data, i) {
                    var display;
                    if (data.limitation == 'daily')
                        display = '每日';
                    else if (data.limitation == 'monthly')
                        display = '每月';
                    else if (data.limitation == 'sign up')
                        display = '注册';
                    else if (data.limitation == 'once')
                        display = '单次';
                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
                }
            },
            {
                field: "display_method",
                title: "展现方式",
                sortable: true,
                autoHide: false
            },
            {
                field: "start_at",
                title: "开始时间",
                sortable: true,
                autoHide: false
            },
            {
                field: "end_at",
                title: "结束时间",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function(data, i) {
                    var display;
                    display = (data.end_at == '2999-01-01 00:00:00') ? '长久' : data.end_at;

                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
                }
            },
            {
                field: "settle_at",
                title: "结算时间",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function(data, i) {
                    var display;

                    display = (data.settle_at == '0000-00-00 00:00:00') ? '手动' : data.settle_at; //  手动结算 & 系统自动结算
                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
                }
            },
            {
                field: "introduction",
                title: "活动简介",
                sortable: true,
                autoHide: false,
				template: function(data, i){
                    var display = data.introduction;
					if(display.length > 30){
						display = display.substring(0,30);
					}
                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
				}
            },
            {
                field: "Actions",
                width: 80,
                title: "Actions",
                sortable: false,
                autoHide: false,
                overflow: 'visible',
                template: function(row) {
                    if (row.system) return '' +
                        '<div>' +
                        '<a href="promotion-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +
                        '</div>';
                    else return '' +
                        '<div>' +
                        '<a href="promotion-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        // '<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';




                },
            }
        ]
    });


    // search
}

function getPromoDetail(promo_id) {
    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/promotion',
        data: {
            id: promo_id
        },
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function(response, status, xhr) {
            ////console.log(response);
            obj = response;
            ////console.log(obj.data);

            var form_data = obj.data;
            populateForm($("form"), form_data);

            //DISPLAY 活动类型
            $("#promo_type").val(form_data.type);

            //DISPLAY 参加方式
            $("input[name=sign_up][value='" + form_data.sign_up + "']").prop("checked", true);

            //DISPLAY 参与等级
            //var level_id_arr=form_data.level_id.replace(/"/g, ""); //replace all "
            //var level_id_arr =  form_data.level_id.split(','); 

            $("input[name=level_id]").prop('checked', false);

            $.each(form_data.level_id, function(i, value) {
                ////console.log(i +'->'+ value);
                $("input[name=level_id][value='" + value + "']").prop("checked", true);

            });

            //DISPLAY 展现方式
            $("input[name=display_method][value='" + form_data.display_method + "']").prop("checked", true);

            //DISPLAY 活动图片
            if (form_data.upload_big_url)
                $('#big_image').attr('src', '/' + form_data.upload_big_url);

            if (form_data.upload_medium_url)
                $('#medium_image').attr('src', '/' + form_data.upload_medium_url);

            if (form_data.upload_small_url)
                $('#small_image').attr('src', '/' + form_data.upload_small_url);

            //DISPLAY 长久
            if (form_data.end_at == null || form_data.end_at == '2999-01-01 00:00:00') {
                $("input[name=permanent]").prop("checked", true); //check checkbox
                $("input[name=end_at]").val("-");
                $('input[name="end_at"]').attr('disabled', true); //disabled end_at
            }

            //DISPLAY 限制次数
            $("input[name=limitation][value='" + form_data.limitation + "']").prop("checked", true);

            //DISPLAY 结算方式 & 结算时间
            if (form_data.settle_at == null || form_data.settle_at == '0000-00-00 00:00:00') {
                $("input[name=settle_type][value='manual']").prop("checked", true);
                $("input[name=settle_at]").val("-");
                $('input[name="settle_at"]').attr('disabled', true); //disabled end_at
            } else {
                $("input[name=settle_type][value='auto']").prop("checked", true);
            }


            unblockUI();
        },
        error: function() {
            showAlert("Problem occurred while sending request.", "danger");
            unblockUI();
        }

    });
}

function getPromoRedeemList() {


    datatable = $('#promo_redeem_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/promotion_redeem', //?search[type]=Member
                    method: 'GET'

                },
                params: {
                    sort: {
                        field: "promotion.name",
                        sort: "desc"
                    }
                }
            },

            pageSize: 20, // display 20 records per page
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            saveState: false,

        },

        // layout definition
        layout: {
            scroll: true,
            height: 550,
            footer: true
        },


        // column sorting
        sortable: true,

        pagination: true,

        search: {
            input: $('#generalSearch'),
            delay: 400,
        },

        rows: {
            autoHide: false,
        },

        // columns definition
        columns: [{
                field: 'id',
                title: "<input type='checkbox' id='checkall' >",
                sortable: false,
                width: 20,

                template: function(data, i) { ////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },
            {
                field: "promotion_redeem.created_at",
                title: "申请时间",
                sortable: true,
                // width: 100
                template: function(data, i) { ////console.log(data);

                    if (data.promotion_data != null)
                        return data.promotion_data.created_at;
                    else
                        return '';

                }
            },
            {
                field: "promotion.name",
                title: "活动名称",
                sortable: true,
                //width: 80,
                // callback function support for column rendering

                template: function(data, i) { ////console.log(data);
                    if (data.promotion_data != null)
                        return data.promotion_data.name;
                    else
                        return '';
                }


            },
            {
                field: "user.username",
                title: "会员账号",
                sortable: true,
                // width: 30,
                // callback function support for column rendering
                template: function(data, i) { ////console.log(data);
                    if (data.user_data != null)
                        return data.user_data.username;
                    else
                        return '';
                }
            },
            {
                field: "promotion_redeem.status",
                title: "审核状态",
                sortable: true,
                // width: 30,
                // callback function support for column rendering
                template: function(data, i) { ////console.log(data);

                    var display = '';
                    var style = '';

                    if (data.status == 'pending') {
                        display = '等待审核';
                        style = 'color:blue';
                    } else if (data.status == 'approve') {
                        display = '审核通过';
                        style = 'color:green';
                    } else if (data.status == 'reject') {
                        display = '审核失败';
                        style = 'color:red';
                    }

                    var output = '' +
                        '<div class="kt-user-card-v2" style=' + style + '>' + display + '</div>';
                    return output;
                }

            },
            {
                field: "promotion_redeem.admin_id",
                title: "审核人",
                sortable: true,
                // width: 30,
                // callback function support for column rendering
                template: function(data, i) { ////console.log(data);
                    if (data.admin_data != null)
                        return data.admin_data.username;
                    else
                        return '';
                }
            },
            {
                field: "promotion_redeem.updated_at",
                title: "审核时间",
                sortable: true,
                // width: 30,
                // callback function support for column rendering
                template: function(data, i) { ////console.log(data);

                    return data.updated_at;
                }
            },

            {
                field: "Actions",
                width: 80,
                title: "Actions",
                sortable: false,
                autoHide: false,
                overflow: 'visible',
                template: function(row) {
                    var redirect_uri = '';
                    if (row.status == 'pending')
                        return '' +
                            /* '<div>'+
											'<a href="member-edit.html?id='+row.id+'" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">'+
											'<i class="flaticon2-paper"></i></a>'+
											
											'<a href="#" class="kt-nav__link" onclick="deleterecord(\''+row.id+'\', \''+redirect_uri+'\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
											'<i class="text-danger flaticon2-trash"></i>' +
																// '<span class="kt-nav__link-text">删除</span>' +
																	'</a>' +
																	'</div>';*/

                            '<div>' +
                            '<select class="" id=action_' + row.id + ' onchange="promo_redeem_action(' + row.id + ')">' +
                            '<option value="-">选择审核状态</option>' +
                            '<option value="approve">审核通过</option>' +
                            '<option value="reject">审核失败</option>' +
                            ' </select>' +
                            '</div>';
                    else
                        return ""
                },
            },

        ]
    });


    // search
}

function searchPromoList(){
	var SearchKeyword=$('#SearchKeyword').val();
	var fullUri = getCurrentFullUri();
	var user_type='';
	var disabled='';
	//var datatablle_id='';
	

	$(".kt-datatable").KTDatatable().API.params = {};
	
	//filter by keyword
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][field]', "promotion.name")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][value]', "%"+ SearchKeyword.toLowerCase()+"%")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][operator]', "LIKE")
	 
	 $(".kt-datatable").KTDatatable().setDataSourceParam('sort[field]', "promotion.id")
     $(".kt-datatable").KTDatatable().setDataSourceParam('sort[sort]', "desc")
	 
	
//console.log('ni')
     $(".kt-datatable").KTDatatable().load();
}

function searchPromoRedeemList(){
	var SearchKeyword=$('#SearchKeyword').val();
	var SearchUserName=$('#filter-ID').val();
	var SearchReviewStatus=$('#review_status').val();
	
	var fullUri = getCurrentFullUri();
	var user_type='';
	var disabled='';
	//var datatablle_id='';
	


	$(".kt-datatable").KTDatatable().API.params = {};
	
	//filter by keyword
     
	 $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][field]', "promotion.name")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][value]', "%"+ SearchKeyword.toLowerCase()+"%")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[0][operator]', "LIKE")
	 
	 $(".kt-datatable").KTDatatable().setDataSourceParam('filter[1][field]', "user.username")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[1][value]', "%"+ SearchUserName.toLowerCase()+"%")
     $(".kt-datatable").KTDatatable().setDataSourceParam('filter[1][operator]', "LIKE")
	 
	 if (SearchReviewStatus!='-'){
		 $(".kt-datatable").KTDatatable().setDataSourceParam('filter[2][field]', "promotion_redeem.status")
		 $(".kt-datatable").KTDatatable().setDataSourceParam('filter[2][value]', SearchReviewStatus)
		 $(".kt-datatable").KTDatatable().setDataSourceParam('filter[2][operator]', "=") 
	 }
	 
	 $(".kt-datatable").KTDatatable().setDataSourceParam('sort[field]', "promotion.id")
     $(".kt-datatable").KTDatatable().setDataSourceParam('sort[sort]', "desc")
	 
	
//console.log('ni')
     $(".kt-datatable").KTDatatable().load();
}

function promo_redeem_action(id) {
    var select_box_id = 'action_' + id;
    var action = $('#' + select_box_id).val();
    var action_status;
	$error_selector=$(".message_output");
    $redirect_uri = 'promotion-redeem.html';
	
    var json_form_obj = {
        id: id,
        status: action

    };

    //alert($('#'+select_box_id).val());

    if (action == 'approve') action_status = '通过';
    else if (action == 'reject') action_status = '失败';

    var status = confirm('确定把状态改为“' + action_status + '”？');

    if (status == true) {

        var data = JSON.stringify(json_form_obj);
        ////console.log(data);

        $.ajax({
            type: 'PUT',
            //url: getBackendHost() + $action,
            url: link + '/api/cn/promotion_redeem',
            crossDomain: true,
            headers: getHeaders(),
            contentType: false,
            processData: false,
            // contentType: "charset=utf-8",
            data: data,
            success: function(response, status, xhr) {
                // //console.log(response);

                obj = response;


                if (obj.code == 1) {
                    if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + '成功编辑资料');
                } else if (obj.code == -1) {
                    redirect_to_login();
                } else {
                    showAlert(obj.message, "danger", $error_selector);
                }
            },

            error: function() {
                showAlert("Problem occurred while sending request.", "danger", $error_selector);
            },
        });
    }
}


function validateForm($form, validateField = 'all') {

    var msg = '';
    var name = '';
    $form.find(':input').each(function(key, value) {
        temp = $(this).attr("name");


        if (temp == 'tempfile')
            return; //continue
        else
            name = $('#' + temp + '_label').html();

        if ($(this).val().length <= 0) {
            msg += name + ',';
        }
    });

    if (msg.length > 0) {
        msg = msg.slice(0, -2); //remove last comma
        msg += ' 不能为空';

        showAlert(msg, "danger", $(".message_output"));
        return false;
    } else
        return true;

}

function addPromo($form) {
    blockUI();
    var json_form_obj = {};
    ////console.log("Add Promo");

    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    ////console.log("action : " + $action);
    ////console.log("method : " + $method);
    ////console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById('form');
    formData = new FormData($_form);

    var level_id_arr = [];
    formData.forEach(function(value, key) {

        if (key == 'level_id') {
            level_id_arr.push(value);
        }
        if (key != 'image1' && key != 'image2' && key != 'image3' && key != 'level_id') {
            ////console.log(key+' -> '+value); 
            json_form_obj[key] = value;
        }

    });

    var level_id_str = level_id_arr.toString();

    json_form_obj['level_id'] = level_id_arr;
    ////console.log(level_id_arr);
    json_form_obj['disabled'] = $('input[name="disabled"]').is(':checked') ? 0 : 1;
    json_form_obj['permanent'] = $('input[name="permanent"]').is(':checked') ? 1 : 0;
    if (json_form_obj['permanent'] == "1") json_form_obj['end_at'] = "2999-01-01"
        //json_form_obj.image_data.big=big;
        //json_form_obj.image_data.medium=medium;
        //json_form_obj.image_data.small=small;

    var formData = JSON.stringify(json_form_obj);
    //console.log(formData);
    //$formData = new FormData($_form);
    clearAlert();

    $.ajax({
        type: ($method) ? $method : 'POST',
        //url: getBackendHost() + $action,
        url: link + $action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: formData,
        success: function(response, status, xhr) {
            ////console.log(response);

            obj = response;
            //media_save($form,obj,$redirect_uri);

            if (obj.code == 1) {
                //var message='成功添加新活动';
                //if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);

                //success add new promo then upload images
                local_media_ajax_submit($form, obj, 'addPromo', $('.message_output'));
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $(".message_output"));
            }
        },

        error: function() {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
    //local_media_ajax_submit($form,$('.message_output'));
    //addPromo_all($form);
    //submitAdd();
}

function editPromo($form, $error_selector = $('.message_output')) {
    var promo_id = getQueryString('id');
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect') + '?id=' + promo_id;

    $_form = document.getElementById('form');
    formData = new FormData($_form);

    //var form_data = {};
    var temp = 0;

    var json_form_obj = {};
    var level_id_arr = [];

    json_form_obj['id'] = promo_id;
    formData.forEach(function(value, key) {
        if (key == 'level_id') {
            level_id_arr.push(value);
        }

        if (key != 'image1' && key != 'image2' && key != 'image3' && key != 'level_id')
            json_form_obj[key] = value;
    });

    json_form_obj['level_id'] = level_id_arr;
    json_form_obj['disabled'] = $('input[name="disabled"]').is(':checked') ? 0 : 1;
    json_form_obj['permanent'] = $('input[name="permanent"]').is(':checked') ? 1 : 0;
    if (json_form_obj['permanent'] == "1") json_form_obj['end_at'] = "2999-01-01"

    var formData = JSON.stringify(json_form_obj);
    //console.log(formData);
    //$formData = new FormData($_form);

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
        success: function(response, status, xhr) {
            ////console.log(response);

            obj = response;

            if (obj.code == 1) {
                var message = '成功编辑活动';
                // if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + message);

                local_media_ajax_submit($form, obj, 'editPromo', $('.message_output'));

            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $error_selector);
            }
        },

        error: function() {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}



var big = {};
var medium = {};
var small = {};

var big_indicator = false;
var medium_indicator = false;
var small_indicator = false;

//below function is for image upload
function media_save($form, obj, promo_id, image_size, callFrom, $redirect_uri) {

    //$form.find('#extra').val(obj.extra.extra);
    //$form = $($form);
    //$_form = document.getElementById($form.attr('id'));
    //formData = new FormData($_form);
    ////console.log(image_size);
    ////console.log(obj);
    ////console.log(' extra->'+obj.extra.extra);

    formData.append('tempfile', obj.extra.extra)
    $.ajax({
        type: 'POST',
        // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url: '/assets/php/media-save.php',
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false,
        success: function(response, status, xhr) {
            ////console.log(response);

            obj = response;

            ////console.log (obj)
            //alert(promo_id);
            //add control if exist only run
            if (image_size == 'big') {

                var file2 = $('#image2')[0].files[0];
                if (file2) { //if file 2 exist
                    wait(1000);
                    formData.append('file', file2)
                    formData.append('type', file2.type)

                    ////console.log('medium ->');
                    ////console.log(file2);
                    media_meta($form, formData, 'medium', promo_id, callFrom, $error_selector = $(".message_output"));
                } else { //if file 2 not exist, go to file 3
                    var file3 = $('#image3')[0].files[0];
                    if (file3) { //if file 3 exist
                        wait(1000);
                        formData.append('file', file3)
                        formData.append('type', file3.type)

                        ////console.log('medium ->');
                        ////console.log(file2);
                        media_meta($form, formData, 'small', promo_id, callFrom, $error_selector = $(".message_output"));
                    } else { //if file 3 not exist, redirect
                        promo_redirect(promo_id, callFrom);
                    }
                }
            } else if (image_size == 'medium') {
                var file3 = $('#image3')[0].files[0]
                if (file3) { //if file 3 exist
                    wait(1000);
                    formData.append('file', file3)
                    formData.append('type', file3.type)

                    ////console.log('medium ->');
                    ////console.log(file2);
                    media_meta($form, formData, 'small', promo_id, callFrom, $error_selector = $(".message_output"));
                } else { //if file 3 not exist, redirect
                    promo_redirect(promo_id, callFrom);
                }

            } else if (image_size == 'small') {
                //console.log('finish');

                promo_redirect(promo_id, callFrom);
            }
            // if (big_indicator==true && medium_indicator==true && small_indicator==true)
            //	addPromo_all($form,$data,$error_selector = $(".message_output"));
        },
        error: function(resp) {

            ////console.log(resp);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });


}

function wait(ms) {
    var start = new Date().getTime();
    var end = start;
    while (end < start + ms) {
        end = new Date().getTime();
    }

}

function after_media_meta($form, $data, image_size, promo_id, callFrom, $error_selector = $(".message_output")) {

    var json_form_obj = {
        "image_data": {},
        id: promo_id
    };

    if (image_size == 'big') {
        json_form_obj.image_data.big = {}
        json_form_obj.image_data.big.url = $data.media_meta_data.url;
        json_form_obj.image_data.big.name = $data.media_meta_data.name;
        json_form_obj.image_data.big.type = $data.media_meta_data.type;
        json_form_obj.image_data.big.size = $data.media_meta_data.filesize;

    } else if (image_size == 'medium') {
        json_form_obj.image_data.medium = {}
        json_form_obj.image_data.medium.url = $data.media_meta_data.url;
        json_form_obj.image_data.medium.name = $data.media_meta_data.name;
        json_form_obj.image_data.medium.type = $data.media_meta_data.type;
        json_form_obj.image_data.medium.size = $data.media_meta_data.filesize;
    } else if (image_size == 'small') {
        json_form_obj.image_data.small = {}
        json_form_obj.image_data.small.url = $data.media_meta_data.url;
        json_form_obj.image_data.small.name = $data.media_meta_data.name;
        json_form_obj.image_data.small.type = $data.media_meta_data.type;
        json_form_obj.image_data.small.size = $data.media_meta_data.filesize;
    }
    ////console.log($data);

    json_form_obj.extra = $data.extra;
    ////console.log("after_media_meta + "+image_size);
    ////console.log(JSON.stringify(json_form_obj));
    ////console.log(link + '/api/cn/promotion');

    var formData = JSON.stringify(json_form_obj);

    $.ajax({
        type: 'PUT',
        //url: getBackendHost() + $action,
        url: link + '/api/cn/promotion',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        // contentType: "charset=utf-8",
        data: formData,
        success: function(response, status, xhr) {
            ////console.log(image_size);
            ////console.log(response);

            obj = response;
            if (image_size == 'big')
                big_indicator = true;
            else if (image_size == 'medium')
                medium_indicator = true;
            else if (image_size == 'small')
                small_indicator = true;

            media_save($form, obj, promo_id, image_size, callFrom, $redirect_uri);
        },

        error: function() {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });


}

function media_meta($form, formData, image_size, promo_id, callFrom, $error_selector = $(".message_output")) {
    $.ajax({
        type: 'POST',
        //url: 'http://fdcb6912.ngrok.io/assets/php/media-meta.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url: getHost() + '/assets/php/media-meta.php',
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        success: function(response, status, xhr) {
            ////console.log(response);

            obj = response;

            if (obj.code == 1) {

                after_media_meta($form, {
                        media_meta_data: obj.data,
                        extra: obj.extra,
                        method: "PUT",
                        redirect: $redirect_uri,
                        params: {
                            extra: response.extra,
                        }
                    },

                    image_size,
                    promo_id,
                    callFrom,
                    $error_selector
                );

            } else {
                showAlert(response.message, "danger", $error_selector);
            }


        },
        error: function(resp) {

            ////console.log(resp);
            showAlert("Problem occurred while sending requesddt.", "danger", $error_selector);
        },
    });
}

function local_media_ajax_submit($form, obj, callFrom, $error_selector = $(".message_output")) {

    var promo_id = obj.id;
    /*var json_form_obj = {
      "image_data":{
					big:{},
					medium:{},
					small:{}
		}
	};*/

    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    $_form = document.getElementById($form.attr('id'));
    formData = new FormData($_form);
    formData2 = new FormData($_form);
    formData3 = new FormData($_form);

    // var file1 = $('#image1')[0].files[0]
    // var file2 = $('#image2')[0].files[0]
    // var file3 = $('#image3')[0].files[0]
    // ////console.log('big');
    // ////console.log(file1);
    //
    // if (file1){
    // 	formData.append('file', file1)
    // 	formData.append('type', file1.type)
    // 	media_meta($form,formData,'big',promo_id,callFrom,$error_selector = $(".message_output"));
    // }
    // else{
    // 	if (file2){
    // 		formData.append('file', file2)
    // 		formData.append('type', file2.type)
    // 		media_meta($form,formData,'medium',promo_id,callFrom,$error_selector = $(".message_output"));
    // 	}
    // 	else{
    // 	if (file3){
    // 		formData.append('file', file3)
    // 		formData.append('type', file3.type)
    // 		media_meta($form,formData,'small',promo_id,callFrom,$error_selector = $(".message_output"));
    // 	}
    // 	else{
    // 		//no picture validation
    // 		promo_redirect(promo_id,callFrom);
    // 	}
    // }
    // }

    promo_redirect(promo_id, callFrom);

}


function promo_redirect(promo_id, callFrom) {
    var msg;

    if (callFrom == 'addPromo')
        msg = '成功添加新活动';
    else if (callFrom == 'editPromo')
        msg = '成功编辑活动';
    redirect_to("promotion-list.html?alert-success=" + msg);
}

function bulkEditConfirm($form, selected_action) {
    var msg = '';
    if (selected_action == 'bulk_blacklist') {
        msg = '确定批量加入黑名单？';
    } else if (selected_action == 'bulk_whitelist') {
        msg = '确定批量加入白名单？';
    }
    if (selected_action == 'bulk_delete') {
        msg = '确定批量删除？';
    }

    var status = confirm(msg);

    if (status == true) {
        bulkEdit($form, selected_action);
    }
}

function bulkEdit($form, selected_action) {
    var json_form_obj = {
        "data": []
    };
    var subdata = {};
    var promoAryId = []
    var name;
    var redirectMsg;

    //console.log("bulk_edit");
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    //console.log("action : " + $action);
    //console.log("method : " + $method);
    //console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));

    ////console.log('lala');
    // //console.log($_form);

    if (selected_action == 'bulk_delete') {
        json_form_obj['action'] = 'delete';
        redirectMsg = '成功批量删除';

        //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
        $('#form').find('td[data-field="id"] input[type=checkbox]').each(function(key, value) {
            if (($(this).attr('id') != 'checkall') && ($(this).prop("checked"))) {
                /*subdata['id'] = $(this).attr('id');
                ////console.log(subdata);
                json_form_obj.data.push(subdata);
                subdata = {}; //reset subdata for new row		*/
                promoAryId.push($(this).attr('id'))
            }
        });
        var nonSystemPromoIds = _.pullAll(promoAryId, systemPromoAry)
        nonSystemPromoIds.map(function(eachId) {
            json_form_obj.data.push({ "id": eachId })
        })
    }
    var formData = JSON.stringify(json_form_obj);
    clearAlert();
    $.ajax({
        type: ($method) ? $method : 'PATCH',
        //url: getBackendHost() + $action,
        url: link + $action,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: formData,
        success: function(response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {

                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + redirectMsg);
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                showAlert(obj.message, "danger", $error_selector);
            }
        },

        error: function() {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function deletePromo($form, selected_action) {
    var status = confirm('确定删除此活动？');

    if (status == true) {
        var promo_id = getQueryString('id');
        $('#delete_form').find('input').val(promo_id);

        $redirect_uri = $('#delete_form').attr('data-redirect');

        $.ajax({
            type: 'DELETE',
            url: link + '/api/cn/promotion',
            crossDomain: true,
            headers: getHeaders(),
            contentType: 'application/json',
            processData: false,
            // contentType: "charset=utf-8",

            data: JSON.stringify({ id: promo_id }),

            success: function(response, status, xhr) {
                ////console.log(response);
                obj = response;
                if (obj.code == 1) {
                    var message = '成功删除活动';
                    if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);
                } else if (obj.code == -1) {
                    redirect_to_login();
                } else {
                    showAlert(obj.message, "danger", $error_selector);
                }
            }

        });
    }
}

//to check or uncheck all checkbox
function checkall($form) {
    //console.log($form);
    if (checkall_indicator == true) {
        $form.find('td[data-field="id"] input[type=checkbox]').prop("checked", true);
        checkall_indicator = false;
    } else {
        $form.find('td[data-field="id"] input[type=checkbox]').prop("checked", false);
        checkall_indicator = true;
    }

}