<?php
require ('../config.php');
require ('class.ezpdf.php');

if ($_SESSION['company'] != '0'){
	$select = "SELECT * FROM posted_summary where company_id = '" . $_SESSION['company'] . "' and posted_id = '" . $_GET['pid'] . "' GROUP BY posted_id";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if (empty($row['posted_id'])){
		echo "<script>";
		echo "window.close();";
		echo "alert('Unauthorized access not allowed!!! Alert has been send to administrator!!! Administrator has the authority to block your IP w/o any warning...')";
		echo "</script>";
		}
	}
	
function h2m($hours){
	$expl = explode(":", $hours); 
	return ($expl[0] * 60) + $expl[1];
	}
	
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

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function GetInfo($id){
	$select = "select `name`,`ts`,`department`,`company_id`, `salary_based`, `salary` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['name'],$row['ts'],$row['department'],$row['company_id'],$row['salary_based'],$row['salary']);
	}
	
function GetSummary($id,$pid){
	$select = "select * from posted_summary where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
	
function GetTYDTax($id, $date){
	$select = "select * from yearly_cutoff where date1 <= '" . $date . "' and date2 >= '" . $date . "'";
	$result = mysql_query($select, connect());
	$rowz = mysql_fetch_array($result,MYSQL_ASSOC);

	$select = "select sum(`taxable_salary`) as `taxable_salary` ,sum(`tax`) as `tax` ,sum(`sss`) as `sss` ,sum(`ph`) as `ph` ,sum(`pi`) as `pi` from posted_summary where em_id = '" . $id . "' and `from` between '" . $rowz['date1'] . "' and '" . $rowz['date2'] . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['taxable_salary'] - $row['sss'] - $row['pi'] - $row['ph'],$row['tax']);
	}
	
