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

    $sport = new cn_sports($dbc);
    if (!$sport->load($cv['id'])) {
        responseFail($error, "不存在。");
        break;
    }

    $item = array();
    $item['id'] = $sport->getId();
    $item['rank'] = $sport->getRank();
    $item['category'] = $sport->getCategory()->getTitle();
    $item['title'] = $sport->getTitle();
    $item['image'] = $sport->getImage();
    $item['url'] = $sport->getContentUrl();
    $item['date'] = date("Y-m-d", strtotime($sport->getCreatedAt()));
    $item['content'] = $sport->getContent();

    $response['sport'] = $item;


} while (0);



