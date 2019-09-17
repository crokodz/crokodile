<?php
require ('config.php');

function GetInfo($id){
	$select = "select `name`,`ts`,`department`,`company_id`, `salary_based`, `salary`, `em_id` from employee where finger = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['name'],$row['ts'],$row['em_id'],$row['company_id'],$row['salary_based'],$row['salary']);
	}

if(isset($_POST['post'])){
	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['cb' . $x]){
			$select = "  select trxn_time_in from transaction where em_id = '" . $_POST['emid' . $x] . "' and trxn_date = '" . $_POST['date' . $x] . "' ";		
			$result = mysql_query($select, connect());
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			
			if($row['trxn_time_in'] == '00:00:00'){
				$update = " update transaction set trxn_time_in = '" . $_POST['time' . $x] . "', viewer = 0 where em_id = '" . $_POST['emid' . $x] . "' and trxn_date = '" . $_POST['date' . $x] . "' ";
				mysql_query($update, connect());
				}
			else{
				$update = " update transaction set trxn_time_out = '" . $_POST['time' . $x] . "', viewer = 0 where em_id = '" . $_POST['emid' . $x] . "' and trxn_date = '" . $_POST['date' . $x] . "' ";
				mysql_query($update, connect());
				}
			}
		}
	}

$myFile = "timecards/" . $_GET['filename'];
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
$a = explode("\n",$theData);
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<form method="post">
<body id="innerframe">
<table border=1>
<?php
for($x=0;$x<=count($a);$x++){
	$b = explode("\t", $a[$x]);
	$c = explode(" ", $b[6]);
	if($b[0] > 0 and $b[1] > 0){
		$info = GetInfo($b[2]);
		if($info[0]){
			$name = $info[0];
			$check = '<input type="checkbox" name="cb' . $x . '" value="1" checked>';
			$bg='';
			}
		else{
			$check = "&nbsp;";
			$name = "&nbsp;";
			$bg='bgcolor=#c6c6c6';
			}
		?>
		<tr <?php echo $bg; ?>>
			<td><?php echo $name; ?></td>
			<td><?php echo $b[2]; ?></td>
			<td><?php echo str_replace("/","-",$c[0]); ?></td>
			<td><?php echo $c[2]; ?></td>
			<td><?php echo $check; ?></td>
		</tr>
		<input type="hidden" name="emid<?php echo $x; ?>" value="<?php echo $info[2]; ?>">
		<input type="hidden" name="date<?php echo $x; ?>" value="<?php echo str_replace("/","-",$c[0]); ?>">
		<input type="hidden" name="time<?php echo $x; ?>" value="<?php echo $c[2]; ?>">
		<?php
		}
	}
?>
</table>
<input type="hidden" name="count" value="<?php echo $x; ?>">
<input type="submit" name="post" value="post" style="margin-top:10px;">
</form>
</body>