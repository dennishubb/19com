function article_radio() {
    $('#category_radio_area').html("");

    var filter = [
        {field: "category.type", value: "sport", operator: "="},
        {field: "category.parent_id", value: 0, operator: "="},
        {field: "category.id", value: "1", operator: "!="},
        {field: "category.id", value: "2", operator: "!="},
        {field: "category.id", value: "3", operator: "!="},
        {field: "category.id", value: "4", operator: "!="}
    ];

    var categories = $.ajax({
        url: link + 'api/cn/category',
        type: 'get',
        data: {
            filter: filter,
            sort: {
                field: 'category.sorting',
                sort: 'ASC'
            },
            label: 'article-radio',
        },
        success: function (response) {
            var obj = {
                categories: response.data
            };
        }
    });

    $.when(categories).done(function (categories) {
        let obj = [];
        obj['categories'] = categories.data;

        //console.log(obj);

        var html = $.get('/cn/module/category_radio.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#category_radio_area').html(str);

            article_category_list(obj['categories'][0]['id'], 0, 10);
        });

    });

}

var cat_id = 0;
var sub_cat_id = 0;
var limit = 0;

function article_category_list($category_id = 0, $sub_category_id = 0, $limit = 10, $page_no = window.article_all_list_page_no) {
    $('#article_list').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    if ($category_id) {
        filter.push({field: "article.category_id", value: $category_id, operator: "="});
        cat_id = $category_id;
    }

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
        sub_cat_id = $sub_category_id;
    }

    limit = $limit;

    var articles = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            page_number: window.article_all_list_page_no ? window.article_all_list_page_no : 1,
            label: 'article-category-list',
        },
        success: function (response) {
            var obj = {
                articles: response.data
            };
        }
    });

    $.when(articles).done(function (articles) {
        let obj = [];
        obj['articles'] = articles.data;
        obj["totalPage"] = articles["totalPage"];

        //console.log("---");
        //console.log(obj);

        var html = $.get('/cn/module/article_category_list.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#article_list').html(str);
        });

    });
}
