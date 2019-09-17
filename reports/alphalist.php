
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("alphalist_" .$ymd. ".xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
$excel->writeLine($column_header);

$w1 = str_replace("w1", "First Payday of ", $_GET['payday']);
if($w1 == $_GET['payday']){
    $w1 = str_replace("w2", "Second Payday of ", $_GET['payday']);
}

$month = ["01","02","03","04","05","06","07","08","09","10","11","12"];
$monthname = ["January","February","March","April","May","June","July","August","September","October","November","December"];
$month_spacer = ["","","","","","","","","","",""];

$company_id = $_GET['compa'];



function getsss($salary,$cnfsss){
    if($cnfsss == 'YES'){
        $select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
        $result = mysql_query($select, connect());
        $row = mysql_fetch_array($result,MYSQL_ASSOC);
        return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
        }
    else{
        return array('0','0','0','0');
        }
    }

function getph($salary,$cnfph){
    if($cnfph == 'YES'){
        $select = "select `id`,`ees`,`ers` from ph where `from` <= '" . $salary . "' order by `from` desc limit 1";
        $result = mysql_query($select, connect());
        $row = mysql_fetch_array($result,MYSQL_ASSOC);
        return array($row['id'],$row['ees'],$row['ers']);
        }
    else{
        return array('0','0','0');
        }
    }

function getEC($y,$pid,$em_id){
    //Todo check the adjustment
    if($y==1){
        $select = "select `payday`, `sss` from `posted_summary` where `posted_id` = '" . $pid . "' and `em_id` = '" . $em_id . "'";
        $result = mysql_query($select, connect());
        $row = mysql_fetch_array($result,MYSQL_ASSOC);
        $s = explode("@",$row['payday']);
        $ec = getsss($row['sss']);

        if($s[0] == 'w1'){
            return array($ec[0],$ec[1]);
            }
        else{
            $pastpayid = 'w1@' . $s[1];
            $select1 = "select `payday`, `sss` from `posted_summary` where `payday` = '" . $pastpayid . "' and `em_id` = '" . $em_id . "' and post_type = 'REGULAR'";
            $result1 = mysql_query($select1, connect());
            $row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
            $ec1 = getsss($row1['sss']+$row['sss']);
            $ec2 = getsss($row1['sss']);
            return array($ec1[0] - $ec2[0], $ec1[1] - $ec2[1]);
            }
        }
    }

function getpi($salary,$cnfpi,$pdm){
    $pdm = $pdm * 2;
    if($cnfpi == 'YES'){
        if ($salary <= 1500){
            return $salary * .02;
            }
        else{
            if ($pdm > 0){
                return array($pdm+100,100);
            } else {
                return array(0,0);
            }

            }
        }
    else{
        if ($pdm > 0){
            return array($pdm,100);
        } else {
            return array(0,0);
        }
    }
}


$column_header = array($company_name . ' - Alphalist');
$excel->writeLine($column_header);

$column_header = array('For - ' . $_GET['type']);
$excel->writeLine($column_header);

$column_header = array('Year ' . $_GET['year']);
$excel->writeLine($column_header);

$column_header = array('');
$excel->writeLine($column_header);


$s1 = "select
    sum(salary) as salary,
    sum(taxable_salary) as taxable_salary,
    sum(ot) as ot,
    sum(nontax) as nontax,
    sum(other_tax_inc) as other_tax_inc,
    sum(tax) as tax,
    sum(sss) as sss,
    sum(ph) as ph,
    sum(pi) as pi,
    sum(absent+late+ut) as adj,
    sum(ot - (ut+late+absent)) as otul,
    group_concat(DISTINCT(posted_id) SEPARATOR ',') as posted_id
    from posted_summary where em_id = '" . $row['em_id'] . "' and payday like '%@" . $_GET['year'] . "-" . $v . "'
    and post_type='REGULAR'";
$r1 = mysql_query($s1, connect());
$w1 = mysql_fetch_array($r1,MYSQL_ASSOC);


if($company_id == 7){
    if($_GET['type'] == 'RESIGNED'){
        $select = "select * from employee where company_id = " . $company_id . " and status = 'deleted' and em_id not like '%xx%' and
        ts != 'MWE' and
        file_status not like 'TALENTS-IBMI' and
        `file_status` NOT  LIKE 'consultant' and
         (`reason_living_date` like '%" . $_GET['year'] . "%' or `reason_living_date` like '%" . date('Y') . "%') order by lname, fname Asc";
    } else if($_GET['type'] == 'TALENTS-IBMI'){
        $select = "select * from employee where company_id = " . $company_id . " and file_status like 'TALENTS-IBMI' order by lname, fname Asc";
    } else if($_GET['type'] == 'MWE'){
        $select = "select * from employee where company_id = " . $company_id . " and ts = 'MWE' order by lname, fname Asc";
    } else if($_GET['type'] == 'RNF'){
        $select = "select * from employee where
        company_id = " . $company_id . " and
        ts != 'MWE' and
        em_id not like '%xx%' and
        file_status not like 'TALENTS-IBMI' and
        `file_status` NOT  LIKE 'consultant'
        order by lname, fname Asc";
    } else {
        $select = "select * from employee where
        company_id = " . $company_id . " and
        status != 'deleted' and
        em_id not like '%xx%' and
        file_status not like 'TALENTS-IBMI' and
        `file_status` NOT  LIKE 'consultant'
        order by lname, fname Asc";
    }
} else {
    if($_GET['type'] == 'RESIGNED'){
        $select = "select distinct t2.* from posted_summary t1 left join employee t2 on t1.em_id = t2.em_id where t2.company_id = " . $company_id . " and (t2.reason_living_date like '%" . $_GET['year'] . "%' or `reason_living_date` like '%" . date('Y') . "%') and (t2.status = 'deleted' or t2.status = 'inactive') and t1.payday like '%" . $_GET['year'] . "%'
        and file_status not like 'DRAMA-TALENTS-RMN'
        and `file_status` NOT  LIKE 'consultant'
        order by t2.lname, t2.fname Asc";
    // } else if($_GET['type'] == 'MWE'){
    //  $select = "select * from employee where company_id = " . $company_id . " and ts = 'MWE' and pay_id not like '%DRAMA%' order by lname, fname Asc";
    // } else if($_GET['type'] == 'DRAMA-TALENT'){
    //  $select = "select * from employee where company_id = " . $company_id . " and pay_id like '%DRAMA%' order by lname, fname Asc";
    // } else if($_GET['type'] == $_GET['year'] . '-BATCH1'){
    //  $select = "select * from employee where company_id = " . $company_id . " and special_annual = 1 and pay_id not like '%DRAMA%' order by lname, fname Asc";
    // } else if($_GET['type'] == 'RNF'){
    //  $select = "select * from employee where
    //  company_id = " . $company_id . " and
    //  pay_id not like '%DRAMA%' and
    //  em_id not like '%xx%' and
 //        `file_status` NOT  LIKE 'consultant'
    //  order by lname, fname Asc";
    } else {
        $select = "select distinct t2.* from posted_summary t1 left join employee t2 on t1.em_id = t2.em_id where t2.company_id = " . $company_id . " and (t2.status = 'active') and t1.payday like '%" . $_GET['year'] . "%'
        and file_status not like 'DRAMA-TALENTS-RMN'
        and `file_status` NOT  LIKE 'consultant'
         order by t2.lname, t2.fname Asc";
        // $select = "select * from employee where
        // company_id = " . $company_id . " and
        // status != 'deleted' and
        // special_annual != 1 and
        // pay_id not like '%DRAMA%' and
        // em_id not like '%xx%' and
  //       `file_status` NOT  LIKE 'consultant'
        // order by lname, fname Asc";
    }
}


$result = mysql_query($select, connect());

$basic = 0;
$nontax = 0;
$talent = 0;
$ot = 0;
$other = 0;
$adjustment = 0;
$tax = 0;
$sss = 0;
$ph = 0;
$pi = 0;
$gross = 0;


/*
$column_header = array_merge(array('','','','','Basic'), $month_spacer);
array_push($column_header, '','','','Non-taxable');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '','Taxable');
$excel->writeLine($column_header);
*/


$column_header = array(
    '',
    '',
    '',
    'BASIC'
);
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '', 'OT + Adjustment(OT ADJUSTMENT) - (Late + Undertime + Absent)');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '','','','','','','', 'W/H TAX', '');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header,'NON-TAXABLE','');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '','SSS', '');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '','PHILHEALTH');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '','PAG-IBIG');
$column_header = array_merge($column_header, $month_spacer);
array_push($column_header, '');

