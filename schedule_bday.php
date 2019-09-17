<div id="bdate"></div>
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

$dtz = "-" .  $month1 . "-" . $day1;

$select = "select `name` from employee where  `birthdate` like '%" . $dtz . "' and status = 'active' ";
$result = mysql_query($select, connect());
$num_ht = "";
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$num_ht = $num_ht . "<li type=square>" . $row['name'];
	}
$num_ht = "<ul style='margin-left:13px;'>". $num_ht . "</ul>";

//~ if($row['name']){
	//~ $ccc = "";
	//~ if($row['day_type'] == 'RESTDAY'){
		//~ $ccc = 'style="color:#a60000;"';
		//~ }
	//~ $num_ht = "<br><font " . $ccc . ">" . $row['shift_code'] . "<br>" . $row['day_type'] . "</font>";
	//~ }
//~ else{
	//~ $num_ht = "<br>&nbsp;<br>&nbsp;";
	//~ }


?>
<td <?php if($num_hl >0){ echo 'id="holiday"';}elseif(( $day == $tod_mday ) && ( $month == $today["mon"] ) && ( $year == $today["year"] )){ echo 'id="today"';}elseif(($day < $tod_mday and $month == $today["mon"]) or ($month < $today["mon"] and $year == $today["year"]) or ($year < $today["year"])){ echo 'id="past"'; }else{echo 'id="future"';}?> valign="top" onclick="showbday('<?php echo $dtz; ?>',this);" ondblclick="hidebday();" style="cursor: pointer;" id="scedlist">
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
	
echo "<br><a href='index.php?menu=schedule_bday&lang=".$lang."&month=".$prev_month."&year=".$prev_year."'>".getword($msg[prev_month])."</a>&nbsp;&nbsp;";
echo "<a href='index.php?menu=schedule_bday&lang=".$lang."'>".getword($msg[cur_month])."</a>&nbsp;&nbsp;";
echo "<a href='index.php?menu=schedule_bday&lang=".$lang."&month=".$next_month."&year=".$next_year."'>".getword($msg[next_month])."</a>";
?>
<input type="hidden" name="idx" id="idx">
<table cellspacing='0'>
<form action='index.php?menu=schedule_bday&lang=<?php echo $lang; ?>' method='post'>
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
