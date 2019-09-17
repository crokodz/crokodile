<?php

function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$difference = $end_ts - $start_ts;
	return round($difference / 86400);
	}

function dDays($date,$days){
	$newdate = strtotime ( $days.' day' , strtotime ( $date ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );
	return $newdate;
	}

function get_date($id,$date){
	$date = explode("-", $date);
	if ($id == 0){
		return $date[0];
		}
	if ($id == 1){
		return $date[1];
		}
	if ($id == 2){
		return $date[2];
		}
	}

function getcompany($id){
	$select = "select name from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}

function getpaycode($id){
	$select = "select name from pay where name = '" . $id . "' group by `name`";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}

function get_id(){
	$select = "select max(id) as maxid from employee_certificate";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'] + 1;
	}

function get_branch($id){
	$select = "select * from branch where id = '" . $id . "'";
	$result_branch = mysql_query($select, connect());
	$row = mysql_fetch_array($result_branch,MYSQL_ASSOC);
	return $row['branch'];
	}

if(isset($_POST['svsal'])){
	$insert = "INSERT INTO `employee_salary` (
		`id` ,
		`em_id` ,
		`old_salary` ,
		`new_salary` ,
		`type` ,
		`remarks` ,
		`username` ,
		`date`
		)
		VALUES (
		NULL ,
		'" . $_POST['id_number'] . "',
		'" . $_POST['oldsalary'] . "',
		'" . $_POST['newsalary'] . "',
		'" . $_POST['saltype'] . "',
		'" . $_POST['salrem'] . "',
		'" . $_SESSION['user'] . "',
		curdate()
		);";
	mysql_query($insert, connect());

	$update = "update employee set `salary` = '" . $_POST['newsalary'] . "' where `em_id` = '" . $_POST['id_number'] . "'";
	mysql_query($update, connect());
	}

if(isset($_POST['update_trxn_pc'])){
	$update = "update `transaction` set `pay_id` = '" . $_POST['pc'] . "', `company_id` = '" . $_POST['company_id'] . "'
		WHERE `em_id` = '" . $_GET['id'] . "'  and `trxn_date` between '" . $_POST['date1_pc'] . "' and '" . $_POST['date2_pc'] . "' ";
	mysql_query($update, connect());
	echo "<script>";
	echo "window.location='index.php?menu=1DE&id=" . $_POST['em_id'] . "';";
	echo "</script>";
	}

if(isset($_POST['update_trxn'])){
	$count = dateDiff($_POST['date1'], $_POST['date2']);
	for($x=0;$x<=$count;$x++){
		$date = dDays($_POST['date1'],$x);
		$select = "select trxn_id from transaction where trxn_date = '" . $date . "' and em_id = '" . $_GET['id'] . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);

		if(!$row['trxn_id']){
			$select = "select * from employee where em_id = '" . $_GET['id'] . "'";
			$result = mysql_query($select, connect());
			$row = mysql_fetch_array($result,MYSQL_ASSOC);

			$insert = "INSERT INTO `transaction` (
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
				`status` ,
				`shift_code` ,
				`allowed_late` ,
				`allowed_ot` ,
				`allowed_ut` ,
				`posted_id` ,
				`company_id` ,
				`pay_id` ,
				`start_ot` ,
				`end_ot`
				)
				VALUES (
				NULL ,
				'" . $date . "',
				'00:00',
				'00:00',
				'" . $row['em_id'] . "',
				'" . $row['salary_based'] . "',
				'" . $row['salary'] . "',
				'0',
				'',
				'',
				NOW( ),
				'LWOP',
				'" . $row['shift_code'] . "',
				'" . $row['allowed_late'] . "',
				'" . $row['allowed_ot'] . "',
				'" . $row['allowed_ut'] . "',
				'0',
				'" . $row['company_id'] . "',
				'" . $row['pay_id'] . "',
				'00:00:00',
				'00:00:00'
				)";
			mysql_query($insert, connect());
			}
		}

	$update = "update `transaction` set `salary_based` = '" . $_POST['salary_based'] . "',
		`salary` = '" . $_POST['salary'] . "'
		WHERE `em_id` = '" . $_GET['id'] . "'  and `trxn_date` between '" . $_POST['date1'] . "' and '" . $_POST['date2'] . "' ";
	mysql_query($update, connect());
	echo "<script>";
	echo "window.location='index.php?menu=1DE&id=" . $_POST['em_id'] . "';";
	echo "</script>";
	}

