
var link = getBackendHost();

buildNavigationBarsV2(link);
buildHeader();

//add change pw button to every page header
$(function chgpwbutton() {
    var button_content="    <div class='chg_pw_button'>"
                            +"  <button type='button' class='btn blue-madison btn-outline' style='margin-top:15px' onclick='change_pw()'><i class='fa fa-key' style='color:powderblue'></i></button>"
                            +'</div>';
    $('.kt-header__topbar-item--user').prepend(button_content);
});

$.ajaxSetup({
    beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", window.localStorage.admin_access_token);
    }
});

$( document ).ajaxComplete(function( event, xhr, settings ) {   
    if (xhr.responseJSON) { 
        if (xhr.responseJSON.code === 401) {    
            redirect_to_login()
        }   
    }   
});

function change_pw(){
    location.href = 'change-password.html';
}
function buildHeader() {
    buildLanguageDialog([{"locale": "Chinese", "img": "034-china.svg"}]);
    buildUserMinifyDialog({
        "username": window.localStorage.admin_username,
        "display_name": window.localStorage.admin_display_name
    });
    buildWhiteLabel();
}

function buildWhiteLabel($default = "../../assets/branding/logo.png") {
    $('#branding_pc').attr("src", $default);
    $('#branding_pc').css('width', '112px');

    $('#branding_mobile').attr("src", $default);
    $('#branding_mobile').css('width', '82px');
}

function buildTimeOut() {
    $.sessionTimeout({
        title: '系统信息',
        message: '系统将在5分钟后自动登出。',
        logoutButton: '登出',
        keepAliveButton: '继续使用',
        keepAliveUrl: 'index.html',
        logoutUrl: '../auth/logout.html',
        redirUrl: '../auth/logout.html',
        warnAfter: 300000,
        redirAfter: 600000
    });
}

function redirect_to_login() {
    window.location.href = "../auth/login.html";
}

//elise
function get_permission() {
    var link = window.location.protocol + "//test.19com.backend:5280/";
    var role_id = window.localStorage.admin_role_id;

    ////console.log('role id:' +role_id);
    ////console.log(getHeaders());

    $.ajax({
        type: 'GET',
        url: link + '/api/cn/role_permission',
        data: {search: {role_id: role_id}},

        crossDomain: true,

        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response.data);
            obj = response;
            var permissoin_id = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {
                permissoin_id.push(value.permission_id);

            });
            ////console.log(permissoin_id);
            show_sidemenu_jax(permissoin_id);
            ////console.log(curr_permissoin_id);
            //$('#display_username').html(obj.data.username);
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },

    });

}

//BY ELISE, GET LIST FROM API PERMISSION AND DISPLAY SIDE MENU.
function getNavigationsV2() {
    //return get_role_permission();
    var sidemenu = [];
    var subsidemenu = {};
    ////console.log(getHeaders());
    $.ajax({
        type: 'GET',
        url: getBackendHost() + '/api/cn/permission',
        //data:{search:{role_id:role_id}},

        crossDomain: true,
        //headers: getHeaders(),
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            obj = response;

            //sort by sorting
            obj.data.sort(function (a, b) {
                return a.sorting - b.sorting;
            });

            ////console.log(response.data);


            var temp = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {
                subsidemenu.title = value.name;
                subsidemenu.icon = "flaticon2-architecture-and-city";
                subsidemenu.url = value.url;
                subsidemenu.type = value.type;
                subsidemenu.display = true;
                subsidemenu.involved = [''];

                sidemenu.push(subsidemenu);

                ////console.log(subsidemenu);

                subsidemenu = {};
            });
            //console.log(sidemenu);
            show_sidemenu(sidemenu);

        }

    });

    /*var sidemenu = [];
    var subsidemenu={};
    subsidemenu.title ="首頁";
    subsidemenu.icon ="flaticon2-architecture-and-city";
    subsidemenu.url ="index.html";
    subsidemenu.type ="single";
    subsidemenu.display=true;
    subsidemenu.involved=[''];
    sidemenu.push(subsidemenu);*/

    /*


        */
    //sidemenu
    ////console.log(sidemenu[0].display);
    //sidemenu[0].display=false;

}

//BY JAX
function getNavigations() {
    get_permission();
}

