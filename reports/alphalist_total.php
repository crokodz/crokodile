3
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("pay_register_details.xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
$column_header = array('FOR THE MONTH OF' . $_GET['payday']);

$month = ["01","02","03","04","05","06","07","08","09","10","11","12"];


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
			$select1 = "select `payday`, `sss` from `posted_summary` where `payday` = '" . $pastpayid . "' and `em_id` = '" . $em_id . "'";
			$result1 = mysql_query($select1, connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
			$ec1 = getsss($row1['sss']+$row['sss']);
			$ec2 = getsss($row1['sss']);
			return array($ec1[0] - $ec2[0], $ec1[1] - $ec2[1]);
			}
		}
	}


$excel->writeLine($column_header);
$company_id = 1;
$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');

$company_id = $_GET['compa'];
$post_type = urldecode($_GET['type']);
$company_name = $company_array[$company_id];

$excel->writeLine(array('MANAGEMENT'));


$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

$header = array(
			'Payroll #',
			'Paycode',
			'Id',
			'First Name',
			'Last Name',
			'Division',
			'File Status',
			'Basic Salary',
			'Absent/halfday',
			'Late',
			'UnderTime',
			'OverTime',
			'Other Income',
			'Total Employee SSS',
			'Total Employee MCR',
			'Total Employee PagIbig',
			'Gross Taxable',
			'Tax',
			'Non Taxable',
			'Deduction',
			'Net Income',
			'Bank Account'
);

$header = array(
    'ID',
    'NAME OF EMPLOYEE',
    'TIN',
    'BASIC 01',
    'BASIC 02',
    'BASIC 03',
    'BASIC 04',
    'BASIC 05',
    'BASIC 06',
    'BASIC 07',
    'BASIC 08',
    'BASIC 09',
    'BASIC 10',
    'BASIC 11',
    'BASIC 12',
    'TOTAL BASIC',
    'COMMISSION',
    'PROD COST',
    'OTHER INCOME',
    'TOTAL DEDUCTION',
    'ACCRUAL',
    'GROSS',
    'STATUTORY',
    'TAXABLE 13th',
    'NON-TAX 13th',
    'TAXABLE BONUS',
    'NON-TAX BONUS',
    'TAXABLE INCOME',
    'TOTAL AFTER CEILING',
    'TOTAL AFTER TAX RATE',
    'ANNUALIZED TAX',
    'TAX WITHHELD',
    'TAX 13th',
    'TAX BONUS',
    'TOTAL TAX WITHHELD',
    'TAX REFUND/(TAX DUE)'
);


// $selectx = "select * from deductions";
// $resultx = mysql_query($selectx, connect());
// while($rowx = mysql_fetch_array($resultx)){
// 	array_push($header, $rowx['name']);
// }
// array_push($header, 'TOTAL Deduction');

// $selectx = "select * from nontaxable_entry";
// $resultx = mysql_query($selectx, connect());
// while($rowx = mysql_fetch_array($resultx)){
// 	array_push($header, $rowx['name']);
// }
// array_push($header, 'TOTAL Non Taxable');

// $selectx = "select * from taxable_entry";
// $resultx = mysql_query($selectx, connect());
// while($rowx = mysql_fetch_array($resultx)){
// 	array_push($header, $rowx['name']);
// }
// array_push($header, 'TOTAL Taxable');

$excel->writeLine($header);


$select = "select
		t1.em_id,
		t2.name,
		t2.tin,
		sum(t1.sss) as sss,
		sum(t1.taxable_salary) as taxable_salary,
		group_concat(posted_id) as posted_id,
		sum(t1.ph) as ph,
		sum(t1.salary) as salary,
		sum(t1.`absent`) as absent,
		t2.division,
		sum(t1.`late`) as late,
		sum(t1.`ut`)as ut,
		sum(t1.`ot`) as ot,
		sum(t1.`other_tax_inc`) as other_taxable,
		sum(t1.`taxable_salary`) as taxable,
		sum(t1.`sss`) as sss,
		sum(t1.`ph`)as ph,
		sum(t1.`pi`) as pi,
		sum(t1.`tax`) as tax,
		sum(t1.`nontax`) as non,
		sum(t1.`netpay`) as netpay,
		sum(t1.`deduction`) as deduction,
		t2.`bank_account`
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['year'] . "%' and t1.company_id = " . $company_id . " and post_type = 'REGULAR'

		and `file_status` like 'EMPLOYEE'


	group by t1.em_id";


