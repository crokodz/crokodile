<?php
require ('../config.php');
require ('class.ezpdf.php');

if ($_SESSION['company'] != '0'){
	$select = "SELECT * FROM posted_summary where company_id = '" . $_SESSION['company'] . "' and posted_id = '" . $_GET['pid'] . "' GROUP BY posted_id";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if (empty($row['posted_id'])){
		echo "<script>";
		echo "window.close();";
		echo "alert('Unauthorized access not allowed!!! Alert has been send to administrator!!! Administrator has the authority to block your IP w/o any warning...')";
		echo "</script>";
		}
	}

function h2m($hours){
	$expl = explode(":", $hours);
	return ($expl[0] * 60) + $expl[1];
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

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function GetInfo($id){
	$select = "select `name`,`ts`,`pay_id`,`company_id`, `salary_based`, `salary`, employee_status from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['name'],$row['ts'],$row['pay_id'],$row['company_id'],$row['salary_based'],$row['salary'], $row['employee_status']);
	}

function GetSummary($id,$pid){
	$select = "select * from posted_summary where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function GetAdjustments($id,$pid){
	$select = "select * from employee_adjustments where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
}

function getDayType($day,$id,$x9){
	if($x9){
		$select = "select 	`type` from employee_ot where id = '" . $x9 . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return $row['type'];
		}
	else{
		$select = "select 	`status` from transaction where em_id = '" . $id . "' and `trxn_date` = '" . $day . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return $row['status'];
		}
	}


function GetTYDTax($id, $date){

	$date = explode("@", $date);
	$date = explode("-", $date[1]);
	$year = $date[0];

	// $select = "select * from yearly_cutoff where date1 <= '" . $date . "' and date2 >= '" . $date . "'";
	// $result = mysql_query($select, connect());
	// $rowz = mysql_fetch_array($result,MYSQL_ASSOC);

	$select = "select sum(`taxable_salary`) as `taxable_salary` ,sum(`tax`) as `tax` ,sum(`sss`) as `sss` ,sum(`ph`) as `ph` ,sum(`pi`) as `pi` from posted_summary where em_id = '" . $id . "' and `payday` like '%" . $year . "%' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['taxable_salary'],$row['tax']);
	}

function getDeductionBal($subid, $pid, $emid){
	$select1 = "select `to`, `em_id` from posted_summary where `posted_id` = '" . $pid . "' and em_id = '" . $emid . "' ";
	$result1 = mysql_query($select1, connect());
	$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);

	$select2 = "select `posted_id` from posted_summary where `to` <= '" . $row1['to'] . "' and `em_id` = '" . $row1['em_id'] . "'  and `posted_id` != 0 ";
	$amt = 0;
	$result2 = mysql_query($select2, connect());
	while($row2 = mysql_fetch_array($result2,MYSQL_ASSOC)){
		$select = "select `amount` from employee_deduction where `sub_id` = '" .  $subid. "' and posted_id = '" . $row2['posted_id'] . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$amt = $amt + $row['amount'];
		}

	$select = "select name, sum(amount) as `bal` from employee_deduction where `sub_id` = '" .  $subid. "' and status = 'pending' group by `sub_id`";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);

	return $row['bal'];
	}

