
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("timekeeping.xls");

function m2h($mins) { 
	if ($mins < 0) { 
		$min = Abs($mins); 
		} 
	else { 
                $min = $mins; 
		}	 
	$H = Floor($min / 60); 
	$M = ($min - ($H * 60)) / 100; 
	$hours = $H +  $M; 
	
	if ($mins < 0) { 
                $hours = $hours * (-1); 
		} 
	$expl = explode(".", $hours); 
	$H = $expl[0]; 
	if (empty($expl[1])) { 
                $expl[1] = 00; 
		} 
	$M = $expl[1]; 
            if (strlen($M) < 2) { 
                $M = $M . 0; 
		} 
	$hours = $H . ":" . $M; 
	return $hours; 
	} 

if($_GET['filter']==1){
	$sql = "";
	}
elseif($_GET['filter']==2){
	$sql = " and (`trxn_time_out` = '00:00:00' or `trxn_time_in` = '00:00:00' or `trxn_time_out` = `trxn_time_in`) 
		and (`status` = 'REGULAR' or `status` = 'UNFILED')
		";
	}
elseif($_GET['filter']==3){
	$sql = " and (`status` = 'RESTDAY' or `status` = 'UNFILED')
		";
	}	
elseif($_GET['filter']==4){
	
	}

$from = $_GET['from'];
$to = $_GET['to'];
$key = $_GET['keyword'];


$column_header = array('Name','ID','Paycode','Day','Status','Date','Shift Code','Time In','Time Out','Total','Late','UT','Start OT','End OT','OT','OTX','ND','REMARKS');
$excel->writeLine($column_header);




$select = "select * from transaction where trxn_date between '" . $from . "' and '" . $to . "' and em_id = '" . $key . "' " . $sql . " order by `trxn_date` asc, `trxn_time_in` asc ";

$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$a = split("-" , $row['trxn_date']);
	$dayoftheweek = date("D", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
	
	$rowx=array($row['trxnname'], $row['em_id'],$row['pay_id'],$dayoftheweek,$row['status'],$row['trxn_date'],$row['shift_code'],$row['trxn_time_in'],$row['trxn_time_out'],m2h($row['total']),m2h($row['late']),m2h($row['ut']),m2h($row['ot_in']),m2h($row['ot_out']),m2h($row['ot']),m2h($row['otx']),m2h($row['nd']),$row['otremarks']);
	$excel->writeLine($rowx);
	
	$ttotal = $ttotal + $row['total'];
	$tlate = $tlate + $row['late'];
	$tut = $tut + $row['ut'];
	$tot = $tot + $row['ot'];
	$totx = $totx + $row['otx'];
	$tnd = $tnd + $row['nd'];
	}

$rowx=array('','','','','','',m2h($ttotal),m2h($tlate),m2h($tut),'','',m2h($tot),m2h($totx),m2h($tnd),'');
$excel->writeLine($rowx);

header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=timekeeping.xls');
header("Location: timekeeping.xls");
exit;
?>