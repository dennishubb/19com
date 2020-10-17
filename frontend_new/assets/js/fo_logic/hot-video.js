function hot_videos($category_id = 0, $sub_category_id = 0, $limit = 4) {
    $('#hot_video_area').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [
        {field: "article_id", value: 0, operator: "!="},
        {field: "article.hot", value: 1, operator: "="}
    ];

    var hot_videos = $.ajax({
        url: link + 'api/cn/upload',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            search: {
                type: 'video'
            },
            label: 'hot-videos',
        },
        success: function (response) {
            var obj = {
                hot_videos: response.data
            };
        }
    });

    $.when(hot_videos).done(function (hot_videos) {
        let obj = [];
        obj['hot_videos'] = hot_videos.data;

        //console.log(obj);

        var html = $.get('/cn/module/hot_video.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#hot_video_area').html(str);
        });
    });
}

function featured_news_banner($category_id = 0, $sub_category_id = 0, $limit = 4) {
    $('#featured_news').html("");

    var variable = "id";
    var id = getQueryString(variable);
    var filter = [{field: "article.hot", value: 1, operator: "="}];

    if ($category_id) {
        filter.push({field: "article.category_id", value: $category_id, operator: "="});
    }

    if ($sub_category_id) {
        filter.push({field: "article.sub_category_id", value: $sub_category_id, operator: "="});
    }

    var featured_news_banner = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            limit: $limit,
            filter: filter,
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: 'featured-news-banner',
        },
        success: function (response) {
            var obj = {
                latest_news: response.data
            };
        }
    });

    $.when(featured_news_banner).done(function (featured_news_banner) {
        let obj = [];
        obj['main'] = [];
        if (featured_news_banner.data.length > 0) {
            obj['main'] = featured_news_banner.data[0];
        }

        obj['sub'] = [];
        if ($limit > 1) {

            if (featured_news_banner.data.length > 1) {
                obj['sub'].push(featured_news_banner.data[1]);
            }

            if (featured_news_banner.data.length > 2) {
                obj['sub'].push(featured_news_banner.data[2]);
            }

            if (featured_news_banner.data.length > 3) {
                obj['sub'].push(featured_news_banner.data[3]);
            }

        }

        //console.log(obj['main']);

        var html = $.get('/cn/module/featured_news_banner.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#featured_news_banner').html(str);
        });
    });
}
