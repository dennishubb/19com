var $newCategory = null
var $newSubCategory = null
var listedSubCategoryIds = []
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

    batchDeleteGift(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function batchDeleteGift(gifts) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    gifts.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/gift'
    )
 }

function open_hot_category_modal($subcat_selector) {
    //console.log($subcat_selector.val());

    if (!$subcat_selector.val()) {
        alert("请选择子类级别来继续。");
        return false;
    }

    $('#hotCategoryModal').modal('show');

    populate_hot_category_checkbox($subcat_selector.val());
}

function save_hot_category() {
    var $checkboxes = $('.hot-cat-checkbox:checked');

    //console.log("length :" + $checkboxes.length);

    var checkedDisplay = [];
    var checked = [], unchecked = [];

    $checkboxes.each(function (index) {
        if ($(this).attr('checked')) {
            checked.push($(this).parent().text());
            checkedDisplay.push($(this).parent().text());
        }
    });

    //console.log("-");
    //console.log(checked);
    //console.log(checkedDisplay);

    $('input[name=hot_category]').val(checkedDisplay.toString());
    $('#hot_category_display').val(checkedDisplay.toString());

    $('#hotCategoryModal').modal('hide');
}

function add_hot_category($subcat_selector) {
    var newHotCategory = prompt("请输入类别")
    if (newHotCategory) {
        window.alert("成功新增类别");
    }
    if (newHotCategory != null) {
        addNewHotCategory(newHotCategory, $subcat_selector.val())
            .then(response => {
                if (response.code == 1) {
                    populate_hot_category_checkbox($subcat_selector.val());
                } else {
                    window.alert("新增类别失败");
                }
            });
    }
    return Promise.resolve()
}

function addNewHotCategory($display, $subcat_id) {
    var data = {
        name: $display,
        display: $display,
        parent_id: $subcat_id,
        type: "gift-hot"
    }
    return callAjaxFunc('POST', data, '/api/cn/category')
}

function populate_hot_category_checkbox($sub_cat_id) {
    callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=gift-hot&search[parent_id]=' + $sub_cat_id
    ).then(response => {
        $('#modal-checkbox-area').html('');

        $html = '';
        response.data.forEach(category => {
            $html += '<label class="checkbox" style="padding: 10px;">';
            $html += '<input class="hot-cat-checkbox" onclick="checkbox_click(this);" type="checkbox" name="hot_category[]" value="' + category.display + '">';
            $html += '<span style="padding-left: 8px;">' + category.display + '</span>';
            $html += '</label>';
        });

        if (getQueryString("id")) {

            getGift(getQueryString("id")).then(response => {

                //console.log(response);

                if (response.code == 1) {
                    var $checkboxes = $('.hot-cat-checkbox');

                    var checked = [], display = [];

                    $checkboxes.each(function (index) {
                        if (response.data.hot_category.includes($(this).val())) {
                            $(this).attr('checked', true);
                            checked.push($(this).val());
                            display.push($(this).parent().text());
                        }
                    });

                    $('#hot_category_display').val(display.toString());
                }
            });

        }

        $('#modal-checkbox-area').html($html);
    });
}

function checkbox_click($selector) {
    //console.log("checkbox_click");
    $($selector).attr('checked', !$($selector).attr('checked'));
}

function selectedSizes(size, sizeType) {
    var inputId = ""
    var standardValues = []
    if (!sizeType) return ""
    if (!size) return ""
    if (sizeType == 'inch') {
        standardValues = standardInchAry
        inputId = "extra-inch-input"
    } else if (sizeType == 'size') {
        standardValues = standardSizeAry
        inputId = "extra-size-input"
    }
    for (index in size) {
        $(`input[name=size][value="${size[index]}"]`).attr('checked', '1')
    }
    var customValue = getCustomValue(standardValues, size)
    if (customValue) {
        $(`input[name=customized][value=""]`).attr('checked', '1')
        $("#" + inputId).removeAttr("disabled")
        $("#" + inputId).val(customValue);
    }
    sizingSelector.selectpicker('refresh');
}