if (isset($_POST['update'])){
	$birth_date = $_POST['bdyy'] . "-" . $_POST['bdmm'] . "-" . $_POST['bddd'];
	$employed_date = $_POST['debdyy'] . "-" . $_POST['debdmm'] . "-" . $_POST['debddd'];
	$leaving_date = $_POST['dlbdyy'] . "-" . $_POST['dlbdmm'] . "-" . $_POST['dlbddd'];
	$permanent_date = $_POST['dpbdyy'] . "-" . $_POST['dpbdmm'] . "-" . $_POST['dpbddd'];
	$name = $_POST['fname'] . " " . $_POST['mname'] . " " . $_POST['lname'];

	$update = "update `employee` set
		`name` = '" . $name . "',
		`em_address` = '" . sTr($_POST['em_address']) . "',
		`em_number` ='" . $_POST['em_number'] . "',
		`birthdate` = '" . $birth_date . "',
		`gender` = '" . $_POST['gender'] . "',
		`civil_status` = '" . $_POST['civil'] . "',
		`citizenship` = '" . $_POST['citizenship'] . "',
		`contact_person` = '" . $_POST['contact'] . "',
		`cp_number` = '" . $_POST['cp_number'] . "',
		`cp_address` = '" . sTr($_POST['cp_address']) . "',
		`position` = '" . $_POST['position'] . "',
		`salary` = '" . $_POST['salary'] . "',
		`salary_based` = '" . $_POST['salary_based'] . "',
		`department` = '" . $_POST['department'] . "',
		`description` = '" . sTr($_POST['description']) . "',
		`branch` = '" . $_POST['branch'] . "',
		`race` = '" . $_POST['race'] . "',
		`username` = '',
		`datetime` = now( ),
		`sss` = '" . $_POST['sss'] . "',
		`pi` = '" . $_POST['pi'] . "',
		`ph` = '" . $_POST['ph'] . "',
		`tin` = '" . $_POST['tin'] . "',
		`sssn` = '" . $_POST['sssn'] . "',
		`pin` = '" . $_POST['pin'] . "',
		`phn` = '" . $_POST['phn'] . "',
		`tinn` = '" . $_POST['tinn'] . "',
		`ts` = '" . $_POST['ts'] . "',
		`date_employed` = '" . $employed_date . "',
		`date_permanent` = '" . $permanent_date . "',
		`reason_living` = '" . $_POST['reason_living'] . "',
		`reason_living_date` = '" . $leaving_date . "',
		`allowed_ot` = '" . $_POST['allowed_ot'] . "',
		`allowed_late` = '" . $_POST['allowed_late'] . "',
		`allowed_ut` = '" . $_POST['allowed_ut'] . "',
		`employee_status` = '" . $_POST['employee_status'] . "',
		`height` = '" . $_POST['height'] . "',
		`weight` = '" . $_POST['weight'] . "',
		`shift_code` = '" . $_POST['shift_code'] . "',
		`company_id` = '" . $_POST['company_id'] . "',
		`pay_id` = '" . $_POST['pc'] . "',
		`pay_id_sub` = '" . $_POST['spc'] . "',
		`pin_number` = '" . $_POST['pin_number'] . "',
		`em_id` = '" . $_POST['em_id'] . "',
		`finger` = '" . $_POST['finger'] . "',
		`manager` = '" . $_POST['keyword'] . "',
		`login_id` = '" . $_POST['login_id'] . "',
		`icn` = '" . $_POST['icn'] . "',
		`ename` = '" . $_POST['ename'] . "',
		`zipcode` = '" . $_POST['zipcode'] . "',
		`email` = '" . $_POST['email'] . "',
		`bank_account` = '" . $_POST['bankaccount'] . "',
		`file_status` = '" . $_POST['file_status'] . "',
		`division` = '" . $_POST['division'] . "',
		`fname` = '" . $_POST['fname'] . "',
		`lname` = '" . $_POST['lname'] . "',
		`mname` = '" . $_POST['mname'] . "',
		`wtax` = '" . $_POST['wtax'] . "',
		`pdm` = '" . $_POST['pdm'] . "',
		`blood_type` = '" . $_POST['blood_type'] . "',
		`half_13th` = '" . $_POST['half_13th'] . "',
		`last_13th` = '" . $_POST['last_13th'] . "',
		`bonus` = '" . $_POST['bonus'] . "',
		`non_tax_13th` = '" . $_POST['non_tax_13th'] . "',
		`non_tax_bonus` = '" . $_POST['non_tax_bonus'] . "',
		`vl` = '" . $_POST['vl'] . "',
		`sl` = '" . $_POST['sl'] . "'
		WHERE `em_id` = '" . $_GET['id'] . "' LIMIT 1 ;";

	mysql_query($update, connect());
	echo "<script>";
	echo "window.location='index.php?menu=1DE&id=" . $_POST['em_id'] . "';";
	echo "</script>";
	}

// upload photo
$upload_dir = "pix";

$tf = $upload_dir.'/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false)
	die("Fatal error! {$upload_dir} is not writable. Set 'chmod 777 {$upload_dir}'or something like this");
fclose($f);
unlink($tf);


if (isset($_POST['fileframe'])){
	$result = 'ERROR';
	$result_msg = 'No FILE field found';

	if (isset($_FILES['file'])){
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK){
			$filename = $_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.'/'.$_POST['file_name'] . ".png");
			$result = 'OK';
			}
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else
			$result_msg = 'Unknown error';
		}
	$random = (rand()%20);
	echo '<html><head><title>-</title></head><body>';
	echo '<script language="JavaScript" type="text/javascript">'."\n";
	echo 'var parDoc = window.parent.document;';

	echo 'parDoc.getElementById("pix").src = "imgs.php?path=pix/'.$_POST['file_name'] . '.png&w=200&h=160&' . $random . '";';

	echo "\n".'</script></body></html>';
	exit();
	}

if (isset($_POST['ldelete'])){
	$delete = "delete from employee_letter where `id` = '" . $_POST['lid'] . "'";
	mysql_query($delete, connect());
	}

