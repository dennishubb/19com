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

function showBulkActionPrompt(action){
    var msg='';

    if (action=='bulk_delete'){
        msg='确定批量删除？';
    }else if(action==''){
        msg='请选择功能';
    }
    return confirm(msg);
}

function handleBatchDelete(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchDeleteRedeem(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function batchDeleteRedeem(redeems) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    redeems.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/gift_redeem'
    )
 }
//category dropdown list
function giftDropdown(){
    return getAllGift().then(response => {
        giftPicker.empty();
        response.data.forEach(gift => {
            giftPicker.append($(`<option value="${gift.id}">${gift.name}</option>`))
        })
        giftPicker.selectpicker('refresh');
    })
}
//category dropdown list
function categoryDropdown(){
    getAllMainCategories().then(response => {
        response.data.forEach(category => {
            categoryPicker.append($(`<option value="${category.id}">${category.display}</option>`))
        })
        categoryPicker.selectpicker('refresh');
    })
}

function subCategoryDropdown(){
    //sub category dropdown list
    categoryPicker.change(function(){
        var selectedCategoryId = categoryPicker.val()
        subCategoryPicker.empty();
        getSubCategory(selectedCategoryId).then(response => {
            response.data.forEach(subCategory => {
                subCategoryPicker.append($(`<option value="${subCategory.id}">${subCategory.display}</option>`))
            })
            subCategoryPicker.selectpicker('refresh');
        })
    })
}

function addNewCategory() {
    var newCategory = prompt("请输入类别");
    if (newCategory != null) {
        addedNewCategory(newCategory)
        categoryDropdown()
    }
}

function addNewSubCategory() {
    var newSubCategory = prompt("请输入子级类别");
    if (newSubCategory != null) {
        addedNewSubCategory(newSubCategory)
        subCategoryDropdown()
    }
}

function addedNewCategory(redeemCategory){
    return callAjaxFunc('POST', redeemCategory,
    '/api/cn/gift_redeem')
}

function addedNewSubCategory(redeemSubCategory){
    return callAjaxFunc('POST', redeemSubCategory,
    '/api/cn/gift_redeem')
}

function addFiltering(paramName, value) {
    var datatable = $("#main-datatable").KTDatatable();
    datatable.setDataSourceParam('search['+paramName+']', value)
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params['search['+paramName+']']
}

function postRedeem(redeemId) {
    $redirect_uri = "redeem-list.html"
    $error_selector = $(".message_output")
    getRedeem(redeemId)
        .then(response => {
            var redeem = response.data
            return editRedeem(redeem);
        })
        .then(response => {
            obj = response;
            if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=Redeem%20posted");
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                window.scrollTo(0,0);
                showAlert(obj.message, "danger", $error_selector);
            }
        })
        .catch(function (error) {
            window.scrollTo(0,0);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function editRedeem(redeem){
    return $.ajax({
        type: 'PUT',
        url: getBackendHost() + '/api/cn/gift_redeem?id=' + redeem.id,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: JSON.stringify(redeem)
    });
}

function getRedeem(redeemId) {
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/gift_redeem?id=' + redeemId,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function updateRedeem(redeem) {
    return callAjaxFunc('PUT', redeem,
    '/api/cn/gift_redeem')
}

function createRedeem(redeem) {
    return callAjaxFunc('POST', redeem,
    '/api/cn/gift_redeem')
}


function uploadImageCall(file) {
    if (file == null)
        return Promise.resolve({
            code: 1
        })
    var formData = new FormData();
    formData.append('file', file)
    formData.append('type', file.type)
    formData.append('folder', "")
    return $.ajax({
        type: "POST",
        url:"/assets/php/media-meta.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}

function saveMetaFilePath(path) {
    if (path == null)
        return Promise.resolve({
            code: 1
        })
    var formData = new FormData();
    formData.append('tempfile', path)
    return $.ajax({
        type: "POST",
        url:"/assets/php/media-save.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}


function deleteRedeem(redeemId) {
    return callAjaxFunc('DELETE', {
        id: redeemId
    },
    '/api/cn/gift_redeem')
}

function sentItem(redeemId) {
    var redeem = {
        status: "approve"
    }
    return $.ajax({
        type: 'PUT',
        url: getBackendHost() + '/api/cn/gift_redeem?id=' + redeemId,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: JSON.stringify(redeem)
    });
}


function getAllMainCategories() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=sport&search[parent_id]=0&search[disabled]=0'
    )
}

function getAllGift() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/gift'
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
    //filter by userId
    datatable.setDataSourceParam('filter[0][field]', "user.username")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $('#filter-ID').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")

    //filter by name
    datatable.setDataSourceParam('filter[1][field]', "gift_redeem.name")
    datatable.setDataSourceParam('filter[1][value]', "%"+ $('#filter-name').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[1][operator]', "LIKE")
    //filter by date
    var dateOption = $("#form-date-picker").val()
    if (dateOption != "申请时间"){
        datatable.setDataSourceParam('filter[2][field]', "gift_redeem.created_at")
        datatable.setDataSourceParam('filter[2][value]',  "%"+$("#form-date-picker").val()+"%")
        datatable.setDataSourceParam('filter[2][operator]', "LIKE")
    }
    //filter by phone
    datatable.setDataSourceParam('filter[3][field]', "gift_redeem.phone")
    datatable.setDataSourceParam('filter[3][value]', "%"+ $('#filter-phone').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[3][operator]', "LIKE")
    //filter by tracking_no
    datatable.setDataSourceParam('filter[4][field]', "gift_redeem.tracking_no")
    datatable.setDataSourceParam('filter[4][value]', "%"+ $('#filter-tracking_no').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[4][operator]', "LIKE")
    //filter by status
    datatable.setDataSourceParam('filter[5][field]', "gift_redeem.status")
    datatable.setDataSourceParam('filter[5][value]', "%"+ $('#filter-status').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[5][operator]', "LIKE")
    datatable.load();

}