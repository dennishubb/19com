function submitEdit(){
    var topTenSetting = {
      topTenSetting: ''
    }
    var method = ""
    form = document.getElementById('form');
    formData = new FormData(form);

    var data = {};
    formData.forEach(function(value, key){
        data[key] = value;
    });
    if (data.top_ten_rate_id) {
      data.id = data.top_ten_rate_id
      method = "PUT"
    }
    else {
      method = "POST"
    }
    topTenSetting = {
        ...topTenSetting,
        ...data
    }
  
  updateSetting(topTenSetting,method)
  .then(function (updateSettingResponse) {
      $redirect_uri = $(form).attr('data-redirect');
      if (updateSettingResponse.code == 1)
          if (updateSettingResponse.redirect) redirect_to($redirect_uri + "?alert-success=" + updateSettingResponse.status);
      else
          showAlert(updateSettingResponse.message, "danger", $(".message_output"));
      return updateSettingResponse
  })
  .catch(err => {
      $error_selector = $(".message_output")
      showAlert(updateSettingResponse.message, "danger", $error_selector);
  })
  
}

function updateSetting(topTenSetting, method) {
  return callAjaxFunc(method, topTenSetting,
  '/api/cn/top_ten_rate')
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

function getAllMainCategories() {
  return callAjaxFunc(
      'GET',
      {},
      '/api/cn/category?search[type]=sport&search[parent_id]=0'
  )
}

function filterByItem() {
  var filterByLeague = $("#filter-league-id").val();
  var filterByCategory = $("#form-category-picker").val();
  var datatable = $("#main-datatable").KTDatatable();
  //filter by category_id
  datatable.setDataSourceParam('filter[0][field]', "category.id")
  datatable.setDataSourceParam('filter[0][value]', "%"+ filterByCategory.toLowerCase()+"%")
  datatable.setDataSourceParam('filter[0][operator]', "LIKE")

  datatable.setDataSourceParam('filter[1][field]', "name_zh")
  datatable.setDataSourceParam('filter[1][value]', "%"+ filterByLeague.toLowerCase()+"%")
  datatable.setDataSourceParam('filter[1][operator]', "LIKE")

  datatable.load();

}