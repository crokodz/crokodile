<?php
include "config.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<link href="styles/default/default.css" rel="stylesheet" type="text/css" media="screen" />
<script language="JavaScript" src="windiag.js"></script>
<?php
if(isset($_POST['id'])){
	if(is_dir($_POST['id'])){
		rmdir($_POST['id']);
		}
	else{
		unlink($_POST['id']);
		$delete = "delete from documents where `id` = '" . $_POST['id'] . "'";
		mysql_query($delete, connect());
		}
	}

if(isset($_POST['create'])){
	mkdir($_POST['dir']."/".$_POST['newdir'], 0700);
	}
	
if(isset($_POST['update'])){
	$delete = "delete from documents where `id` = '" . $_POST['dir2'] . "'";
	mysql_query($delete, connect());
	
	for($x=0;$x<$_POST['ecount'];$x++){
		$insert = "
			INSERT INTO `documents` (
			`id` ,
			`description` ,
			`date` ,
			`company`
			)
			VALUES (
			'" . $_POST['dir2'] . "', 
			'" . $_POST['edescription'] . "', 
			CURDATE(),
			'" . $_POST['ecb' . $x] . "'
			);
			";
		mysql_query($insert, connect());
		}
	$insert = "
		INSERT INTO `documents` (
		`id` ,
		`description` ,
		`date` ,
		`company`
		)
		VALUES (
		'" . $_POST['dir2'] . "', 
		'" . $_POST['edescription'] . "', 
		CURDATE(),
		'0'
		);
		";
	mysql_query($insert, connect());
	}
	
if(isset($_POST['upload'])){
	if (isset($_FILES['file'])){
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK){
			$filename = $_FILES['file']['name'];
			$ext = $_FILES['file']['ext'];
			$path = $_POST['dir1'].'/'.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'], $_POST['dir1'].'/'.$filename);
			
			for($x=0;$x<$_POST['count'];$x++){
				$insert = "
					INSERT INTO `documents` (
					`id` ,
					`description` ,
					`date` ,
					`company`
					)
					VALUES (
					'" . $path . "', 
					'" . $_POST['description'] . "', 
					CURDATE(),
					'" . $_POST['cb' . $x] . "'
					);
					";
				mysql_query($insert, connect());
				}
			
			$insert = "
				INSERT INTO `documents` (
				`id` ,
				`description` ,
				`date` ,
				`company`
				)
				VALUES (
				'" . $path . "', 
				'" . $_POST['description'] . "', 
				CURDATE(),
				'0'
				);
				";
			mysql_query($insert, connect());
			$status = 0;
			}
			
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
		}
	}
	
if(isset($_POST['upload_again'])){
	if (isset($_FILES['file'])){
		if ($_FILES['file']['error'] == UPLOAD_ERR_OK){
			$filename = $_FILES['file']['name'];
			$ext = $_FILES['file']['ext'];
			$path = $_POST['dir1'].'/'.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'], $_POST['dir1'].'/'.$filename);
			
			for($x=0;$x<$_POST['count'];$x++){
				$insert = "
					INSERT INTO `documents` (
					`id` ,
					`description` ,
					`date` ,
					`company`
					)
					VALUES (
					'" . $path . "', 
					'" . $_POST['description'] . "', 
					CURDATE(),
					'" . $_POST['cb' . $x] . "'
					);
					";
				mysql_query($insert, connect());
				}
			
			$insert = "
				INSERT INTO `documents` (
				`id` ,
				`description` ,
				`date` ,
				`company`
				)
				VALUES (
				'" . $path . "', 
				'" . $_POST['description'] . "', 
				CURDATE(),
				'0'
				);
				";
			mysql_query($insert, connect());
			
			$status = 1;
			}
			
		elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			$result_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		else 
			$result_msg = 'Unknown error';
		}
	}
	


function dirList ($directory){
	$results = array();
	$handler = opendir($directory);

	while ($file = readdir($handler)) {
        if ($file != '.' && $file != '..'){
		$results[] = $file;
		}
	}
	closedir($handler);
	return $results;
	}
	
function getdata($id){
	$select = "select * from documents where `id`= '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
	
function getaccess($id){
	$select = "select * from documents where `id`= '" . $id . "'";
	$result = mysql_query($select, connect());
	$access = "";
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$access = $access . $row['company'] . "@@";
		}
	return $access;
	}
	
	