//for jax's buildNavigationBarsV2
function show_sidemenu_jax(permissoin_id_arr) {
    ////console.log(permissoin_id_arr);
    var index_menu = {
        "data": []
    };
    var index_subdata = {};

    var sidemenu = [
        {
            title: "首頁",
            permission_id: 32,
            icon: "flaticon2-architecture-and-city",
            url: "index.html",
            type: "single",
            display: true,
            involved: ['']
        },

        {
            title: "Articles",
            type: "title",
        },

        {
            title: "SEO",
            permission_id: 10,
            icon: "fa fa-ad",
            url: "seo.html",
            type: "single",
            involved: []
        },

        {
            title: "文章",
            permission_id: 11,
            icon: "fa fa-newspaper",
            url: "article-list.html",
            type: "single",
            involved: [
                "article-add.html", "article-edit.html"
            ]
        },

        {
            title: "媒体库",
            permission_id: 12,
            icon: "fa fa-cloud-upload-alt",
            url: "media-library-v2.html",
            type: "single",
            involved: []
        },

        {
            title: "广告按钮",
            permission_id: 13,
            icon: "fa fa-ad",
            display: true,
            url: "ad-button-list.html",
            type: "single",
            involved: [
                'ad-button-add.html', 'ad-button-edit.html'
            ]
        },


        {
            title: "分类管理",
            permission_id: 14,
            icon: "fa fa-list",
            url: "sort.html",
            type: "single",
            involved: []
        },

        {
            title: "Sections",
            type: "title",
        },

        {
            title: "会员管理",
            permission_id: 15,
            icon: "fa fa-users",
            url: "member-list.html",
            urls: [
                {
                    key: "所有会员",
                    permission_id: 16,
                    path: "member-list.html",
                    involved: ["member-edit.html", "member-edit-password.html", "member-add.html"]
                },
                {
                    key: "会员黑名单列表",
                    permission_id: 44,
                    path: "member-blacklist.html"

                },
                {
                    key: "会员白名单列表",
                    permission_id: 45,
                    path: "member-whitelist.html"

                },
                {
                    key: "会员等级",
                    permission_id: 17,
                    path: "member-rank-list.html",
                    involved: ["user-role-edit.html", "user-role-add.html"]
                },
                {
                    key: "胜率列表",
                    permission_id: 18,
                    path: "win-rate-list.html",
                    involved: ["user-role-edit.html", "user-role-add.html"]
                }
            ],
            type: "multi",
            involved: []
        },

        {
            title: "积分管理",
            permission_id: 19,
            icon: "fa fa-list",
            type: "multi",
            url: "point-management.html",
            urls: [
                {
                    key: "兑换审核",
                    permission_id: 41,
                    path: "redeem-review.html",
                    involved: []
                },
                {
                    key: "礼品列表",
                    permission_id: 42,
                    path: "gift-list.html",
                    involved: []
                },
                {
                    key: "战数管理",
                    permission_id: 43,
                    path: "point-management.html",
                    involved: ["point-management.html"]
                }
            ],
        },

        {
            title: "赛事预测",
            permission_id: 20,
            icon: "fa fa-list",
            url: "match-prediction-list.html",
            urls: [
                {
                    key: "新增赛事",
                    permission_id: 21,
                    path: "match-add.html",
                    involved: []
                },
                {
                    key: "设置",
                    permission_id: 53,
                    path: "match-setting.html",
                    involved: []
                },
                {
                    key: "赛事结果",
                    permission_id: 22,
                    path: "match-result.html",
                    involved: []
                },
                {
                    key: "预测列表",
                    permission_id: 23,
                    path: "match-prediction-list.html",
                    involved: []
                },
                {
                    key: "赛事历史",
                    permission_id: 24,
                    path: "match-history-v2.html",
                    involved: ["match-history-v2.html"]
                },
                {
                    key: "留言评论",
                    permission_id: 33,
                    path: "match-comment.html",
                    involved: []
                },
                {

                    key: "赛事教程",
                    permission_id: 34,
                    path: "match-tutorial.html",
                    involved: []
                },

            ],
            type: "multi",
            involved: []
        },

        {
            title: "潮星天堂",
            permission_id: 25,
            type: "single",
        },
        {
            title: "活动专区",
            permission_id: 26,
            icon: "fa fa-comments",
            url: "promotion-list.html",
            urls: [
                {
                    key: "新增活动",
                    permission_id: 38,
                    path: "promotion-add.html",
                    involved: []
                },
                {
                    key: "活动列表",
                    permission_id: 39,
                    path: "promotion-list.html",
                    involved: []
                },
                {
                    key: "活动审核",
                    permission_id: 40,
                    path: "promotion-redeem.html",
                    involved: []
                }
            ],
            type: "multi",
        },
        {
            title: "留言评论",
            permission_id: 27,
            icon: "fa fa-comments",
            url: "message.html",
            type: "single",
        },
        {
            title: "留言举报",
            permission_id: 46,
            icon: "fa fa-comments",
            url: "message-report.html",
            type: "single",
        },
        {
            title: "Settings",
            type: "single",
        },

        {
            title: "账号管理",
            permission_id: 28,
            icon: "fa fa-users",
            url: "admin-list.html",
            type: "multi",
            involved: [''],
            urls: [
                {
                    key: "所有账号",
                    permission_id: 29,
                    path: "admin-list.html",
                    involved: ["admin-edit.html", "admin-edit-password.html", "admin-add.html"]
                },
                {
                    key: "内部黑名单列表",
                    permission_id: 51,
                    path: "admin-blacklist.html"

                },
                {
                    key: "内部白名单列表",
                    permission_id: 52,
                    path: "admin-whitelist.html"

                },
                 {
                    key: "账号功能权限设置",
                    permission_id: 30,
                    path: "admin-role-edit.html"

                }
            ],
        },
        
        {
            title: "19宠粉专区",
            permission_id: 47,
            icon: "fa fa-users",
            url: "fan-zone-list.html",
            type: "multi",
            involved: [],
            urls: [
                {
                    key: "新增宠粉活动",
                    permission_id: 49,
                    path: "fan-zone-add.html",
                    involved: []
                },
                {
                    key: "19宠粉专区列表",
                    permission_id: 50,
                    path: "fan-zone-list.html"

                }
            ],
        },
        {
            title: "网站资讯",
            permission_id: 31,
            icon: "fab fa-whmcs",
            url: "web-info.html",
            type: "single",
            involved: []
        }
    ];

    ////console.log(sidemenu);
    $(".kt-menu__nav").html("");
    var content = "";
    ////console.log(permissoin_id_arr)


    $.each(sidemenu, function (i, entry) {

        ////console.log(entry);
        //if (entry.display!=false){
        if (permissoin_id_arr.indexOf(entry.permission_id) > -1) {
            switch (entry.type) {
                case 'title':
                    content += drawNavTitle(entry.title);
                    break;
                case 'single':
                    content += drawNavSingle(entry.title, entry.icon, entry.url, entry.involved);
                    break;
                case 'multi'://only loop for parent, child need to go in drawNavMulti
                    content += drawNavMulti(entry.title, entry.icon, entry.urls, entry.involved, permissoin_id_arr);
                    break;
            }


            index_subdata['title'] = entry.title;
            index_subdata['url'] = entry.url;
            index_subdata['icon'] = entry.icon;
            index_menu.data.push(index_subdata);
            index_subdata = {};

        }
    });
    ////console.log(content);
    $(".kt-menu__nav").html(content);

    if (getCurrentUri().indexOf('index.html') > 0)
        getDashboard(index_menu);

}

