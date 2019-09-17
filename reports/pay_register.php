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

function GetCompany($id){
	$select = "select `company`.`name` from `posted_summary` left join `company` on (`posted_summary`.`company_id` = `company`.`id`) where `posted_id` = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}

#variable
$pid = $_GET['pid'];

$pdf =& new Cezpdf('LEGAL','landscape');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezSetCmMargins(.5,.5,.5,.5);
$pdf->ezStartPageNumbers(320,15,10,'','',1);

$cols = array('');	
$data = array(array('<b>' . GetCompany($pid) . '</b>'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 11,'cols'=>array(array('justification'=>'left','width'=>990))));
	
$cols = array('');	
$data = array(array('Pay Register'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 7,'cols'=>array(array('justification'=>'left','width'=>990))));
	
$cols = array('');	
$data = array(array('Posted ID : ' . $pid));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 7,'cols'=>array(array('justification'=>'left','width'=>990))));

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
	
	$rec[$x] = array($info[0],roundoff($row['salary'],2),roundoff($otherstaxable,2),roundoff($othersnontaxable,2),roundoff($othersnontaxable,2),'',roundoff($row['taxable_salary'],2),roundoff($sss[1]+$pi[1]+$ph[1],2),roundoff($sss[2]+$pi[2]+$ph[2],2),roundoff($row['tax'],2),roundoff($row['tax'],2),roundoff($row['netpay'],2),roundoff($sss[2]+$pi[2]+$ph[2]+$row['tax'],2));
	$x++;
	}

$cols = "";
$data = array(array('Name','Monthly Basic','13th Month','Transportation','Others','Total Gross','SSS, Philhealth, HDM Funds','Income Tax Withholding', 'NET Payment to Employee','Total Remittance'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'center','width'=>150),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>140),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>140),
	array('justification'=>'center','width'=>140),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70)
	)));
	
$data = array(array('','Salary','Taxable','Non-Taxable','Allowance','','Salary','Employee', 'Employer','For General Compensation', 'Fro Item (3) at rate 10%','','Amount'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'center','width'=>150),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70),
	array('justification'=>'center','width'=>70)
	)));
	
$data = $rec;
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'left','width'=>150),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70),
	array('justification'=>'right','width'=>70)
	)));

$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>