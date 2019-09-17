<?php
include "config.php";

if (isset($_POST['fyy'])){
	$fyy = $_POST['fyy'];
	}
else{
	$fyy = date('Y');
	}
if (isset($_POST['tyy'])){
	$tyy = $_POST['tyy'];
	}
else{
	$tyy = date('Y');
	}
if (isset($_POST['fmm'])){
	$fmm = $_POST['fmm'];
	}
else{
	$fmm = date('m');
	}
if (isset($_POST['tmm'])){
	$tmm = $_POST['tmm'];
	}
else{
	$tmm = date('m');
	}
if (isset($_POST['fdd'])){
	$fdd = $_POST['fdd'];
	}
else{
	$fdd = date('d');
	}
if (isset($_POST['tdd'])){
	$tdd = $_POST['tdd'];
	}
else{
	$tdd = date('d');
	}
if (isset($_POST['smm'])){
	$smm = $_POST['smm'];
	}
else{
	$smm = date('m');
	}
if (isset($_POST['syy'])){
	$syy = $_POST['syy'];
	}
else{
	$syy = date('Y');
	}

function getTime($time,$id){
	$s = split(':', $time);
	if ($id == 1){
		return $s[0];
		}
	elseif ($id == 2){
		return $s[1];
		}
	else{
		return $s[2];
		}
	}

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function h2m($hours){
	$expl = explode(":", $hours);
	return ($expl[0] * 60) + $expl[1];
	}

function GetInfo($id){
	$select = "select `ts`,`salary_based`,`pay_id` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['salary_based'],$row['ts'],$row['pay_id']);
	}

function getpercent($pay, $based){
	$select = "select reg_rate, ot_rate from pay join ot_rate on (pay.ot = ot_rate.id) where pay.name = '" . $pay . "' and ot_rate.name = '" . $based . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['reg_rate'],$row['ot_rate']);
	}

function getperday($salary,$company,$id){
	$select = "select factor, days from company where id = '" . $company . "'";
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

function m2h($mins) {
	if ($mins < 0) {
		$min = Abs($mins);
		}
	else {
                $min = $mins;
		}
	$H = Floor($min / 60);
	$M = ($min - ($H * 60)) / 100;
	$hours = $H +  $M;

	if ($mins < 0) {
                $hours = $hours * (-1);
		}
	$expl = explode(".", $hours);
	$H = $expl[0];
	if (empty($expl[1])) {
                $expl[1] = 00;
		}
	$M = $expl[1];
            if (strlen($M) < 2) {
                $M = $M . 0;
		}
	$hours = $H . ":" . $M;
	return $hours;
	}

function GetShift($id){
	$select = "select `from`,`to` from shift where shift_code = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['from'],$row['to']);
	}

function GetID(){
        $select = "SELECT MAX(posted_id) AS maxid FROM posted_summary";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'] + 1;
        }

function UpdateDeduction($posted_id,$em_id,$id){
	$select = "select * from employee_deduction where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted' and
		deduct_id = '" . $id . "'
		group by name
		order by deduct_id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$update = "update employee_deduction set status = 'posted', posted_id = '" . $posted_id . "'
			where deduct_id = '" . $row['deduct_id'] . "'
			";
		mysql_query($update,connect());
		}
	}

//~ function UpdateAdjustments($posted_id,$em_id){
	//~ $select = "select * from employee_adjustments where
		//~ em_id = '" . $em_id . "' and
		//~ status != 'deleted' and
		//~ status != 'posted'
		//~ order by id
		//~ ";
	//~ $result = mysql_query($select, connect());
	//~ while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		//~ $update = "update employee_adjustments set status = 'posted', posted_id = '" . $posted_id . "'
			//~ where id = '" . $row['id'] . "'
			//~ ";
		//~ mysql_query($update,connect());
		//~ }
	//~ }

function UpdateTaxable($posted_id,$em_id, $id){
	$select = "select * from employee_taxable where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted' and
		id = '" . $id . "'
		order by id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		if($row['status'] == 'Deminimis'){
			$copy = " insert into employee_taxable select null, `days`, `name`, `em_id`, `amount`, `posted_id`, `status`, `username`, `datetime`, `deleted`, `deleted_date`, `remarks`, `status` from employee_taxable where id = '" . $row['id'] . "' ";
			mysql_query($copy,connect());
			}

		$update = "update employee_taxable set status = 'posted', posted_id = '" . $posted_id . "'
			where id = '" . $row['id'] . "'
			";
		mysql_query($update,connect());
		}
	}

