<?php
function add_personded( $emid, $type, $amount, $gives,$date ){
	if($emid){
		if($gives > 0 and $amount > 0){
			$amountz = $amount/$gives;
			$sql = '';
			for($x=0;$x<$gives;$x++){
				$sql = $sql . "insert into employee_deduction (
					`em_id`,
					`name`,
					`amount`,
					`balance`,
					`status`,
					`username`,
					`date`,
					`terms`,
					`datetime`) values (
					'" . $emid . "',
					'" . $type . "',
					'" . $amountz . "',
					'0',
					'pending',
					'" . $_SESSION['user'] . "',
					curdate(),
					'" . $gives . "',
					now()
					); ";
				}
			}
		$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
			"ded", 
			"' . getName($emid) . '", 
			"' . $emid . '", 
			"' . $date . '", 
			"' . $amount . '", 
			"' . $gives . '", 
			"' . $type . '", 
			"' . $sql . '", 
			NOW(), 
			"' . $_SESSION['user'] . '", 
			"0"
			);';
		mysql_query($insert, connect());
		}
	}
	
function add_personsal( $emid, $amount, $date ){
	if($emid){
		$sql = $sql . "update employee set  salary = '" . $amount . "' where em_id = '" . $emid . "'; ";
				
		$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
			"sal", 
			"' . getName($emid) . '", 
			"' . $emid . '", 
			"' . $date . '", 
			"' . $amount . '", 
			"", 
			"", 
			"' . $sql . '", 
			NOW(), 
			"' . $_SESSION['user'] . '", 
			"0"
			);';
		mysql_query($insert, connect());
		}
	}
	
function add_personpal( $emid, $amount, $payday,$date ){
	if($emid){
		if($payday > 0 and $amount > 0){
			$amountz = $amount/$payday;
			$sql = '';
			for($x=0;$x<$payday;$x++){
				$sql = $sql . "insert into employee_deduction (
					`em_id`,
					`name`,
					`amount`,
					`balance`,
					`status`,
					`username`,
					`date`,
					`terms`,
					`datetime`) values (
					'" . $emid . "',
					'PALUWAGAN CONT',
					'" . $amountz . "',
					'0',
					'pending',
					'" . $_SESSION['user'] . "',
					curdate(),
					'" . $payday . "',
					now()
					); ";
				}
			}
		$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
			"pal", 
			"' . getName($emid) . '", 
			"' . $emid . '", 
			"' . $date . '", 
			"' . $amount . '", 
			"' . $payday . '", 
			"", 
			"' . $sql . '", 
			NOW(), 
			"' . $_SESSION['user'] . '", 
			"0"
			);';
		mysql_query($insert, connect());
		}
	}

function getName($id){
	$select= "select `name` from employee where `em_id` = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}
if(isset($_POST['clearded'])){
	$update = "update cronjob set `status` = '1' where username = '" . $_SESSION['user'] . "' and type='ded' and `status` = '0' ";
	mysql_query($update, connect());
	$deduction = 1;
	}
	
if(isset($_POST['clearpal'])){
	$update = "update cronjob set `status` = '1' where username = '" . $_SESSION['user'] . "' and type='pal' and `status` = '0' ";
	mysql_query($update, connect());
	$palwagan = 1;
	}
	
if(isset($_POST['clearsal'])){
	$update = "update cronjob set `status` = '1' where username = '" . $_SESSION['user'] . "' and type='sal'  and `status` = '0'";
	mysql_query($update, connect());
	$salary = 1;
	}

if(isset($_POST['postded'])){
	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['cb' . $x]){
			$update = "update cronjob set `status` = '2' where id = '" . $_POST['cb' . $x] . "' ";
			mysql_query($update, connect());
			}
		}
	$deduction = 1;
	}
	
if(isset($_POST['delded'])){
	$update = "update cronjob set `status` = '1' where id = '" . $_POST['id'] . "' ";
	mysql_query($update, connect());
	$deduction = 1;
	}

