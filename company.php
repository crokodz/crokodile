<?php
include "config.php";

$select_deduction = "
	<option value='hh'>Half 15th, Whole-Half 30th</option>
	<option value='h'>Half - Half per month</option>
	<option value='w1'>Whole - First pay day of the month</option>
	<option value='w2'>Whole - Last pay day of the month</option>
	";

function getded($id){
	if ($id == 'h'){
		return 'Half - Half per month';
		}
	elseif ($id == 'w1'){
		return 'Whole - First pay day of the month';
		}
	elseif ($id == 'w2'){
		return 'Whole - Last pay day of the month';
		}
	elseif ($id == 'hh'){
		return 'Half 15th, Whole-Half 30th';
		}
	}
	
function get_id(){
	$result = mysql_query("select max(id) as total from company", connect()); 
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['total'] + 1;
	}

if (isset($_POST['save'])){
	$id = get_id();
	
	move_uploaded_file($_FILES['cfilename']['tmp_name'], 'style/' . $id . 'jpg');

	$insert = "INSERT INTO `company` (
		`id` ,
		`name` ,
		`currency` ,
		`address` ,
		`number` ,
		`tin` ,
		`bussiness` ,
		`description` ,
		`status` ,
		`foundation`,
		`tax`,
		`pi`,
		`ph`,
		`sss`,
		`factor`,
		`days`,
		`night_differential`,
		`nd_rate`,
		`min`,
		`tin_name`,
		`tin_rdo`,
		`bank_name`,
		`bank_account`,
		`sssn`,
		`phn`,
		`pin`
		)
		VALUES (
		'" . $id . "' , 
		'" . $_POST['name'] . "',
		'" . $_POST['currency'] . "',
		'" . $_POST['address'] . "',
		'" . $_POST['number'] . "',
		'" . $_POST['tin'] . "',
		'" . $_POST['bussiness'] . "',
		'" . $_POST['description'] . "',
		'" . $_POST['status'] . "',
		'" . $_POST['foundation'] . "',
		'" . $_POST['tax'] . "',
		'" . $_POST['pi'] . "',
		'" . $_POST['ph'] . "',
		'" . $_POST['ssss'] . "',
		'" . $_POST['factor'] . "',
		'" . $_POST['days'] . "',
		'" . $_POST['night_differential'] . "',
		'" . $_POST['nd_rate'] . "',
		'" . $_POST['min'] . "',
		'" . $_POST['tin_name'] . "',
		'" . $_POST['tin_rdo'] . "',
		'" . $_POST['bank_name'] . "',
		'" . $_POST['bank_account'] . "',
		'" . $_POST['sssn'] . "',
		'" . $_POST['phn'] . "',
		'" . $_POST['pin'] . "'
		)";
		
	mysql_query($insert, connect());
	
	echo "<script>";
	echo "window.location='company.php?id=" . $id . "'";
	echo "</script>";
	}

if (isset($_POST['update'])){
	if (isset($_FILES['cfilename'])){
		if ($_FILES['cfilename']['error'] == UPLOAD_ERR_OK){
			move_uploaded_file($_FILES['cfilename']['tmp_name'], 'style/' . $_GET['id'] . '.png');
			}
		elseif ($_FILES['cfilename']['error'] == UPLOAD_ERR_INI_SIZE){
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			}
		else {
			$result_msg = 'Unknown error';
			}
		}
		
	$update = "UPDATE `company` SET 
		`name` = '" . $_POST['name'] . "',
		`address` = '" . $_POST['address'] . "',
		`currency` = '" . $_POST['currency'] . "',
		`number` = '" . $_POST['number'] . "',
		`tin` ='" . $_POST['tin'] . "',
		`bussiness` = '" . $_POST['bussiness'] . "',
		`description` = '" . $_POST['description'] . "',
		`foundation` = '" . $_POST['foundation'] . "',
		`tax` = '" . $_POST['tax'] . "',
		`pi` = '" . $_POST['pi'] . "',
		`ph` = '" . $_POST['ph'] . "',
		`sss` = '" . $_POST['sss'] . "',
		`factor` = '" . $_POST['factor'] . "',
		`days` = '" . $_POST['days'] . "',
		`night_differential` = '" . $_POST['night_differential'] . "',
		`nd_rate` = '" . $_POST['nd_rate'] . "',
		`min` = '" . $_POST['min'] . "',
		`tin_name` = '" . $_POST['tin_name'] . "',
		`tin_rdo` = '" . $_POST['tin_rdo'] . "',
		`bank_name` = '" . $_POST['bank_name'] . "',
		`bank_account` = '" . $_POST['bank_account'] . "',
		`sssn` = '" . $_POST['sssn'] . "',
		`pin` = '" . $_POST['pin'] . "',
		`phn` = '" . $_POST['phn'] . "'
		WHERE 
		`id` = '" . $_GET['id'] . "'
		";
	mysql_query($update, connect());
	}

$result = mysql_query("select * from company where id = '" . $_GET['id'] . "'", connect()); 
$row = mysql_fetch_array($result,MYSQL_ASSOC);
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>

