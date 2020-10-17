<?php
do {
    
    $validator = new Validator;

    // fields info container
    $fields = array();
    $fields[] = array('index' => 'season_id', 'label' => 'season_id', 'required' => true);
    $fields[] = array('index' => 'round', 'label' => 'round');
    $fields[] = array('index' => 'month', 'label' => 'month');
    $fields[] = array('index' => 'limit', 'label' => 'limit');

    $validator->formHandle($fields);
    $problem = $validator->getErrors();
    $cv = $validator->escape_val(); // get the form values

    if ($problem) {
        responseFail($error, $problem);
        break;
    }

    $schedules = Collection::getSchedule($dbc, $cv);
    
    $is_round = false;
    
    $date_grouping = array();

    foreach ($schedules as $schedule) {
        $item = array();
        
        if($schedule->getRound() > 0){
            $is_round = true;
        }
        
        $item['id'] = $schedule->getId();
        $item['match_time'] = $time = date("H:i", $schedule->getMatchTime());
        $item['status_id'] = $schedule->getStatusId();
        $item['round'] = $schedule->getRound();
        
        $home_team = Collection::getTeamById($dbc, $schedule->getHomeTeamId());
        
        $item['home_team_logo'] = $home_team->getLogo();
        $item['home_team_name_en'] = $home_team->getNameEn();
        $item['home_team_name_zh'] = $home_team->getNameZh();
        $item['home_scores'] = $schedule->getHomeScores();
        
        $away_team = Collection::getTeamById($dbc, $schedule->getAwayTeamId());
        
        $item['away_team_logo'] = $away_team->getLogo();
        $item['away_team_name_en'] = $away_team->getNameEn();
        $item['away_team_name_zh'] = $away_team->getNameZh();
        $item['away_scores'] = $schedule->getAwayScores();
        
        $dayOfWeek = getDayOfWeekStringZh(date("N", $schedule->getMatchTime()));
        $month = getMonthStringZh(date("m", $schedule->getMatchTime()));
        $day = date("d", $schedule->getMatchTime());
        
        //$date_grouping["$dayOfWeek $month, $day"][] = $item;
        //08月25日 周日
        $date_grouping[$month.$day.'日 '.$dayOfWeek][] = $item;
    }

} while (0);

$response['schedule'] = $date_grouping;
$response['is_round'] = $is_round;