if(isset($_POST['upsded'])){
if ( $_FILES['file']['tmp_name'] ){
	$update = "update cronjob set `status` = '1' where username = '" . $_SESSION['user'] . "' and type='ded' ";
	mysql_query($update, connect());
	
	$dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
	$rows = $dom->getElementsByTagName( 'Row' );
	$first_row = true;
	foreach ($rows as $row){
		if ( !$first_row ){
			$emid = "";
			$type = "";
			$amount = "";
			$gives = "";
			$date = "";
  
			$index = 1;
			$cells = $row->getElementsByTagName( 'Cell' );
			foreach( $cells as $cell ){ 
				$ind = $cell->getAttribute( 'Index' );
				if ( $ind != null ) $index = $ind;
  				if ( $index == 1 ) $emid = $cell->nodeValue;
				if ( $index == 2 ) $type = $cell->nodeValue;
				if ( $index == 3 ) $amount = $cell->nodeValue;
				if ( $index == 4 ) $gives = $cell->nodeValue;
				if ( $index == 5 ) $date = $cell->nodeValue;
  				$index += 1;
				}
			add_personded( $emid, $type, $amount, $gives,$date );
			}
		$first_row = false;
		}
	$deduction = 1;
	}
	}
	
if(isset($_POST['upssal'])){
if ( $_FILES['file']['tmp_name'] ){
	$dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
	$rows = $dom->getElementsByTagName( 'Row' );
	$first_row = true;
	foreach ($rows as $row){
		if ( !$first_row ){
			$emid = "";
			$salarynew = "";
			$date = "";
  
			$index = 1;
			$cells = $row->getElementsByTagName( 'Cell' );
			foreach( $cells as $cell ){ 
				$ind = $cell->getAttribute( 'Index' );
				if ( $ind != null ) $index = $ind;
  				if ( $index == 1 ) $emid = $cell->nodeValue;
				if ( $index == 2 ) $salarynew = $cell->nodeValue;
				if ( $index == 3 ) $date = $cell->nodeValue;
  				$index += 1;
				}
			add_personsal( $emid, $salarynew,$date );
			}
		$first_row = false;
		}
	$salary = 1;
	}
	}
	
if(isset($_POST['delsal'])){
	$update = "update cronjob set `status` = '1' where id = '" . $_POST['id'] . "' ";
	mysql_query($update, connect());
	$salary = 1;
	}
	
if(isset($_POST['postsal'])){
	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['cb' . $x]){
			$update = "update cronjob set `status` = '2' where id = '" . $_POST['cb' . $x] . "' ";
			mysql_query($update, connect());
			}
		}
	$salary = 1;
	}
	
if(isset($_POST['upspal'])){
if ( $_FILES['file']['tmp_name'] ){
	$dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
	$rows = $dom->getElementsByTagName( 'Row' );
	$first_row = true;
	foreach ($rows as $row){
		if ( !$first_row ){
			$emid = "";
			$type = "";
			$amount = "";
			$payday = "";
			$date = "";
  
			$index = 1;
			$cells = $row->getElementsByTagName( 'Cell' );
			foreach( $cells as $cell ){ 
				$ind = $cell->getAttribute( 'Index' );
				if ( $ind != null ) $index = $ind;
  				if ( $index == 1 ) $emid = $cell->nodeValue;
				if ( $index == 2 ) $amount = $cell->nodeValue;
				if ( $index == 3 ) $payday = $cell->nodeValue;
				if ( $index == 4 ) $date = $cell->nodeValue;
  				$index += 1;
				}
			add_personpal( $emid, $amount, $payday,$date );
			}
		$first_row = false;
		}
	$palwagan = 1;
	}
	}

if(isset($_POST['postpal'])){
	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['cb' . $x]){
			$update = "update cronjob set `status` = '2' where id = '" . $_POST['cb' . $x] . "' ";
			mysql_query($update, connect());
			}
		}
	$palwagan = 1;
	}
	
if(isset($_POST['delpal'])){
	$update = "update cronjob set `status` = '1' where id = '" . $_POST['id'] . "' ";
	mysql_query($update, connect());
	$palwagan = 1;
	}

########################################################################
if(isset($_POST['saveded'])){
	$gives = $_POST['payday'];
	$amount = $_POST['amount'];
	$emid = $_POST['emid'];
	$type = $_POST['type'];
	$date = $_POST['datetime'];
	
	$amountz = $amount/$gives;
	$sql = '';
	for($x=0;$x<$gives;$x++){
		$sql = $sql . "insert into employee_deduction (
			`em_id`,
			`name`,
			`amount`,
			`balance`,
			`status`,
			`username`,
			`date`,
			`terms`,
			`datetime`) values (
			'" . $emid . "',
			'" . $type . "',
			'" . $amountz . "',
			'0',
			'pending',
			'" . $_SESSION['user'] . "',
			curdate(),
			'" . $gives . "',
			now()
			); ";
			}
					
	$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
		"ded", 
		"' . getName($emid) . '", 
		"' . $emid . '", 
		"' . $date . '", 
		"' . $amount . '", 
		"' . $gives . '", 
		"' . $type . '", 
		"' . $sql . '", 
		NOW(), 
		"' . $_SESSION['user'] . '", 
		"0"
		);';
	mysql_query($insert, connect());
	$deduction = 1;
	}


