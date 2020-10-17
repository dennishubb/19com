var link = getBackendHost();
//var link = "http://test.19com.backend2:5280/";
var checkall_indicator = true;
var categoryPicker = $('#form-category-picker');
var leaguePicker = $('#form-league-picker');
var filterByYear = $('#form-year-picker');
var filterByMonth = $('#form-month-picker');
var filterByMember = $('#filter-member-id');
////console.log(link);

function getAdminList() {

    var redirect_uri = 'admin-list.html';
    datatable = $('#admin_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/user', //?search[type]=Member
                    headers: getHeaders(),
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
                    params: {

                        search: {type: 'Admin'}
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
             footer: true// display/hide footer
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
            {
                field: 'id',
                title: "<input type='checkbox' id='checkall'>",
                sortable: false,
                width: 20,

                template: function (data, i) {////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },
            {
                field: "username",
                title: "用户名ID",
                width: 50,
                sortable: true,
                // callback function support for column rendering
                template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="admin-edit.html?id=' + data.id + ' "class="kt-user-card-v2__name" >' + data.username + '</a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }
            },

            {
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
            },
            {
                field: 'user.name',
                title: "姓名",
                autoHide: false,
                sortable: true,
                width: 50,
                template: function (data, i) {
                    var number = i + 1;
                    while (number > 5) {
                        number = number - 3;
                    }

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.name +
                        '</div>';

                    return output;
                }
            },
            {
                field: 'phone',
                title: "电话",
                autoHide: false,
                sortable: true,
                template: function (data, i) {////console.log(data);
                    output = '<div class="kt-user-card-v2">' + data.phone + '</div>';

                    return output;
                }
            },
            {
                field: 'article_count',
                title: "文章数",
                autoHide: false,
                sortable: true,
                template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="#" class="kt-user-card-v2__name"></a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }
            },
            {
                field: 'comment_count',
                title: "留言数",
                sortable: true,
                autoHide: false,
                template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="#" class="kt-user-card-v2__name"></a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }
            },
            {
                field: 'role.name',
                title: "角色",
                sortable: true,
                autoHide: false,
                template: function (data, i) {////console.log(data);
                    output = '<div class="kt-user-card-v2">' + data.role + '</div>';

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
                template: function (row) {
                    return '' +
                        '<div>' +
                        '<a href="admin-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
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

function getMemberList() {

    var redirect_uri = 'member-list.html';
    datatable = $('#member_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/user', //?search[type]=Member
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
                    params: {

                        search: {type: 'Member'}
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
        columns: [
            {
                field: 'id',
                title: "<input type='checkbox' id='checkall' >",
                sortable: false,
                width: 20,

                template: function (data, i) {////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },
            {
                field: "username",
                title: "会员账号",
                sortable: true,
                width: 80,
                // callback function support for column rendering
                template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="member-edit.html?id=' + data.id + ' "class="kt-user-card-v2__name" >' + data.username + '</a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }


            },
            {
                field: "phone",
                title: "手机号",
                sortable: true,
                width: 50,
                // callback function support for column rendering
                template: function (data, i) {////console.log(data);
                    output = '<div class="kt-user-card-v2">' + data.phone + '</div>';

                    return output;
                }


            },
            {
                field: 'user.name',
                title: "姓名",
                sortable: true,
                autoHide: false,
                width: 50,
                template: function (data, i) {
                    var number = i + 1;
                    while (number > 5) {
                        number = number - 3;
                    }

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.name +
                        '</div>';

                    return output;
                }
            },

            {
                field: "email",
                title: "电子邮件",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.email +
                        '</div>';

                    return output;
                }
            },

            {
                field: "address",
                title: "地址",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.address +
                        '</div>';

                    return output;
                }
            },
            {
                field: "gender",
                title: "性别",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.gender +
                        '</div>';

                    return output;
                }
            },
            {
                field: "birth_at",
                title: "生日",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.birth_at +
                        '</div>';

                    return output;
                }
            },
            {
                field: "weibo",
                title: "微博",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.weibo +
                        '</div>';

                    return output;
                }
            },

            {
                field: 'level_id',
                title: "等级",
                sortable: true,
                autoHide: false,
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.level +
                        '</div>';

                    return output;
                }

            },
            {
                field: "win_rate",
                title: "总胜率",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering

            },
            {
                field: "user.points",
                title: "战数",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.points + '</div>';

                    return output;
                }
            },
            {
                field: "user.voucher",
                title: "卷数",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.voucher + '</div>';

                    return output;
                }
            },

            {
                field: 'comment_count',
                title: "留言数",
                sortable: true,
                autoHide: false,
                template: function (row) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + row.comment_count +
                        '</div>';

                    return output;
                    ;
                }
            },
            {
                field: 'login_at',
                title: "最后登入时间",
                autoHide: false,
                sortable: true,
                type: 'date',
                format: 'MM/DD/YYYY',

                template: function (row) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + row.login_at +
                        '</div>';

                    return output;
                    ;
                }


            },


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
                        '<a href="member-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        // '<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';


                },
            }]
    });


    // search
}