//BY ELISE, GET LIST FROM API PERMISSION AND DISPLAY SIDE MENU.
function show_sidemenu(sidemenu) {
    $(".kt-menu__nav").html("");
    var content = "";
    $.each(sidemenu, function (i, entry) {
        if (entry.display != false) {
            switch (entry.type) {
                case 'title':
                    content += drawNavTitle(entry.title);
                    break;
                case 'single':
                    content += drawNavSingle(entry.title, entry.icon, entry.url, entry.involved);
                    break;
                case 'multi':
                    content += drawNavMulti(entry.title, entry.icon, entry.urls, entry.involved);
                    break;
            }
        }
    });
    $(".kt-menu__nav").html(content);
}

//BY JAX, modified by elise [hardcode side menu]
function buildNavigationBarsV2(link) {
    //alert(link)
    
    var role_id = window.localStorage.admin_role_id;

    ////console.log('role id:' +role_id);
    ////console.log(getHeaders());

    $.ajax({
        type: 'GET',
        url: link + '/api/cn/role_permission',
        data: {search: {role_id: role_id}},

        crossDomain: true,
        //headers: "Authorization: window.localStorage.access_token"
        contentType: false,
        //processData: false,
        // contentType: "charset=utf-8",

        success: function (response, status, xhr) {
            ////console.log(response.data);
            obj = response;
            var permissoin_id = [];
            ////console.log(obj.data[0].id);

            $.each(obj.data, function (index, value) {
                permissoin_id.push(value.permission_id);

            });
            ////console.log(permissoin_id);
            show_sidemenu_jax(permissoin_id);
            ////console.log(curr_permissoin_id);
            //$('#display_username').html(obj.data.username);
        },
        error: function () {
            showAlert("Problem occurred while sending request.", "danger", $error_selector);
        },

    });
}

