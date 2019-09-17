<?php
function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}
?>
<h3 class="wintitle">Pay Slip</h3>
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0">
<tr>
	<td width=70px align="center">Payroll #</td>
	<td width=200px align="center">Company</td>
          <td width=80px align="center">From</td>
          <td width=80px align="center">To</td>
	<td width=300px>Description</td>
          <td>&nbsp;</td>
</tr>
<?php
$result = get_myposted();
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
<tr>
	<td><?php echo $row['posted_id']; ?></td>
	<td><b><?php echo getcompany($row['company_id']); ?></b></td>
	<td><?php echo $row['from']; ?></td>
	<td><?php echo $row['to']; ?></td>
	<td><?php echo $row['title']; ?></td>
	<td><input type="submit" name="select" value="select" onClick="self.location='index.php?menu=21&from=<?php echo $row['from']; ?>&to=<?php echo $row['to']; ?>&pid=<?php echo $row['posted_id']; ?>';"></td>
</tr>
<?php
}
?>
</table>
</form>