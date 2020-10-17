var $error_selector = $(".message_output")
var categoryPicker = $('select[name=parent_id]');
function submitBatchUpdate(newCategory){
    var selectedIds = getSelectedIds();
    var parsedData = selectedIds.map(function(id) {
        return {
            id,
            parent_id: newCategory,
        }
    })
    batchUpdateCategory(parsedData).then(function() {
        $('#pop-up-modal').modal('hide');
        var datatable = $("#main-datatable").KTDatatable();
        datatable.load()
    })
}

function batchUpdateCategory(category) {
    return callAjaxFunc('PATCH', {
            data: category
        },
        '/api/cn/category?search[type]=sport'
    )
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

    batchDeleteSubCategory(selectedIds)
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
function batchUpdateSubCategory(subCategory) {
    return callAjaxFunc('PATCH', {
            data: subCategory
        },
        '/api/cn/category'
    )
}
function createSubCategory(subCategory) {
    return callAjaxFunc('POST', subCategory,
    '/api/cn/category')
}

function updateSubCategory(subCategory) {
    return callAjaxFunc('PUT', subCategory,
    '/api/cn/category')
}

function batchDeleteSubCategory(subCategoriesIds) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    subCategoriesIds.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/category'
    )
 }

function getAllMainCategories() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=sport&search[parent_id]=0&search[disabled]=0'
    )
}

function getSubCategory(categoryId) {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=sport&search[disabled]=0&search[parent_id]='+categoryId
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

function filterByItem() {
    var datatable = $("#main-datatable").KTDatatable();
    datatable.setDataSourceParam('filter[0][field]', "display")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $("#searchCategory").val() +"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")
    datatable.load();
}