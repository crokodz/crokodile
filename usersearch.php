<?php
include "config.php";

$select = "select * from users where `username` like '%" . $_GET['search'] . "%' limit 50";
$result = mysql_query($select, connect());
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<form method="post">
<body id="innerframe">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=10% align="center" ><b>id</b></td>
		<td width=50% align="center"><b>username</b></td>
		<td width=20% align="center"><b>realname</b></td>
		<td width=20% align="center"><b>position</b></td>
	</tr>
<?php
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;" onclick="PutID('<?php echo $row['username']; ?>')">
		<td align="left"><?php echo $row['id']; ?></td>
		<td align="left"><?php echo $row['username']; ?></td>
		<td align="left"><?php echo $row['realname']; ?></td>
		<td align="left"><?php echo $row['position']; ?></td>
	</tr>
	<?php
	}
?>
</table>
</body>
</form>