//BY ELISE, GET LIST FROM API PERMISSION AND DISPLAY SIDE MENU.
function buildNavigationBarsV3() {
    $(".kt-menu__nav").html("");
    var content = "";
    var sidemenu = getNavigationsV2();
    $.when(sidemenu).done(function () {////console.log(sidemenu);
        $.each(sidemenu, function (i, entry) {
            if (entry.display != false) {
                switch (entry.type) {
                    case 'title':
                        content += drawNavTitle(entry.title);
                        break;
                    case 'single':
                        content += drawNavSingle(entry.title, entry.icon, entry.url, entry.involved);
                        break;
                    case 'multi':
                        content += drawNavMulti(entry.title, entry.icon, entry.urls, entry.involved);
                        break;
                }
            }
        });
    });


    $(".kt-menu__nav").html(content);
}

function buildNavigationBars() {
    $(".kt-menu__nav").html("");
    var content = "";

    content += drawNavSingle("首頁", "flaticon2-architecture-and-city", "index.html", ['']);

    content += drawNavTitle("管理");
    content += drawNavSingle("關於我們", "flaticon2-information", "about-us.html", [
        'about-us-edit.html',
        'about-us-add.html'
    ]);
    content += drawNavSingle("最新消息", "fa fa-newspaper", "news-list.html", [
        'news-edit.html',
        'news-add.html'
    ]);
    content += drawNavSingle("爬蟲資料", "fa fa-list", "sport-list.html");
    content += drawNavSingle("首頁廣告", "fa fa-ad", "ad-main-list.html");
    content += drawNavSingle("內頁廣告", "fa fa-ad", "ad-sub-list.html");
    content += drawNavMulti("賬號", "fa fa-users", [{
        "key": "列表",
        "path": "user-list.html"
    }, {
        "key": "權限",
        "path": "user-role-list.html",
        "involved": [
            "user-role-edit.html"
        ]
    }]);

    content += drawNavTitle("設定");
    content += drawNavSingle("網站基本", "fab fa-whmcs", "web-settings.html");

    $(".kt-menu__nav").html(content);
}

function buildLanguageDialog($types) {
    $("#kt-language").html("");
    var content = "";

    content += '<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">';
    content += '<span class="kt-header__topbar-icon">';
    content += '<img src="../../assets/template/metronic/demo12/assets/media/flags/' + $types[0].img + '">';
    content += '</span>';
    content += '</div>';

    content += '<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround">';
    content += '<ul class="kt-nav kt-margin-t-10 kt-margin-b-10">';
    $.each($types, function (key, entry) {
        content += '<li class="kt-nav__item kt-nav__item--active">';
        content += '<a href="#" class="kt-nav__link">';
        content += '<span class="kt-nav__link-icon">';
        content += '<img src="../../assets/template/metronic/demo12/assets/media/flags/' + entry.img + '">';
        content += '</span>';
        content += '<span class="kt-nav__link-text">' + entry.locale + '</span>';
        content += '</a>';
        content += '</li>';
    });
    content += '</ul>';
    content += '</div>';

    $("#kt-language").html(content);
}

