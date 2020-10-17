function dynamic_video_category($limit = 6) {
    $('#dynamic_video_category').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 1;
    } else {
        id = $category_id;
    }

    var categoriesArticle = {
        categories: [],
        articles: []
    }

    getCategories(0)
        .then(function (categories) {
            //console.log("-");
            //console.log(categories);
            categoriesArticle.categories = categories;
            return categories;
        })
        .then(categories => {
            //console.log("- -");

            $.each(categories, function (index, item) {
                //console.log(item);

                categoriesArticle.articles[item.id] = {
                    id: item.id,
                    display: item.display,
                    items: []
                };
            });

            return getArticles(
                categories.map(category => category.id), $limit
            );
        })
        .then(articles => {
            //console.log("- - -");

            $.each(articles, function (index, item) {
                //console.log(item);
                if (item) {
                    categoriesArticle.articles[item.article_data.category_id].items.push(item);
                }
            });

            let obj = [];
            obj['category'] = [];
            $.each(categoriesArticle.articles, function (index, item) {
                if (item && item.items.length > 0) {
                    //console.log(item);
                    obj['category'].push(item);
                }
            });

            // obj['sub_category'] = subCategoriesArticle.articles;

            //console.log(obj);

            var html = $.get('/cn/module/dynamic_video_category.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#dynamic_video_category').html(str);
            });

            return articles;
        });

}

function getCategories($parent_id, parentIds = ["0"]) {
    if (!parentIds) parentIds = [];

    const promises = parentIds.map(
        parentId => $.ajax({
            url: link + 'api/cn/category',
            type: 'get',
            data: {
                filter: [
                    {
                        field: 'category.parent_id',
                        value: 0,
                        operator: '=',
                    },
                    {
                        field: 'category.type',
                        value: 'sport',
                        operator: '=',
                    }
                ],
            }
        }).then(response => response.data)
    );

    return Promise.all(promises).then(promiseResponses => {
        let categories = []
        promiseResponses.forEach(promiseResponse => {
            categories = [...categories, ...promiseResponse]
        })
        return categories
    });
}

function getArticles($category_ids, $limit = 5) {
    const promises = $category_ids.map(
        parentId => $.ajax({
            url: link + 'api/cn/upload',
            type: 'get',
            data: {
                limit: $limit,
                filter: [
                    {
                        field: 'article.category_id',
                        value: parentId,
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
                label: "getArticles"
            }
        }).then(response => response.data)
    );

    return Promise.all(promises).then(promiseResponses => {
        let articles = []

        //console.log(promiseResponses);

        promiseResponses.forEach(promiseResponse => {
            articles = [...articles, ...promiseResponse]
        })
        return articles;
    });
}