function gogogogo($id, $pid,$pdf){
	$info = GetInfo($id);
	$summary = GetSummary($id, $pid);
	$company = getcompany($info[3]);

	$salary = $summary['salary'];
	$otherstaxable = $summary['other_tax_inc'];
	$taxable = $summary['taxable_salary'];
	$tin = $summary['tax'];
	$sss = $summary['sss'];
	$ph = $summary['ph'];
	$pi = $summary['pi'];
	$otherdeduction = $summary['deduction'];
	$othersnontaxable = $summary['nontax'];
	$netpay = $summary['netpay'];
	//$absent = $summary['absent'];
	$gross =  $summary['taxable_salary'] - $summary['tax'];
	$late = $summary['late'];
	$ot = $summary['ot'];
	$ut = $summary['ut'];
	$halfday = 0;
	$absent = 0;
	$perday_salary = $summary['perday_salary'];
	$type = $summary['post_type'];

	$result = explode("@@",$summary['nt_value']);
	$taxrefund = 0;
	for($k=0;$k<=count($result);$k++){
		if($result[$k]){
			$select = "select name, amount from employee_non_taxable where `id` = '" . $result[$k] . "' and name = 'Tax RefundX'  and status = 'posted'";
			$resultz = mysql_query($select, connect());
			$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
			$taxrefund = $taxrefund + $row['amount'];
			}
		}


	//~ $result = explode("@@",$summary['late_value']);
	//~ for($x=0;$x<count($result)-1;$x++){
		//~ $latez = explode("||", $late[$x]);
		//~ if($latez[2]>0){
			//~ $reglate = $reglate + $latez[2];
			//~ }
		//~ else{
			//~ $adjlate = $adjlate + ($latez[2]*-1);
			//~ }
		//~ }

	//~ $result = explode("@@",$summary['ut_value']);
	//~ for($x=0;$x<count($result)-1;$x++){
		//~ $utz = explode("||", $late[$x]);
		//~ if($utz[2]>0){
			//~ $regut = $regut + $utz[2];
			//~ }
		//~ else{
			//~ $adjut = $adjut + ($utz[2]*-1);
			//~ }
		//~ }





	if($taxrefund>0){
		$othersnontaxable = $othersnontaxable - $taxrefund;
		$tin = $taxrefund - $tin;
		$tin = roundoff($tin,2);
		$tin = "(".$tin.")";
		$gross = $gross + $taxrefund;
		}
	else{
		$othersnontaxable = $othersnontaxable - $taxrefund;
		$tin = $tin - $taxrefund;
		$tin = roundoff($tin,2);
		}

	#absent
	//if($type != 'RESIGNED' and $summary['absent'] > 0){
		$abs = $summary['abs_value'];
		$abs = explode("@@",$abs);
		$cntabs = 0;
		$cnthd = 0;
		for($x=0;$x<=count($abs);$x++){
			$absz = explode("||",$abs[$x]);
			if($absz[0]){
				if($absz[2] == 'ABSENT' or ($absz[2] == 'LWOP' and $type != 'RESIGNED' and $summary['absent'] > 0) or $absz[2] == 'AWOL' or $absz[2] == 'EXTRA DAY'){
					if($absz[1]>0){
						$cntabs++;
						$absent = $absent + ($absz[1] * $perday_salary);
						}
					else{
						//$cntabsadj = $cntabsadj + ($absz[1]*-1);
						//$adjabsent = $adjabsent - (($absz[1]*-1)*$perday_salary);
						//$totaladjustment = $totaladjustment + $adjabsent;
						}
					}
				elseif($absz[2] == 'LWOP' and $type == 'RESIGNED' and $summary['absent'] > 0){
					$cntabs++;
					$absent = $absent + ($absz[1] * $perday_salary);
					}
				elseif($absz[2] == 'HALF DAY'){
					$halfday = $halfday + ($absz[1] * $perday_salary);
					$cnthd++;
					}
				}
			}
	//	}


	$adjx = 0;
	$selectadj = "select * from employee_adjustments where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$resultadj = mysql_query($selectadj, connect());
	while($rowadj = mysql_fetch_array($resultadj,MYSQL_ASSOC)){
		$adjx += $rowadj['amount'];
		if($rowadj['name'] == 'ABSENT'){
			$cntabsadj++;
			$adjabsent = $adjabsent + ($rowadj['amount']);
		}
		if($rowadj['name'] == 'LATE'){
			$$cntladj = $$cntladj + ($rowadj['amount']*-1);
		}
		if($rowadj['name'] == 'UNDER TIME'){
			$cntutadj = $cntutadj + ($rowadj['amount']*-1);
		}
		if($rowadj['name'] == 'UNDER TIME'){
			$cntutadj = $cntutadj + ($rowadj['amount']*-1);
		}
	}


	$totaladjustment = $totaladjustment + $late + $ut + $halfday + $absent + $adjx;



	#late
	$l = $summary['late_value'];
	$l = explode("@@",$l);
	$cntl = 0;
	for($x=0;$x<=count($l);$x++){
		if($l[$x]){
			$rl = explode("||", $l[$x]);
			if($rl[2]>0){
				$cntl = $cntl + $rl[1];
				$regl = $regl + $rl[2];
				}
			else{
				$cntladj = $cntladj + $rl[1];
				$adjl = $adjl + ($rl[2]*-1);
				}
			}
		}

	#ut
	$u = $summary['ut_value'];
	$u = explode("@@",$u);
	$cntut = 0;
	$cntutadj = 0;
	$adjut = 0;
	$regut = 0;
	for($x=0;$x<=count($u);$x++){
		if($u[$x]){
			$ur = explode("||", $u[$x]);
			if($ur[2]>0){
				$cntut = $cntut + $ur[1];
				$regut = $regut + $ur[2];
				}
			else{
				$cntutadj = $cntutadj + $ur[1];
				$adjut = $adjut + ($ur[2]*-1);
				}
			}
		}

	$pdf->addText(17,365,10,'<b>' . $company['name'] . '</b>');

	if($_SESSION['company'] == 7){
		$image = imagecreatefromjpeg("ibmi.jpg");
		$pdf->addImage($image, 490, 30, 100, 100);
	} else if($info[6] == 'TALENTS' || $info[2] == 'DRAMA-CEBU' || $info[2] == 'DRAMA-BACOLOD'){

	} else {
		$image = imagecreatefromjpeg("logo.jpg");
		$pdf->addImage($image, 490, 30, 100, 100);
	}


	// $image = imagecreatefrompng("logo.jpg");
	// $pdf->addImage($image, 200, 200, 10, 10);

	if($totaladjustment<0){
		$wwwss = roundoffNoComma($totaladjustment)*-1;
		$totaladjustmentTXT = "(". $wwwss . ")";
		}
	else{
		$totaladjustmentTXT = roundoff($totaladjustment,2);
		}

	$data = array(
		array('Basic Salary',roundoff($salary,2)),
		array('Adjustments*',$totaladjustmentTXT),
		array('OT*',roundoff($summary['ot'],2)),
		array('Othr Tx Inc*',roundoff($otherstaxable,2)),
		array('Others',roundoff(0,2)),
		array('Gross Taxable',roundoff($taxable,2)),
		array('Less: W/H Tax',$tin),
		array('Gross After Tax',roundoff($gross,2)),
		array(' SSS Premium',roundoff($sss,2)),
		array(' PhilHealth',roundoff($ph,2)),
		array(' Pag-Ibig',roundoff($pi,2)),
		array(' Loan Pymts*',roundoff(0,2)),
		array(' Other Ded/(Add)',roundoff($otherdeduction,2)),
		array('Add : ',''),
		array(' NoTax Inc/Pmt',roundoff($othersnontaxable,2))
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>90),
		array('justification'=>'right','width'=>70)
		)));
		$pdf->ezText('');
		$data = array(
		array('<b>Net Pay</b>','<b>'.roundoff($netpay,2).'</b>')
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));

	$pdf->ezText('');
		$data = array(
		array('<b>Tax Status : ' . $info[1] . '</b>',''),
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));

	$pdf->ezText('');
		$data = array(
		array('','')
		);
	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>100),
		array('justification'=>'right','width'=>60)
		)));


	#$pdf->ezText('');
	$data = array(array($info[2],''));

	$pdf->ezTable($data,$cols,'',array('showHeadings'=>0,'showLines'=>0,'shaded'=>0,'xPos'=>'left','xOrientation'=>'right','width'=>490,'fontSize' => 9,'cols'=>array(
		array('justification'=>'left','width'=>140),
		array('justification'=>'right','width'=>40)
		)));

	$pdf->addText(110,292,8,'<b>____________</b>');
	$pdf->addText(110,264,8,'<b>____________</b>');
	$pdf->addText(110,251,8,'<b>____________</b>');
	$pdf->addText(110,138,8,'<b>____________</b>');

	$pdf->addText(17,73,8,'<b>' . $info[0] . '</b> - ' . $_GET['id']);

	#
	$pdf->addText(450,60,8,$now);

	##
	$pp = explode("@", $summary['payday']);
	if($pp[0] == 'w1'){
		$ppz = 'First Pay Day of ' . $pp[1];
		}
	else{
		$ppz = 'Second Pay Day of ' . $pp[1];
		}
	$pdf->addText(16,40,8,'Payroll# : ' . $_GET['pid'] . '       <b>Pay Date<b> : ' . $summary['title']);
		#row2
	$pdf->addText(180,345,8,'Misc. Salary Adjustments');
		$pdf->addText(180,325,8,'ABSENT(' . $cntabs . ')');
		$pdf->addText(246,325,8,make10($absent));
		$pdf->addText(180,315,8,'LATE(' . m2h($cntl) . ')');
		$pdf->addText(246,315,8,make10($regl));
		$pdf->addText(180,305,8,'UNDERTIME(' . m2h($cntut) . ')');
		$pdf->addText(246,305,8,make10($regut));
		$pdf->addText(180,295,8,'HALFDAY(' . $cnthd . ')');
		$pdf->addText(246,295,8,make10($halfday));

		$pdf->addText(180,285,8,'ADJ-LATE(' . m2h($cntladj) . ')');
		$pdf->addText(246,285,8,make1010($adjl));
		$pdf->addText(180,275,8,'ADJ-UT(' . m2h($cntutadj) . ')');
		$pdf->addText(246,275,8,make1010($adjut));
		$pdf->addText(180,265,8,'ADJ-ABSENT(' . $cntabsadj . ')');
		$pdf->addText(246,265,8,make10($adjabsent));

		$pdf->addText(180,245,8,'<b>TOTAL</b>');
		$pdf->addText(246,245,8,make10($totaladjustment));

		$pdf->addText(180,220,8,'Other Tax Income Detail');
		$result = explode("@@",$summary['oth_value']);
		$x = 210;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount from employee_taxable where id = '" . $result[$k] . "' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				if($row['name'] == 'ADDITIONAL INCOME'){
					$row['name'] = 'ADD. INCOME';
					}
				$pdf->addText(180,$x,8,$row['name']);
				$pdf->addText(246,$x,8,make10($row['amount']));
				$total = $total + $row['amount'];
				$x = $x - 10;
				}
			}
		$pdf->addText(180,$x,8,"<b>TOTAL</b>");
		$pdf->addText(246,$x,8,make10($total));

	//~ $pdf->addText(180,190,8,'Other Deductions/(Additions)');
		//~ $result = explode("@@",$summary['ded_value']);
		//~ $x = 170;
		//~ $total = 0;
		//~ for($k=0;$k<=count($result);$k++){
			//~ if($result[$k]){
				//~ $select = "select name, amount, balance from employee_deduction where deduct_id = '" . $result[$k] . "' ";
				//~ $resultz = mysql_query($select, connect());
				//~ $row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				//~ $pdf->addText(180,$x,8,substr($row['name'],0,15));
				//~ $pdf->addText(246,$x,8,make10($row['amount']));
				//~ $total = $total + $row['amount'];
				//~ $x = $x - 10;
				//~ }
			//~ }
		//~ $pdf->addText(180,$x,8,"<b>TOTAL</b>");
		//~ $pdf->addText(246,$x,8,make10($total));
		//~ #row3

	$pdf->addText(310,345,8,'Non-Taxable Income/(Pyt)');
		$result = explode("@@",$summary['nt_value']);
		$x = 325;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount from employee_non_taxable where `id` = '" . $result[$k] . "' and name != 'Tax RefundX' and status = 'posted' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);
				$pdf->addText(310,$x,8,$row['name']);
				$pdf->addText(370,$x,8,make10($row['amount']));
				$total = $total + $row['amount'];
				$x = $x -10;
				}
			}
		$pdf->addText(310,$x,8,"<b>TOTAL</b>");
		$pdf->addText(370,$x,8,make10($total));

	$pdf->addText(310,250,8,'Over Time OT/ND Hrs.');
		$result = explode("@@",$summary['ot_value']);
		$adjustment = 0;
		$resot = 0;
		$sphol = 0;
		$lghol = 0;
		$regot = 0;
		$otx = 0;
		$ot = 0;
		$nd = 0;
		$otamt = 0;
		$otxamt = 0;
		$ndamt = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$dedz = explode("||", $result[$k]);
				if($dedz[9]){
					$x9 = $dedz[9];
					}
				else{
					$x9 = false;
					}

				$dtype = getDayType($dedz[0],$id,$x9);
				//echo $dtype . "--" .  $dedz[6]."<br>";

				if($dtype=='RESTDAY' or $dtype=='HALF DAY'){
					$resot =  $resot + $dedz[1];
					$resota =  $resota + $dedz[5];
					$resotx = $resotx + $dedz[2];
					$resotxamt = $resotxamt + $dedz[6];
					}
				elseif($dtype=='REGULAR'){
					$regot =  $regot + $dedz[1];
					$regota =  $regota + $dedz[5];
					$regotx = $regotx + $dedz[2];
					$regotxamt = $regotxamt + $dedz[6];
					}
				elseif($dtype=='LEGAL HOLIDAY'){
					$lghol =  $lghol + $dedz[1];
					$lghola =  $lghola + $dedz[5];
					$lgotx = $lgotx + $dedz[2];
					$lgotxamt = $lgotxamt + $dedz[6];
					}
				elseif($dtype=='SPECIAL HOLIDAY'){
					$sphol =  $sphol + $dedz[1];
					$sphola =  $sphola + $dedz[5];
					$spotx = $spotx + $dedz[2];
					$spotxamt = $spotxamt + $dedz[6];
					}
				elseif($dtype=='LEGAL HOLIDAY RESTDAY'){
					$lgholr =  $lgholr + $dedz[1];
					$lgholra =  $lgholra + $dedz[5];
					$lgotxr = $lgotxr + $dedz[2];
					$lgotxamtr = $lgotxamtr + $dedz[6];
					}
				elseif($dtype=='SPECIAL HOLIDAY RESTDAY'){
					$spholr =  $spholr + $dedz[1];
					$spholra =  $spholra + $dedz[5];
					$spotxr = $spotxr + $dedz[2];
					$spotxamtr = $spotxamtr + $dedz[6];
					}
				else{
					$regot =  $regot + $dedz[1];
					$regota =  $regota + $dedz[5];
					$regotx = $regotx + $dedz[2];
					$regotxamt = $regotxamt + $dedz[6];
					//~ $otx = $otx + $dedz[2];
					//~ $otxamt = $otxamt + $dedz[6];
					//~ $ot = $ot + $dedz[1];
					//~ $otamt = $otamt + $dedz[5];
					}



				$nd = $nd + $dedz[3];
				$ndamt = $ndamt + $dedz[7];
				}
			}
		//die();
		$x = 230;
		$total = 0;
		for($j=0;$j<1;$j++){
			if ($adjustment != 0){
				$pdf->addText(310,$x,8, "ADJ" . "(" . 0 . ")");
				$pdf->addText(370,$x,8,make10(0));
				$x = $x -10;
				$total = $total + 0;
				}

			//REGULAR
			if ($regot != 0){
				$pdf->addText(310,$x,8,"REG-OT" . "(" . (m2h($regot)) . ")");
				$pdf->addText(370,$x,8,make10($regota));
				$x = $x -10;
				$total = $total + $regota;
				}
			if ($regotx != 0){
				$pdf->addText(310,$x,8,"REG-OX" . "(" . (m2h($regotx)) . ")");
				$pdf->addText(370,$x,8,make10($regotxamt));
				$x = $x -10;
				$total = $total + $regotxamt;
				}


			//RESTDAY
			if ($resot != 0){
				$pdf->addText(310,$x,8,"REST-OT" . "(" . (m2h($resot)) . ")");
				$pdf->addText(370,$x,8,make10($resota));
				$x = $x -10;
				$total = $total + $resota;
				}
			if($resotx != 0){
				$pdf->addText(310,$x,8,"RES-OX" . "(" . (m2h($resotx)) . ")");
				$pdf->addText(370,$x,8,make10($resotxamt));
				$x = $x -10;
				$total = $total + $resotxamt;
				}

			//SH
			if ($sphol != 0){
				$pdf->addText(310,$x,8,"SH-OT" . "(" . (m2h($sphol)) . ")");
				$pdf->addText(370,$x,8,make10($sphola));
				$x = $x -10;
				$total = $total + $sphola;
				}
			if($spotx != 0){
				$pdf->addText(310,$x,8,"SH-OX" . "(" . (m2h($spotx)) . ")");
				$pdf->addText(370,$x,8,make10($spotxamt));
				$x = $x -10;
				$total = $total + $spotxamt;
				}

			//LH
			if ($lghol != 0){
				$pdf->addText(310,$x,8,"LH-OT" . "(" . (m2h($lghol)) . ")");
				$pdf->addText(370,$x,8,make10($lghola));
				$x = $x -10;
				$total = $total + $lghola;
				}
			if($lgotx != 0){
				$pdf->addText(310,$x,8,"LH-OX" . "(" . (m2h($lgotx)) . ")");
				$pdf->addText(370,$x,8,make10($lgotxamt));
				$x = $x -10;
				$total = $total + $lgotxamt;
				}

			//SHR
			if ($spholr != 0){
				$pdf->addText(310,$x,8,"SH-ROT" . "(" . (m2h($spholr)) . ")");
				$pdf->addText(370,$x,8,make10($spholra));
				$x = $x -10;
				$total = $total + $spholra;
				}
			if($spotxr != 0){
				$pdf->addText(310,$x,8,"SH-ROX" . "(" . (m2h($spotxr)) . ")");
				$pdf->addText(370,$x,8,make10($spotxamtr));
				$x = $x -10;
				$total = $total + $spotxamtr;
				}

			//LHR
			if ($lgholr != 0){
				$pdf->addText(310,$x,8,"LH-ROT" . "(" . (m2h($lgholr)) . ")");
				$pdf->addText(370,$x,8,make10($lgholra));
				$x = $x -10;
				$total = $total + $lgholra;
				}
			if($lgotxr != 0){
				$pdf->addText(310,$x,8,"LH-ROX" . "(" . (m2h($lgotxr)) . ")");
				$pdf->addText(370,$x,8,make10($lgotxamtr));
				$x = $x -10;
				$total = $total + $lgotxamtr;
				}

			//ND
			if ($nd != 0){
				$pdf->addText(310,$x,8,"REG-ND" . "(" . (m2h($nd)) . ")");
				$pdf->addText(370,$x,8,make10($ndamt));
				$x = $x -10;
				$total = $total + $ndamt;
				}






			//~ if ($ot > 0){
				//~ $pdf->addText(315,$x,8,"OTH-OT" . "(" . (m2h($ot)) . ")");
				//~ $pdf->addText(370,$x,8,make10($otamt));
				//~ $x = $x -10;
				//~ $total = $total + $otamt;
				//~ }

			//~ if ($sphol > 0){
				//~ $pdf->addText(315,$x,8,"SH-OT" . "(" . (m2h($sphol)) . ")");
				//~ $pdf->addText(370,$x,8,make10($sphola));
				//~ $x = $x -10;
				//~ $total = $total + $sphola;
				//~ }




			//~ if($regotx>0){
				//~ $pdf->addText(315,$x,8,"REG-OX" . "(" . (m2h($regotx)) . ")");
				//~ $pdf->addText(370,$x,8,make10($regotxamt));
				//~ $x = $x -10;
				//~ $total = $total + $regotxamt;
				//~ }
			//~ if($spotx>0){
				//~ $pdf->addText(315,$x,8,"SH-OX" . "(" . (m2h($spotx)) . ")");
				//~ $pdf->addText(370,$x,8,make10($spotxamt));
				//~ $x = $x -10;
				//~ $total = $total + $spotxamt;
				//~ }

			}

		$pdf->addText(310,$x,8,"<b>TOTAL</b>");
		$pdf->addText(370,$x,8,make10($total));


	//~ #row4
	$tax = GetTYDTax($id,$summary['payday']);

	$pdf->addText(435,345,8,'Year-To-Date Summaries');
	$pdf->addText(435,325,8,'YTD W/H Tax:');
		$pdf->addText(530,325,8,make10($tax[1]));
	$pdf->addText(435,315,8,'YTD Taxable Inc.');
		$pdf->addText(530,315,8,make10($tax[0]));
	$pdf->addText(435,305,8,'YTD Txbl Bon/13th');
		$pdf->addText(530,305,8,make10(0));
	$pdf->addText(435,295,8,'YTD NTx Bon/13th');
		$pdf->addText(530,295,8,make10(0));
	$pdf->addText(435,270,8,'Deduction / Loan Balance');
		$result = explode("@@",$summary['ded_value']);
		$x = 260;
		$total = 0;
		for($k=0;$k<=count($result);$k++){
			if($result[$k]){
				$select = "select name, amount, balance, sub_id from employee_deduction where deduct_id = '" . $result[$k] . "' ";
				$resultz = mysql_query($select, connect());
				$row = mysql_fetch_array($resultz,MYSQL_ASSOC);

				$balz = getDeductionBal($row['sub_id'],$pid, $id);

				$pdf->addText(435,$x,8,substr($row['name'],0,11));
				$pdf->addText(480,$x,8,make10($row['amount']));
				$pdf->addText(530,$x,8,make10($balz));
				$total = $total + $row['amount'];
				$x = $x - 10;
				}
			}
		$pdf->addText(435,$x,8,"<b>TOTAL</b>");
		$pdf->addText(480,$x,8,make10($total));

		//~ $result = getDeductionBal($id);
		//~ $x = 220;
		//~ $total = 0;
		//~ while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			//~ $pdf->addText(435,$x,8,$row['name']);
			//~ $pdf->addText(550,$x,8,make10($row['bal']));
			//~ $total = $total + $row['bal'];
			//~ $x = $x - 10;
			//~ }
	#lines
	for ($x=85;$x<350;$x++){
		$pdf->addText(170,$x,8,'|');
		}
	$pdf->ezNewPage();
	return $pdf;
	}


