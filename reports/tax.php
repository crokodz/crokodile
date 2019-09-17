<?php
require ('../config.php');
require ('class.ezpdf.php');

function GetInfo($id){
	$select = "select `name`,`tinn` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

#variable
$pid = $_GET['pid'];
$pid = $_GET['pid'];
$select = "select tb1.*, tb2.name from posted_summary tb1 join company tb2 on(tb1.company_id = tb2.id) where tb1.posted_id = '" . $pid . "' group by tb1.posted_id";
$result = mysql_query($select, connect());
$rowsum = mysql_fetch_array($result,MYSQL_ASSOC);

$pdf =& new Cezpdf('LETTER','portrait');
$pdf->selectFont('./fonts/Helvetica.afm');
$pdf->ezSetCmMargins(1,1,2.1,1);
$pdf->ezStartPageNumbers(320,15,10,'','',1);

$cols = array('');	
$data = array(array('<b>' . $rowsum['name'] . '</b>'));
$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>960,'fontSize' => 12,'cols'=>array(array('justification'=>'left','width'=>960))));
	
$cols = array('');	
$data = array(array('Withholding Tax Report'));
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
	$info = GetInfo($row['em_id']);
	$rec[$x] = array($x+1,$info['name'],$info['tinn'],roundoff($row['tax'],2));
	$x++;
	}

$cols = array('<b>#</b>','<b>Name</b>','<b>Tin #</b>','<b>Whithholding Tax</b>');	
$data = $rec;
$pdf->ezTable($data,$cols,'',array('showHeadings'=>1,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'center','width'=>20),
	array('justification'=>'left','width'=>300),
	array('justification'=>'left','width'=>100),
	array('justification'=>'right','width'=>80)
	)));

$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>