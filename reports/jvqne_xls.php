
<?php
require ('../config.php');
include_once("xlsxwriter.class.php");
$ymd = date("FjYgia");
//$excel=new ExcelWriter("jv_" . $ymd . ".xls");


$column_header1 = array(
	"DocCode", "DocDate", "Description", "Currency", "CurrencyRate", "IsTaxInclusive", "TaxDate", "IsCancelled", "", "GLAccount", "Description", "Project", "CostCentre", "ReferenceNo", "DebitForeign", "CreditForeign", "TaxCode", "IsTaxInclusive", "TaxRate"
);


$ho = ['700-000101', '700-000102', '700-000104', '700-000105', '700-000106'];
$pr = ['810-000101', '810-000102', '810-000104', '810-000105', '810-000106'];


$pr1 = array(
	"ALLOWANCE" => array("700-000112", "COS – TRANSAPORTATION ALLOWANCE"),
	"TEMP ALLOW" => array("700-000112", "COS – TRANSAPORTATION ALLOWANCE"),
	"TRANS ALLOW" => array("700-000112", "COS – TRANSAPORTATION ALLOWANCE"),
	"COMMISSION" => array("810-000200", "COMMISSION"),
	"PRODUCTION COST" => array("700-000200", "COS - PRODUCTION COST"),
	"ADDITIONAL INCOME" => array("700-000203","COS - PRODUCTION COST - TALENT FEE"),
	"COMPANY ALLOW." => array("700-000112", "COS – TRANSAPORTATION ALLOWANCE"),
	"SPECIAL ALLOW" => array("700-000112", "COS – TRANSAPORTATION ALLOWANCE"),
	"SALARY ADJUSTMENT" => array("700-000101","COS - SALARIES AND WAGES"),
	"OT ADJUSTMENT" =>  array("700-000102","COS - OVERTIME"),
	"PF-VILLASICA OTHERS" => array("700-000506","COS - LEGAL AND PROFESSIONAL FEE"),
	"PF-SANTUYO ALLOWANCE" => array("700-000506","COS - LEGAL AND PROFESSIONAL FEE"),
	"TAX DEFECIENCY" => array("104-000102","ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
	"ADJ. RMNECA" => array("303-000101","ACCOUNTS PAYABLE - RMNECA")
);

$ho1 = array(
	"ALLOWANCE" => array("810-000111", "TRANSAPORTATION ALLOWANCE"),
	"TEMP ALLOW" => array("810-000111", "TRANSAPORTATION ALLOWANCE"),
	"TRANS ALLOW" => array("810-000111", "TRANSAPORTATION ALLOWANCE"),
	"COMMISSION" => array("810-000200", "COMMISSION"),
	"COMPANY ALLOW." => array("810-000111","TRANSAPORTATION ALLOWANCE"),
	"SPECIAL ALLOW" => array("810-000111","TRANSAPORTATION ALLOWANCE"),
	"SALARY ADJUSTMENT" => array("810-000101","SALARIES AND WAGES"),
	"OT ADJUSTMENT" =>  array("810-000102","OVERTIME"),
	"PF-VILLASICA OTHERS" => array("700-000506","COS - LEGAL AND PROFESSIONAL FEE"),
	"PF-SANTUYO ALLOWANCE" => array("700-000506","COS - LEGAL AND PROFESSIONAL FEE"),
	"TAX DEFECIENCY" => array("104-000102","ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
	"ADJ. RMNECA" => array("303-000101","ACCOUNTS PAYABLE - RMNECA")
);






$writer = new XLSXWriter();
//$writer->writeSheet(array($column_header1),'Sheet1');

$dataxx = array();
$dataxx[] = $column_header1;

function getsss($salary,$cnfsss){
	$select = "select id, ssee, sser, ec from sss where `ssee` >= '" . $salary . "' order by `ssee` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
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

function getpi($salary,$cnfpi,$pdm){
	$pdm = $pdm * 2;
	if($cnfpi == 'YES'){
		if ($salary <= 1500){
			return array($pdm+100,100);
			}
		else{
			if ($pdm > 0){
				return array($pdm+100,100);
			} else {
				return array(100,100);
			}

			}
		}
	else{
		if ($pdm > 0){
			return array(0,0);
		} else {
			return array(0,0);
		}
	}
}

//$excel->writeLine($column_header1);
//$excel->writeLine($column_header);
$company_id = 1;
$company_name = 'RMN INC';



$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');
$company_id = $_GET['compa'];
$company_name = $company_array[$company_id];
// $excel->writeLine(array($company_name));

// $excel->writeLine(array('', ''));
// $excel->writeLine(array('', ''));

// $excel->writeLine(array('<b>ACCOUNT</b>', '', '<b>SL</b>','<b>AMOUNT</b>',''));
// $excel->writeLine(array('<b>CODE</b>', '<b>ACCOUNT NAME</b>', '<b>DR(CR)</b>','<b>DEBIT</b>','<b>CREDIT</b>'));


$talentfee = 0;

$select = "select
 	t2.pay_id,
 	sum(netpay-(t1.sss+t1.ph+t1.pi+t1.deduction+(t1.ot - (t1.ut+t1.late)))) as netpay,
	sum(t1.ot - (t1.ut+t1.late)) as ot,
	sum(t1.sss) as sss,
	sum(t1.ph) as ph,
	sum(t1.pi) as pi,
	sum(t1.tax) as tax,
	sum(t1.deduction) as deduction,
	sum(t1.taxable_salary) as taxable_salary,
	sum(t1.nontax) as nontax,
	group_concat(DISTINCT(t1.posted_id) SEPARATOR ',') as posted_id
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t2.pay_id";

if($company_id == 6 || $company_id == 1){
	$select = "select
 	t2.pay_id,
 	t1.department,
 	sum(netpay-(t1.sss+t1.ph+t1.pi+t1.deduction+(t1.ot - (t1.ut+t1.late)))) as netpay,
	sum(t1.ot - (t1.ut+t1.late)) as ot,
	sum(t1.sss) as sss,
	sum(t1.ph) as ph,
	sum(t1.pi) as pi,
	sum(t1.tax) as tax,
	sum(t1.deduction) as deduction,
	sum(t1.taxable_salary) as taxable_salary,
	sum(t1.nontax) as nontax,
	group_concat(DISTINCT(t1.posted_id) SEPARATOR ',') as posted_id
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t1.department";
}

$result = mysql_query($select, connect());

$ssstx = 0;
$phtx = 0;
$pitx = 0;
$taxx = 0;
$totalx = 0;
$basictax = 0;
$talenttax = 0;
$ded = 0;
$totalx1 = 0;


$monthname = ["","JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"];


$payday = explode("@", $_GET['payday']);
$month = explode("-", $payday[1]);
$monthnum = $month[1]*1;
$week = date('t', $ts);
$first = '16';
$ts = strtotime('APR ' . $monthname[$monthnum]);

if($payday[0] == 'w1'){
	$first = '1';
	$week = '15';
}


$title = "JV" . $week . $month[1] . "/00" . $company_id;
$datetitle = $week . "/" . $month[1] . "/" . $month[0];




$desc1 = array("1"=>"RANK AND FILE", "6"=>"CONFIDENTIAL", "7"=>"IBMI");



$destitle = $desc1[$company_id] . " PAYROLL FOR THE PERIOD " . $monthname[$monthnum] . " " . $first . "-" . $week . ", " . $month[0];
$datetitle2 = $week . "/" . $month[1] . "/" . $month[0];



$left = [$title, $datetitle, $destitle , "PHP", "", "FALSE",$datetitle2, "FALSE",""];

$xxxx = 0;
while($row = mysql_fetch_array($result)){
	$ssst = 0;
	$pht = 0;
	$pit = 0;
	$dwopt = 0;
	$nontaxt = 0;
	$taxt = 0;

  	$select1 = "select t2.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary, sum(tax) as tax, sum(t1.salary) as salary, sum(t1.nontax) as nontax,
	group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary, t2.pdm, sum(t1.pi) as pi
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and t2.pay_id = '" . $row['pay_id']  . "'  group by t1.em_id";
	if($company_id == 6 || $company_id == 1){
	   	$select1 = "select t2.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary, sum(tax) as tax, sum(t1.salary) as salary, sum(t1.nontax) as nontax,
		group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary, t2.pdm, sum(t1.pi) as pi
		from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and t1.department = '" . $row['department']  . "'  group by t1.em_id";
	}
	$result1 = mysql_query($select1, connect());
	while($row1 = mysql_fetch_array($result1)){
		$salary = $row1['taxable_salary'];
		if($row1['salary'] > 0){
			$basictax = $basictax + $row1['tax'];
			$salary  = $row1['salary'];
		} else {
			$talenttax = $talenttax + $row1['tax'];
			$talentfee = $talentfee + $row1['nontax'] + $row1['taxable_salary'];
		}


		$cnfph = 'NO';
		if($row1['ph'] > 0){
			$cnfph = 'YES';
		}

		$ph = getph($salary,$cnfph);

		if($row1['ph'] != $ph[2]){
			$ph[1] = $row['ph'];
			$ph[2] = $row['ph'];
		}

		$pht =  $pht + $row1['ph'];

		$cnfpi = 'NO';
		if($row1['pi'] > 0){
			$cnfpi = 'YES';
		}
		$pi = getpi($salary,$cnfpi,$row1['pdm']);
		$pi_employee = $pi[0];
		$pi_employer = $pi[1];

		$pit = $pit + ($pi_employer);


		$cnfsss = 'NO';
		if($row1['sss'] > 0){
			$cnfsss = 'YES';
		}



		$sss = getsss($row1['sss'],$cnfsss);
		$d = $sss[1] - $row1['sss'];
		if($d < 0){
			$sss[1] = $row1['sss'];
		}
		$sss_employee = $sss[1];
		$sss_employer = $sss[2];
		$ec = $sss[3];



		//$sss = getsss($row1['sss']);
		$ssst = $ssst + $sss_employer + $ec;
		$taxx = $tax + $row['tax'];


		$select1x = "select sum(amount) as amount, name from employee_deduction where status = 'posted' and posted_id in (" . $row['posted_id'] . ") and name = 'DWOP' and em_id = '" . $row1['em_id'] . "' group by name";
		$result1x = mysql_query($select1x, connect());
		$row1x = mysql_fetch_array($result1x);
		$dwop = $row1x['amount'];
		$dwopt = $dwopt + $dwop;
	}

	$nontaxtx = 0;
	$taxtx = 0;
	$total_row = 0;



	if(trim($row['department']) == ''){
			$row['department'] = '--NO DEPARTMENT--';
		}

	$selectDep = "select description as department, type from departments where name = '" . $row['department'] . "'";
	$resultDep = mysql_query($selectDep, connect());

	$department = $row['department'];


	$code1 = 'NoCode';
	$code2 = 'NoCode';
	$code3 = 'NoCode';
	$code4 = 'NoCode';
	$code5 = 'NoCode';
	$ds = '---';

	while($rowDep = mysql_fetch_array($resultDep)){
		$department = $rowDep['department'];

		$cd = $pr;
		$ds = 'COS - ';




		if($rowDep['type'] == '1'){
			$cd = $ho;
			$ds = '';



		}
		$code1 = $cd[0];
		$code2 = $cd[1];
		$code3 = $cd[2];
		$code4 = $cd[3];
		$code5 = $cd[4];
	}


	
	



	if($company_id == 6 || $company_id == 1){
		$select2x = "select  sum(t1.amount) as amount, t1.name from employee_non_taxable t1 left join posted_summary t2 on (t1.em_id = t2.em_id and t1.posted_id = t2.posted_id) where t2.department = '" . $row['department']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
		$result2x = mysql_query($select2x, connect());
		while($row2x = mysql_fetch_array($result2x)){

			if($rowDep['type'] == '1'){
				$nad = empty($ho1[$row2x['name']]) ? $row2x['name']: $ho1[$row2x['name']][1];
				$dx = array($ho1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
			} else {
				$nad = empty($pr1[$row2x['name']]) ? $row2x['name']: $pr1[$row2x['name']][1];
				$dx = array($pr1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
			}
			
			

			$dataxx[] = array_merge($left, $dx);

			$left = ["","","","","","","","",""];

			//$excel->writeLine(array('', $row2x['name'] , '',roundoffjv($row2x['amount']),''));
			$nontax2x = $row2x['amount'];
			$nontaxt = $nontaxt + $nontax2x;
			$nontaxtx = $nontaxtx + $nontax2x;
			$total_row = $total_row + $row2x['amount'];
		}
	} else {
		$select2x = "select  sum(t1.amount) as amount, t1.name from employee_non_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where t2.pay_id = '" . $row['pay_id']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
		$result2x = mysql_query($select2x, connect());
		while($row2x = mysql_fetch_array($result2x)){
				//$dataxx[] = array("","","","","","","","","",'NoCode', $row2x['name'] , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
				
				if($rowDep['type'] == '1'){
					$nad = empty($ho1[$row2x['name']]) ? $row2x['name']: $ho1[$row2x['name']][1];
					$dx = array($ho1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
				} else {
					$nad = empty($pr1[$row2x['name']]) ? $row2x['name']: $pr1[$row2x['name']][1];
					$dx = array($pr1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
				}
				$dataxx[] = array_merge($left, $dx);

				$left = ["","","","","","","","",""];
				//$excel->writeLine(array('', $row2x['name'] , '',roundoffjv($row2x['amount']),''));
				$nontax2x = $row2x['amount'];
				$nontaxt = $nontaxt + $nontax2x;
				$nontaxtx = $nontaxtx + $nontax2x;
				$total_row = $total_row + $row2x['amount'];
		}
	}

	$select2x = "select  sum(t1.amount) as amount, t1.name from employee_taxable t1 left join posted_summary t2 on (t1.em_id = t2.em_id and t1.posted_id = t2.posted_id) where t2.department = '" . $row['department']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
	$result2x = mysql_query($select2x, connect());
	while($row2x = mysql_fetch_array($result2x)){
		//$dataxx[] = array("","","","","","","","","",'NoCode', $row2x['name'] , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
		//$excel->writeLine(array('', $row2x['name'] , '',roundoffjv($row2x['amount']),''));

		if($rowDep['type'] == '1'){
			$nad = empty($ho1[$row2x['name']]) ? $row2x['name']: $ho1[$row2x['name']][1];
			$dx = array($ho1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
		} else {
			$nad = empty($pr1[$row2x['name']]) ? $row2x['name']: $pr1[$row2x['name']][1];
			$dx = array($pr1[$row2x['name']][0], $nad , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
		}


		//$dx = array('NoCode', $row2x['name'] , $department, "","",  roundoffjv($row2x['amount']), "","", "FALSE", "0.00%");
		$dataxx[] = array_merge($left, $dx);

		$left = ["","","","","","","","",""];
		$tax2x = $row2x['amount'];
		$taxt = $taxt + $tax2x;
		$taxtx = $taxtx + $tax2x;
		$total_row = $total_row + $row2x['amount'];
	}





	$ssstx = $ssstx + ($row['sss']+$ssst);
	$phtx = $phtx + ($row['ph']+$pht);
	$pitx = $pitx + ($row['pi']+$pit);

	//$total = $row['netpay'] + $row['ot'] + $row['sss'] + $row['ph'] + $row['pi']+$ssst+$pit+$pht+$row['tax'];
	$total = $ssst+$pit+$pht+(($row['taxable_salary'] + $row['nontax']));
	$total1 = (($row['taxable_salary'] + $row['nontax'])) - ($row['ph']+$row['pi']+$row['sss'] + $row['deduction']+$row['tax']);


	$selectc = "select id from pay where name = '" . $row['pay_id'] . "' limit 1";
	$resultc = mysql_query($selectc, connect());
	$rowc = mysql_fetch_array($resultc,MYSQL_ASSOC);
	$idc = $rowc['id'];

	$totalx = $totalx + $total1;
	// $excel->writeLine(array($idc.'-3101', 'SALARIES AND WAGES' , '',roundoffjv(($row['taxable_salary'] - $taxtx) - ($row['ot']+$dwopt)),''));
	// $excel->writeLine(array($idc.'-3103', 'OVERTIME/LATE/UDERTIME' , '',roundoffjv($row['ot']),''));
	// $excel->writeLine(array($idc.'-3104', 'SSS AND ECC CONTRIBUTIONS' , '',roundoffjv($ssst)));
	// $excel->writeLine(array($idc.'-3105', 'MEDICARE CONTRIBUTIONS' , '',roundoffjv($pht)));
	// $excel->writeLine(array($idc.'-3106', 'PAGIBIG CONTRIBUTIONS' , '',roundoffjv($pit)));
	$total_row = $total_row + (($row['taxable_salary'] - $taxtx) - ($row['ot']+$dwopt)) + $row['ot'] + $ssst + $pht + $pit;

	if($total > $total_row){
		//$excel->writeLine(array($idc.'', '13Th Month' , '',roundoffjv($total - $total_row)));
	}





	if($company_id == 6 || $company_id == 1){

		$dx = array($code1, $ds."SALARIES AND WAGES",$department, "","",  roundoffjv(($row['taxable_salary'] - $taxtx) - ($row['ot']+$dwopt)), "","", "FALSE", "0.00%");
		$dataxx[] = array_merge($left, $dx);
		$dataxx[] = array("","","","","","","","","",$code2, $ds."OVERTIME", $department,"","",roundoffjv($row['ot']), "","", "FALSE", "0.00%");
		$dataxx[] = array("","","","","","","","","",$code3, $ds."SSS& ECC PREMIUM EXPENSE",$department, "","",  roundoffjv($ssst), "","", "FALSE", "0.00%");
		$dataxx[] = array("","","","","","","","","",$code4, $ds."PHILHEALTH PREMIUM EXPENSE",$department, "","",  roundoffjv($pht), "","", "FALSE", "0.00%");
		$dataxx[] = array("","","","","","","","","",$code5, $ds."PAG-IBIG PREMIUM EXPENSE", $department, "","",  roundoffjv($pit), "","", "FALSE", "0.00%");

		$left = ["","","","","","","","",""];

		//$excel->writeLine(array('', '<b>'.$row['department'].'</b>', roundoffjv($total) ,'',''));
		$totalx1 = $totalx1 + $total;
	} else {
		//$excel->writeLine(array('', '<b>'.$row['pay_id'] . '</b>', roundoffjv($total) ,'',''));
		$totalx1 = $totalx1 + $total;
	}
	//$excel->writeLine(array('','','','',''));

}

//$excel->writeLine(array('','','','',''));

$select = "select posted_id
	from posted_summary where post_type='" . $_GET['type'] . "' AND  payday like '%" . $_GET['payday'] . "' and company_id = " . $company_id . " group by posted_id";
$result = mysql_query($select, connect());
$sssln = 0;
$piln = 0;

$posted_id = '';

while($row = mysql_fetch_array($result)){
	$posted_id = $posted_id . $row['posted_id'] . ', ';
}

$posted_id = substr($posted_id, 0, -2);

$fd = array(
"CRU LOAN" => array("303-000101", "ACCOUNT PAYABLE - RMNECA"),
"CRU LOAN(2)" => array("303-000101", "ACCOUNT PAYABLE - RMNECA"),
"CRU SHARES LOAN" => array("303-000101", "ACCOUNT PAYABLE - RMNECA"),
"MEDICAL ASSISTANCE" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"OFFICE LOAN" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"OFFICE LOAN - EYE GLASSES" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"PAG-IBIG CALAMITY LOAN" => array("304-0000104", "PAG IBIG LOAN PAYABLE"),
"PAG-IBIG LOAN" => array("304-0000104", "PAG IBIG LOAN PAYABLE"),
"SSS CALAMITY LOAN" => array("304-0000101", "SSS LOAN PAYABLE"),
"SSS LOAN" => array("304-0000101", "SSS LOAN PAYABLE"),
"TAX DEFICIENCY" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"TAX DEFICIENCY 2016" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"Word Monthly Due" => array("104-000102", "ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"TAX DEFECIENCY" => array("104-000102","ADVANCES FROM/TO OFFICERS AND EMPLOYEES"),
"ADJ. RMNECA" => array("303-000101","ACCOUNTS PAYABLE - RMNECA"),
"EXPANDED TAXES PAYABLE" => array("306-000001", "WITHHOLDING TAX - EXPANDED")

	);




$select1 = "select sum(amount) as amount, name from employee_deduction where status = 'posted' and posted_id in (" . $posted_id . ") and name != 'DWOP' group by name";
$result1 = mysql_query($select1, connect());
$ttl1 = 0;
while($row1 = mysql_fetch_array($result1)){
	//$sssln = $sssln + $row1['amount'];
	//$excel->writeLine(array(substr($row1['name'], 0, 3) . '-01', $row1['name'] , '','',roundoffjv($row1['amount'])));

	$dfs = empty($fd[$row1['name']]) ? $row1['name'] : $fd[$row1['name']][1];

	$dataxx[] = array("","","","","","","","","",$fd[$row1['name']][0], $dfs,"", "","","", roundoffjv($row1['amount']),"", "FALSE", "0.00%");

	$ttl1 = $ttl1 + $row1['amount'];
}


$totalx2 = $pitx + $ssstx + $phtx + $talenttax + $basictax + $totalx + $ttl1;


$xxss = $totalx1 - $totalx2;
$totalx = $totalx + $xxss;


// $excel->writeLine(array('-1430', 'ACCRUED SALARIES AND WAGES' , '','',roundoffjv($totalx)));
// $excel->writeLine(array('-1433', 'EMPLOYEES W/HOLDING TAXES PAYABLE' , '','',roundoffjv($basictax)));
// $excel->writeLine(array('-1434', 'EXPANDED TAXES PAYABLE' , '','',roundoffjv($talenttax)));
// $excel->writeLine(array('1436', 'PHILHEALTH CONTRIBUTIONS PAYABLE' , '','',roundoffjv($phtx)));
// $excel->writeLine(array('-1436', 'SSS CONTRIBUTIONS PAYABLE' , '','',roundoffjv($ssstx)));
// $excel->writeLine(array('-1432', 'PAGIBIG CONTRIBUTION PAYABLE' , '','',roundoffjv($pitx)));


$dataxx[] = array("","","","","","","","","","306-000002", "WITHHOLDING TAX  - COMPENSATION","", "","","", roundoffjv($basictax),"", "FALSE", "0.00%");
$dataxx[] = array("","","","","","","","","","306-000001", "WITHHOLDING TAX - EXPANDED","", "","","", roundoffjv($talenttax),"", "FALSE", "0.00%");
$dataxx[] = array("","","","","","","","","","101-000120", "BPI SALCEDO", "","","","", roundoffjv($totalx),"", "FALSE", "0.00%");
$dataxx[] = array("","","","","","","","","","304-000102", "PHIC PREMIUM PAYABLE","", "","", "", roundoffjv($phtx),"", "FALSE", "0.00%");
$dataxx[] = array("","","","","","","","","","304-000103", "PAG IBIG PREMIUM PAYABLE","", "","", "", roundoffjv($pitx),"", "FALSE", "0.00%");
$dataxx[] = array("","","","","","","","","","304-000100", "SSS PREMIUM PAYABLE", "", "","", "", roundoffjv($ssstx),"", "FALSE", "0.00%");








$totalx2 = $pitx + $ssstx + $phtx + $talenttax + $basictax + $totalx + $ttl1;



// $excel->writeLine(array('', 'TOTAL' , '',ronc($totalx1, 1),ronc($totalx2, 1)));


// $excel->writeLine(array(''));
// $excel->writeLine(array(''));
// $excel->writeLine(array(''));
// $excel->writeLine(array(''));







//$excel->writeLine(array('DEPARTMENT', 'NETPAY'));

$select = "select
	t2.department,
 	sum(netpay) as netpay
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t1.department";
$result = mysql_query($select, connect());

$total = 0;

while($row1 = mysql_fetch_array($result)){
	//$excel->writeLine(array($row1['department'], roundoffjv($row1['netpay'])));
	$total += $row1['netpay'];
}
//$excel->writeLine(array('TOTAL', roundoffjv($total)));


// $excel->writeLine(array(''));
// $excel->writeLine(array(''));
// $excel->writeLine(array(''));
// $excel->writeLine(array(''));



// $select = "select
// 	sum(t1.salary) as salary,
// 	sum(t1.taxable_salary) as taxable_salary,
// 	sum(t1.other_tax_inc) as other_tax_inc
// 	from posted_summary t1 where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id;
// $result = mysql_query($select, connect());

// $total = 0;

// while($row1 = mysql_fetch_array($result)){
// 	$excel->writeLine(array('BASIC',roundoffjv($row1['salary'])));
// 	$excel->writeLine(array('COLA',0));
// 	$excel->writeLine(array('TALENT FEE',roundoffjv($talentfee)));
// 	$excel->writeLine(array('BUNOS',0));
// 	$excel->writeLine(array('OTHER EARNINGS',roundoffjv($row1['other_tax_inc'])));
// 	$excel->writeLine(array('GROSS PAY', roundoffjv($row1['taxable_salary'])));
// }


// $excel->writeLine(array('WITHHOLDING TAX',roundoffjv($basictax)));
// $excel->writeLine(array('SSS CONTRIBUTION',roundoffjv($ssstx)));
// $excel->writeLine(array('MEDICARE',roundoffjv($phtx)));
// $excel->writeLine(array('DWOP',0));


// $select1 = "select sum(t2.amount) as amount, t2.name from employee_deduction t2 left join posted_summary t1 on (t1.posted_id = t2.posted_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " group by t2.name";
// $result1 = mysql_query($select1, connect());
// $ttl1 = 0;
// while($row1 = mysql_fetch_array($result1)){
// 	$excel->writeLine(array($row1['name'] , roundoffjv($row1['amount'])));
// 	$ttl1 = $ttl1 + $row1['amount'];
// }

// $excel->writeLine(array('TOTAL DEDUCTIONS',$ttl1));
// $excel->writeLine(array('ADJUSTMENT ON DEDUCTIONS',''));
$writer->writeSheet($dataxx,'Sheet1');
$writer->writeToFile("jv_" . $ymd . ".xlsx");

header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename='.'jv_' . $ymd . '.xlsx');
header("Location: " ."jv_" . $ymd . ".xlsx");
exit;

?>