function outs($path,$spath){
	$dpath = explode("/", $spath);
	
	$dir = dirList ($path);
	$count = count($dir);
	echo  '<ul class="php-file-tree">';
	$b = 0;
	for($x=0;$x<$count;$x++){
		if(is_dir($path.$dir[$x])){
			
			?>
			<li class="pft-directory">
				<table width=100% border=0>
					<tr>
						<td>
							<a href="myupload.php?path=<?php echo $path.$dir[$x]; ?>/"><?php echo $dir[$x]; ?></a>
						</td>
						<td width=520px align="right">
							<form method="POST">
								<input type="hidden" name="id" value="<?php echo $path.$dir[$x]; ?>">
								<input type="button" value="c" title="create directory on this directory" onclick="showcreate('<?php echo $path.$dir[$x]; ?>',this);">
								<input type="button" value="u" title="upload file on this directory" onclick="upload('<?php echo $path.$dir[$x]; ?>',this);" id="<?php echo $path.$dir[$x]; ?>">
								<input type="button" value="e" title="edit directory" disabled="true">
								<input type="button" value="x" title="delete directory" name="delete" onclick="this.form.submit()">
							</form>
						</td>
					</tr>
				</table>
			</li>
			<?php
			if($dpath[1] == $dir[$x]){
				outs($path.$dir[$x]."/",$spath);
				}
			if($dpath[2] == $dir[$x]){
				outs($path.$dir[$x]."/",$spath);
				}
			if($dpath[3] == $dir[$x]){
				outs($path.$dir[$x]."/",$spath);
				}
			if($dpath[4] == $dir[$x]){
				outs($path.$dir[$x]."/",$spath);
				}
			if($dpath[5] == $dir[$x]){
				outs($path.$dir[$x]."/",$spath);
				}
			}
		else{
			$ext = "ext-" . substr($dir[$x], strrpos($dir[$x], ".") + 1);
			$data = getdata($path.$dir[$x]);
			$access = getaccess($path.$dir[$x]);
			?>
			<li class="<?php echo $ext; ?>">
				<table width=100% border=0>
					<tr>
						<td><a href="#" onclick="openpop('<?php echo $path.$dir[$x]; ?>', screen.width,screen.height);"><?php echo $dir[$x]; ?></a></td>
						<td width=260px><?php echo $data['description']; ?></td>
						<td width=120px align="center"><?php echo $data['date']; ?></td>
						<td width=140px align="right">
							<form method="POST">
								<input type="hidden" name="id" value="<?php echo $path.$dir[$x]; ?>">
								<input type="button" value="e" title="edit description / priv. this file" onclick="edit('<?php echo $path.$dir[$x]; ?>',this,'<?php echo $data['description']; ?>','<?php echo $access; ?>');">
								<input type="button" value="x" title="delete directory" name="delete" onclick="this.form.submit()">
							</form>
						</td>
					</tr>
				</table>
			</li>
			<?php
			}
		}
	echo  '</ul>';
	}
	
if($_GET['path']){
	$path = $_GET['path'];
	}

?>
<ul class="php-file-tree">
	<li class="pft-directory">
		<table width=100% border=0>
			<tr>
				<td>
					<a href="myupload.php?path=./upload">...</a>
				</td>
				<td width=260px>Description</td>
				<td width=120px align="center">Date Upload</td>
				<td width=140px align="right">
					<input type="button" value="c" name="delete" title="create directory" onclick="showcreate('./upload', this);">
					<input type="button" value="u" name="delete" title="upload file" onclick="upload('./upload', this);" id="./upload">
					<input type="button" value="e" title="edit directory" disabled="true">
					<input type="button" value="x" name="delete" title="delete directory" onclick="this.form.submit()" disabled="true">
				</td>
			</tr>
		</table>
	</li>
</ul>
<?php
outs("./upload/", $path);
?>
<div class="createdir" id="cam">
	<form method="POST"  autocomplete="off">
	<input type="hidden" name="dir" id="dir">
	<table width=100% border=0>
		<tr>
			<td width="100%">Forder Name</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="newdir" style="width:250px;"></td>
		</tr>
		<tr>
			<td><input type="submit" value="create" name="create"></td>
		</tr>
		<tr>
			<td align="right"><input type="button" value="Close" onclick="document.getElementById('cam').style.visibility='hidden';"></td>
		</tr>
	</table>
	</form>
