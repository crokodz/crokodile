<?php
include "config.php";
?>
<style>
div.fileinputs {
	position: relative;
	cursor: pointer; 
	cursor: hand;
	width:360px;
	text-align:right;
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
<div style="width:350px;">
<div class="salary">Amount</div><div class="searchinline">&nbsp;<input type="text" name="amount" id="amount"></div><br>
<div class="salary">No. of  Payday</div><div class="searchinline">&nbsp;<input type="text" name="payday" id="payday"></div><br>
<div class="salary">Date Time</div>&nbsp;<div class="searchinlinedt"><input type="text" name="datetime" id="datetime" value="<?php echo date('Y-m-d');?>" maxlength="10"> 3:00 AM&nbsp;&nbsp;</div><br>
<div class="salary"><input type="submit" name="savepal" id="savepal" value="Save"></div>
</div>
<br>
<br>
<div class="fileinputs" style="display:inline-block;">
	<input type="file" name="file" id="file" class="zfile" onchange="onfreaky(this);">
	<div class="fakefile">
		<img src="button_select.gif">
	</div>
	<div class="fakefile1">
		<input type="text" name="file_name" id="file_name" value="" readonly="true" style="width:270px;">
	</div>
</div>
<div style="display:inline-block;">
	<input type="submit" name="upspal" id="upspal" value="upload xml" onclick="return onups('file_name');">
	<a href="javascript:void.void(0)" onclick="showTip(this)">HELP</a>
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="clearpal" value="Clear">
</div>
<div class="LiHeader">
<div>
	<div id="h1" style="font-weight:bold;">Date</div>
	<div id="h2" style="font-weight:bold;">Employee</div>
	<div id="h3" style="font-weight:bold;">Name</div>
	<div id="h4" style="font-weight:bold;">Amount</div>
	<div id="h5" style="font-weight:bold;">Payday</div>
</div>
<?php
$select = "select * from cronjob where `username` = '" . $_SESSION['user'] . "' and type = 'pal' and `status` = 0";
$result = mysql_query($select, connect());
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
$style = "";
$disabled = "";
$checked = "checked";
if($row['date'] == '0000-00-00' or $row['amount'] <= 0 or $row['name'] == ''){
	$sytle = "background-color:#9b9b98";
	$disabled = "disabled";
	$checked = "";
	}
?>
<div style="<?php echo $sytle; ?>">
	<div id="h1"><?php echo $row['date']; ?></div>
	<div id="h2"><?php echo $row['emid']; ?></div>
	<div id="h3"><?php echo $row['name']; ?></div>
	<div id="h4"><?php echo $row['amount']; ?></div>
	<div id="h5"><?php echo $row['gives']; ?></div>
	<div id="h6"><input type="checkbox" name="cb<?php echo $x; ?>" value="<?php echo $row['id']; ?>" <?php echo $disabled; ?> <?php echo $checked; ?>></div>
</div>
<?php
$x++;
}
?>
<input type="hidden" name="count" value="<?php echo $x; ?>">
</div>
<div style="margin-top:8px;"><input type="submit" name="postpal" value="post"></div>
<br>
Pending
<div class="LiHeader">
<div>
	<div id="h1" style="font-weight:bold;">Date</div>
	<div id="h2" style="font-weight:bold;">Employee</div>
	<div id="h3" style="font-weight:bold;">Name</div>
	<div id="h4" style="font-weight:bold;">Amount</div>
	<div id="h5" style="font-weight:bold;">Payday</div>
</div>
<?php
$select = "select * from cronjob where type = 'pal' and `status` = 2";
$result = mysql_query($select, connect());
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
$style = "";
$disabled = "";
$checked = "checked";
if($row['date'] == '0000-00-00' or $row['amount'] <= 0 or $row['name'] == ''){
	$sytle = "background-color:#9b9b98";
	$disabled = "disabled";
	$checked = "";
	}
?>
<div>
	<div id="h1"><?php echo $row['date']; ?></div>
	<div id="h2"><?php echo $row['emid']; ?></div>
	<div id="h3"><?php echo $row['name']; ?></div>
	<div id="h4"><?php echo $row['amount']; ?></div>
	<div id="h5"><?php echo $row['gives']; ?></div>
	<div id="h6"><input type="submit" name="delpal" value="Delete" onclick="return ondeletez('<?php echo $row['id']; ?>');"></div>
</div>
<?php
$x++;
}
?>
</div>
<input type="hidden" name="id" id="id">

<div id="help" class="help">Create a Excel file with the exact header as follows Employee ID, Amount, No. of Payday, Date. Then save it as XML file</div>