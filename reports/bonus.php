
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("bunos_" .$ymd. ".xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
$excel->writeLine($column_header);

$w1 = str_replace("w1", "First Payday of ", $_GET['payday']);
if($w1 == $_GET['payday']){
	$w1 = str_replace("w2", "Second Payday of ", $_GET['payday']);
}

$month = ["01","02","03","04","05","06","07","08","09","10","11","12"];
$monthname = ["January","February","March","April","May","June","July","August","September","October","November","December"];
$month_spacer = ["","","","","","","","","","",""];

$company_id = $_GET['compa'];





function getsss($salary,$cnfsss){
	if($cnfsss == 'YES'){
		$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
		}
	else{
		return array('0','0','0','0');
		}
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
			return $salary * .02;
			}
		else{
			if ($pdm > 0){
				return array($pdm+100,100);
			} else {
				return array(0,0);
			}
			
			}
		}
	else{
		if ($pdm > 0){
			return array($pdm,100);
		} else {
			return array(0,0);
		}
	}
}


$column_header = array($company_name . ' - 13 Month');
$excel->writeLine($column_header);

$column_header = array('For - ' . $_GET['type']);
$excel->writeLine($column_header);

$column_header = array('Year ' . $_GET['year']);
$excel->writeLine($column_header);

$column_header = array('');
$excel->writeLine($column_header);


$column_header = array('ID', 'NAME','SALARY', 'DEPARTMENT','DATE EMPLOYED', 'DATE RERULAR','EMPLOYEE STATUS','PAYCODE','BONUS');
$excel->writeLine($column_header);


$select  = "select em_id, name, salary, department, date_employed, date_permanent, employee_status, pay_id, bonus from employee where status = 'active' and company_id = " . $company_id;
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$excel->writeLine($row);
}






header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=bunos_'.$ymd.'.xls');
header("Location: bunos_".$ymd.".xls");
exit;

?>
