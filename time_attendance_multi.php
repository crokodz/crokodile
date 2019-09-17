<script type="text/javascript"><!--
/*
   Float Submit Button To Right Edge Of Window
   Version 1.0
   April 11, 2010

   Will Bontrager
   http://www.willmaster.com/
   Copyright 2010 Bontrager Connection, LLC

   Generated with customizations on January 24, 2011 at
   http://www.willmaster.com/library/manage-forms/floating-submit-button.php

   Bontrager Connection, LLC grants you
   a royalty free license to use or modify
   this software provided this notice appears
   on all copies. This software is provided
   "AS IS," without a warranty of any kind.
*/

//*****************************//

/** Five places to customize **/

// Place 1:
// The id value of the button.

var ButtonId = "save";


// Place 2:
// The width of the button.

var ButtonWidth = 100;


// Place 3:
// Left/Right location of button (specify "left" or "right").

var ButtonLocation = "right";


// Place 4:
// How much space (in pixels) between button and window left/right edge.

var SpaceBetweenButtonAndEdge = 10;


// Place 5:
// How much space (in pixels) between button and window top edge.

var SpaceBetweenButtonAndTop = 10;


/** No other customization required. **/

//************************************//

TotalWidth = parseInt(ButtonWidth) + parseInt(SpaceBetweenButtonAndEdge);
ButtonLocation = ButtonLocation.toLowerCase();
ButtonLocation = ButtonLocation.substr(0,1);
var ButtonOnLeftEdge = (ButtonLocation=='l') ? true : false;

function AddButtonPlacementEvents(f)
{
   var cache = window.onload;
   if(typeof window.onload != 'function') { window.onload = f; }
   else { window.onload = function() { if(cache) { cache(); } f(); }; }
   cache = window.onresize;
   if(typeof window.onresize != 'function') { window.onresize = f; }
   else { window.onresize = function() { if(cache) { cache(); } f(); }; }
}

function WindowHasScrollbar() {
var ht = 0;
if(document.all) {
   if(document.documentElement) { ht = document.documentElement.clientHeight; }
   else { ht = document.body.clientHeight; }
   }
else { ht = window.innerHeight; }
if (document.body.offsetHeight > ht) { return true; }
else { return false; }
}

function GlueButton(ledge) {
var did = document.getElementById(ButtonId);
did.style.top = SpaceBetweenButtonAndTop + "px";
did.style.width = ButtonWidth + "px";
did.style.left = ledge + "px";
did.style.display = "block";
did.style.zIndex = "9999";
did.style.position = "fixed";
}

function PlaceTheButton() {
if(ButtonOnLeftEdge) {
   GlueButton(SpaceBetweenButtonAndEdge);
   return;
   }
if(document.documentElement && document.documentElement.clientWidth) { GlueButton(document.documentElement.clientWidth-TotalWidth); }
else {
   if(navigator.userAgent.indexOf('MSIE') > 0) { GlueButton(document.body.clientWidth-TotalWidth+19); }
   else {
      var scroll = WindowHasScrollbar() ? 0 : 15;
      if(typeof window.innerWidth == 'number') { GlueButton(window.innerWidth-TotalWidth-15+scroll); }
      else { GlueButton(document.body.clientWidth-TotalWidth+15); }
      }
   }
}

AddButtonPlacementEvents(PlaceTheButton);
//--></script>





<?php
include "config.php";

$fx = $_GET['from'];
$tx = $_GET['to'];
$idx = $_GET['id'];
$iddx = $_GET['idd'];

if($_SESSION['pay_id']){
	$ddd = str_replace("@@","",$_GET['idd']);
	if($ddd != $_SESSION['pay_id']){
		echo "This request is send to HR for immediate action. You are trying to access beyond your control. Thanks for your hard work...";
		die();
		}
	}

