
<?php
$result = mysql_query("select *from users;", connect()); 

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
?>
<h3 class="wintitle"><b>BPI File Export</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<input type="button" value="File Extract" onclick="top.f_dialogOpen('reports/bpifile_contri.php?date=<?php echo date('Y-m-d'); ?>','BPI Export FILE', 'width=500px, height=300px','0');">
		</td>
	</tr>
</table>
<br>
<br>
<h3 class="wintitle"><b>PhilHealth</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" value="Monthly Remittance Report"  Onclick="OpenGetID(this,'phq',8,12);">
		</td>
	</tr>
</table>

<br>
<br>
<h3 class="wintitle"><b>Pag-Ibig</b></h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" value="Monthly Contributions" id="pimc"  Onclick="OpenGetID(this,'piq',90,-67);">
		</td>
		<td>
			<input type="button" value="Monthly Remittance Schedule"  id="pims" Onclick="OpenGetID(this,'pis',90,-67);">
		</td>
	</tr>
</table>

<form method="POST" name="reports">


<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<h3 class="wintitle">For Testing Purposes</h3>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10% align="left">
		payroll # : 
		<select name="id" id="id" style="width:50%;">
		<?php
		if ($_SESSION['company'] == '0'){
			$select = "SELECT * FROM posted_summary GROUP BY posted_id order by posted_id desc";
			}
		else{
			$select = "SELECT * FROM posted where company_id = '" . $_SESSION['company'] . "' GROUP BY posted_id order by posted_id desc";
			}
		$result = mysql_query($select, connect());
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		?>
		<option value="<?php echo $row['posted_id']; ?>"><?php echo $row['posted_id'] . " - " .  $row['from'] . " to " . $row['to']?></option>
		<?php
		}
		?>
		</select>
		</td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>
			<input type="button" value="Posted" Onclick="openwindow('reports/posted.php?pid='+reports.id.value,600,800)">
			<input type="button" value="Summary" Onclick="openwindow('reports/payroll_summary.php?pid='+reports.id.value,600,800)">
			<input type="button" value="Pay Register" Onclick="openwindow('reports/pay_register.php?pid='+reports.id.value,600,800)">
			<input type="button" value="SSS, PI, PH" Onclick="openwindow('reports/ssspiph.php?pid='+reports.id.value,600,800)">
			<input type="button" value="Tax" Onclick="openwindow('reports/tax.php?pid='+reports.id.value,600,800)">
		</td>
	</tr>
	<tr>
		<td>BPI</td>
	</tr>
	<tr>
		<td>
			<input type="button" value="Extract File Contibution"  Onclick="openwindow('reports/bpifile_contri.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
		</td>
	</tr>
	<tr>
		<td>SSS</td>
	</tr>
	<tr>
		<td>
			<input type="button" value="Extract File Contibution"  Onclick="openwindow('reports/sss_contri.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)"> | 
			<input type="button" value="Extract Summary Contribution" Onclick="openwindow('reports/sss_contrib.php?pid='+reports.id.value,600,800)">
			<input type="button" value="Extract Summary Loan" Onclick="openwindow('reports/sss_loan.php?pid='+reports.id.value,600,800)">
			<input type="button" value="Extract File Loan" disabled>
			<input type="button" value="New Reg."  Onclick="openwindow('python/sss_new_reg.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
		</td>
	</tr>
	<tr>
		<td>PhilHealth</td>
	</tr>
	<tr>
		<td>
			<input type="button" value="Extract Employer's Quarterly Report"  Onclick="openwindow('python/ph_quarterly.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
			<input type="button" value="Extract Employer's Report"  Onclick="openwindow('python/ph_remit.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
		</td>
	</tr>
	<tr>
		<td>PagIbig</td>
	</tr>
	<tr>
		<td>
			<input type="button" value="Extract Summary Contribution"  Onclick="openwindow('python/pi_remit.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
			<input type="button" value="Extract Summary Loan"  Onclick="openwindow('python/pi_loan.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
		</td>
	</tr>
	<tr>
		<td>Bir</td>
	</tr>
	<tr>
		<td>
			<input type="button" value="Extract Monthly Contribution"  Onclick="openwindow('python/monthly_remit.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
			<input type="button" value="Extract Yearly Contribution"  Onclick="openwindow('python/yearly_remit.php?date=<?php echo date('Y-m-d'); ?>',screen.height,screen.width)">
		</td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>

<div id="getid" class="getid">
	<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
		<?php
		$x=0;
		$select = "select `title`, `posted_id`, `from`, `to`, `company_id` from `posted_summary` where `company_id` != 0 group by `posted_id` order by `posted_id` asc";
		$result = mysql_query($select, connect());
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$cinfo = getcompany($row['company_id']);
		?>
		<tr class="listid" id="tr<?php echo $x; ?>">
			<td onclick="clickCH('<?php echo $x; ?>');"><?php echo $row['title']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="100px"><?php echo $cinfo['name']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="60px"><?php echo $row['posted_id']; ?></td>
			<td onclick="clickCH('<?php echo $x; ?>');" width="170px"><?php echo $row['from'] . " - " . $row['to']; ?></td>
			<td width="10px"><input type="checkbox" name="ch<?php echo $x; ?>" id="ch<?php echo $x; ?>" value="<?php echo $row['posted_id']; ?>"></td>
		</tr>
		<input type="hidden" name="compa<?php echo $x; ?>" id="compa<?php echo $x; ?>" value="<?php echo $row['company_id']; ?>">
		<?php
		$x++;
		}
		?>
	</table>
	<div id="extra"></div>
	<br>
	<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
	<input type="hidden" name="type" id="type" value="">
	
	<input type="button" value="Generate" onclick="GenReport();">
</div>

</form>