if (isset($_POST['cdelete'])){
	$delete = "delete from employee_card where `id` = '" . $_POST['cid'] . "'";
	mysql_query($delete, connect());
	}

if (isset($_POST['upload'])){
	if ($_POST['dir'] == 'card'){
		$select = "select max(`id`) as `ids` from `employee_card`";
		$result_branch = mysql_query($select, connect());
		$row = mysql_fetch_array($result_branch,MYSQL_ASSOC);
		$id = $row['ids'] + 1;

		if (isset($_FILES['ufile'])){
			if ($_FILES['ufile']['error'] == UPLOAD_ERR_OK){
				$filename = $_FILES['ufile']['name'];
				$ext = substr($filename, strrpos($filename, ".") + 1);
				$path = $_POST['dir'].'/'.$id . "." . $ext;
				move_uploaded_file($_FILES['ufile']['tmp_name'], $path);

				$insert = "
					INSERT INTO `employee_card` (
					`id` ,
					`em_id` ,
					`name` ,
					`description`,
					`datetime`,
					`filename`
					)
					VALUES (
					'" . $id . "' ,
					'" . $_POST['id_number'] . "',
					'" . $_SESSION['user'] . "',
					'" . $_POST['udescription'] . "',
					NOW(),
					'" . $filename . "'
					)
					";
				mysql_query($insert, connect());
				}
			}
		}
	if ($_POST['dir'] == 'letter'){
		$select = "select max(`id`) as `ids` from `employee_letter`";
		$result_branch = mysql_query($select, connect());
		$row = mysql_fetch_array($result_branch,MYSQL_ASSOC);
		$id = $row['ids'] + 1;

		if (isset($_FILES['ufile'])){
			if ($_FILES['ufile']['error'] == UPLOAD_ERR_OK){
				$filename = $_FILES['ufile']['name'];
				$ext = substr($filename, strrpos($filename, ".") + 1);
				$path = $_POST['dir'].'/'.$id . "." . $ext;
				move_uploaded_file($_FILES['ufile']['tmp_name'], $path);

				$insert = "
					INSERT INTO `employee_letter` (
					`id` ,
					`em_id` ,
					`name` ,
					`description`,
					`datetime`,
					`filename`
					)
					VALUES (
					'" . $id . "' ,
					'" . $_POST['id_number'] . "',
					'" . $_SESSION['user'] . "',
					'" . $_POST['udescription'] . "',
					NOW(),
					'" . $filename . "'
					)
					";
				mysql_query($insert, connect());
				}
			}
		}
	}

$dd = '';
for ($x=1; $x < 32; $x++){
	$dd = $dd . "<option>" . $x . "</option>";
	}

$yy = '';
for ($x=1950; $x <= date('Y'); $x++){
	$yy = $yy . "<option>" . $x . "</option>";
	}

$mm = '';
for ($x=1; $x < 13; $x++){
	$mm = $mm . "<option>" . $x . "</option>";
	}

$gender = "<option>M</option><option>F</option>";
$civil = "<option>SINGLE</option><option>MARRIED</option><option>WIDOWED</option><option>COMPLICATED</option>";

$select = "select * from employee where em_id = '" . $_GET['id'] . "';";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

#next
$select = "select * from employee where em_id > '" . $row['em_id'] . "' and pay_id like '" . $_SESSION['dep'] . "' and company_id = '" . $row['company_id'] . "' and status != 'deleted' order by em_id asc limit 1";
$nresult = mysql_query($select, connect());
$nrow = mysql_fetch_array($nresult,MYSQL_ASSOC);
if($nrow['em_id']){
	$nlink = "<a href='index.php?menu=1DE&id=" . $nrow['em_id'] . "'>next employee</a>";
	}
else{
	$nlink = "end";
	}

#priv
$select = "select * from employee where  em_id < '" . $row['em_id'] . "' and pay_id like '" . $_SESSION['dep'] . "'  and company_id = '" . $row['company_id'] . "' and status != 'deleted' order by em_id desc limit 1";
$presult = mysql_query($select, connect());
$prow = mysql_fetch_array($presult,MYSQL_ASSOC);
if($prow['em_id']){
	$plink = "<a href='index.php?menu=1DE&id=" . $prow['em_id'] . "'>prev. employee</a>";
	}
else{
	$plink = "start";
	}


$tf = "pix/" . $_GET['id'] . ".png";
$f = @fopen($tf, "r");
if ($f == false){
	$pic = "pix/no_photo.jpg";
	$pic = "imgs.php?path=" . $pic."&w=200&h=160";
	}
else{
	$pic = "imgs.php?path=" . $tf."&w=200&h=160";
	}
