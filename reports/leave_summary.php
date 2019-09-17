`
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("leavebalances.xls");

$column_header = array('Employee ID', 'First Name','Last Name', 'Paycode', 'Date', 'Leave Type', 'Payroll ID');
$excel->writeLine($column_header);


$select = "select t1.em_id, t2.fname, t2.lname, t2.pay_id, t1.trxn_date, t1.status, t1.posted_id from transaction t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.posted_id >= 0 AND t1.status like '%LEAVE%' order by t1.em_id" ;
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
    $rowx=array($row['em_id'],$row['fname'],$row['lname'],$row['pay_id'],$row['trxn_date'],$row['status'],$row['posted_id']);
    $excel->writeLine($rowx);
    }


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=leavebalances.xls');
header("Location: leavebalances.xls");
exit;
?>
