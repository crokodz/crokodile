<?php
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
        $select = "SELECT MAX(posted_id) AS maxid FROM posted";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'] + 1;
        }

function UpdateDeduction($posted_id,$em_id){
	$select = "select * from employee_deduction where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted'
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

function UpdateAdjustments($posted_id,$em_id){
	$select = "select * from employee_adjustments where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted'
		order by id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$update = "update employee_adjustments set status = 'posted', posted_id = '" . $posted_id . "'
			where id = '" . $row['id'] . "'
			";
		mysql_query($update,connect());
		}
	}

function UpdateTaxable($posted_id,$em_id){
	$select = "select * from employee_taxable where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted'
		order by id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		if($row['status'] == 'Deminimis'){
			$copy = " insert into employee_taxable select NULL, `name`, `em_id`, `amount`, `posted_id`, `status`, `username`, `datetime`, `deleted`, `deleted_date` from employee_taxable where id = '" . $row['id'] . "' ";
			mysql_query($copy,connect());
			}

		$update = "update employee_taxable set status = 'posted', posted_id = '" . $posted_id . "'
			where id = '" . $row['id'] . "'
			";
		mysql_query($update,connect());
		}
	}

function UpdateNonTaxable($posted_id,$em_id){
	$select = "select * from employee_non_taxable where
		em_id = '" . $em_id . "' and
		status != 'deleted' and
		status != 'posted'
		order by id
		";
	$result = mysql_query($select, connect());
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		if($row['status'] == 'Deminimis'){
			$copy = " insert into employee_non_taxable select NULL, `name`, `em_id`, `amount`, `posted_id`, `status`, `username`, `datetime`, `deleted`, `deleted_date` from employee_non_taxable where id = '" . $row['id'] . "' ";
			mysql_query($copy,connect());
			}

		$update = "update employee_non_taxable set status = 'posted', posted_id = '" . $posted_id . "'
			where id = '" . $row['id'] . "'
			";
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

if (isset($_POST['confirm'])){
	$select = "select * from `users` where `username` = '" . $_POST['uname'] . "' and `password` = md5('" . $_POST['passwd'] . "') ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if($row['username'] == 'efren'){
		$delete = "delete from posted_summary where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($delete,connect());
		$update = "update transaction set `posted_id` = '' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());
		$update = "update employee_deduction set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());
		$update = "update employee_adjustments set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());
		$update = "update employee_taxable set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());
		$update = "delete from employee_taxable where posted_id = '0' and status = 'pending' and (name = 'COMPANY ALLOW.' OR name = 'LICENSE FEE') ";
		mysql_query($update,connect());

		$update = "delete from employee_non_taxable where `name` = '13th Month Pay' and posted_id = '" . $_POST['del_id'] . "' ";
		mysql_query($update,connect());

		$update = "delete from employee_non_taxable where `name` = 'Tax Refund' and posted_id = '" . $_POST['del_id'] . "' ";
		mysql_query($update,connect());

		$update = "delete from employee_non_taxable where posted_id = '" . $_POST['del_id'] . "' and `origin` = 'Deminimis' ";
		mysql_query($update,connect());

		$update = "delete from employee_non_taxable where posted_id = '" . $_POST['del_id'] . "' and `old_status` = 'Deminimis' ";
		mysql_query($update,connect());

		$update = "update employee_non_taxable set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());


		$update = "update employee_ot set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());

		$update = "update employee_adjustments set `posted_id` = '0', status = 'pending' where posted_id = '" . $_POST['del_id'] . "'";
		mysql_query($update,connect());
		}
	else{
		echo 'your not authorized to delete';
		}
        }