if($_GET['type'] == 'RESIGNED'){
	$select = "select
		t1.em_id,
		t2.name,
		t2.tin,
		sum(t1.sss) as sss,
		sum(t1.taxable_salary) as taxable_salary,
		group_concat(posted_id) as posted_id,
		sum(t1.ph) as ph,
		sum(t1.salary) as salary,
		sum(t1.`absent`) as absent,
		t2.division,
		sum(t1.`late`) as late,
		sum(t1.`ut`)as ut,
		sum(t1.`ot`) as ot,
		sum(t1.`other_tax_inc`) as other_taxable,
		sum(t1.`taxable_salary`) as taxable,
		sum(t1.`sss`) as sss,
		sum(t1.`ph`)as ph,
		sum(t1.`pi`) as pi,
		sum(t1.`tax`) as tax,
		sum(t1.`nontax`) as non,
		sum(t1.`netpay`) as netpay,
		sum(t1.`deduction`) as deduction,
		t2.`bank_account`
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['year'] . "%' and t1.company_id = " . $company_id . " and post_type = 'REGULAR'

		and `file_status` like 'EMPLOYEE'
		and (`reason_living_date` like '%" . $_GET['year'] . "%' or `reason_living_date` like '%" . date('Y') . "%')

	group by t1.em_id";
}




