// template.config("escape", false);

function article_detail() {
    $('#article_detail').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 11;
    }

    var article_detail = $.ajax({
        url: link + 'api/cn/article',
        type: 'get',
        data: {
            id: id
        },
        success: function (response) {
            var obj = {
                article_detail: response.data
            };
        }
    });

    $.when(article_detail).done(function (article_detail) {
        let obj = [];
        obj['article_detail'] = article_detail.data;

        //console.log(obj);
        window.message_chatroom_id = article_detail.data.chatroom_id
        var html = $.get('/cn/module/article_detail.html', function (data) {
            var render = template.compile(data);
            var str = render(obj);

            $('#article_detail').html(str);

            var html_content = '';
            if (article_detail.data.content) {
                article_detail.data.content = article_detail.data.content.replace(/\\"/g, '"');
                var element = $(`<div>${article_detail.data.content}</div>`);
                element.find('a').each(function () {
                    const allowedTypes = ['avi', 'flv', 'mov', 'mp4', 'mpeg']
                    const href = $(this).attr('href') || ''
                    const videoData = href.split('.')
                    const type = videoData[videoData.length - 1]
                    const isAllowed = !!_.find(allowedTypes, t => t === type)
                    if (!type || !isAllowed)
                        return
                    $(this).replaceWith(`<div style="position: relative; height: 0; padding-bottom: 50%;"><iframe src="${href}" style="position: absolute; width: 640px; height: 360px; top: 0; frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe></div>`)
                });
                html_content = element.html()
            }
            $('#div_content').html(html_content);

        });

    });

    return article_detail;
}