function make1010($amount){
	$amount = roundoff($amount,2);
	if (strlen($amount) == 2){
		return "         " . $amount;
		}
	elseif (strlen($amount) == 3){
		return "        " . $amount;
		}
	elseif (strlen($amount) == 4){
		return "       " . $amount;
		}
	elseif (strlen($amount) == 5){
		return "      " . $amount;
		}
	elseif (strlen($amount) == 6){
		return "     " . $amount;
		}
	elseif (strlen($amount) == 7){
		return "    " . $amount;
		}
	elseif (strlen($amount) == 8){
		return "   " . $amount;
		}
	elseif (strlen($amount) == 9){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 10){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 11){
		return " " . $amount;
		}
	elseif (strlen($amount) == 12){
		return $amount;
		}
	}

function make10($amount){
	$amount = roundoff($amount,2);

	if (strlen($amount) == 2){
		return "         " . $amount;
		}
	elseif (strlen($amount) == 3){
		return "        " . $amount;
		}
	elseif (strlen($amount) == 4){
		return "       " . $amount;
		}
	elseif (strlen($amount) == 5){
		return "      " . $amount;
		}
	elseif (strlen($amount) == 6){
		return "     " . $amount;
		}
	elseif (strlen($amount) == 7){
		return "    " . $amount;
		}
	elseif (strlen($amount) == 8){
		return "   " . $amount;
		}
	elseif (strlen($amount) == 9){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 10){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 11){
		return " " . $amount;
		}
	elseif (strlen($amount) == 12){
		return $amount;
		}
	}

