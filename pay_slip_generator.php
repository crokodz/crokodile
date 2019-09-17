<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="medshtml">
<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<?php
function getcompany($id){
	$select = "select tb1.name from company tb1 join posted_summary tb2 on (tb1.id = tb2.company_id) where tb2.posted_id = '" . $id . "' group by posted_id";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}


?>
<form method="POST" name="payslip">
<h3 class="wintitle">Pay Slip Generator</h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=12%>Company : </td>
	<td><?php echo getcompany($_GET['pid']); ?></td>
</tr>
<tr>
	<td>Posted ID : </td>
	<td><?php echo $_GET['pid']; ?></td>
</tr>
<tr>
	<td>From : </td>
	<td><?php echo $_GET['from']; ?></td>
</tr>
<tr>
          <td>To : </td>
	<td><?php echo $_GET['to']; ?></td>
</tr>
<tr>
          <td>Pay Code : </td>
	<td>
		<select style="width:150px;height:100px;" name="id" id="id" multiple>
			<?php
			if($_SESSION['user'] == 'mso'){
				$select = "select `name` from `pay` where `group` = 'mso' group by `name`";
				}
			else{
				$select = "select `name` from `pay` where name like '" . $_SESSION['pay_id'] . "%' group by `name`";
				}
			$result_data = mysql_query($select, connect());
			if($_SESSION['user'] == 'mso'){
				?>
				<option value="ALL MSO" selected>ALL MSO</option>
				<?php
				}
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['name']; ?>" <?php if ($_SESSION['dep'] == $data['name']){ echo 'selected'; } ?> selected><?php echo $data['name']; ?></option>
			<?php
			}
			?>
		</select>
	</td>
</tr>
</table>
<br>
<h3 class="wintitle">Pay Slip By Employee</h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=10%>employee id</td>
	<td width=90%>
	<input type="text" name="keyword" id="keyword" size=27 value="<?php echo $_POST['keyword']; ?>">
	<div id="hint"></div>
	<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@");
			//$('name').value=myData[1];
			$('keyword').value=myData[0];
			}
	</script>
	</td>
</tr>
<tr>
	<td colspan=2 align="right"><input type="button" <?php if($_SESSION['user']=='efren' || $_SESSION['user']=='mae'){ echo ''; } else{ echo 'disabled'; } ?> name="generate" value="generate payslip" Onclick="openwindow('reports/payslip.php?id='+payslip.keyword.value+'&pid=<?php echo $_GET['pid']; ?>',600,800)"></td>
</tr>
</table>
<br>
<h3 class="wintitle">Generate All Payslip...  note: this will take time</h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=100%>
	<?php
		$select = "select count(`em_id`) as `num` from posted_summary where `posted_id` = '" . $_GET['pid'] . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$count = $row['num'];
		for($x=0;$x<$count;$x=$x+100){
		?>
		<a href="javascript:void(0);" Onclick="openwindowpay('reports/payslip.php?id=ALL&pid=<?php echo $_GET['pid']; ?>&max=<?php echo $x; ?>' ,600,800)"> payslip <?php echo ($x+1) . ' to ' . ($x+100)?></a><br>
		<?php
		}
		?>
	</td>
</tr>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
<input type="hidden" name="id" id="id" value="<?php echo $_GET['pid']; ?>">
</form>
