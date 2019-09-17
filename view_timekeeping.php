<?php
require ('config.php');

function h2m($hours){
	$expl = explode(":", $hours); 
	return ($expl[0] * 60) + $expl[1];
	}
	
function GetShift($id){
	$select = "select `from`,`to` from shift where shift_code = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['from'],$row['to']);
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
	
function getname($fid,$cid){
	$select = "select name,em_id from employee where finger = '" . $fid . "' and `company_id` = '" . $cid . "'";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

$id = $_GET['id'];

$select = "select filename, status,company_id from upload_timekeeping where id = '" . $id . "'";
$result = mysql_query($select,connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$file = 'timecards/' . $id . $row['filename'];
$company = $row['company_id'];

if ($row['status'] == 'uploaded'){
	$fd = fopen($file, 'r');
	$x = 0;
	while (! feof($fd) ){
		$line = fgets($fd);
		$row = explode("\t", $line);
		$datetime = explode("  ", $row[6]);
		if($x != 0){
			$insert = "insert into uploaded_timecard values(null,'" . $file . "','" . $row[2] . "','" . $datetime[0] . "','" . $datetime[1] . "')";
			$insert = "insert into uploaded_timecard values(null,'" . $file . "','" . $row[2] . "','" . $datetime[0] . "','" . $datetime[1] . "')";
			mysql_query($insert, connect());
			}
		$x++;
		}
	fclose($fd);
	}
	
function getfl($date, $finger,$file){
	$select = "select count(*) as cnt from uploaded_timecard where finger = '" . $finger . "' and `date` = '" . $date . "' order by time asc";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	if ($row['cnt'] == 1){
		$insert = "insert into uploaded_timecard values(null,'" . $file . "','" . $finger . "','" . $date . "','00:00')";
		mysql_query($insert, connect());
		}

	$select = "select * from uploaded_timecard where finger = '" . $finger . "' and `date` = '" . $date . "' order by time asc";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$timein = $row['time'];
	$idin = $row['id'];
		
	$select = "select * from uploaded_timecard where finger = '" . $finger . "' and `date` = '" . $date . "' order by time desc";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$timeout = $row['time'];
	$idout = $row['id'];
		
	return array($timein, $timeout,$idin,$idout);
	}
	
if(isset($_POST['update'])){
	for ($x=0;$x<$_POST['count'];$x++){
		$timein = $_POST['in' . $x];
		$timeout = $_POST['out' . $x];
		$idin = $_POST['idin' . $x];
		$idout = $_POST['idout' . $x];
		
		$update = "update uploaded_timecard set 
			time  = '" . $timein . "'
			where id = '" . $idin . "'
			";
		mysql_query($update, connect());
		
		$update = "update uploaded_timecard set 
			time  = '" . $timeout . "'
			where id = '" . $idout . "'
			";
		mysql_query($update, connect());
		}
	}
	
if(isset($_POST['approved'])){
	$update = "update upload_timekeeping set status = 'imported' where id = '" . $id . "'";
	mysql_query($update,connect());

	for ($y=0;$y<$_POST['count'];$y++){
		$timein = $_POST['in' . $y];
		$timeout = $_POST['out' . $y];
		$idin = $_POST['idin' . $y];
		$idout = $_POST['idout' . $y];
		$emid = $_POST['em_id' . $y];
		$date = $_POST['date' . $y];
		
		$select = "select * from employee where em_id = '" . $emid . "'";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$from = $date;
			$to = $date;
			$difference = (strtotime($to)-strtotime($from));
			$days = $difference/24;
			$days = $days/60;
			$days = $days/60;
			$days = ceil($days);
			$shift = GetShift($row['em_id']);
			for($x=0;$x<=$days;$x++){
				$ya = explode("-",$from);
				$date =  date('Y-m-d',mktime(0, 0, 0, $ya[1], $ya[2], $ya[0]) + ($x * 24 * 60 * 60));
			
				$select = "select * from transaction where em_id = '" . $row['em_id'] . "' and trxn_date = '" . $date . "' limit 1";
				$result1 = mysql_query($select, connect());
				$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
		
				if (!$row1['em_id']){
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
						'" . $timein . "', 
						'" . $timeout . "', 
						'" . $row['em_id'] . "', 
						'" . $row['salary_based'] . "', 
						'" . $row['salary'] . "', 
						'0', 
						'', 
						'', 
						NOW( ),
						'REGULAR', 
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
				else{
					$update = "update transaction set trxn_time_out = '" . $timeout . "', trxn_time_in = '" . $timein . "' where em_id = '" . $row['em_id'] . "' and trxn_date = '" . $date . "'";
					mysql_query($update, connect());
					}
				}
			}
		}
	}
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Time Keeping In/Out</h3>
<form method="post">
<table width=100% border="1" cellpadding="4" cellspacing="0">
<tr>
	<td width="8%" align="center">Name</td>
	<td width="8%" align="center">Day</td>
	<td width="7%" align="center">Date</td>
	<td width="9%" align="center">Time In (Earliest)</td>
	<td width="9%" align="center">Time Out (Latest)</td>
	<td width="8%" align="center">Total</td>
</tr>
<?php
$select = "select * from uploaded_timecard where file = '" . $file . "' and finger != '' group by finger,date order by finger asc";
$result = mysql_query($select, connect());
$trig = "";

$x == 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$a = split("-" , $row['date']);
	$dayoftheweek = date("l", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
	$time = getfl($row['date'], $row['finger'],$file);
	$total = h2m($time[1])-h2m($time[0]);
	if($trig != $row['finger']){
	?>
	<tr>
		<td colspan=6 bgcolor="lightblue">&nbsp;</td>
	</tr>
	<?php	
		}
	$trig = $row['finger'];
	$info = getname($row['finger'],$company);
	?>
	<tr>
		<td><?php echo $info['name']; ?>&nbsp;</td>
		<td><?php echo $dayoftheweek; ?></td>
		<td><?php echo $row['date']; ?></td>
		<td><input type="text" name="in<?php echo $x; ?>" value="<?php echo  $time[0]; ?>"></td>
		<td><input type="text" name="out<?php echo $x; ?>" value="<?php echo  $time[1]; ?>"></td>
		<td><?php echo m2h($total); ?></td>
		<input type="hidden" name="idin<?php echo $x; ?>" value="<?php echo $time[2]; ?>">
		<input type="hidden" name="idout<?php echo $x; ?>" value="<?php echo $time[3]; ?>">
		<input type="hidden" name="em_id<?php echo $x; ?>" value="<?php echo $info['em_id']; ?>">
		<input type="hidden" name="date<?php echo $x; ?>" value="<?php echo $row['date']; ?>">
	</tr>
	<?php
	$x++;
	}
	?>

<input type="hidden" name="count" value="<?php echo $x; ?>">

<tr>
	<td colspan=6 align="right"><input type="submit" name="approved" value="approved" style="width:20%;"> || <input type="submit" name="update" value="update" style="width:20%;"></td>
</tr>
</table>
</form>