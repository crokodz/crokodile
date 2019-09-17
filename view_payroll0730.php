
<?php
include "config.php";
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];


if(isset($_POST['add'])){
	$insert="insert into employee_resigned values ('" . $_POST['em_id'] . "', '" . $_GET['date1'] . "', '" . $_GET['date2'] . "', '" . $_POST['payday'] . "')";
	mysql_query($insert, connect());
	}
	
if(isset($_POST['clear'])){
	$delete="delete from employee_resigned where date1 = '" . $_GET['date1'] . "' and  date2 = '" . $_GET['date2'] . "' ";
	mysql_query($delete, connect());
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
	
function getsahod($id){
	$select = "select `salary` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['salary'];
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
	$select = "select `ts`,`salary_based`,`pay_id`,`company_id`,`wtax`,`tin`,`sss`,`ph`,`pi`,`salary`  from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['salary_based'],$row['ts'],$row['pay_id'],$row['company_id'],$row['tin'],$row['sss'],$row['ph'],$row['pi'],$row['salary']);
	}

function getpercent($pay, $based){
	$select = "select reg_rate, ot_rate from pay join ot_rate on (pay.ot = ot_rate.id) where pay.name = '" . $pay . "' and ot_rate.name = '" . $based . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['reg_rate'],$row['ot_rate']);
	}
	
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
        $select = "SELECT MAX(posted_id) AS maxid FROM posted";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'] + 1;
        }
	
function getNon_Taxable($id, $type){
	$select = "select sum(tb1.amount) as amount from employee_non_taxable tb1 left join view_payroll_hist tb2 on 
		(tb1.id = tb2.misc)
		where tb1.em_id = '" . $id . "' and (tb1.status = 'pending' or tb1.status = 'Deminimis') 
		and (tb2.chk = 'checked' or tb2.chk is null)
		";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['amount'];
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

function GetSalary($em_id, $date){
	$select = "select salary from transaction where `em_id` = '" . $em_id . "' and `trxn_date` <= '" . $date . "' order by `trxn_date` desc limit 1 ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['salary'] / 2;
	}
	
function getTaxable($id){
	$select = "select sum(tb1.amount) as amount from employee_taxable tb1 left join view_payroll_hist tb2 on 
		(tb1.id = tb2.misc)
		where tb1.em_id = '" . $id . "' and (tb1.status = 'pending' or tb1.status = 'Deminimis') 
		and (tb2.chk = 'checked' or tb2.chk is null)
		";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['amount'];
	}
	
function getDeduction_No($em_id, $type,$date1, $date2){
	$select = "select amount, deduct_id from employee_deduction where em_id = '" . $em_id . "' and `name` = '" . $type . "' and `status` = 'pending'  LIMIT 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$ded = getDeduction($row['em_id'],$rowded['name']);
	$rowx = getcHk('ded',$date1,$date2,$em_id,$row['deduct_id']);
	if($rowx['chk']=='checked' or $rowx['count']==0){
		return array('amount'=>$row['amount'], 'id'=>$row['deduct_id']);
		}
	else{
		return array('amount'=>o, 'id'=>'');
		}
	}
	
function getDeduction($em_id, $type){
	$select = "select amount, deduct_id from employee_deduction where em_id = '" . $em_id . "' and `name` = '" . $type . "' and `status` = 'pending'  group by ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array('amount'=>$row['amount'], 'id'=>$row['deduct_id']);
	}
	
function gettin($salary,$type,$status,$cnftin){
	if($cnftin == 'YES'){
		if($type == 'DAILY'){
			$type = 'SEMI-MONTHLY';
			}
		$select = "select * from tin where status = '" . $status . "' and type = '" . $type . "' and salary <= '" . $salary . "' order by salary desc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		$a = $salary - $row['salary'];
		$b = $a * $row['percent'];
		$c = $b + $row['exception'];
		return $c;
		}
	else{
		return 0;
		}
	}
	
function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
	}