function getWinRateList() {

    var redirect_uri = 'win-rate-list.html';
	
	var sort = [{
        field: 'user_id',
        sort: "asc",
       
    }];
	sort.push({
        field: 'prediction_rate.sorting',
        sort: "asc",
    });
	
    datatable = $('#winrate_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/prediction_rate', //?search[type]=Member
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
					params: {sort}

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
        columns: [
            /*{
                field: 'id',
                title: "<input type='checkbox' id='checkall' >",
                sortable: false,
				autoHide: true,
                width: 20,

                template: function (data, i) {////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },*/
            {
                field: "user.username",
                title: "会员账号",
                sortable: true,
                //width: 50,
                // callback function support for column rendering
                template: function (data, i) {
					////console.log(data);
                   // var username=winRate_getUserName(data.user_id);
					
					
					//$.when(username).done(function (username) {////console.log(username)
						 output = '<div class="kt-user-card-v2" >'+data.user_username+'</div>';

                    return output;
					//});
					 
					
                }


            },
            {
                field: "prediction_rate.type",
                title: "项目",
                sortable: true,
                //width: 50,
                // callback function support for column rendering
               template: function (data, i) {
					if (data.type=='handicap')
						var display='让分';
					else if (data.type=='over_under')
						var display='大小分';
					else if (data.type=='single')
						var display='独赢';
					else if (data.type=='total')
						var display='总胜场';
					output = '<div class="kt-user-card-v2" >'+display+'</div>';
					return output;
				}


            },
            {
                field: 'year',
                title: "年份",
                sortable: true,
                autoHide: false,
               // width: 50,
                template: function (data, i) {
                    output = '<div class="kt-user-card-v2" >'+data.year+'</div>';

                    return output;
                }
            },
            {
                field: 'month',
                title: "月份",
                sortable: true,
                autoHide: false,
               // width: 50,
                template: function (data, i) {
                    output = '<div class="kt-user-card-v2" >'+data.month+'</div>';
                    return output;
                }
            },
            {
                field: 'prediction_rate.category_id',
                title: "分类目录",
                sortable: true,
                autoHide: false,
               // width: 50,
                template: function (data, i) {
                    output = '<div class="kt-user-card-v2" >'+data.category_display+'</div>';

                    return output;
                }
            },
            {
                field: 'prediction_rate.league_id',
                title: "联赛",
                sortable: true,
                autoHide: false,
               // width: 50,
                template: function (data, i) {
                  output = '<div class="kt-user-card-v2" >'+data.league_name_zh+'</div>';

                    return output;
                }
            },
            {
                field: 'prediction_rate.win_count',
                title: "胜场",
                sortable: true,
                autoHide: false,
               // width: 50,
                template: function (data, i) {
                    var number = i + 1;
                    while (number > 5) {
                        number = number - 3;
                    }

                  output = '<div class="kt-user-card-v2" >'+data.win_count+'</div>';

                    return output;
                }
            },

            {
                field: "prediction_rate.lose_count",
                title: "败场",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    output = '<div class="kt-user-card-v2" >'+data.lose_count+'</div>';

                    return output;
                }
            },

            {
                field: "prediction_rate.rate",
                title: "胜率[排名]",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {
                    output = '<div class="kt-user-card-v2" >'+data.rate+'['+data.rank+']</div>';
                    return output;
                }
            }
           /* {
                field: "Actions",
                width: 80,
                title: "Actions",
                sortable: false,
                autoHide: false,
                overflow: 'visible',
                template: function (row) {
                    return '' +
                        '<div>' +
                        '<a href="member-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        // '<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';


                },
            }*/
			]
    });


    // search
}

function winRate_getUserName(user_id){
	$.ajax({
        type: 'GET',
        url: link + '/api/cn/user',
        data: {
            id: user_id
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
			return obj.data.username;

        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger");
            unblockUI();
        }

    });
	
}

//get user detail
function getUserDetail(user_id) {
    $.ajax({
        type: 'GET',
        url: link + '/api/cn/user',
        data: {
            id: user_id
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
            $('#display_username').html(obj.data.username);

            if (obj.data.disabled == 1) {
                bl_button = '<a href="#" class="kt-nav__link btn btn-primary" onclick="blacklistUser(0);"><i class="fa fa-user-slash"></i><span class="kt-nav__link-text">从黑名单移除</span></a>';
                $('#display_status').html('黑名单用户');
                $('#display_status').addClass('btn btn-label-danger');
                $('#blacklist_div').prepend(bl_button);
                $('input[name="disabled"]').val(0); //set input 'disabled' to 0, in case user whitelist the account

            } else if (obj.data.disabled == 0) {
                bl_button = '<a href="#" class="kt-nav__link btn btn-primary" onclick="blacklistUser(1);"><i class="fa fa-user-slash"></i><span class="kt-nav__link-text">列为黑名单</span></a>';
                $('#display_status').html('白名单用户');
                $('#display_status').addClass('btn btn-label-success');
                $('#blacklist_div').prepend(bl_button);
                $('input[name="disabled"]').val(1);//set input 'disabled' to 0, in case user blacklist the account

            }
            $("[name='level_id']").val(obj.data.level_id);
            unblockUI();
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger");
            unblockUI();
        }

    });
}

