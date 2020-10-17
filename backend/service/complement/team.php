<?php
	include __DIR__.'/../common/Medoo.php';
	include __DIR__.'/../common/function.php';
	include __DIR__.'/../../cron/sports/include/config.php';

	$team_pic_domain = 'http://cdn.sportnanoapi.com/football/team/';
	$url_preg = "/^http(s)?:\\/\\/.+/";

	$table = 'team';

	$matchevent_id = $argv[1]; //19com id not leisu id

	$last_season_id = 0;

	if (isset($argv[2])) {
		$last_season_id = $argv[2];
	}
	else {
		$last_season_id = $database->get('season_list', 'season_id', ['league_id' => $matchevent_id, 'ORDER' => ['season_id' => 'DESC']]);
	}

	$url = "{$api_urls['soccer_season_detail']}?user={$user}&secret={$secret}&id={$last_season_id}";

	$response = httpGet($url);
	$result = json_decode($response, true);

	if (isset($result['teams'])) {
		// var_dump($result['competition']);
		// var_dump($result['teams']);exit();
		$database->delete($table, ['league_id' => $matchevent_id]);

		foreach ($result['teams'] as $key => $value) {
			$data['api_id'] = $value['id'];
			$data['league_id'] = $matchevent_id;
			$data['category_id'] = 1;
			$data['name_zh'] = $value['name_zh'];
			$data['name_zht'] = $value['name_zht'];
			$data['name_en'] = $value['name_en'];

			if (preg_match($url_preg, $value['logo']))
	    		$data['logo'] = $value['logo'];
	    	else
	    		$data['logo'] = $team_pic_domain . $value['logo'];

	    	$data['created_at'] = date('Y-m-d H:i:s');
	    	$data['updated_at'] = date('Y-m-d H:i:s');

	    	$database->insert($table, $data);
		}
	}
?>