function getpayid($payid){
	$select = "select * from pay where name = '" . $payid . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function getWtax($em_id,$salary){
	$select = " select `wtax` from employee where em_id = '" . $em_id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$wtax = $salary * ($row['wtax']/100);
	return $wtax;
	}
	
function Get13Month($em_id,$date2,$payid){
	$select = " select * from employee where em_id = '" . $em_id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	$select1 = "select date1 from yearly_cutoff where status = 'active' ";
	$result1 = mysql_query($select1, connect());
	$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
	
	$cinfo = getpayid($row['pay_id']);
	
	if ($row['salary_based'] == 'SEMI-MONTHLY'){
		$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
		$salary = $row['salary'];
		$g = 0;
		}
	elseif ($row['salary_based'] == 'MONTHLY'){
		$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
		$salary = $row['salary'];
		$g = 0;
		}
	elseif ($row['salary_based'] == 'DAILY'){
		$perday = $row['salary'];
		$g = 1;
		}
	elseif ($row['salary_based'] == 'WEEKLY'){
		$perday = getperday($row['salary'],$row['company_id'],2,$row['pay_id']);
		$g = 1;
		}
	elseif ($row['salary_based'] == 'HOURLY'){
		$perday = ($row['salary'] / 60) * $cinfo['min'];
		$g = 1;
		}
	
	$date_employed = $row['date_employed'];
	$yday = dateDiff($row1['date1'], $date2);
	
	if($yday >= 365){
		if($g == 1){
			$salary = $perday * $cinfo['factor'];
			}
		}
	else{
		if($g == 1){
			$salary = $perday * $cinfo['factor'];
			$month = $yday/30;
			$salary = $salary / 12 * ($month);
			}
		else{
			$month = $yday/30;
			$salary = $salary / 12 * ($month);
			}
		}
	
	return array('yday' => $yday, 'salary' => $salary);
	}
	
function getcHk($type,$date1,$date2,$emid,$misc){
	if($type=='basic'){
		$selectx = "select `chk` from view_payroll_hist where typ = '" . $type . "' and date1 = '" . $date1 . "' and date2 = '" . $date2 . "' and emid = '" . $emid . "' ";
		}
	if($type=='emp'){
		$selectx = "select `chk` from view_payroll_hist where typ = '" . $type . "' and date1 = '" . $date1 . "' and date2 = '" . $date2 . "' and emid = '" . $emid . "' ";
		}
	elseif($type=='oth' or $type=='ded' or $type=='nt'){
		$selectx = "select `chk` from view_payroll_hist where typ = '" . $type . "' and date1 = '" . $date1 . "' and date2 = '" . $date2 . "' and emid = '" . $emid . "' and misc = '" . $misc . "'  ";
		}
	else{
		$selectx = "select `chk` from view_payroll_hist where typ = '" . $type . "' and date1 = '" . $date1 . "' and emid = '" . $emid . "' ";
		}
		
	$resultx = mysql_query($selectx, connect());
	$rowx = mysql_fetch_array($resultx,MYSQL_ASSOC);
	$count = mysql_num_rows($resultx);
	return array('chk'=>$rowx['chk'], 'count'=>$count);
	}

function CS($em_id,$from_date,$to_date,$based,$sal){
	

	$select = "select * from transaction where `trxn_date` BETWEEN '" . $from_date . "' AND '" . $to_date . "' and em_id = '" . $em_id . "' ";
	$result = mysql_query($select, connect());
	$tot = 0;
	$totx = 0;
	$tnd = 0;
	$ut = 0;
	$late = 0;
	$absent = 0;
	$halfday = 0;
	$hldate = "";
	$absdate = "";
	$latedate = "";
	$otdate = "";
	$utdate = "";
	$addition = 0;
	$otmin = 0;
	$otxmin = 0;
	$ndlmin = 0;
	$salary = 0;
	
	$x = 0;
	
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$row['salary_based'] = $based;
		$row['salary'] = $sal;
	
		if ($row['salary_based'] == 'SEMI-MONTHLY'){
			$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
			$salary = getsahod($em_id)/2;
			//~ $salary = $row['salary'] / 2;
			}
		elseif ($row['salary_based'] == 'MONTHLY'){
			$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
			$salary = getsahod($em_id);
			//~ $salary = $row['salary'];
			}
		elseif ($row['salary_based'] == 'DAILY'){
			//~ if($row['status'] != 'NO WORK'){
				//~ $perday = $row['salary'];
				//~ $salary = $salary + $row['salary'];
				//~ }
			if($row['status'] == 'ABSENT' or $row['status'] == 'UNFILED' or $row['status'] == 'AWOL' or $row['status'] == 'LWOP' or $row['status'] == 'VLWOP' or $row['status'] == 'RESTDAY'){
				$ssssssd = 0;
				//$perday = $row['salary'];
				//$salary = $salary + $row['salary']
				}
			else{
				echo $perday = getsahod($em_id);
				$salary = $salary + $perday;
				//~ $perday = $row['salary'];
				//~ $salary = $salary + $row['salary'];
				}
			}
		elseif ($row['salary_based'] == 'WEEKLY'){
			if($row['status'] != 'ABSENT' or $row['status'] != 'UNFILED' or $row['status'] != 'AWOL' or $row['status'] != 'LWOP' or $row['status'] != 'VLWOP'){
				$perday = getperday($row['salary'],$row['company_id'],2,$row['pay_id']);
				$salary = $salary + $perday;
				}
			}
		elseif ($row['salary_based'] == 'HOURLY'){
			if($row['status'] != 'ABSENT' or $row['status'] != 'UNFILED' or $row['status'] != 'AWOL' or $row['status'] != 'LWOP' or $row['status'] != 'VLWOP'){
				$perday = ($row['salary'] / 60) * $cinfo['min'];
				$salary =  $salary + $perday;
				}
			}
			
		if($row['status'] == 'ABSENT' or $row['status'] == 'UNFILED' or $row['status'] == 'AWOL' or $row['status'] == 'LWOP' or $row['status'] == 'VLWOP'){
			if($row['salary_based'] != 'DAILY'){
				$rowx = getcHk('abs',$row['trxn_date'],'',$em_id,'');
				if($rowx['chk']=='checked' or $rowx['count'] == 0){
					$absent = $absent + 1;
					$rowx['chk'] = 'checked';
					}
				if($row['status'] == 'ABSENT' or $row['status'] == 'UNFILED' or $row['status'] == 'AWOL'){
					$absxx = 'ABSENT';
					}
				else{
					$absxx = $row['status'];
					}
			
				$absdate = $absdate . $row['trxn_date']. "||1||" . $absxx . "||" . $rowx['chk'] . "@@";
				}
			}
			
		if($row['status'] == 'HALF DAY'){
			$rowx = getcHk('abs',$row['trxn_date'],'',$em_id,'');
			if($rowx['chk']=='checked' or $rowx['count'] == 0){
				$absent = $absent + .5;
				$rowx['chk'] = 'checked';
				}
			$absdate = $absdate . $row['trxn_date']. "||.5||HALF DAY||" . $rowx['chk'] . "@@";
			}
		
		$perhour = $perday / 8;
		$permin = $perhour / 60;
		
		if ($row['total'] >= $cinfo['min']){
			$total = $cinfo['min'];
			}
		else{
			$total = $row['total'];
			}
		
		
		
		$paycode = GetPayCode($row['pay_id'],$row['status']);
		
		if($x == 0){
			$selectox = "select * from employee_ot where em_id = '" . $em_id . "' and status = 'pending'  ";
			$resultox = mysql_query($selectox, connect());
			while($rowox = mysql_fetch_array($resultox,MYSQL_ASSOC)){
				$otox = $permin * $rowox['mins'] * $paycode['reg_rate'];
				$otxox = $permin * $rowox['minsx'] * $paycode['ot_rate'];
				
				if($rowox['type'] == 'REGULAR'){
					$ndox = $permin * $rowox['minsnd'] * $paycode['ndl'];
					}
				else{
					$ndox = $permin * $rowox['minsnd'] * $paycode['reg_rate'] * $paycode['ndl'];
					}
					
				
				$rowx = getcHk('ot',$rowox['date'],'',$em_id,'');
				if($rowx['chk']=='checked' or $rowx['count'] == 0){
					$tot = $tot + $otox;
					$totx = $totx + $otxox;
					$tnd = $tnd + $ndox;
					
					$otmin = $otmin + $rowox['mins'];
					$otxmin = $otxmin + $rowox['minsx'];
					$ndlmin = $ndlmin + $rowox['minsnd'];
					
					$rowx['chk'] = 'checked';
					}
					
				$otdate = $otdate . $rowox['date']. "||" . $rowox['mins'] . "||" . $rowox['minsx'] . "||" . $rowox['minsnd'] . "||" . ($otox + $otxox + $ndox) . "||" . $otox . "||" . $otxox . "||" . $ndox . "||" . $rowx['chk'] . "||" . $rowox['id'] ."@@";
				}
			}
		
		#regular rate
		if($row['cbot']=='checked'){
			//~ echo $paycode['reg_rate'] . '<br>';
			//~ echo $row['ot'] . '<br>';
			 //~ echo $permin. '<br>';
			$ot = $permin * $row['ot'] * $paycode['reg_rate'];
			$otx = $permin * $row['otx'] * $paycode['ot_rate'];
			if($row['status'] == 'REGULAR'){
				$nd = $permin * $row['nd'] * $paycode['ndl'];
				}
			else{
				$nd = $permin * $row['nd'] * $paycode['reg_rate'] * $paycode['ndl'];
				}
			}
		else{
			$ot = 0;
			$otx = 0;
			$nd = 0;
			}
		
		
		/////////////////////////////
		
		$rowx = getcHk('late',$row['trxn_date'],'',$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$late = $late + $row['late'];
			$rowx['chk'] = 'checked';
			}
		if($row['late']>0){
			$latedate = $latedate . $row['trxn_date']. "||" . $row['late'] . "||" . ($row['late']*$permin) . "||" . $rowx['chk'] . "@@";
			}
			
		/////////////////////////////
			
		$rowx = getcHk('ot',$row['trxn_date'],'',$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$tot = $tot + $ot;
			$totx = $totx + $otx;
			$tnd = $tnd + $nd;
			
			$otmin = $otmin + $row['ot'];
			$otxmin = $otxmin + $row['otx'];
			$ndlmin = $ndlmin + $row['nd'];
			
			$rowx['chk'] = 'checked';
			}
			
		if($ot > 0){
			$otdate = $otdate . $row['trxn_date']. "||" . $row['ot'] . "||" . $row['otx'] . "||" . $row['nd'] . "||" . ($ot + $otx + $nd) . "||" . $ot . "||" . $otx . "||" . $nd . "||" . $rowx['chk'] . "@@";
			}
			
		/////////////////////////////
			
		$rowx = getcHk('ut',$row['trxn_date'],'',$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$ut = $ut + $row['ut'];
			
			$rowx['chk'] = 'checked';
			}
		
		if($row['ut'] > 0){
			$utdate = $utdate . $row['trxn_date']. "||" . $row['ut'] . "||" . ($row['ut']*$permin) . "||" . $rowx['chk'] . "@@";
			}
		
		//////////////////////////////
		
		$req_basic = '';
		$req_rice = '';
		$req_laun = '';
		$req_med = '';
		$req_trans = '';
		$req_meal = '';
		$req_hazard = '';
		
		$rowx = getcHk('basic',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_basic = 'checked';
			}
		$rowx = getcHk('0nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_rice = 'checked';
			}
		$rowx = getcHk('1nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_laun = 'checked';
			}
			
		$rowx = getcHk('2nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_med = 'checked';
			}
			
		$rowx = getcHk('3nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_trans = 'checked';
			}
			
		$rowx = getcHk('4nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_meal = 'checked';
			}
		$rowx = getcHk('5nt',$from_date,$to_date,$em_id,'');
		if($rowx['chk']=='checked' or $rowx['count'] == 0){
			$req_hazard = 'checked';
			}
			
		$x++;
		}
		
	

	return array(
		'basic' => $salary, 
		'absent' => $absent, 
		'late' => $late, 
		'ot' => $otmin, 
		'perday'=>$perday, 
		'permin'=>$permin, 
		'ut'=>$ut, 
		'absdate' => $absdate, 
		'latedate'=>$latedate, 
		'otdate' => $otdate, 
		'utdate' => $utdate,
		'hldate' => $hldate,
		'otamt' => $tot,
		'otxamt' => $totx,
		'ndamt' => $tnd,
		'halfday' => $halfday,
		'req_basic' => $req_basic,
		'req_rice' => $req_rice,
		'req_laun' => $req_laun,
		'req_med' => $req_med,
		'req_trans' => $req_trans,
		'req_meal' => $req_meal,
		'req_hazard' => $req_hazard
		);
	}
	
