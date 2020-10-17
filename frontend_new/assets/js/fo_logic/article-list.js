// template.config("escape", false);

function article_list($category_id = 0, $sub_category_id = 0, $type = "latest_news", $limit = 10, $page_no = 1) {
    $('#article_list').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    if ($category_id) {
        filter.push({field: "article.category_id", value: $category_id, operator: "="});
    }

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
    }

    if ($type) {
        filter.push({field: "article.type", value: $type, operator: "="});
    }

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
            page_number: $page_no,
            label: 'article-list',
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
        obj["totalPage"] = articles.totalPage;
        // obj['current_page'] = $page_no;
        // obj['pages'] = [$page_no++, $page_no++, $page_no++];
        //console.log(obj);

        var html = $.get('/cn/module/article_list.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#article_list').html(str);
        });

    });
}