function getCustomValue(standardValues, backendValues) {
    var newArray = _.clone(backendValues)
    _.pullAll(newArray, standardValues)
    if (newArray.length > 0)
        return newArray[0]
}

//category dropdown list
function categoryDropdown() {
    return getAllMainCategories().then(response => {
        categoryPicker.empty();
        categoryPicker.append($(`<option value="">---请选择一个---</option>`))
        response.data.forEach(category => {
            categoryPicker.append($(`<option value="${category.id}">${category.display}</option>`))
            if ($newCategory == category.display) {
                categoryPicker.find(`option[value="${category.id}"]`).attr('selected', '1')
                subCategoryPicker.empty();
                subCategoryPicker.append($(`<option value="">请选择类别</option>`))
                $('#addSubCategoryBtn').show()
            }
        })
        categoryPicker.selectpicker('refresh');
        subCategoryPicker.selectpicker('refresh');
    })
}

function subCategoryDropdown() {
    //sub category dropdown list
    var selectedCategoryId = categoryPicker.val()
    listedSubCategoryIds = []
    subCategoryPicker.empty()
    subCategoryPicker.append($(`<option value="">请选择类别</option>`))
    return getSubCategory(selectedCategoryId).then(response => {
        response.data.forEach(subCategory => {
            subCategoryPicker.append($(`<option value="${subCategory.id}">${subCategory.display}</option>`))
            listedSubCategoryIds.push(subCategory.id)
            if ($newSubCategory == subCategory.display) {
                subCategoryPicker.find(`option[value="${subCategory.id}"]`).attr('selected', '1')
            }
        })
        subCategoryPicker.selectpicker('refresh');
    })
}

function editSubCategoryDropdown(parent_id) {
    listedSubCategoryIds = []
    return getSubCategory(parent_id).then(response => {
        response.data.forEach(subCategory => {
            subCategoryPicker.append($(`<option value="${subCategory.id}">${subCategory.display}</option>`))
            listedSubCategoryIds.push(subCategory.id)
        })
        subCategoryPicker.selectpicker('refresh');
    })
}

function addNewCategory() {
    var newCategory = prompt("请输入类别")
    if (newCategory) {
        window.alert("成功新增类别");
    }
    if (newCategory != null) {
        $newCategory = newCategory
        return addedNewCategory(newCategory).then(categoryDropdown)
    }
    return Promise.resolve()
}

function addedNewCategory(giftCategory) {
    var data = {
        name: giftCategory,
        display: giftCategory,
        type: "gift"
    }
    return callAjaxFunc('POST', data,
        '/api/cn/category')
}

function addNewSubCategory() {
    var newSubCategory = prompt("请输入子级类别");
    if (newSubCategory) {
        window.alert("成功新增子级类别");
    }
    if (newSubCategory != null) {
        $newSubCategory = newSubCategory
        return addedNewSubCategory(newSubCategory).then(subCategoryDropdown)
    }
    return Promise.resolve()
}

function addedNewSubCategory(giftSubCategory) {
    var selectedCategoryId = categoryPicker.val()
    var data = {
        name: giftSubCategory,
        display: giftSubCategory,
        type: "gift",
        parent_id: selectedCategoryId
    }
    return callAjaxFunc('POST', data,
        '/api/cn/category')
}

function deleteGiftCategory() {
    var deleteCategory = $("#category_id").val()
    var confirmDelete = ""
    if (deleteCategory) {
        confirmDelete = confirm("确认删除类别？")
    } else {
        alert("请选择需要删除的类别");
    }
    if (deleteCategory != null && confirmDelete) {
        return deletedGiftCategory(deleteCategory)
            .then(deletedPatchSubGiftCategory(listedSubCategoryIds))
            .then(categoryDropdown)
            .then(subCategoryDropdown)
    }
}