function getsss($salary,$cnfsss){
	if($cnfsss == 'YES'){
		$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
		}
	else{
		return 0;
		}
	}
	
function LastPay($em_id, $date2, $taxable, $gtax, $ts){
	$select = "select date1 from yearly_cutoff where status = 'active' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$date1 = $row['date1'];
	
	$select = "select sum(`taxable_salary`) taxable, sum(`tax`) as `tax`, sum(`sss`) as `sss`, sum(`pi`) as `pi`, sum(`ph`) as `ph` from posted_summary where `from` >= '" . $date1 . "' and `to` <= '" . $date2 . "' and em_id = '" . $em_id . "' group by `em_id` ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	
	if($ts=='S' or $ts=='ME'){
		$excepmtion = 50000;
		}
	elseif($ts=='HF1' or $ts=='ME1'){
		$excepmtion = 75000;
		}
	elseif($ts=='HF2' or $ts=='ME2'){
		$excepmtion = 100000;
		}
	elseif($ts=='HF3' or $ts=='ME3'){
		$excepmtion = 125000;
		}
	elseif($ts=='HF4' or $ts=='ME4'){
		$excepmtion = 150000;
		}
	else{
		$excepmtion = 150000;
		}
	
	$lp = $row['taxable'] - $row['tax'] - $row['sss'] - $row['ph'] - $row['pi'] + $taxable - $excepmtion;
	
	$tax = $row['tax'] + $gtax;
	
	$select = "select  * from tax_return where `over` <= '" . $lp . "' and `not_over` >= '" . $lp . "' order by `over` desc limit 1  ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);	
	
	//~ echo $ts;
	//~ echo $lp . "<br>";
	//~ echo $row['over'] . "<br>";
	//~ echo $row['percent'] . "<br>";
	//~ echo $tax . '<br>';
	//~ echo $row['amount']. "<br>";
	
	
	$a = ($lp - $row['over']) * $row['percent'];
	$b = $a + $row['amount'];
	$c = $tax - $b;
	return $c;
	}
	
function getpi($salary,$cnfpi){
	if($cnfpi == 'YES'){
		if ($salary <= 1500){
			return $salary * .02;
			}
		else{
			return 100;
			}
		}
	else{
		return 0;
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
		return 0;
		}
	}
	
$alpha = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>

<title>Payroll System</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=win 1252">
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="lib/prototype.js"></script>
<script type="text/javascript" src="src/scriptaculous.js"></script>
<body id="innerframe">
<form method="POST">
	<div style="margin-left:10px;margin-top:20px;margin-bottom:20px;border:1px solid #487598;width:600px;padding-left:30px;padding-bottom:20px;padding-top:20px;-moz-border-radius:4px;background:#1e4c1e;">
	<input type="text" name="keyword" id="keyword" style="height:22px;width:350px;border:1px solid #FFFFFF;">
	<input type="hidden" name="em_id" id="em_id">
	<input type="hidden" name="payday" id="payday" value="<?php echo $_GET['payday']; ?>">
	<input type="submit" name="add" value="Add" class="ShowHideHeader" style="width:50px;-moz-border-radius:4px;border:1px solid #FFFFFF;"> <input type="submit" name="clear" value="Clear" class="ShowHideHeader" style="width:70px;-moz-border-radius:4px;border:1px solid #FFFFFF;">
	<div id="hint"></div>
	<script type="text/javascript">
	new Ajax.Autocompleter("keyword","hint","server_resigned.php", {afterUpdateElement : getSelectedId});
	function getSelectedId(text, li) {
		myData = li.id.split("@@");
		$('keyword').value=myData[1];
		$('em_id').value=myData[0];
		}
	</script>
	</div>
</form>

<form method="POST" action="payroll_posting_gen.php">
<input type="hidden" name="fdate" id="fdate" value="<?php echo $date1; ?>">
<input type="hidden" name="tdate" id="tdate" value="<?php echo $date2; ?>">
<input type="hidden" name="payday" value="<?php echo $_GET['payday']; ?>">
<input type="hidden" name="company_id" value="<?php echo $_GET['cid']; ?>">
<input type="hidden" name="days" value="<?php echo $_GET['days']; ?>">
<input type="hidden" name="post_type" id="post_type" value="<?php echo $_GET['type']; ?>">

<?php
$p = explode("@", $_GET['payday']);
if($_GET['type'] == 'REGULAR'){
	if($p[0] == 'w1'){
		$title = "FIRST " . $_GET['type'] . " PAYDAY FOR " . $p[1];
		}
	else{
		$title = "SECOND " . $_GET['type'] . " PAYDAY FOR " . $p[1];
		} 
	}
elseif($_GET['type']=='13TH MONTH' or $_GET['type']=='BONUS'){
	$title = $_GET['type'] . " FOR " . $p[1];
	}
else{
	$title = $_GET['type'];
	}
	
?>

<div style="padding:10px;"> Payroll Covered From : <b><?php echo $_GET['date1']; ?></b>&nbsp;&nbsp;&nbsp;To : <b><?php echo $_GET['date2']; ?></b></div>

&nbsp;&nbsp;&nbsp;<b>Title</b>&nbsp;<input type="text" name="title" value="<?php echo $title; ?>" style="height:22px;width:350px;">
<input type="submit" class="ShowHideHeader" name="post" value="POST" onclick="return checkPosting();">

<?php
if($_GET['pay_id'] == 'ALL'){
?>
<input type="button" id="pay_id" class="ShowHideHeader" value="MINIMIZE PAY CODE" onclick="showpayid('pay_id')">
<?php
}
?>


<div class="tabmainview" id="tabmainview">
<?php


