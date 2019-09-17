<?php
$tf = 'certificate/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false) 
	die("Not writable. Set");
fclose($f);
unlink($tf);

function get_id(){
	$select = "select max(id) + 1 as maxid from employee_certificate";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['maxid'];
	}

if (isset($_POST['cadd'])){
	if (isset($_FILES['cfilename'])){
		if ($_FILES['cfilename']['error'] == UPLOAD_ERR_OK){
			$id = get_id();
		
			$filename = $_FILES['cfilename']['name'];
			move_uploaded_file($_FILES['cfilename']['tmp_name'], 'certificate/' . $id . $filename);
						
			$insert = "insert into employee_certificate values ('" . $id . "','" . $_GET['id'] . "','" . $_POST['cname'] . "','" . $_POST['cdate'] . "','" . $id . $filename . "')";
			mysql_query($insert, connect());
			}
		elseif ($_FILES['cfilename']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
		}
		
	echo $result_msg;
	}

if (isset($_POST['cdel'])){
	$delete = "delete from employee_certificate where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="lightblue">
	<tr>
		<td colspan=4 align="left">CERTIFICATES</td>
	</tr>
	<tr>
		<td width=25% align="left">Title</td>
		<td width=40% align="left">Filename</td>
		<td width=20% align="left" colspan=2>Date Awarded/Issued</td>
	</tr>
	<tr>
		<td><input type="text" name="cname" style="width:100%"></td>
		<td><input type="file" name="cfilename" onChange="picupload(this)"></td>
		<td><input type="text" name="cdate" style="width:100%"></td>
		<td width=5%><input type="submit" name="cadd" value="add" style="width:100%"></td>
	</tr>
	<?php
	$select = "select * from employee_certificate where em_id = '" . $_GET['id'] . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><a href="certificate/<?php echo $row['filename']; ?>"><?php echo $row['filename']; ?></a></td>
		<td><?php echo $row['date']; ?></td>
		<td><input type="submit" name="cdel" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>