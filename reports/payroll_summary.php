<?php
require ('../config.php');
require ('class.ezpdf.php');

function getDeduction($id, $pid){
	$select = "select name, amount, balance from employee_deduction where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}

function getNon_Taxable($id, $pid){
	$select = "select name, amount from employee_non_taxable where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getTaxable($id, $pid){
	$select = "select name, amount from employee_taxable where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getsss($salary){
	$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
	}
	
function getpi($salary){
	if ($salary <= 1500){
		return $salary * .02;
		}
	else{
		return 100;
		}
	}
	
function getph($salary){
	$select = "select `id`,`ees`,`ers` from ph where `from` <= '" . $salary . "' order by `from` desc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ees'],$row['ers']);
	}


function GetInfo($id){
	$select = "select `name`,`ts`,`department`,`company_id` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['name'],$row['ts'],$row['department'],$row['company_id']);
	}

#variable
$pid = $_GET['pid'];
$select = "select tb1.*, tb2.name from posted_summary tb1 join company tb2 on(tb1.company_id = tb2.id) where tb1.posted_id = '" . $pid . "' group by tb1.posted_id";
$result = mysql_query($select, connect());
$rowsum = mysql_fetch_array($result,MYSQL_ASSOC);

$pdf =& new Cezpdf('LEGAL','landscape');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,1,1);
$pdf->ezStartPageNumbers(530,15,10,'','',1);

$cols = array('');	
$data = array(array('<b>' . $rowsum['name'] . '</b>'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>960,'fontSize' => 12,'cols'=>array(array('justification'=>'left','width'=>960))));
	
$cols = array('');	
$data = array(array('Payroll Summary'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>960,'fontSize' => 10,'cols'=>array(array('justification'=>'left','width'=>960))));
	
$cols = array('');	
$data = array(array('Payroll # : ' . $pid));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>960,'fontSize' => 10,'cols'=>array(array('justification'=>'left','width'=>960))));

$cols = array('');	
$data = array(array('<b>FROM</b> : ' . $rowsum['from'] . ' <b>TO</b> : ' . $rowsum['to']));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>960,'fontSize' => 10,'cols'=>array(array('justification'=>'left','width'=>960))));

$pdf->ezText('');

$select = "select posted_summary.* from posted_summary join employee using(em_id) where posted_id = '" . $pid . "' and employee.status = 'ACTIVE'";
#$select = "select * from posted_summary where posted_id = '" . $pid . "'";
$result = mysql_query($select, connect());
$x = 0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	#taxable
	$results = getTaxable($row['em_id'],$_GET['pid']);
	$otherstaxable = 0;
	while($rows = mysql_fetch_array($results,MYSQL_ASSOC)){
		$otherstaxable = $otherstaxable + $rows['amount'];
		}
	
	#non-taxable
	$results = getNon_Taxable($row['em_id'],$_GET['pid']);
	$othersnontaxable = 0;
	while($rows = mysql_fetch_array($results,MYSQL_ASSOC)){
		$othersnontaxable = $othersnontaxable + $rows['amount'];
		}
		
	#deduction
	$results = getDeduction($row['em_id'],$_GET['pid']);
	$otherdeduction = 0;
	while($rows = mysql_fetch_array($results,MYSQL_ASSOC)){
		$otherdeduction = $otherdeduction + $rows['amount'];
		}
		
	#loan balance
	$results = getDeduction($row['em_id'],$_GET['pid']);
	$loanbalance = 0;
	while($rows = mysql_fetch_array($results,MYSQL_ASSOC)){
		$loanbalance = $loanbalance + $rows['balance'];
		}
		
	$sss = getsss($row['salary']);
	$ph = getph($row['salary']);
	$pi = getpi($row['salary']);

	$info = GetInfo($row['em_id']);
	$rec[$x] = array($info[0],roundoff($row['salary'],2),roundoff($row['late']+$row['ut'],2),roundoff($row['absent'],2),roundoff($row['netpay'],2),roundoff($othersnontaxable,2),roundoff($otherstaxable,2),roundoff($row['taxable_salary'],2),roundoff($row['tax'],2),roundoff($sss[1],2),roundoff($ph[1],2),roundoff($pi,2),roundoff($loanbalance,2),roundoff($sss[1]+$pi+$ph[1]+$otherdeduction,2),roundoff($row['netpay'],2),roundoff($sss[2],2),$info[1]);
	$x++;
	}

$cols = array('Name','Basic Pay','Late     Undertime Trady   ','Absent    ','Net Pay   ','Other Non-Taxable    Income   ','Other      Taxable    Income   ','Gross Pay', 'Withholding Tax       ','SSS       Contri.    ','Phil.Health Contri.    ','Pag-Ibig    Contri.   ','Loan      Balance  ','Total      Deduction', 'Net Pay   ', 'ER Share SSS     ', 'Tax Status');	
$data = $rec;
$pdf->ezTable($data,$cols,'',array('showHeadings'=>1,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'left','width'=>155),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>53),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'right','width'=>50),
	array('justification'=>'left','width'=>50)
	)));

$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>