if (isset($_POST['post'])){
	//$from_date = $_POST['fyy'] . "-" . $_POST['fmm'] . "-" . $_POST['fdd'];
        //$to_date = $_POST['tyy'] . "-" . $_POST['tmm'] . "-" . $_POST['tdd'];
	$from_date = $_POST['fdate'];
        $to_date = $_POST['tdate'];
	$payday = $_POST['svar'] . $_POST['syy'] . "-" . $_POST['smm'];

	$id = GetID();
        $username = $_SESSION['user'];

	$insert = "INSERT INTO `posted` (
		`trxn_id` ,
		`trxn_date` ,
		`trxn_time_in` ,
		`trxn_time_out` ,
		`em_id` ,
		`salary_based` ,
		`salary` ,
		`ot` ,
		`holiday` ,
		`username` ,
		`datetime` ,
		`status`,
		`shift_code`,
		`allowed_late`,
		`allowed_ot`,
		`allowed_ut`,
		`posted_id` ,
		`posted_date` ,
		`posted_datetime` ,
		`posted_username` ,
		`from` ,
		`to`,
		`days`,
		`company_id`,
		`pay_id`,
		`start_ot`,
		`end_ot`,
		`late`,
		`ut`,
		`total`,
		`otx`,
		`nd`,
		`adjustment`,
		`adjustment_min`
		)
		SELECT
                `trxn_id` ,
		`trxn_date` ,
		`trxn_time_in` ,
		`trxn_time_out` ,
		`em_id` ,
		`salary_based` ,
		`salary` ,
		`ot` ,
		`holiday` ,
		`username` ,
		`datetime` ,
		`status`,
		`shift_code`,
		`allowed_late`,
		`allowed_ot`,
		`allowed_ut`,
		'" . $id . "' as `posted_id` ,
		curdate() as `posted_date` ,
		now() as `posted_datetime` ,
		'" . $username . "' as `posted_username` ,
		'" . $from_date . "' as `from` ,
		'" . $to_date . "' as `to`,
		'" . $_POST['days'] . "' as `days`,
		'" . $_POST['company_id'] . "' as `company_id`,
		`pay_id`,
		`start_ot`,
		`end_ot`,
		`late`,
		`ut`,
		`total`,
		`otx`,
		`nd`,
		`adjustment`,
		`adjustment_min`
                FROM transaction WHERE `trxn_date` BETWEEN '" . $from_date . "' AND '" . $to_date . "'
		and company_id = '" . $_POST['company_id'] . "'
                ";
        mysql_query($insert,connect());

	$cinfo = getcompany($_POST['company_id']);

	$select_em = "select * from transaction  where `trxn_date` BETWEEN '" . $from_date . "' AND '" . $to_date . "' and company_id = '" . $_POST['company_id'] . "' group by em_id";
	$result_em = mysql_query($select_em, connect());

	while($row_em = mysql_fetch_array($result_em,MYSQL_ASSOC)){
		$select = "select * from transaction where `trxn_date` BETWEEN '" . $from_date . "' AND '" . $to_date . "' and em_id = '" . $row_em['em_id'] . "' and company_id = '" . $_POST['company_id'] . "'";
		$result = mysql_query($select, connect());
		$tot = 0;
		$totx = 0;
		$tnd = 0;
		$ut = 0;
		$late = 0;
		$absent = 0;
		$addition = 0;
		$otmin = 0;
		$otxmin = 0;
		$ndlmin = 0;

		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			if ($row['salary_based'] == 'SEMI-MONTHLY'){
				$perday = getperday($row['salary'],$_POST['company_id'],1);
				$salary = $row['salary'] / 2;
				}
			elseif ($row['salary_based'] == 'MONTHLY'){
				$perday = getperday($row['salary'],$_POST['company_id'],1);
				$salary = $row['salary'];
				}
			elseif ($row['salary_based'] == 'DAILY'){
				if($row['status'] != 'NO WORK'){
					$perday = $row['salary'];
					$salary = $salary + $row['salary'];
					}
				}
			elseif ($row['salary_based'] == 'WEEKLY'){
				$perday = getperday($row['salary'],$_POST['company_id'],2);
				$salary = $salary + $row['salary']/7;
				}
			elseif ($row['salary_based'] == 'HOURLY'){
				if($row['status'] != 'NO WORK'){
					$perday = ($row['salary'] / 60) * $cinfo['min'];
					$salary =  $salary + $perday;
					}
				}

			if($row['status'] == 'ABSENT')
				$absent = $absent + 1;

			$perhour = $perday / 8;
			$permin = $perhour / 60;

			if ($row['total'] >= $cinfo['min']){
				$total = $cinfo['min'];
				}
			else{
				$total = $row['total'];
				}



			$paycode = GetPayCode($row['pay_id'],$row['status']);

			#regular rate
			if($row['cbot']=='checked'){
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

			$tot = $tot + $ot;
			$totx = $totx + $otx;
			$tnd = $tnd + $nd;

			$ut = $ut + $row['ut'];
			$late = $late + $row['late'];

			$otmin = $otmin + $row['ot'];
			$otxmin = $otxmin + $row['otx'];
			$ndlmin = $ndlmin + $row['nd'];


			//~ $addition = $addition + $regular_rate + $ot_rate_wo_ndl + $ot_rate_w_ndl;

			//~ $update = "update posted set adjustment = '" . $regular_rate . "' ,  adjustment_min = '" . $total . "' where trxn_id = '" . $row['trxn_id'] . "'";
			//~ mysql_query($update,connect());
			//~ $update = "update transaction set adjustment = '" . $regular_rate . "' ,  adjustment_min = '" . $total . "' where trxn_id = '" . $row['trxn_id'] . "'";
			//~ mysql_query($update,connect());
			}

		$insert = " INSERT INTO `posted_summary` (
			`id` ,
			`posted_id` ,
			`em_id` ,
			`salary_based` ,
			`salary` ,
			`from` ,
			`to` ,
			`days` ,
			`status`,
			`late`,
			`ot`,
			`ut`,
			`company_id`,
			`pay_id`,
			`nd`,
			`absent`,
			`perday_salary`,
			`payday`,
			`otmin`,
			`ndlmin`,
			`adjustment`,
			`otx`,
			`otxmin`,
			`netpay`,
			`taxable_salary`,
			`tax`
			)
			VALUES (
			NULL ,
			'" . $id . "',
			'" . $row_em['em_id'] . "',
			'" . $row_em['salary_based'] . "',
			'" . $salary . "',
			'" . $from_date . "',
			'" . $to_date . "',
			'" . $_POST['days'] . "',
			now(),
			'" . $late . "',
			'" . $tot . "',
			'" . $ut . "',
			'" . $_POST['company_id'] . "',
			'" . $row_em['pay_id'] . "',
			'" . $tnd . "',
			'" . $absent . "',
			'" . $perday . "',
			'" . $payday . "',
			'" . $otmin . "',
			'" . $ndlmin . "',
			'" . $addition . "',
			'" . $totx . "',
			'" . $otxmin . "',
			'" . $netpay . "',
			'" . $gross . "',
			'" . $tax . "'
			) ";

		mysql_query($insert,connect());

		UpdateDeduction($id,$row_em['em_id']);
		UpdateAdjustments($id,$row_em['em_id']);
		UpdateTaxable($id,$row_em['em_id']);
		UpdateNonTaxable($id,$row_em['em_id']);
		}

	$update = "update transaction set `posted_id` = '" . $id . "' where `trxn_date` between '" . $from_date . "' AND '" . $to_date . "'";
	mysql_query($update,connect());
        }

