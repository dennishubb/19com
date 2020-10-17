<?php
do {

    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'id', 'label' => 'id');
    $fields[] = array('index' => 'detail', 'label' => 'detail');

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    $footballs = Collection::getFootballs($dbc);

    foreach ($footballs as $football) {
        $item = array();
        $item['id'] = $football->getId();
        $item['short_name'] = $football->getShortNameZh();

        if (isset($cv['detail']) && $cv['detail']) {
            $item['name'] = $football->getNameZh();
        }

        $response['footballs'][] = $item;
    }

} while (0);
