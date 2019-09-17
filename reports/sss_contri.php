<?php
require ('../config.php');

function cspace($char,$num){
	$s = '';
	for($x=0;$x<$num;$x++){
		$s=$s.$char;
		}
	return $s;
	}
	
function addz($char,$num){
	$char = str_replace("ÑE", "N",$char);
	$char = str_replace("Ñ", "N",$char);
	
	$c = strlen($char);
	$a = $num - $c;
	$s = '';
	if($c>=$num){
		for($x=0;$x<$num;$x++){
			$s = $s.$char[$x];
			}
		return $s;
		}
	else{
		for($x=0;$x<$a;$x++){
			$s = $s.' ';
			}
		return $char.$s;
		}
	}
	
function addzl($char,$num){
	$c = strlen($char);
	$a = $num - $c;
	$s = '';
	for($x=0;$x<$a;$x++){
		$s = $s.' ';
		}
	return $s.$char;
	}

function getM($num){
	$a = array("","January","February","March","April","May","June","July","August","September","October","November","December");
	return $a[$num];
	}
	
function getsss($salary){
	$select = "select sser, ec from sss where `ssee` = '" . $salary . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['sser'],$row['ec']);
	}
	
function getStat($em_id){
	$select = "select `file_status` from employee where `em_id` = '" . $em_id . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if($row['file_status'] == 'SEPARATED'){
		$stat = '2';
		}
	else{
		$select = "select `em_id` from transaction where `em_id` = '" . $em_id . "' ";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_row($result);
		if($row[0] > 1){
			$stat = 'N';
			}
		else{
			$stat = '1';
			}
		}
	return $stat;
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

$select = " select sum(tb1.`sss`) as `sss`, tb1.`payday`
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) where " . $appendsql . " ";
$result = mysql_query($select, connect());
$emtot = 0;
$employee = 0;
while($vr = mysql_fetch_array($result,MYSQL_ASSOC)){
	$emtot++;
	if($totem==0){
		$dt = explode("-",$vr['payday']);
		$yyy = explode("@",$dt[0]);
		$yy = $yyy[1];
		$M = $dt[1];
		$mm = getM($dt[1]+0);
		}
	}
	


$select = " select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '" . $_GET['compa'] . "' ";
$result = mysql_query($select, connect());
$cmp = mysql_fetch_array($result,MYSQL_ASSOC);



$date = $date[5].$date[6].$date[8].$date[9].$date[2].$date[3];
$h1 = addz('00',2);
$h2 = addz($cmp['tin_name'],30);
$h3 = addz($M . $yy,6);
#$h4 = addz(str_replace("-","",$cmp['sssn']),79);
$h4 = addz(str_replace("-","",$cmp['sssn']),10);
$h5 = addz('',30);
$h = $h1.$h2.$h3.$h4.$h5."\r\n";

$select = " select sum(tb1.`sss`) as `sss`, tb1.`payday`, tb2.`fname`, tb2.`lname`, tb2.`mname`, tb2.`sssn`, `em_id`
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) where " . $appendsql . " group by `em_id`  order by lname asc, fname asc, mname asc";
$result = mysql_query($select, connect());
$emtot = 0;
$employee = 0;
$employer = 0;
$ec = 0;
$bx = "";
while($vr = mysql_fetch_array($result,MYSQL_ASSOC)){
if($vr['sss']>0){
$emtot++;
$w = getsss($vr['sss']);
$employee = $employee + $vr['sss'];
$employer = $employer + $w[0];
$ec = $ec + $w[1];

$mname  = ($vr['mname']);
$stat = getStat($vr['em_id']);
$bx1 = addz('20',2);
$bx2 = addz($vr['lname'],15);
$bx3 = addz($vr['fname'],15);
$bx4 = addz($mname[0],1);
$bx5 = addz(str_replace("-","",$vr['sssn']),10);
if($M == '01' or $M == '04' or $M == '07' or $M == '10'){
	$bx6 = addzl(ronc($vr['sss']+$w[0]),8);
	}
else{
	$bx6 = addzl('0.00',8);
	}
	
if($M == '02' or $M == '05' or $M == '08' or $M == '11'){
	$bx7 = addzl(ronc($vr['sss']+$w[0]),8);
	}
else{
	$bx7 = addzl('0.00',8);
	}
	
if($M == '03' or $M == '06' or $M == '09' or $M == '12'){
	$bx8 = addzl(ronc($vr['sss']+$w[0]),8);
	}
else{
	$bx8 = addzl('0.00',8);
	}
	
$bx9 = addzl('0.00',6);
$bx10 = addzl('0.00',6);
$bx11 = addzl('0.00',6);

#EC
if($M == '01' or $M == '04' or $M == '07' or $M == '10'){
	$bx12 = addzl(ronc($w[1]),6);
	}
else{
	$bx12 = addzl('0.00',6);
	}
	
if($M == '02' or $M == '05' or $M == '08' or $M == '11'){
	$bx13 = addzl(ronc($w[1]),6);
	}
else{
	$bx13 = addzl('0.00',6);
	}
	
if($M == '03' or $M == '06' or $M == '09' or $M == '12'){
	$bx14 = addzl(ronc($w[1]),6);
	}
else{
	$bx14 = addzl('0.00',6);
	}

$bx15 = addzl(' ',6);
$bx16 = addzl($stat,1);
$bx17 = addz('',8);

$bx = $bx.$bx1.$bx2.$bx3.$bx4.$bx5.$bx6.$bx7.$bx8.$bx9.$bx10.$bx11.$bx12.$bx13.$bx14.$bx15.$bx16.$bx17."\r\n";
}
}

$tx1 = addz('99',2);
if($M == '01' or $M == '04' or $M == '07' or $M == '10'){
	$tx2 = addzl(ronc($employee+$employer),12);
	}
else{
	$tx2 = addzl('0.00',12);
	}
	
if($M == '02' or $M == '05' or $M == '08' or $M == '11'){
	$tx3 = addzl(ronc($employee+$employer),12);
	}
else{
	$tx3 = addzl('0.00',12);
	}
	
if($M == '03' or $M == '06' or $M == '09' or $M == '12'){
	$tx4 = addzl(ronc($employee+$employer),12);
	}
else{
	$tx4 = addzl('0.00',12);
	}
$tx5 = addzl('0.00',10);
$tx6 = addzl('0.00',10);
$tx7 = addzl('0.00',10);

#EC TOTAL
if($M == '01' or $M == '04' or $M == '07' or $M == '10'){
	$tx8 = addzl(ronc($ec),10);
	}
else{
	$tx8 = addzl('0.00',10);
	}
	
if($M == '02' or $M == '05' or $M == '08' or $M == '11'){
	$tx9 = addzl(ronc($ec),10);
	}
else{
	$tx9 = addzl('0.00',10);
	}
if($M == '03' or $M == '06' or $M == '09' or $M == '12'){
	$tx10 = addzl(ronc($ec),10);
	}
else{
	$tx10 = addzl('0.00',10);
	}

$tx = $tx1.$tx2.$tx3.$tx4.$tx5.$tx6.$tx7.$tx8.$tx9.$tx10."\r\n";

$Data = $h.$bx.$tx;
$File = 'R3' . $M . $yy . "." . str_replace("-","",$cmp['sssn']).".txt"; 

$Handle = fopen($File, 'w');
fwrite($Handle, $Data); 
fclose($Handle); 

header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=".$File);
header('Content-Disposition: attachment; filename="'.$File.'"');
readfile($File);
exit;
?>