?>
<form method="post" enctype="multipart/form-data">
<h3 class="wintitle"><b><?php echo getword("Employee Data Entry"); ?></b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $plink; ?> &nbsp;|| &nbsp;<?php echo $nlink; ?></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td  width=18% align="right"><?php echo getword("First Name"); ?></td>
		<td><input type="text" name="fname" id="fname" value="<?php echo $row['fname']; ?>" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
		<td width="220px" rowspan=7 align="center">
		<form target="upload_iframe" method="post" enctype="multipart/form-data">
		<img src="pix/spacer.gif" height="160px" width="1px;"><img name="pix" id="pix" src="<?php echo $pic; ?>" style="margin-bottom:5px;">
		<input type="hidden" name="fileframe" value="true"><input type="hidden" name="file_name" id="file_name" value="<?php echo $_GET['id']; ?>"><input type="file" name="file" id="file" onChange="jsUpload(this)"><iframe name="upload_iframe" style="width: 10px; height: 100px; display: none;"></iframe>
		</form>
		</td>
	</tr>
	<tr>
		<td  width=18% align="right"><?php echo getword("Middle Name"); ?></td>
		<td><input type="text" name="mname" id="mname" value="<?php echo $row['mname']; ?>" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
		</td>
	</tr>
	<tr>
		<td  width=18% align="right"><?php echo getword("Last Name"); ?></td>
		<td><input type="text" name="lname" id="lname" value="<?php echo $row['lname']; ?>" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
		</td>
	</tr>
<input type="hidden" name="old_id" value="<?php echo $row['em_id']; ?>">
<input type="hidden" name="id">
	<tr>
		<td align="right"><?php echo getword("Staff Code"); ?></td>
		<td><input type="text" readonly="true" name="em_id" id="em_id" value="<?php echo $row['em_id']; ?>" style="width:20%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Login ID"); ?></td>
		<td><input type="text" name="login_id" id="login_id" value="<?php echo $row['login_id']; ?>" style="width:10%;" onKeyup="this.value=this.value.toUpperCase()"> | <?php echo getword("Pin Number"); ?> <input type="text" name="pin_number" id="pin_number" style="width:10%;" value="<?php echo $row['pin_number']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
                <td align="right"><?php echo getword("Position"); ?></td>
                <td>
                        <select style="width:80%" name="position" id="position">
                        <option><?php echo $row['position']; ?></option>
                        <?php
                        $select = "select * from positions";
                        $result_data = mysql_query($select, connect());
                        while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
                        ?>
                        <option><?php echo $data['name']; ?></option>
                        <?php
                        }
                        ?>
                        </select>
                </td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Department"); ?></td>
		<td>
			<select style="width:50%" name="department" id="department">
			<option><?php echo $row['department']; ?></option>
			<?php
			$select = "select * from departments";
			$result_data = mysql_query($select, connect());
                        while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
                        ?>
			<option><?php echo $data['name']; ?></option>
			<?php
                        }
                        ?>
			</select>
		</td>
	</tr>
	<tr>
                <td align="right"><?php echo getword("Division"); ?></td>
                <td>
                        <select style="width:50%" name="division" id="division">
                        <option><?php echo $row['division']; ?></option>
                        <?php
                        $select = "select * from division";
                        $result_data = mysql_query($select, connect());
                        while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
                        ?>
                        <option><?php echo $data['name']; ?></option>
                        <?php
                        }
                        ?>
                        </select>
                </td>
        </tr>
	<tr>
		<td align="right"><?php echo getword("e-Mail"); ?></td>
		<td colspan=2><input type="text" name="email" id="email" value="<?php echo $row['email']; ?>" value="" style="width:40%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Finger Print Number"); ?></td>
		<td><input type="text" name="finger" id="finger" style="width:20%;" value="<?php echo $row['finger']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Res. Address"); ?></td>
		<td colspan=2><textarea name="em_address" id="em_address" style="width:100%;height:50px;"><?php echo $row['em_address']; ?></textarea></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Postal Code"); ?></td>
		<td colspan=2><input type="text" name="zipcode" id="zipcode" value="<?php echo $row['zipcode']; ?>" value="" style="width:20%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Tel. Number"); ?></td>
		<td colspan=2><input type="text" name="em_number" id="em_number" value="<?php echo $row['em_number']; ?>" value="" style="width:20%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date of Birth"); ?></td>
		<td colspan=2>
			<select name="bdyy"><option><?php echo get_date(0,$row['birthdate']); ?></option><?php echo $yy; ?></select>
			<select name="bdmm"><option><?php echo get_date(1,$row['birthdate']); ?></option><?php echo $mm; ?></select>
			<select name="bddd"><option><?php echo get_date(2,$row['birthdate']); ?></option><?php echo $dd; ?></select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Gender"); ?></td>
		<td colspan=2><select name="gender" id="gender"><option><?php echo $row['gender']; ?></option><?php echo $gender; ?></select></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Blood Type"); ?></td>
		<td colspan=2><input type="text" name="blood_type" id="blood_type" value="<?php echo $row['blood_type']; ?>" value="" style="width:10%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Civil Status"); ?></td>
		<td colspan=2><select name="civil" id="civil"><option><?php echo $row['civil_status']; ?></option><?php echo $civil; ?></select></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Citizenship"); ?></td>
		<td colspan=2>
			<select style="width:20%" name="citizenship" id="citizenship">
			<option><?php echo $row['citizenship']; ?></option>
			<?php
			$select = "select * from nationality";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Ethnic Race"); ?></td>
		<td colspan=2>
			<select style="width:20%" name="race" id="race">
			<option><?php echo $row['race']; ?></option>
			<?php
			$select = "select * from ethnic_race";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Height"); ?></td>
		<td colspan=2><input type="text" name="height" id="height" value="<?php echo $row['height']; ?>" style="width:30%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Weight"); ?></td>
		<td colspan=2><input type="text" name="weight" id="weight" value="<?php echo $row['weight']; ?>" style="width:30%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Emergency Contact Person"); ?></td>
		<td colspan=2><input type="text" name="contact" id="contact" value="<?php echo $row['contact_person']; ?>" style="width:100%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Em. Number"); ?></td>
		<td colspan=2><input type="text" name="cp_number" id="cp_number" value="<?php echo $row['cp_number']; ?>" style="width:20%;"></td>
	</tr>
		<input type="text" name="cp_address" id="cp_address" value="<?php echo $row['cp_address']; ?>" style="width:100%;display:none;" onKeyup="this.value=this.value.toUpperCase()">
	<tr>
		<td align="right"><?php echo getword("Job Description"); ?></td>
		<td colspan=2><textarea name="description" id="description" style="width:100%;height:120px;"><?php echo $row['description']; ?></textarea></td>
	</tr>
	<tr>
		<td align="right" width="18%"><?php echo getword("Employee Status"); ?></td>
		<td width="82%">
			<select style="width:40%" name="employee_status" id="employee_status">
			<option><?php echo $row['employee_status']; ?></option>
			<?php
			$select = "select * from employee_status";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
                <td align="right"><?php echo getword("ID Card Number"); ?></td>
                <td><input type="text" name="icn" id="icn" style="width:20%;" value="<?php echo $row['icn']; ?>" onKeyup="this.value=this.value.toUpperCase()"> | <input type="button" value="upload" onclick="upload('card', this);"> <input type="button" value="view" onclick="view('cardview',this)"></td>
        </tr>
	<tr>
		<td align="right"><?php echo getword("TIN Number"); ?></td>
		<td><input type="text" name="tinn" id="tinn" style="width:20%;" value="<?php echo $row['tinn']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("SSS Number"); ?></td>
		<td><input type="text" name="sssn" id="sssn" style="width:20%;" value="<?php echo $row['sssn']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("PH Number"); ?></td>
		<td><input type="text" name="phn" id="phn" style="width:20%;" value="<?php echo $row['phn']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Pag-Ibig Number"); ?></td>
		<td><input type="text" name="pin" id="pin" style="width:20%;" value="<?php echo $row['pin']; ?>" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("VL Balance"); ?></td>
		<td><input type="text" name="vl" id="vl" style="width:20%;" value="<?php echo $row['vl']; ?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("SL Balance"); ?></td>
		<td><input type="text" name="sl" id="sl" style="width:20%;" value="<?php echo $row['sl']; ?>"></td>
	</tr>
