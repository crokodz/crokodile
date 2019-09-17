
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("ot_" . $ymd . ".xls");


$column_header1 = array('<b>RADIO MINDANAO NETWORK INC.</b>');
$column_header2 = array('<b>OVERTIME SUMMARY</b>');
$column_header = array('<b>FOR THE MONTH OF' . $_GET['payday'].'</b>');

function getsss($salary,$cnfsss){
	$select = "select id, ssee, sser, ec from sss where `ssee` >= '" . $salary . "' order by `ssee` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
	}

function getph($salary,$cnfph){
	if($cnfph == 'YES'){
		$select = "select `id`,`ees`,`ers` from ph where `from` <= '" . $salary . "' order by `from` desc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ees'],$row['ers']);
		}
	else{
		return array('0','0','0');
		}
	}

function getEC($y,$pid,$em_id){
	if($y==1){
		$select = "select `payday`, `sss` from `posted_summary` where `posted_id` = '" . $pid . "' and `em_id` = '" . $em_id . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$s = explode("@",$row['payday']);
		$ec = getsss($row['sss']);

		if($s[0] == 'w1'){
			return array($ec[0],$ec[1]);
			}
		else{
			$pastpayid = 'w1@' . $s[1];
			$select1 = "select `payday`, `sss` from `posted_summary` where `payday` = '" . $pastpayid . "' and `em_id` = '" . $em_id . "'";
			$result1 = mysql_query($select1, connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
			$ec1 = getsss($row1['sss']+$row['sss']);
			$ec2 = getsss($row1['sss']);
			return array($ec1[0] - $ec2[0], $ec1[1] - $ec2[1]);
			}
		}
	}

function getpi($salary,$cnfpi,$pdm){
	$pdm = $pdm * 2;
	if($cnfpi == 'YES'){
		if ($salary <= 1500){
			return array($pdm+100,100);
			}
		else{
			if ($pdm > 0){
				return array($pdm+100,100);
			} else {
				return array(100,100);
			}

			}
		}
	else{
		if ($pdm > 0){
			return array(0,0);
		} else {
			return array(0,0);
		}
	}
}

$excel->writeLine($column_header1);
$excel->writeLine($column_header);
$company_id = 1;
$company_name = 'RMN INC';



$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');
$company_id = $_GET['compa'];
$company_name = $company_array[$company_id];
$excel->writeLine(array($company_name));

$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

$excel->writeLine(array('DEPARTMENT', 'OT HOURS', 'AMOUNT'));

$select = "select
	t2.department,
 	sum(ot) as ot,
 	GROUP_CONCAT(posted_id) as posted_id,
 	sum((t1.ot / t1.permin_salary) / 60) as otx
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and ot > 0 group by t2.department";
$result = mysql_query($select, connect());

$total = 0;
$hours = 0;

while($row1 = mysql_fetch_array($result)){
	// $select2 = "select permin_salary, salary from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.posted_id in (" . $row1['posted_id'] . ") and t2.department = '" . $row1['department'] . "'";
	// $result2 = mysql_query($select2, connect());
	// $row2 = mysql_fetch_array($result2,MYSQL_ASSOC);




	$excel->writeLine(array($row1['department'], roundoff($row1['otx']), roundoff($row1['ot'])));
	$total += $row1['ot'];
	$hours += $row1['otx'];
}
$excel->writeLine(array('TOTAL', roundoff($hours),roundoff($total)));


$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename='.'ot_' . $ymd . '.xls');
header("Location: " ."ot_" . $ymd . ".xls");
exit;

?>
