<?php
 	require 'assets/php/phpSpreadsheet/vendor/autoload.php';
	require 'assets/php/symfony/vendor/autoload.php';
	include_once("config/config.php");
	error_reporting(E_ERROR | E_PARSE);
	//header('Content-Type: text/html; charset=utf-8');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use Symfony\Component\HttpFoundation\StreamedResponse;
	use Symfony\Component\HttpFoundation\Response;

	
	$curr_uri = "$_SERVER[REQUEST_URI]";
	//echo $curr_uri ;
	//$param=substr($curr_uri, strpos($curr_uri, "?") + 1);    
	$param=explode('param=', $curr_uri , 2)[1];  
	
	//echo $param;
	
	$type=$_GET['type'];
	if ($type=='match')
		$api='event';
		
	else if ($type=='member_history')
		$api='prediction';
	
	//$url = 'http://test.19com.backend2:5280/api/cn/'.$api.'?'.$param;
	$url = CURL_API_URL . '/api/cn/'.$api.'?'.$param;
	//echo $url ;
	//echo $param ;

	//echo $url ;
	$query_string = "";
//	foreach($POST as $key => $value){
//		if($key == 'token') continue;
//		if(!$query_string){
//			$query_string = "?$key = $value";
//		}else{
//			$query_string = "&$key = $value";
//		}
//	}

	//$headers = array();
	//$headers[] = "Authorization: " . $_POST['token'];
	//$headers = "Authorization: ".$_GET['token'];
	
	$headers = array(
		"Authorization:".$_GET['token']
	);

	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url.$query_string);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_USERAGENT, "Export CSV");
	$response = curl_exec($ch);
	curl_close($ch);

	$json_data = json_decode($response, true);
	//print_r ($response );
	//print_r ($json_data );
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	//$allowed_keys = array('handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'handicap_win', 'over_under_win', 'single_win', 'win_amount');
	$allowed_keys = array('id', 'created_at', 'match_at', 'league_data.name_zh', 'category_data.display', 'home_team_data.name_zh', 'over_under_away_bet', 'over_under_away_bet', 'over_under_away_bet');
	
	
	//insert data into array
	
	$data_arr = array();
	
	//array_push($data_arr,"blue","yellow");
	//array_push($data_arr,"blue2","yellow2");
	
	
	//print_r($data_arr);
	
	$count=0;
	
	if ($type=='match'){
		foreach($json_data['data'] as $data){
			/*print_r ($data);
			echo '<br><BR>--><BR>';
			echo $data['home_team_id'];echo '<br><BR>--><BR>';*/
			
			if (!$data['result_data'])
				continue;
			
			$temp_handicap = $data['result_data']['handicap_odds'] + '/' + $data['result_data']['handicap_bet']; //exp: -0.5/1.00
			$temp_over_under = $data['result_data']['over_under_odds'] + '/' + $data['result_data']['over_under_bet']; //exp: 大2.5/2.00
		   
			
			if ($data['result_data']['handicap_home']== 1) {
				$home_handicap = $temp_handicap;
			} else if ($data['result_data']['handicap_away'] == 1) {
				$away_handicap = $temp_handicap;
			}

			if ($data['result_data']['over_under_home'] == 1) {
				$home_over_under = $temp_over_under;
			} else if ($data['result_data']['over_under_away'] == 1) {
				$away_over_under = $temp_over_under;
			}

			if ($data['result_data']['single_home'] == 1) {
				$single_result = '主';
			} else if ($data['result_data']['single_away'] == 1) {
				$single_result = '客';
			}
			
			 $editor_note =  strip_tags($data['editor_note']);
			 
			// element.find('img').remove();
			
			if ($data['prediction_count'])
				$prediction_count=$data['prediction_count'];
			else
				$prediction_count=0;
			
			if ($data['prediction_win_count'])
				$prediction_win_count=$data['prediction_win_count'];
			else
				$prediction_win_count='-';
			
			if ($data['bonus_distribution'])
				$bonus_distribution=$data['bonus_distribution'];
			else
				$bonus_distribution='-';
			
			if ($data['comment_count'])
				$comment_count=$data['comment_count'];
			else
				$comment_count='-';
			
			$data_arr[$count]=  array (
			  'id' => $data['id'],
			  'created_at' => $data['created_at'],
			  'match_at' => $data['match_at'],
			  'prediction_end_at' => $data['prediction_end_at'],
			  'league_name' => $data['league_data']['name_zh'].$data['round'],
			  'category' => $data['category_data']['display'],
			  
			  'home_team' => $data['home_team_data']['name_zh'],
			  'home_team_handicap' => $home_handicap,
			  'home_team_over_under' => $home_over_under,
			  
			  'away_team' => $data['away_team_data']['name_zh'],
			  'away_team_handicap' => $away_handicap,
			  'away_team_over_under' => $away_over_under,
			  
			  'single_result' => $single_result,
			  'editor_note' => $editor_note,
			  'prediction_count' => $prediction_count,
			  'prediction_win_count' => $prediction_win_count,
			  'bonus_distribution' => $bonus_distribution,
			  'comment_count' => $comment_count,
			  
			 
			 
			  
			 
			);
			$count++;
		}
		
	}
	
	else if ($type=='member_history'){
		foreach($json_data['data'] as $data){
			/*print_r ($data);
			echo '<br><BR>--><BR>';
			echo $data['home_team_id'];echo '<br><BR>--><BR>';*/
			
			
			$handicap = '';
			$over_under = '';
			$single = '';

			//Handicap	
			if ($data['handicap_home'] == 0 && $data['handicap_away']== 0) //if no bet
				$handicap = '-';
			else if ($data['handicap_win']==1) //if win
				$handicap = 'O';
			else if ($data['handicap_win']==0) //if lose
				$handicap = 'X';
				
			//over under
			if ($data['over_under_home']  == 0 && $data['over_under_away'] == 0) //if no bet
				$over_under = '-';
			else if ($data['over_under_win']==1) //if win
				$over_under = 'O';
			else if ($data['over_under_win']==0) //if lose
				$over_under = 'X';
			
			//single
			if ($data['single_home'] == 0 && $data['single_away'] == 0 && $data['single_tie'] == 0) //if no bet
				$single = '-';
			else if ($data['single_win']==1) //if win
				$single = 'O';
			else if ($data['single_win']==0) //if lose
				$single = 'X';
			
			if ($data['category_data'])
				$category_data=$data['category_data']['display'];
			else
				$category_data='';
		
			if ($data['league_data'] && $data['event_data'])
				$league_data=$data['league_data']['name_zh'].' '.$data['event_data']['round']; 
			else
				$league_data=='';
			
			$data_arr[$count]=  array (
				'id' => $data['id'],
				'category' => $category_data,
				'league_data' => $league_data,
				'username' => $data['user_data']['username'],
				'created_at' => $data['created_at'],
				'handicap' => $handicap,
				'over_under' => $over_under,
				'single' => $single,
				'win_amount' => $data['win_amount']
			);
			$count++;
		}
		
	}
	//print_r ($data_arr);
	
	if ($type=='match'){
		$sheet->setCellValue('A1', '编号');
		$sheet->setCellValue('B1', '创建日期时间');
		$sheet->setCellValue('C1', '比赛日期时间');
		$sheet->setCellValue('D1', '预测结束时间');
		$sheet->setCellValue('E1', '联赛名称');
		$sheet->setCellValue('F1', '体育类别');
		$sheet->setCellValue('G1', '主队伍');
		$sheet->setCellValue('H1', '主队伍让球');
		$sheet->setCellValue('I1', '主队伍大小');
		
		$sheet->setCellValue('J1', '客队伍');
		$sheet->setCellValue('K1', '客队伍让球');
		$sheet->setCellValue('L1', '客队伍大小');
		
		$sheet->setCellValue('M1', '独赢');
		$sheet->setCellValue('N1', '预测简介');
		$sheet->setCellValue('O1', '预测人数');
		$sheet->setCellValue('P1', '获胜人数');
		$sheet->setCellValue('Q1', '总积分派发');
		$sheet->setCellValue('R1', '留言总数');
	}
	else if ($type=='member_history'){
		$sheet->setCellValue('A1', '编号');
		$sheet->setCellValue('B1', '体育类别');
		$sheet->setCellValue('C1', '联赛名称');
		$sheet->setCellValue('D1', '会员账号');
		$sheet->setCellValue('E1', '预测创建时间');
		$sheet->setCellValue('F1', '让球');
		$sheet->setCellValue('G1', '大小');
		$sheet->setCellValue('H1', '独赢');
		$sheet->setCellValue('I1', '获得战数');
	}
	
	foreach(range('A','R') as $columnID) {
		$sheet->getColumnDimension($columnID)->setAutoSize(true);
	}
	$sheet->getColumnDimension('N')->setAutoSize(false);
	$sheet->getColumnDimension('N')->setWidth(100);
	//$header=array('編號','创建日期时间','比赛日期时间','联赛名称','体育类别','主队伍','主队伍让球','主队伍大小','主队伍独赢','客队伍','客队伍让球','客队伍大小','客队伍独赢');
	$row = 2;
	foreach($data_arr as $data){
		$alphabet = 'A';
		
		foreach($data as $key => $value){
			
			//echo "$alphabet.$row -》 $key -> $value <br> ";
			$sheet->setCellValue($alphabet.$row, $value);
			//$sheet->setCellValue('I1', '赛事结果-独赢');
			$alphabet++;
			
		}
		$row++;
		//print_r($data);
	}
	
	if ($type=='match')
		$filename='赛事历史.xlsx';
	else if ($type=='member_history')
		$filename='会员历史.xlsx';
		
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=$filename");
	header('Cache-Control: max-age=0');

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');


?>