function getDeductionBal($emid){
	$select = "select name, sum(amount) as `bal` from employee_deduction where em_id = '" .  $emid. "' and posted_id='0' group by `sub_id`";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function gogogogo($id, $pid,$pdf){
	$info = GetInfo($id);
	$summary = GetSummary($id, $pid);
	$company = getcompany($info[3]);
	
	$salary = $summary['salary'];
	$otherstaxable = $summary['other_tax_inc'];
	$taxable = $summary['taxable_salary'];
	$tin = $summary['tax'];
	$sss = $summary['sss'];
	$ph = $summary['ph'];
	$pi = $summary['pi'];
	$otherdeduction = $summary['deduction'];
	$othersnontaxable = $summary['nontax'];
	$netpay = $summary['netpay'];
	//$absent = $summary['absent'];
	$gross =  $summary['taxable_salary'] - $summary['tax'];
	$late = $summary['late'];
	$ot = $summary['ot'];
	$ut = $summary['ut'];
	$halfday = 0;
	$absent = 0;
	$perday_salary = $summary['perday_salary'];
	
	
	
	#absent
	$abs = $summary['abs_value'];
	$abs = explode("@@",$abs);
	$cntabs = 0; 
	$cnthd = 0; 
	for($x=0;$x<=count($abs);$x++){
		$absz = explode("||",$abs[$x]);
		if($absz[0]){
			if($absz[2] == 'ABSENT'){
				$cntabs++;
				$absent = $absent + ($absz[1] * $perday_salary);
				}
			else{
				$halfday = $halfday + ($absz[1] * $perday_salary);
				$cnthd++;
				}
			}
		}
		
	$totaladjustment = $late + $ut + $halfday + $absent;
		
	#late
	$l = $summary['late_value'];
	$l = explode("@@",$l);
	$cntl = 0; 
	for($x=0;$x<=count($l);$x++){
		if($l[$x]){
			$rl = explode("||", $l[$x]);
			$cntl = $cntl + $rl[1];
			}
		}
		
	#ut
	$u = $summary['ut_value'];
	$u = explode("@@",$u);
	$cntut = 0; 
	for($x=0;$x<=count($u);$x++){
		if($u[$x]){
			$ur = explode("||", $u[$x]);
			$cntut = $cntut + $ur[1];
			}
		}
	
	$pdf->addText(17,365,10,'<b>' . $company['name'] . '</b>');
	
	$data = array(
		array('Basic Salary',roundoff($salary,2)),
		array('Adjustments*',roundoff($totaladjustment,2)),
		array('OT*',roundoff($summary['ot'],2)),
		array('Othr Tx Inc*',roundoff($otherstaxable,2)),
		array('Others',roundoff(0,2)),
		array('Gross Taxable',roundoff($taxable,2)),
		array('Less: W/H Tax',roundoff($tin,2)),
		array('Gross After Tax',roundoff($gross,2)),
		array(' SSS Premium',roundoff($sss,2)),
		array(' PhilHealth',roundoff($ph,2)),
		array(' Pag-Ibig',roundoff($pi,2)),
		array(' Loan Pymts*',roundoff(0,2)),
		array(' Other Ded/(Add)',roundoff($otherdeduction,2)),
		array('Add : ',''),
		array(' NoTax Inc/Pmt',roundoff($othersnontaxable,2))
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));
		$pdf->ezText('');
		$data = array(
		array('<b>Net Pay</b>','<b>'.roundoff($netpay,2).'</b>')
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));
		
	$pdf->ezText('');
		$data = array(
		array('<b>Tax Status : ' . $info[1] . '</b>',''),
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));
		
	$pdf->ezText('');
		$data = array(
		array('','')
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));
		
		
	$pdf->ezText('');
		$data = array(
		array($info[2],'')
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>110),
		array('justification'=>'right','width'=>40)
		)));
		
	$pdf->addText(110,292,8,'<b>____________</b>'); 
	$pdf->addText(110,264,8,'<b>____________</b>'); 
	$pdf->addText(110,251,8,'<b>____________</b>'); 
	$pdf->addText(110,151,8,'<b>____________</b>'); 	
		
	$pdf->addText(17,73,8,'<b>' . $info[0] . '</b> - ' . $_GET['id']); 
	
	#
	$pdf->addText(450,60,8,$now); 
	
	##
	$pp = explode("@", $summary['payday']);
	if($pp[0] == 'w1'){
		$ppz = 'First Pay Day of ' . $pp[1];
		}
	else{
		$ppz = 'Second Pay Day of ' . $pp[1];
		}
	$pdf->addText(16,40,8,'Payroll# : ' . $_GET['pid'] . '     ' . $summary['from'] . ' to ' . $summary['to'] . '    <b>Pay Date<b> : ' . $summary['title']); 
		#row2
	$pdf->addText(180,345,8,'Misc. Salary Adjustments');
		$pdf->addText(180,325,8,'ABSENT(' . $cntabs . ')'); 
		$pdf->addText(246,325,8,make10($absent)); 
		$pdf->addText(180,315,8,'LATE(' . $cntl . ')'); 
		$pdf->addText(246,315,8,make10($late)); 
		$pdf->addText(180,305,8,'UNDERTIME(' . $cntut . ')'); 
		$pdf->addText(246,305,8,make10($ut)); 
		$pdf->addText(180,295,8,'HALFDAY(' . $cnthd . ')'); 
		$pdf->addText(246,295,8,make10($halfday));
		$pdf->addText(180,285,8,'<b>TOTAL</b>'); 
		$pdf->addText(246,285,8,make10($totaladjustment));

		$pdf->addText(180,250,8,'Other Tax Income Detail'); 
		$result = explode("@@",$summary['oth_value']);
		$x = 230;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount from employee_taxable where id = '" . $result[$k] . "' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				$pdf->addText(180,$x,8,$row['name']); 
				$pdf->addText(246,$x,8,make10($row['amount'])); 
				$total = $total + $row['amount'];
				$x = $x - 10;
				}
			}
		$pdf->addText(180,$x,8,"<b>TOTAL</b>"); 
		$pdf->addText(246,$x,8,make10($total)); 
		
	$pdf->addText(180,190,8,'Other Deductions/(Additions)'); 
		$result = explode("@@",$summary['ded_value']);
		$x = 170;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount, balance from employee_deduction where deduct_id = '" . $result[$k] . "' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				$pdf->addText(180,$x,8,substr($row['name'],0,15)); 
				$pdf->addText(246,$x,8,make10($row['amount'])); 
				$total = $total + $row['amount'];
				$x = $x - 10;
				}
			}
		$pdf->addText(180,$x,8,"<b>TOTAL</b>"); 
		$pdf->addText(246,$x,8,make10($total)); 
		#row3
		
	$pdf->addText(315,345,8,'Non-Taxable Income/(Pyt)'); 
		$result = explode("@@",$summary['nt_value']);
		$x = 325;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount from employee_non_taxable where `id` = '" . $result[$k] . "' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				$pdf->addText(315,$x,8,$row['name']); 
				$pdf->addText(380,$x,8,make10($row['amount'])); 
				$total = $total + $row['amount'];
				$x = $x -10;
				}
			}
		$pdf->addText(315,$x,8,"<b>TOTAL</b>"); 
		$pdf->addText(380,$x,8,make10($total)); 
	
	$pdf->addText(315,250,8,'Over Time OT/ND Hrs.'); 
		$result = explode("@@",$summary['ot_value']);
		$adjustment = 0;
		$ot = 0;
		$otx = 0;
		$nd = 0;
		$otamt = 0;
		$otxamt = 0;
		$ndamt = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$dedz = explode("||", $result[$k]);
				$ot = $ot + $dedz[1];
				$otx = $otx + $dedz[2];
				$nd = $nd + $dedz[3];
				$otamt = $otamt + $dedz[5];
				$otxamt = $otxamt + $dedz[6];
				$ndamt = $ndamt + $dedz[7];
				}
			}
		
		$x = 230;
		$total = 0;
		for($j=0;$j<1;$j++){
			if ($adjustment > 0){
				$pdf->addText(315,$x,8, "ADJ" . "(" . 0 . ")"); 
				$pdf->addText(380,$x,8,make10(0));
				$x = $x -10;
				$total = $total + 0;
				}
			if ($ot > 0){
				$pdf->addText(315,$x,8,"REG-OT" . "(" . $ot . ")"); 
				$pdf->addText(380,$x,8,make10($otamt)); 
				$x = $x -10;
				$total = $total + $otamt;
				}
			if ($otx > 0){
				$pdf->addText(315,$x,8,"REG-OX" . "(" . $otx . ")"); 
				$pdf->addText(380,$x,8,make10($otxamt)); 
				$x = $x -10;
				$total = $total + $otxamt;
				}
			if ($nd > 0){
				$pdf->addText(315,$x,8,"REG-ND" . "(" . $nd . ")"); 
				$pdf->addText(380,$x,8,make10($ndamt)); 
				$x = $x -10;
				$total = $total + $ndamt;
				}
			}
		
		$pdf->addText(315,$x,8,"<b>TOTAL</b>"); 
		$pdf->addText(380,$x,8,make10($total)); 
			
		
	//~ #row4
	$tax = GetTYDTax($id,$summary['from']);
	
	$pdf->addText(450,345,8,'Year-To-Date Summaries'); 
	$pdf->addText(450,325,8,'YTD W/H Tax:'); 
		$pdf->addText(550,325,8,make10($tax[1])); 
	$pdf->addText(450,305,8,'YTD Taxable Inc.');
		$pdf->addText(550,305,8,make10($tax[0])); 
	$pdf->addText(450,285,8,'YTD Txbl Bon/13th'); 
		$pdf->addText(550,285,8,make10(0)); 
	$pdf->addText(450,265,8,'YTD NTx Bon/13th'); 
		$pdf->addText(550,265,8,make10(0)); 
	$pdf->addText(450,240,8,'Loan Balance'); 
		$result = getDeductionBal($id);
		$x = 220;
		$total = 0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$pdf->addText(450,$x,8,$row['name']); 
			$pdf->addText(550,$x,8,make10($row['bal'])); 
			$total = $total + $row['bal'];
			$x = $x - 10;
			}
	#lines
	for ($x=85;$x<350;$x++){
		$pdf->addText(170,$x,8,'|'); 
		}
	$pdf->ezNewPage();
	return $pdf;
	}

