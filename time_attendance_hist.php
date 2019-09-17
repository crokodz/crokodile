
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="medshtml">

<?php
if($_SESSION['level'] == 'employee'){
include 'time_employee.php';
die();
}
?>

<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>

<?php
$result = mysql_query("select *from users;", connect());

$ccct = 1;

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
	if($nd > 300){
		#$nd = $nd - $break;
		$nd = $nd;
		}
		
	if($nd > 480){
		$nd = 480;
		}
	return $nd;
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
	
function cutz($time){
	$s = split(':', $time);
	return $s[0].":".$s[1];
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
	
if (isset($_POST['upload'])){
	$result = 'ERROR';
	$result_msg = 'No FILE field found';

	if (isset($_FILES['file'])){
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK){
			$filename = $_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], "timecards/time.xls");
			$result = 'OK';
			}
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
		}
	if($result == 'OK'){
		echo "<script>";
		echo "self.location='index.php?menu=23';";
		echo "</script>";
		}
	else{
		echo "<script>";
		echo "alert('" . $result_msg . "')";
		echo "</script>";
		}
	}

if(isset($_POST['save'])){
	$ccct = 2;
	$select = "select `shift_code` from employee where em_id = '" . $_POST['em_id'] . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
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
			
		echo $nd.'<br>';
		
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
?>
<h3 class="wintitle"><?php echo getword("Time Attendance Search"); ?></h3>
<form method="post" autocomplete="off">
<?php
if($_SESSION['level'] == 'employee'){
?>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10% align="left"><?php echo getword("employee name"); ?></td>
		<td width=90% align="letf">
		<?php
		if($_SESSION['level'] == 'employee'){
		?>
		<input type="text" name="name" id="name" size=100 value="<?php echo $_SESSION['name']; ?>" readonly></td>
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_SESSION['em_id']; ?>"></td>
		<?php
		}
		else{
		?>
		<input type="text" name="name" id="name" size=50 value="<?php echo $_POST['name']; ?>"></td>
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_POST['keyword']; ?>"></td>
		<?php
		}
		?>
		
		<div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("name","hint","server.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			$('name').value=myData[1];
			$('keyword').value=myData[0];
			}
		</script>
	</tr>
	<tr>
		<td align="left"><?php echo getword("from"); ?></td>
		<td align="left"><select name="fyy" id="fyy"><option><?php echo $fyy; ?></option><?php echo $yy; ?></select><select name="fmm" id="fmm"><option><?php echo $fmm; ?><?php echo $mm; ?></select><select name="fdd" id="fdd"><option><?php echo $fdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td align="left"><?php echo getword("to"); ?></td>
		<td align="left"><select name="tyy" id="tyy"><option><?php echo $tyy; ?><?php echo $yy; ?></select><select name="tmm" id="tmm"><option><?php echo $tmm; ?><?php echo $mm; ?></select><select name="tdd" id="tdd"><option><?php echo $tdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td colspan=2 align="left"><input type="submit" name="search" value="search"></td>
	</tr>
</table>
<?php
}
else{
?>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10% align="left"><?php echo getword("employee name"); ?></td>
		<td width=90% align="letf">
		<input type="text" name="name" id="name" size=50 value="<?php echo $_POST['name']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Upload Excel" id="ups" Onclick="OpenUpload(this,20,0);"></td>
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_POST['keyword']; ?>"></td>
		<div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("name","hint","server_ta.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			$('name').value=myData[1];
			$('keyword').value=myData[0];
			}
		</script>
	</tr>
	<tr>
		<td align="left"><?php echo getword("from"); ?></td>
		<td align="left"><select name="fyy" id="fyy"><option><?php echo $fyy; ?></option><?php echo $yy; ?></select><select name="fmm" id="fmm"><option><?php echo $fmm; ?><?php echo $mm; ?></select><select name="fdd" id="fdd"><option><?php echo $fdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td align="left"><?php echo getword("to"); ?></td>
		<td align="left"><select name="tyy" id="tyy"><option><?php echo $tyy; ?><?php echo $yy; ?></select><select name="tmm" id="tmm"><option><?php echo $tmm; ?><?php echo $mm; ?></select><select name="tdd" id="tdd"><option><?php echo $tdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td colspan=2 align="left"><input type="submit" name="search" value="search"></td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>

	<tr>
		<td colspan=2 align="left">
			<select style="width:30%" name="company" id="company">
			<?php
			$result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
			<select style="width:150px;height:100px;" name="id" id="id" multiple>
			<?php
			if($_SESSION['user'] == 'mso'){
				$select = "select `name` from `pay` where `group` = 'mso' group by `name`";
				}
			else{
				$select = "select `name` from `pay` where name like '" . $_SESSION['pay_id'] . "%' group by `name`";
				}
			$result_data = mysql_query($select, connect());
			if($_SESSION['user'] == 'mso'){
				?>
				<option value="ALL MSO" selected>ALL MSO</option>
				<?php
				}
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['name']; ?>" <?php if ($_SESSION['dep'] == $data['name']){ echo 'selected'; } ?> selected><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select> | 
			<select style="width:300px" name="filter" id="filter">
				<option value="1" <?php echo gEtFil($_POST['filter'], '1'); ?>>No Filter</option>
				<option value="2" <?php echo gEtFil($_POST['filter'], '2'); ?>>Filter By Incomplete/Error Time In-Out</option>
				<option value="3" <?php echo gEtFil($_POST['filter'], '3'); ?>>Filter by Unfiled/Restday Status</option>
				<option value="4" <?php echo gEtFil($_POST['filter'], '4'); ?>>Filter By OT</option>
				<option value="5" <?php echo gEtFil($_POST['filter'], '5'); ?>>Filter By UT</option>
				<option value="6" <?php echo gEtFil($_POST['filter'], '6'); ?>>Filter By Holiday</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td colspan=2 align="left"><input type="button" name="search" value="Open By Company & PayCode" onclick="openwindow_timeattendance('time_attendance_multi.php?from='+this.form.fyy.value+'-'+this.form.fmm.value+'-'+this.form.fdd.value+'&to='+this.form.tyy.value+'-'+this.form.tmm.value+'-'+this.form.tdd.value+'&id='+this.form.company.value,400,400)"></td>
	</tr>
</table>
<?php
}
?>
<br>
<?php
$select = "select * from employee where em_id = '" . $_POST['keyword'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$allowed_ot = $row['allowed_ot'];
$allowed_ut = $row['allowed_ut'];
$allowed_late = $row['allowed_late'];

?>
<h3 class="wintitle"><?php echo getword("Employee Information"); ?></h3>
<input type="hidden" name="em_id" id="em_id" value="<?php echo $row['em_id']; ?>">

<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=20%><?php echo getword("Id Number"); ?> : <b><?php echo $row['em_id']; ?></b></td>
</tr>
<tr>
	<td width=20%><?php echo getword("Name"); ?> : <b><?php echo $row['name']; ?></td>
</tr>
</table>
<br>
<h3 class="wintitle"><?php echo getword("Employee Attendance History"); ?></h3>
<table width=100% class="timeattendance">
<tr>
	<td width="30px" align="center"><?php echo getword("Day"); ?></td>
	<td align="center"><?php echo getword("Status"); ?></td>
	<td width="70px" align="center"><?php echo getword("Date"); ?></td>
	<td width="60px" align="center"><?php echo getword("Shift Code"); ?></td>
	<td width="40px" align="center"><?php echo getword("Time In"); ?></td>
	<td width="40px" align="center"><?php echo getword("Time Out"); ?></td>
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
$from = $_POST['fyy'] . "-" . $_POST['fmm'] . "-" . $_POST['fdd'];
$to = $_POST['tyy'] . "-" . $_POST['tmm'] . "-" . $_POST['tdd'];

if($_POST['filter']==1){
	$sql = "";
	}
elseif($_POST['filter']==2){
	$sql = " and (`trxn_time_out` = '00:00:00' or `trxn_time_in` = '00:00:00' or `trxn_time_out` = `trxn_time_in`) 
		and (`status` = 'REGULAR' or `status` = 'LWOP')
		";
	}
elseif($_POST['filter']==3){
	$sql = " and (`status` = 'RESTDAY' or `status` = 'LWOP')
		";
	}	
elseif($_POST['filter']==4){
	
	}
	
$select = "select * from transaction where trxn_date between '" . $from . "' and '" . $to . "' and em_id = '" . $_POST['keyword'] . "' " . $sql . " and `posted_id` >0 order by `trxn_date` asc, `trxn_time_in` asc ";
$result = mysql_query($select, connect());
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$a = split("-" , $row['trxn_date']);
	
	$dayoftheweek = date("D", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
	$hours = getTime($row['trxn_time_out'],1) - getTime($row['trxn_time_in'],1);
	$totz = $row['total'];
	
	$ttotal = $ttotal + $row['total'];

	
	
	if($row['approved']){
		$tlate = $tlate + $row['late'];
		$tut = $tut + $row['ut'];
		}
	if($row['cbot']){
		$tot = $tot + $row['ot'];
		$totx = $totx + $row['otx'];
		$tnd = $tnd + $row['nd'];
		}
	
	if($row['approved'] == "" and $row['status'] != 'LWOP'){
		$bg = 'bgcolor="#EDDA74"';
		}
	else{
		$bg = "";
		}
	if ($row['posted_id']){
		?>
		<tr <?php if ($row['status'] == 'LWOP') { echo 'bgcolor="#4CC552"'; $bbg=""; } else{ $bbg='bgcolor="lightyellow"'; }?> <?php echo $bg; ?>>	
			<td><?php echo $dayoftheweek; ?></td>
			<td><?php echo $row['status']; ?></td>
			<td><?php echo $row['trxn_date']; ?></td>
			<td><?php echo $row['shift_code']; ?></td>
			<td <?php echo $bbg; ?>><?php echo cutz($row['trxn_time_in']); ?></td>
			<td <?php echo $bbg; ?>><?php echo cutz($row['trxn_time_out']); ?></td>
			<td><?php echo m2h($row['total']); ?></td>
			<td><?php echo m2h($row['late']); ?></td>
			<td><?php echo m2h($row['ut']); ?></td>
			<td><?php echo cutz($row['ot_in']); ?></td>
			<td><?php echo cutz($row['ot_out']); ?></td>
			<td><input type="text" name="ot<?php echo $x; ?>" value="<?php echo m2h($row['ot']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" name="otx<?php echo $x; ?>" value="<?php echo m2h($row['otx']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" name="nd<?php echo $x; ?>" value="<?php echo m2h($row['nd']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><?php echo stripslashes($row['otremarks']); ?></td>
			
			<input type="hidden" name="cb<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="1">
			<input type="hidden" name="ot_in<?php echo $x; ?>" value="<?php echo cutz($row['ot_in']); ?>">
			<input type="hidden" name="ot_out<?php echo $x; ?>" value="<?php echo cutz($row['ot_out']); ?>">
			<input type="hidden" name="company<?php echo $x; ?>" value="<?php echo $row['company_id']; ?>">
			<input type="hidden" name="allowed_ot<?php echo $x; ?>" value="<?php echo $row['allowed_ot']; ?>">
			<input type="hidden" name="allowed_late<?php echo $x; ?>" value="<?php echo $row['allowed_late']; ?>">
			<input type="hidden" name="allowed_ut<?php echo $x; ?>" value="<?php echo $row['allowed_ut']; ?>">
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
				$select = "select * from `ot_rate`";
				$result_data = mysql_query($select, connect());
				while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
				?>
				<option <?php if ($row['status'] == $data['name']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
				<?php
				}
				?>
			</select>
			</td>
			<td><?php echo $row['trxn_date']; ?><input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['trxn_id']; ?>"></td>
			<td>
				<select name="shift_code<?php echo $x; ?>" style="width:100%;"  readonly>
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
			<td><input type="text" readonly  name="in<?php echo $x; ?>" id="in<?php echo $x; ?>" value="<?php echo cutz($row['trxn_time_in']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;" onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" readonly  name="out<?php echo $x; ?>" id="out<?php echo $x; ?>" value="<?php echo cutz($row['trxn_time_out']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;" onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><?php echo m2h($row['total']); ?></td>
			<td><?php echo m2h($row['late']); ?></td>
			<td><?php echo m2h($row['ut']); ?></td>
			<td><input type="text" readonly name="ot_in<?php echo $x; ?>" id="ot_in<?php echo $x; ?>" value="<?php echo cutz($row['ot_in']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;"  onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" readonly  name="ot_out<?php echo $x; ?>" id="ot_out<?php echo $x; ?>" value="<?php echo cutz($row['ot_out']); ?>" maxlength="5" style="width:40px;background-color:yellow;border:1px solid #000;" onkeydown="javascript:return maskTime(this,event.keyCode);"></td>
			<td><input type="text" readonly  name="ot<?php echo $x; ?>" value="<?php echo m2h($row['ot']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" readonly  name="otx<?php echo $x; ?>" value="<?php echo m2h($row['otx']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" readonly  name="nd<?php echo $x; ?>" value="<?php echo m2h($row['nd']); ?>" style="width:40px;" maxlength="5" readonly></td>
			<td><input type="text" name="otrem<?php echo $x; ?>" value="<?php echo stripslashes($row['otremarks']); ?>" style="width:120px;"></td>
			
			<input type="hidden" name="cb<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="1">
			<input type="hidden" name="company<?php echo $x; ?>" value="<?php echo $row['company_id']; ?>">
			<input type="hidden" name="allowed_ot<?php echo $x; ?>" value="<?php echo $allowed_ot; ?>">
			<input type="hidden" name="allowed_late<?php echo $x; ?>" value="<?php echo $allowed_late; ?>">
			<input type="hidden" name="allowed_ut<?php echo $x; ?>" value="<?php echo $allowed_ut; ?>">
		</tr>
	<?php
		}
	$x++;
	}
?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php echo m2h($ttotal); ?></td>
	<td><?php echo m2h($tlate); ?></td>
	<td><?php echo m2h($tut); ?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php echo m2h($tot); ?></td>
	<td><?php echo m2h($totx); ?></td>
	<td><?php echo m2h($tnd); ?></td>
	<td>&nbsp;</td>
</tr>
<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
<?php
if($_SESSION['level'] != 'employee'){
?>
<tr>
	<td colspan=15 align="right"><input type="submit" name="save" id="save" value="update" onclick="return checkAttendance()"> | <input type="button" value="Extract to Excel" onclick="timekeppingexl()"></td>
</tr>
<?php
}
?>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>

</form>

<div id="getupload" class="getupload">
	<form enctype="multipart/form-data" method="post">
	<input type="file" name="file" id="file"><br><br>
	<input type="submit" name="upload" value="upload">
	</form>
</div>
<input type="hidden" id="ccct" value="<?php echo $ccct; ?>">

<script>
var ccct = document.getElementById("ccct");
if(parseInt(ccct.value)==1){
	document.getElementById("save").click();
	}
</script>
