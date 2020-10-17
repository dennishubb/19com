<?php
do {

    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'id', 'label' => 'id');
    $fields[] = array('index' => 'detail', 'label' => 'detail');
    $fields[] = array('index' => 'limit', 'label' => 'limit');

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    $sports = Collection::getSports($dbc, $cv['id'], (!empty($cv['limit']) ? $cv['limit'] : null));

    foreach ($sports as $sport) {
        $item = array();
        $item['id'] = $sport->getId();
        $item['rank'] = $sport->getRank();
        $item['is_visible'] = $sport->getIsVisible();
        $item['category'] = $sport->getCategory()->getTitle();
        $item['title'] = $sport->getTitle();
        $item['image'] = $sport->getImage();
        $item['url'] = $sport->getContentUrl();
        $item['date'] = date("Y-m-d", strtotime($sport->getCreatedAt()));
        if (isset($cv['detail']) && $cv['detail']) {
            $item['content'] = $sport->getContent();
        }

        $response['sports'][] = $item;
    }

} while (0);
