<?php
do {

    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'id', 'label' => 'id', 'required' => true);

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    $news = new cn_news($dbc);
    if (!$news->load($cv['id'])) {
        responseFail($error, "不存在。");
        break;
    }

    $item = array();
    $item['id'] = $news->getId();
    $item['rank'] = $news->getRank();
    $item['is_visible'] = $news->getIsVisible();
    $item['date'] = $news->getDate();
    $item['title'] = $news->getTitle();
    $item['content'] = $news->getContent();
    $item['tags'] = $news->getTagsObject();
    $item['image'] = $news->getImage();

    $response['news'] = $item;


} while (0);



