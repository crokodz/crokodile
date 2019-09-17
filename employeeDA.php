<?php
function getID(){
	$select = "select max(login_id) as maxid from employee";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if($row['maxid']){
		$idx = $row['maxid'];//preg_replace("/[^0-9]/","",$row['maxid']);
		if($_SESSION['company'] == 0){
			$pref = "RMN-";
		} else {
			$select1 = "select name from company where id = " . $_SESSION['company'];
			$result1 = mysql_query($select1,connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);

			$pref = substr($row1['name'], 0, 3) . '-';
		}

		return  $pref . ($idx +1);
		}
	else{
		return 1;
		}
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

if (isset($_POST['save'])){
	$birth_date = $_POST['bdyy'] . "-" . $_POST['bdmm'] . "-" . $_POST['bddd'];

	$employed_date = $_POST['debdyy'] . "-" . $_POST['debdmm'] . "-" . $_POST['debddd'];

	$name = $_POST['fname'] . " " . $_POST['mname'] . " " . $_POST['lname'];

	$insert = "INSERT INTO `employee` (
		`em_id` ,
		`name` ,
		`em_address` ,
		`em_number` ,
		`birthdate` ,
		`gender` ,
		`civil_status` ,
		`citizenship` ,
		`contact_person` ,
		`cp_number` ,
		`cp_address` ,
		`position` ,
		`salary` ,
		`salary_based`,
		`department` ,
		`description`,
		`username`,
		`datetime`,
		`sss`,
		`pi`,
		`ph`,
		`tin`,
		`sssn`,
		`pin`,
		`phn`,
		`tinn`,
		`ts`,
		`race`,
		`employee_status`,
		`branch`,
		`status`,
		`allowed_late`,
		`date_employed`,
		`allowed_ot`,
		`allowed_ut`,
		`shift_code`,
		`company_id`,
		`pay_id`,
		`pin_number`,
		`finger`,
		`manager`,
		`login_id`,
		`icn`,
		`ename`,
		`zipcode`,
		`email`,
		`bank_account`,
		`file_status`,
		`division`,
		`fname`,
		`lname`,
		`mname`,
		`wtax`,
		`pdm`,
		`blood_type`,
		`pay_id_sub`
		)
		VALUES (
		'" . $_POST['em_id'] . "',
		'" . $name . "',
		'" . $_POST['em_address'] . "',
		'" . $_POST['em_number'] . "',
		'" . $birth_date . "',
		'" . $_POST['gender'] . "',
		'" . $_POST['civil'] . "',
		'" . $_POST['citizenship'] . "',
		'" . $_POST['contact'] . "',
		'" . $_POST['cp_number'] . "',
		'" . $_POST['cp_address'] . "',
		'" . $_POST['position'] . "',
		'" . $_POST['salary'] . "',
		'" . $_POST['salary_based'] . "',
		'" . $_POST['department'] . "',
		'" . $_POST['description'] . "',
		'',
		now(),
		'" . $_POST['sss'] . "',
		'" . $_POST['pi'] . "',
		'" . $_POST['ph'] . "',
		'" . $_POST['tin'] . "',
		'" . $_POST['sssn'] . "',
		'" . $_POST['pin'] . "',
		'" . $_POST['phn'] . "',
		'" . $_POST['tinn'] . "',
		'" . $_POST['ts'] . "',
		'" . $_POST['race'] . "',
		'" . $_POST['employee_status'] . "',
		'" . $_POST['branch'] . "',
		'active',
		'" . $_POST['allowed_late'] . "',
		'" . $employed_date . "',
		'" . $_POST['allowed_ot'] . "',
		'" . $_POST['allowed_ut'] . "',
		'" . $_POST['shift_code'] . "',
		'" . $_POST['company_id'] . "',
		'" . $_POST['pc'] . "',
		'" . $_POST['pin_number'] . "',
		'" . $_POST['finger'] . "',
		'" . $_POST['keyword'] . "',
		'" . $_POST['login_id'] . "',
		'" . $_POST['icn'] . "',
		'" . $_POST['ename'] . "',
		'" . $_POST['zipcode'] . "',
		'" . $_POST['email'] . "',
		'" . $_POST['bankaccount'] . "',
		'" . $_POST['file_status'] . "',
		'" . $_POST['division'] . "',
		'" . $_POST['fname'] . "',
		'" . $_POST['lname'] . "',
		'" . $_POST['mname'] . "',
		'" . $_POST['wtax'] . "',
		'" . $_POST['pdm'] . "',
		'" . $_POST['blood_type'] . "',
		'" . $_POST['spc'] . "'
		);";
	mysql_query($insert, connect());

	echo "<script>";
	echo "window.location='index.php?menu=1DE&id=" . $_POST['em_id'] . "';";
	echo "</script>";
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

$id = getID();

?>
<h3 class="wintitle"><?php echo getword("Employee Data Entry"); ?></h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td align="right"><?php echo getword("First Name"); ?></td>
		<td><input type="text" name="fname" id="fname" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
          </tr>
	<tr>
		<td align="right"><?php echo getword("Middle Name"); ?></td>
		<td><input type="text" name="mname" id="mname" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
          </tr>
	<tr>
		<td align="right"><?php echo getword("Last Name"); ?></td>
		<td><input type="text" name="lname" id="lname" style="width:180px;" onKeyup="this.value=this.value.toUpperCase()"></td>
          </tr>
	<tr>
		<td width=18% align="right"><?php echo getword("Staff Code"); ?></td>
		<td width=82%><input type="text" name="em_id" id="em_id" value="<?php echo $id; ?>" style="width:20%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
                <td width=18% align="right"><?php echo getword("Login ID"); ?></td>
                <td width=82%><input type="text" name="login_id" id="login_id" value="" style="width:10%;" onKeyup="this.value=this.value.toUpperCase()"> | <?php echo getword("Pin Number"); ?> <input type="text" name="pin_number" id="pin_number" style="width:10%;" value="1234" onKeyup="this.value=this.value.toUpperCase()"></td>
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
		<td><input type="text" name="finger" id="finger" style="width:20%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Res. Address"); ?></td>
		<td><textarea name="em_addres" id="em_addres" style="width:100%;height:50px;"></textarea></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Postal Code"); ?></td>
		<td><input type="text" name="zipcode" id="zipcode" value="" style="width:20%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Tel. Number"); ?></td>
		<td><input type="text" name="em_number" id="em_number" value="" style="width:20%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date of Birth"); ?></td>
		<td><select name="bdyy"><?php echo $yy; ?></select><select name="bdmm"><?php echo $mm; ?></select><select name="bddd"><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Gender"); ?></td>
		<td><select name="gender" id="gender"><?php echo $gender; ?></select></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Blood Type"); ?></td>
		<td colspan=2><input type="text" name="blood_type" id="blood_type" value="" style="width:10%;"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Civil Status"); ?></td>
		<td><select name="civil" id="civil"><?php echo $civil; ?></select></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Citizenship"); ?></td>
		<td>
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
		<td>
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
		<td align="right"><?php echo getword("Emergency Contact Person"); ?></td>
		<td><input type="text" name="contact" id="contact" style="width:100%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Em. Number"); ?></td>
		<td><input type="text" name="cp_number" id="cp_number" style="width:20%;"></td>
	</tr>
	<input type="text" name="cp_address" id="cp_address" style="width:100%;display:none;" onKeyup="this.value=this.value.toUpperCase()">
	</tr>
	<tr>
		<td align="right"><?php echo getword("Job Description"); ?></td>
		<td><textarea name="description" id="description" style="width:100%;height:120px;"></textarea></td>
	</tr>