</table>

<br>
<?php
$display = "";
if($_SESSION['user'] == 'raz-rmn'){
	//$disabled = "style='display:none'";
    $readonly = ' disabled=true ';
}
?>
<h3 class="wintitle" <?php echo $disabled; ?>><?php echo getword("Payroll Requirements"); ?></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all" <?php echo $disabled; ?>>
	<tr>
		<td align="right"><?php echo getword("Salary"); ?></td>
		<td><input type="text" name="salary" id="salary" style="width:10%;" value="<?php echo $row['salary']; ?>" onKeyup="this.value=this.value.toUpperCase()" readonly> <input type="button" value="edit" onclick="eDitsalary(this, 'editsalary');" <?php echo $readonly; ?>> <input type="button" value="view" onclick="eDitsalary(this, 'viewsalary');" <?php echo $readonly; ?>></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Salary Based"); ?></td>
		<td>
		<select name="salary_based" style="width:100px;">
			<option><?php echo $row['salary_based']; ?></option>
			<option><?php echo getword("SEMI-MONTHLY"); ?></option>
			<option><?php echo getword("MONTHLY"); ?></option>
			<option><?php echo getword("WEEKLY"); ?></option>
			<option><?php echo getword("DAILY"); ?></option>
			<option><?php echo getword("HOURLY"); ?></option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date From <input type="text" name="date1" style="width:80px" value="<?php echo date('Y-m-d'); ?>"> To <input type="text" name="date2" style="width:80px" value="<?php echo date('Y-m-d'); ?>">&nbsp;&nbsp;<input type="submit" name="update_trxn" value="Update Transaction" <?php echo $readonly; ?>>
		</td>
	</tr>



	<tr>
		<td align="right"><?php echo getword("1st 13th Month"); ?></td>
		<td><input type="text" name="half_13th" id="half_13th" style="width:20%;" value="<?php echo $row['half_13th']; ?>"></td>
	</tr>

	<tr>
		<td align="right"><?php echo getword("2st 13th Month"); ?></td>
		<td><input type="text" name="last_13th" id="last_13th" style="width:20%;" value="<?php echo $row['last_13th']; ?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Additional 13th (eq Allowance)"); ?></td>
		<td><input type="text" name="non_tax_13th" id="non_tax_13th" style="width:20%;" value="<?php echo $row['non_tax_13th']; ?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Bonus"); ?></td>
		<td><input type="text" name="bonus" id="bonus" style="width:20%;" value="<?php echo $row['bonus']; ?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Additional Non Tax Bonus"); ?></td>
		<td><input type="text" name="non_tax_bonus" id="non_tax_bonus" style="width:20%;" value="<?php echo $row['non_tax_bonus']; ?>"></td>
	</tr>

	<tr>
		<td align="right"><?php echo getword("SSS Deduction"); ?></td>
		<td>
		<select name="sss" style="width:8%;">
			<option><?php echo $row['sss']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Pagibig Deduction"); ?></td>
		<td>
		<select name="pi" style="width:8%;">
			<option><?php echo $row['pi']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select> | <input type="text" name="pdm" id="pdm" style="width:70px;" value="<?php echo $row['pdm']; ?>">
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Philhealth Deduction"); ?></td>
		<td>
		<select name="ph" style="width:8%;">
			<option><?php echo $row['ph']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Tax Status"); ?></td>
		<td>
		<select name="ts" style="width:8%;">
			<option><?php echo $row['ts']; ?></option>
			<?php
				$select = "select * from tax_status";
				$result = mysql_query($select, connect());
				while ($tax = mysql_fetch_array($result,MYSQL_ASSOC)){
				?>
				<option><?php echo $tax['name'];?></option>
				<?php
				}
			?>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Pay Code"); ?></td>
		<td>
		<select name="pc" style="width:30%;">
			<option><?php echo $row['pay_id']; ?></option>
			<?php
				$select = "select * from pay group by name";
				$result = mysql_query($select, connect());
				while ($tax = mysql_fetch_array($result,MYSQL_ASSOC)){
				?>
				<option><?php echo $tax['name'];?></option>
				<?php
				}
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date From <input type="text" name="date1_pc" style="width:80px" value="<?php echo date('Y-m-d'); ?>"> To <input type="text" name="date2_pc" style="width:80px" value="<?php echo date('Y-m-d'); ?>">&nbsp;&nbsp;<input type="submit" name="update_trxn_pc" value="Update Transaction" <?php echo $readonly; ?>>

		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Sub Pay Code"); ?></td>
		<td>
		<select name="spc" style="width:30%;">
			<option><?php echo $row['pay_id_sub']; ?></option>
			<?php
				$select = "select * from pay group by name";
				$result = mysql_query($select, connect());
				while ($tax = mysql_fetch_array($result,MYSQL_ASSOC)){
				?>
				<option><?php echo $tax['name'];?></option>
				<?php
				}
			?>
		</select> <b>For Manager Only</b>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Tax Deduction"); ?></td>
		<td>
		<select name="tin" style="width:8%;">
			<option><?php echo $row['tin']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>  |  <input type="text" name="wtax" id="wtax" style="width:60px;" onKeyup="this.value=this.value.toUpperCase()" value="<?php echo $row['wtax']; ?>"> <b>%</b>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed OT"); ?></td>
		<td>
		<select name="allowed_ot" style="width:8%;">
			<option><?php echo $row['allowed_ot']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed Late"); ?></td>
		<td>
		<select name="allowed_late" style="width:8%;">
			<option><?php echo $row['allowed_late']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed Under Time"); ?></td>
		<td>
		<select name="allowed_ut" style="width:8%;">
			<option><?php echo $row['allowed_ut']; ?></option>
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Default Shift Code"); ?></td>
		<td>
		<select name="shift_code" style="width:20%;">
			<?php
				$select = "select * from shift";
				$result = mysql_query($select, connect());
				while ($shift = mysql_fetch_array($result,MYSQL_ASSOC)){
				?>
				<option <?php if ($shift['shift_code'] == $row['shift_code']){ echo 'selected'; }?>><?php echo $shift['shift_code'];?></option>
				<?php
				}
			?>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("BPI Account No."); ?></td>
		<td><input type="text" name="bankaccount" id="bankaccount" style="width:20%;" onKeyup="this.value=this.value.toUpperCase()" value="<?php echo $row['bank_account']; ?>"></td>
	</tr>
