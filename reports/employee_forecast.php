<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("employee_forecast_" . $ymd . ".xls");

$middle = $_GET['payday'];
$type = $_GET['type'];
$company = $_GET['compa'];


$time = strtotime($middle."-01");
$last = substr(date("Y-m-d", strtotime("+1 month", $time)), 0, 7);
$first = substr(date("Y-m-d", strtotime("-1 month", $time)), 0, 7);


$month = array($first, $middle, $last);

$text = array('HEAD COUNT AND PAYROLL SUMMARY');
$excel->writeLine($text);
$text = array('COMPANY ID: #' . $company);
$excel->writeLine($text);

$text = array('');
$excel->writeLine($text);

$text = array('Month', '#Contract Term','New Employee','Resigned','Total Head Count', 'Salary (Note 5)', 'Guaranteed Bonus / Double Pay', 'Bonus / Gratuity / Year End Payment / (if any)', 'Overtime', 'Commission', '*Other Taxable', '*Non-Taxable','Employer P.fund /Social Securities Contribution', 'Total Payroll Cost (g)', 'Last Month Total(h)', 'Variance with Last Month(j) = (h)-(g)', '% of Change (j) / (h)');
$excel->writeLine($text);

 
$month = ["01","02","03","04","05","06","07","08","09","10","11","12"];


$last_total = array();
$z = 0;

foreach ($month as $m) {
    $mx = date('Y').'-'.$m;

    $select = "select 
        t1.employee_status,
        sum(t2.salary) as salary,
        sum(t2.ot) as overtime,
        sum(t2.sss+t2.pi) as contri
        from employee t1 left join posted_summary t2 on (t1.em_id = t2.em_id) where payday like '%" . $mx . "' and 

        post_type = '" . $type . "' and t2.company_id = '" . $company . "' group by employee_status" ;
    $result = mysql_query($select, connect());
    $y = 0;
    $salary = 0;
    $overtime = 0;
    $commission = 0;
    $taxable = 0;
    $nontax = 0;
    $contri = 0;
    $totalx = 0;
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
        $select1 = "select 
            sum(t3.amount) as commission
            from employee_taxable t3 left join
            posted_summary t2 on (t3.posted_id = t2.posted_id and t3.em_id = t2.em_id) left join
            employee t1 on (t1.em_id = t2.em_id) where payday like '%" . $mx . "' and post_type = '" . $type . "' and employee_status = '" . $row['employee_status'] . "' and t2.company_id = '" . $company . "' 
                and t3.name = 'COMMISSION'
                and t3.status = 'posted'
                group by employee_status" ;
        $result1 = mysql_query($select1, connect());
        $row1 = mysql_fetch_row($result1,MYSQL_ASSOC);

        $select2 = "select 
            sum(t3.amount) as taxable
            from employee_taxable t3 left join
            posted_summary t2 on (t3.posted_id = t2.posted_id and t3.em_id = t2.em_id) left join
            employee t1 on (t1.em_id = t2.em_id) where payday like '%" . $mx . "' and post_type = '" . $type . "' and employee_status = '" . $row['employee_status'] . "' and t2.company_id = '" . $company . "' 
                and t3.name != 'COMMISSION'
                and t3.status = 'posted'
                group by employee_status" ;
        $result2 = mysql_query($select2, connect());
        $row2 = mysql_fetch_row($result2,MYSQL_ASSOC);

        $select3 = "select 
            sum(t3.amount) as nontax
            from employee_non_taxable t3 left join
            posted_summary t2 on (t3.posted_id = t2.posted_id and t3.em_id = t2.em_id) left join
            employee t1 on (t1.em_id = t2.em_id) where payday like '%" . $mx . "' and post_type = '" . $type . "' and employee_status = '" . $row['employee_status'] . "' and t2.company_id = '" . $company . "' 
                and t3.status = 'posted'
                group by employee_status" ;
        $result3 = mysql_query($select3, connect());
        $row3 = mysql_fetch_row($result3,MYSQL_ASSOC);


        if($y==0){
            $mxx = date('Y').'-'.date("F", mktime(0, 0, 0, $m, 10));
        } else {
            $mxx = "";
        }

        $mxz = date('Y').'-'.date("F", mktime(0, 0, 0, $m, 10));

        $mxy = date('Y').'-'.date("F", mktime(0, 0, 0, $month[$z-1], 10));


        $total = roundoffNoComma($row['salary'])+
            roundoffNoComma($row['overtime'])+
            roundoffNoComma($row1['commission'])+
            roundoffNoComma($row2['taxable'])+
            roundoffNoComma($row3['nontax'])+
            roundoffNoComma($row['contri']);

        if($z == 0){
            $lt = '';
        } else {
            $lt = $last_total[$mxy.strtoupper($row['employee_status'])];
        }


        $select4 = "Select count(*) as cnt from employee where employee_status = '" . $row['employee_status'] . "' and company_id = '" . $company . "' and date_employed <= '" . $mx . "-31'";
        $result4 = mysql_query($select4, connect());
        $row4 = mysql_fetch_row($result4,MYSQL_ASSOC);
        $headcount = $row4['cnt'];

        $select5 = "Select count(*) as cnt from employee where employee_status = '" . $row['employee_status'] . "' and company_id = '" . $company . "' and reason_living_date <= '" . $mx . "-31' and reason_living_date != '0000-00-00'";
        $result5 = mysql_query($select5, connect());
        $row5 = mysql_fetch_row($result5,MYSQL_ASSOC);
        $headcount_res = $row5['cnt'];

        $select6 = "Select count(*) as cnt from employee where employee_status = '" . $row['employee_status'] . "' and company_id = '" . $company . "' and date_employed like '" . $mx . "%' and reason_living_date != '0000-00-00'";
        $result6 = mysql_query($select6, connect());
        $row6 = mysql_fetch_row($result6,MYSQL_ASSOC);
        $head = $row6['cnt'];

        $select7 = "Select count(*) as cnt from employee where employee_status = '" . $row['employee_status'] . "' and company_id = '" . $company . "' and reason_living_date like '" . $mx . "%' and reason_living_date != '0000-00-00'";
        $result7 = mysql_query($select7, connect());
        $row7 = mysql_fetch_row($result7,MYSQL_ASSOC);
        $resigned = $row7['cnt'];



        $text = array(
            $mxx, 
            strtoupper($row['employee_status']),
            $head,
            $resigned,
            $headcount-$headcount_res,
            roundoff($row['salary']),
            '',
            '',
            roundoff($row['overtime']),
            roundoff($row1['commission']),
            roundoff($row2['taxable']),
            roundoff($row3['nontax']),
            roundoff($row['contri']),
            roundoff($total),
            roundoff($lt),
            roundoff($total - $lt),
            roundoff(($lt/$total)),
        );

        

        $excel->writeLine($text);
        $salary += roundoffNoComma($row['salary']);
        $overtime += roundoffNoComma($row['overtime']);
        $commission += roundoffNoComma($row1['commission']);
        $taxable += roundoffNoComma($row2['taxable']);
        $nontax += roundoffNoComma($row3['nontax']);
        $contri += roundoffNoComma($row['contri']);
        $totalx += $total;
        $y++;
        $last_total[$mxz.strtoupper($row['employee_status'])] = $total;
    }

    

    $text = array(
        '', 
        'TOTAL',
        '',
        '',
        '',
        roundoff($salary),
        '',
        '',
        roundoff($overtime),
        roundoff($commission),
        roundoff($taxable),
        roundoff($nontax),
        roundoff($contri),
        roundoff($totalx),
        roundoff($last_total[$mxy.'total']),
        roundoff($totalx - $last_total[$mxy.'total']),
        roundoff(($last_total[$mxy.'total']/$totalx)),
    );
    $excel->writeLine($text);

    $last_total[$mxz.'total'] = $totalx;
    //print_r($last_total);
    

    if($m == date('m')){
        break;
    }
    $z++;
}