//get all blacklist/whitelist user
function getBlackWhitelist(type,user_type) {

    var disabled = '';
	var pre_redirect_uri ='';
	var next_link ='';
	
	if (user_type=='Member'){
		pre_redirect_uri='member';
		next_link='member-edit.html';
	}
	else{
		pre_redirect_uri='admin';
		next_link='admin-edit.html';
	}
	
    if (type == 'whitelist') {
        disabled = 0;
        var redirect_uri = pre_redirect_uri+'-whitelist.html';
    } else if (type == 'blacklist') {
        disabled = 1;
        var redirect_uri = pre_redirect_uri+'-blacklist.html';
    }
    datatable = $('#blacklist_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/user', //?search[type]=Member
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
                    params: {
                        search: {
                            type: user_type,
                            disabled: disabled
                        }
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
        columns: [
            {
                field: 'id',
                title: "<input type='checkbox' id='checkall' >",
                sortable: false,
                width: 20,

                template: function (data, i) {////console.log(data);
                    output = "<input type='checkbox' id=" + data.id + ">";

                    return output;
                }
            },
            {
                field: "username",
                title: "会员账号",
                sortable: true,
                width: 80,
                // callback function support for column rendering
                template: function (data, i) {////console.log(data);
                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="'+next_link+'?from=' + redirect_uri + '&id=' + data.id + ' "class="kt-user-card-v2__name" >' + data.username + '</a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }


            },

            {
                field: 'user.name',
                title: "姓名",
                sortable: true,
                autoHide: false,
                width: 50,
                template: function (data, i) {
                    var number = i + 1;
                    while (number > 5) {
                        number = number - 3;
                    }

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.name +
                        '</div>';

                    return output;
                }
            },

            {
                field: "email",
                title: "电子邮件",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.email +
                        '</div>';

                    return output;
                }
            },

            {
                field: "gender",
                title: "性别",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + data.gender +
                        '</div>';

                    return output;
                }
            },
            {
                field: "disabled",
                title: "状态",
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {
                    var content = '';
                    var style = '';
                    if (data.disabled == 0) {
                        content = '白名单用户';
                        style = 'background-color:green;color:white';
                    } else if (data.disabled == 1) {
                        content = '黑名单用户';
                        style = 'background-color:red;color:white';
                    }

                    var output = '' +
                        '<div class="kt-user-card-v2" style=' + style + '>' + content +
                        '</div>';

                    return output;
                }
            },

            {
                field: 'login_at',
                title: "最后登入时间",
                autoHide: false,
                sortable: true,
                type: 'date',
                format: 'MM/DD/YYYY',

                template: function (row) {

                    var output = '' +
                        '<div class="kt-user-card-v2">' + row.login_at +
                        '</div>';

                    return output;
                    ;
                }


            },


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
                        '<a href="'+next_link+'?from=' + redirect_uri + '&id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        // '<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';


                },
            }]
    });
}

function getMemberRankList() {
    var redirect_uri = 'member-rank-list.html';
    datatable = $('#member_rank_datatable').KTDatatable({
        // datasource definition
        data: {
            type: 'remote',
            source: {
                read: {
                    url: link + 'api/cn/level', //?search[type]=Member
                    // url: 'https://keenthemes.com/metronic/tools/preview/api/datatables/demos/client.php',
                    //url: 'http://test.19com.backend:5280/api/cn/user',
                    method: 'GET',
                    params: {
                        sort: {
                            field: "sorting",
                            sort: "asc"
                        }
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
             footer: true // display/hide footer
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
            {
                field: 'level.id',
                title: "等级",
                width: 30,
                sortable: true,

                template: function (data, i) {////console.log(data);
                    output = '<div class="kt-user-card-v2">' + data.id + '</div>';

                    return output;
                }


            },
            {
                field: "level.name",
                title: "等级名称",
                width: 60,
                sortable: true,

                // callback function support for column rendering
                template: function (data, i) {////console.log(data);
                    var display = data.name;

                    if (display.length <= 0)
                        display = '-';

                    output = '' +
                        '<div class="kt-user-card-v2">' +

                        '<div class="kt-user-card-v2__details">' +
                        '<a href="member-rank-edit.html?id=' + data.id + ' "class="kt-user-card-v2__name" >' + data.name + '</a>' +

                        '</div>' +
                        '</div>';

                    return output;
                }


            },
            {
                field: "description",
                title: "等级描述",
                width: 70,
                sortable: true,

                template: function (data, i) {////console.log(data);
                    var display = data.description;

                    if (display.length <= 0)
                        display = '-';

                    output = '<div class="kt-user-card-v2">' + display + '</div>';

                    return output;
                }
            },
            {
                field: "user_count",
                title: "人数",
                width: 30,
                sortable: true
            },
            {
                field: 'points',
                title: "战数门槛",
                width: 60,
                sortable: true,
                autoHide: false
            },

           

            {
                field: "reward_description",
                title: "晋级奖励",
                width: 60,
                sortable: true,
                autoHide: false,
                // callback function support for column rendering
                template: function (data, i) {
                    var display = data.reward_description;

                    if (display.length <= 0)
                        display = '-';

                    var output = '' +
                        '<div class="kt-user-card-v2">' + display +
                        '</div>';

                    return output;
                }
            },
            {
                field: "Actions",
                title: "Actions",
                width: 60,
                sortable: false,
                autoHide: false,
                overflow: 'visible',
                template: function (row) {
                    return '' +
                        '<div>' +

                        //edit api not ready
                        '<a href="member-rank-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改">' +
                        '<i class="flaticon2-paper"></i></a>' +

                        // delete api not ready
                        '<a href="#" class="kt-nav__link" onclick="deleterecord(\'' + row.id + '\', \'' + redirect_uri + '\',\'level\' );" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除">' +
                        '<i class="text-danger flaticon2-trash"></i>' +
                        // '<span class="kt-nav__link-text">删除</span>' +
                        '</a>' +
                        '</div>';
                },
            }]
    });
}

function getMemberRankDetail(id) {
    return $.ajax({
        type: 'GET',
        url: link + '/api/cn/level',
        data: {
            id: id
        },
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response);
            obj = response;
            //console.log(obj.data);

            populateForm($("form"), obj.data);

            $('#display_username').html(obj.data.username);

            // if (obj.data.upload_data.url){//console.log(obj.data.upload_data.url);
			// $('#rank_image').attr('src','/'+obj.data.upload_data.url);}
            unblockUI();
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger");
            unblockUI();
        }

    });
}


function rank_edit_first($form){
	// var file1 = $('#image1')[0].files[0]
	////console.log(file1)
	
	//if user upload new image
	// if (file1)
	// 	local_media_ajax_submit($form, $error_selector = $(".message_output"),1)
	// else
    rank_edit($form);

}

function rank_edit($form, $error_selector = $(".message_output")) {
    var rank_id = getQueryString('id');
	// var file1 = $('#image1')[0].files[0]
    //console.log("rank_edit");
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect') + '?id=' + rank_id;

    //console.log("action : " + $action);
    //console.log("method : " + $method);
    //console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));

    ////console.log('lala');
    //console.log($_form);

    var json_form_obj = new Object();
    var name;

	// if (file1){
	// 	var json_form_obj = {
    //         "image_data": {
    //             //uploadtype: {}
    //         }
    //     }
	// 	json_form_obj.image_data.url = obj.data.url;
    //     json_form_obj.image_data.name = obj.data.name;
    //     json_form_obj.image_data.type = obj.data.type;
    //     json_form_obj.image_data.size = obj.data.filesize;
    //     json_form_obj.image_data.resolution = obj.data.resolution;
    //     json_form_obj.image_data.alt = obj.data.alt;
    //     json_form_obj.image_data.extension = obj.data.extension;
	// 	json_form_obj.extra=obj.extra;
	// }
	
    json_form_obj['id'] = rank_id;
    //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
    $('#form').find(':input').each(function (key, value) {
        name = $(this).attr("name");
        json_form_obj[name] = $(this).val();
    });
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
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

			
           if (obj.code == 1) {

               if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + '成功编辑资料');

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


//to check or uncheck all checkbox
function checkall($form) {
    //console.log($form);
    if (checkall_indicator == true) {
        $form.find('input[type="checkbox"]').prop("checked", true);
        checkall_indicator = false;
    } else {
        $form.find('input[type="checkbox"]').prop("checked", false);
        checkall_indicator = true;
    }

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
    //console.log($_form);

    if (selected_action == 'bulk_blacklist' || selected_action == 'bulk_whitelist') {
        if (selected_action == 'bulk_blacklist') {
            disabled = 1;
            redirectMsg = '成功批量加入黑名单';
        } else if (selected_action == 'bulk_whitelist') {
            disabled = 0;
            redirectMsg = '成功批量加入白名单';
        }

        //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
        $('#form').find('input[type="checkbox"]:checked').each(function (key, value) {
            if ($(this).attr('id') != 'checkall') {
                subdata['id'] = $(this).attr('id');
                subdata['disabled'] = disabled;
                ////console.log(subdata);
                json_form_obj.data.push(subdata);
                subdata = {};//reset subdata for new row
            }
        });
    } else if (selected_action == 'bulk_delete') {
        // json_form_obj['action'] = 'delete';
        redirectMsg = '成功批量删除';

        //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
        $('#form').find('input[type="checkbox"]:checked').each(function (key, value) {
            if ($(this).attr('id') != 'checkall') {
                subdata['id'] = $(this).attr('id');
                subdata['deleted'] = 1;
                json_form_obj.data.push(subdata);
                subdata = {};//reset subdata for new row
            }

        });
    }

    var formData = JSON.stringify(json_form_obj);
    //console.log(formData);

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
        success: function (response, status, xhr) {
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

        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },
    });
}

