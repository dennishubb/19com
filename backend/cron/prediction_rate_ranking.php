<?php
	//script to generate prediction ranking
	//runs everyday 5pm
	include(__DIR__.'/../include/shared_function.inc.php');
	include(__DIR__.'/../config/app.config.inc.php');
	include(__DIR__.'/../config/config.'.SERVER_STATE.'.inc.php');
	include(__DIR__.'/../include/db.inc.php');
	include(__DIR__.'/../model/prediction_rate.php');
	include(__DIR__.'/../model/category.php');
	include(__DIR__.'/../model/top_ten.php');

	$top_ten 	= new top_ten();
	$date		= date('Y-m-d H:i:s');
	$this_month	= date('n');
	$this_year	= date('Y');

	$prediction_rate	= new prediction_rate();
	$league_ids			= $prediction_rate->where('month', $this_month)->where('year', $this_year)->groupBy('league_id')->getValue('league_id', null);
	$types				= $prediction_rate->where('month', $this_month)->where('year', $this_year)->groupBy('type')->getValue('type', null);

	foreach($league_ids as $league_id){
		foreach($types as $type){
			$prediction_rate_results = $prediction_rate->where('league_id', $league_id)->where('type', $type)->where('month', $this_month)->where('year', $this_year)->orderBy('rate', 'desc')->orderBy('season_rate', 'desc')->orderBy('total_points', 'desc')->get();

			$count = 0;
			$rank  = 1;
			$previous_points 		= 0;
			$previous_rate	 		= 0;
			$previous_season_rate	= 0;
			foreach($prediction_rate_results as $prediction_rateObj){
				
				$count++;
				if($prediction_rateObj->rate == $previous_rate && $prediction_rateObj->season_rate == $previous_season_rate && $prediction_rateObj->total_points == $previous_points){
					
				}else{
					$rank = $count;
				}
				
				$prediction_rateObj->updated_at	= $date;
				$prediction_rateObj->rank		= $rank;
				$prediction_rateObj->save();

				$previous_points 		= $prediction_rateObj->total_points;
				$previous_rate			= $prediction_rateObj->rate;
				$previous_season_rate	= $prediction_rateObj->season_rate;
			}
		}
	}
?>