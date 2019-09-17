<?php
include "config.php";
$payid = $_GET['paycode'];

$select = "select name, em_id from `employee` where " . $kez1 . $kez2 . "  pay_id = '" . $payid . "'";
$result_data = mysql_query($select, connect());
$x = 0;
while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
?>
<div class="sell"><input type="checkbox" name="cb<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="<?php echo $data['em_id']?>"><div style="display:inline;" onclick="check('cb<?php echo $x; ?>');"><?php echo $data['name']?></div></div>
<?php
$x++;
}
?>
<input type="hidden" id="count" name="count" value="<?php echo $x; ?>">