function buildUserMinifyDialog($user_info) {
    $("#kt-user").html("");
    var content = "";

    content += '<span class="kt-header__topbar-welcome kt-hidden-mobile">歡迎,</span>';
    content += '<span class="kt-header__topbar-username kt-hidden-mobile">' + $user_info.username + '</span>';
    content += '<span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">';
    content += $user_info.username.substr(0, 1).toUpperCase();
    content += '</span>';

    $("#kt-user").html(content);

    buildUserDialogDropdown($user_info);
}

function buildUserDialogDropdown($user_info) {
    $("#kt-user-dropdown").html("");
    var content = "";

    content += '<div class="kt-user-card__avatar">';
    content += '<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">';
    content += $user_info.username.substr(0, 1).toUpperCase();
    content += '</span>';
    content += '</div>';

    content += '<div class="kt-user-card__name">' + $user_info.display_name + '</div>';
    content += '<div class="kt-user-card__badge"></div>';

    $("#kt-user-dropdown").html(content);
}

function drawNavSingle($title, $icon, $url, $involved_urls = []) {
    var content = "";
    $involved_urls.push($url);

    $active = (checkActive($involved_urls) !== -1) ? ' kt-menu__item--active' : '';

    content += '<li class="kt-menu__item' + $active + '" aria-haspopup="true">';
    content += '<a href="' + $url + '" class="kt-menu__link ">';
    content += '<i class="kt-menu__link-icon ' + $icon + '"></i>';
    content += '<span class="kt-menu__link-text">' + $title + '</span>';
    content += '</a>';
    content += '</li>';
    return content;
}

function drawNavMulti($title, $icon, $urls, $involved_urls = [], permissoin_id_arr) {


    ////console.log(permissoin_id_arr);

    //if (permissoin_id_arr.indexOf(entry.permission_id)>-1){
    //
    //}

    $.each($urls, function (key, entry) {

        ////console.log(key, entry,entry.permission_id);
        if (permissoin_id_arr.indexOf(entry.permission_id) > -1) {// if role permission exist
           
            $involved_urls.push(entry.path);
            $.each(entry.involved, function (k, i) {
                $involved_urls.push(i);
            });
            entry.display = true;
        } else
            entry.display = false;
        // //console.log(entry);
    });

    $active = (checkActive($involved_urls) !== -1) ? ' kt-menu__item--here kt-menu__item--open' : '';

    var content = "";
    content += '<li class="kt-menu__item kt-menu__item--submenu' + $active + '" aria-haspopup="true" data-ktmenu-submenu-toggle="hover" >';
    content += '<a href="javascript:;" class="kt-menu__link kt-menu__toggle">';
    content += '<i class="kt-menu__link-icon fa fa-users"></i>';
    content += '<span class="kt-menu__link-text">' + $title + '</span>';
    content += '<i class="kt-menu__ver-arrow la la-angle-right"></i>';
    content += '</a>';
    content += '<div class="kt-menu__submenu " kt-hidden-height="auto">';
    content += '<span class="kt-menu__arrow"></span>';
    content += '<ul class="kt-menu__subnav">';

    $.each($urls, function (key, entry) {
        var paths = [];
        paths.push(entry.path);
        $.each(entry.involved, function (k, i) {
            paths.push(i);
        });

        if (entry.display == true)
            display_type = 'block';
        else
            display_type = 'none';
        $active = (checkActive(paths) !== -1) ? ' kt-menu__item--active' : '';
        content += '<li class="kt-menu__item' + $active + '" aria-haspopup="true" style="display:' + display_type + ';">';
        content += '<a href="' + entry.path + '" class="kt-menu__link ">';
        content += '<i class="kt-menu__link-bullet kt-menu__link-bullet--line"></i>';
        content += '<span class="kt-menu__link-text"> ' + entry.key + ' </span>';
        content += '</a>';
        content += '</li>';
    });

    content += '</ul>'
    content += '</div>';
    content += '</li>';
    return content;
}

function drawNavTitle($title) {
    var content = "";
    content += '<li class="kt-menu__section ">';
    content += '<h4 class="kt-menu__section-text font-weight-500" style="font-size: 1.1rem">' + $title + '</h4>';
    content += '<i class="kt-menu__section-icon flaticon-more-v2"></i>';
    content += '</li>';
    return content;
}

