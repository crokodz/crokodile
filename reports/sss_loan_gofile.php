<?php
require ('../config.php');
require ('class.ezpdf.php');

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
	
$select = " select `tin_name`, `address`, `tin`, `zip_code`, `sssn`,`sssname`, `number`, `hr_manager`, `hr_designation`,  `phn`  from company where id = '" . $_GET['compa'] . "' ";
$result = mysql_query($select, connect());
$cmp = mysql_fetch_array($result,MYSQL_ASSOC);
	
$select  = "select sum(tb1.`amount`) as `amount`, count(tb1.`em_id`) as `cnt`, tb3.`payday` from employee_deduction tb1 left join employee tb2 using(`em_id`) left join posted_summary tb3 on(tb1.`posted_id` = tb3.`posted_id` and tb1.`em_id` = tb3.`em_id`) where tb1.`name` = 'SSS LOAN' and " . $appendsql . "  order by lname asc, fname asc, mname asc";
$result = mysql_query($select, connect());
$vr = mysql_fetch_array($result,MYSQL_ASSOC);
$amount = $amount + $vr['amount'];
$dt = explode("-",$vr['payday']);
$yyy = explode("@",$dt[0]);
$yy = $yyy[1];
$yyx = substr($yyy[1],2,2);
$M = $dt[1];
$mm = getM($dt[1]+0);
$aa = $vr['payday'];

	
$File = $cmp['sssname']." ".$yyx.$M.'.TXT';
	
$ddfs = $yyx.$M;
$ddfsx = $dt[1] . "-" . $dt[0];



#variable
$pdf =& new Cezpdf('A4','portrait');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1,1);



$pdf->addText(60,785,11,$cmp['tin_name']);
$pdf->addText(60,770,11,$cmp['address']);
$pdf->addText(60,755,11,$cmp['number']);
$pdf->addText(60,740,11,'');

$pdf->addText(60,700,14,'<b>Transmittal List</b>');
$pdf->addText(60,700,14,'<b>______________</b>');
$pdf->addText(60,680,11,'File Name (XXXXYYMM)');
$pdf->addText(60,660,11,'ER ID No.');
$pdf->addText(60,640,11,'Billing Month (MM-YYYY)');
$pdf->addText(60,620,11,'Total No. of Records');

$pdf->addText(260,700,14,'');
$pdf->addText(260,680,11,':');
$pdf->addText(260,660,11,':');
$pdf->addText(260,640,11,':');
$pdf->addText(260,620,11,':');

$pdf->addText(290,700,14,'');
$pdf->addText(290,680,11,$File);
$pdf->addText(290,660,11,$cmp['sssn']);
$pdf->addText(290,640,11,$ddfsx);
$pdf->addText(290,620,11,$vr['cnt']);

######

$pdf->addText(60,580,14,'<b>Remittance</b>');
$pdf->addText(60,580,14,'<b>__________</b>');
$pdf->addText(60,560,11,'Total Amount Paid');
$pdf->addText(60,540,11,'Trans/SBR No. (Attached 2 photocopies)');
$pdf->addText(60,520,11,'Date Paid');

$pdf->addText(260,580,14,'');
$pdf->addText(260,560,11,':');
$pdf->addText(260,540,11,':');
$pdf->addText(260,520,11,':');

$pdf->addText(290,580,14,'');
$pdf->addText(290,560,11,roundoffNoComma($vr['amount'],2));
$pdf->addText(290,540,11,$_GET['sbr']);
$pdf->addText(290,520,11,$_GET['date']);

$pdf->addText(390,495,11,'<b>CERTIFIED CORRECT</b>');
$pdf->addText(340,475,11,'___________________________________');
$pdf->addText(355,462,11,'Company Authorized Representative');
$pdf->addText(375,447,11,'(Signed Over Printed Name)');


$pdf->addText(30,420,12,'<b>________________________________________________________________________________</b>');

