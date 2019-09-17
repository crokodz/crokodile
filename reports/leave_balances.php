`
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");



$excel=new ExcelWriter("leavebalances" . $ymd . ".xls");


function getperday($salary,$company,$id,$payid){
    $select = "select factor, days from pay where name = '" . $payid . "' limit 1";

    $result = mysql_query($select, connect());
    $row = mysql_fetch_array($result,MYSQL_ASSOC);

    $factor = $row['factor'];
    $days = $row['days'];

    if ($id == 1){
        return ($salary * 12) / $factor;
        }
    elseif ($id == 2){
        return ($salary / $days);
        }

    }

$column_header = array('Status', 'Employee ID', 'First Name','Last Name', 'Paycode', 'Employee Status', 'Used VL', 'VL Balance', 'Amount VL', 'Used SL', 'SL Balance', 'Amount SL');
$excel->writeLine($column_header);


$select = "select t1.em_id, t2.fname, t2.lname, t2.pay_id, t1.trxn_date, t1.status, t1.posted_id from transaction t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.posted_id >= 0 AND t1.status like '%LEAVE%' order by t1.em_id" ;

$select = "select * from employee order by status, employee_status, em_id" ;
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){

    $vselect = "select count(*) as cnt, status from `transaction` where trxn_date > '2018-11-01' and em_id = '" . $row['em_id'] . "' group by status";
    $vresult = mysql_query($vselect, connect());
    $vrow = mysql_fetch_array($vresult,MYSQL_ASSOC);


    $sselect = "select count(*) as cnt, status from `transaction` where YEAR(trxn_date) = YEAR(CURDATE())  and em_id = '" . $row['em_id'] . "' group by status";
    $sresult = mysql_query($sselect, connect());
    $srow = mysql_fetch_array($sresult,MYSQL_ASSOC);

    $vl = 0;
    $sl = 0;

    while ($vdata = mysql_fetch_array($vresult,MYSQL_ASSOC)){
        if ($vdata['status'] == 'VACATION LEAVE 0.5'){
            $vl += ($vdata['cnt']/2);
        }
        if ($vdata['status'] == 'VACATION LEAVE'){
            $vl += $vdata['cnt'];
        }
    }

    while ($sdata = mysql_fetch_array($sresult,MYSQL_ASSOC)){
        if ($sdata['status'] == 'SICK LEAVE 0.5'){
            $sl += ($sdata['cnt']/2);
        }
        if ($sdata['status'] == 'SICK LEAVE'){
            $sl += $sdata['cnt'];
        }
    }


    $daily = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);



    $rowx=array($row['status'], $row['em_id'],$row['fname'],$row['lname'],$row['pay_id'],$row['employee_status'],$vl,$row['vl'] - $vl,$daily * ($row['vl'] - $vl),$sl,$row['sl'] - $sl, $daily * ($row['sl'] - $sl));
    $excel->writeLine($rowx);
    }


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=leavebalances' . $ymd . '.xls');
header("Location: leavebalances" . $ymd . ".xls");
exit;
?>