function user_edit($form, $error_selector = $(".message_output")) {
	
	var fullUri = getCurrentFullUri();
	var validation=false;
	
	if (fullUri.indexOf("member-edit") >= 0)
		validation = validateForm($form, ['phone','email']);
	else
		validation=true;
	
    if (validation == true) {
		var user_id = getQueryString('id');
		//console.log("member_edit");
		$form = $($form);

		$action = $form.attr('action');
		$method = $form.attr('method');
		$accept_charset = $form.attr('accept-charset');
		$redirect_uri = $form.attr('data-redirect') + '?id=' + user_id;

		//console.log("action : " + $action);
		//console.log("method : " + $method);
		//console.log("redirect : " + $redirect_uri);

		$_form = document.getElementById($form.attr('id'));

		////console.log('lala');
		//console.log($_form);

		var json_form_obj = new Object();
		var name;

		//TO GET NAME AND VALUE FROM FORM AND STRINGIFY
		$('#form').find(':input').each(function (key, value) {
			
			name = $(this).attr("name");
			
			if (name!='win_rate' && name!='points' && name!='voucher' && name!='commentcount')
				json_form_obj[name] = $(this).val();
		});
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
			success: function (response, status, xhr) {
				//console.log(response);

				obj = response;


				if (obj.code == 1) {
					if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + '成功编辑资料');
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
}

//use in single edit in member-edit and admin-edit page
function call_delete(redirect_uri) {
    deleterecord(getQueryString('id'), redirect_uri);
}

function deleterecord(del_id, $redirect_uri, api = 'user') {

    var status = confirm('确定要删除这项纪录？');

    if (status == true) {
        //$redirect_uri = 'member-list.html';
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
                    showAlert(obj.message, "danger", $error_selector);
                }
            }

        });
    }

}

