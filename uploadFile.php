
<?php
require_once 'Excel/reader.php';
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
$data->read('timecards/time.xls');
error_reporting(E_ALL ^ E_NOTICE);

function conv($str){
	switch ($str){
		case 'RD':
			return 'RESTDAY';
		case 'SL':
			return 'SICK LEAVE';
		case 'FV':
			return 'FIELD VISIT';
		case 'EL':
			return 'EMERGENCY LEAVE';
		case 'VL':
			return 'VACATION LEAVE';
		case 'SLWOP':
			return 'LWOP';
		case 'LWOP':
			return 'LWOP';
		case 'SUS':
			return 'SUS';
		case 'ACTUAL':
			return 'REGULAR';
		case 'BL':
			return 'BIRTHDAY LEAVE';
		case 'PL':
			return 'PATERNITY LEAVE';
		case 'ML':
			return 'MATERNITY LEAVE';
		case 'BRL':
			return 'BEREAVEMENT LEAVE';
		case '':
			return 'REGULAR';
		default:
			return 'REGULAR';
		}
	}

$sheet = 0;
$cntx = 0;
for ($i = 1; $i <= $data->sheets[$sheet]['numRows']; $i++) {
	$idx = $data->sheets[$sheet]['cells'][$i][1];
	
	
	
	$mdy = $data->sheets[$sheet]['cells'][$i][2];
	$ymd = '20'.$mdy[6].$mdy[7]."-".$mdy[0].$mdy[1]."-".$mdy[3].$mdy[4];

	$timeStamp = strtotime($ymd);
	$timeStamp += 24 * 60 * 60 * -1;
	$newDate = date("Y-m-d", $timeStamp);


	$status = conv($data->sheets[$sheet]['cells'][$i][3]);
	$shift = str_replace(":","",$data->sheets[$sheet]['cells'][$i][4]);
	$in = $data->sheets[$sheet]['cells'][$i][6];
	$out = $data->sheets[$sheet]['cells'][$i][7];
	$remarks = addslashes($data->sheets[$sheet]['cells'][$i][15]);
	$selectz = "select `em_id` from employee where em_id = '" . $idx . "'";
	$resultz = mysql_query($selectz, connect());
	$rowz = mysql_fetch_array($resultz,MYSQL_ASSOC);
	if($rowz['em_id']){
		$update = "update transaction set trxn_time_in = '" . $in . "', trxn_time_out = '" . $out . "', shift_code = '" . $shift . "', status = '" . $status . "', otremarks = '" . $remarks . "' where em_id = '" . $idx . "' and trxn_date = '" . $newDate . "' and posted_id = 0    ";
		mysql_query($update, connect());
		$cntx++;
		}
	else{
		echo "No Employee info for " .  $idx . "<br>";
		}
	}
	
echo "successfully updated " . $cntx . " transaction";
?>
