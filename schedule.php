<?php
function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$difference = $end_ts - $start_ts;
	return round($difference / 86400);
	}
	
function dDays($date,$days){
	$newdate = strtotime ( $days.' day' , strtotime ( $date ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );
	return $newdate;
	}
	
if(isset($_POST['save'])){
	$delete = "delete from employee_schedule where `em_id` = '" . $_POST['em_id'] . "' and `date` = '" . $_POST['date'] . "' ";
	mysql_query($delete, connect());
	
	$a = explode(" ", $_POST['day_type']);
	if($a[1] == 'LEAVE'){
		$status = 'FOR APPROVAL';
		}
	else{
		$status = "";
		}
	
	$insert = "insert into employee_schedule (`date`,`em_id`,`day_type`,`shift_code`, `status`) values ('" . $_POST['date'] . "','" . $_POST['em_id'] . "','" . $_POST['day_type'] . "','" . $_POST['shift_code'] . "', '" . $status . "')";
	mysql_query($insert, connect());
	$update = "update `transaction` set `shift_code` = '" . $_POST['shift_code'] . "', `status` = '" . $_POST['day_type'] . "' where `em_id` = '" . $_POST['em_id'] . "' and `trxn_date` = '" . $_POST['date'] . "' and `posted_id` = 0";
	mysql_query($update, connect());
	}
	
if(isset($_POST['copyday'])){
	$count = dateDiff($_POST['date1x'], $_POST['date2x']);
	for($x=0;$x<=$count;$x++){
		$date = dDays($_POST['date1x'],$x);
		$delete = "delete from employee_schedule where `em_id` = '" . $_SESSION['em_idz'] . "' and `date` = '" . $date . "' ";
		mysql_query($delete, connect());
	
		$insert = "insert into employee_schedule select '" .  $_SESSION['em_idz'] . "', '" . $date . "', `shift_code`, `day_type`, `status`, `approvedby`, `approveddate`, `time` from employee_schedule where `date` = '" . $_POST['dateox'] . "' and em_id = '" .  $_SESSION['em_idz'] . "' ";
		mysql_query($insert, connect());
		}
		
	}
	
if(isset($_POST['copy'])){
	for($x=0;$x<$_POST['count'];$x++){
		if($_POST['cb'.$x]){
			$delete = "delete from employee_schedule where `em_id` = '" . $_POST['cb'.$x] . "' and `date` between '" . $_POST['date1'] . "' and '" . $_POST['date2'] . "' ";
			mysql_query($delete, connect());
			
			$insert = "insert into employee_schedule select '" . $_POST['cb'.$x] . "', `date`, `shift_code`, `day_type`, null, null, null, null from employee_schedule where `date` between '" . $_POST['date1'] . "' and '" . $_POST['date2'] . "' and em_id = '" . $_SESSION['em_idz'] . "' ";
			mysql_query($insert, connect());
			
			//~ $update = "update `transaction` set `shift_code` = '" . $_POST['shift_code'] . "', `status` = '" . $_POST['day_type'] . "' where `em_id` = '" . $_POST['em_id'] . "' and `trxn_date` = '" . $_POST['date'] . "' and `posted_id` = 0";
			//~ mysql_query($update, connect());
			}
		}
	}
	
if($_POST['keyword']){
	$_SESSION['keywordz'] = $_POST['keyword'];
	$_SESSION['em_idz'] = $_POST['em_idz'];
	}
?>

<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<form method="post" name="myform">
<div id="edit"></div>
<div id="copyto">
	<div style="margin-bottom:4px;"> <b>Date</b>&nbsp;&nbsp;&nbsp;From <input type="text" name="date1" id="date1" value="" maxlength="10" style="width:90px;"> | To <input type="text" name="date2" id="date2" value=""  maxlength="10" style="width:90px;"></div>
	<div style="margin-bottom:4px;">
	<select name="paycode" id="paycode" onchange="puthtml('server_copy.php?paycode='+this.value)">
		<?php
		$select = "select `name` from `pay` where name like '" . $_SESSION['pay_id'] . "%' group by `name` ";
		$result_data = mysql_query($select, connect());
		while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
		?>
		<option><?php echo $data['name']; ?></option>
		<?php
		}
		?>
	</select>
	</div>
	<div id="searchbody"></div>
	<div class="sell"><input type="checkbox" id="ca" onclick="checkall();"><div style="display:inline;" onclick="check('ca');"><b>Check ALL</b></div></div>
	<div style="margin-top:4px;"><input type="submit" name="copy" value="Copy"></div>
