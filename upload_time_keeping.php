<?php
$tf = 'timecards/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false) 
	die("Not writable. Set");
fclose($f);
unlink($tf);

function get_id(){
	$select = "select max(id) as maxid from uploaded_timecard";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'];
	}
	
function get_com($id){
	$select = "select company_id from employee where em_id = '" . $id . "'";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['company_id'];
	}

if (isset($_POST['add'])){
	if (isset($_FILES['filename'])){
		if ($_FILES['filename']['error'] == UPLOAD_ERR_OK){
		
			$id = get_id() + 1;
					
			$filename = $_FILES['filename']['name'];
			
			$insert = "INSERT INTO `uploaded_timecard` (
				`id` ,
				`filename` ,
				`tittle` ,
				`date`,
				`time`
				)
				VALUES (
				NULL,
				'" . $id . $filename . "',
				'" . $_POST['tittle'] . "',
				curdate(),
				curtime()
				);";
			mysql_query($insert, connect());
			
			move_uploaded_file($_FILES['filename']['tmp_name'], 'timecards/' . $id . $filename);
						
			
			}
		elseif ($_FILES['cfilename']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
		}
		
	echo $result_msg;
	}

if (isset($_POST['del'])){
	$delete = "delete from uploaded_timecard where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}

?>
<h3 class="wintitle">Upload Time Keeping</h3>
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id">

<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="lightblue">
	<tr>
		<td width=40% align="left">Tittle</td>
		<td width=40% align="left">Filename</td>
	</tr>
	<tr>
		<td><input type="text" name="tittle" style="width:100%" value=""></td>
		<td><input type="file" name="filename" onChange="picupload(this)"></td>
		<td><input type="submit" name="add" value="add"></td>
	</tr>
	<?php
	$select = "select * from uploaded_timecard ";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	
	?>
	<tr>
		<td><?php echo $row['tittle']; ?></td>
		<td><?php echo $row['filename']; ?></td>
		<td><input type="button" name="post" value="view" onclick="openTimeCard('<?php echo $row['filename']; ?>');"> | <input type="submit" name="del" value="del" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>