function make10($amount){
	$amount = roundoff($amount,2);
	
	if (strlen($amount) == 4){
		return "      " . $amount;
		}
	elseif (strlen($amount) == 5){
		return "     " . $amount;
		}
	elseif (strlen($amount) == 6){
		return "    " . $amount;
		}
	elseif (strlen($amount) == 7){
		return "   " . $amount;
		}
	elseif (strlen($amount) == 8){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 9){
		return " " . $amount;
		}
	elseif (strlen($amount) == 10){
		return $amount;
		}
	}

$pdf =& new Cezpdf('LETTERHALF','portrait');
$pdf->selectFont('./fonts/Courier.afm');
$pdf->ezSetCmMargins(1.3,.5,.60,.5);

#ALL
if ($_GET['id'] == 'ALL'){
	$select = "select posted_summary.* from posted_summary join employee using(em_id) where posted_id = '" . $_GET['pid'] . "' and employee.status = 'active' group by em_id limit " . $_GET['max'] . ", 100";
	$result_summary = mysql_query($select, connect());
	while($row_summary = mysql_fetch_array($result_summary,MYSQL_ASSOC)){
		$_GET['id'] = $row_summary['em_id'];
		$pdf = gogogogo($_GET['id'], $_GET['pid'],$pdf);
		}
	}

#INDIVIDUAL
else{
	$pdf = gogogogo($_GET['id'], $_GET['pid'],$pdf);
	}
		
$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>