function deletedGiftCategory(giftCategoryId) {
    return callAjaxFunc('DELETE', {
            id: giftCategoryId
        },
        '/api/cn/category')
}

function deleteSubGiftCategory() {
    var deleteSubCategory = $("#sub_category_id").val()
    var confirmDelete = ""
    if (deleteSubCategory) {
        confirmDelete = confirm("确认删除子级类别？")
    } else {
        alert("请选择需要删除的子级类别");
    }
    if (deleteSubCategory != null && confirmDelete) {
        return deletedGiftCategory(deleteSubCategory).then(subCategoryDropdown)
    }
}

function deletedPatchSubGiftCategory(subCategoryIds) {
    var item = {}
    var delete_data = {data: []};
    $.each(subCategoryIds, function (i, entry) {
        item['id'] = entry;
        delete_data.data.push(item);
        item = {};
    });
    delete_data['action'] = "delete";
    var remove_item_func =
        $.ajax({
            type: "PATCH",
            url: getBackendHost() + 'api/cn/category',
            data: JSON.stringify(delete_data),
            crossDomain: true,
            headers: getHeaders(),
            contentType: false,
            processData: false,
            error: function () {
                alert('AJAX ERROR - delete sub category');
            },
        });
}

function addFiltering(paramName, value) {
    var datatable = $("#main-datatable").KTDatatable();
    datatable.setDataSourceParam('search[' + paramName + ']', value)
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    delete datatable.API.params['search[' + paramName + ']']
}

function postGift(giftId) {
    $redirect_uri = "gift-list.html"
    $error_selector = $(".message_output")
    getGift(giftId)
        .then(response => {
            var gift = response.data
            return editGift(gift);
        })
        .then(response => {
            obj = response;
            if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=Gift%20posted");
            } else if (obj.code == -1) {
                redirect_to_login();
            } else {
                window.scrollTo(0, 0);
                showAlert(obj.message, "danger", $error_selector);
            }
        })
        .catch(function (error) {
            window.scrollTo(0, 0);
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        });
}

function editGift(gift) {
    return $.ajax({
        type: 'PUT',
        url: getBackendHost() + '/api/cn/gift?id=' + gift.id,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: JSON.stringify(gift)
    });
}

function getGift(giftId) {
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/gift?id=' + giftId,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function updateGift(gift) {
    return callAjaxFunc('PUT', gift,
        '/api/cn/gift')
}

function createGift(gift) {
    return callAjaxFunc('POST', gift,
        '/api/cn/gift')
}

function uploadImageCall(file) {
    if (file == null)
        return Promise.resolve({
            code: 1,
            isFileNull: true
        })
    var formData = new FormData();
    formData.append('file', file)
    formData.append('type', file.type)
    formData.append('folder', 'gift')
    return $.ajax({
        type: "POST",
        url: "/assets/php/media-meta.php",
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
        url: "/assets/php/media-save.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    })
}


function deleteGift(giftId) {
    return callAjaxFunc('DELETE', {
            id: giftId
        },
        '/api/cn/gift')
}


function getAllMainCategories() {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=gift&search[parent_id]=0'
    )
}

function getSubCategory(categoryId) {
    return callAjaxFunc(
        'GET',
        {},
        '/api/cn/category?search[type]=gift&search[parent_id]=' + categoryId
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
    //filter by gift name
    datatable.setDataSourceParam('filter[0][field]', "gift.name")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $('#filter-gift-name').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")

    //filter by category
    datatable.setDataSourceParam('filter[1][field]', "gift.category_id")
    datatable.setDataSourceParam('filter[1][value]', $('#form-category-picker').val().toLowerCase())
    datatable.setDataSourceParam('filter[1][operator]', "=")

    //filter by subCategory
    datatable.setDataSourceParam('filter[2][field]', "gift.sub_category_id")
    datatable.setDataSourceParam('filter[2][value]', $('#subCategoryPicker').val().toLowerCase())
    datatable.setDataSourceParam('filter[2][operator]', "=")

    datatable.load();
}