$excel->writeLine($column_header);



$column_header = array(
    'ID',
    'Name',
    'Tin',
);
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'Total Basic');
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'Total OT + Adjustment(OT ADJUSTMENT) - (Late + Undertime + Absent)','Other Taxable Income (comm, prod cost, fees, pf)','Taxable 13th Month','Taxable Bonus', 'Accrual COM','Accrual PC', 'Gross Taxable');
$column_header = array_merge($column_header, $monthname);



array_push($column_header, 'Total Tax');
array_push($column_header, 'Non-Taxable 13th Month');
array_push($column_header, 'Non-Taxable Bonus');
//array_push($column_header, 'Gross Nontaxable (13th + Bonus + Nontax like allowance, etc');
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'Professional Fee');
array_push($column_header, 'Total Non-taxable');
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'SSS Adjustment for Dec');
array_push($column_header, 'Total SSS');
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'Total PhilHealth');
$column_header = array_merge($column_header, $monthname);
array_push($column_header, 'Total Pagibig');

//array_push($column_header, 'Total OT');
//array_push($column_header, 'Total Adjustment');
//array_push($column_header, 'Total Late/Undertime/Absent');
//array_push($column_header, 'Commision January');
//array_push($column_header, 'Commision February');
// array_push($column_header, 'Awardee Amount');
// array_push($column_header, 'Awardee Tax');
// array_push($column_header, 'Jan Com');
// array_push($column_header, 'Feb Com');
// array_push($column_header, 'Accrual Com');

