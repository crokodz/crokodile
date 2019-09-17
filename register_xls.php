
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("employee.xls");





$column_header = array('Family Name','First Name','MI','Basic','SPCL AL/Non Tax','Talent/OtherTaxable Inc','Overtime - (UT/Late)','Bonus','Tel Al','Gross','Adjustment','Tax','SSS','Medicare','Pagibig Contri','SSS LN','Calamity Loan','Credit LN','Shares LN','OFF LN','HSE LN','Pagibig LN','Othen LN','Total DED','ADJ DED','Net Pay');
$excel->writeLine($column_header);











// $appendsql = " ( ";
// $var = explode("@@",$_GET['vars']);
// for($x=0;$x<count($var);$x++){
// 	if ($var[$x]){
// 		if ($x==count($var)-2){
// 			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "') ";
// 			}
// 		else{
// 			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "' or ";
// 			}
// 		}
// 	}


$select = "select distinct pay_id from employee where status = 'active'";
$result = mysql_query($select, connect());

$select_nnontax = "select distinct name from employee_non_taxable where posted_id = "

while($row = mysql_fetch_array($result)){
	$excel->writeLine(array('Pay Code: ' . $row['pay_id']));

	$select1 = "select lname, fname, mname, em_id from employee where pay_id = '" . $row['pay_id'] . "'";
	$result1 = mysql_query($select1, connect());
	$basic = 0;
	$nontax = 0;
	$other_tax_inc = 0;
	$bonus = 0;
	$overtime = 0;
	$tel_all = 0;
	$gross = 0;
	$adjustment = 0;
	$tax = 0;
	$sss_con = 0;
	$medicare = 0;
	$days_wop = 0;
	$pagibig_con = 0;
	$sss_ln = 0;
	$cal_ln = 0;
	$credit_ln = 0;
	$shares_ln = 0;
	$off_ln = 0;
	$hse_ln = 0;
	$pagibig_ln = 0;
	$other_ln = 0;
	$tot_ded = 0;
	$adj_ded = 0;
	$net_pay = 0;
	while($row1 = mysql_fetch_array($result1)){
		$select2 = "select salary, nontax, other_tax_inc, ot, ut, late from posted_summary where em_id = '" . $row1['em_id'] . "' and payday = '" . $_GET['payday'] . "'";
		$result2 = mysql_query($select2, connect());
		while($row2 = mysql_fetch_array($result2)){
			$basic += $row2['salary'];
			$nontax += $row2['nontax'];
			$other_tax_inc += $row2['other_tax_inc'];
			$overtime += $row2['ot'] - ($row2['ut'] + $row2['late']);
			$bonus = $bonus + 0;
			$tel_all += 0

            $grossx = 0;
            $adjustmentx = 0;
            $taxx = 0;
            $sss_conx = 0;
            $medicarex = 0;
            $days_wopx = 0;
            $pagibig_conx = 0;
            $sss_lnx = 0;
            $cal_lnx = 0;
            $credit_lnx = 0;
            $shares_lnx = 0;
            $off_lnx = 0;
            $hse_lnx = 0;
            $pagibig_lnx = 0;
            $other_lnx = 0;
            $tot_dedx = 0;
            $adj_dedx = 0;
            $net_payx = 0;

			$gross += $grossx;
			$adjustment += 0;
			$tax += 0;
			$sss_con += 0;
			$medicare += 0;
			$days_wop += 0;
			$pagibig_con += 0;
			$sss_ln += 0;
			$cal_ln += 0;
			$credit_ln += 0;
			$shares_ln += 0;
			$off_ln += 0;
			$hse_ln += 0;
			$pagibig_ln += 0;
			$other_ln += 0;
			$tot_ded += 0;
			$adj_ded += 0;
			$net_pay += 0;

			//$credit_loan +=
			$excel->writeLine(array($row1['lname'],$row1['fname'], $row1['mname'], $row2['salary'], $row2['nontax'], $row2['other_tax_inc'], $row2['ot'] - ($row2['ut'] + $row2['late']), 0, 0, $grossx, $adjustmentx, $taxx, $sss_conx, $medicarex, $days_wopx, $pagibig_conx, $sss_lnx, $cal_lnx, $credit_lnx, $shares_lnx, $off_lnx, $hse_lnx, $pagibig_lnx, $other_lnx, $tot_dedx, $adj_dedx, $net_pay));
		}
	}

	$total = array('', '', '', $basic, $nontax, $other_tax_inc, $overtime,$bonus, $gross, $adjustment, $tax, $sss_con, $medicare, $days_wop, $pagibig_con, $sss_ln, $cal_ln, $credit_ln, $shares_ln, $off_ln, $hse_ln, $pagibig_ln, $other_ln, $tot_ded, $adj_ded, $net_pay);
	$excel->writeLine($total);
	$excel->writeLine(array(''));
	$excel->writeLine(array(''));
	}





header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=employe.xls');
header("Location: employee.xls");
exit;

?>