function checkActive($involved_urls) {
    $current_uri = getCurrentUri().replace("/cn/backend-admin/", "");

    if ($involved_urls.length > 0) {
        return jQuery.inArray($current_uri, $involved_urls);
    } else {
        return false;
    }
}

function checkActivePath($uri) {
    $current_uri = getCurrentUri().replace("/cn/backend-admin/", "");
    return $current_uri == $uri;
}

function blockUI() {
    KTApp.block('.kt-portlet', {
        overlayColor: '#000000',
        state: 'primary'
    });
}

function unblockUI() {
    KTApp.unblock('.kt-portlet');
}

function export2csv(table_id) {//pass in table id
    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById(table_id); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); 

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html","replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus(); 
        sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
    }  
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

    return (sa);
}

function export2csv_old($headers = [], $datas = []) {
    let data = "";
    const tableData = [];

    let rows = [];
    rows[0] = $headers;
    rows = rows.concat($datas);

    for (const $data of $datas) {
    rows.push($data);
    }

    for (const row of rows) {
    const rowData = [];
    for (const column of row) {
    rowData.push(column);
    }
    tableData.push(rowData.join(","));
    }
    data += tableData.join("\n");
    const a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob(["\uFEFF"+data], { type: "text/csv,charset=utf-18" }));
    a.setAttribute("download", "data.csv");
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function getDashboard(index_menu) {
////console.log(index_menu);
    var content2 = '';
    //working div for reference
    content2 = '<div class="col-xl-3 col-lg-6 col-md-6" >' +
        ' <!--Begin::Portlet-->' +
        '<div class="kt-portlet">' +
        ' <div class="kt-portlet__body">' +
        '<!--begin::Widget -->' +
        '  <div class="kt-widget__files">' +
        ' <div class="kt-widget__media">' +
        '<span class="kt-widget__img kt-hidden-">' +
        '<button type="button"' +
        'class="btn btn-secondary btn-lg btn-icon btn-circle"' +
        'style="width: 8rem;height: 8rem;"' +
        'onclick="window.location=' + "'" + 'c.html' + "'" + ';">' +
        '<i class="text-primary fa fa-info"' +
        'style="font-size: 4rem;"></i>' +
        '</button>' +
        '</span>' +
        '</div>' +
        '<span class="kt-widget__desc kt-padding-t-25">' +
        '<h5 class="font-weight-bold"> 關於我們d </h5>' +
        '<h6 style="padding-top: 1rem;"> 關於我們 </h6>' +
        ' </span>' +
        '</div>' +

        '<!--end::Widget -->' +
        '</div>' +
        ' </div>' +

        ' <!--End::Portlet-->' +
        ' </div>';


    var content = '';
    $.each(index_menu.data, function (i, entry) {
        content += '<div class="col-xl-3 col-lg-6 col-md-6" >' +
            ' <!--Begin::Portlet-->' +
            '<div class="kt-portlet">' +
            ' <div class="kt-portlet__body">' +
            '<!--begin::Widget -->' +
            '  <div class="kt-widget__files">' +
            ' <div class="kt-widget__media">' +
            '<span class="kt-widget__img kt-hidden-">' +
            '<button type="button"' +
            'class="btn btn-secondary btn-lg btn-icon btn-circle"' +
            'style="width: 8rem;height: 8rem;"';
        content += 'onclick="window.location=';
        content += "'";
        content += entry.url;//+ 'c.html'+
        content += "'";
        content += ';">';


        content += '<i class="text-primary ';
        content += entry.icon;
        content += '"' + 'style="font-size: 4rem;"></i>' +
            '</button>' +
            '</span>' +
            '</div>' +
            '<span class="kt-widget__desc kt-padding-t-25">';
        content += '<h5 class="font-weight-bold">';
        content += entry.title;
        content += '</h5>';
        content += '<h6 style="padding-top: 1rem;">';
        content += entry.title;
        content += '</h6>';
        content += ' </span>' +
            '</div>' +

            '<!--end::Widget -->' +
            '</div>' +
            ' </div>' +

            ' <!--End::Portlet-->' +
            ' </div>';

    });


    $('#index_grid').html(content);
}