</table>
<br>

<h3 class="wintitle"><?php echo getword("Company Engagement"); ?></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td align="right" width=18%><?php echo getword("Immediate Supervisor "); ?></td>
		<td><input type="text" name="keyword" id="keyword" size=27 onKeyup="OnPop('popDiv','keyword',0,-310,2);" onBlur="PopHide('popDiv');" value="<?php echo $row['manager']; ?>"></td>
	</tr>
	<tr>
		<td align="right" width=18%><?php echo getword("Date Employed"); ?></td>
		<td width=82%>
			<select name="debdyy"><option><?php echo get_date(0,$row['date_employed']); ?></option><?php echo $yy; ?></select>
			<select name="debdmm"><option><?php echo get_date(1,$row['date_employed']); ?></option><?php echo $mm; ?></select>
			<select name="debddd"><option><?php echo get_date(2,$row['date_employed']); ?></option><?php echo $dd; ?></select>
			 | <input type="button" value="upload" onclick="upload('letter', this);"> <input type="button" value="view" onclick="view('letterview',this)">
			</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("File Status"); ?></td>
		<td>
			<select style="width:30%" name="file_status" id="file_status">
				<option <?php if ($row['file_status'] == 'EMPLOYEE'){ echo 'selected'; }?> >EMPLOYEE</option>
				<option <?php if ($row['file_status'] == 'PROBEE'){ echo 'selected'; }?> >PROBEE</option>
				<option <?php if ($row['file_status'] == 'CONSULTANT'){ echo 'selected'; }?> >CONSULTANT</option>
				<option <?php if ($row['file_status'] == 'CONTRACTUAL'){ echo 'selected'; }?> >CONTRACTUAL</option>
				<option <?php if ($row['file_status'] == 'DRAMA-TALENTS-RMN'){ echo 'selected'; }?> >DRAMA-TALENTS-RMN</option>
				<option <?php if ($row['file_status'] == 'TALENTS-IBMI'){ echo 'selected'; }?> >TALENTS-IBMI</option>
				<option <?php if ($row['file_status'] == 'RETIRED'){ echo 'selected'; }?> >RETIRED</option>
				<option <?php if ($row['file_status'] == 'DISMISSED'){ echo 'selected'; }?> >DISMISSED</option>
				<option <?php if ($row['file_status'] == 'RESIGNED'){ echo 'selected'; }?> >RESIGNED</option>
				<option <?php if ($row['file_status'] == 'APPLICANT'){ echo 'selected'; }?> >APPLICANT</option>
				<option <?php if ($row['file_status'] == 'SEPARATED'){ echo 'selected'; }?> >SEPARATED</option>
				<option <?php if ($row['file_status'] == 'HOLD'){ echo 'selected'; }?> >HOLD</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Company"); ?></td>
		<td>
			<select style="width:30%" name="company_id" id="company_id">
			<?php
			$result_data = $result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>" <?php if ($data['id'] == $row['company_id']){ echo 'selected'; }?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Branch"); ?></td>
		<td>
			<select style="width:50%" name="branch" id="branch">
			<?php
			$result_data = get_mybranch();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['branch']; ?>" <?php if ($data['branch'] == $row['branch']){ echo 'selected'; }?>><?php echo $data['company'] . " - " . $data['branch']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date Permanent"); ?></td>
		<td>
			<select name="dpbdyy"><option><?php echo get_date(0,$row['date_permanent']); ?></option><?php echo $yy; ?></select>
			<select name="dpbdmm"><option><?php echo get_date(1,$row['date_permanent']); ?></option><?php echo $mm; ?></select>
			<select name="dpbddd"><option><?php echo get_date(2,$row['date_permanent']); ?></option><?php echo $dd; ?></select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date Leaving"); ?></td>
		<td>
			<select name="dlbdyy"><option><?php echo get_date(0,$row['reason_living_date']); ?></option><option>0000</option><?php echo $yy; ?></select>
			<select name="dlbdmm"><option><?php echo get_date(1,$row['reason_living_date']); ?></option><option>00</option><?php echo $mm; ?></select>
			<select name="dlbddd"><option><?php echo get_date(2,$row['reason_living_date']); ?></option><option>00</option><?php echo $dd; ?></select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Reason in Leaving"); ?></td>
		<td><input type="text" name="reason_living" id="reason_living" value="<?php echo $row['reason_living']; ?>" style="width:100%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=100% align="right"><input type="submit" name="update" value="<?php echo getword("update"); ?>"></td>
	</tr>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
