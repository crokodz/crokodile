
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("loan.xls");


function addz($char,$num){
	$c = strlen($char);
	$a = $num - $c;
	$s = '';
	for($x=0;$x<$a;$x++){
		$s = $s.' ';
		}
	return $char.$s;
	}
	
function add0($char,$num){
	$c = strlen($char);
	$a = $num - $c;
	$s = '';
	for($x=0;$x<$a;$x++){
		$s = $s.'0';
		}
	return $char.$s;
	}
	
 function cspace($char,$num){
	$s = '';
	for($x=0;$x<$num;$x++){
		$s=$s.$char;
		}
	return $s;
	}
	
function addzz($char,$num){
	$c = strlen($char);
	if($c == 0){
		return '000000000000';
		}
	$a = $num - $c;
	$s = '';
	for($x=0;$x<$a;$x++){
		$s = $s.'0';
		}
	return $s.$char;
	}
	
function getM($num){
	$a = array("","January","February","March","April","May","June","July","August","September","October","November","December");
	return $a[$num];
	}

$appendsql = " ( ";
$var = explode("@@",$_GET['vars']);
for($x=0;$x<count($var);$x++){
	if ($var[$x]){
		if ($x==count($var)-2){
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "') ";
			}
		else{
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "' or ";
			}
		}
	}

						


//$column_header = array('Employee SSS Number','Employee Last Name','Employee First Name','Employee Middle Initial','SSS Premium Amount','EC Premium Amount','Date Hired/ Separated (MM/DD/YYYY)','Employment Status (N-Normal, 1-New, 2-Separated)');
//$excel->writeLine($column_header);


$select = "select sum(`amount`) as `amt`, `sssn`, `fname`, `lname`,`mname`,tb1.`date`, `principal_amount`, `date_effectivity`, `date_granted` from  employee_deduction tb1 left join employee using(`em_id`) where " . $appendsql . " and tb1.name = 'SSS LOAN' group by em_id" ;
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$sssno = str_replace("-","",$row['sssn']);
	$lname = $row['lname'];
	$fname = $row['fname'];
	$mname = substr($row['mname'],0,1);
	$type = "";
	$date = explode("-",$row['date_granted']);
	$yy = substr($date[0],2,4);
	$datex = $yy.$date[1].$date[2];
	$loan = roundoffNoComma($row['principal_amount'],2);
	$amount = roundoffNoComma($row['amt'],2);
	$penalty = 0;
	$amp = "";
	$remarks = "N";
	$rowx=array($sssno,$lname,$fname,$mname,$type,$datex,$loan,$penalty,$amount,$amp,$remarks);
	$excel->writeLine($rowx);
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=loan.xls');
header("Location: loan.xls");
exit;
?>