function dynamic_sub_category($category_id = 0, $limit = 5) {
    $('#dynamic_sub_category').html("");

    var variable = "id";
    var id = getQueryString(variable);

    if (!id) {
        id = 1;
    } else {
        id = $category_id;
    }

    var subCategoriesArticle = {
        sub_categories: [],
        articles: []
    }

    getSubCategories(id)
        .then(function (sub_categories) {
            //console.log(sub_categories);
            subCategoriesArticle.sub_categories = sub_categories;
            return sub_categories;
        })
        .then(sub_categories => {
            // //console.log(sub_categories.map(sub_category => sub_category.id));
            // //console.log(sub_categories.map(sub_category => sub_category.name));

            $.each(sub_categories, function (index, item) {
                subCategoriesArticle.articles[item.id] = {
                    id: item.id,
                    display: item.display,
                    items: []
                };
            });

            return getArticles(
                sub_categories.map(sub_category => sub_category.id), $limit
            );
        })
        .then(articles => {

            $.each(articles, function (index, item) {
                if (item) {
                    // //console.log(item);
                    subCategoriesArticle.articles[item.sub_category_id].items.push(item);
                }
            });

            let obj = [];
            obj['sub_category'] = [];
            $.each(subCategoriesArticle.articles, function (index, item) {
                if (item && item.items.length > 0) {
                    //console.log(item);
                    obj['sub_category'].push(item);
                }
            });

            // obj['sub_category'] = subCategoriesArticle.articles;

            //console.log(obj);

            var html = $.get('/cn/module/dynamic_sub_category.html', function (data) {
                var render = template.compile(data);
                var str = render(obj);

                $('#dynamic_sub_category').html(str);
            });

            return articles;
        });

}

function getSubCategories($parent_id, parentIds = ["0"]) {
    if (!parentIds) parentIds = [];

    const promises = parentIds.map(
        parentId => $.ajax({
            url: link + 'api/cn/category',
            type: 'get',
            data: {
                filter: [
                    {
                        field: 'category.parent_id',
                        value: $parent_id,
                        operator: '=',
                    }
                ],
            }
        }).then(response => response.data)
    );

    return Promise.all(promises).then(promiseResponses => {
        let sub_categories = []
        promiseResponses.forEach(promiseResponse => {
            sub_categories = [...sub_categories, ...promiseResponse]
        })
        return sub_categories
    });
}

function getArticles($sub_category_ids, $limit = 5) {
    const promises = $sub_category_ids.map(
        parentId => $.ajax({
            url: link + 'api/cn/article',
            type: 'get',
            data: {
                limit: $limit,
                filter: [
                    {
                        field: 'article.sub_category_id',
                        value: parentId,
                        operator: '=',
                    }
                ],
                sort: {
                    field: 'article.active_at',
                    sort: 'DESC'
                }
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