</div>

<div id="copydayx">
	<div style="margin-bottom:4px;"> <b>Date to be Copied</b> : <input type="text" name="dateox" id="dateox" value="<?php echo date('Y-m-d'); ?>" maxlength="10" style="width:90px;"></div>
	<div style="margin-bottom:4px;"> <b>Date</b>&nbsp;&nbsp;&nbsp;From <input type="text" name="date1x" id="date1x" value="<?php echo date('Y-m-d'); ?>" maxlength="10" style="width:90px;"> | To <input type="text" name="date2x" id="date2x" value="<?php echo date('Y-m-d'); ?>"  maxlength="10" style="width:90px;"></div>
	<div style="margin-top:4px;"><input type="submit" name="copyday" value="Copy"></div>
</div>

<table width=100% border="0" cellpadding="4" cellspacing="0" onclick="closeSet()">
	<tr>
		<td width=50% align="left" >
		<input type="hidden" name="em_idz" id="em_idz" value="<?php echo $_SESSION['em_idz']; ?>">
		Search <input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_SESSION['keywordz']; ?>">
		<div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em.php");
		new Ajax.Autocompleter("keyword","hint","server_em.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			$('em_idz').value=myData[0];
			$('keyword').value=myData[1];
			document.myform.submit();
			}
		</script>
		</td>
		<td align="right">
			<input type="button" value="Copy Day" style="border:1px solid #bdb9b9;font:bold 10pt Trebuchet MS;margin-right:25px;" onclick="getHtmlCopyDay(this)">  <input type="button" value="Copy To" style="border:1px solid #bdb9b9;font:bold 10pt Trebuchet MS;margin-right:25px;" onclick="getHtmlCopy(this)">
		</td>
	</tr>
</table>
</form>
<?php
$default_lang = "en";

$vars = array (
	"title"   => "",		
	"version" => "",			
	"author"  => "",	
	"email"   => "",
	"charset" => "iso-8859-7",
	"home"    => "dias/webcalendar/",	
	"fnt-fml" => "Arial",
	"bd_bgc"  => "#ffffff",	
	"bd_txt"  => "#000000",	
	"bd_lnk"  => "#000099",			
	"td1_bgc" => "#717CA5",		
	"td2_bgc" => "#EDEDF1",			
	"td3_bgc" => "#DDDDDD",			
	"cl_hgt"  => "60",	
	"cl_wdt"  => "110",	
	"cl_bgc"  => "#FFFFFF",	
	"ch_bgc"  => "#E8E8E8"	
	);

$langs_path = "./";

$langs_short = array ( "en" );

$langs_file = array (
      "en" => "english.php" );

$langs_name = array (
      "en" => "English" );

$day   = '1';

$off   = '1';

$week = array (
      "1" => "Monday",
      "2" => "Tuesday",
      "3" => "Wednesday",
      "4" => "Thursday",
      "5" => "Friday",
      "6" => "Saturday",
      "7" => "Sunday"
  );

$days_etos = array (
      "1" => "31",
      "2" => "28",
      "3" => "31",
      "4" => "30",
      "5" => "31",
      "6" => "30",
      "7" => "31",
      "8" => "31",
      "9" => "30",
      "10" => "31",
      "11" => "30",
      "12" => "31"
  );
  
$start_year = 2007;
$end_year = 2012;