// $column_header = array('Month', 'Company','Count');
// $excel->writeLine($column_header);

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('Newly Hired');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from employee left join company on (employee.company_id = company.id) where date_employed like '" . $m . "%' group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('Regularized');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from employee left join company on (employee.company_id = company.id) where date_permanent like '" . $m . "%' group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('Resigned');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from employee left join company on (employee.company_id = company.id) where reason_living_date like '" . $m . "%' group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('Absent');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from transaction left join company on (transaction.company_id = company.id) where trxn_date like '" . $m . "%' and transaction.status like '%ABSENT%' group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('LWOP/Unfiled');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from transaction left join company on (transaction.company_id = company.id) where trxn_date like '" . $m . "%' and (transaction.status like '%LWOP%' or transaction.status like '%unfiled%') group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

// $column_header = array('');
// $excel->writeLine($column_header);

// $column_header = array('LEAVE');
// $excel->writeLine($column_header);

// foreach ($month as $m) {
//     $select = "select count(*) as cnt, company.name  from transaction left join company on (transaction.company_id = company.id) where trxn_date like '" . $m . "%' and (transaction.status like '%LEAVE%') group by company_id" ;
//     $result = mysql_query($select, connect());
//     while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
//         $body = array($m, $row['name'],$row['cnt']);
//         $excel->writeLine($body);
//     }
// }

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=employee_forecast_" . $ymd . ".xls");
header("Location: employee_forecast_" . $ymd . ".xls");
exit;
?>