function adduser($form) {
	var fullUri = getCurrentFullUri();
	var validate_field=['username', 'phone' ,'email', 'password', 'confirmpassword']; //for member
	
	if (fullUri.indexOf("admin") >= 0)
		validate_field=['username' ,'email', 'password', 'confirmpassword']; //for admin
	
    var validation = validateForm($form, validate_field);
	
    if (validation == true) {

        var member_id = getQueryString('id');
        //console.log("adduser");
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
        //console.log($_form);

        var json_form_obj = new Object();
        var name;

        //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
        $('#form').find(':input').each(function (key, value) {
            name = $(this).attr("name");
            json_form_obj[name] = $(this).val();
        });
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
            success: function (response, status, xhr) {
                //console.log(response);

                obj = response;

                if (obj.code == 1) {
                    var message = '成功添加新用户';
                    if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);
                } else if (obj.code == -1) {
                    redirect_to_login();
                } else {
                    showAlert(obj.message, "danger", $(".message_output"));
                }
            },

            error: function () {
                showAlert("Problem occurred while sending request.", "danger", $(".message_output"));
            },
        });
    }
}

function addMemberRank_image($form) {

    var validation = validateForm($form);
    if (validation == true)
        local_media_ajax_submit($form, $('.message_output'));

}

function addMemberRank_all($form, obj) {
    //var unindexed_array = $($form).serializeArray(); //file cant serialised
    //console.log(obj);
    //var validateField = [];
    $form = $($form);


    var validation = validateForm($form);
    //validation=true;

    if (validation == true) {

        $action = $form.attr('action');
        $method = $form.attr('method');
        $accept_charset = $form.attr('accept-charset');
        $redirect_uri = $form.attr('data-redirect');

        //console.log("action : " + $action);
        //console.log("method : " + $method);
        //console.log("redirect : " + $redirect_uri);

        $_form = document.getElementById($form.attr('id'));

        ////console.log('lala');
        //console.log($action);

        var json_form_obj = {
            "image_data": {
                //uploadtype: {}
            }
        }

        var name;

        //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
        $('#form').find(':input').each(function (key, value) {
            name = $(this).attr("name");

            //if (name=='file' && $(this).val().length>0)
            ////console.log($(this).val().length);


            json_form_obj[name] = $(this).val();
        });

        var uploadtype = $form.find('input[name=type]').val();
        json_form_obj.image_data.url = obj.data.url;
        json_form_obj.image_data.name = obj.data.name;
        json_form_obj.image_data.type = obj.data.type;
        json_form_obj.image_data.size = obj.data.filesize;
        json_form_obj.image_data.resolution = obj.data.resolution;
        json_form_obj.image_data.alt = obj.data.alt;
        json_form_obj.image_data.extension = obj.data.extension;
		
		json_form_obj.extra=obj.extra;
        var formData = JSON.stringify(json_form_obj);
        //console.log(formData);

        //$formData = new FormData($_form);


        //local_media_ajax_submit($form,$('.message_output'));	//put in success after api ready

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
            success: function (response, status, xhr) {
                //console.log(response);

                obj = response;

                if (obj.code == 1) {
					media_save($form, obj, $redirect_uri);
                    var message = '成功添加新会员等级';
                    
                } 
            },

            error: function () {
                showAlert("Problem occurred while sending request.", "danger", $error_selector);
            },
        });
    }
}

function validatePhone() {
   var phone =$('[name="phone"]').val();
    var validNumber = "0123456789-+";


    for(i = 0; i < phone.length; i++) {
			////console.log(validNumber.indexOf(phone.charAt(i)))
            if(validNumber.indexOf(phone.charAt(i)) == -1) {
                    //alert("You have entered an invalid phone number");
                    return false;
                }
            }

        return true;
}

function validateForm($form, validateField = []) {
    //alert(validateField.length);

    var msg = '';
    var name = '';
    var pw_validate = false;
    var phone_validate = false;

    if (validateField.length > 0) {//validate certain fields only //member-add, admin-add

        validateField.forEach(function (key, i) { //check empty

            temp = $('input[name=' + key + ']').val();
            ////console.log(temp);
            label_name = $('#' + key + '_label').html();

            if (temp.length <= 0)
                msg += label_name + ',';
        });

        if (validateField.indexOf('password') > 0 && validateField.indexOf('confirmpassword') > 0) {
            pw = $('input[name=password]').val();
            confirm_pw = $('input[name=confirmpassword]').val();

            if (pw != confirm_pw && msg.length <= 0) {
                msg = '密码不一致';
                pw_validate = true;
            }
        }
		if (validateField.indexOf('phone') >0){
			var phone_validation=validatePhone();
			if (phone_validation==false){
				msg = '手机号码只能接受 “0-9” “+” “-” ';
				phone_validate=true;
			}
		}


    } else { //validate all //member rank add
        $form.find(':input').each(function (key, value) {
            temp = $(this).attr("name");

            if (temp == 'tempfile')
                return;//continue
            else
                label_name = $('#' + temp + '_label').html();

            if ($(this).val().length <= 0) {
                msg += label_name + ',';
            }
        });
    }

    if (msg.length > 0) {

        if (pw_validate == false && phone_validate==false) {
            msg = msg.slice(0, -2);//remove last comma
            msg += ' 不能为空';
        }

        showAlert(msg, "danger", $(".message_output"));
        return false;
    } else
        return true;

}