if($_GET['idd']){
	$paysel  = "";
	$paysel1  = "";
	$pcd = explode("@@",$_GET['idd']);
	for($xy=0;$xy<count($pcd);$xy++){
		if($pcd[$xy]){
			if($xy==count($pcd)-2){
				$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' ";
				$paysel1 = $paysel1 . " employee.`pay_id` = '" . $pcd[$xy] . "' OR employee.pay_id_sub = '" . $pcd[$xy] . "'  ";
				}
			else{
				$paysel = $paysel . " employee.`pay_id` = '" . $pcd[$xy] . "' OR ";
				$paysel1 = $paysel1 . " employee.`pay_id` = '" . $pcd[$xy] . "'  OR employee.pay_id_sub = '" . $pcd[$xy] . "' OR ";
				}
			}
		}
	$paysel = " and (".$paysel.") ";
	$paysel1 = " and (".$paysel1.") ";
	$idd = $paysel;
	#$idd= " and employee.pay_id = '" . $_GET['idd'] . "' ";
	}

$from_date = $_GET['from'];
$to_date = $_GET['to'];

$result = mysql_query("select *from users;", connect());

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

function h2m($hours){
	$expl = explode(":", $hours);
	return ($expl[0] * 60) + $expl[1];
	}

function getndl($ti,$to,$nd,$ndb,$break){
	if($to > 1800){
		$to = 1800;
		}

	if($ti>$nd){
		$b=$ti-$nd;
		}

	if($to > $nd){
		$a = $to - $nd;
		}

	$nd = $a - $b;
	if($nd >= 30){
		if($nd > 300){
			#$nd = $nd - $break;
			$nd = $nd;
			}

		if($nd > 480){
			$nd = 480;
			}
		return $nd;
		}
	else{
		if($ti < 360 && $ti > 0){
			$nd = 360 - $ti;
			return $nd;
		} else {
			return 0;
			}
		}
	}

function cutz($time){
	$s = split(':', $time);
	return $s[0].":".$s[1];
	}

