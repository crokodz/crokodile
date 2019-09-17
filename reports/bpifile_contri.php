<?php
require ('../config.php');
$select = "select ";

if(isset($_POST['gen'])){
	$id=$_POST['id'];
	$date=$_POST['new_day'];
	$ceiling = roundoffNoComma($_POST['ceiling'],2);
	}
else{
	$id=$_GET['idd'];
	$date=date('Y-m-d');
	$ceiling = '200000.00';
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Payroll System[BPI File Extration]</title>
<meta content="application/octet-stream">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=win 1252">
<script type="text/javascript" src="mootools.v1.11.js"></script> 
<script type="text/javascript" src="DatePicker.js"></script>
<script type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript"> 
window.addEvent('domready', function(){
	$$('input.DatePicker').each( function(el){
		new DatePicker(el);
		});
	});
function DownloadFile(sURL, sFilename){
	setTimeout("window.open('"+sURL+"','Download')",2000);  
	}
</script> 
<style type="text/css"> 
 
/* ---- calendar and input styles ---- */
 
input.DatePicker{
	display: block;
	width: 150px;
	padding: 3px 3px 3px 24px;
	border: 1px solid #0070bf;
	font-size: 13px;
	background: #fff url(date.gif) no-repeat top left;
	cursor: pointer;
}

input.IDPicker{
	display: block;
	width: 120px;
	padding: 3px 3px 3px 24px;
	border: 1px solid #0070bf;
	font-size: 13px;
	cursor: pointer;
}


input:focus.DatePicker{
	background: #fffce9 url(datefocus.gif) no-repeat top left;
}
.dp_container{
	position: relative;
	padding: 0;
	z-index: 500;
}
.dp_cal{
	background-color: #fff;
	border: 1px solid #0070bf;
	position: absolute;
	width: 177px;
	top: 24px;
	left: 0;
	margin: 0px 0px 3px 0px;
}
.dp_cal table{
	width: 100%;
	border-collapse: collapse;
	border-spacing: 0;
}
.dp_cal select{
	margin: 2px 3px;
	font-size: 11px;
}
.dp_cal select option{
	padding: 1px 3px;
}
.dp_cal th,
.dp_cal td{
	width: 14.2857%;
	text-align: center;
	font-size: 11px;
	padding: 2px 0;
}
.dp_cal th{
	border: solid #aad4f2;
	border-width: 1px 0;
	color: #797774;
	background: #daf2e6;
	font-weight: bold;
}
.dp_cal td{
	cursor: pointer;
}
.dp_cal thead th{
	background: #d9eefc;
}
.dp_cal td.dp_roll{
	color: #000;
	background: #fff6bf;
}
/* must have this for the IE6 select box hiding */
.dp_hide{
	visibility: hidden;
}
.dp_empty{
	background: #eee;
}
.dp_today{
	background: #daf2e6;
}
.dp_selected{
	color: #fff;
	background: #328dcf;
}
 
 
 
/* ---- just to pretty up this page ---- */
 
body{
	font-family: Tahoma, Geneva, sans-serif;
}
.yep{
	width: 450px;
	margin: 50px auto;
	text-align: center;
}
h1{
	margin: 20px 0;
	color: #60bf8f;
	font: normal 28px Georgia, serif;
}
h2{
	margin: 20px 0;
	color: #60bf8f;
	font: normal 22px Georgia, serif;
}
p{
	float: left;
	display: inline;
	width: 180px;
	margin: 20px;
	text-align: left;
}
label{
	color: #797774;
	display: block;
	font-size: 12px;
	font-weight: bold;
	margin: 8px 0 3px 0;
}
dl,dt,dd,ul,li{
	margin: 0;
	padding: 0;
	list-style: none;
}
ul{
	clear: both;
}
li{
	font-size: 10px;
}
li a{
	color: #004a7f;
	text-decoration: none;
}
li a:hover{
	color: #328dcf;
	border-bottom: 1px solid #328dcf;
}
dl{
	font-size: 12px;
	text-align: left;
}
dt, dd.default{
	font-family: monaco, "Bitstream Vera Sans Mono", "Courier New", courier, monospace;
	font-weight: bold;
}
dt{
	clear: left;
	float: left;
	width: 140px;
	padding: 5px;
	text-align: right;
}
dd{
	margin: 5px 0 30px 160px;
	padding: 5px;
}
.default{
	margin: 0 0 0 160px;
	background: #eee;
}
p.note{
	background: #ffd;
	border: 1px solid #dd6;
	display: block;
	float: none;
	font-size: 12px;
	line-height: 1.8;
	padding: 10px;
	width: auto;
}
code{
	background: #eee;
	border: 1px solid #ccc;
	padding: 0 5px;
}
</style> 
 <?php
 function cspace($char,$num){
	$s = '';
	for($x=0;$x<$num;$x++){
		$s=$s.$char;
		}
	return $s;
	}
	
function addz($char,$num){
	$c = strlen($char);
	if($c == 0){
		return '000000000000';
		}
	$a = $num - $c;
	$s = '';
	for($x=0;$x<$a;$x++){
		$s = $s.'0';
		}
	return $s.$char;
	}
 ?>


<form method="POST" autocomplete="off">
<table>
<tr>
	<td>
		Payroll Date : 
	</td>
	<td>
		<input id="new_day" name="new_day" type="text" class="DatePicker" alt="{
			dayChars:3,
			dayNames:['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			daysInMonth:[31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
			format:'yyyy-mm-dd',
			monthNames:['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			startDay:1,
			yearOrder:'desc',
			yearRange:90,
			yearStart:2010
		}" tabindex="1" value="<?php echo $date; ?>">
	</td>
</tr>
<tr>
	<td>
		Payroll ID : 
	</td>
	<td>	
		<select name="id" id="id" class="IDPicker" style="width:250px;">
			<?php
			if ($_SESSION['company'] == '0'){
				$select = "SELECT * FROM posted_summary GROUP BY posted_id order by posted_id desc";
				}
			else{
				$select = "SELECT * FROM posted_summary where company_id = '" . $_SESSION['company'] . "' GROUP BY posted_id order by posted_id desc";
				}
			$result = mysql_query($select, connect());
			while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $row['posted_id']; ?>"><?php echo $row['pay_id'] . " - (" . $row['from'] . " to " . $row['to'] . ")"; ?></option>
			<?php
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>
		Ceiling :
	</td>
	<td>	
		 <input type="text" name="ceiling" id="ceiling" value="<?php echo $ceiling; ?>" class="IDPicker">
	</td>
</tr>
</table>
<br>
<input type="submit" name="gen" id="gen" value="Download File"> | <input type="button" value="Export PDF" onclick="onBPIpdf()"><br><br>
</form>
<?php
$selecty = "select `company_id` from posted_summary where `posted_id` = '" . $id . "' limit 1";
$resulty = mysql_query($selecty, connect());
$rowy = mysql_fetch_array($resulty,MYSQL_ASSOC);

$selectx = "select `company_code`,`bank_account` from company where `id` = '" . $rowy['company_id'] . "'";
$resultx = mysql_query($selectx, connect());
$rowx = mysql_fetch_array($resultx,MYSQL_ASSOC);

$date = $date[5].$date[6].$date[8].$date[9].$date[2].$date[3];
$companycode = $rowx['company_code'];
$batch = '01';
$r5 = '1';
$companyaccount = $rowx['bank_account'];
$pobc = '339'; #presenting office or branch code
$ceiling = addz(str_replace('.','',$ceiling),12);
$rh10 = '1';
$rh11 = cspace(' ',75);

##
$totalAN = 0;
$totalTA = 0;
$x = 0;
$select = "select `netpay`,`bank_account` from posted_summary left join employee using(`em_id`) where posted_summary.`posted_id` = '" . $id . "' and `bank_account` != '' ";
$result = mysql_query($select, connect());
$d = '';
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$end = "\n";
	$aax = str_replace(' ','',$row['bank_account']);
	$account = addz(str_replace('-','',$aax),10);
	$r5 = '3';
	$netpay = addz(str_replace('.','',roundoffNoComma($row['netpay'],2)),12);
	$a1 = $account[4].$account[5];
	$a2 = $account[6].$account[7];
	$a3 = $account[8].$account[9];
	$hash = ($a1 * $netpay) + ($a2 * $netpay) + ($a3 * $netpay);
	$hash = addz(str_replace('.','',$hash),12);
	$r11 = cspace(' ',79);
	$d = $d.'D'.$companycode.$date.$batch.$r5.$account.$netpay.$hash.$r11.$end;
	$totalAN = $totalAN + $account;
	$totalTA = $totalTA + $row['netpay'];
	$x++;
	}

##
$end = "\n";
$r5 = '2';
$totalAN = addz($totalAN,15);
$totalTA1 = addz(str_replace('.','',roundoffNoComma($totalTA,2)),18);
$totalTA2 = addz(str_replace('.','',roundoffNoComma($totalTA,2)),12);
$x = addz($x,5);
$r11 = cspace(' ',50);
$t = 'T'.$companycode.$date.$batch.$r5.$companyaccount.$totalAN.$totalTA1.$x.$r11.$end;
$h = 'H'.$companycode.$date.$batch.$r5.$companyaccount.$pobc.$ceiling.$totalTA2.$rh10.$rh11.$end;

$Data = $h.$d.$t;

$File = $companycode.$date.$batch.$id.".txt"; 
$Handle = fopen($File, 'w');
fwrite($Handle, $Data); 
fclose($Handle); 
if(isset($_POST['gen'])){
?>
<script>
self.location="bpifile_contri_dl.php?file=<?php echo $File; ?>";
</script>
<?php
}
?>