<?php
do {

    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'id', 'label' => 'Id', 'required' => true);

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    $news = Collection::getNews($dbc, (!empty($cv['limit']) ? $cv['limit'] : 6));

    foreach ($news as $news_item) {
        $item = array();
        $item['id'] = $news_item->getId();
        $item['date'] = $news_item->getDate();
        $item['title'] = $news_item->getTitle();
        $item['tags'] = $news_item->getTagsObject();
        $item['image'] = $news_item->getImage();
        if (isset($cv['detail']) && $cv['detail']) {
            $item['content'] = $news_item->getContent();
        }

        $response['news'][] = $item;
    }

} while (0);
