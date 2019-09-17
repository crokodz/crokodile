
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$filename = date('ymdhis').".xls";
$excel=new ExcelWriter($filename);

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


if($_GET['idd']){
	$paysel  = "";
	$paysel1  = "";
	$pcd = explode("@@",$_GET['idd']);
	for($xy=0;$xy<count($pcd);$xy++){
		if($pcd[$xy]){
			if($xy==count($pcd)-2){
				$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' ";
				$paysel1 = $paysel1 . " employee.`pay_id` = '" . $pcd[$xy] . "' OR employee.pay_id_sub = '" . $pcd[$xy] . "'  ";
				}
			else{
				$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' OR ";
				$paysel1 = $paysel1 . " employee.`pay_id` = '" . $pcd[$xy] . "'  OR employee.pay_id_sub = '" . $pcd[$xy] . "' OR ";
				}
			}
		}
	$paysel = " and (".$paysel.") ";
	$paysel1 = " and (".$paysel1.") ";
	$idd = $paysel;
	#$idd= " and employee.pay_id = '" . $_GET['idd'] . "' ";
	}

$from_date = $_GET['from'];
$to_date = $_GET['to'];

if($_GET['idd']){
	$_GET['idd']= " and employee.pay_id = '" . $_GET['idd'] . "' ";
	}

$x = 0;
$x = 0;
if($_SESSION['user'] == 'mso'){
	if($_GET['idd'] == "ALL MSO"){
		$select = "select employee.em_id, employee.name from transaction left join employee using(`em_id`) left join pay on(employee.`pay_id` =  pay.`name`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  and pay.`group` = 'mso' group by em_id order by employee.em_id asc";
		}
	else{
		$select = "select em_id, name from transaction left join employee using(`em_id`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  " . $idd . " group by em_id order by employee.em_id asc";
		}
	}
else{
	#$idd= " and (employee.pay_id = '" . $_GET['idd'] . "' or employee.pay_id_sub = '" . $_GET['idd'] . "' ) ";
	$idd = $paysel1;
	$select = "select em_id, name from transaction left join employee using(`em_id`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  " . $idd . " group by em_id order by employee.em_id asc";
	}

$result_all = mysql_query($select, connect());
while ($row_all = mysql_fetch_array($result_all,MYSQL_ASSOC)){
	$txt=array('');
	$excel->writeLine($txt);
	
	//~ $txt=array($row_all['name']);
	//~ $excel->writeLine($txt);
	//~ $txt=array($row_all['em_id']);
	//~ $excel->writeLine($txt);

	//~ $column_header = array('Name', 'ID', 'Day','Status','Date','Shift Code','Time In','Time Out','Total','Late','UT','Start OT','End OT','OT','OTX','ND','REMARKS');
	//~ $excel->writeLine($column_header);

	$select = "select * from transaction where trxn_date between '" . $from_date . "' and '" . $to_date . "' and em_id = '" . $row_all['em_id'] . "' order by `trxn_date` asc, `trxn_time_in` asc ";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$a = split("-" , $row['trxn_date']);
		$dayoftheweek = date("D", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
		
		$rowx=array($row_all['name'], $row_all['em_id'], $dayoftheweek,$row['status'],$row['trxn_date'],$row['shift_code'],$row['trxn_time_in'],$row['trxn_time_out'],m2h($row['total']),m2h($row['late']),m2h($row['ut']),m2h($row['ot_in']),m2h($row['ot_out']),m2h($row['ot']),m2h($row['otx']),m2h($row['nd']),$row['otremarks']);
		$excel->writeLine($rowx);
		}
	}
	
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=' . $filename);
header("Location: " . $filename);
exit;
?>