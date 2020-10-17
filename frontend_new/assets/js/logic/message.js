var $error_selector = $(".message_output")
function getSelectedIds() {
    var datatable = $('#main-datatable').KTDatatable()
    
    return datatable
            .getSelectedRecords()
            .find('td[data-field="id"] input[type=checkbox]')
            .toArray()
            .map(function (input) {
                return parseInt($(input).val())
            })
}

function submitBatchUpdate(){
    var selectedIds = getSelectedIds();
    var modalDatatable = $('#modal-datatable').KTDatatable()
    var form = document.getElementById('modal-form');
    var formData = new FormData(form);
    var data = {};
    formData.forEach(function(value, key){
        data[key] = value;
    });
    var parsedData = selectedIds.map(function(id) {
        return {
            id,
            message: data[`message[${id}]`] || '',
            reply: data[`reply[${id}]`] || '',
            status: data[`status[${id}]`],
            read: data[`read[${id}]`],
        }
    })

    batchUpdateMessage(parsedData).then(function() {
        $('#batch_edit_modal').modal('hide');
        var datatable = $("#main-datatable").KTDatatable();
        datatable.load()
    })
}

function getChecked() {
    var selectedIds = getSelectedIds();
    clearAlert();

    if (selectedIds.length < 1) {
        showAlert("批次修改需先选择记录。", "info");
        return;
    }
    var selectedData = []

    selectedIds.forEach(function (selectedId) {
        var found = tableDataRaw.find(function (row) { 
            return row.id == selectedId
        })
        selectedData.push(found)
    })

    modalConfig.data.source = selectedData
    var oldDatatable = null
    try{
        oldDatatable= $('#modal-datatable').KTDatatable()
    } catch(err) {}

    oldDatatable && oldDatatable.destroy();
    $('#modal-datatable').KTDatatable(modalConfig);
    $('#modal-datatable').KTDatatable().load()
    $('#batch_edit_modal').modal('show');
    //$('input[name=ids]').val(rows.toArray().toString());
}

function addFiltering(paramName, value) {
    var datatable = $("#main-datatable").KTDatatable();
    datatable.setDataSourceParam('search['+paramName+']', value)
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params['search['+paramName+']']
}

function handleBatchReject(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchRejectMessage(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}
function handleBatchApprove(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchApproveMessage(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}
function handleBatchDelete(selectedIds, link_API='/api/cn/message') {
    var datatable = $("#main-datatable").KTDatatable();

    batchDeleteMessage(selectedIds,link_API)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}
function showBulkActionPrompt(action){
    var msg='';

    if (action=='bulk_delete'){
        msg='确定批量删除？';
    }else if (action==''){
        msg='请选择功能';
    }else if (action=='bulk_reject'){
        msg='确定批量驳回？';
    }else if (action=='bulk_approve'){
        msg='确定批量批准？';
    }
    return confirm(msg);
}

function batchUpdateMessage(message) {
    return callAjaxFunc('PATCH', {
            data: message
        },
        '/api/cn/message'
    )
 }

function batchApproveMessage(messageIds) {
    return batchUpdateMessage(messageIds, 'approve')
}

function batchRejectMessage(messageIds) {
    return batchUpdateMessage(messageIds, 'reject')
}

function batchUpdateMessage(messageIds, status) {
    return callAjaxFunc('PATCH', {
        data: messageIds.map(function (id) {
            return {
                id,
                status
            }
        })
    },
    '/api/cn/message')
}

function batchDeleteMessage(messageIds,callAPILink) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    messageIds.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        callAPILink
    )
 }

function  getAllEvent(){
    return callAjaxFunc('GET', 
    {},
    '/api/cn/event')
}

function createReplyMessage(replyMessage) {
    //token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMDgsImV4cCI6MTU4Njg1MTIzMiwiaXNzIjoiMTljb20iLCJpYXQiOjE1ODY4NDQwMzJ9.xHZI8gac4EUP424lOhS9UMPpUP1BQt0WBNh_PteykNU"
    return callAjaxFunc('POST', replyMessage,
    '/api/cn/message')
}

function callAjaxFunc(method, data, url,token=null) {
    return $.ajax({
        type: method,
        url: getBackendHost() + url,
        data: JSON.stringify(data),
        crossDomain: true,
        //headers: token? { Authorization: token } : getHeaders(),
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function filterByItem() {
    var datatable = $("#main-datatable").KTDatatable();
    //filter by message
    datatable.setDataSourceParam('filter[0][field]', "message.message")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $("#searchComment").val().toLowerCase() +"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")
    //filter by match
    datatable.setDataSourceParam('filter[1][field]', "message.chatroom_id")
    datatable.setDataSourceParam('filter[1][value]', $("#match-filter").val())
    datatable.setDataSourceParam('filter[1][operator]', "=")

    datatable.load();
}

function filterByMessage() {
    var datatable = $("#main-datatable").KTDatatable();
    //filter by message
    datatable.setDataSourceParam('filter[0][field]', "message.message")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $("#searchMessage").val().toLowerCase() +"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")

    datatable.load();
}

function filterByMsgReport() {
    var datatable = $("#main-datatable").KTDatatable();
    //filter by message report
    datatable.setDataSourceParam('filter[0][field]', 'message.message');
    datatable.setDataSourceParam('filter[0][value]', "%"+ $("#searchMsgReport").val().toLowerCase() +"%")
    datatable.setDataSourceParam('filter[0][operator]', 'LIKE');
    
    datatable.load();
}