$select = "SELECT `pay_id` FROM employee where `status` = 'active' and `file_status` != 'SEPARATED' group by `pay_id` order by `pay_id` asc";
$result = mysql_query($select, connect());
$x=0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
<div class="tabview" id="tab<?php echo $x; ?>"  onclick="onclickpayID('<?php echo str_replace(" ","",$row['pay_id']); ?>')"><input type="checkbox" id="cb<?php echo str_replace(" ","",$row['pay_id']); ?>" checked> <?php echo $row['pay_id']; ?></div>
<?php
}
?>
<div class="warning" id="load">LOADING</div>
</div>
<div class="tabmainview" id="SHheader" style="width:250px;">
<div class="tabview" id="" onclick="onClck('1')"><input type="checkbox" id="cb1" checked=true> Id No.</div>
<div class="tabview" id="" onclick="onClck('2')"><input type="checkbox" id="cb2" checked=true> Name</div>
<div class="tabview" id="" onclick="onClck('3')"><input type="checkbox" id="cb3" checked=true> Basic As of <?php echo $date1; ?></div>
<div class="tabview" id="" onclick="onClck('4')"><input type="checkbox" id="cb4" checked=true> Absent</div>
<div class="tabview" id="" onclick="onClck('5')"><input type="checkbox" id="cb5" checked=true> Late</div>
<div class="tabview" id="" onclick="onClck('6')"><input type="checkbox" id="cb6" checked=true> OT</div>
<div class="tabview" id="" onclick="onClck('7')"><input type="checkbox" id="cb7" checked=true> Rice Sub</div>
<div class="tabview" id="" onclick="onClck('8')"><input type="checkbox" id="cb8" checked=true> Med Sud</div>
<div class="tabview" id="" onclick="onClck('9')"><input type="checkbox" id="cb9" checked=true> Laundry</div>
<div class="tabview" id="" onclick="onClck('10')"><input type="checkbox" id="cb10" checked=true> Other Tax Income</div>
<?php 
$z=11;
$k = 0;
$select = "select `name` from deductions order by `name` asc";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
<div class="tabview" id="" onclick="onClck('<?php echo $z; ?>')"><input type="checkbox" id="cb<?php echo $z; ?>" checked=true> <?php echo $row['name']; ?></div>
<?php
$z++;
$k++;
}
?>
<div class="tabview" id="" onclick="onClck('<?php echo $z+0; ?>')"><input type="checkbox" id="cb<?php echo $z+0; ?>" checked=true> SSS</div>
<div class="tabview" id="" onclick="onClck('<?php echo $z+1; ?>')"><input type="checkbox" id="cb<?php echo $z+1; ?>" checked=true> PH</div>
<div class="tabview" id="" onclick="onClck('<?php echo $z+2; ?>')"><input type="checkbox" id="cb<?php echo $z+2; ?>" checked=true> PI</div>
<div class="tabview" id="" onclick="onClck('<?php echo $z+3; ?>')"><input type="checkbox" id="cb<?php echo $z+3; ?>" checked=true> Net Pay</div>
</div>


<br>
<div class="mainviewtable">
<div>
	<div id="" class="td10" style="height:33px;font-size:10px;">&nbsp;</div>
	<div id="" class="td70" style="height:28px;font-size:10px;">Id No.</div>
	<div id="" class="tdname" style="height:28px;font-size:10px;width:295px;">Name</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">Basic As of <?php echo $date1; ?></div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">Absent HalfDay</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">Late</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">OT</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">UT</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">non-Tax / Benefits</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">Other Income</div>
	<div id="" class="td80" style="width:83px;height:28px;font-size:10px;border-right:1px solid #FFF;">Deduction</div>
	<div id="" class="td80" style="width:65px;height:28px;font-size:10px;border-right:1px solid #FFF;">Netpay</div>
</div>

<?php
$payid = "";
$paysel = "";
if($_GET['pay_id'] != 'ALL'){
	$paysel = " `pay_id` = '" . $_GET['pay_id'] . "'  and ";
	}
	
if($_GET['cid'] != 'ALL'){
	$comsel = " company_id = '" . $_GET['cid'] . "' and ";
	}
else{
	$comsel  = "";
	$paysel = "";
	}
	
if($_GET['type'] == 'RESIGNED'){
	$select = "SELECT tb2.`em_id`, tb2.`name`, tb2.`pay_id` FROM employee_resigned tb1 join employee tb2 using(`em_id`) where tb1.id = '" . $_GET['payday'] . "'  order by tb2.pay_id asc, tb2.em_id asc"; 
	}
else{
	$select = "SELECT `em_id`, `name`, `pay_id` FROM employee where    " . $comsel . $paysel . " `status` = 'active' and `file_status` = 'EMPLOYEE' order by pay_id asc, em_id asc"; //`pay_id` asc, name asc 
	}
$result = mysql_query($select, connect());
$y = 0;
$s = 1;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
if($row['pay_id'] != $payid){
?>
<br>
<div id="<?php echo $row['pay_id']; ?>z0">
	<div><b><?php echo $row['pay_id']; ?></b></div>
</div>
<?php
$s = 1;
}

$info = GetInfo($row['em_id']);
$cnftin = $info[4];
$cnfsss = $info[5];
$cnfph = $info[6];
$cnfpi = $info[7];
$sal = $info[8];

$company = getcompany($info[3]);
$var = CS($row['em_id'], $date1, $date2,$info[0],$sal);

$basicQ = $var['basic'];

$rowx = getcHk('basic',$date1,$date2,$row['em_id'],'');
if($rowx['chk']=='' and $rowx['count']!=0){
	$var['basic'] = 0;
	$chkbasic = '';
	}
else{
	$chkbasic = 'checked';
	}


$otherincome = getTaxable($row['em_id']);
$totaladjustment = ($var['absent'] * $var['perday']) + ($var['late']*$var['permin']) + ($var['ut']*$var['permin']);

$taxable = ($var['basic'] - $totaladjustment + $otherincome + $var['otamt'] + $var['otxamt'] + $var['ndamt']);


$tin = gettin($taxable,$info[0],$info[1],$cnftin);
$wtax = getWtax($row['em_id'],$taxable);

$tin = $tin + $wtax;
$earnings = $taxable;



$nontax = getNon_Taxable($row['em_id']);

$var_sss = $company['sss'];
$var_tin = $company['tin'];
$var_ph = $company['ph'];
$var_pi = $company['pi'];

$varpd = explode("@", $_GET['payday']);
$svar = $varpd[0];

#social security system
if($var_sss == $svar){
	$sss = getsss($earnings,$cnfsss);
	$sss_id = $sss[0];
	$sss_employee = $sss[1];
	$sss_employer = $sss[2] + $sss[3];
	}
elseif($var_sss == 'h'){
	$sss = getsss($earnings,$cnfsss);
	$sss_id = $sss[0];
	$sss_employee = $sss[1];    
	$sss_employer = $sss[2] + $sss[3];
	}
elseif($var_sss == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$sss = getsss($earnings,$cnfsss);
		$sss_id = $sss[0];
		$sss_employee = $sss[1];    
		$sss_employer = $sss[2] + $sss[3];
		}
	else{
		
		$pd = 'w1@' . $pp[1];
		$select = " select sss, taxable_salary from posted_summary  where em_id = '" . $row['em_id'] . "' and payday = '" . $pd . "' ";
		$pdresult = mysql_query($select, connect());
		$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
		$pdearnings = $pdrow['taxable_salary'] + $earnings;
		
		$sss = getsss($pdearnings,$cnfsss);
		$sss_id = $sss[0];
		$sss_employee = $sss[1] - $pdrow['sss'];    
		$sss_employer = $sss[2] + $sss[3];
		}
	}