function getot($company,$start_ot,$end_ot){
	$select = "select * from company where id = '" . $company . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);

	$nd = h2m($row['night_differential']);
	$eo = h2m($end_ot);
	$so = h2m($start_ot);

	if($nd){
		if ($eo >= $nd){
			return $nd - $so;
			}
		else{
			return $eo - $so;
			}
		}
	else{
		return $eo - $so;
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

function GetPaycode($id){
	$select = "select `from`,`to` from shift where shift_code = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['from'],$row['to']);
	}

function GetShift($id){
	$select = "select * from shift where shift_code = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['from'],$row['to'],$row['break'],$row['start_ndiff'],$row['end_ndiff']);
	}


if(isset($_POST['save'])){
	for ($x=0;$x<$_POST['count'];$x++){

		$timein = $_POST['in' . $x];
		$timeout = $_POST['out' . $x];

		$id = $_POST['id' . $x];
		$status = $_POST['status' . $x];
		$shiftcode = $_POST['shift_code' . $x];
		$ot_in = $_POST['ot_in' . $x];
		$ot_out = $_POST['ot_out' . $x];
		$cb = $_POST['cb' . $x];
		$cbot = $_POST['cbot' . $x];
		$otrem = addslashes($_POST['otrem' . $x]);

		$company = $_POST['company' . $x];

		#####
		if(!$shiftcode){
			$shiftcode = $row['shift_code'];
			}

		$shift = GetShift($shiftcode);
		$ti = h2m($timein);
		$to = h2m($timeout);
		$cti = h2m($shift[0]);
		$cto = h2m($shift[1]);
		$break = $shift[2];
		$ndin = h2m($shift[3]);
		$ndout = h2m($shift[4]);


		$ot_inx = h2m($ot_in);
		$ot_outx = h2m($ot_out);

		$otx = 0;
		$ot = 0;
		###
		$nd = getndl($ti,$to,$ndin,$ndout,$break);

		if(($ot_inx > 0 and $ot_outx > 0) or $nd > 0){
			$cbot = "checked";
			$ot = $ot_outx - $ot_inx;
			if($ot >= 300){
				#$ot = $ot-$break;
				if($_POST['status' . $x] == 'RESTDAY'  or $_POST['status' . $x] == 'LEGAL HOLIDAY RESTDAY'  or $_POST['status' . $x] == 'SPECIAL HOLIDAY RESTDAY'   or $_POST['status' . $x] == 'LEGAL HOLIDAY'  or $_POST['status' . $x] == 'SPECIAL HOLIDAY'){
					$ot = $ot-$break;
					}
				}
			if($ot > 480){
				$otx = $ot - 480;
				$ot = 480;
				}
			}
		else{
			$cbot = "";
			$ot = 0;
			$otx = 0;
			}



		####
		if($to > $cto and $to > 0){
			$to = $cto;
			}
		if($ti < $cti and $ti > 0){
			$ti = $cti;
			}
		$total = $to - $ti;
		if($total<0){
			$total = 0;
			}
		$ut = ($cto - $to);
		$late = $ti - $cti;
		if($late <= 0){
			$late = 0;
		}

		if($_POST['status' . $x] == 'VACATION LEAVE' or $_POST['status' . $x] == 'SICK LEAVE'  or $_POST['status' . $x] == 'BIRTHDAY LEAVE'  or $_POST['status' . $x] == 'EMERGENCY LEAVE'  or $_POST['status' . $x] == 'MATERNITY LEAVE'  or $_POST['status' . $x] == 'PATERNITY LEAVE'  or $_POST['status' . $x] == 'BEREAVEMENT LEAVE'  or $_POST['status' . $x] == 'OTHER LEAVE'){
			$ut = "0";
			$total = 540;
			}

		if($total > 0 and $total > 300){
			$total = $total-$break;
			}
		if($cb){
			$cb="checked";
			}
		else{
			$total = 0;
			$ut = 0;
			$late = 0;
			}


		if($_POST['status' . $x] == 'VACATION LEAVE 0.5' or $_POST['status' . $x] == 'SICK LEAVE 0.5'  or $_POST['status' . $x] == 'BIRTHDAY LEAVE 0.5'  or $_POST['status' . $x] == 'EMERGENCY LEAVE 0.5'  or $_POST['status' . $x] == 'MATERNITY LEAVE 0.5'  or $_POST['status' . $x] == 'PATERNITY LEAVE 0.5'  or $_POST['status' . $x] == 'BEREAVEMENT LEAVE 0.5'  or $_POST['status' . $x] == 'OTHER LEAVE 0.5'){
			if($total >= 240){
				$late = "0";
				$ut = "0";
				}
			else{
				$late = 240 - $total;
				$ut = "0";
				}
			}

		if($_POST['status' . $x] == 'HALF DAY'){
			if($total >= 240){
				$late = "0";
				$ut = "0";
				}
			else{
				$late = 240 - $total;
				$ut = "0";
				}
			}


		//~ if(($_POST['status' . $x] == 'LEGAL HOLIDAY RESTDAY' and $timein == '00:00')  or ($_POST['status' . $x] == 'SPECIAL HOLIDAY RESTDAY' and $timein == '00:00')){
			//~ $ut = "0";
			//~ //$late = "0";
			//~ }

		//~ if(($_POST['status' . $x] == 'LEGAL HOLIDAY' and $timein == '00:00') or ($_POST['status' . $x] == 'SPECIAL HOLIDAY' and $timein == '00:00')  or ($_POST['status' . $x] == 'FIELD VISIT' and $timein == '00:00')){
			//~ $ut = "0";
			//~ //$ot = "0";
			//~ //$late = "0";
			//~ }

		if($ti>0 and $to==0){
			if($ti<=$cti){
				$xx = $cto - $cti;
				$ut = $xx - $break;
				}
			else{
				$xx = $cto - $ti;
				$ut = $xx - $break;
				}
			}

		if($_POST['status' . $x] == 'LEGAL HOLIDAY RESTDAY'  or $_POST['status' . $x] == 'SPECIAL HOLIDAY RESTDAY'){
			$ut = "0";
			$late = "0";
			}

		if($_POST['status' . $x] == 'LEGAL HOLIDAY' or $_POST['status' . $x] == 'SPECIAL HOLIDAY'  or $_POST['status' . $x] == 'FIELD VISIT'){
			$ut = "0";
			//$ot = "0";
			$late = "0";
			}

		if(($_POST['status' . $x] == 'LEGAL HOLIDAY' and $cbot == "checked") or ($_POST['status' . $x] == 'SPECIAL HOLIDAY' and $cbot == "checked") or ($_POST['status' . $x] == 'FIELD VISIT' and $cbot == "checked")){
			$ut = "0";
			$late = "0";
			//$ot = $total;
			}

		//~ if($shiftcode + 0 < 1500 and $ot_inx == 0){
			//~ $nd=0;
			//~ }





		//~ if($_POST['status' . $x] == 'REGULAR'){
			//~ if($ot_inx < $cto){

				//~ }
			//~ }

		if ($_POST['allowed_ot' . $x] == "NO" or $_POST['status' . $x] == 'UNFILED' or $ot < 0 or $_POST['status' . $x] == 'ABSENT' or $_POST['status' . $x] == 'AWOL' or $_POST['status' . $x] == 'LWOP' or $_POST['status' . $x] == 'VLWOP' or $_POST['status' . $x] == 'SUS'){
			$ot = "0";
			$otx = "0";
			$nd = "0";
			}
		if ($_POST['allowed_late' . $x] == "NO" or $_POST['status' . $x] == 'RESTDAY' or $_POST['status' . $x] == 'UNFILED' or $late < 0 or $_POST['status' . $x] == 'ABSENT' or $_POST['status' . $x] == 'AWOL' or $_POST['status' . $x] == 'LWOP' or $_POST['status' . $x] == 'VLWOP' or $_POST['status' . $x] == 'SUS'){
			$late = "0";
			}
		if ($_POST['allowed_ut' . $x] == "NO" or $_POST['status' . $x] == 'RESTDAY' or $_POST['status' . $x] == 'UNFILED' or $ut < 0 or $_POST['status' . $x] == 'ABSENT' or $_POST['status' . $x] == 'AWOL' or $_POST['status' . $x] == 'LWOP' or $_POST['status' . $x] == 'VLWOP' or $_POST['status' . $x] == 'SUS'){
			$ut = "0";
			}

		if($late>480){
			$late=480;
			}
		if($ut>480){
			$ut = 480;
			}

		$update = "update transaction set
			trxn_time_in = '" . $timein . "',
			trxn_time_out = '" . $timeout . "',
			status = '" . $status . "',
			ot = '" . $ot . "',
			shift_code = '" . $shiftcode . "',
			start_ot = '" . $start_ot . "',
			end_ot = '" . $end_ot . "',
			late = '" . $late . "',
			ut = '" . $ut . "',
			total = '" . $total . "',
			ot_wo_ndl = '" . $ot_wo_ndl . "',
			ot_w_ndl = '" . $ot_w_ndl . "',
			approved = '" . $cb . "',
			cbot = '" . $cbot . "',
			nd = '" . $nd . "',
			otx = '" . $otx . "',
			otremarks = '" . $otrem . "',
			ot_in = '" . $ot_in . "',
			ot_out = '" . $ot_out . "'
			where trxn_id = '" . $id . "'
			";
		mysql_query($update, connect());
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/imask.js"></script>
<h3 class="wintitle">Time Attendance Multi Approved</h3>
<form method="post">

<input type="submit" name="save" id="save" value="UPDATE" onclick="return checkAttendance()" style="width:70px;">

<body id="innerframe">
<table width=900px border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10% align="left">company id</td>
		<td width=90% align="letf"><?php echo $_GET['id']; ?></td>
	</tr>
	<tr>
		<td align="left">from</td>
		<td align="left"><?php echo $_GET['from']; ?></td>
	</tr>
	<tr>
		<td align="left">to</td>
		<td align="left"><?php echo $_GET['to']; ?></td>
	</tr>
	<tr>
		<td align="left" colspan=2><h1>Kindly first click UPDATE before editing, to show the accurate computation</h1></td>
	</tr>
	<tr>
		<td colspan=2 align="right"><input type="submit" name="save" value="update" onclick="return checkAttendance()" style="width:70px;"> | <input type="button" value="Extract to Excel" onclick="self.location='reports/timeattendance-exl-multi.php?from=<?php echo $fx; ?>&to=<?php echo $tx; ?>&id=<?php echo $idx; ?>&idd=<?php echo $iddx; ?>';"></td>
	</tr>
</table>
<?php
$x = 0;
if($_SESSION['user'] == 'mso'){
	if($_GET['idd'] == "ALL MSO"){
		$select = "select employee.em_id, employee.name, employee.pay_id from transaction left join employee using(`em_id`) left join pay on(employee.`pay_id` =  pay.`name`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  and pay.`group` = 'mso' and employee.`file_status` != 'SEPARATED' group by em_id order by employee.pay_id asc, employee.em_id asc";
		}
	else{
		$select = "select em_id, name, employee.pay_id from transaction left join employee using(`em_id`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  " . $idd . " and employee.`file_status` != 'SEPARATED'  group by em_id order by employee.pay_id asc, employee.em_id asc";
		}
	}
else{
	#$idd= " and (employee.pay_id = '" . $_GET['idd'] . "' or employee.pay_id_sub = '" . $_GET['idd'] . "' ) ";
	$idd = $paysel1;
	$select = "select em_id, name, employee.pay_id from transaction left join employee using(`em_id`) where transaction.trxn_date between '" . $from_date . "' and '" . $to_date . "' and transaction.company_id = '" . $_GET['id'] . "'  " . $idd . " and employee.`file_status` != 'SEPARATED'  group by em_id order by employee.pay_id asc, employee.em_id asc";
	}

$result_all = mysql_query($select, connect());
while ($row_all = mysql_fetch_array($result_all,MYSQL_ASSOC)){
?>

<input type="hidden" name="em_id" id="em_id" value="<?php echo $row_all['em_id']; ?>">
<table width=900px border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=20%>Id Number : <b><?php echo $row_all['em_id']; ?></b></td>
</tr>
<tr>
	<td width=20%>Name : <b><?php echo $row_all['name']; ?></td>
</tr>
</table>
<table width=900px class="timeattendance">
<tr>
	<td width="30px" align="center"><?php echo getword("Day"); ?></td>
	<td align="center"><?php echo getword("Status"); ?></td>
	<td width="70px" align="center"><?php echo getword("Date"); ?></td>
	<td width="60px" align="center"><?php echo getword("Shift Code"); ?></td>
	<td width="50px" align="center"><?php echo getword("Time In"); ?></td>
	<td width="50px" align="center"><?php echo getword("Time Out"); ?></td>
	<td width="40px" align="center"><?php echo getword("Total"); ?></td>
	<td width="40px" align="center"><?php echo getword("Late"); ?></td>
	<td width="40px" align="center"><?php echo getword("UT"); ?></td>
	<td width="40px" align="center"><?php echo getword("Start OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("End OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("OTX"); ?></td>
	<td width="40px" align="center"><?php echo getword("ND"); ?></td>
	<td width="120px" align="center"><?php echo getword("REMARKS"); ?></td>
</tr>
<?php
$selectw = "select * from employee where status='active' and em_id = '" . $row_all['em_id'] . "'";
$resultw = mysql_query($selectw, connect());
$roww = mysql_fetch_array($resultw,MYSQL_ASSOC);

$select = "select * from transaction where trxn_date between '" . $from_date . "' and '" . $to_date . "' and em_id = '" . $row_all['em_id'] . "' order by trxn_date asc";
$result = mysql_query($select, connect());
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$a = split("-" , $row['trxn_date']);

	$dayoftheweek = date("D", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
	$hours = getTime($row['trxn_time_out'],1) - getTime($row['trxn_time_in'],1);
	$totz = $row['total'];
	//~ $allowed_ot = $roww['allowed_ot'];
	//~ $allowed_ut = $roww['allowed_ut'];
	//~ $allowed_late = $roww['allowed_late'];



	if($row['approved']){
		$ttotal = $ttotal + $row['total'];
		$tlate = $tlate + $row['late'];
		$tut = $tut + $row['ut'];
		}
	if($row['cbot']){
		$tot = $tot + $row['ot'];
		$totx = $totx + $row['otx'];
		$tnd = $tnd + $row['nd'];
		}

	if($row['approved'] == "" and $row['status'] != 'LWOP' and $dayoftheweek != 'Sat' and $dayoftheweek != 'Sun'){
		$bg = 'bgcolor="#EDDA74"';
		}
	else{
		$bg = "";
		}

	if ($row['posted_id']){
		?>
		<tr>
			<td><?php echo $dayoftheweek; ?></td>
			<td><?php echo $row['status']; ?> - <b><?php echo getword("POSTED"); ?></b></td>
			<td><?php echo $row['trxn_date']; ?></td>
			<td><?php echo $row['shift_code']; ?></td>
			<td><?php echo $row['trxn_time_in']; ?></td>
			<td><?php echo $row['trxn_time_out']; ?></td>
			<td><?php echo m2h($row['total']); ?></td>
			<td><?php echo m2h($row['late']); ?></td>
			<td><?php echo m2h($row['ut']); ?></td>
			<td><?php echo m2h($row['ot_in']); ?></td>
			<td><?php echo m2h($row['ot_out']); ?></td>
			<td><input type="text" name="ot<?php echo $x; ?>" value="<?php echo m2h($row['ot']); ?>"  style="width:40px;" maxlength="5"  readonly></td>
			<td><input type="text" name="otx<?php echo $x; ?>" value="<?php echo m2h($row['otx']); ?>"  style="width:40px;" maxlength="5"  readonly></td>
			<td><input type="text" name="nd<?php echo $x; ?>" value="<?php echo m2h($row['nd']); ?>"  style="width:40px;" maxlength="5"  readonly></td>
			<td><?php echo stripslashes($row['otremarks']); ?></td>

			<input type="hidden" name="cb<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="1">
			<input type="hidden" name="ot_in<?php echo $x; ?>" id="ot_in<?php echo $x; ?>" value="<?php echo cutz($row['ot_in']); ?>">
			<input type="hidden" name="ot_out<?php echo $x; ?>" id="ot_out<?php echo $x; ?>" value="<?php echo cutz($row['ot_out']); ?>">
			<input type="hidden" name="company<?php echo $x; ?>" value="<?php echo $roww['company_id']; ?>">
			<input type="hidden" name="allowed_ot<?php echo $x; ?>" value="<?php echo $roww['allowed_ot']; ?>">
			<input type="hidden" name="allowed_late<?php echo $x; ?>" value="<?php echo $roww['allowed_late']; ?>">
			<input type="hidden" name="allowed_ut<?php echo $x; ?>" value="<?php echo $roww['allowed_ut']; ?>">
		</tr>
		<?php
		}
	else{
	?>
		<tr <?php if ($row['status'] == 'LWOP') { echo 'bgcolor="#4CC552"'; }?> <?php echo $bg; ?>>
			<td><b><?php echo $dayoftheweek; ?></b></td>
			<td>
			<select name="status<?php echo $x; ?>" id="status<?php echo $x; ?>" style="width:100%;">
				<?php
				$select = "select * from `ot_rate` order by name";
				$result_data = mysql_query($select, connect());

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
						$vl = $vl + .5;
					}
					if ($vdata['status'] == 'VACATION LEAVE'){
						$vl++;
					}
				}

				while ($sdata = mysql_fetch_array($sresult,MYSQL_ASSOC)){
					if ($sdata['status'] == 'SICK LEAVE 0.5'){
						$sl = $sl + .5;
					}
					if ($sdata['status'] == 'SICK LEAVE'){
						$sl++;
					}
				}

				while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
				$sle = "";
				$vle = "";
				$vname = $data['name'];
				if ($data['name'] == "VACATION LEAVE 0.5" || $data['name'] == "VACATION LEAVE"){
					$vname = ($employee_vl - $vl) . " | " . $data['name'];

					if ($employee_vl - $vl == 0){
						$vle = "disabled";
					}
				}

				if ($data['name'] == 'SICK LEAVE 0.5' || $data['name'] == 'SICK LEAVE'){
					$vname = ($employee_sl - $sl) . " | " . $data['name'];

					if ($employee_sl - $sl == 0){
						$sle = "disabled";
					}
				}
				?>
				<option <?php if ($row['status'] == $data['name']){ echo 'selected'; } ?>
						<?php echo $vle; ?>
						<?php echo $sle; ?>
						<?php if ($row['status'] == $data['name']){ echo 'selected'; } ?>
					value="<?php echo $data['name']; ?>"><?php echo $vname; ?></option>
				<?php
				}
				?>
			</select>
			</td>
			<td><?php echo $row['trxn_date']; ?><input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['trxn_id']; ?>"></td>
			<td>
				<select name="shift_code<?php echo $x; ?>" style="width:100%;">
					<option><?php echo $row['shift_code']; ?></option>
					<?php
						$select_shift = "select `shift_code` from shift order by shift_code asc";
						$result_shift = mysql_query($select_shift, connect());
						while ($shift = mysql_fetch_array($result_shift,MYSQL_ASSOC)){
						?>
						<option><?php echo $shift['shift_code']; ?></option>
						<?php
						}
					?>
				</select>
			</td>
			<td><input type="text" name="in<?php echo $x; ?>" id="in<?php echo $x; ?>" value="<?php echo cutz($row['trxn_time_in']); ?>" style="background-color:yellow;width:50px;" maxlength="5"  onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" name="out<?php echo $x; ?>" id="out<?php echo $x; ?>" value="<?php echo cutz($row['trxn_time_out']); ?>" style="background-color:yellow;width:50px;" maxlength="5"  onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><?php echo m2h($row['total']); ?></td>
			<td><?php echo m2h($row['late']); ?></td>
			<td><?php echo m2h($row['ut']); ?></td>
			<td><input type="text" name="ot_in<?php echo $x; ?>" value="<?php echo cutz($row['ot_in']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;" onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" name="ot_out<?php echo $x; ?>" value="<?php echo cutz($row['ot_out']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;" onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" name="ot<?php echo $x; ?>" value="<?php echo m2h($row['ot']); ?>"  style="width:40px;" maxlength="5"  readonly></td>
			<td><input type="text" name="otx<?php echo $x; ?>" value="<?php echo m2h($row['otx']); ?>"  style="width:40px;" maxlength="5"  readonly></td>
			<td><input type="text" name="nd<?php echo $x; ?>" value="<?php echo m2h($row['nd']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" name="otrem<?php echo $x; ?>" value="<?php echo stripslashes($row['otremarks']); ?>" style="width:120px;"></td>


			<input type="hidden" name="cb<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="1">
			<input type="hidden" name="company<?php echo $x; ?>" value="<?php echo $roww['company_id']; ?>">
			<input type="hidden" name="allowed_ot<?php echo $x; ?>" value="<?php echo $roww['allowed_ot']; ?>">
			<input type="hidden" name="allowed_late<?php echo $x; ?>" value="<?php echo $roww['allowed_late']; ?>">
			<input type="hidden" name="allowed_ut<?php echo $x; ?>" value="<?php echo $roww['allowed_ut']; ?>">
		</tr>
	<?php
		}
	$x++;
	}
}
?>

<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
</table>
<br>
<br>
<br>
<br>
<table width=900px border="0" cellpadding="4" cellspacing="0">
<tr>
	<td align="right"><input type="submit" name="save" value="update" onclick="return checkAttendance()"> | <input type="button" value="Extract to Excel" onclick="self.location='reports/timeattendance-exl-multi.php?from=<?php echo $fx; ?>&to=<?php echo $tx; ?>&id=<?php echo $idx; ?>&idd=<?php echo $iddx; ?>';"></td>
</tr>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
</body>
</form>
<script>
var Thiswidth=screen.width;
var Thisheight=screen.height;
window.moveTo(0,0);
window.resizeTo(Thiswidth,Thisheight-30);
</script>
