// template.config("escape", false);

function featured_video($limit = 5) {
    $('#featured_video_area').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 11;
    }

    var featured_video = $.ajax({
        url: link + 'api/cn/upload',
        type: 'get',
        data: {
            limit: $limit,
            filter: [
                {
                    field: 'article.popular',
                    value: 1,
                    operator: '=',
                },
            ],
            search: {
                type: "video"
            },
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: "featured_video"
        },
        success: function (response) {
            var obj = {
                featured_video: response.data
            };
        }
    });

    $.when(featured_video).done(function (featured_video) {
        let obj = [];
        obj['featured_video'] = featured_video.data;
        //console.log(obj);
        var html = $.get('/cn/module/featured_video.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#featured_video_area').html(str);
        });

    });

    return featured_video;
}

function featured_video_index($limit = 3) {
    $('#featured_video_index').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 11;
    }

    var featured_video = $.ajax({
        url: link + 'api/cn/upload',
        type: 'get',
        data: {
            limit: $limit,
            filter: [
                {
                    field: 'article.popular',
                    value: 1,
                    operator: '=',
                },
            ],
            search: {
                type: "video"
            },
            sort: {
                field: 'article.active_at',
                sort: 'DESC'
            },
            label: "featured_video"
        },
        success: function (response) {
            var obj = {
                featured_video: response.data
            };
        }
    });

    $.when(featured_video).done(function (featured_video) {
        let obj = [];
        obj['featured_video'] = featured_video.data;
        //console.log(obj);
        var html = $.get('/cn/module/featured_video_index.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#featured_video_index').html(str);
        });

    });

    return featured_video;
}