$pdf->addText(60,400,14,'<b>To Be Filled Up By SSS Personnel</b>');
$pdf->addText(120,372,11,'Received By     :       _________________________');
$pdf->addText(260,358,11,'Printed Name');
$pdf->addText(198,335,11,':       _________________________');
$pdf->addText(267,322,11,'Signature');
$pdf->addText(198,305,11,':       _________________________');
$pdf->addText(280,292,11,'Date');

$pdf->addText(60,270,14,'<b>Remarks : </b>');
$pdf->addText(60,260,12,'<b>_______________________________________________________________________</b>');
$pdf->addText(60,245,12,'<b>_______________________________________________________________________</b>');
$pdf->addText(60,230,12,'<b>_______________________________________________________________________</b>');

$pdf->addText(60,200,14,'<b>Diskette Returned To ER : </b>');

$pdf->addText(120,172,11,'Received By     :       _________________________');
$pdf->addText(260,158,11,'Printed Name');
$pdf->addText(198,135,11,':       _________________________');
$pdf->addText(267,122,11,'Signature');
$pdf->addText(198,105,11,':       _________________________');
$pdf->addText(280,92,11,'Date');

$pdf->addText(60,80,14,'<b>Other Documents : (Specify)</b>');

$pdf->addText(90,65,11,'Diskette');
$pdf->addText(190,65,11,'Invalid Entries');
$pdf->addText(290,65,11,'Transmittal/Receipts');
$pdfcode = $pdf->ezOutput(0);
//~ $pdf->ezStream($pdfcode);
?>


<?php
$select = "select * from  company where `id` ='" . $_GET['compa'] . "'";
$result = mysql_query($select, connect());
$rowx = mysql_fetch_array($result,MYSQL_ASSOC);

$h1 = '00';
$h2 = str_replace("-","",$rowx['sssn']);
$h3 = addz($rowx['tin_name'],30);
$h4 = $ddfs;
$header = $h1.$h2.$h3.$h4."\n";

$select = "select sum(`amount`) as `amt`, `sssn`, `fname`, `lname`,`mname`,tb1.`date`, `principal_amount` from  employee_deduction tb1 left join employee using(`em_id`) where " . $appendsql . " and tb1.name = 'SSS LOAN' group by em_id" ;
$result = mysql_query($select, connect());
$pa = 0;
$amt = 0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$b1 = '100';
	$b2 = str_replace("-","",$row['sssn']);
	$b3 = addz($row['lname'],15);
	$b4 = addz($row['fname'],15);
	$b5 = substr($row['mname'],1,1);
	$b6 = " S";
	$bdate = explode("-",$row['date']);
	$bd1 = substr($bdate[0],1,2);
	$b7 = $bd1.$bdate[1].$bdate[2].'0';
	$bam1 = roundoffNoComma($row['principal_amount'],2);
	$bam2 = str_replace(".","",$bam1);
	$bam3 = add0($bam2,13);
	$b8 = $bam3;
	$bom1 = roundoffNoComma($row['amt'],2);
	$b9 = str_replace(".","",$bom1);
	$body = $body.$b1.$b2.$b3.$b4.$b5.$b6.$b7.$b8.$b9."\n";
	$amt = $amt + $row['amt'];
	$pa = $pa + $row['principal_amount'];
	}
$f1 = '990';
$fam1 = roundoffNoComma($pa,2);
$fam2 = str_replace(".","",$fam1);
$fam3 = add0($fam2,13);
$f2 = $fam3;
$fom1 = roundoffNoComma($amt,2);
$fom2 = str_replace(".","",$fom1);
$fom3 = $fom2;
$f3 = $fom3;
$footer =$f1.$f2.$f3;

$Data = $header.$body.$footer;

//$File = $rowx['id'].date('Ymd')."_sss_loan.txt"; 
$Handle = fopen($File, 'w');
fwrite($Handle, $Data); 
fclose($Handle); 
?>
<script>
self.location="sss_loan_file.php?file=<?php echo $File; ?>";
</script>
