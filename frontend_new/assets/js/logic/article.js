var $error_selector = $(".message_output")
function submitBatchUpdateModal(newCategory, newSubCategory){
    var selectedIds = getSelectedIds();
    var parsedData = selectedIds.map(function(id) {
        return {
            id,
            category_id: newCategory,
            sub_category_id: newSubCategory
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
        '/api/cn/article'
    )
}
function getImgUrl (contentData) {
    return data = Array.from( new DOMParser().parseFromString(contentData, 'text/html')
    .querySelectorAll( 'img' ) )
    .map( img => img.getAttribute( 'src' ))
}

function removeCharacter (contentData) {
    var newData = []
    contentData.forEach(function(value, key){
        newData.push(value.replace(/\\"/g, ''))
    });
    return newData
}

function deleteUnusedImage(image) {
    if (!image) 
        return Promise.resolve()

    var formData = new FormData();
    formData.append('filename', image)
    return $.ajax({
        type: "POST",
        url:"/assets/php/media-delete.php",
        data: formData,
        crossDomain: true,
        contentType: false,
        processData: false
    });
}

var oldEditor = []
var usedImages = []
var allImgAry = []

function getRemovedImages(allImages, usedImages) {
    var deletedImages = _.uniq(allImages);
    _.pullAll(deletedImages, usedImages);

    return deletedImages;
}

function submitAdd($draft = null){
    const editorData = editor.getData();
    usedImages = getImgUrl(editorData)
    var article = {
        article: ''
    }
    form = document.getElementById('form');
    formData = new FormData(form);
    formData.append("content", editorData);

    var data = {};
    formData.forEach(function(value, key){
        data[key] = value;
    });
    data.tags = data.tags ? JSON.parse(data['tags']).map(tag => tag.value) : [];
    $draft ? data.draft="1" : data.draft="0";
    var deletedImages = getRemovedImages(allImgAry, usedImages)
    article = {
        ...article,
        ...data,
        disabled: data.disabled ? 0 : 1,
        deletedImages
    }
    createArticle(article)
    /*.then(response => {
        return Promise.all(deletedImages.map(deleteUnusedImage))
            .catch(error => //console.log(error))
            .then(_ => response)
    })*/
    .then(function (createArticleResponse) {
        $redirect_uri = $(form).attr('data-redirect');
        if (createArticleResponse.code == 1){
            if (createArticleResponse.redirect) redirect_to($redirect_uri + "?alert-success=" + createArticleResponse.status);
		}else if(createArticleResponse.code == -1){
			redirect_to_login();
		}else if(createArticleResponse.code == 0){
			window.scrollTo(0,0);
			showAlert(createArticleResponse.message, "danger", $error_selector);
		}
		
		/*else if (obj.code == -1) {
                redirect_to_login();
            } else {
                window.scrollTo(0,0);
                showAlert(obj.message, "danger", $error_selector);
            }*/
        return createArticleResponse
    })
    .catch(err => {
        $error_selector = $(".message_output")
        showAlert(createArticleResponse.message, "danger", $error_selector);
    })
}

function submitEdit() {
    const editorData = editor.getData();
    usedImages = getImgUrl(editorData)
    
    var article = {
        id: parseInt(getQueryString('id')),
        article: ''
    }
    form = document.getElementById('form');
    formData = new FormData(form);
    formData.append("content", editorData);
    var data = {};
    formData.forEach(function(value, key){
        data[key] = value;
    });
    data.tags = data.tags ? JSON.parse(data['tags']).map(tag => tag.value) : [];
    var deletedImages = getRemovedImages(allImgAry, usedImages)
    article = {
        ...article,
        ...data,
        disabled: data.disabled ? 0 : 1,
        deletedImages
    }
    updateArticle(article)
    /*.then(response => {
        return Promise.all(deletedImages.map(deleteUnusedImage))
            .catch(error => //console.log(error))
            .then(_ => response)
    })*/
    .then(function (updateArticleResponse) {
        $redirect_uri = $(form).attr('data-redirect');
        if (updateArticleResponse.code == 1){
            if (updateArticleResponse.redirect) 
                redirect_to($redirect_uri + "?alert-success=" + updateArticleResponse.status);
        }else if(updateArticleResponse.code == -1){
			redirect_to_login();
		}else if(updateArticleResponse.code == 0){
			window.scrollTo(0,0);
			showAlert(updateArticleResponse.message, "danger", $error_selector);
		}
        return updateArticleResponse
    })
    .catch(err => {
        $error_selector = $(".message_output")
        showAlert("Failed to upload image!", "danger", $error_selector);
    })
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
            title: data[`title[${id}]`],
            content: data[`content[${id}]`],
            active_at: data[`active_at[${id}]`],
            category: data[`category[${id}]`],
            tags: data[`tags[${id}]`] ? JSON.parse(data[`tags[${id}]`]).map(tag => tag.value) : [],
            seo_title: data[`seo_title[${id}]`], 
            description: data[`description[${id}]`],
            keywords: data[`keywords[${id}]`],
            disabled: data[`disabled[${id}]`] == "on" ? 0 : 1
        }
    })

    batchUpdateArticle(parsedData).then(function() {
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
    if (paramName == "comment_count") {
        datatable.setDataSourceParam('sort[field]', "comment_count")
        datatable.setDataSourceParam('sort[sort]', "desc")
    }else {
        datatable.setDataSourceParam('search['+paramName+']', value)
    }
}

function removeFilter(paramName) {
    var datatable = $("#main-datatable").KTDatatable();
    
    if (paramName == "comment_count") {
        delete datatable.API.params['sort[field]']
        delete datatable.API.params['sort[sort]']
    }else {
        delete datatable.API.params['search['+paramName+']']
    }
}

function handleBatchDelete(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchDeleteArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function handleBatchHotNews(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchHotNewsArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function handleBatchCancelHotNews(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchCancelHotNewsArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}
function handleBatchPopularNews(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchPopularNewsArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function handleBatchCancelPopularNews(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchCancelPopularNewsArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}
function handleBatchRecycled(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchRecycledArticle(selectedIds)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
}

function handleBatchCancelRecycled(selectedIds) {
    var datatable = $("#main-datatable").KTDatatable();

    batchCancelRecycledArticle(selectedIds)
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
    }else if (action=='bulk_hotNews'){
        msg='确定批量设定热门新闻？';
    }else if (action=='bulk_recycled'){
        msg='确定批量转至回收站？';
    }else if (action=='bulk_cancel_recycled'){
        msg='确定批量取消转至回收站？';
    }else if (action=='bulk_cancel_hotNews'){
        msg='确定批量取消热门新闻？';
    }else if (action=='bulk_popularNews'){
        msg='确定批量设定推荐新闻？';
    }else if (action=='bulk_cancel_popularNews'){
        msg='确定批量取消推荐新闻？';
    }
    
    return confirm(msg);
}

function postArticle(articleId) {
    $redirect_uri = "article-list.html"
    $error_selector = $(".message_output")
    getArticle(articleId)
        .then(response => {
            var article = response.data
            article.draft = 0;
            return editArticle(article);
        })
        .then(response => {
            ////console.log(response);
            obj = response;
            if (obj.code == 1) {
                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=Article%20posted");
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

function editArticle(article){
    return $.ajax({
        type: 'PUT',
        url: getBackendHost() + '/api/cn/article?id=' + article.id,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false,
        // contentType: "charset=utf-8",
        data: JSON.stringify(article)
    });
}

function getArticle(articleId) {
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/article?id=' + articleId,
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: false
    });
}

function updateArticle(article) {
    return callAjaxFunc('PUT', article,
    '/api/cn/article')
}

function createArticle(article) {
    return callAjaxFunc('POST', article,
    '/api/cn/article')
}
function batchUpdateArticle(article) {
    return callAjaxFunc('PATCH', {
            data: article
        },
        '/api/cn/article'
    )
}

function draft_ajax_submit($form) {
    var formData = new FormData(document.getElementById($form.attr('id')));
    formData.append('draft', 1);
    ajax_submit($form, $(".message_output"), formData);
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

/*function batchDeleteArticle(articleIds) {
    let dataObj = {
        "data": []
    };
    dataObj["action"] = "delete"
    articleIds.map(function(eachId) {
        dataObj.data.push({ "id": eachId })
    })
    return callAjaxFunc(
        'PATCH',
            dataObj
        ,
        '/api/cn/article'
    )
 }*/

 function batchDeleteArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    deleted: 1
                }
            })
        },
        '/api/cn/article'
    )
 }

 function softDeleteItem(articleId) {
    return callAjaxFunc(
        'PUT',
        {
            id : articleId,
            deleted: 1
        },
        '/api/cn/article'
    )
 }

 function softDeleteArticle(articleId) {
    var status = confirm('确定要删除这项纪录？');
    if(!status) return ""
    var datatable = $("#main-datatable").KTDatatable();

    softDeleteItem(articleId)
    .then(function (response) {
        datatable.load();
        if (response.code == 1) showAlert(response.status, "success", $error_selector);
        else showAlert(response.status, "danger", $error_selector);
    }).catch(function (e) {
        showAlert("Problem occurred while sending request.", "danger", $error_selector);
    });
 }

function batchHotNewsArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    hot: 1
                }
            })
        },
        '/api/cn/article'
    )
}

function batchCancelHotNewsArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    hot: 0
                }
            })
        },
        '/api/cn/article'
    )
}
function batchPopularNewsArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    popular: 1
                }
            })
        },
        '/api/cn/article'
    )
}

function batchCancelPopularNewsArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    popular: 0
                }
            })
        },
        '/api/cn/article'
    )
}

function batchRecycledArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    deleted: 1
                }
            })
        },
        '/api/cn/article'
    )
}

function batchCancelRecycledArticle(articleIds) {
    return callAjaxFunc(
        'PATCH',
        {
            data: articleIds.map(articleId => {
                return {
                    id: articleId,
                    deleted: 0
                }
            })
        },
        '/api/cn/article'
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

function getAllSubCategory() {
    var filter_array = [
        {
            field: 'parent_id',
            value: 0,
            operator: '>',
        },
        {
            field: 'category.type',
            value: 'sport',
            operator: '=',
        },
        {
            field: 'disabled',
            value: '0',
            operator: '=',
        },
    ];
    return $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/category',
        crossDomain: true,
        headers: getHeaders(),
        contentType: false,
        processData: true,
        data: { filter: filter_array },
    });
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
    //filter by title
    datatable.setDataSourceParam('filter[0][field]', "title")
    datatable.setDataSourceParam('filter[0][value]', "%"+ $('#searchTitle').val().toLowerCase()+"%")
    datatable.setDataSourceParam('filter[0][operator]', "LIKE")
    //filter by active_at
    var dateOption = $("#form-date-picker").val()
    if (dateOption != "全部日期"){
        datatable.setDataSourceParam('filter[1][field]', "active_at")
        datatable.setDataSourceParam('filter[1][value]',  "%"+$("#form-date-picker").val()+"%")
        datatable.setDataSourceParam('filter[1][operator]', "LIKE")
    }
    //filter by types
    var optionSelected = $("#form-filter-type").val();
    if(optionSelected == 'hot') {
        addFiltering('hot', 1)
        removeFilter('popular')
        removeFilter('comment_count')
    } else if(optionSelected == 'popular') {
        addFiltering('popular', 1)
        removeFilter('hot')
        removeFilter('comment_count')
    } else if(optionSelected == 'comment_count') {
        datatable.setDataSourceParam('sort[field]', "comment_count")
        datatable.setDataSourceParam('sort[sort]', "desc")
        removeFilter('hot')
        removeFilter('popular')
    }else{
        removeFilter('hot')
        removeFilter('popular')
        removeFilter('comment_count')
    }
    //filter by category_id
    datatable.setDataSourceParam('filter[3][field]', "article.category_id")
    datatable.setDataSourceParam('filter[3][value]',  $('#form-category-picker').val())
    datatable.setDataSourceParam('filter[3][operator]', "=")
    //filter by sub_category_id
    datatable.setDataSourceParam('filter[4][field]', "article.sub_category_id")
    datatable.setDataSourceParam('filter[4][value]', $('#subCategoryPicker').val())
    datatable.setDataSourceParam('filter[4][operator]', "=")

    datatable.load();
}