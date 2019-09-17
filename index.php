<?php
#echo 'Payroll system is transferring to a new server.  Will be up on June 13 7:00AM.';
#die();
include "config.php";

function getUpdate($username){
	$select = "select count(id) as total from messages where `to` = '" . $_SESSION['user'] . "' and status = 'sent'";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	if ($username){
		if ($row['total'] > 0){
			$message = getword("Welcome") .  " <b>" . $username . "</b>! You have " . $row['total'] . " message/s...  <blink><b><font color='orange'>kindly check your messages</font><b></blink>";
			}
		else{
			$message = getword("Welcome") .  " <b>" . $username . "</b>! " . getword("There are no available updates/messages on your account");
			}
		}
	else{
		$message = "Human Resources System";
		}
	return $message;
	}
	
if(isset($_POST['cleng'])){
	$_SESSION['language'] = $_POST['lenguahe'];
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Payroll System</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=win 1252">
<link rel="stylesheet" type="text/css" href="style/v31.css">
<link rel="stylesheet" type="text/css" href="windiag.css">
<script type="text/javascript">
var browser=navigator.appName;
var b_version=navigator.appVersion;
var version=parseFloat(b_version);
if((browser == 'Netscape' && version == '5') || (browser == 'Opera')){
	ayos='enter';
	}
else{
	self.location='notallowed.php';
	}
</script>
<script type="text/javascript" src="js/main.js"></script>
<script language="JavaScript" src="windiag.js"></script>
<script type="text/javascript" src="lib/prototype.js"></script>
<script type="text/javascript" src="src/scriptaculous.js"></script>
</head>
<body id="mainbody">
<div id="container">
<table id="layout" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td colspan="3">
		<table width="100%" cellpadding="0" cellspacing="0" id="header">
		<tbody>
		<!-- <tr>
			<td id="logo">&nbsp;</td>
			<td id="banner_top">&nbsp;</td>
		</tr>-->
		<?php
		if ($_SESSION['login'] == true){
		?>
		<tr>
			<td colspan="2" id="nav">
				<a href="index.php"><?php echo getword("Home"); ?></a>
				<!--<a href="index.php?menu=25"><?php echo getword("Time In/Out"); ?></a>-->
				<?php
				if (get_privileges($_SESSION['user'],12) > 0){
					echo '<a href="index.php?menu=12">' . getword('Message') . '</a>';
					}
				if (get_privileges($_SESSION['user'],13) > 0){
					echo '<a href="index.php?menu=13">' . getword('Alert') . '</a>';
					}
				if (get_privileges($_SESSION['user'],15) > 0){
					echo '<a href="index.php?menu=15">' . getword('Job Order') . '</a>';
					}
				if (get_privileges($_SESSION['user'],14) > 0){
					echo '<a href="index.php?menu=14">' . getword('Change Password') . '</a>';
					}
				if (get_privileges($_SESSION['user'],14) > 0){
					echo '<a href="javascript:void(0);"  onclick="langs(0,20);" id="lingual" onblur="langsh();">' . getword("Language") . '</a>';
					}
				if (get_privileges($_SESSION['user'],11) > 0){
					echo '<a href="index.php?menu=11">' . getword('Logout') . '</a>';
					}
				if ($_SESSION['level'] == 'employee'){
					echo '<a href="index.php?menu=11">' . getword('Logout') . '</a>';
					}
				?>
			</td>
		</tr>
		<?php
		}
		else{
		?>
		<tr>
			<td colspan="2" id="nav">
				<a href="index.php"><?php echo getword("Home"); ?></a>
				<!-- <a href="index.php?menu=25"><?php echo getword("Time In/Out"); ?></a> -->
				<a href="index.php?menu=10"><?php echo getword("Login"); ?></a>
			</td>
		</tr>
		<?php
		}
		?>
		</tbody>
		</table>
	</td>
</tr>
<tr>
	<td colspan="3">
		<div id="banner_big"></div>
	</td>
</tr>
<tr>
	<td id="left">
		<div id="content_left">
			<div class="window">
				<h3 class="wintitle"><?php echo getword("User Menu"); ?></h3>
				<div class="winbody">
					<ul class="bulleted">
					<?php
					if (get_privileges($_SESSION['user'],1) > 0){
						echo '<li><a href="index.php?menu=1">'.getword("Employee").'</a></li>';
						}
					if (get_privileges($_SESSION['user'],'em_inactive') > 0){
						echo '<li><a href="index.php?menu=em_inactive">'.getword("Employee (Inactive)").'</a></li>';
						}
					if (get_privileges($_SESSION['user'],'schedule_bday') > 0){
						echo '<li><a href="index.php?menu=schedule_bday">' . getword('BirthDay Calendar') . '</a></li>';
						}
					if ($_SESSION['level'] == 'employee'){
						echo '<li><a href="index.php?menu=myinfo">' . getword('My Information') . '</a></li>';
						echo '<li><a href="index.php?menu=2">' . getword('Time Attendance') . '</a></li>';
						echo '<li><a href="index.php?menu=leaveinfo">' . getword('Leave Info') . '</a></li>';
						}
					?>
					 <li><a href="index.php?menu=17">Contact Us</a><li>
					</ul>
				</div>
			</div>
		</div>
	
		<?php
		if($_GET['id']){
		?>
		<div id="content_left">
			<div class="window">
				<h3 class="wintitle"><?php echo getword("Persona"); ?></h3>
				<div class="winbody">
					<ul class="bulleted">
					<?php
					if (get_privileges($_SESSION['user'],'1DE') > 0){
						echo '<li><a href="index.php?menu=1DE&id=' . $_GET['id'] . '">' . getword('Personal') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eskills') > 0){
						echo '<li><a href="index.php?menu=eskills&id=' . $_GET['id'] . '">' . getword('Skills') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eleave') > 0){
						echo '<li><a href="index.php?menu=eleave&id=' . $_GET['id'] . '">' . getword('Leave') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'emerits') > 0){
						echo '<li><a href="index.php?menu=emerits&id=' . $_GET['id'] . '">' . getword('Merits') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'ejobs') > 0){
						echo '<li><a href="index.php?menu=ejobs&id=' . $_GET['id'] . '">' . getword('Jobs') . '</a></li>';
						}	
					if (get_privileges($_SESSION['user'],'eeducation') > 0){
						echo '<li><a href="index.php?menu=eeducation&id=' . $_GET['id'] . '">' . getword('Education') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'ecertificate') > 0){
						echo '<li><a href="index.php?menu=ecertificate&id=' . $_GET['id'] . '">' . getword('Certificate') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'elanguage') > 0){
						echo '<li><a href="index.php?menu=elanguage&id=' . $_GET['id'] . '">' . getword('Language') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'emembership') > 0){
						echo '<li><a href="index.php?menu=emembership&id=' . $_GET['id'] . '">' . getword('Membership') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'elicense') > 0){
						echo '<li><a href="index.php?menu=elicense&id=' . $_GET['id'] . '">' . getword('License') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'etrainings') > 0){
						echo '<li><a href="index.php?menu=etrainings&id=' . $_GET['id'] . '">' . getword('Trainings') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eviolations') > 0){
						echo '<li><a href="index.php?menu=eviolations&id=' . $_GET['id'] . '">' . getword('Violations/Lates/Abs.') . '</a></li>';
						}
					?>
					</ul>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		
		<div id="content_left">
			<div class="window">
				<h3 class="wintitle"><?php echo getword("Timekeeping"); ?></h3>
				<div class="winbody">
					<ul class="bulleted">
					<?php
					if (get_privileges($_SESSION['user'],2) > 0){
						echo '<li><a href="index.php?menu=2">' . getword('Time Attendance') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'us') > 0){
						echo '<li><a href="index.php?menu=us">' . getword('Update Status') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'schedule') > 0){
						echo '<li><a href="index.php?menu=schedule">' . getword('Advanced Schedule') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'leaveapproval') > 0){
						echo '<li><a href="index.php?menu=leaveapproval">' . getword('Leave (For Approval)') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],26) > 0){
						echo '<li><a href="index.php?menu=26">' . getword('Holiday Entry') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],50) > 0){
						echo '<li><a href="index.php?menu=50">' . getword('Late/UT Summary') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],51) > 0){
						echo '<li><a href="index.php?menu=51">' . getword('Absences Summary') . '</a></li>';
						}
					?>
					</ul>
				</div>
			</div>
		</div>
		
		<div id="content_left">
			<div class="window">
				<h3 class="wintitle"><?php echo getword("Payroll"); ?></h3>
				<div class="winbody">
					<ul class="bulleted">
					<?php
					if (get_privileges($_SESSION['user'],'8DE') > 0){
						echo '<li><a href="index.php?menu=8DE&id=' . $_GET['id'] . '">' . getword('Deductions') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eotaxable') > 0){
						echo '<li><a href="index.php?menu=eotaxable&id=' . $_GET['id'] . '">' . getword('Taxable Income') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eontaxable') > 0){
						echo '<li><a href="index.php?menu=eontaxable&id=' . $_GET['id'] . '">' . getword('Non-Taxable Income') . '</a>';
						}
					if (get_privileges($_SESSION['user'],'holidaypay') > 0){
						echo '<li><a href="index.php?menu=holidaypay&id=' . $_GET['id'] . '">' . getword('Holiday Pay') . '</a>';
						}
					if (get_privileges($_SESSION['user'],'otx') > 0){
						echo '<li><a href="index.php?menu=otx&id=' . $_GET['id'] . '">' . getword('Unpaid Over Time') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'utrxn') > 0){
						echo '<li><a href="index.php?menu=utrxn&id=' . $_GET['utrxn'] . '">' . getword('Update Transaction') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],5) > 0){
						echo '<li><a href="index.php?menu=5">' . getword('Payroll Posting') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],6) > 0){
						echo '<li><a href="index.php?menu=6">' . getword('Pay Slip') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],7) > 0){
						echo '<li><a href="index.php?menu=7">' . getword('Reports') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'reports_spp') > 0){
						echo '<li><a href="index.php?menu=reports_spp">' . getword('Reports') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'schedule_update') > 0){
						echo '<li><a href="index.php?menu=schedule_update">' . getword('Cron Job') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'eadjustments') > 0){
						echo '<li><a href="index.php?menu=eadjustments&id=' . $_GET['id'] . '">' . getword('Payroll Adjustments') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],'mod') > 0){
						echo '<li><a href="index.php?menu=mod&id=' . $_GET['id'] . '">' . getword('Monitoring of Dep..') . '</a></li>';
						}					
					
					?>
					</ul>
				</div>
			</div>
		</div>
		
		<div id="content_left">
			<div class="window">
				<h3 class="wintitle"><?php echo getword("Admin Menu"); ?></h3>
				<div class="winbody">
					<ul class="bulleted">
					<?php
					if (get_privileges($_SESSION['user'],9) > 0){
						echo '<li><a href="index.php?menu=9">' . getword('General') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],16) > 0){
						echo '<li><a href="index.php?menu=16">' . getword('Users') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],4) > 0){
						echo '<li><a href="index.php?menu=4">' . getword('Privileges') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],22) > 0){
						echo '<li><a href="index.php?menu=22">' . getword('Leave Approval') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],23) > 0){
						echo '<li><a href="index.php?menu=23">' . getword('Upload Time Keeping') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],24) > 0){
						echo '<li><a href="index.php?menu=24">' . getword('TK Upload List') . '</a></li>';
						}
					
					if (get_privileges($_SESSION['user'],27) > 0){
						echo '<li><a href="index.php?menu=27">' . getword('Export Employee Info') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],28) > 0){
						echo '<li><a href="index.php?menu=28">' . getword('Time Attendance Refill') . '</a></li>';
						}
					if (get_privileges($_SESSION['user'],30) > 0){
						echo '<li><a href="index.php?menu=30">' . getword('Upload Documents') . '</a></li>';
						}
					?>
					<li><a href="index.php?menu=18"><?php echo getword("Help"); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
	</td>
	
	<td id="main">
		<div id="content_main">
			<div class="normalbox">
				<?php echo getUpdate($_SESSION['user']); ?>
			</div>
			<div id="announcement">
				<?php
				if($_GET['menu'] == "1"){
					include "employeeLI.php";
					}
				elseif($_GET['menu'] == "1DE"){
					include "employeeDE.php";
					}
				elseif($_GET['menu'] == "1DA"){
					include "employeeDA.php";
					}
				elseif($_GET['menu'] == "eskills"){
					include "employee_skills.php";
					}
				elseif($_GET['menu'] == "ejobs"){
					include "employee_jobs.php";
					}
				elseif($_GET['menu'] == "eviolations"){
					include "employee_violations.php";
					}
				elseif($_GET['menu'] == "emerits"){
					include "employee_merits.php";
					}
				elseif($_GET['menu'] == "elicense"){
					include "employee_license.php";
					}
				elseif($_GET['menu'] == "elanguage"){
					include "employee_language.php";
					}
				elseif($_GET['menu'] == "emembership"){
					include "employee_membership.php";
					}
				elseif($_GET['menu'] == "ecertificate"){
					include "employee_certificate.php";
					}
				elseif($_GET['menu'] == "eeducation"){
					include "employee_education.php";
					}
				elseif($_GET['menu'] == "elates"){
					include "employee_lates.php";
					}
				elseif($_GET['menu'] == "eabsences"){
					include "employee_absences.php";
					}
				elseif($_GET['menu'] == "eleave"){
					include "employee_leave.php";
					}
				elseif($_GET['menu'] == "etrainings"){
					include "employee_trainings.php";
					}
				elseif($_GET['menu'] == "2"){
					include "time_attendance.php";
					}
				elseif($_GET['menu'] == "5"){
					include "payroll_posting.php";
					}
				elseif($_GET['menu'] == "6"){
					include "pay_slip.php";
					}
				elseif($_GET['menu'] == "7"){
					include "reports.php";
					}
				elseif($_GET['menu'] == "reports_spp"){
					include "reports_spp.php";
					}
				elseif($_GET['menu'] == "8DE"){
					include "deductionDE.php";
					}
				elseif($_GET['menu'] == "9"){
					include "persona.php";
					}
				elseif($_GET['menu'] == "11"){
					include "logout.php";
					}
				elseif($_GET['menu'] == "12"){
					include "messages.php";
					}
				elseif($_GET['menu'] == "13"){
					include "alert.php";
					}
				elseif($_GET['menu'] == "14"){
					include "chpassword.php";
					}
				elseif($_GET['menu'] == "15"){
					include "joborder.php";
					}
				elseif($_GET['menu'] == "16"){
					include "users.php";
					}
				elseif($_GET['menu'] == "17"){
					include "contact.php";
					}
				elseif($_GET['menu'] == "10"){
					include "login.php";
					}
				elseif($_GET['menu'] == "18"){
					include "help.php";
					}
				elseif($_GET['menu'] == "19"){
					include "error.php";
					}
				elseif($_GET['menu'] == "20"){
					include "messages_reply.php";
					}
				elseif($_GET['menu'] == "21"){
					include "pay_slip_generator.php";
					}
				elseif($_GET['menu'] == "22"){
					include "employee_leave_approval.php";
					}
				elseif($_GET['menu'] == "23"){
					include "uploadFile.php";
					}
				elseif($_GET['menu'] == "24"){
					include "tkupload_list.php";
					}	
				elseif($_GET['menu'] == "4"){
					include "privileges.php";
					}
				elseif($_GET['menu'] == "3"){
					include "reports_window.php";
					}
				elseif($_GET['menu'] == "eadjustments"){
					include "employee_adjustment.php";
					}
				elseif($_GET['menu'] == "25"){
					include "timekeeping.php";
					}
				elseif($_GET['menu'] == "26"){
					include "holiday_entry.php";
					}
				elseif($_GET['menu'] == "eotaxable"){
					include "employee_taxable.php";
					}
				elseif($_GET['menu'] == "eontaxable"){
					include "employee_non_taxable.php";
					}
				elseif($_GET['menu'] == "27"){
					include "export_employee.php";
					}
				elseif($_GET['menu'] == "28"){
					include "complete.php";
					}
				elseif($_GET['menu'] == "29"){
					include "warning.php";
					}
				elseif($_GET['menu'] == "30"){
					include "upload.php";
					}
				elseif($_GET['menu'] == "schedule"){
					include "schedule.php";
					}
				elseif($_GET['menu'] == "leaveapproval"){
					include "leaveapproval.php";
					}
				elseif($_GET['menu'] == "schedule_update"){
					include "schedule_update.php";
					}
				elseif($_GET['menu'] == "schedule_bday"){
					include "schedule_bday.php";
					}
				elseif($_GET['menu'] == "leaveinfo" and $_SESSION['level'] == 'employee'){
					include "leaveinfo.php";
					}
				elseif($_GET['menu'] == "2" and $_SESSION['level'] == 'employee'){
					include "time_attendance.php";
					}
				elseif($_GET['menu'] == "myinfo" and $_SESSION['level'] == 'employee'){
					include "myinfo.php";
					}
				elseif($_GET['menu'] == "50"){
					include "late_summary.php";
					}
				elseif($_GET['menu'] == "51"){
					include "absences_summary.php";
					}
				elseif($_GET['menu'] == "em_inactive"){
					include "employeeLI_inactive.php";
					}
				elseif($_GET['menu'] == "otx"){
					include "otx.php";
					}
				elseif($_GET['menu'] == "utrxn"){
					include "utrxn.php";
					}
				elseif($_GET['menu'] == "mod"){
					include "employee_mod.php";
					}
				elseif($_GET['menu'] == "holidaypay"){
					include "employee_additional.php";
					}
				elseif($_GET['menu'] == "us"){
					include "update_status.php";
					}
				elseif($_GET['menu'] == "50det"){
					include "late_summary_det.php";
					}
				elseif($_GET['menu'] == "51det"){
					include "absences_summary_det.php";
					}
				else{
					include "home.php";
					}
				?>
			</div> 
			<br>
		</div>
	</td>
	<td id="right">&nbsp;</td>
</tr>
</tbody>
</table>
<form method="POST">
<div id="language">
<table>
	<tr style="cursor: pointer;cursor: hand;">
		<td onClick="document.getElementById('lenguahe').value='english';document.getElementById('cleng').click();">English</td>
	</tr>
	<tr style="cursor: pointer;cursor: hand;">
		<td onClick="document.getElementById('lenguahe').value='china';document.getElementById('cleng').click();">Chinese Traditional</td>
	</tr>
	<tr style="cursor: pointer;cursor: hand;">
		<td onClick="document.getElementById('lenguahe').value='china';document.getElementById('cleng').click();">Chinese Simplified</td>
	</tr>
	<input type="hidden" name="lenguahe" id="lenguahe">
	<input type="submit" name="cleng" id="cleng" style="display:none;document.getElementById('cleng').click();">
</table>
</div>
</form>
</body>
</html>