$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$sx1 = "select
        group_concat(DISTINCT(posted_id) SEPARATOR ',') as posted_id
        from posted_summary where em_id = '" . $row['em_id'] . "' and payday like '%" . $_GET['year'] . "%'
        and post_type='REGULAR'";
    $rx1 = mysql_query($sx1, connect());
    $wx1 = mysql_fetch_array($rx1,MYSQL_ASSOC);

    $s2 = "select sum(amount) com from employee_taxable where em_id = '" . $row['em_id'] . "'
        and name in ('COMMISSION')
        and posted_id in (" . $wx1['posted_id'] . ")
        and status = 'posted' ";
    $r2 = mysql_query($s2, connect());
    $w2 = mysql_fetch_array($r2,MYSQL_ASSOC);
    $com_total = empty($w2['com']) ? 0 : $w2['com'];

    $s2 = "select sum(amount) com from employee_taxable where em_id = '" . $row['em_id'] . "'
        and name in ('PRODUCTION COST')
        and posted_id in (" . $wx1['posted_id'] . ")
        and status = 'posted' ";
    $r2 = mysql_query($s2, connect());
    $w2 = mysql_fetch_array($r2,MYSQL_ASSOC);
    $pc_total = empty($w2['com']) ? 0 : $w2['com'];


    $mdata = [];

    $totalbasic = 0;

    foreach ($month as $k => $v) {

        $s11 = "select
            sum(t1.salary) as taxable_salary
        from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['year'] . "-" . $v . "%' and t1.company_id = " . $company_id . " and post_type = 'REGULAR'

            and `file_status` like 'EMPLOYEE'
            and t1.em_id = '". $row['em_id'] ."'

        group by t1.em_id";

        $r11 = mysql_query($s11, connect());
        $w11 = mysql_fetch_array($r11,MYSQL_ASSOC);
        $gross11 = empty($w11['taxable_salary']) ? 0 : $w11['taxable_salary'];
        array_push($mdata, $gross11);
        $totalbasic += $gross11;
    }




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


	$s2 = "select sum(amount) com from employee_taxable where em_id = '" . $row['em_id'] . "'
        and name in ('ACCRUAL - COMMISSION', 'ACCRUAL - PRODUCTION COST')
        and datetime like '%" . $_GET['year'] . "%'
        and status = 'posted'";
    $r2 = mysql_query($s2, connect());
    $w2 = mysql_fetch_array($r2,MYSQL_ASSOC);
    $accru = empty($w2['com']) ? 0 : $w2['com'];


    $totalgrosses = ($row['taxable'] - $accru) + $bonus_taxable_total + $b13_taxable_total;


 	$select_tax = "select * from annual_tax where start <= " . ($totalgrosses) . " and end >= " . ($totalgrosses);
    $tx = mysql_query($select_tax, connect());
    $wx = mysql_fetch_array($tx,MYSQL_ASSOC);


    $annual_tax = ($wx['fix_amount']) + ((($totalgrosses) - $wx['excess']) * ($wx['percent'] / 100));

    $others = ($totalbasic + $com_total + $pc_total) - ($row['taxable'] - $accru);

    if($others > 0){
        $deduction = $others;
        $others = 0;
    } else {
        $others = $others * -1;
        $deduction = 0;
    }



	$body = array(
				$row['em_id'],
				$row['name'],
				$row['tin'],
                $mdata[0],
                $mdata[1],
                $mdata[2],
                $mdata[3],
                $mdata[4],
                $mdata[5],
                $mdata[6],
                $mdata[7],
                $mdata[8],
                $mdata[9],
                $mdata[10],
                $mdata[11],
                $totalbasic,
                $com_total,
                $pc_total,
                $others,
                $deduction,
                $accru,
				$row['taxable'] - $accru,
				$row['sss']+$row['pi']+$row['ph'],
				$b13_taxable_total,
				$b13_nontaxable_total,
				$bonus_taxable_total,
				$bonus_nontaxable_total,
				$totalgrosses,
				$totalgrosses - $wx['fix_amount'] - $wx['excess'],
				((($totalgrosses) - $wx['excess']) * ($wx['percent'] / 100)),
				$annual_tax,
				$row['tax'],
				$b13_tax,
				$bonus_tax,
				($row['tax'] + $b13_tax + $bonus_tax),
				($row['tax'] + $b13_tax + $bonus_tax) - $annual_tax
			);




	// print_r($body);
	// echo '<br>';

	// // if($post_type == 'REGULAR'){
	// // 	$total = 0;
	// // 	while($rowx = mysql_fetch_array($resultx)){
	// // 		$amount  = 0;
	// // 		$select1 = "select amount from employee_deduction where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
	// // 		$result1 = mysql_query($select1, connect());
	// // 		while($row1 = mysql_fetch_array($result1)){
	// // 			$amount  = $amount + $row1['amount'];
	// // 		}
	// // 		array_push($body, $amount);
	// // 		$total = $total + $amount;
	// // 	}
	// // 	#if ($total > 0){
	// // 		array_push($body, $total);
	// // 		//$excel->writeLine($body);

	// // 	#}


	// // 	//
	// // 	$total = 0;
	// // 	$selectx = "select * from nontaxable_entry";
	// // 	$resultx = mysql_query($selectx, connect());
	// // 	while($rowx = mysql_fetch_array($resultx)){
	// // 		$amount  = 0;
	// // 		$select1 = "select amount from employee_non_taxable where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
	// // 		$result1 = mysql_query($select1, connect());
	// // 		while($row1 = mysql_fetch_array($result1)){
	// // 			$amount  = $amount + $row1['amount'];
	// // 		}
	// // 		array_push($body, $amount);
	// // 		$total = $total + $amount;
	// // 	}
	// // 	#if ($total > 0){
	// // 		array_push($body, $total);
	// // 		//$excel->writeLine($body);

	// // 	#}

	// // 	//
	// // 	$total = 0;
	// // 	$selectx = "select * from taxable_entry";
	// // 	$resultx = mysql_query($selectx, connect());
	// // 	while($rowx = mysql_fetch_array($resultx)){
	// // 		$amount  = 0;
	// // 		$select1 = "select amount from employee_taxable where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
	// // 		$result1 = mysql_query($select1, connect());
	// // 		while($row1 = mysql_fetch_array($result1)){
	// // 			$amount  = $amount + $row1['amount'];
	// // 		}
	// // 		array_push($body, $amount);
	// // 		$total = $total + $amount;
	// // 	}
	// // }
	// 	#if ($total > 0){
	// 		array_push($body, $total);
			$excel->writeLine($body);

		#}
}



header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=pay_register_details.xls');
header("Location: pay_register_details.xls");
exit;

?>