?>
<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />
<h3 class="wintitle">Payroll Posting</h3>
Note : This portion is very critical. <br>
Be sure to complete all necessary procedure before posting. <br>
This will take time, it runs all computations, from over time up to deduction.<br><br><br><br>
<form method="post">
<input type="hidden" name="del_id" id="del_id">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td align="left" width=100px>from</td>
		<td align="left" width=320px><input type="text" maxlength="10" name="fdate" id="fdate" style="width:80px;"></td>
		<td align="left" rowspan=6>
			<div id="cont"></div>
			<div id="info" style="text-align: center; margin-top: 1em;font-weight:bold;">Click to select start date</div>
		</td>
		<td align="left" width=150px rowspan=6>&nbsp;</td>
	</tr>
	<tr>
		<td align="left">to</td>
		<td align="left"><input type="text" maxlength="10" name="tdate" id="tdate" style="width:80px;"></td>
	</tr>
	<tr>
		<td align="left"># of Days</td>
		<td align="left" ><input type="text" size=2 name="days" id="days" value=12></td>
	</tr>
	<tr>
		<td align="left">pay day</td>
		<td align="left" ><select name="svar" id="svar"><option value="w1@" <?php if ($_POST['svar'] == "w1@"){ echo 'selected'; }?>>1st pay day of the month of</option><option value="w2@" <?php if ($_POST['svar'] == "w2@"){ echo 'selected'; }?>>2nd pay day of the month of</option></select><select name="smm" id="smm"><option><?php echo $smm; ?><?php echo $mm; ?></select><select name="syy" id="syy"><option><?php echo $syy; ?></option><?php echo $yy; ?></select></td>
	</tr>
	<tr>
		<td valign="bottom"><b>Company</b></td>
		<td>
			<select style="width:30%" name="company_id" id="company_id" onchange="oncmpposting()">
			<option value="ALL">ALL</option>
			<?php
			$result_data = $result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>

			<select style="width:200px;display:none;height:300px;" name="pay_id" id="pay_id" multiple>
			<?php
			$select = "select name from pay group by `name` order by name";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['name']; ?>"><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Type</td>
		<td>
			<select style="width:30%;" name="type" id="type">
				<option>REGULAR</option>
				<option>COMMISSION</option>
				<option>RESIGNED</option>
				<option>HALF 13TH MONTH</option>
				<option>WHOLE 13TH MONTH</option>
				<option>BONUS</option>
				<option>SL PAY</option>
				<option>VL PAY</option>
			</select>

		</td>
	</tr>
	<tr>
		<td colspan=2 align="left"><input type="button" name="view" id="view" value="create payroll" onclick="createpayroll('view_payroll.php?',screen.height,screen.width)" disabled> <input type="hidden" name="post" value="post"></td>
	</tr>
