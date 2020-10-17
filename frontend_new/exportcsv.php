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
	
	$param=substr($curr_uri, strpos($curr_uri, "?") + 1);    
	
	//echo $param;
	
	$url = 'http://test.19com.backend:5280/api/cn/event?'.$param;
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

	$headers = array();
//	$headers[] = "Authorization: " . $_POST['token'];
	$headers[] = "Authorization: abc";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url.$query_string);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Export CSV");
	$response = curl_exec($ch);
	curl_close($ch);

	$json_data = json_decode($response, true);

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
	foreach($json_data['data'] as $data){
		/*print_r ($data);
		echo '<br><BR>--><BR>';
		echo $data['home_team_id'];echo '<br><BR>--><BR>';*/
		
		
		
		//HANDICAP让球
		$home_handicap=$data['handicap_home_bet'].'/'.$data['handicap_home_odds'];
		$away_handicap=$data['handicap_away_bet'].'/'.$data['handicap_away_odds'];
		
		//OVER UNDER大小
		//home_over_under=data[index].over_under_home_bet+'/'+data[index].over_under_home_odds;
		//away_over_under=data[index].over_under_away_bet+'/'+data[index].over_under_away_odds;
		$home_over_under=$data['over_under_home_bet'].'/'.$data['over_under_home_odds'];
		$away_over_under=$data['over_under_away_bet'].'/'.$data['over_under_away_odds'];		
		
		
			
		$zhu_single='主 - '.$data['single_home'];
		$ke_single='客 - '.$data['single_away'];
		$he_single='';
		
		if ($data['category_data']['display']!='篮球'){
			$he_single='和 - '.$data['single_tie'];

		}
		
		$data_arr[$count]=  array (
		  'id' => $data['id'],
		  'created_at' => $data['created_at'],
		  'match_at' => $data['match_at'],
		  'league_name' => $data['league_data']['name_zh'],
		  'category' => $data['category_data']['display'],
		  
		  'home_team' => $data['home_team_data']['name_zh'],
		  'home_team_handicap' => $home_handicap,
		  'home_team_over_under' => $home_over_under,
		  
		  'away_team' => $data['away_team_data']['name_zh'],
		  'away_team_handicap' => $away_handicap,
		  'away_team_over_under' => $away_over_under,
		 
 		  'zhu_single' => $zhu_single,
		  'he_single' => $he_single,
		  'ke_single' => $ke_single,
		 
		);
		$count++;
	}
	
	//print_r ($data_arr);
	
	
	$sheet->setCellValue('A1', '编号');
	$sheet->setCellValue('B1', '创建日期时间');
	$sheet->setCellValue('C1', '比赛日期时间');
	$sheet->setCellValue('D1', '联赛名称');
	$sheet->setCellValue('E1', '体育类别');
	$sheet->setCellValue('F1', '主队伍');
	$sheet->setCellValue('G1', '主队伍让球');
	$sheet->setCellValue('H1', '主队伍大小');
	
	$sheet->setCellValue('I1', '客队伍');
	$sheet->setCellValue('J1', '客队伍让球');
	$sheet->setCellValue('K1', '客队伍大小');
	
	$sheet->setCellValue('L1', '独赢- 主');
	$sheet->setCellValue('M1', '独赢- 和');
	$sheet->setCellValue('N1', '独赢- 客');
	
	foreach(range('A','N') as $columnID) {
		$sheet->getColumnDimension($columnID)->setAutoSize(true);
	}
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
	
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="预测列表.xlsx"');
	header('Cache-Control: max-age=0');

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');


?>