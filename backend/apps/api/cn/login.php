<?php

    include_once('model/user.php');

    $fields[] = array('index' => 'username', 'label' => 'username', 'required' => true);
    $fields[] = array('index' => 'password', 'label' => 'password', 'required' => true);

    $fields = array_merge($fields, $additional_fields);

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    do{

        $user = new user();
        $date = date("Y-m-d H:i:s");
        
        
        
    } while(0);

?>