</div>
<div class="xstooltips " id="upload">
	<form method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="dir1" id="dir1">
	<table width=100% border=0>
		<tr>
			<td width="100%">Description</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="description" style="width:250px;"></td>
		</tr>
		<tr>
			<td>File Location</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="file" name="file"></td>
		</tr>
		<tr>
			<td>Company Access</td>
		</tr>
		<?php
		$result_data = $result_data = get_mycompany();
		$x = 0;
		while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
		?>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="cb<?php echo $x; ?>" value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></td>
		</tr>
		<?php
		$x++;
		}
		?>
		<tr>
			<td><input type="submit" value="upload and close" name="upload"> | <input type="submit" value="upload another" name="upload_again"></td>
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
<div class="xstooltips " id="edit">
	<form method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="dir2" id="dir2">
	<table width=100% border=0>
		<tr>
			<td width="100%">Description</td>
		</tr>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="edescription" id="edescription" style="width:250px;"></td>
		</tr>
		<tr>
			<td>Company Access</td>
		</tr>
		<?php
		$result_data = $result_data = get_mycompany();
		$x = 0;
		while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
		?>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ecb<?php echo $x; ?>" id="ecb<?php echo $x; ?>" value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></td>
		</tr>
		<?php
		$x++;
		}
		?>
		<tr>
			<td><input type="submit" value="update" name="update"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right"><input type="button" value="Close" onclick="document.getElementById('edit').style.visibility='hidden';"></td>
		</tr>
	</table>
	<input type="hidden" name="ecount" id="ecount" value="<?php echo $x; ?>">
	</form>
</div>
<script>
<?php
if($status == 1){
?>
	var parentId = document.getElementById('<?php echo $_POST['dir1'];?>');
	upload('<?php echo $_POST['dir1']; ?>',parentId);
<?php
}
?>
function showcreate(dir,parentId){
	var it = document.getElementById('cam');
	var upload = document.getElementById('upload');
	var edit = document.getElementById('edit');
    	var img = parentId; 
	var dirid = document.getElementById('dir'); 
	
	x = findPosX(img) - 300;
	y = findPosY(img);
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	it.style.visibility = 'visible'; 
	upload.style.visibility = 'hidden'; 
	edit.style.visibility = 'hidden'; 
	dirid.value=dir;
	}
function upload(dir,parentId){
	var it = document.getElementById('upload');
	var cam = document.getElementById('cam');
	var edit = document.getElementById('edit');
    	var img = parentId; 
	var dirid = document.getElementById('dir1'); 
    
	x = findPosX(img) - 300;
	y = findPosY(img);
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	it.style.visibility = 'visible'; 
	cam.style.visibility = 'hidden'; 
	edit.style.visibility = 'hidden'; 
	dirid.value=dir;
	}
function edit(dir,parentId,desc,access){
	var it = document.getElementById('edit');
	var cam = document.getElementById('cam');
	var upload = document.getElementById('upload');
	var edesc = document.getElementById('edescription');
	var countw = document.getElementById('ecount');
    	var img = parentId; 
	var dirid = document.getElementById('dir2'); 
    
	x = findPosX(img) - 300;
	y = findPosY(img);
        
	it.style.top = y + 'px';
	it.style.left = x + 'px';
	it.style.visibility = 'visible'; 
	upload.style.visibility = 'hidden'; 
	cam.style.visibility = 'hidden'; 
	dirid.value=dir;
	edesc.value=desc;
		
	myAccess = access.split("@@");
	
	c = myAccess.length
	
	for(x=0;x<parseFloat(countw.value);x++){
		var ecb = document.getElementById('ecb' + x);
		ecb.checked = false;
		}
	
	for(x=0;x<parseFloat(countw.value);x++){
		var ecb = document.getElementById('ecb' + x);
		for(y=0;y<c;y++){
			if (myAccess[y] == ecb.value){
				ecb.checked = true;
				}
			}
		}
	}
</script>

<style>
.xstooltips 
{
    visibility: hidden; 
    position: absolute; 
    top: 0px;  
    left: 0px; 
    z-index: 2; 
    width:300px;
    height:300px;

    font: normal 8pt sans-serif; 
    margin: 0px 0px 0px 0px;
    padding: 0 0 0 0;
    border: solid 1px black;
    background-color: white;
}

.createdir
{
    visibility: hidden; 
    position: absolute; 
    top: 0px;  
    left: 0px; 
    z-index: 2; 
    width:300px;
    height:150px;

    font: normal 8pt sans-serif; 
    margin: 0px 0px 0px 0px;
    padding: 0 0 0 0;
    border: solid 1px black;
    background-color: white;
}
</style>