#pag-ibig
if($var_pi == $svar){
	$pi = getpi($earnings,$cnfpi);
	$pi_employee = $pi;
	$pi_employer = $pi;
	}
elseif($var_pi == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$pi = getpi($earnings,$cnfpi);
		$pi_employee = $pi;
		$pi_employer = $pi;
		}
	else{
		$pi_employee = 0;
		$pi_employer = 0;
		}
	}
elseif($var_pi == 'h'){
	$pi = getpi($earnings,$cnfpi);
	$pi_employee = $pi/2;
	$pi_employer = $pi/2;
	}
	
#phil health
if($var_ph == $svar){
	$ph = getph($earnings,$cnfph);
	$ph_1d = $ph[0];
	$ph_employee = $ph[1];
	$ph_employer = $ph[2];
	}
elseif($var_ph == 'h'){
	$ph = getph($earnings,$cnfph);
	$ph_1d = $ph[0];
	$ph_employee = $ph[1];
	$ph_employer = $ph[2];
	}	
elseif($var_ph == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$ph = getph($earnings,$cnfph);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1];
		$ph_employer = $ph[2];
		}
	else{
		$pd = 'w1@' . $pp[1];
		$select = " select ph, taxable_salary from posted_summary  where em_id = '" . $row['em_id'] . "' and payday = '" . $pd . "' ";
		$pdresult = mysql_query($select, connect());
		$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
		$pdearnings = $pdrow['taxable_salary'] + $earnings;
		
		$ph = getph($pdearnings,$cnfph);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1] - $pdrow['ph'];
		$ph_employer = $ph[2];
		}
	}



$netpay = $taxable - $tin - $pi_employee - $ph_employee - $sss_employee + $nontax;

$deduction = 0;
$selectded = "select amount, deduct_id from employee_deduction where em_id = '" . $row['em_id'] . "' and `status` = 'pending'  group by sub_id order by deduct_id asc";
$resultded = mysql_query($selectded, connect());
while($ded = mysql_fetch_array($resultded,MYSQL_ASSOC)){
	$rowx = getcHk('ded',$date1,$date2,$row['em_id'],$ded['deduct_id']);
	if($rowx['chk']=='checked' or $rowx['count']==0){
		$netpay = $netpay - $ded['amount'];
		$deduction = $deduction + $ded['amount'];
		}
	}


if($_GET['type']=='13TH MONTH'){
	$sss_employee = 0;
	$taxable = 0;
	$tin = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$deduction = 0;
	$otherincome = 0;
	$nontax = 0;
	$var['late'] = 0;
	$var['ot'] = 0;
	$var['ut'] = 0;
	$var['absent'] = 0;
	$var['basic'] = 0;
	$var['otamt'] = 0;
	$var['otxamt'] = 0;
	$var['ndamt'] = 0;
	$m = Get13Month($row['em_id'],$date2);
	$var['basic'] = $m['salary'];
	$netpay = $m['salary'];
	$checked = 'disabled="true"';
	$readonly = 'readonly';
	}
elseif($_GET['type']=='BONUS'){
	$sss_employee = 0;
	$tin = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$deduction = 0;
	$otherincome = 0;
	$nontax = 0;
	$var['late'] = 0;
	$var['ot'] = 0;
	$var['ut'] = 0;
	$var['absent'] = 0;
	$var['basic'] = 0;
	$var['otamt'] = 0;
	$var['otxamt'] = 0;
	$var['ndamt'] = 0;
	$m = Get13Month($row['em_id'],$date2);
	$var['basic'] = $m['salary'];
	
	$tin = gettin($var['basic'],$info[0],$info[1],$cnftin);
	$wtax = getWtax($row['em_id'],$var['basic']);
	$tin = $tin + $wtax;
	
	$taxable = $m['salary'];
	
	$netpay = $taxable-$tin;
	$checked = 'disabled="true"';
	$readonly = 'readonly';
	$readonly1 = '';
	}
elseif($_GET['type']=='COMMISSION'){
	$sss_employee = 0;
	$taxable = 0;
	$tin = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$deduction = 0;
	$otherincome = 0;
	$nontax = 0;
	$var['late'] = 0;
	$var['ot'] = 0;
	$var['ut'] = 0;
	$var['absent'] = 0;
	$var['absent'] = 0;
	$var['basic'] = 0;
	$var['otamt'] = 0;
	$var['otxamt'] = 0;
	$var['ndamt'] = 0;
	$var['basic'] = 0;
	$netpay = 0;
	$checked = 'disabled="true"';
	$readonly = 'readonly';
	$readonly1 = '';
	}
elseif($_GET['type']=='SL PAY'){
	$sss_employee = 0;
	$taxable = 0;
	$tin = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$deduction = 0;
	$otherincome = 0;
	$nontax = 0;
	$var['late'] = 0;
	$var['ot'] = 0;
	$var['ut'] = 0;
	$var['absent'] = 0;
	$var['absent'] = 0;
	$var['basic'] = 0;
	$var['otamt'] = 0;
	$var['otxamt'] = 0;
	$var['ndamt'] = 0;
	$var['basic'] = 0;
	$netpay = 0;
	$checked = 'disabled="true"';
	$readonly = 'readonly';
	$readonly1 = '';
	}
elseif($_GET['type']=='VL PAY'){
	$sss_employee = 0;
	$taxable = 0;
	$tin = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$deduction = 0;
	$otherincome = 0;
	$nontax = 0;
	$var['late'] = 0;
	$var['ot'] = 0;
	$var['ut'] = 0;
	$var['absent'] = 0;
	$var['absent'] = 0;
	$var['basic'] = 0;
	$var['otamt'] = 0;
	$var['otxamt'] = 0;
	$var['ndamt'] = 0;
	$var['basic'] = 0;
	$netpay = 0;
	$checked = 'disabled="true"';
	$readonly = 'readonly';
	$readonly1 = '';
	}
elseif($_GET['type']=='RESIGNED'){
	$m = Get13Month($row['em_id'],$date2);
	$lp = LastPay($row['em_id'], $date2, $taxable,$tin,$info[1]);
	$s13 = $m['salary'];
	
	$netpay = $netpay+$sss_employee+$pi_employee+$ph_employee+$s13+$lp;
	$sss_employee = 0;
	$pi_employee = 0;
	$ph_employee = 0;
	$nontax = $nontax + $s13 + $lp;
	$disabled = "onclick='this.click();'";
	}
else{
	$checked = 'checked = "true"';
	$readonly = 'readonly';
	$readonly1 = '';
	}
	
//~ if($netpay < 0){
	//~ $netpay = 0;
	//~ }

$req_emp = "";
$req_emp_style = "";	
$rowx = getcHk('emp',$date1,$date2,$row['em_id'],'');
	if($rowx['chk']=='checked' or $rowx['count'] == 0){
	$req_emp = 'checked';
	}
else{
	$req_emp_style = "background:#dbab77;";
	}

