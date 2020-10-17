function type_news($selector, $type, $sub_category_id = 0, $limit = 5) {
    $($selector).html("");

    var filter = [];

    filter.push({field: "article.type", value: $type, operator: "="});

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
    }

    var type_news = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: 'type-news',
        },
        success: function (response) {
            var obj = {
                type_news: response.data
            };
        }
    });

    //console.log(type_news);

    $.when(type_news).done(function (type_news) {
        let obj = [];
        obj['type_news'] = type_news.data;

        //console.log(obj);

        var html = $.get('/cn/module/type_news.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $($selector).html(str);
        });
    });

}
