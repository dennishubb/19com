$(document).ready(function () {
    getAlert();


    buildDataTable($("#main-datatable"), list_config);
    $("#main-datatable").init();


    /*$('#form-type').on('change', function () {
        current_type = $(this).val();
        getFilter();
    });

    $('#generalSearch').on('change', function () {
        current_search = $(this).val();
        getFilter();
    });*/

    $('#form-type').selectpicker();
   
});

var current_type = "";
var current_search = "";

function getFilter() {
    // $("#main-datatable").KTDatatable().setDataSourceParam('search[type]','');
    // $("#main-datatable").KTDatatable().setDataSourceParam('search[display]','');
    
	$("#main-datatable").KTDatatable().API.params = {};
	
	//filter by keyword
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[0][field]', "display")
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[0][value]', "%"+ $('#searchKeyword').val().toLowerCase()+"%")
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[0][operator]', "LIKE")
	
    //filter by ads type
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[1][field]', "ads_button.type")	
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[1][value]',  $('#form-type').val())
     $("#main-datatable").KTDatatable().setDataSourceParam('filter[1][operator]', "=")
	
//console.log('ni')
    $("#main-datatable").KTDatatable().load();
}

var list_config = {
    url: "/api/cn/ads_button?sort[field]=ads_button.id&sort[sort]=desc",
    method: "GET",
    page_size: 50,
    search_element: '#generalSearch',
    server_sorting: true,
    server_filtering: true,
    layout_header: true,
    columns: [
        {
            field: 'id',
            title: '#',
            sortable: false,
            width: 20,
            type: 'number',
            selector: {class: 'kt-checkbox--solid'},
            textAlign: 'center',
        },
        {
            field: 'ads_button.type',
            title: '项目',
            autoHide: false,
            template: function (row) {
                var status = {0: "无", 1: "广告", 2: "按钮", 3: "QRcode"};

                if (row.type) {
                    return status[row.type];
                } else {
                    return status[0];
                }
            }
        },
        {
            field: 'display',
            title: '显示文字',
            autoHide: false,
        },
        {
            field: 'ads_button.url',
            title: '链接',
            autoHide: false,
            template: function (row) {
                btnGroup = '';
                btnGroup += row.url;
                return btnGroup;
            }
        },
        {
            field: 'target',
            title: '目标属性 (target)',
            autoHide: false,
        },
        {
            field: 'rel',
            title: '关系属性 (rel)',
            autoHide: false,
        },
        {
            field: 'disabled',
            title: '狀態列',
            width: 55,
            sortable: false,
            template: function (row) {
                var status = {0: {'checked': 'checked="checked"'}, 1: {'checked': ''}};

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
            field: 'Actions',
            title: 'Actions',
            sortable: false,
            width: 80,
            overflow: 'visible',
            autoHide: false,
            template: function (row) {
                btnGroup = '';
                btnGroup += '<a href="ad-button-edit.html?id=' + row.id + '" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="修改"><i class="flaticon2-paper"></i></a>&nbsp;&nbsp;&nbsp;';
                btnGroup += '<a href="javascript:;" onclick="deleteItem(' + row.id + ');" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="删除"><i class="text-danger flaticon2-trash"></i></a>&nbsp;&nbsp;&nbsp;';
                return btnGroup;
            }
        }
    ]
};

var list_modalConfig = {
    url: "/api/cn/ads_button",
    params_temp: {
        ids: ""
    },
    method: "GET",
    columns: [
        {
            field: 'index',
            title: '#',
            width: 5,
            template: function (row, index) {
                return '<span style="width: 100%">' + (index + 1) + '</span>';
            }
        },
        {
            field: 'type',
            title: '项目',
            width: 20,
            template: function (row, index) {
                var HTML = '';

                HTML += '<select class="form-control" name="type[' + row.id + ']" >';
                HTML += '<option value="" selected>没选项</option>';
                HTML += '<option value="1" ' + ((row.type == 1) ? 'selected' : '') + '>广告</option>';
                HTML += '<option value="2" ' + ((row.type == 2) ? 'selected' : '') + '>按钮</option>';
                HTML += '<option value="3" ' + ((row.type == 3) ? 'selected' : '') + '>QRcode</option>';
                HTML += '</select>';

                return HTML;
                // return '<input name="type[' + row.id + ']" style="width: 100%" class="form-control" type="text" value="' + row.type + '">';
            }
        },
        {
            field: 'url',
            title: '链接',
            width: 20,
            template: function (row, index) {
                return '<input name="url[' + row.id + ']" style="width: 100%" class="form-control" type="text" value="' + row.url + '">';
            }
        },
        {
            field: 'display',
            title: '显示文字',
            width: 20,
            template: function (row, index) {
                return '<input name="display[' + row.id + ']" style="width: 100%" class="form-control" type="text" value="' + row.display + '">';
            }
        },
        {
            field: 'target',
            title: '目标属性 (target)',
            width: 10,
            template: function (row, index) {
                return '<input name="target[' + row.id + ']" style="width: 100%" class="form-control" type="text" value="' + row.target + '">';
            }
        },
        {
            field: 'rel',
            title: '关系属性 (rel)',
            width: 10,
            template: function (row, index) {
                return '<input name="rel[' + row.id + ']" style="width: 100%" class="form-control" type="text" value="' + row.rel + '">';
            }
        },
        {
            field: 'disabled',
            title: '狀態列',
            width: 5,
            template: function (row, index) {
                var is_checked = (row.disabled == 0) ? '' : 'checked="checked"';

                var HTML = '';
                HTML += '<span class="kt-switch kt-switch--success">';
                HTML += '<label>';
                HTML += '<input type="checkbox" name="disabled[' + row.id + ']" ' + is_checked + '>';
                HTML += '<span></span>';
                HTML += '</label>';
                HTML += '</span>';

                return HTML;
            }
        },
    ]
};

function getChecked() {
    var rows = $(".kt-datatable").find('input[type="checkbox"]').map(function () {
        if ($(this).val() !== "on") {   // Skip the checkbox on <th>
            return $(this).prop("checked") ? $(this).val() : null;
        }
    });

    clearAlert();
    if (rows.toArray().length < 1) {
        showAlert("批次修改需先选择记录。", "info");
        return;
    }

    modalConfig.params_temp.ids = rows.toArray();

    buildCustomTable($('#modal-datatable'), list_modalConfig);
    $('#batch_edit_modal').modal('show');
    $('input[name=ids]').val(rows.toArray().toString());
}