$mysql_set = "0";

$lang = 'en';

if ( empty( $lang ) )
	$lang = $default_lang;

$cur_lang = "English";

$etos = array( '1' => 'January',
               '2' => 'February',
               '3' => 'March',
               '4' => 'April',
               '5' => 'May',
               '6' => 'June',
               '7' => 'July',
               '8' => 'August',
               '9' => 'September',
               '10' => 'October',
               '11' => 'November',
               '12' => 'December'
               );


$evdomada = array(
               '1' => 'Monday',
               '2' => 'Tuesday',
               '3' => 'Wednesday',
               '4' => 'Thursday',
               '5' => 'Friday',
               '6' => 'Saturday',
               '7' => 'Sunday'
               );
	       
$msg = array (
               "button" => "Go Date",
               "prev_month" => "Previous Month",
               "next_month" => "Next Month",
               "cur_month" => "Current Month"
               );
?>

  <center>
<?php

$today = getdate();

$tod_mday  = $today['mday'];

if ( empty( $_POST["month"] ) ) {

	if ( empty( $_GET["month"] ) )
		$month = $today["mon"];

	else
      		$month = $_GET["month"];

  	}

else
	$month = $_POST["month"];

if ( empty( $_POST["year"] ) ) {

	if ( empty( $_GET["year"] ) )
		 $year = $today["year"];
    	else
      		$year = $_GET["year"];

  	}

else
	$year = $_POST["year"];

if ( ( $year < $start_year ) || ( $year > $end_year ) )
	$year = $today["year"];

if ( ( $month < 1 ) || ( $month > 12 ) )
	$month = $today["mon"];

if ($month=="2")
	for($i=1900;$i<$end_year;$i=$i+4)
     		if ($year==$i){
        		$days_etos[$month]="29";
        		break;
      			}

$easter_year = $year;

$b1 = $easter_year - 2;

$b2 = $b1 % 19;

$b3 = $b2 * 11;

$b4 = $b3 % 30;

$b5 = 44 - $b4;

if ($b4 > 23) $easter_month = 4;

else $easter_month = 3;

$b6 = $b5 + 13;

if ( ($easter_month == 4) && ($b6 > 30) ) { $b6 = $b6 - 30; $easter_month++; }

if ( ($easter_month == 3) && ($b6 > 31) ) { $b6 = $b6 - 30; $easter_month++; }

###########

  $prev_month = $month - 1;

  $next_month = $month + 1;

  $prev_year = $next_year = $year;

  if ($prev_month == 0) { $prev_month = 12; $prev_year--; }

  if ($next_month == 13) { $next_month = 1; $next_year++; }



############

for($i=$b6+1;$i<$b6+8;$i++){
	if ( date('w', mktime(0,0,0,$easter_month,$i,$easter_year)) == 0 ){
	  	$easter_day = $i;
		break;
		}

	}
			

echo "<font color='blue' size='5'>".getword($etos[$month])." ".$year."</font>";
echo "<input type='hidden' id='mmmm' value='" . $month . "'>";
echo "<input type='hidden' id='yyyy' value='" . $year . "'>";
echo "<table border='1' width='".($vars[cl_wdt]*7)."' cellspacing='0' bgcolor='#FFF0F0' id='tablesched'>";
echo "<tr>";
for ($i=1;$i<8;$i++)
	echo "<td width='".$vars[cl_wdt]."' align='center'><strong>".getword($evdomada[$i])."</strong></td>";
echo "</tr>";
echo "<tr id='tdx'>";
while ($day<($days_etos[$month]+1)):
if ($day == 1 ) {
	for ( $i=1;$i<8;$i++ )
		if ( date('l', mktime(0,0,0,$month,$day,$year)) == $week[$i] ){
			$off = $i;
			break;
			}

      for ( $i=1;$i<$off;$i++ )
		echo "<td>&nbsp;</td>\n";
	}
	
