<?php
	//script to generate top 10 predictors
	/*rules
	System will automatically generate top 10 predictors for each category(like basketball, soccer) based on the users’ success rate, the outcomes will be published before 2nd next Month 12.00pm. the success rate must be greater than 60%. If 2+ users got same success rate, the higher points gained user will be enrolled. If points is also same, all those users will be enrolled to top 10 predictors. 
	15/06/2020 - change rate requirement to 50, add total_count >= 100 
	16/06/2020 - Add year, month and type filter
	03/07/2020 - change to top 5
	07/07/2020 - change to total_count >= 10
	14/07/2020 - add top_ten_rate, set default rate 50%, count 100
	08/09/2020 - add season win rate
	*/

	include(__DIR__.'/../include/shared_function.inc.php');
	include(__DIR__.'/../config/app.config.inc.php');
	include(__DIR__.'/../config/config.'.SERVER_STATE.'.inc.php');
	include(__DIR__.'/../include/db.inc.php');
	include(__DIR__.'/../model/prediction_rate.php');
	include(__DIR__.'/../model/category.php');
	include(__DIR__.'/../model/top_ten.php');
	include(__DIR__.'/../model/top_ten_rate.php');

	$top_ten 	= new top_ten();
	$date		= date('Y-m-d H:i:s');
	$last_month	= date('n', strtotime('last month'));
	$last_year	= date('Y', strtotime('last month'));
	$top_count	= 5;

	$prediction_rate	= new prediction_rate();
	$league_ids			= $prediction_rate->where('month', $last_month)->where('year', $last_year)->where('type', 'total')->groupBy('league_id')->getValue('league_id', null);

	$top_ten_rate		= new top_ten_rate();

	foreach($league_ids as $league_id){
		
		//default rate 50, season rate 50, count 100
		$min_rate			= 50;
		$season_min_rate	= 50;
		$total_count		= 100;
		
		$top_ten_rateObj 	= $top_ten_rate->where('league_id', $league_id)->getOne();
		
		if($top_ten_rateObj){
			$min_rate			= $top_ten_rateObj->min_rate;
			$season_min_rate	= $top_ten_rateObj->season_min_rate;
			$total_count		= $top_ten_rateObj->prediction_count;
		}
		
		$prediction_rate_results = $prediction_rate->where('league_id', $league_id)->where('rate', $min_rate, '>=')->where('season_rate', $season_min_rate, '>=')->where('total_count', $total_count, '>=')->where('type', 'total')->where('month', $last_month)->where('year', $last_year)->orderBy('rate', 'desc')->orderBy('season_rate', 'desc')->orderBy('total_points', 'desc')->get();

		$count 		  	 		= 1;
		$previous_points 		= 0;
		$previous_rate	 		= 0;
		$previous_season_rate	= 0;
		
		if($prediction_rate_results){
			foreach($prediction_rate_results as $prediction_rateObj){

				if($count > $top_count){
					if($prediction_rateObj->rate != $previous_rate || $prediction_rateObj->season_rate != $previous_season_rate || $prediction_rateObj->total_points != $previous_points){
						break;
					}
				}

				$top_ten->isNew 			= true;
				$top_ten->user_id 			= $prediction_rateObj->user_id;
				$top_ten->category_id		= $prediction_rateObj->category_id;
				$top_ten->league_id			= $prediction_rateObj->league_id;
				$top_ten->points			= $prediction_rateObj->total_points;
				$top_ten->prediction_count	= $prediction_rateObj->total_count;
				$top_ten->rank				= $count;
				$top_ten->month				= $last_month;
				$top_ten->year				= $last_year;
				$top_ten->created_at		= $date;
				$top_ten->save();

				$previous_points 		= $prediction_rateObj->total_points;
				$previous_rate			= $prediction_rateObj->rate;
				$previous_season_rate 	= $prediction_rateObj->season_rate;

				$count++;
			}

			//cache the min rate, season min rate and prediction count used for history purpose
			$prediction_rate->updateCustom(array('top_ten_rate' => $min_rate, 'top_ten_season_rate' => $season_min_rate, 'top_ten_prediction_count' => $total_count), array('league_id' => $league_id, "month" => $last_month, "year" => $last_year));
		}
		
	}
	
?>