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

    $football = new cn_season_f($dbc);
    if (!$football->load($cv['id'])) {
        responseFail($error, "不存在。");
        break;
    }

    $item = array();
    $item['id'] = $football->getId();
    $item['short_name'] = $football->getShortNameZh();
    $item['name'] = $football->getNameZh();

    $response['football'] = $item;


} while (0);