if ($day < 10){
	$day1 = "0" . $day;
	}
else{
	$day1 = $day;
	}

if ($month < 10){
        $month1 = "0" . $month;
        }
else{
	$month1 = $month;
	}

$datenow =  $year . "-" .  $month1 . "-" . $day1;

$dateday = date("l", mktime(0, 0, 0, $month1,$day1,$year));

$select = "SELECT * FROM holiday_entry WHERE `date` = '".$datenow."'";
$result = mysql_query($select, connect());
$num_hl = mysql_fetch_row($result,MYSQL_ASSOC);

$select = "select shift_code, day_type from employee_schedule where `em_id` = '" . $_SESSION['em_idz'] . "' and `date` = '" . $datenow . "' ";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

if($row['shift_code']){
	$ccc = "";
	if($row['day_type'] == 'RESTDAY'){
		$ccc = 'style="color:#a60000;"';
		}
	$num_ht = "<br><font " . $ccc . ">" . $row['shift_code'] . "<br>" . $row['day_type'] . "</font>";
	}
else{
	$num_ht = "<br>&nbsp;<br>&nbsp;";
	}


?>
<td <?php if($num_hl >0){ echo 'id="holiday"';}elseif(( $day == $tod_mday ) && ( $month == $today["mon"] ) && ( $year == $today["year"] )){ echo 'id="today"';}elseif(($day < $tod_mday and $month == $today["mon"]) or ($month < $today["mon"] and $year == $today["year"]) or ($year < $today["year"])){ echo 'id="past"'; }else{echo 'id="future"';}?> valign="top" onClick='setsched("server_sched.php?em_id=<?php echo $_SESSION['em_idz']; ?>&date=<?php echo $datenow; ?>",this)' style="cursor: pointer;" id="scedlist">
<?php
echo "<b><font size=6 id='";
if ( ( $day == $tod_mday ) && ( $month == $today["mon"] ) && ( $year == $today["year"] ) )
	echo "blue";
else
	if($dateday == "Sunday"){
		echo "red";
		}
	else{
      		echo "#000000";
		}	
echo "'>".$day."</b></font> " . $num_ht;
echo "</td>";

$day++;
$off++;

if ($off>7) {
	echo "</tr><tr>\n";
	$off='1';
	}

endwhile; 
if($off !=1){
	for ($sa=0;$sa<=(7-$off);$sa++){
		echo "<td>&nbsp;</td>";
		}
	}
echo "</tr></table>";
	
echo "<br><a href='index.php?menu=schedule&lang=".$lang."&month=".$prev_month."&year=".$prev_year."'>".getword($msg[prev_month])."</a>&nbsp;&nbsp;";
echo "<a href='index.php?menu=schedule&lang=".$lang."'>".getword($msg[cur_month])."</a>&nbsp;&nbsp;";
echo "<a href='index.php?menu=schedule&lang=".$lang."&month=".$next_month."&year=".$next_year."'>".getword($msg[next_month])."</a>";
?>
<table cellspacing='0'>
<form action='index.php?menu=schedule&lang=<?php echo $lang; ?>' method='post'>
<tr>
	<td>
	<select name='month'>
		<option value='<?php echo $month; ?>'> <?php echo getword($etos[$month]); ?> </option>
		<option value='<?php echo $month; ?>'> ---------- </option>
		<?php
		for($i=1;$i<13;$i++)
			echo "<option value='".$i."'> ".getword($etos[$i])." </option>";
		?>
          </select>
	</td>
	<td>
	<select name='year'>
		<option value='<?php echo $year; ?>'> <?php echo $year; ?> </option>
		<option value='<?php echo $year; ?>'> ---------- </option>
		<?php
		for($i=$start_year;$i<$end_year;$i++)
			echo "<option value='".$i."'> ".$i." </option>";
		?>
          </select>	
	</td>
	<td>
		<input type='submit' name='submit' value='Go'>
	</td>
</tr>
</form>
</table>