$pdf =& new Cezpdf('LETTERHALF','portrait');
$pdf->selectFont('./fonts/Courier.afm');
$pdf->ezSetCmMargins(1.3,.5,.60,.5);

#ALL
if ($_GET['id'] == 'ALL'){
	if($_GET['iddx']){
		$paysel  = "";
		$pcd = explode("@@",$_GET['iddx']);
		for($xy=0;$xy<count($pcd);$xy++){
			if($pcd[$xy]){
				if($xy==count($pcd)-2){
					$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' ";
					}
				else{
					$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' OR ";
					}
				}
			}
		$paysel = " and (".$paysel.") ";
	}

	$select = "select posted_summary.* from posted_summary join employee using(em_id) where posted_id = '" . $_GET['pid'] . "' " . $paysel . " group by em_id limit " . $_GET['max'] . ", 100";
	$result_summary = mysql_query($select, connect());
	while($row_summary = mysql_fetch_array($result_summary,MYSQL_ASSOC)){
		$_GET['id'] = $row_summary['em_id'];
		$pdf = gogogogo($_GET['id'], $_GET['pid'],$pdf);
		}
	}

#INDIVIDUAL
else{
	$pdf = gogogogo($_GET['id'], $_GET['pid'],$pdf);
	}

$pdfcode = $pdf->ezOutput(0);
$pdf->ezStream($pdfcode);
?>
