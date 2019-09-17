<?php
include "config.php";

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Yearly Cut-Off</h3>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<?php
	$x = 0;
	$select = "select * from yearly_cutoff order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td width=30%><?php echo $row['date1'] . " - " . $row['date2'];  ?></td>
		<td ></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>