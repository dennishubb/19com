<?php
 	echo 'Current PHP version: ' . phpversion();
	require 'assets/php/phpSpreadsheet/vendor/autoload.php';
	require 'assets/php/symfony/vendor/autoload.php';
	include_once("config/config.php");
	error_reporting(E_ERROR | E_PARSE);

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use Symfony\Component\HttpFoundation\StreamedResponse;
	use Symfony\Component\HttpFoundation\Response;

	$url = 'http://test.19com.backend:5280/api/cn/prediction';
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

	$allowed_keys = array('handicap_home', 'handicap_away', 'over_under_home', 'over_under_away', 'single_home', 'single_away', 'single_tie', 'handicap_win', 'over_under_win', 'single_win', 'win_amount');

	$row = 1;
	foreach($json_data['data'] as $data){
		
		//print_r($data);
		if($row == 1){
			$alphabet = 'A';
			foreach($data as $key => $value){
				if(is_array($value)) continue;
				if(in_array($key, $allowed_keys)){
					$sheet->setCellValue($alphabet.$row, $key);
					$alphabet++;
				}
			}
			
			$row++;
		}
		
		$alphabet = 'A';
		foreach($data as $key => $value){
			if(is_array($value)) continue;
			if(in_array($key, $allowed_keys)){
				$sheet->setCellValue($alphabet.$row, $value);
				$alphabet++;
			}
		}
		//$sheet->setCellValue("A$row", "hello");
		
		$row++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="GeneratedFile.xlsx"');
	header('Cache-Control: max-age=0');

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');

/*
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Writer\Xls($spreadsheet);

        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="ExportScan.xls"');
        $response->headers->set('Cache-Control','max-age=0');
        return $response;*/

/*$streamedResponse = new StreamedResponse();
$streamedResponse->setCallback(function () {
      $spreadsheet = //create you spreadsheet here;

      $writer =  new Xlsx($spreadsheet);
      $writer->save('php://output');
});

$streamedResponse->setStatusCode(Response::HTTP_OK);
$streamedResponse->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$streamedResponse->headers->set('Content-Disposition', 'attachment; filename="your_file.xlsx"');

return $streamedResponse->send();`*/
?>