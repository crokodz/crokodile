<?php
include "config.php";

require_once 'Excel/reader.php';
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
$data->read('timecards/DTR.xls');
error_reporting(E_ALL ^ E_NOTICE);

$sheet = 0;
$header = 3;

for ($i = 1; $i <= $data->sheets[$sheet]['numRows']; $i++) {
	if($i > $header){
		$id = $data->sheets[$sheet]['cells'][$i][1];
		$fname = $data->sheets[$sheet]['cells'][$i][3];
		$lname = $data->sheets[$sheet]['cells'][$i][2];
		$selectz = "select `em_id` from employee where em_id = '" . $id . "'";
		$resultz = mysql_query($selectz, connect());
		$rowz = mysql_fetch_array($resultz,MYSQL_ASSOC);
		echo $rowz['em_id']
			echo $id;
			echo "<br>";
			}
		//$insert = "insert into employee (`em_id`, `name`, `fname`, `lname`, `salary_based`, `sss`, `pi`, `ph`, `tin`, ) values ()";
		
		}
	}
?>