?>
<div>
	<div id="box<?php echo $row['em_id']; ?>" class="td10" style="padding-top:1px;<?php echo $req_emp_style; ?>"><input type="checkbox" id="cbw<?php echo str_replace(" ","",$row['pay_id']); ?><?php echo $y; ?>" name="emid<?php echo $y; ?>" value="<?php echo $row['em_id']; ?>" onclick="chbox('<?php echo $row['em_id']; ?>',this,'<?php echo $date1; ?>','<?php echo $date2; ?>','cbw<?php echo str_replace(" ","",$row['pay_id']); ?><?php echo $y; ?>');" <?php echo $req_emp; ?>></div>
	<div id="id<?php echo $row['em_id']; ?>" class="td70" style="<?php echo $req_emp_style; ?>"><?php echo $row['em_id']; ?></div>
	<div id="name<?php echo $row['em_id']; ?>" class="tdname" style="<?php echo $req_emp_style; ?>"><?php echo $row['name']; ?></div><div class="misc" onclick="OclkMisc(this,'<?php echo $row['em_id']; ?>');">Misc</div>
	<div class="inline"><input type="text" name="basic<?php echo $row['em_id']; ?>" id="textbasic<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma($var['basic'],2); ?>" <?php echo $readonly1; ?> onkeyup="basicEdit('<?php echo $row['em_id']; ?>');"></div><div class="butz" onclick="onclk('basic',this,'<?php echo $row['em_id']; ?>',20)">...</div>
	<div class="inline"><input type="text" name="absent<?php echo $row['em_id']; ?>" id="textabs<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma(($var['absent']*$var['perday']),2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('abs',this,'<?php echo $row['em_id']; ?>',20)">...</div>
	<div class="inline"><input type="text" name="late<?php echo $row['em_id']; ?>" id="textlate<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma(($var['late']*$var['permin']),2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('late',this,'<?php echo $row['em_id']; ?>',20)">...</div>
	<div class="inline"><input type="text" name="ot<?php echo $row['em_id']; ?>" id="textot<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma((($var['otamt']) + ($var['otxamt']) + ($var['ndamt'])),2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('ot',this,'<?php echo $row['em_id']; ?>',-578)">...</div>
	<div class="inline"><input type="text" name="ut<?php echo $row['em_id']; ?>" id="textut<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma(($var['ut']*$var['permin']),2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('ut',this,'<?php echo $row['em_id']; ?>',-378)">...</div>
	<div class="inline"><input type="text" name="nt<?php echo $row['em_id']; ?>" id="textnt<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma($nontax,2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('nt',this,'<?php echo $row['em_id']; ?>',-478)">...</div>
	<div class="inline"><input type="text" name="oth<?php echo $row['em_id']; ?>" id="textoth<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma($otherincome,2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('oth',this,'<?php echo $row['em_id']; ?>',-478)">...</div>
	<div class="inline"><input type="text" name="ded<?php echo $row['em_id']; ?>" id="textded<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma($deduction,2); ?>" <?php echo $readonly; ?>></div><div class="butz" onclick="onclk('ded',this,'<?php echo $row['em_id']; ?>',-478)">...</div>
	<div class="inline"><input type="text" name="net<?php echo $row['em_id']; ?>" id="textnet<?php echo $row['em_id']; ?>" value="<?php echo roundoffNoComma($netpay,2); ?>" class="net" onclick="onclknet('net',this,'<?php echo $row['em_id']; ?>',-278)" <?php echo $readonly; ?>></div>
</div>


<input type="hidden" name="em_idx<?php echo $y; ?>" id="em_idx<?php echo $y; ?>" value="<?php echo $row['em_id']; ?>">
<input type="hidden" name="emp<?php echo $row['em_id']; ?>" id="textemp<?php echo $row['em_id']; ?>" value="0">
<input type="hidden" name="status<?php echo $row['em_id']; ?>" id="textstatus<?php echo $row['em_id']; ?>" value="<?php echo $info[1]; ?>">
<input type="hidden" name="type<?php echo $row['em_id']; ?>" id="texttype<?php echo $row['em_id']; ?>" value="<?php echo $info[0]; ?>">
<input type="hidden" name="sss<?php echo $row['em_id']; ?>" id="textsss<?php echo $row['em_id']; ?>" value="<?php echo $sss_employee; ?>">
<input type="hidden" name="pi<?php echo $row['em_id']; ?>" id="textpi<?php echo $row['em_id']; ?>" value="<?php echo $pi_employee; ?>">
<input type="hidden" name="ph<?php echo $row['em_id']; ?>" id="textph<?php echo $row['em_id']; ?>" value="<?php echo $ph_employee; ?>">
<input type="hidden" name="tax<?php echo $row['em_id']; ?>" id="texttin<?php echo $row['em_id']; ?>" value="<?php echo $tin; ?>">
<input type="hidden" name="gat<?php echo $row['em_id']; ?>" id="textgat<?php echo $row['em_id']; ?>" value="<?php echo $taxable-$tin; ?>">
<input type="hidden" name="pay_id<?php echo $row['em_id']; ?>" id="textpayid<?php echo $row['em_id']; ?>" value="<?php echo $row['pay_id']; ?>">
<input type="hidden" name="perday<?php echo $row['em_id']; ?>" id="textperday<?php echo $row['em_id']; ?>" value="<?php echo $var['perday']; ?>">
<input type="hidden" name="permin<?php echo $row['em_id']; ?>" id="textpermin<?php echo $row['em_id']; ?>" value="<?php echo $var['permin']; ?>">
<input type="hidden" name="gross<?php echo $row['em_id']; ?>" id="textgross<?php echo $row['em_id']; ?>" value="<?php echo $taxable; ?>">



<div id="basic<?php echo $row['em_id']; ?>" class="hide" style="width:200px;">
	<input <?php echo $disabled; ?> type="checkbox" name="chbxbasic" id="bcb<?php echo $row['em_id']; ?>"  <?php echo $chkbasic; ?> value="<?php echo roundoffNoComma($basicQ,2); ?>" onclick="onclbox('basic','<?php echo $row['em_id']; ?>','b','<?php echo $date1; ?>','<?php echo $date2; ?>','bcb<?php echo $row['em_id']; ?>')">
	<div <?php echo $disabled; ?> onclick="oncldiv('bcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($basicQ,2); ?></div>
</div>

<div id="abs<?php echo $row['em_id']; ?>" class="hide" style="width:400px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('abs',this,'<?php echo $row['em_id']; ?>',20)"></img></div>
	<?php
	$abs = explode("@@",$var['absdate']);
	for($x=0;$x<count($abs)-1;$x++){
		$absz = explode("||",$abs[$x]);
		if($absz[0]){
			$absamt = $absz[1] * $var['perday'];
			?>
			<input <?php echo $disabled; ?> type="checkbox" name="<?php echo $x; ?>abschbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>acb<?php echo $row['em_id']; ?>" <?php echo $absz[3]; ?> value="<?php echo $absamt; ?>" onclick="onclbox('abs','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>a','<?php echo $absz[0]; ?>','<?php echo $date2; ?>','<?php echo $x; ?>acb<?php echo $row['em_id']; ?>')">
			<input type="hidden" name="<?php echo $x; ?>absval<?php echo $row['em_id']; ?>" value="<?php echo $abs[$x]; ?>">
			<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>acb<?php echo $row['em_id']; ?>')" style="width:120px;"><?php echo $absz[0]; ?></div>
			<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>acb<?php echo $row['em_id']; ?>')" style="width:120px;"><?php echo $absz[2]; ?></div>
			<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>acb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($absamt,2); ?></div><br>
			<?php
			}
			}
			?>
			<input type="hidden" name="abscount<?php echo $row['em_id']; ?>" value="<?php echo $x; ?>">
	<div>&nbsp;</div>
</div>