</table>
<br>

<?php
$disabled = "";
?>

<h3 class="wintitle" <?php echo $disabled; ?>><?php echo getword("Payroll Requirements"); ?></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all" <?php echo $disabled; ?>>
	<tr>
		<td width=18% align="right"><?php echo getword("Salary"); ?></td>
		<td width=82%><input type="text" name="salary" id="salary" style="width:10%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Salary Based"); ?></td>
		<td>
		<select name="salary_based" style="width:40%;">
			<option><?php echo getword("SEMI-MONTHLY"); ?></option>
			<option><?php echo getword("MONTHLY"); ?></option>
			<option><?php echo getword("WEEKLY"); ?></option>
			<option><?php echo getword("DAILY"); ?></option>
			<option><?php echo getword("HOURLY"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("SSS Deduction"); ?></td>
		<td>
		<select name="sss" style="width:8%;">
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
		</select> | <input type="text" name="pdm" id="pdm" style="width:70px;">
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
			<?php
				$select = "select * from pay group by name";
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
		<td align="right"><?php echo getword("Sub Pay Code"); ?></td>
		<td>
		<select name="spc" style="width:30%;">
			<option></option>
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
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select> | <input type="text" name="wtax" id="wtax" style="width:60px;" onKeyup="this.value=this.value.toUpperCase()"> <b>%</b>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed OT"); ?></td>
		<td>
		<select name="allowed_ot" style="width:8%;">
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed Late"); ?></td>
		<td>
		<select name="allowed_late" style="width:8%;">
			<option><?php echo getword("YES"); ?></option>
			<option><?php echo getword("NO"); ?></option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Allowed UT"); ?></td>
		<td>
		<select name="allowed_ut" style="width:8%;">
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
				<option><?php echo $shift['shift_code'];?></option>
				<?php
				}
			?>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("BPI Account No."); ?></td>
		<td><input type="text" name="bankaccount" id="bankaccount" style="width:20%;" onKeyup="this.value=this.value.toUpperCase()"></td>
	</tr>
</table>
<br>
<h3 class="wintitle"><?php echo getword("Company Engagement"); ?></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td align="right" width=18%><?php echo getword("Manager"); ?></td>
		<td><input type="text" name="keyword" id="keyword" size=27 onKeyup="OnPop('popDiv','keyword',0,-310,2);" onBlur="PopHide('popDiv');"></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date Employed"); ?></td>
		<td width=82%>
			<select name="debdyy"><?php echo $yy; ?></select><select name="debdmm"><?php echo $mm; ?></select><select name="debddd"><?php echo $dd; ?></select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("File Status"); ?></td>
		<td>
			<select style="width:30%" name="file_status" id="file_status">
			<option>EMPLOYEE</option>
			<option>APPLICANT</option>
			<option>SEPARATED</option>
			<option>HOLD</option>
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
			<option value="<?php echo $data['id']; ?>" <?php if($data['id'] == $_GET['c']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
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
			<option value="<?php echo $data['branch']; ?>"><?php echo $data['company'] . " - " . $data['branch']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
</table>

<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=100% align="right"><input type="submit" name="save" value="<?php echo getword("save"); ?>"></td>
	</tr>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
</form>