function UpdateNonTaxable($posted_id,$em_id,$id,$val){
	$select = "select * from employee_non_taxable where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted' and
		id = '" . $id . "'
		order by id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		if($row['status'] == 'Deminimis'){
			$copy = " insert into employee_non_taxable select NULL, `name`, `em_id`, `amount`, `posted_id`, `status`, `username`, `datetime`, `deleted`, `deleted_date`, `origin`, `status` from employee_non_taxable where id = '" . $row['id'] . "' ";
			mysql_query($copy,connect());
			if($val == '13TH MONTH'){
				$update = "update employee_non_taxable set status = 'posted', posted_id = '" . $posted_id . "', origin = 'Deminimis', `amount`  = '" . ($row['amount']*2) . "' where id = '" . $row['id'] . "' ";
				}
			else{
				$update = "update employee_non_taxable set status = 'posted', posted_id = '" . $posted_id . "', origin = 'Deminimis' where id = '" . $row['id'] . "' ";
				}
			}
		else{
			$update = "update employee_non_taxable set status = 'posted', posted_id = '" . $posted_id . "' where id = '" . $row['id'] . "'";
			}
		mysql_query($update,connect());
		}
	}

function GetPayCode($id,$status){
	$select = "select name from pay where `name` = '" . $id . "' LIMIT 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$name = $row['name'];

	$select = "select id from ot_rate where name = '" . $status . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$ot_id = $row['id'];

	$select = "select * from pay where name = '" . $name . "' and ot = '" . $ot_id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function UpdateOt($var,$id){
	$a = explode("||",$var);
	if($a[9]){
		$update = "update employee_ot set `posted_id` = '" . $id . "', `status` = 'posted' where `id` = '" . $a[9] . "' ";
		mysql_query($update, connect());
		}
	}

function UpdateAdjustments($emid,$id){
	$update = "update employee_adjustments set `posted_id` = '" . $id . "', `status` = 'posted' where `em_id` = '" . $emid . "' and  `status` = 'pending' ";
	mysql_query($update, connect());
	}


if (isset($_POST['post'])){
	$id = GetID();
	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];
	$payday = $_POST['payday'];
	$company_id = $_POST['company_id'];
	$days = $_POST['days'];


	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['emid' . $x]){
			$emid = $_POST['emid' . $x];
			$basic = $_POST['basic' . $emid];
			$ot = $_POST['ot' . $emid];
			$absent = $_POST['absent' . $emid];
			$late = $_POST['late' . $emid];
			$ut = $_POST['ut' . $emid];
			$oth = $_POST['oth' . $emid];
			$gross = $_POST['gross' . $emid];
			$tax = $_POST['tax' . $emid];
			$gat = $_POST['gat' . $emid];
			$sss = $_POST['sss' . $emid];
			$ph = $_POST['ph' . $emid];
			$pi = $_POST['pi' . $emid];
			$ded = $_POST['ded' . $emid];
			$nt = $_POST['nt' . $emid];
			$net = $_POST['net' . $emid];
			$status = $_POST['status' . $emid];
			$type = $_POST['type' . $emid];
			$pay_id = $_POST['pay_id' . $emid];
			$perday = $_POST['perday' . $emid];
			$permin = $_POST['permin' . $emid];
			$post_type = $_POST['post_type'];
			$title = $_POST['title'];

			#absent
			$a = "";
			for($y=0;$y<$_POST['abscount'.$emid];$y++){
				if($_POST[$y.'abschbx'.$emid]){
					$a = $a . $_POST[$y.'absval'.$emid] . '@@';
					}
				}

			#late
			$l = "";
			for($y=0;$y<$_POST['latecount'.$emid];$y++){
				if($_POST[$y.'latechbx'.$emid]){
					$l = $l . $_POST[$y.'lateval'.$emid] . '@@';
					}
				}

			#overtime
			$o = "";
			for($y=0;$y<$_POST['otcount'.$emid];$y++){
				if($_POST[$y.'otchbx'.$emid]){
					$o = $o . $_POST[$y.'otval'.$emid] . '@@';

					UpdateOt($_POST[$y.'otval'.$emid],$id);
					}
				}

			#undertime
			$u = "";
			for($y=0;$y<$_POST['utcount'.$emid];$y++){
				if($_POST[$y.'utchbx'.$emid]){
					$u = $u . $_POST[$y.'utval'.$emid] . '@@';
					}
				}


			#adjustments
			UpdateAdjustments($emid,$id);

			#non taxable income
			$n = "";
			for($y=0;$y<$_POST['ntcount'.$emid];$y++){
				if($_POST[$y.'ntchbx'.$emid]){
					if($_POST[$y.'ntval'.$emid] == '13pay'){
						$select = "select max(`id`) as id from employee_non_taxable";
						$result = mysql_query($select, connect());
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$idx = $row['id'] + 1;
						$insert = "INSERT INTO `employee_non_taxable` (
							`id` ,
							`name` ,
							`em_id` ,
							`amount` ,
							`posted_id` ,
							`status` ,
							`username` ,
							`datetime`
							)
							VALUES (
							'" . $idx . "',
							'13th Month Pay',
							'" . $emid . "',
							'" . $_POST[$y.'ntchbx'.$emid] . "',
							'0',
							'pending',
							'" . $_SESSION['user'] . "',
							now()
							)";
						mysql_query($insert, connect());
						$_POST[$y.'ntval'.$emid] = $idx;
						}
					elseif($_POST[$y.'ntval'.$emid] == 'taxrefund'){
						$select = "select max(`id`) as id from employee_non_taxable";
						$result = mysql_query($select, connect());
						$row = mysql_fetch_array($result,MYSQL_ASSOC);
						$idx = $row['id'] + 1;

						$insert = "INSERT INTO `employee_non_taxable` (
							`id` ,
							`name` ,
							`em_id` ,
							`amount` ,
							`posted_id` ,
							`status` ,
							`username` ,
							`datetime`
							)
							VALUES (
							'" . $idx . "',
							'Tax Refund',
							'" . $emid . "',
							'" . $_POST[$y.'ntchbx'.$emid] . "',
							'0',
							'pending',
							'" . $_SESSION['user'] . "',
							now()
							)";
						mysql_query($insert, connect());

						$_POST[$y.'ntval'.$emid] = $idx;
						}
					$n = $n . $_POST[$y.'ntval'.$emid] . '@@';

					if($post_type == 'REGULAR'){
						UpdateNonTaxable($id,$emid,$_POST[$y.'ntval'.$emid],$_POST['paytype']);
					}
					}
				}



			#other taxable income
			$h = "";
			for($y=0;$y<$_POST['othcount'.$emid];$y++){
				if($_POST[$y.'othchbx'.$emid]){
					$h = $h . $_POST[$y.'othval'.$emid] . '@@';
					UpdateTaxable($id,$emid,$_POST[$y.'othval'.$emid]);
					}
				}

			#deduction
			$d = "";
			for($y=0;$y<$_POST['dedcount'.$emid];$y++){
				if($_POST[$y.'dedchbx'.$emid]){
					$d = $d . $_POST[$y.'dedval'.$emid] . '@@';
					UpdateDeduction($id,$emid, $_POST[$y.'dedval'.$emid]);
					}
				}



			$insert = "
				INSERT INTO `posted_summary` (
				`id` ,
				`posted_id` ,
				`em_id` ,
				`salary` ,
				`from` ,
				`to` ,
				`days` ,
				`status` ,
				`pay_id` ,
				`company_id` ,
				`late` ,
				`ut` ,
				`ot` ,
				`absent` ,
				`deduction` ,
				`other_tax_inc` ,
				`taxable_salary` ,
				`tax` ,
				`sss` ,
				`pi` ,
				`ph` ,
				`nontax` ,
				`netpay` ,
				`perday_salary` ,
				`permin_salary` ,
				`payday` ,
				`abs_value` ,
				`late_value` ,
				`ot_value` ,
				`ut_value` ,
				`oth_value` ,
				`nt_value` ,
				`ded_value` ,
				`type`,
				`post_type`,
				`title`
				)
				VALUES (
				NULL ,
				'" . $id . "',
				'" . $emid . "',
				'" . $basic . "',
				'" . $fdate . "',
				'" . $tdate . "',
				'" . $days . "',
				'" . $status . "',
				'" . $pay_id . "',
				'" . $company_id . "',
				'" . $late . "',
				'" . $ut . "',
				'" . $ot . "',
				'" . $absent . "',
				'" . $ded . "',
				'" . $oth . "',
				'" . $gross . "',
				'" . $tax . "',
				'" . $sss . "',
				'" . $pi . "',
				'" . $ph . "',
				'" . $nt . "',
				'" . $net . "',
				'" . $perday . "',
				'" . $permin . "',
				'" . $payday . "',
				'" . $a . "',
				'" . $l . "',
				'" . $o . "',
				'" . $u . "',
				'" . $h . "',
				'" . $n . "',
				'" . $d . "',
				'" . $type . "',
				'" . $post_type . "',
				'" . $title . "'
				);
				";
			mysql_query($insert, connect());

			$dep = "update `posted_summary` t1 set department = (select department from employee t2 where t2.em_id = t1.em_id ) where t1.department = '' or t1.department is null";
			mysql_query($dep, connect());

			$update = "update transaction set `posted_id` = '" . $id . "' where em_id = '" . $emid . "' and `trxn_date` between '" . $fdate . "' and '" . $tdate . "' ";
			mysql_query($update, connect());
			}
		}
        }
?>