function category_news($category_id = 0, $sub_category_id = 0, $id, $title, $exclude_cat = [], $limit = 5) {
    $($id).html("");

    var filter = [];

    if ($exclude_cat.length > 0) {
        if ($sub_category_id) {
            filter.push({field: "article.sub_category_id", value: $exclude_cat, operator: "NOT IN"});
        } else {
            filter.push({field: "article.category_id", value: $exclude_cat, operator: "NOT IN"});
        }
    }

    var category_news = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: 'featured-news',
        },
        success: function (response) {
            var obj = {
                category_news: response.data
            };
        }
    });

    $.when(category_news).done(function (featured_news) {
        let obj = [];
        obj['title'] = $title;
        obj['category_news'] = category_news.data;

        //console.log(obj);

        var html = $.get('/cn/module/category_news.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $($id).html(str);
        });
    });

}
