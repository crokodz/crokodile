<?php
include "class.pager.php";

$select = "select count(*) as total from employee where status='active';";
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

$select = "select * from employee where status='active' LIMIT " . $start . " , " . $limit;
$result = mysql_query($select, connect());
?>

<h3 class="wintitle">Employee Deduction</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=80% align="left" ><input type="text" name="keyword"> | <input type="submit" name="serach" value="search"></td>
		<td width=20%><?php echo $pagelist; ?></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=10% align="center"><b>id</b></td>
		<td width=50% align="center"><b>name</b></td>
		<td width=20% align="center"><b>department</b></td>
		<td width=15% align="center"><b>contact</b></td>
		<td width=5% align="center"><input type="checkbox" name="checkb" onclick="checkall(this.form)"></td>
	</tr>
<?php
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;">
		<td align="left" onclick="self.location='index.php?menu=8DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['department']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['em_number']; ?></td>
		<td align="center"><input type="checkbox" name="id<?php echo $x; ?>" id="id" value="<?php echo $row['em_id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
?>
<input type="hidden" name="count" value="<?php echo $x; ?>">
</table>
</form>