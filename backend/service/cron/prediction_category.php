<?php
	include __DIR__.'/../common/Medoo.php';
	include __DIR__.'/../common/function.php';


    $current_time = date('Y-m-d H:i');
    $last_min = date('Y-m-d H:i', strtotime($current_time) - 60);

    $datas = $database->select('prediction', ['event_id'], ['updated_at[>=]' => $last_min, 'updated_at[<]' => $current_time]);

    foreach ($datas as $key => $value) {
    	$event_info = $database->get('event', ['id', 'category_id', 'league_id', 'match_at'], ['id' => $value['event_id']]);
    	$database->update('prediction', ['match_at' => $event_info['match_at'], 'category_id' => $event_info['category_id'], 'league_id' => $event_info['league_id']],['event_id' => $event_info['id']]);
    }
?>