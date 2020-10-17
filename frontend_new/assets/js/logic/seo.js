var link = getBackendHost();

//to be edit, now only show category
function getSEOList(call_type) {
  var seo_main_menu = "";

  //seo_main_menu+="<option value="+ value.id+ " "+selected+" >"+ value.display+"</option>";
  seo_main_menu += "<option value='sport' selected >分类SEO</option>";
  seo_main_menu += "<option value='seo'>首页SEO</option>";
  seo_main_menu += "<option value='zonghe'>综合SEO</option>";
  seo_main_menu += "<option value='video'>视频SEO</option>";

  $("#seo_main_menu").html(seo_main_menu);

  if (call_type == "first") {
    getParentList("", 0, 0, call_type); //generate parent and child menu
  } else if (call_type == "seo") {
    $("#seo_main_menu").val("seo");
    changeSEOContent();
  }
}

//get category parent list
function getParentList(selected_id, parent_id, child_id = 0, call_type) {
  var selected = "";
  //var parent_id_to_pass='';
  var seo_parent_menu = "";
  var filter_array = [
    {
      field: "parent_id",
      value: 0,
      operator: "=",
    },
  ];
  filter_array.push({
    field: "category.type",
    value: "sport",
    operator: "=",
  });

  $.ajax({
    type: "GET",
    url: link + "/api/cn/category",
    data: {
      filter: filter_array,
      sort: {
        field: "category.id",
        sort: "asc",
      },
      label: "getParentList",
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      //////console.log(response);
      obj = response;
      ////console.log(obj.data);

      $.each(obj.data, function (index, value) {
        //////console.log(index+' -> '+value)

        if (value.id == selected_id) selected = "selected";
        else selected = "";

        seo_parent_menu +=
          "<option value=" +
          value.id +
          " " +
          selected +
          " >" +
          value.display +
          "</option>";
      });

      if (parent_id == 0)
        //if 0, pass 1 to get child menu
        parent_id = 1;

      $("#seo_parent_menu").html(seo_parent_menu);
      getChildList(parent_id, child_id, call_type);
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function getChildList(parent_id, child_id = 0, call_type) {
  var selected = "";

  if (call_type == "first") {
    parent_id = $("#seo_parent_menu").val();
  }

  var filter_array = [
    {
      field: "parent_id",
      value: parent_id,
      operator: "=",
    },
  ];
  var seo_child_menu = "";

  $.ajax({
    type: "GET",
    url: link + "/api/cn/category",
    data: {
      filter: filter_array,
      sort: {
        field: "category.id",
        sort: "asc",
      },
      label: "getChildList",
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////console.log(response);
      obj = response;
      //////console.log(obj.data);

      seo_child_menu += "<option value='' " + selected + " >主栏目</option>";
      $.each(obj.data, function (index, value) {
        ////////console.log(index+' -> '+value)

        // if (value.id==child_id)
        //  selected='selected';
        // else
        selected = "";

        seo_child_menu +=
          "<option value=" +
          value.id +
          " " +
          selected +
          " >" +
          value.display +
          "</option>";
      });

      $("#seo_child_menu").html(seo_child_menu);
      //changeSEOContent(parent_id);
      //alert(child_id)
      ////console.log(child_id,call_type)
      if (child_id > 0 || call_type == "first")
        //if user did not come from redirect url
        checkExist(); //check if row exist in db
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function selectCurrent(site_id) {
  var filter_array = [
    {
      field: "site.id",
      value: site_id,
      operator: "=",
    },
  ];

  $.ajax({
    type: "GET",
    url: link + "/api/cn/site",
    data: {
      filter: filter_array,
      label: "selectCurrent",
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////////console.log(response);
      obj = response;
      //////console.log(obj.data);

      $("input[name=site_id]").val(obj.data[0]["id"]);
      populateForm($("form"), obj.data[0]);
      getSEOList(); //generate main menu
      getParentList(
        obj.data[0].category_id,
        obj.data[0].category_id,
        obj.data[0].sub_category_id
      );
      //changeSEOContent(parent_id);
      // checkExist();//check if row exist in db
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function checkExist() {
  //var return_val=false;
  var cat_parent_id = $("#seo_parent_menu :selected").val(); //category.parent_id
  var cat_sub_id = $("#seo_child_menu :selected").val(); //category id
  if (cat_sub_id <= 0 || cat_sub_id == null || cat_sub_id == "") cat_sub_id = 0;

  var filter_array = [
    {
      field: "site.category_id",
      value: cat_parent_id,
      operator: "=",
    },
  ];

  filter_array.push({
    field: "site.sub_category_id",
    value: cat_sub_id,
    operator: "=",
  });

  $.ajax({
    type: "GET",
    url: link + "/api/cn/site",
    //data:{search:{id:permission_id}},
    data: { filter: filter_array },

    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////console.log(response);

      obj = response;
    var selected_child=$("#seo_child_menu :selected").text();
      if (obj.data.length <= 0 && selected_child!='主栏目') createSiteRecord(cat_parent_id, cat_sub_id);
      else changeSEOContent(cat_parent_id);
      //$('#permission_name_header').html(permission_menu);
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function createSiteRecord(cat_parent_id, cat_sub_id) {
  var json_form_obj = new Object();
  var name;

  json_form_obj["category_id"] = cat_parent_id;
  json_form_obj["sub_category_id"] = cat_sub_id;
  json_form_obj["title"] = $("#seo_child_menu :selected").text();

  var formData = JSON.stringify(json_form_obj);
  //////console.log(formData);

  $.ajax({
    type: "POST",
    url: link + "/api/cn/site",
    //data:{search:{id:permission_id}},
    data: formData,

    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      //////console.log(response);

      obj = response;
      changeSEOContent(cat_parent_id);
      //$('#permission_name_header').html(permission_menu);
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

//if no record for mainpage seo, set site_id
function setMainSiteId(){
  var filter_array = [
    {
      field: "site.type",
      value: 'main',
      operator: "=",
    },
  ];
  
  $.ajax({
    type: "GET",
    url: link + "/api/cn/site",
    data: {
      filter: filter_array,
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////////console.log(response);
      obj = response;
    //console.log(obj.data);
    //console.log(obj.data[0]["id"]);

      $("input[name=site_id]").val(obj.data[0]["id"]);
   // alert('okkk')
      
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}

function changeSEOContent(parent_id) {
  clearAlert();
  
  var menu_content = "";

  var main_menu_val = $("#seo_main_menu :selected").val();
  var cat_parent_id = $("#seo_parent_menu :selected").val(); //category.parent_id
  var cat_sub_id = $("#seo_child_menu :selected").val(); //category id

  if (cat_sub_id <= 0 || cat_sub_id == null || cat_sub_id == "") cat_sub_id = 0;
  //alert(child_menu_text);

  //console.log(main_menu_val,cat_parent_id,cat_sub_id);
  if (main_menu_val == "sport") {
    var filter_array = [
      {
        field: "site.category_id",
        value: cat_parent_id, //parent_menu_val = foodball category id
        operator: "=",
      },
    ];

    filter_array.push({
      field: "site.sub_category_id",
      value: cat_sub_id,
      operator: "=",
    });
  } 
  else if (main_menu_val == "seo") {
    var filter_array = [
      {
        field: "site.type",
        value: 'main', //for seo
        operator: "=",
      },
    ];
  }
  else if (main_menu_val == "zonghe") {
    var filter_array = [
      {
        field: "site.type",
        value: 'zonghe', //for zonghe
        operator: "=",
      },
    ];
  }
   else if (main_menu_val == "video") {
    var filter_array = [
      {
        field: "site.type",
        value: 'video', //for video
        operator: "=",
      },
    ];
  }

  //article get caterogy_id
  //sub-category get parent_id
  $.ajax({
    type: "GET",
    url: link + "/api/cn/site",
    //data:{search:{id:permission_id}},
    data: { filter: filter_array },

    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////console.log(response);
    //console.log(obj.data.length);

      obj = response;

      if (obj.data.length == 0) {
        //set header content
        $("#seo_name_header").html("无记录");

        $("#menu_name").html("标题");
        $("#menu_descp").html("描述");
        $("#menu_keyword").html("Keywords");

        $("#form").find('[name="site_id"]').val("");
        $("#form").find('[name="title"]').val("");
        $("#form").find('[name="description"]').val("");
        $("#form").find('[name="keywords"]').val("");
    
    if ($("#seo_main_menu").val=='seo')
      setMainSiteId();
      } else {
        //set header content
    var header_title=obj.data[0].title;
    if (obj.data[0].title=='')
      header_title='标题尚未设置';
        $("#seo_name_header").html(header_title);

        //set label
        $("#menu_name").html(obj.data[0].title + " 标题");
        $("#menu_descp").html(obj.data[0].title + " 描述");
        $("#menu_keyword").html(obj.data[0].title + " Keywords");

        //set value
    $("#form").find('[name="site_id"]').val(obj.data[0].id);
        $("#form").find('[name="title"]').val(obj.data[0].title);
        $("#form").find('[name="description"]').val(obj.data[0].description);
        $("#form").find('[name="keywords"]').val(obj.data[0].keywords);

        if (main_menu_val == "seo") {
          //if SEO add form to upload sitemap and robot
          $("#sitemap_div").css("display", "block");
          $("#robots_div").css("display", "block");
          //alert('ff');
        } else {
          $("#sitemap_div").css("display", "none");
          $("#robots_div").css("display", "none");
        }

        $("#menu_content").html(menu_content);
      }

      //$('#permission_name_header').html(permission_menu);
    },
    error: function () {
      showAlert("Problem occurred while sending request.", "danger");
    },
  });
}


//not in use
//function setSEOV2($form) {
//  var json_form_obj = {};
//  var main_menu_val = $("#seo_main_menu :selected").val();
//  //json_form_obj['id']=1;
//
//  //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
//  $("#form")
//    .find(":input")
//    .each(function (key, value) {
//      name = $(this).attr("name");
//
//      if (name == "site_id") name = "id";
//
//      json_form_obj[name] = $(this).val();
//    });
//
//  var formData = JSON.stringify(json_form_obj);
//  ////////console.log(document.getElementById("sitemap").files[0].name);
//  //////console.log(formData);
//
//  //$formData = new FormData($_form);
//  clearAlert();
//
//  ////////console.log("setSEO");
//  $form = $($form);
//
//  $action = $form.attr("action");
//  $method = $form.attr("method");
//  $accept_charset = $form.attr("accept-charset");
//  $redirect_uri = $form.attr("data-redirect");
//
//  //////console.log("action : " + $action);
//  //////console.log("method : " + $method);
//  //////console.log("redirect : " + $redirect_uri);
//
//  $_form = document.getElementById($form.attr("id"));
//
//  ////////console.log('lala');
//  ////////console.log($_form);
//
//  $.ajax({
//    type: $method ? $method : "PUT",
//    //url: getBackendHost() + $action,
//    url: link + $action,
//    crossDomain: true,
//    headers: getHeaders(),
//    contentType: false,
//    processData: false,
//    // contentType: "charset=utf-8",
//    data: formData,
//    success: function (response, status, xhr) {
//      ////console.log(response);
//      obj = response;
//      ////////console.log(obj.id);
//      $redirect_uri += "?id=" + obj.id;
//
//      if (obj.id == 1 && main_menu_val == "seo") {
//        //IF IS SEO AND USER UPLOAD SITEMAP
//        $("#sitemap_form")
//          .find("input[name=file]")
//          .each(function (key, value) {
//            if ($(this).val().length > 0)
//              local_media_ajax_submit($("#sitemap_form"), $(".message_output"));
//          });
//
//        $("#robots_form")
//          .find("input[name=file]")
//          .each(function (key, value) {
//            if ($(this).val().length > 0)
//              local_media_ajax_submit($("#robots_form"), $(".message_output"));
//          });
//
//        $redirect_uri += "?id=" + obj.id + "&type=seo";
//      }
//
//      if (obj.code == 1) {
//        var message = "成功储存";
//        if (obj.redirect)
//          redirect_to($redirect_uri + "&alert-success=" + message);
//      } else if (obj.code == -1) {
//        redirect_to_login();
//      } else {
//        showAlert(obj.message, "danger", $error_selector);
//      }
//    },
//
//    error: function () {
//      showAlert(
//        "Problem occurred while sending request.",
//        "danger",
//        $error_selector
//      );
//    },
//  });
//}

function getSEO() {
  $.ajax({
    type: "GET",
    url: link + "/api/cn/site",
    data: {
      id: 1,
    },
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    //processData: false,
    // contentType: "charset=utf-8",

    success: function (response, status, xhr) {
      ////////console.log(response);
      obj = response;
      ////////console.log(obj.data);

      populateForm($("form"), obj.data);
      //$('#display_username').html(obj.data.username);
    },
  });
}

//not in use
//function setSEO($form) {
//  var json_form_obj = {
//    image_data: {},
//  };
//  var subdata = {};
//  var imagedata = {};
//  var name;
//  var filetype;
//  var extension;
//  var temp;
//  json_form_obj["id"] = 1;
//
//  //TO GET NAME AND VALUE FROM FORM AND STRINGIFY
//  $("#form")
//    .find(":input")
//    .each(function (key, value) {
//      name = $(this).attr("name");
//
//      json_form_obj[name] = $(this).val();
//    });
//
//  var formData = JSON.stringify(json_form_obj);
//  ////////console.log(document.getElementById("sitemap").files[0].name);
//  ////////console.log(formData);
//
//  //$formData = new FormData($_form);
//  clearAlert();
//
//  ////////console.log("setSEO");
//  $form = $($form);
//
//  $action = $form.attr("action");
//  $method = $form.attr("method");
//  $accept_charset = $form.attr("accept-charset");
//  $redirect_uri = $form.attr("data-redirect");
//
//  // //////console.log("action : " + $action);
//  ////////console.log("method : " + $method);
//  // //////console.log("redirect : " + $redirect_uri);
//
//  $_form = document.getElementById($form.attr("id"));
//
//  ////////console.log('lala');
//  ////////console.log($_form);
//
//  $.ajax({
//    type: $method ? $method : "PUT",
//    //url: getBackendHost() + $action,
//    url: link + $action,
//    crossDomain: true,
//    headers: getHeaders(),
//    contentType: false,
//    processData: false,
//    // contentType: "charset=utf-8",
//    data: formData,
//    success: function (response, status, xhr) {
//      ////////console.log(response);
//      obj = response;
//
//      //IF USER UPLOAD SITEMAP
//      $("#sitemap_form")
//        .find("input[name=file]")
//        .each(function (key, value) {
//          if ($(this).val().length > 0)
//            local_media_ajax_submit($("#sitemap_form"), $(".message_output"));
//        });
//
//      $("#robots_form")
//        .find("input[name=file]")
//        .each(function (key, value) {
//          if ($(this).val().length > 0)
//            local_media_ajax_submit($("#robots_form"), $(".message_output"));
//        });
//
//      /*if (obj.code == 1) {
//                if (obj.redirect) redirect_to($redirect_uri + "?alert-success=" + obj.message);
//            } else if (obj.code == -1) {
//                redirect_to_login();
//            } else {
//                showAlert(obj.message, "danger", $error_selector);
//            }*/
//    },
//
//    error: function () {
//      showAlert(
//        "Problem occurred while sending request.",
//        "danger",
//        $error_selector
//      );
//    },
//  });
//}

function local_media_ajax_submit(
  $form,
  $error_selector = $(".message_output")
) {
  $form = $($form);

  $action = $form.attr("action");
  $method = $form.attr("method");
  $accept_charset = $form.attr("accept-charset");
  $redirect_uri = $form.attr("data-redirect");

  $_form = document.getElementById($form.attr("id"));
  $formData = new FormData($_form);

  //clearAlert($error_selector);
  //  //////console.log($formData);
  ////////console.log(getHost());

  $.ajax({
    type: "POST",
    //url: 'http://fdcb6912.ngrok.io/assets/php/media-meta.php',
    //url: getHost()+'/assets/php/media-meta.php',
    url: "/assets/php/media-meta.php",
    contentType: false,
    cache: false,
    processData: false,
    data: $formData,
    success: function (response, status, xhr) {
      //////console.log(response);

      obj = response;

      if (obj.code == 1) {
        backend_media_ajax_submit(
          $form,
          {
            media_meta_data: obj.data,
            extra: obj.extra,
            method: "PUT",
            redirect: $redirect_uri,
            params: {
              image_data: response.image_data,
              extra: response.extra,
            },
          },
          $error_selector
        );
      } else {
        showAlert(response.message, "danger", $error_selector);
      }
    },
    error: function (resp) {
      //////console.log(resp);
      showAlert(
        "Problem occurred while sending requesddt.",
        "danger",
        $error_selector
      );
    },
  });
}

function backend_media_ajax_submit(
  $form,
  $data,
  $error_selector = $(".message_output")
) {
  $method = $data.method;
  $redirect_uri = $data.redirect;

  var uploadtype = $form.find("input[name=type]").val();

  var json_form_obj = {
    image_data: {
      uploadtype: {},
    },
  };
  json_form_obj["id"] = 1;
  json_form_obj.image_data.uploadtype.url = $data.media_meta_data.url;
  json_form_obj.image_data.uploadtype.name = $data.media_meta_data.name;
  json_form_obj.image_data.uploadtype.type = $data.media_meta_data.type;
  json_form_obj.image_data.uploadtype.size = $data.media_meta_data.filesize;
  json_form_obj.extra = $data.extra;

  var formData = JSON.stringify(json_form_obj);
  ////////console.log(document.getElementById("sitemap").files[0].name);
  ////////console.log(formData);

  $.ajax({
    type: $method ? $method : "PUT",
    url: getBackendHost() + "/api/cn/site",
    //url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',

    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    processData: false,
    // contentType: "charset=utf-8",
    data: formData,

    success: function (response, status, xhr) {
      //////console.log(response);

      obj = response;

      if (obj.code == 1) {
        ////////console.log(obj.code);
        media_save($form, obj, $redirect_uri);
      } else {
        showAlert(response.message, "danger", $error_selector);
      }
    },
    error: function (resp) {
      //////console.log(resp);
      showAlert(
        "Problem occurred while sending request.",
        "danger",
        $error_selector
      );
    },
  });
}

function media_save($form, obj, $redirect_uri) {
  $form.find("#extra").val(obj.extra.extra);
  $formData = new FormData($_form);

  //clearAlert($error_selector);
  //////console.log($formData);

  $.ajax({
    type: "POST",
    // url: 'http://fdcb6912.ngrok.io/assets/php/media-save.php',
    //url: getHost()+'/assets/php/media-meta.php',
    url: "/assets/php/media-save.php",
    contentType: false,
    cache: false,
    processData: false,
    data: $formData,
    success: function (response, status, xhr) {
      //////console.log(response);
      obj = response;
    },
    error: function (resp) {
      //////console.log(resp);
      showAlert(
        "Problem occurred while sending requesddt.",
        "danger",
        $error_selector
      );
    },
  });
}

function submitForm() {
  var site = {
    site: "",
  };
  form = document.getElementById("form");
  formData = new FormData(form);
  var data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });
  site = {
    ...site,
    ...data,
    id: data["site_id"],
  };
  var file1 = $("#xmlFile")[0].files[0];
  var file2 = $("#robotFile")[0].files[0];
  var uploadFile1Promise = uploadFilesCall(file1, 1);
  var uploadFile2Promise = uploadFilesCall(file2, 0);
  Promise.all([uploadFile1Promise, uploadFile2Promise])
    .then(([uploadFile1Response, uploadFile2Response]) => {
      if (uploadFile1Response.code != 1)
        return Promise.reject(uploadFile1Response);

      if (uploadFile2Response.code != 1)
        return Promise.reject(uploadFile2Response);

      response = [uploadFile1Response.extra, uploadFile2Response.extra];
      return response;
    })
    .then((response) => {
      response.map(saveMetaFilePath);
    })
    .then((_) => {
      checkSitemapExists();
    })
    .then((_) => {
      checkRobotExists();
      return site;
    })
    .then(createSEO)
    .then((createSiteResponse) => {
      $redirect_uri = $(form).attr("data-redirect");
      if (createSiteResponse.code == 1)
        showAlert(createSiteResponse.status, "success", $(".message_output"));
      else showAlert(createSiteResponse.status, "danger", $(".message_output"));
      return createSiteResponse;
    })
    .catch((err) => {
      showAlert("Failed to upload file!", "danger", $(".message_output"));
    });
}
function uploadFilesCall(file, isXml) {
  if (file == null)
    return Promise.resolve({
      code: 1,
      isFileNull: true,
    });
  var type = "";
  isXml ? (type = "sitemap") : (type = "robots");
  var formData = new FormData();
  formData.append("file", file);
  formData.append("type", type);
  return $.ajax({
    type: "POST",
    url: "/assets/php/media-meta.php",
    data: formData,
    crossDomain: true,
    contentType: false,
    processData: false,
  });
}

function createSEO(SEO) {
  return callAjaxFunc("PUT", SEO, "/api/cn/site");
}

function saveMetaFilePath(path) {
  if (path == null)
    return Promise.resolve({
      code: 1,
    });
  var formData = new FormData();
  formData.append("tempfile", path);
  return $.ajax({
    type: "POST",
    url: "/assets/php/media-save.php",
    data: formData,
    crossDomain: true,
    contentType: false,
    processData: false,
  });
}

function callAjaxFunc(method, data, url) {
  clearAlert();
  return $.ajax({
    type: method,
    url: getBackendHost() + url,
    data: JSON.stringify(data),
    crossDomain: true,
    headers: getHeaders(),
    contentType: false,
    processData: false,
  });
}

function viewFilesFunction(fileType) {
  if (fileType == "viewSitemap") {
    //window open xml file
    window.open("/sitemap.xml");
  } else {
    //window open robot file
    window.open("/robots.txt");
  }
}

function checkSitemapExists() {
  return $.ajax({
    url: "/sitemap.xml",
    type: "HEAD",
    error: function () {
      $("#viewSitemap").css("display", "none");
    },
    success: function () {
      $("#viewSitemap").css("display", "block");
    },
  });
}

function checkRobotExists() {
  return $.ajax({
    url: "/robots.txt",
    type: "HEAD",
    error: function () {
      $("#viewRobot").css("display", "none");
    },
    success: function () {
      $("#viewRobot").css("display", "block");
    },
  });
}
