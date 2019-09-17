
<?php
require ('../config.php');
require ('class.ezpdf.php');


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

$select = " select sum(tb1.`sss`) as `sss`, tb1.`payday`
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) where " . $appendsql . " group by `em_id` ";
$result = mysql_query($select, connect());
$emtot = 0;
$employee = 0;
$employer = 0;
$ec = 0;
while($vr = mysql_fetch_array($result,MYSQL_ASSOC)){
	$emtot++;
	$w = getsss($vr['sss']);
	$employee = $employee + $vr['sss'];
	$employer = $employer + $w[0];
	$ec = $ec +  + $w[1];
	}

$select = " select `tin_name`, `address`, `tin`, `zip_code`, `sssn`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '" . $_GET['compa'] . "' ";
$result = mysql_query($select, connect());
$cmp = mysql_fetch_array($result,MYSQL_ASSOC);


#variable
$pdf =& new Cezpdf('A4','portrait');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1,1);

$pdf->addText(60,785,16,'<b>SSS Transmittal Certification</b>');

$pdf->addText(30,775,12,'<b>________________________________________________________________________________</b>');


$pdf->addText(60,745,12,'File Name');
$pdf->addText(60,725,12,'Employer Name');
$pdf->addText(60,705,12,'Employer ID');

$File = 'R3' . $M . $yy . "." . str_replace("-","",$cmp['sssn']);
$Url = '/reports/sss_contri.php?vars='.$_GET['vars'].'&compa='.$_GET['compa'].'&or='.$_GET['or'].'&dt='.$_GET['dt'].'&dp='.$_GET['dp'];
$pdf->addText(160,745,12,'<c:alink:' . $Url . '>'.$File.'</c:alink>');
$pdf->addText(160,725,12,$cmp['tin_name']);
$pdf->addText(160,705,12,str_replace("-","",$cmp['sssn']));

$pdf->addText(390,745,12,'Date       ' . $_GET['dt']);
$pdf->addText(390,725,12,'Applicable Mo: ' . $mm . ", " . $yy);


$pdf->addText(60,600,12,'');
$pdf->addText(60,575,12,'Amount');

$pdf->addText(170,600,12,'SSS');
$pdf->addText(170,575,12,roundoff($employer+$employee));

$pdf->addText(260,600,12,'EC');
$pdf->addText(260,575,12,roundoff($ec));

$pdf->addText(330,600,12,'Total');
$pdf->addText(330,575,12,roundoff($employer+$employee+$ec));

$pdf->addText(400,600,12,'SBR #/ OR #');
$pdf->addText(400,575,12,$_GET['or']);

$pdf->addText(490,600,12,'Date Paid');
$pdf->addText(490,575,12,$_GET['dp']);


$pdf->addText(60,505,12,'TOTAL NUMBER OF EMPLOYEES : ' . $emtot);

$pdf->addText(200,442,12,'<b>____________________________</b>');
$pdf->addText(200,425,12,'CERTIFIED CORRECT AND PAID');

$pdf->addText(60,360,12,'RECEIVED BY : ');
$pdf->addText(60,330,12,'DATE RECEIVED : ');
$pdf->addText(60,300,12,'TRANSACTION NO. : ');

$pdf->addText(200,363,12,'<b>_______________________________________</b>');
$pdf->addText(200,333,12,'<b>_______________________________________</b>');
$pdf->addText(200,303,12,'<b>_______________________________________</b>');

$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>