<div id="late<?php echo $row['em_id']; ?>" class="hide" style="width:400px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('late',this,'<?php echo $row['em_id']; ?>',20)"></img></div>
	<?php
	$late = explode("@@",$var['latedate']);
	for($x=0;$x<count($late)-1;$x++){
	$latez = explode("||", $late[$x]);
	?>
	<input type="checkbox" name="<?php echo $x; ?>latechbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>lcb<?php echo $row['em_id']; ?>" <?php echo $latez[3]; ?> value="<?php echo $latez[2]; ?>" onclick="onclbox('late','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>l','<?php echo $latez[0]; ?>','<?php echo $date2; ?>','<?php echo $x; ?>lcb<?php echo $row['em_id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>lateval<?php echo $row['em_id']; ?>" value="<?php echo $late[$x]; ?>">
	<div onclick="oncldiv('<?php echo $x; ?>lcb<?php echo $row['em_id']; ?>')" style="width:120px;"><?php echo $latez[0]; ?></div>
	<div onclick="oncldiv('<?php echo $x; ?>lcb<?php echo $row['em_id']; ?>')" style="width:90px;text-align:right;"><?php echo roundoffNoComma($latez[1],2); ?> min.</div>
	<div onclick="oncldiv('<?php echo $x; ?>lcb<?php echo $row['em_id']; ?>')" style="width:90px;text-align:right;"><?php echo roundoffNoComma($latez[2],2); ?></div><br>
	<?php
	}
	?>
	<input type="hidden" name="latecount<?php echo $row['em_id']; ?>" value="<?php echo $x; ?>">
	<div>&nbsp;</div>
</div>


<div id="ot<?php echo $row['em_id']; ?>" class="hide" style="width:500px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('ot',this,'<?php echo $row['em_id']; ?>',-578)"></img></div>
	<div style="width:20px;">&nbsp;</div>
	<div style="width:120px;">Date</div>
	<div style="width:70px;text-align:right;">OT(min.)</div>
	<div style="width:70px;text-align:right;">OTX(min.)</div>
	<div style="width:70px;text-align:right;">ND(min.)</div>
	<div style="width:70px;text-align:right;">Total</div><br>
	<?php
	$ot = explode("@@",$var['otdate']);
	for($x=0;$x<count($ot)-1;$x++){
	$otx = explode("||", $ot[$x])
	?>
	<input type="checkbox" name="<?php echo $x; ?>otchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>" <?php echo $otx[8]; ?> value="<?php echo roundoffNoComma($otx[4],2); ?>" onclick="onclbox('ot','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>ot','<?php echo $otx[0]; ?>','<?php echo $date2; ?>','<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>otval<?php echo $row['em_id']; ?>" value="<?php echo $ot[$x]; ?>">
	<div style="width:120px;" onclick="oncldiv('<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')"><?php echo $otx[0]; ?></div>
	<div style="width:70px;text-align:right;" onclick="oncldiv('<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($otx[1],0); ?></div>
	<div style="width:70px;text-align:right;" onclick="oncldiv('<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($otx[2],0); ?></div>
	<div style="width:70px;text-align:right;" onclick="oncldiv('<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($otx[3],0); ?></div>
	<div style="width:70px;text-align:right;" onclick="oncldiv('<?php echo $x; ?>otcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($otx[4],2); ?></div><br>
	<?php
	}
	?>
	<input type="hidden" name="otcount<?php echo $row['em_id']; ?>" value="<?php echo $x; ?>">
	<div>&nbsp;</div>
</div>


<div id="ut<?php echo $row['em_id']; ?>" class="hide" style="width:300px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('ut',this,'<?php echo $row['em_id']; ?>',20)"></img></div>
	<?php
	$ut = explode("@@",$var['utdate']);
	for($x=0;$x<count($ut)-1;$x++){
	$utx = explode("||", $ut[$x]);
	?>
	<input type="checkbox" name="<?php echo $x; ?>utchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>utcb<?php echo $row['em_id']; ?>" <?php echo $utx[3]; ?> value="<?php echo roundoffNoComma($utx[2],2); ?>" onclick="onclbox('ut','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>ut','<?php echo $utx[0]; ?>','<?php echo $date2; ?>','<?php echo $x; ?>utcb<?php echo $row['em_id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>utval<?php echo $row['em_id']; ?>" value="<?php echo $ut[$x]; ?>">
	<div style="width:90px;"  onclick="oncldiv('<?php echo $x; ?>utcb<?php echo $row['em_id']; ?>')"><?php echo $utx[0]; ?></div>
	<div style="width:70px;text-align:right;"  onclick="oncldiv('<?php echo $x; ?>utcb<?php echo $row['em_id']; ?>')"><?php echo $utx[1]; ?></div>
	<div style="width:70px;text-align:right;"  onclick="oncldiv('<?php echo $x; ?>utcb<?php echo $row['em_id']; ?>')"><?php echo roundoffNoComma($utx[2],2); ?></div><br>
	<?php
	}
	?>
	<input type="hidden" name="utcount<?php echo $row['em_id']; ?>" value="<?php echo $x; ?>">
	<div>&nbsp;</div>
</div>

