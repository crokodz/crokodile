<?php
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
			move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.'/'.$_SESSION['user'] . ".png");
			
			
			$image_p = imagecreatetruecolor(320, 240);
			$image = imagecreatefromjpeg($upload_dir.'/'.$_SESSION['user'] . ".png");
			list($width, $height) = getimagesize($upload_dir.'/'.$_SESSION['user'] . ".png");
			if($height > 240 or $width > 320){
				if($height>= $width){
					$a = $height-240;
					$b = $a/$height;
					$c=$width*$b;
					$w = $width - $c;
					$h = 240;
					}
				else{
					$a = $width-320;
					$b = $a/$width;
					$c=$height*$b;
					$h = $height - $c;
					$w = 320;
					}
				}
			else{
				$w = $width;
				$h = $height;
				}
			
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $width, $height);
			imagepng($image_p,$upload_dir.'/'.$_SESSION['user'] . ".png", 0,null);
			
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
	
	echo 'parDoc.getElementById("pix").src = "imgs.php?path=pix/'.$_SESSION['user'] . '.png&w=200&h=160&' . $random . '";';
	
	echo "\n".'</script></body></html>';
	}
	

$dd = '';
for ($x=1; $x < 32; $x++){
	$dd = $dd . "<option>" . $x . "</option>";
	}
	
$yy = '';
for ($x=1940; $x < 2008; $x++){
	$yy = $yy . "<option>" . $x . "</option>";
	}

$mm = '';
for ($x=1; $x < 13; $x++){
	$mm = $mm . "<option>" . $x . "</option>";
	}

$gender = "<option>M</option><option>F</option>";
$civil = "<option>SINGLE</option><option>MARRIED</option><option>WIDOWED</option><option>COMPLICATED</option>";

$select = "select * from employee where em_id = '" . $_SESSION['user'] . "';";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$tf = "pix/" . $_SESSION['user'] . ".png";
$f = @fopen($tf, "r");
if ($f == false){
	$pic = "pix/no_photo.jpg";
	$pic = "imgs.php?path=" . $pic."&w=200&h=160";
	}
else{
	$pic = "imgs.php?path=" . $tf."&w=200&h=160";
	}
?>
<style>
div.fileinputs {
	position: relative;
	cursor: pointer; 
	cursor: hand;
}

div.fakefile {
	position: absolute;
	top: 0px;
	right: 10px;
	z-index: 1;
	cursor: pointer; 
	cursor: hand;
	}
div.fakefile1 {
	position: absolute;
	top: 0px;
	left: 10px;
	z-index: 3;
	cursor: pointer; 
	cursor: hand;
	}
</style>
<form method="post" enctype="multipart/form-data">
<h3 class="wintitle"><b><?php echo getword("Employee Data Entry"); ?></b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td  width=18% align="right"><?php echo getword("English Name"); ?></td>
		<td class="bview"><?php echo $row['name']; ?></td>
		<td width="220px" rowspan=7 align="center">
		<form target="upload_iframe" method="post" enctype="multipart/form-data">
		<img src="pix/spacer.gif" height="160px" width="1px;"><img name="pix" id="pix" src="<?php echo $pic; ?>" style="margin-bottom:5px;">
		<input type="hidden" name="fileframe" value="true"><input type="hidden" name="file_name" id="file_name" value="<?php echo $_GET['id']; ?>">
		<div class="fileinputs">
			<input type="file" name="file" id="file" onChange="jsUpload(this)" class="zfile">
			<div class="fakefile">
				<img src="search.gif">
			</div>
			<div class="fakefile1" onclick="view('webcam',this)">
				<img src="webcam.gif">
			</div>
		</div>
		<iframe name="upload_iframe" style="width: 10px; height: 100px; display: none;"></iframe>
		</form>
		</td>
	</tr>
<input type="hidden" name="old_id" value="<?php echo $row['em_id']; ?>">
<input type="hidden" name="id">
	<tr>
		<td align="right"><?php echo getword("Chinese Name"); ?></td>
		<td class="bview"><?php echo $row['ename']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Staff Code"); ?></td>
		<td class="bview"><?php echo $row['em_id']; ?></td>
	</tr>
	<tr>
                <td align="right"><?php echo getword("Position"); ?></td>
                <td class="bview"><?php echo $row['position']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Department"); ?></td>
		<td class="bview"><?php echo $row['department']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("e-Mail"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['email']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Finger Print Number"); ?></td>
		<td class="bview"><?php echo $row['em_id']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Res. Address"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['em_address']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Postal Code"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['zipcode']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Tel. Number"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['em_number']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Date of Birth"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['birthdate']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Gender"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['gender']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Civil Status"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['civil_status']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Citizenship"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['citizenship']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Ethnic Race"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['race']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Height"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['height']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Weight"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['weight']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Emergency Contact Person"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['contact_person']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Em. Number"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['cp_number']; ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo getword("Job Description"); ?></td>
		<td colspan=2 class="bview"><?php echo $row['description']; ?></td>
	</tr>
</table>
<div class="xstooltip " id="webcam">
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
	
function view(id,parentId){
	var it = document.getElementById(id);
	var pix = document.getElementById("pix");
    	var img = parentId; 

	x = findPosX(img)-320;
	y = findPosY(img)-120;
	
	if(it.style.visibility == 'visible'){
		var randomnumber=Math.floor(Math.random()*11)
		it.style.visibility = 'hidden'; 
		pix.src="imgs.php?path=pix/<?php echo $_SESSION['user']; ?>.png&w=200&h=160&"+randomnumber;
		}
	else{
		it.style.top = y + 'px';
		it.style.left = x + 'px';
		it.style.visibility = 'visible';
		it.innerHTML = '<iframe width="260px" height="225x" src="cam.html" style="border:1px solid #FFF;"></iframe>';
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
</style>