<br>
<body id="innerframe">
<form method="post" enctype="multipart/form-data">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width="20%">Company Name</td>
		<td width="80%"><input type="text" name="name" size="30" value="<?php echo $row['name']; ?>"></td>
	</tr>
	<tr>
		<td>Address</td>
		<td><input type="text" name="address" style="width:100%" value="<?php echo $row['address']; ?>"></td>
	</tr>
	<tr>
		<td>Telephone Number</td>
		<td><input type="text" name="number" size="20" value="<?php echo $row['number']; ?>"></td>
	</tr>
	<tr>
		<td>Company Bussiness</td>
		<td><input type="text" name="bussiness" size="50" value="<?php echo $row['bussiness']; ?>"></td>
	</tr>
	<tr>
		<td>Description</td>
		<td><textarea name="description" cols="50"><?php echo $row['description']; ?></textarea></td>
	</tr>
	<tr>
		<td>Foundation Date</td>
		<td><input type="text" name="foundation" size="30" value="<?php echo $row['foundation']; ?>"></td>
	</tr>
	<tr>
		<td>Currency</td>
		<td>
			<select style="width:10%" name="currency" id="currency">
			<option><?php echo $row['currency']; ?></option>
			<?php
			$select = "select * from currency";
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
		<td>Status</td>
		<td>
			<select style="width:10%" name="status" id="status">
			<option><?php if(empty($row['status'])){ echo 'active'; } else{ echo $row['status']; } ?></option>
			<option>active</option>
			<option>inctive</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Tax Deduction</td>
		<td>
			<select style="width:50%" name="tax" id="tax">
			<?php
			if ($row['tax']){ echo '<option value="' . $row['tax'] . '">' . getded($row['tax']) . '</option>';}
			?>
			<?php echo $select_deduction; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>SSS Deduction</td>
		<td>
			<select style="width:50%" name="sss" id="sss">
			<?php
			if ($row['sss']){ echo '<option value="' . $row['sss'] . '">' . getded($row['sss']) . '</option>';}
			?>
			<?php echo $select_deduction; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>PagIbig Deduction</td>
		<td>
			<select style="width:50%" name="pi" id="pi">
			<?php
			if ($row['pi']){ echo '<option value="' . $row['pi'] . '">' . getded($row['pi']) . '</option>';}
			?>
			<?php echo $select_deduction; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Phil Health Decuction</td>
		<td>
			<select style="width:50%" name="ph" id="ph">sss
			<?php
			if ($row['ph']){ echo '<option value="' . $row['ph'] . '">' . getded($row['ph']) . '</option>';}
			?>
			<?php echo $select_deduction; ?>
			</select>
		</td>
	</tr>
	<input type="hidden" name="factor" size="10" value="<?php echo $row['factor']; ?>">
	<input type="hidden" name="days" size="10" value="<?php echo $row['days']; ?>">
	<input type="hidden" name="min" size="10" value="<?php echo $row['min']; ?>">
	
	
	<tr>
		<td>SSS No.</td>
		<td><input type="text" name="sssn" value="<?php echo $row['sssn']; ?>" style="width:100px;"></td>
	</tr>
	<tr>
		<td>PhilHealth No.</td>
		<td><input type="text" name="phn" value="<?php echo $row['phn']; ?>" style="width:100px;"></td>
	</tr>
	<tr>
		<td>PagIbig No.</td>
		<td><input type="text" name="pin" value="<?php echo $row['pin']; ?>" style="width:100px;"></td>
	</tr>
	<tr>
		<td>Bir Registered Name</td>
		<td><input type="text" name="tin_name" size="30" value="<?php echo $row['tin_name']; ?>"></td>
	</tr>
	<tr>
		<td>Tin Number</td>
		<td><input type="text" name="tin" size="30" value="<?php echo $row['tin']; ?>"></td>
	</tr>
	<tr>
		<td>RDO Number</td>
		<td><input type="text" name="tin_rdo" size="30" value="<?php echo $row['tin_rdo']; ?>"></td>
	</tr>
	<tr>
		<td>Bank Name</td>
		<td><input type="text" name="bank_name" value="<?php echo $row['bank_name']; ?>" style="width:150px;"></td>
	</tr>
	<tr>
		<td>Bank Account Number</td>
		<td><input type="text" name="bank_account" value="<?php echo $row['bank_account']; ?>" style="width:100px;"></td>
	</tr>
	
	<tr>
		<td>Time Starts Night Differential</td>
		<td><input type="text" name="night_differential" size="10" value="<?php echo $row['night_differential']; ?>"></td>
	</tr>
	<input type="hidden" name="nd_rate" size="10" value="<?php echo $row['nd_rate']; ?>">
	<tr>
		<td>Logo</td>
		<td><input type="file" name="cfilename" onChange="picupload(this)"> size : [20.32cm x 1.76cm]</td>
	</tr>
	<tr>
		<td colspan="2">
		<?php
		if (empty($_GET['id'])){
		?>
		<input type="submit" name="save" value="save">
		<?php
		}
		else{
		?>
		<input type="submit" name="update" value="update">
		<?php
		}
		?>
		</td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>