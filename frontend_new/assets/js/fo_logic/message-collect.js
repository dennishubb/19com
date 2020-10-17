window.likedMessageAry = []
window.message_id= []
window.message_collect_page_no = ""
window.selectedClassItem = ""
//===message collection starts===
function showBulkActionPrompt(action){
  var msg='';

  if (action=='bulk_cancel'){
    msg='确定批量取消收藏？';
  }else if(action==''){
    msg='请选择操作！';    
  }
  return confirm(msg);
}

function handleBulkCancel(selectedIds){
  var removeSelectedId = []
  if (selectedIds.length > 0){
    selectedIds.forEach(function(key,value){
      if(key != "all") removeSelectedId.push(key)
    });
    removeItem(removeSelectedId);
  }
}

function idSelector() {
   return this.id; 
};

function getAllSelectedIds(){
  return $(":checkbox:checked").map(idSelector).get();
}

function removeItem(itemId) {
  if (!itemId) return ""
  delete_item_func(itemId)
}

function delete_item_func(message_like_id){
  var item = {}
  var delete_data = {data: []};
  $.each(message_like_id, function (i, entry) {
    item['id'] = entry;
    delete_data.data.push(item);
    item = {};
  });
  delete_data['action'] = "delete";
  var remove_item_func =
    $.ajax({
      type: "PATCH",
      url:link + 'api/cn/message_like',
      data: JSON.stringify(delete_data),
      crossDomain: true,
      headers: getHeaders(),
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.code == 1) {
            alert("取消收藏成功!")
        }else {
            alert("取消收藏失败！")
        }
      },
      error: function () {
          alert('AJAX ERROR - user favorite');
      },
  });
  $.when(remove_item_func).done(function () {
    getUserLikedMessage()
  });
}

var filterBy = [
  {
    field: 'message_like.user_id',
    value: window.localStorage.user_id,
    operator: "="
  }
]
var sortByItem = [
  {
    field: "message_like.created_at",
    sort: "desc"
  }
]

function getUserLikedMessage() {
  var fav_messages =
    $.ajax({
        url:link + '/api/cn/message_like',
        data: {
          filter: filterBy,
          sort: sortByItem,
          limit:10,
          page_number: window.message_collect_page_no? window.message_collect_page_no : 1
        },
        type:'GET',
        headers: getHeaders(),
        contentType: false,
        success: function (data){
          var obj = {}
          obj["favMessages"] = data['data']
          obj["totalPage"] = data["totalPage"]
          obj["current_page"] = window.message_collect_page_no? window.message_collect_page_no : 1
          var html=$.get('/cn/message-collect/message-collect-body.html',function (data) {
              var render = template.compile(data);
              var str = render(obj);
              $('.main_area').html(str);
              if (window.filter_date!="0") $('#datepicker').val(window.filter_date)
              if (window.selectedClassItem == "fa-sort-up"){
                $('#toggle-icon').removeClass("fa-sort-down")
                $('#toggle-icon').addClass("fa-sort-up")
              }else if (window.selectedClassItem == "fa-sort-down"){
                $('#toggle-icon').removeClass("fa-sort-up")
                $('#toggle-icon').addClass("fa-sort-down")
              }
          });
        },
        error: function () {
          alert('AJAX ERROR - get all messages_like');
        },
    });
    $.when(fav_messages).done(function () {

    });
}

function filterByDate (date){
  var startDateTime = ""
  var endDateTime = ""
  $('#datepicker').datepicker().on('changeDate', function(e) {
		date = e.format(0,"yyyy-mm-dd");
  });
  if(date){
    startDateTime = date +" 00:00:00"
    endDateTime = date +" 23:59:59"
  }
  window.filter_date = date
  filterBy = [
    {
      field: 'message_like.user_id',
      value: window.localStorage.user_id,
      operator: "="
    },
    {
      field: 'message_like.created_at',
      value: startDateTime,
      operator: ">="
    },
    {
      field: 'message_like.created_at',
      value: endDateTime,
      operator: "<="
    }
  ]
  $.when(filterByDate).done(function () {
    getUserLikedMessage()
  });
}

function sortByItemFunc (value){
  if(value == 'fa-sort-up') {
    sortByItem = [
      {
        field: "message_like.created_at",
        sort: "asc"
      }
    ]
  }else if (value == 'fa-sort-down'){
    sortByItem = [
      {
        field: "message_like.created_at",
        sort: "desc"
      }
    ]
  }
  window.selectedClassItem = value
  $.when(sortByItemFunc).done(function () {
    getUserLikedMessage()
  });
}

function sortByDate(){
  $('#toggle-icon').toggleClass("fa-sort-down fa-sort-up")
  var itemClass = $('#toggle-icon').prop("classList")[1]
  sortByItemFunc(itemClass)
}