if(isset($_POST['savepal'])){
	$gives = $_POST['payday'];
	$amount = $_POST['amount'];
	$emid = $_POST['emid'];
	$type = $_POST['type'];
	$date = $_POST['datetime'];
	$payday = $_POST['payday'];
	
	$amountz = $amount/$payday;
	$sql = '';
	for($x=0;$x<$payday;$x++){
		$sql = $sql . "insert into employee_deduction (
			`em_id`,
			`name`,
			`amount`,
			`balance`,
			`status`,
			`username`,
			`date`,
			`terms`,
			`datetime`) values (
			'" . $emid . "',
			'PALUWAGAN CONT',
			'" . $amountz . "',
			'0',
			'pending',
			'" . $_SESSION['user'] . "',
			curdate(),
			'" . $payday . "',
			now()
			); ";
		}
					
	$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
		"pal", 
		"' . getName($emid) . '", 
		"' . $emid . '", 
		"' . $date . '", 
		"' . $amount . '", 
		"' . $payday . '", 
		"", 
		"' . $sql . '", 
		NOW(), 
		"' . $_SESSION['user'] . '", 
		"0"
		);';
	mysql_query($insert, connect());
	$palwagan = 1;
	}
	
if(isset($_POST['savesal'])){
	$gives = $_POST['payday'];
	$amount = $_POST['newsalary'];
	$emid = $_POST['emid'];
	$type = $_POST['type'];
	$date = $_POST['datetime'];
	$payday = $_POST['payday'];
	
	$sql = $sql . "update employee set  salary = '" . $amount . "' where em_id = '" . $emid . "'; ";
				
	$insert = 'INSERT INTO `payroll`.`cronjob` (`type` ,`name` ,`emid` ,`date` ,`amount` ,`gives` ,`dedtype` ,`sql` ,`datetime` ,`username` ,`status`) VALUES (
		"sal", 
		"' . getName($emid) . '", 
		"' . $emid . '", 
		"' . $date . '", 
		"' . $amount . '", 
		"", 
		"", 
		"' . $sql . '", 
		NOW(), 
		"' . $_SESSION['user'] . '", 
		"0"
		);';
	mysql_query($insert, connect());
	$salary = 1;
	}
  ?>
  <style>
  #search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<h3 class="wintitle">Cron Job</h3>
<form method="post" enctype="multipart/form-data">
<br>
<br>
<div class="tabmain">
<div class="tab" id="tab0" onclick="clTab(this,3,'cron_salary.php');">Salary Increase</div>
<div class="tab" id="tab1" onclick="clTab(this,3,'cron_palwagan.php');">Palwagan Cont.</div>
<div class="tab" id="tab2" onclick="clTab(this,3,'cron_deduction.php');">Deduction</div>
</div>
<br>
<div class="salary">Name / ID#</div>
	<div class="searchinline">
	<input type="text" name="name" id="name"> <input type="text" name="emid" id="emid" style="width:60px" readonly>
	<div id="hint"></div>
	<script type="text/javascript">
		new Ajax.Autocompleter("name","hint","server_cron.php");
		new Ajax.Autocompleter("name","hint","server_cron.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			$('name').value=myData[1];
			$('emid').value=myData[0];
			}
	</script>
</div>
<br>
<div id="contentsched">
</div>
</form>
<br>
<br>
<?php
if($deduction == 1){
	?>
	<script>
	var ded = document.getElementById('tab2');
	clTab(ded,3,'cron_deduction.php');
	</script>
	<?php
	}
	
if($salary == 1){
	?>
	<script>
	var sal = document.getElementById('tab0');
	clTab(sal,3,'cron_salary.php');
	</script>
	<?php
	}
	
if($palwagan == 1){
	?>
	<script>
	var pal = document.getElementById('tab1');
	clTab(pal,3,'cron_palwagan.php');
	</script>
	<?php
	}
?>