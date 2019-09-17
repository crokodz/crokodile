<?php
require ('../config.php');
require ('class.ezpdf.php');
	
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
	$select = "select `name`,`ts`,`department`,`company_id`,`sssn`,`pin`,`phn` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
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
$data = array(array('SSS - PHILHEALTH - PAGIBIG'));
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
	$sss = getsss($row['salary']);
	$ph = getph($row['salary']);
	$pi = getpi($row['salary']);

	$info = GetInfo($row['em_id']);
	$rec[$x] = array($x+1,$info['name'],$info['sssn'],$info['pin'],$info['phn'],roundoff($sss[1],2),roundoff($pi,2),roundoff($ph[1],2),roundoff($sss[2],2),roundoff($pi,2),roundoff($ph[2],2));
	$x++;
	}

$cols = array('#','                                         Name','             SSS #','           Philhealth #','            Pagibig #','EM SSS        ','EM PH         ','EM PI         ','ER SSS        ', 'ER PH         ','ER PI         ');	
$data = $rec;
$pdf->ezTable($data,$cols,'',array('showHeadings'=>1,'showLines'=>3,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>520,'fontSize' => 8,'cols'=>array(
	array('justification'=>'center','width'=>20),
	array('justification'=>'left','width'=>220),
	array('justification'=>'left','width'=>100),
	array('justification'=>'left','width'=>100),
	array('justification'=>'left','width'=>100),
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