function blacklistUser(disabled) {
    var json_form_obj = new Object();
    var name;
    var confirmMsg;
    var redirectMsg;
    var id = getQueryString('id');
    $action = 'api/cn/user';
    $redirect_uri = $('#blacklistform').attr('data-redirect') + '?id=' + id;

    if (disabled == 0)
        confirmMsg = '确定要将此用户从黑名单移除？';
    else if (disabled == 1)
        confirmMsg = '确定要将此用户列入黑名单？';

    var status = confirm(confirmMsg);

    if (status == true) {
        clearAlert();

        /*name='id';
        json_form_obj[name] =id;

        name='disabled';
        json_form_obj[name] ='1';*/

        $('#blacklistform').find(':input').each(function (key, value) {
            name = $(this).attr("name");
            json_form_obj[name] = $(this).val();
        });

        var formData = JSON.stringify(json_form_obj);
        //console.log(formData);

        $.ajax({
            type: 'PUT',
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
                    if (json_form_obj['disabled'] == 0)
                        redirectMsg = '成功将用户从黑名单移除';
                    else if (json_form_obj['disabled'] == 1)
                        redirectMsg = '成功将用户加入黑名单';
                    else
                        redirectMsg = 'disabled错误';

                    if (obj.redirect) redirect_to($redirect_uri + "&alert-success=" + redirectMsg);
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
}


function getRolePermission(role_id, table_id) {
    var role_id = $('#form').find('#role_id').val();
    var permission_id;


    $.ajax({
        type: 'GET',
        url: link + '/api/cn/role_permission',
        data: {search: {role_id: role_id}},

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response);
            obj = response;
            curr_permissoin_id = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {

                permission_id = obj.data[index].permission_id;
                ////console.log(permission_id);

                $('#form').find('#' + table_id + ' input[name=' + permission_id + ']').prop("checked", true);
                $('#form').find('#' + table_id + ' input[name=' + permission_id + ']').attr('id', obj.data[index].id);

                curr_permissoin_id.push(permission_id);
                ////console.log(value);

            });
            ////console.log(curr_permissoin_id);
            //$('#display_username').html(obj.data.username);
        }

    });
}

function setRolePermission($form, table_id) {
    ////console.log('hh');
    ////console.log(json_delform_obj);
    var json_form_obj = {
        "data": []
    };
    var json_delform_obj = {
        "data": []
    };

    var subdata = {};
    var name;
    var thisid;
    var role_id = $('#form').find('#role_id').val();

    //FOR BATCH ADD
    $form.find('#' + table_id + ' input[type="checkbox"]:checked').each(function (key, value) {
        ////console.log(curr_permissoin_id);
        ////console.log($(this).attr('name'));
        thisid = parseInt($(this).attr('name'));

        //if checkbox permission not exist, insert into json
        if (curr_permissoin_id.indexOf(thisid) == -1) {
            subdata['role_id'] = role_id;
            subdata['permission_id'] = $(this).attr('name');
            ////console.log(subdata);
            json_form_obj.data.push(subdata);
            subdata = {};//reset subdata for new row
        }
    });

    if (json_form_obj.data.length > 0) {
        var formData = JSON.stringify(json_form_obj);
        ////console.log(formData);
        permissionAjax($form, formData); //to add permission
    }

    //FOR BATCH DELETE
    ////console.log(JSON.stringify(json_delform_obj));


    json_delform_obj['action'] = 'delete';

    $form.find('#' + table_id + ' input:checkbox:not(:checked)').each(function (key, value) {

        //IF CHECKBOX UNCHECKED AND ID EXIST -> USER PERFORM DELETE
        if ($(this).attr('id') > 0) {

            ////console.log(JSON.stringify(json_delform_obj));

            subdata['id'] = $(this).attr('id');
            json_delform_obj.data.push(subdata);
            subdata = {};//reset subdata for new row
            $(this).removeAttr('id');
        }
    });

    if (json_delform_obj.data.length > 0) {

        var delformData = JSON.stringify(json_delform_obj);
        //console.log(delformData);
        permissionAjax($form, delformData);//to delete permission
    }


}

function permissionAjax($form, formData) {
    ////console.log("setRolePermission");
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    ////console.log("action : " + $action);
    ////console.log("method : " + $method);
    ////console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));

    ////console.log('lala');
    ////console.log($_form);

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
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            //no redirect, show pop up msg said success
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

function getPermissionTable(tbody_id) {

    var tr = '';
    $.ajax({
        type: 'GET',
        url: link + '/api/cn/permission',
        //data:{search:{role_id:role_id}},

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            //console.log(response);
            obj = response;
            curr_permissoin_id = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {
                ////console.log(value);

                tr += '<tr>';
                tr += "<td><input type='checkbox'";
                tr += "name='";
                tr += value.id;
                tr += "'></td>";

                tr += '<td>';
                tr += value.name;
                tr += '</td>';

                tr += '<td>';
                tr += value.description;
                tr += '</td>';
                tr += '</tr>';


            });
            ////console.log(curr_permissoin_id);
            $('#' + tbody_id).html(tr);
        }

    });
}


//new role permission function as below ya
function getPermissionMenuContent(role_id, role_name) {
    //var table_id='table'+role_id;
    var header = '<div class="kt-heading kt-heading--md">' + role_name + '权限列表</div>';
    var table = "<table class='table' width='50%' id='permission_table'>";
    table += "<thead class='thead-light'>" +
        '<tr>' +
        "<th width='30%'><b>" + role_name + "权限</b></th>" +
        "<th width='30%'><b>功能</b></th>" +
        "<th width='50%'><b>功能描述</b></th>" +
        '</tr>' +
        '</thead>' +
        '<tbody>';
    var tr = '';
    var content = '';

    $.ajax({
        type: 'GET',
        url: link + '/api/cn/permission',
        data: {
            sort: {
                field: "sorting",
                sort: "asc"
            }
        },

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response);
            obj = response;
            curr_permissoin_id = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {
                ////console.log(value);

                tr += '<tr>';
                tr += "<td><input type='checkbox'";
                tr += "name='";
                tr += value.id;
                tr += "'></td>";

                tr += '<td>';
                tr += value.name;
                tr += '</td>';

                tr += '<td>';
                tr += value.description;
                tr += '</td>';
                tr += '</tr>';


            });
            ////console.log(tr);
            table = table + tr + '</tbody></table>';
            content = header + table;
            $('#permission_menu_content').html(content);

            checkPermissionTable();//after table load, check the checkbox
        }

    });
}