// array_push($column_header, 'Total Regular Com');
// array_push($column_header, 'Taxable Bonus');
// array_push($column_header, 'Total Gross');
array_push($column_header, 'Adjustment');
array_push($column_header, 'Total Govermental Deduction (SSS+Pagibig+PhilHealth+Adjustment)');
//array_push($column_header, 'Non-Taxable Bonus');

array_push($column_header, 'Total Gross (Less Govermental Deduction)');

array_push($column_header, 'Annual Tax (Should BE)');
array_push($column_header, '13th Month Tax');
array_push($column_header, 'Bonus Tax');
array_push($column_header, 'Total W/H Tax');
array_push($column_header, 'Tax Refund / Tax Due');

//$column_header = array_merge($column_header, $monthname);


$excel->writeLine($column_header);

while($row = mysql_fetch_array($result)){

    $sx1 = "select
        group_concat(DISTINCT(posted_id) SEPARATOR ',') as posted_id
        from posted_summary where em_id = '" . $row['em_id'] . "' and payday like '%" . $_GET['year'] . "%'
        and post_type='REGULAR'";
    $rx1 = mysql_query($sx1, connect());
    $wx1 = mysql_fetch_array($rx1,MYSQL_ASSOC);




    $column_data = array(
        $row['em_id'],
        $row['lname'] . ", " . $row['fname'] . " " . $row['mname'],
        $row['tinn']
    );

    $salarytotal = 0;
    $ottotal = 0;
    $ottotal1 = 0;
    $com_total = 0;
    $nontax_total = 0;
    $other_tax_inc_total = 0;
    $taxtotal = 0;
    $ssstotal = 0;
    $sawtotal = 0;
    $phtotal = 0;
    $pitotal = 0;
    $nontax_data = array();
    $tax_data = array();

    $ssstotal = 0;
    $phtotal = 0;
    $pitotal = 0;
    $adjtotal = 0;
    $sss_data = array();
    $ph_data = array();
    $pi_data = array();
    $adj_data = array();
    $saw_data = array();
    $saw_data1 = array();
    //$other_tax_inc_data = array();
    foreach ($month as $k => $v) {
        $s1 = "select
            sum(salary) as salary,
            sum(taxable_salary) as taxable_salary,
            sum(ot) as ot,
            sum(nontax) as nontax,
            sum(other_tax_inc) as other_tax_inc,
            sum(tax) as tax,
            sum(sss) as sss,
            sum(ph) as ph,
            sum(pi) as pi,
            sum(absent+late+ut) as adj,
            sum(ot - (ut+late)) as otul,
            group_concat(DISTINCT(posted_id) SEPARATOR ',') as posted_id
            from posted_summary where em_id = '" . $row['em_id'] . "' and payday like '%@" . $_GET['year'] . "-" . $v . "'
            and post_type='REGULAR'";
        $r1 = mysql_query($s1, connect());
        $w1 = mysql_fetch_array($r1,MYSQL_ASSOC);
        $salary = empty($w1['salary']) ? 0 : $w1['salary'];
        $ot = empty($w1['ot']) ? 0 : $w1['ot'];
        $nontax = empty($w1['nontax']) ? 0 : $w1['nontax'];
        $other_tax_inc = empty($w1['other_tax_inc']) ? 0 : $w1['other_tax_inc'];
        $tax = empty($w1['tax']) ? 0 : $w1['tax'];
        $sss = empty($w1['sss']) ? 0 : $w1['sss'];
        $ph = empty($w1['ph']) ? 0 : $w1['ph'];
        $pi = empty($w1['pi']) ? 0 : $w1['pi'];
        $adj = empty($w1['adj']) ? 0 : $w1['adj'];
        $otul = empty($w1['otul']) ? 0 : $w1['otul'];


        // $s2 = "select sum(amount) com from employee_taxable where em_id = '" . $row['em_id'] . "'
        //     and name in ('ACCRUAL - COMMISSION', 'ACCRUAL - PRODUCTION COST')
        //     and datetime like '%" . $_GET['year'] . "%'
        //     and status = 'posted'";
        // $r2 = mysql_query($s2, connect());
        // $w2 = mysql_fetch_array($r2,MYSQL_ASSOC);
        // $accru = empty($w2['com']) ? 0 : $w2['com'];


        $taxable_salary = empty($w1['taxable_salary']) ? 0 : $w1['taxable_salary'];



        $salarytotal += $salary;
        $ottotal += $ot;
        $nontax_total += $nontax;
        $other_tax_inc_total += $other_tax_inc;
        $taxtotal += $tax;
        $ssstotal += $sss;
        $phtotal += $ph;
        $pitotal += $pi;
        $adjtotal += $adj;



        array_push($column_data, $salary);
        array_push($nontax_data, $nontax);
        array_push($other_tax_inc_data, $other_tax_inc);
        array_push($tax_data, $tax);
        array_push($sss_data, $sss);
        array_push($ph_data, $ph);
        array_push($pi_data, $pi);
        array_push($adj_data, $adj);


        // if($v == '01'){
        //  $saw = $taxable_salary;
        //  $sawtotal += $saw;
        //  // array_push($saw_data, $saw);

        //  $saw1 = ($saw) - $salary;
        //  // array_push($saw_data1, $saw1);

        //  if($_GET['compa'] == 6) {
        //      array_push($saw_data, 0);
        //      array_push($saw_data1, 0);
        //  } else {
        //      array_push($saw_data, $saw);
        //      array_push($saw_data1, $saw1);
        //  }
        // } else if($v == '02'){
        //  $s3x = "select sum(amount) ot from employee_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where
        //      t1.name in
        //      ('ADJUSTMENT',
        //      'OT ADJUSTMENT',
        //      'OVERTIME',
        //      'OVERTIME ADJ.',
        //      'RETRO PAY',
        //      'SALARY ADJUSTMENT') and
        //      t1.posted_id in (" . $w1['posted_id'] . ") and t1.em_id = '" . $row['em_id'] . "'";
        //  $r3x = mysql_query($s3x, connect());
        //  $w3x = mysql_fetch_array($r3x,MYSQL_ASSOC);
        //  $ottotal1x = empty($w3x['ot']) ? 0 : $w3x['ot'];

        //  $saw = ($salary + $ot + $ottotal1x) - ($late + $ut + $absent);
        //  $sawtotal += $saw;
        //  // array_push($saw_data, $saw);

        //  //$saw1 = ($saw) - $salary;

        //  // array_push($saw_data1, $saw1);
        //  $saw1 =
        //  if($_GET['compa'] == 6) {
        //      array_push($saw_data, 0);
        //      array_push($saw_data1, 0);
        //  } else {
        //      array_push($saw_data, $saw);
        //      array_push($saw_data1, $saw1);
        //  }

        // } else {
            $select1x = "select sum(amount) as amount, name from employee_deduction where status = 'posted' and posted_id in (" . $w1['posted_id'] . ") and name = 'DWOP' and em_id = '" . $row['em_id'] . "' group by name";
            $result1x = mysql_query($select1x, connect());
            $row1x = mysql_fetch_array($result1x);
            $dwop = $row1x['amount'];


            $sx = "select  sum(t1.amount) as amount from employee_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where  t1.posted_id in (" . $w1['posted_id'] . ") and t1.em_id = '" . $row['em_id'] . "'";
            $rx = mysql_query($sx, connect());
            $wx = mysql_fetch_array($rx,MYSQL_ASSOC);
            $taxtx = $wx['amount'];


            $s3x = "select sum(amount) ot from employee_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where
                t1.name in
                ('ADJUSTMENT',
                'OT ADJUSTMENT',
                'OVERTIME',
                'OVERTIME ADJ.',
                'RETRO PAY',
                'SALARY ADJUSTMENT') and
                t1.posted_id in (" . $w1['posted_id'] . ") and t1.em_id = '" . $row['em_id'] . "'";
            $r3x = mysql_query($s3x, connect());
            $w3x = mysql_fetch_array($r3x,MYSQL_ASSOC);
            $ottotal1x = empty($w3x['ot']) ? 0 : $w3x['ot'];


            $s3x = "select sum(amount) ot from employee_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where
                t1.name in
                ('ADDITIONAL INCOME') and
                t1.posted_id in (" . $w1['posted_id'] . ") and t1.em_id = '" . $row['em_id'] . "'
                and (t2.pay_id = 'I-FM Davao' or t2.pay_id = 'DXDC-Davao' or t1.em_id = 'SOR-101')";
            $r3x = mysql_query($s3x, connect());
            $w3x = mysql_fetch_array($r3x,MYSQL_ASSOC);
            $ottotal2x = empty($w3x['ot']) ? 0 : $w3x['ot'];

            //$saw = (($taxable_salary - $taxtx) - ($otul+$dwopt))  + $otul + $ottotal1x + $ottotal2x;
            $saw = ($ot - $adj);


            $sawtotal += $saw;
            $saw1 = ($saw);


            if($_GET['compa'] == 6) {
                array_push($saw_data, 0);
                array_push($saw_data1, 0);
            } else {
                array_push($saw_data, $saw);
                array_push($saw_data1, $saw1);
            }
        // }
    }


    // $s2 = "select sum(amount) com from employee_taxable where name in
    //  ('TALENT - ALLOWANCE',
    //  'TALENT - COMMISSION',
    //  'TALENT - TALENT FEE',
    //  'REG. EMPLOYEE COMMISSION (RE COM.)',
    //  'REG. EMPLOYEE TALENT FEE (RE TF)',
    //  'PRODUCTION COST',
 //        '13th Month Pay',
    //  'COMMISSION',
    //  'ADDITIONAL INCOME')
    //  and em_id = '" . $row['em_id'] . "'
    //  and posted_id in (" . $wx1['posted_id'] . ")
    //  and status = 'posted'";



    $s2 = "select sum(amount) com from employee_taxable where em_id = '" . $row['em_id'] . "'
        and name not in ('ACCRUAL - COMMISSION', 'ACCRUAL - PRODUCTION COST')
        and posted_id in (" . $wx1['posted_id'] . ")
        and status = 'posted'";
    $r2 = mysql_query($s2, connect());
    $w2 = mysql_fetch_array($r2,MYSQL_ASSOC);
    $com_total = empty($w2['com']) ? 0 : $w2['com'];

    // $s3 = "select sum(amount) ot from employee_taxable where name in
    //  ('ADJUSTMENT',
    //  'COMPANY ALLOW.',
    //  'OT ADJUSTMENT',
    //  'OVERTIME',
    //  'OVERTIME ADJ.',
    //  'RETRO PAY',
    //  'SALARY ADJUSTMENT')
    //  and em_id = '" . $row['em_id'] . "'
    //  and status = 'posted'";
    // $r3 = mysql_query($s3, connect());
    // $w3 = mysql_fetch_array($r3,MYSQL_ASSOC);
    // $ottotal1 = empty($w3['ot']) ? 0 : $w3['ot'];

    // $ottotal1 = $total - $sawtotal;

    $s13 = "select
        sum(nontax) as nontax,
        sum(other_tax_inc) as other_tax_inc,
        sum(tax) as tax
        from posted_summary where em_id = '" . $row['em_id'] . "'
        and payday like '%" . $_GET['year'] . "%'
        and (post_type='HALF 13TH MONTH' OR post_type='WHOLE 13TH MONTH')
        ";
    $r13 = mysql_query($s13, connect());
    $w13 = mysql_fetch_array($r13,MYSQL_ASSOC);
    $b13_taxable_total = empty($w13['other_tax_inc']) ? 0 : $w13['other_tax_inc'];
    $b13_nontaxable_total = empty($w13['nontax']) ? 0 : $w13['nontax'];
    $b13_tax = empty($w13['tax']) ? 0 : $w13['tax'];


    $s4 = "select
        sum(nontax) as nontax,
        sum(other_tax_inc) as other_tax_inc,
        sum(tax) as tax
        from posted_summary where em_id = '" . $row['em_id'] . "'
        and payday like '%" . $_GET['year'] . "%'
        and (post_type='BONUS')
        ";
    $r4 = mysql_query($s4, connect());
    $w4 = mysql_fetch_array($r4,MYSQL_ASSOC);
    $bonus_taxable_total = empty($w4['other_tax_inc']) ? 0 : $w4['other_tax_inc'];
    $bonus_nontaxable_total = empty($w4['nontax']) ? 0 : $w4['nontax'];
    $bonus_tax = empty($w4['tax']) ? 0 : $w4['tax'];


    $s5 = "select sum(amount) ded from employee_deduction where name in
        ('ADJ.- SSS CONTRI',
        'ADJ. PAGIBIG CONTRI',
        'ADJ. PHILHEALTH CONTRI',
        '13TH MONTH PAY',
        'PAGIBIG-ADJ')
        and em_id = '" . $row['em_id'] . "'
        and posted_id in (" . $wx1['posted_id'] . ")
        and status = 'posted'";
    $r5 = mysql_query($s5, connect());
    $w5 = mysql_fetch_array($r5,MYSQL_ASSOC);
    $dedtotal = empty($w5['ded']) ? 0 : $w5['ded'];


    $s6 = "select sum(amount) pf from employee_non_taxable where name in
        ('PF - OTHERS',
        'PF - TRANS ALLOW',
        'PF-DAVIS ALLOWANCE',
        'PF-SANTUYO ALLOWANCE',
        'PF-TAYAO ALLOWANCE',
        'PF-TOMAS ALLOWANCE',
        'PF-VILLASICA OTHERS',
        'PF-VILLASICA TRANS ALLOW')
        and em_id = '" . $row['em_id'] . "'
        and posted_id in (" . $wx1['posted_id'] . ")
        and status = 'posted'";
    $r6 = mysql_query($s6, connect());
    $w6 = mysql_fetch_array($r6,MYSQL_ASSOC);
    $pftotal = empty($w6['pf']) ? 0 : $w6['pf'];


    // $s6 = "select sum(amount) pf from employee_non_taxable where
    //  em_id = '" . $row['em_id'] . "'
    //  and posted_id in (" . $wx1['posted_id'] . ")
    //  and status = 'posted'";
    // $r6 = mysql_query($s6, connect());
    // $w6 = mysql_fetch_array($r6,MYSQL_ASSOC);
    // $nontotal1 = empty($w6['pf']) ? 0 : $w6['pf'];

    $ottotal = 0;


    //$ottotal1 = $sawtotal - $salarytotal;
    $ottotal1 = $sawtotal;


    array_push($column_data, $salarytotal);
    $column_data = array_merge($column_data, $saw_data1);


    if($_GET['compa'] == 6) {
        $ottotal1 = 0;
        array_push($column_data, $ottotal1);
    } else {
        array_push($column_data, $ottotal1);
    }


    array_push($column_data, $com_total);
    array_push($column_data, $b13_taxable_total);
    array_push($column_data, $bonus_taxable_total);
    $gross = $salarytotal + $ottotal + $ottotal1 +   $com_total + $bonus_taxable_total + $b13_taxable_total;

    $gross = $gross + $row['commission'] + $row['production_cost'];

    array_push($column_data, $row['commission']);
    array_push($column_data, $row['production_cost']);




    array_push($column_data, $gross);
    $column_data = array_merge($column_data, $tax_data);
    array_push($column_data, $taxtotal);
    array_push($column_data, $b13_nontaxable_total);
    array_push($column_data, $bonus_nontaxable_total);
    //array_push($column_data, $gross+$b13_nontaxable_total + $bonus_nontaxable_total);
    $column_data = array_merge($column_data, $nontax_data);
    array_push($column_data, $pftotal);
    array_push($column_data, $nontax_total + $b13_nontaxable_total + $bonus_nontaxable_total);
    $column_data = array_merge($column_data, $sss_data);


    $ssstotal = $ssstotal + $row['sss_accru'];
    array_push($column_data, $row['sss_accru']);

    array_push($column_data, $ssstotal);
    $column_data = array_merge($column_data, $ph_data);
    array_push($column_data, $phtotal);
    $column_data = array_merge($column_data, $pi_data);
    array_push($column_data, $pitotal);



    if($row['ts'] == 'S' || $row['ts'] == 'ME' || $row['ts'] == 'Z'){
        $ex = 50000;
    } else if ($row['ts'] == 'MWE'){
        $ex = 0;
    } else if ($row['ts'] == 'ME1'){
        $ex = 75000;
    } else if ($row['ts'] == 'ME2'){
        $ex = 100000;
    } else if ($row['ts'] == 'ME3'){
        $ex = 125000;
    } else if ($row['ts'] == 'ME4'){
        $ex = 150000;
    }

    //$com_total = $com_total + $row['misc1'] + $row['misc2'] + $row['misc3'] + $row['award_amount'];
    //$com_total += $row['misc2'];
    //$com_total += $row['misc3'];
    //$com_total += $row['extra_bonus'];
    //$com_total += $row['award_amount'];
    // $taxtotal += $row['misc1_tax'];
    // $taxtotal += $row['misc2_tax'];
    // $taxtotal += $row['misc3_tax'];
    // $taxtotal += $row['extra_bonus_tax'];
    #$taxtotal += ($bonus_tax + $b13_tax);




    #$gross = ($salarytotal + $ottotal + $ottotal1 + $com_total + ($b13_taxable_total + $bonus_taxable_total)) - $adjtotal;
    $ex = empty($gross) ? 0 : $ex;



    //array_push($column_data, $salarytotal);
    //array_push($column_data, $ottotal);
    //array_push($column_data, $ottotal1);
    //array_push($column_data, $adjtotal);
    //array_push($column_data, $row['misc1']);
    //array_push($column_data, $row['misc2']);
    // array_push($column_data, $row['award_amount']);
    // array_push($column_data, $row['award_tax']);
    // array_push($column_data, $row['misc1']);
    // array_push($column_data, $row['misc2']);
    // array_push($column_data, $row['misc3']);
    // array_push($column_data, $com_total);
    // array_push($column_data, $bonus_taxable_total);
    // array_push($column_data, $gross + $bonus_nontaxable_total);
    array_push($column_data, $dedtotal);



    $totalgrosses = ((($gross - $dedtotal) - $phtotal) - $ssstotal) - $pitotal;

    array_push($column_data, $dedtotal + $phtotal + $ssstotal + $pitotal);
    //array_push($column_data, $bonus_nontaxable_total);

    $select_tax = "select * from annual_tax where start <= " . ($totalgrosses) . " and end >= " . ($totalgrosses);
    $tx = mysql_query($select_tax, connect());
    $wx = mysql_fetch_array($tx,MYSQL_ASSOC);

    //if($_GET['type'] == 'MWE' || $row['ts'] == 'MWE'){
    //  $annual_tax = 0;
    //} else {
    $annual_tax = ($wx['fix_amount']) + ((($totalgrosses) - $wx['excess']) * ($wx['percent'] / 100));
    //}


    $annual_tax = $annual_tax < 0 ? 0 : $annual_tax;


    array_push($column_data, $totalgrosses);


    array_push($column_data, $annual_tax);
    array_push($column_data, $b13_tax);
    array_push($column_data, $bonus_tax);
    // array_push($column_data, $row['misc1_tax']);
    // array_push($column_data, $row['misc2_tax']);
    // array_push($column_data, $row['misc3_tax']);
    // array_push($column_data, $row['extra_bonus_tax']);





    array_push($column_data, $taxtotal);

    if ($company_id == 6 and $row['file_status'] == 'CONSULTANT') {
        array_push($column_data, 0);
    } else {
        array_push($column_data, $taxtotal - $annual_tax);
    }



    // $column_data = array_merge($column_data, $saw_data);
    // array_push($column_data, $row['department']);
    $excel->writeLine($column_data);
}

header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=alphalist_'.$ymd.'.xls');
header("Location: alphalist_".$ymd.".xls");
exit;

?>
