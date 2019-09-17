<?php
include "config.php";
include "class.pager.php";

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['id' . $x]){
			$update = "update company set status='deleted' where id = '" . $_POST['id' . $x] . "'";
			mysql_query($update, connect());
			}
		}
	}
$select = "select count(*) as total from company where status='active';";
$result = mysql_query($select, connect());
$count = mysql_fetch_array($result,MYSQL_ASSOC);
$total = $count['total'];

#pager
$p = new Pager;
$limit = 10;
$start = $p->findStart($limit);
$pages = $p->findPages($total, $limit);
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=1');
##

$select = "select * from company where status='active' LIMIT " . $start . " , " . $limit;
$result = mysql_query($select, connect());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Payroll System</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=win 1252">
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<br>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=30% align="left" ><input type="text" name="keyword"> | <input type="submit" name="serach" value="search"></td>
		<td width=20%><?php echo $pagelist; ?></td>
		<td width=50% align="right"><a href="company.php">add</a> | <a href="javascript:void(0);" onclick="ClickButton('delete')">delete</a><input type="submit" name="delete" id="delete" value="delete" style="display:none;"></td>
	</tr>
</table>	
<br>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=20% align="center"><b>name</b></td>
		<td width=60% align="center"><b>address</b></td>
		<td width=15% align="center"><b>number</b></td>
		<td width=5% align="center"><input type="checkbox" name="checkb" onclick="checkall(this.form)"></td>
	</tr>
<?php
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;">
		<td align="left" onclick="self.location='company.php?id=<?php echo $row['id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='company.php?id=<?php echo $row['id']; ?>';"><?php echo $row['address']; ?></td>
		<td align="left" onclick="self.location='company.php?id=<?php echo $row['id']; ?>';"><?php echo $row['number']; ?></td>
		<td align="center"><input type="checkbox" name="id<?php echo $x; ?>" id="id" value="<?php echo $row['id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
?>
<input type="hidden" name="count" value="<?php echo $x; ?>">
</table>
</form>
</body>