function checkPermissionTable() {
    var role_id = $('#kt_form').find('#role_id').val();
    var permission_id;


    $.ajax({
        type: 'GET',
        url: link + '/api/cn/role_permission',
        data: {search: {role_id: role_id}},

        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response);
            obj = response;
            curr_permissoin_id = [];
            ////console.log(obj.data);
            ////console.log(role_id);

            $.each(obj.data, function (index, value) {

                permission_id = obj.data[index].permission_id;
                ////console.log(permission_id);

                $('#kt_form').find(' input[name=' + permission_id + ']').prop("checked", true);
                $('#kt_form').find(' input[name=' + permission_id + ']').attr('id', obj.data[index].id);
                //permission_table
                curr_permissoin_id.push(permission_id);
                ////console.log(value);

            });
            ////console.log(curr_permissoin_id);
            //$('#display_username').html(obj.data.username);
        }

    });
}

function setRolePermissionV2($form) {
    //console.log();

    var json_form_obj = {
        "data": []
    };
    var json_delform_obj = {
        "data": []
    };

    var subdata = {};
    var name;
    var thisid;
    var role_id = $form.find('#role_id').val();
    var addAction = false;
    var delAction = false;

    ////console.log($form.find('input[type="checkbox"]:checked'));
    ////console.log(role_id);


    //FOR BATCH ADD
    $form.find('input[type="checkbox"]:checked').each(function (key, value) {
        ////console.log(curr_permissoin_id);
        ////console.log($(this).attr('name'));
        thisid = parseInt($(this).attr('name'));

        //if checkbox permission not exist, insert into json
        if (curr_permissoin_id.indexOf(thisid) == -1) {
            subdata['role_id'] = role_id;
            subdata['permission_id'] = $(this).attr('name');
            ////console.log(subdata);
            json_form_obj.data.push(subdata);
            subdata = {};//reset subdata for new row
        }
    });
    //alert();
    if (json_form_obj.data.length > 0) {
        var formData = JSON.stringify(json_form_obj);
        addAction = true;
        ////console.log(formData);
        var add_action = permissionAjaxV2($form, formData); //to add permission
    }
    //console.log('addAction=' + addAction);
    //FOR BATCH DELETE
    ////console.log(JSON.stringify(json_delform_obj));


    json_delform_obj['action'] = 'delete';

    $form.find('input:checkbox:not(:checked)').each(function (key, value) {

        //IF CHECKBOX UNCHECKED AND ID EXIST -> USER PERFORM DELETE
        if ($(this).attr('id') > 0) { //checkbox id= permission id set when table onload

            ////console.log(JSON.stringify(json_delform_obj));

            subdata['id'] = $(this).attr('id');
            json_delform_obj.data.push(subdata);
            subdata = {};//reset subdata for new row
            $(this).removeAttr('id');
        }
    });

    if (json_delform_obj.data.length > 0) {

        var delformData = JSON.stringify(json_delform_obj);
        delAction = true;
        ////console.log(delformData);
        var del_action = permissionAjaxV2($form, delformData);//to delete permission
    }
    //console.log('delAction=' + delAction);

    //when done action, redirect

    if (addAction == false && delAction == false) { //if user hit save button without any operation selected
        alert('Please check or uncheck checkbox to perform add/delete action.');
    }

    $(document).ajaxStop(function () {
        if (addAction == true && delAction == true) {
            callRedirect($form, role_id, '成功添加和删除权限');
        } else if (addAction == true && delAction == false) {
            callRedirect($form, role_id, '成功添加权限');
        } else if (addAction == false && delAction == true) {
            callRedirect($form, role_id, '成功删除权限');
        }

    });

    /*$.when(add_action, del_action).done(function (add_action, del_action) {
        $redirect_uri = $form.attr('data-redirect')+'?role_id='+role_id;
        if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + obj.message);
        } else if (obj.code == -1) {
            redirect_to_login();
        } else {
            showAlert(obj.message, "danger", $error_selector);
        }
    });*/
}

function callRedirect($form, role_id, msg) {
    $redirect_uri = $form.attr('data-redirect') + '?role_id=' + role_id + '&';
    redirect_to($redirect_uri + "alert-success=" + msg);
}

function permissionAjaxV2($form, formData) {
    ////console.log("setRolePermission");
    $form = $($form);

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    ////console.log("action : " + $action);
    ////console.log("method : " + $method);
    ////console.log("redirect : " + $redirect_uri);

    $_form = document.getElementById($form.attr('id'));

    ////console.log('lala');
    ////console.log($_form);

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
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            //no redirect, show pop up msg said success
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


//below function is for image upload
function local_media_ajax_submit($form, $error_selector = $(".message_output"),edit=0) {

    $form = $($form);

    addMemberRank_all($form, obj);
    return;

    $action = $form.attr('action');
    $method = $form.attr('method');
    $accept_charset = $form.attr('accept-charset');
    $redirect_uri = $form.attr('data-redirect');

    $_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);

	
	var file = $('#image1')[0].files[0];
	$formData.append('file', file);
	$formData.append('type', file.type);
	$formData.append('folder','user_level');
	
	
	////console.log(file);
    //clearAlert($error_selector);
	////console.log($formData);
	
////console.log(getHost());

    $.ajax({
        type: 'POST',
        //url: 'http://fdcb6912.ngrok.io/assets/php/media-meta.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url: getHost() + '/assets/php/media-meta.php',
        contentType: false,
        cache: false,
        processData: false,
        data: $formData,
        success: function (response, status, xhr) {
            //console.log(response);

            obj = response;

            if (obj.code == 1) {

                /*backend_media_ajax_submit($form,
                    {
                        media_meta_data: obj.data,
                        extra:obj.extra,
                        method: "PUT",
                        redirect: $redirect_uri,
                        params: {
                            image_data: response.image_data,
                            extra: response.extra,
                        }
                    },
                    $error_selector
                );*/
				
				
				if (edit==0)	//if add new record
					addMemberRank_all($form, obj);
				else if (edit==1) //if edit recore
					rank_edit($form);
					
					
            } else {
                showAlert(response.message, "danger", $error_selector);
            }


        },
        error: function (resp) {

            //console.log(resp);
            showAlert("Problem occurred while sending requesddt.", "danger", $error_selector);
        },
    });

}


