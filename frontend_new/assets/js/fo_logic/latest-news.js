var ln_hardcoded_data = {
    data: [
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        },
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        },
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        },
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        },
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        },
        {
            title: "<Title>",
            snippet: "<Snippet>",
            image: "/upload/news/2019-12-09a_375.jpg",
            created_datetime: "<Created Datetime>",
            category: "<Category>"
        }
    ]
};

$exclude_list = [];

function latest_news($category_id = 0, $sub_category_id = 0, $limit = 6) {
    $('#latest_news').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [];

    if ($category_id) {
        filter.push({field: "article.category_id", value: $category_id, operator: "="});
    }

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
    }

    if ($exclude_list) {
        $.each($exclude_list, function (index, value) {
            filter.push({field: "article.category_id", value: value, operator: "!="});
        });
    }

    var latest_news = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: 'latest-news',
        },
        success: function (response) {
            var obj = {
                latest_news: response.data
            };
        }
    });

    // var latest_news = ln_hardcoded_data;

    //console.log(latest_news);

    $.when(latest_news).done(function (latest_news) {
        let obj = [];
        obj['latest_news'] = latest_news.data;
        obj['category'] = ($category_id) ? true : false;
        obj['sub_category'] = ($sub_category_id) ? true : false;

        //console.log(obj);

        var html = $.get('/cn/module/latest_news.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#latest_news').html(str);
        });
    });
}