</form>
<center><font size=1px><a href="index.php?menu=<?php echo $_GET['menu']; ?>&id=<?php echo $_GET['id']; ?>#sectiontop"><?php echo getword("back to top"); ?></a></font></center>

<div class="xstooltips " id="upload">
	<form method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="id_number" id="id_number" value="<?php echo $_GET['id']; ?>">
	<input type="hidden" name="dir" id="dir">
	<table width=100% border=0>
		<tr>
			<td width="100%">Description</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="udescription" style="width:250px;"></td>
		</tr>
		<tr>
			<td>File Location</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" name="ufile" id="ufile"></td>
		</tr>
		<tr>
			<td><input type="submit" value="upload" name="upload" onclick="return checkfile();"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right"><input type="button" value="Close" onclick="document.getElementById('upload').style.visibility='hidden';"></td>
		</tr>
	</table>
	<input type="hidden" name="count" value="<?php echo $x; ?>">
	</form>
</div>

<div id="editsalary">
	<form method="POST">
	<input type="hidden" name="id_number" id="id_number" value="<?php echo $_GET['id']; ?>">
	<input type="hidden" name="oldsalary" id="oldsalary" value="<?php echo $row['salary']; ?>" >
	<table width=100% border=0>
		<tr>
			<td width="100px">New Salary</td>
			<td width="200px"><input type="text" name="newsalary" id="newsalary" style="width:70px;"></td>
		</tr>
		<tr>
			<td>Type</td>
			<td>
				<select name="saltype" id="saltype">
					<option>INCREASE</option>
					<option>CORRECTION</option>
					<option>ADJUSTMENT</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Remarks</td>
			<td><textarea name="salrem" id="salrem" style="width:200px;"></textarea></td>
		</tr>
		<tr>
			<td colspan=2><input type="submit" name="svsal" value="save" onclick="return chsalary('<?php echo $row['salary']; ?>');"></td>
		</tr>
	</table>
	</form>
</div>

<div id="viewsalary">
	<table width=100% border=0>
		<tr bgcolor="orange">
			<td width="80px">Date</td>
			<td width="80px">From</td>
			<td width="80px">To</td>
			<td width="100px">Type</td>
			<td>Remarks</td>
		</tr>
		<?php
		$select = "select * from employee_salary where em_id = '" . $_GET['id'] . "'";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		?>
		<tr>
			<td><?php echo $row['date']; ?></td>
			<td><?php echo $row['old_salary']; ?></td>
			<td><?php echo $row['new_salary']; ?></td>
			<td><?php echo $row['type']; ?></td>
			<td><?php echo $row['remarks']; ?></td>
		</tr>
		<?php
		}
		?>
	</table>
