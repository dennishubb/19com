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
            word: data[`word[${id}]`],
            disabled: data[`disabled[${id}]`] == "on" ? 0 : 1
        }
    })

    batchUpdateIllegalWord(parsedData).then(function() {
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
    datatable.setDataSourceQuery({
        ...datatable.getDataSourceQuery(),
        [paramName]: value,
    });
    datatable.load();
}

function handleBatchDelete(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchDeleteIllegalWords(selectedIds)
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
    }else if(action==''){
        msg='请选择功能';
    }
    return confirm(msg);
}

function createIllegalWord(illegalWord) {
    return callAjaxFunc('POST', illegalWord,
    '/api/cn/illegal_words')
}

function updateIllegalWord(illegalWord) {
    return callAjaxFunc('PUT', illegalWord,
    '/api/cn/illegal_words')
}

function batchUpdateIllegalWord(illegalWords) {
   return callAjaxFunc('PATCH', {
       data: illegalWords
   },
   '/api/cn/illegal_words')
}

function batchDeleteIllegalWords(wordsIds) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    wordsIds.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/illegal_words'
    )
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

function filterByIllegalWord() {
    var datatable = $("#main-datatable").KTDatatable();
    //filter by message report
    datatable.setDataSourceParam('filter[0][field]', 'word');
    datatable.setDataSourceParam('filter[0][value]', "%"+ $("#searchIllegalWord").val().toLowerCase() +"%")
    datatable.setDataSourceParam('filter[0][operator]', 'LIKE');
    
    datatable.load();
}