<div id="nt<?php echo $row['em_id']; ?>" class="hide" style="width:400px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('oth',this,'<?php echo $row['em_id']; ?>',-478)"></img></div>
	<?php
	$selectnt = "select amount, name, id from employee_non_taxable where em_id = '" . $row['em_id'] . "' and (status = 'pending' or status = 'Deminimis') ";
	$resultnt = mysql_query($selectnt, connect());
	$x=0;
	while($nt = mysql_fetch_array($resultnt,MYSQL_ASSOC)){
	$chnt = '';
	$rowx = getcHk('nt',$date1,$date2,$row['em_id'],$nt['id']);
	if($rowx['chk']=='checked' or $rowx['count']==0){
		$chnt = 'checked';
		}
	?>
	<input <?php echo $disabled; ?> type="checkbox" name="<?php echo $x; ?>ntchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>" <?php echo $chnt; ?> value="<?php echo roundoffNoComma($nt['amount'],2); ?>" onclick="onclbox('nt','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>nt','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>','<?php echo $nt['id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>ntval<?php echo $row['em_id']; ?>" value="<?php echo $nt['id']; ?>">
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:120px;"><?php echo $nt['name']; ?></div>
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:140px;"><?php echo roundoffNoComma($nt['amount'],2); ?></div><br>
	<?php
	$x++;
	}
	if($_GET['type']=='RESIGNED'){
	?>
	<input <?php echo $disabled; ?>  type="checkbox" name="<?php echo $x; ?>ntchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>" <?php echo $chnt; ?> value="<?php echo roundoffNoComma($s13,2); ?>" onclick="onclbox('nt','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>nt','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>','13pay')">
	<input type="hidden" name="<?php echo $x; ?>ntval<?php echo $row['em_id']; ?>" value="13pay">
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:120px;">13th month pay</div>
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:140px;"><?php echo roundoffNoComma($s13,2); ?></div><br>
	<?php
	$x++;
	?>
	<input <?php echo $disabled; ?>  type="checkbox" name="<?php echo $x; ?>ntchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>" <?php echo $chnt; ?> value="<?php echo roundoffNoComma($lp,2); ?>" onclick="onclbox('nt','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>nt','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>','taxrefund')">
	<input type="hidden" name="<?php echo $x; ?>ntval<?php echo $row['em_id']; ?>" value="taxrefund">
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:120px;">Tax Refund</div>
	<div <?php echo $disabled; ?> onclick="oncldiv('<?php echo $x; ?>ntcb<?php echo $row['em_id']; ?>')" style="width:140px;"><?php echo roundoffNoComma($lp,2); ?></div><br>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="ntcount<?php echo $row['em_id']; ?>" value="<?php echo  $x; ?>">
	<div>&nbsp;</div>
</div>

<div id="oth<?php echo $row['em_id']; ?>" class="hide" style="width:400px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('oth',this,'<?php echo $row['em_id']; ?>',-478)"></img></div>
	<?php
	$selectoth = "select amount, name, id from employee_taxable where em_id = '" . $row['em_id'] . "' and (status = 'pending' or status = 'Deminimis') ";
	$resultoth = mysql_query($selectoth, connect());
	$x=0;
	while($oth = mysql_fetch_array($resultoth,MYSQL_ASSOC)){
	$choth = '';
	$rowx = getcHk('oth',$date1,$date2,$row['em_id'],$oth['id']);
	if($rowx['chk']=='checked' or $rowx['count']==0){
		$choth = 'checked';
		}
	?>
	<input type="checkbox" name="<?php echo $x; ?>othchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>othcb<?php echo $row['em_id']; ?>" <?php echo $choth; ?> value="<?php echo roundoffNoComma($oth['amount'],2); ?>" onclick="onclbox('oth','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>oth','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $x; ?>othcb<?php echo $row['em_id']; ?>','<?php echo $oth['id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>othval<?php echo $row['em_id']; ?>" value="<?php echo $oth['id']; ?>">
	<div onclick="oncldiv('<?php echo $x; ?>othcb<?php echo $row['em_id']; ?>')" style="width:120px;"><?php echo $oth['name']; ?></div>
	<div onclick="oncldiv('<?php echo $x; ?>othcb<?php echo $row['em_id']; ?>')" style="width:140px;"><?php echo roundoffNoComma($oth['amount'],2); ?></div><br>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="othcount<?php echo $row['em_id']; ?>" value="<?php echo  $x; ?>">
	<div>&nbsp;</div>
</div>

<div id="ded<?php echo $row['em_id']; ?>" class="hide" style="width:400px;">
	<div class="clx"><img src="close_icon.gif" onclick="onclk('ded',this,'<?php echo $row['em_id']; ?>',-478)"></img></div>
	<?php
	$selectded = "select amount, deduct_id, name from employee_deduction where em_id = '" . $row['em_id'] . "' and `status` = 'pending'  group by sub_id order by deduct_id asc";
	$resultded = mysql_query($selectded, connect());
	while($ded = mysql_fetch_array($resultded,MYSQL_ASSOC)){
		$rowx = getcHk('ded',$date1,$date2,$row['em_id'],$ded['deduct_id']);
	$dedoth="";	
	if($rowx['chk']=='checked' or $rowx['count']==0){
		$dedoth = 'checked';
		}
	?>
	<input type="checkbox" name="<?php echo $x; ?>dedchbx<?php echo $row['em_id']; ?>" id="<?php echo $x; ?>dedcb<?php echo $row['em_id']; ?>" <?php echo $dedoth; ?> value="<?php echo roundoffNoComma($ded['amount'],2); ?>"  onclick="onclbox('ded','<?php echo $row['em_id']; ?>', '<?php echo $x; ?>ded','<?php echo $date1; ?>','<?php echo $date2; ?>','<?php echo $x; ?>dedcb<?php echo $row['em_id']; ?>', '<?php echo $ded['deduct_id']; ?>')">
	<input type="hidden" name="<?php echo $x; ?>dedval<?php echo $row['em_id']; ?>" value="<?php echo $ded['deduct_id']; ?>">
	<div onclick="oncldiv('<?php echo $x; ?>dedcb<?php echo $row['em_id']; ?>')" style="width:140px;"><?php echo $ded['name']; ?></div>
	<div onclick="oncldiv('<?php echo $x; ?>dedcb<?php echo $row['em_id']; ?>')" style="width:90px;"><?php echo roundoffNoComma($ded['amount'],2); ?></div><br>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="dedcount<?php echo $row['em_id']; ?>" value="<?php echo  $x; ?>">
	<div>&nbsp;</div>
</div>

<?php
$payid = $row['pay_id'];
$y++;
$s++;
}
?>
</div>

<div id="net" class="hidez" style="width:266px;">
	<div style="width:110px;">Basic Salary</div><div style="text-align:right;width:120px;" id="nbasic"></div><br>
	<div style="width:110px;">Absent</div><div style="text-align:right;width:120px;" id="nabs"></div><br>
	<div style="width:110px;">Ot</div><div style="text-align:right;width:120px;" id="not"></div><br>
	<div style="width:110px;">Ut</div><div style="text-align:right;width:120px;" id="nut"></div><br>
	<div style="width:110px;">Late</div><div style="text-align:right;width:120px;" id="nlate"></div><br>
	<div style="width:110px;">Other Income</div><div style="text-align:right;width:120px;" id="noth"></div><br>
	<div style="width:110px;"><b>Gross Taxable</b></div><div style="text-align:right;width:120px;" id="ngt"></div><br>
	<div style="width:110px;">Tax</div><div style="text-align:right;width:120px;" id="ntax"></div><br>
	<div style="width:110px;"><b>Gross After Tax</b></div><div style="text-align:right;width:120px;" id="ngat"></div><br>
	<div style="width:110px;">SSS</div><div style="text-align:right;width:120px;" id="nsss"></div><br>
	<div style="width:110px;">PI</div><div style="text-align:right;width:120px;" id="npi"></div><br>
	<div style="width:110px;">PH</div><div style="text-align:right;width:120px;" id="nph"></div><br>
	<div style="width:110px;">Deduction</div><div style="text-align:right;width:120px;" id="nded"></div><br>
	<div style="width:110px;">Non-Taxable</div><div style="text-align:right;width:120px;" id="nnt"></div><br>
	<div style="width:110px;"><b>Net Pay</b></div><div style="text-align:right;width:120px;" id="nnp"></div><br>
</div>

<div class="hidez" id="misc">
	<div class="clx"><img src="close_icon.gif" onclick="clspOp('misc')"></img></div>
	<div>
		<div class="inlinemisc1">Type : </div>
		<div class="inlinemisc2"><select id="misctype" onclick="onclktypMisc();">
			<option value="1">Extra Day (+)</option>
			<option value="2">Extra Time (+)</option>
			<option value="3">Taxable Income Adjustment</option>
			<option value="4">Tardy</option>
			<option value="5">Extra Day (-)</option>
			<option value="6">Extra Time (-)</option>
		</select>
		</div>
	</div><br>
	<div id="categ">
		<div class="inlinemisc1">Days : </div>
		<div class="inlinemisc2">
			<select id='miscdays'>
				<option value="1">1</option>
				<option value="1.5">1.5</option>
				<option value="2">2</option>
				<option value="2.5">2.5</option>
				<option value="3">3</option>
				<option value="3.5">3.5</option>
				<option value="4">4</option>
				<option value="4.5">4.5</option>
				<option value="5">5</option>
				<option value="5.5">5.5</option>
			</select>
		</div>
	</div><br>
	<div>
		<div class="inlinemisc1">
			Remarks : 
		</div>
		<div class="inlinemisc2">
			<textarea class="txtremmisc" id="miscremarks"></textarea>
		</div>
	</div><br>
	<div style="text-align:right;width:380px;">
		<input type="button" name="put" value="Save" onclick="onClkSaveMisc();">
	</div>
</div>

<input type="hidden" id="typ">
<input type="hidden" id="chk">
<input type="hidden" id="em_id">
<input type="hidden" id="date1">
<input type="hidden" id="date2">
<input type="hidden" id="msc">
<input type="hidden" name="count" id="count" value="<?php echo $y; ?>">
<input type="hidden" name="paytype" id="paytype" value="<?php echo $_GET['type']; ?>">
<input type="hidden" id="em_idorig">
</form>
</body>

<script>
	onMicx('<?php echo $_GET['emid']; ?>');
</script>