</div>

<div class="view" id="cardview">
	<table width=100% border=0>
		<tr bgcolor="orange" height="20px">
			<td width="20%">File Name</td>
			<td>Description</td>
			<td width="30%">Date Uploaded</td>
			<td width="15%" align="center">Action</td>
		</tr>
		<?php
		$select = "select * from employee_card where em_id = '" . $_GET['id'] . "'";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$ext = substr($row['filename'], strrpos($row['filename'], ".") + 1);
		$path = "card/" . $row['id'] . "." . $ext;
		$date = explode(" ", $row['datetime']);
		?>
		<form method="POST">
		<tr>
			<td><?php echo $row['filename']; ?></td>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $date[0] . " by : " . $row['name']; ?></td>
			<td align="center"><input type="button" value="v" title="view this file" onclick="openpop('<?php echo $path; ?>', screen.width,screen.height);"> <input type="submit" name="cdelete" value="d" title="delete this file"></td>
		</tr>
		<input type="hidden" name="cid" value="<?php echo $row['id']; ?>">
		</form>
		<?php
		}
		?>
		<tr>
			<td align="left" colspan=3><input type="button" value="Close" onclick="document.getElementById('cardview').style.visibility='hidden';"></td>
		</tr>
	</table>
	<input type="hidden" name="count" value="<?php echo $x; ?>">
</div>
<div class="view" id="letterview">
	<table width=100% border=0>
		<tr bgcolor="orange" height="20px">
			<td width="20%">File Name</td>
			<td>Description</td>
			<td width="30%">Date Uploaded</td>
			<td width="15%" align="center">Action</td>
		</tr>
		<?php
		$select = "select * from employee_letter where em_id = '" . $_GET['id'] . "'";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$ext = substr($row['filename'], strrpos($row['filename'], ".") + 1);
		$path = "letter/" . $row['id'] . "." . $ext;
		$date = explode(" ", $row['datetime']);
		?>
		<form method="POST">
		<tr>
			<td><?php echo $row['filename']; ?></td>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $date[0] . " by : " . $row['name']; ?></td>
			<td align="center">
				<input type="button" value="v" title="view this file" onclick="openpop('<?php echo $path; ?>', screen.width,screen.height);">
				<input type="submit" name="ldelete" value="d" title="delete this file">
			</td>
		</tr>
		<input type="hidden" name="lid" value="<?php echo $row['id']; ?>">
		</form>
		<?php
		}
		?>
		<tr>
			<td align="left" colspan=3><input type="button" value="Close" onclick="document.getElementById('letterview').style.visibility='hidden';"></td>
		</tr>
	</table>
	<input type="hidden" name="count" value="<?php echo $x; ?>">
</div>
<script>
function jsUpload(upload_field){
	var re_text = /\.gif|\.png|\.jpg/i;
	var filename = upload_field.value;
	if (filename.search(re_text) == -1){
		alert("File does not have pic");
		upload_field.form.reset();
		return false;
		}
	upload_field.form.submit();
	return true;
	}

function upload(dir,parentId){
	var it = document.getElementById('upload');
    	var img = parentId;
	var dirid = document.getElementById('dir');
	dirid.value = dir;

	x = findPosX(img) - 300;
	y = findPosY(img);

	it.style.top = y + 'px';
	it.style.left = x + 'px';
	it.style.visibility = 'visible';
	dirid.value=dir;
	}

function view(id,parentId){
	var it = document.getElementById(id);
    	var img = parentId;

	x = findPosX(img)+1;
	y = findPosY(img)+20;

	it.style.top = y + 'px';
	it.style.left = x + 'px';
	it.style.visibility = 'visible';
	}
function checkfile(){
	var ufile = document.getElementById('ufile');
	file = ufile.value
	if(file){
		file = file.split(".");
		c = file.length
		if (file[c-1] == 'pdf'){
			return true;
			}
		else{
			alert('not a pdf file');
			return false;
			}
		}
	else{
		alert('no selected file');
		return false;
		}
	}

</script>
<style>
.xstooltips{
	visibility: hidden;
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 2;
	width:300px;
	height:200px;
	font: normal 8pt sans-serif;
	margin: 0px 0px 0px 0px;
	padding: 0 0 0 0;
	border: solid 1px black;
	background-color: white;
	}
.view{
	visibility: hidden;
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 2;
	width:580px;
	font: normal 8pt sans-serif;
	margin: 0px 0px 0px 0px;
	padding: 0 0 0 0;
	border: solid 1px black;
	background-color: white;
	}

#editsalary{
	visibility: hidden;
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 2;
	width:320px;
	margin: 0px 0px 0px 0px;
	padding: 0 0 0 0;
	border: solid 1px black;
	background-color: white;
	}

#viewsalary{
	visibility: hidden;
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 2;
	width:400px;
	margin: 0px 0px 0px 0px;
	padding: 0 0 0 0;
	border: solid 1px black;
	background-color: white;
	}
</style>
