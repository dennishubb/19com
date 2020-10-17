// template.config("escape", false);

function full_video() {
    $('#full_video_area').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 11;
    }

    var full_video = $.ajax({
        url: link + 'api/cn/upload',
        type: 'get',
        data: {
            limit: 1,
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
            label: "full_video"
        },
        success: function (response) {
            var obj = {
                full_video: response.data
            };
        }
    });

    $.when(full_video).done(function (full_video) {
        let obj = [];
        obj['full_video'] = full_video.data[0];
        //console.log(obj);
        var html = $.get('/cn/module/full_video.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#full_video_area').html(str);
        });

    });

    return full_video;
}
