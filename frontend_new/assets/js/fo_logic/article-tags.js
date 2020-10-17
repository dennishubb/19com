// template.config("escape", false);

function article_tags($tags = "", $limit = 25, $page_no = 1) {
    $('#article_tags').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    if ($tags) {
        filter.push({field: "article.tags", value: "%" + $tags + "%", operator: "LIKE"});
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
            page_number: window.tag_page_no ? window.tag_page_no : 1,
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
        obj["totalPage"] = articles["totalPage"];
        obj['current_page'] = window.tag_page_no ? window.tag_page_no : 1;

        //console.log(obj);

        var html = $.get('/cn/module/tag_item.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#article_tags').html(str);
        });

    });
}