</table>
<br>
<h3 class="wintitle">Posted Payroll</h3>
<table width=100% border="1" cellpadding="4" cellspacing="0">
<tr class="postedtr">
	<td width="100px" align="center">Payroll #</td>
	<td align="center">Company</td>
	<td width="100px" align="center">Type</td>
	<td width="100px" align="center">From</td>
          <td width="100px" align="center">To</td>
	<td width="80px" align="center"># of Days</td>
          <td width="60px">&nbsp;</td>
</tr>
<?php
if ($_SESSION['company'] == '0'){
	$select = "SELECT * FROM posted_summary where company_id != 0 GROUP BY posted_id order by posted_id desc limit 12";
	}
else{
	$select = "SELECT * FROM posted_summary where company_id = '" . $_SESSION['company'] . "' GROUP BY posted_id order by posted_id desc limit 6";
	}
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$cinfo = getcompany($row['company_id']);
	$disabled = '';
	if($row['title'] == 'BEGINNING BALANCE'){
		$disabled = 'disabled';
		}
?>
<tr class="postedtr">
	<td><?php echo $row['posted_id']; ?></td>
	<td><?php echo $cinfo['name']; ?></td>
	<td><?php echo $row['post_type']; ?></td>
          <td><?php echo $row['from']; ?></td>
          <td><?php echo $row['to']; ?></td>
	<td><?php echo $row['days']; ?></td>
          <td><input type="button" name="delete" value="delete" onClick="DeleteIt('<?php echo $row['posted_id']; ?>')" <?php echo $disabled; ?>></td>
</tr>
<?php
}
?>
<input type="hidden" id="id" name="id">
</table>
</form>
<div class="deleteit" id="deleteit">
	<img class="imgclose" src="close_icon.gif" onclick="CloseDiv('deleteit')">
	<div id="delcontainer">
	</div>
</div>
<div class="coverit" id="coverit">
</div>
<script type="text/javascript">
var SELECTED_RANGE = null;
function getSelectionHandler() {
	var startDate = null;
	var ignoreEvent = false;
	return function(cal) {
		var selectionObject = cal.selection;
		if (ignoreEvent)
			return;
			var selectedDate = selectionObject.get();
		if (startDate == null) {
			startDate = selectedDate;
			SELECTED_RANGE = null;
			document.getElementById("info").innerHTML = "Click to select end date";
			document.getElementById("fdate").value = "";
			document.getElementById("tdate").value = "";
			document.getElementById("view").disabled = true;
			//~ cal.args.min = Calendar.intToDate(selectedDate);
			//~ cal.refresh();
			}
		else {
			ignoreEvent = true;
			selectionObject.selectRange(startDate, selectedDate);
			ignoreEvent = false;
			SELECTED_RANGE = selectionObject.sel[0];
			startDate = null;
			ranger = selectionObject.print("%Y-%m-%d") + "";
			rangerz = ranger.split(" -> ");
			document.getElementById("info").innerHTML = ranger;
			document.getElementById("fdate").value = rangerz[0];
			document.getElementById("tdate").value =  rangerz[1];
			document.getElementById("view").disabled = false;
			var payday = document.getElementById("svar");
			var smm = document.getElementById("smm");
			var syy = document.getElementById("syy");
			var svar = document.getElementById("svar");

			fdt = (rangerz[0]).split("-");
			tdt = (rangerz[1]).split("-");
			if(fdt[2] == '20' && tdt[2] == '04'){
				setSelectedIndex(smm,tdt[1]);
				setSelectedIndex(svar,'w1@');
				}
			if(fdt[2] == '05' && tdt[2] == '19'){
				setSelectedIndex(smm,tdt[1]);
				setSelectedIndex(svar,'w2@');
				}

			cal.args.min = null;
			cal.refresh();
			}
		};
	};

function setSelectedIndex(s, v) {
	for ( var i = 0; i < s.options.length; i++ ) {
		if ( s.options[i].value == v ) {
			s.options[i].selected = true;
			return;
			}
		}
	}

Calendar.setup({
	cont          : "cont",
	fdow          : 1,
	selectionType : Calendar.SEL_SINGLE,
	onSelect      : getSelectionHandler()
	});

</script>
