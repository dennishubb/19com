var mn_hardcoded_data = {
    data: [
        {
            title: "012345678901234567890123456789",
            created_datetime: "YYYY-MM-DD",
        },
        {
            title: "012345678901234567890123456789",
            created_datetime: "YYYY-MM-DD",
        },
        {
            title: "012345678901234567890123456789",
            created_datetime: "YYYY-MM-DD",
        },
        {
            title: "012345678901234567890123456789",
            created_datetime: "YYYY-MM-DD",
        },
        {
            title: "012345678901234567890123456789",
            created_datetime: "YYYY-MM-DD",
        },
    ]
};

function more_news($category_id = 0, $sub_category_id = 0, $exclude_cat = [], $limit = 30) {
    $('#more_news').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    if ($category_id) {
        filter.push({field: "article.category_id", value: $category_id, operator: "="});
    }

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
    }

    if ($exclude_cat.length > 0) {
        if ($sub_category_id) {
            filter.push({field: "article.sub_category_id", value: $exclude_cat, operator: "NOT IN"});
        } else {
            filter.push({field: "article.category_id", value: $exclude_cat, operator: "NOT IN"});
        }
    } else {
        if ($exclude_list) {
            $.each($exclude_list, function (index, value) {
                filter.push({field: "article.category_id", value: value, operator: "!="});
            });
        }
    }

    var more_news = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: 'more-news',
        },
        success: function (response) {
            var obj = {
                more_news: response.data
            };
        }
    });

    // var more_news = mn_hardcoded_data;

    $.when(more_news).done(function (more_news) {
        let obj = [];
        obj['more_news'] = more_news.data;

        //console.log(obj);

        var html = $.get('/cn/module/more_news.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#more_news').html(str);
        });
    });
}