//no use for now
function backend_media_ajax_submit($form, $data, $error_selector = $(".message_output")) {

    $method = $data.method;
    $redirect_uri = $data.redirect;

    var uploadtype = $form.find('input[name=type]').val();

    var json_form_obj = {
        "image_data": {
            uploadtype: {}
        }
    };
    json_form_obj['id'] = 21;

    json_form_obj.image_data.uploadtype.url = $data.media_meta_data.url;
    json_form_obj.image_data.uploadtype.name = $data.media_meta_data.name;
    json_form_obj.image_data.uploadtype.type = $data.media_meta_data.type;
    json_form_obj.image_data.uploadtype.size = $data.media_meta_data.filesize;
    json_form_obj.image_data.uploadtype.resolution = $data.media_meta_data.resolution;
    json_form_obj.image_data.uploadtype.alt = $data.media_meta_data.alt;
    json_form_obj.image_data.uploadtype.extension = $data.media_meta_data.extension;

    json_form_obj.extra = $data.extra;


    var formData = JSON.stringify(json_form_obj);
    ////console.log(document.getElementById("sitemap").files[0].name);
    //console.log(formData);

    $.ajax({
        type: ($method) ? $method : 'PUT',
        url: getBackendHost() + '/api/cn/level',
        //url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',

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

                ////console.log(obj.code);
                media_save($form, obj, $redirect_uri);

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

function media_save($form, obj, $redirect_uri) {
	//console.log(obj)
    //$form.find('#extra').val(obj.extra);
   
	
	$form = $($form);
	$_form = document.getElementById($form.attr('id'));
    $formData = new FormData($_form);
	
	$formData.append('tempfile', obj.extra.extra)
	
    //clearAlert($error_selector);
    //console.log($formData);

    var message = '成功添加新会员等级';
    redirect_to($redirect_uri + "?alert-success=" + message);

    return;

    $.ajax({
        type: 'POST',
        // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
        //url: getHost()+'/assets/php/media-meta.php',
        url: getHost() + '/assets/php/media-save.php',
        contentType: false,
        cache: false,
        processData: false,
        data: $formData,
        success: function (response, status, xhr) {
            //console.log(response);
            obj = response;

			 if (obj.code == 1) {
                    var message = '成功添加新会员等级';
                    if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + message);
                } else if (obj.code == -1) {
                    redirect_to_login();
                } else {
                    showAlert(obj.message, "danger", $(".message_output"));
                }
        },
        error: function (resp) {

            //console.log(resp);
            showAlert("Problem occurred while sending requesddt.", "danger", $error_selector);
        },
    });
}

function getAllMainCategories() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=sport&search[parent_id]=0'
    )
}

function getAllLeague(category_id) {
    return callAjaxFunc(
        'GET', {},
        '/api/cn/league?search[league.category_id]=' + category_id + '&sort[field]= use_count &sort[sort]=desc'
    )
}

function get_league_dropdown(category_id) {
    //league dropdown list
    leaguePicker.empty();
    leaguePicker.selectpicker('refresh');
    leaguePicker.append($(`<option value="">无</option>`))
    getAllLeague(category_id).then(response => {
        response.data.forEach(league => {
            leaguePicker.append($(`<option value="${league.id}">${league.name_zh}</option>`))
        })
        leaguePicker.selectpicker('refresh');
    })

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

function filterByItem() {
    //filter by member
    datatable.setDataSourceParam('filter[0][field]', "user.username")
    datatable.setDataSourceParam('filter[0][value]', "%"+ filterByMember.val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")
    //filter by year
    datatable.setDataSourceParam('filter[1][field]', "prediction_rate.year")
    datatable.setDataSourceParam('filter[1][value]',  filterByYear.val())
    datatable.setDataSourceParam('filter[1][operator]', "=")
    //filter by month
    datatable.setDataSourceParam('filter[2][field]', "prediction_rate.month")
    datatable.setDataSourceParam('filter[2][value]',  filterByMonth.val())
    datatable.setDataSourceParam('filter[2][operator]', "=")
    //filter by category_id
    datatable.setDataSourceParam('filter[3][field]', "prediction_rate.category_id")
    datatable.setDataSourceParam('filter[3][value]',  categoryPicker.val())
    datatable.setDataSourceParam('filter[3][operator]', "=")
    //filter by league
    datatable.setDataSourceParam('filter[4][field]', "prediction_rate.league_id")
    datatable.setDataSourceParam('filter[4][value]', leaguePicker.val())
    datatable.setDataSourceParam('filter[4][operator]', "=")

    datatable.load();
}