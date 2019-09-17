<?php
$result = mysql_query("select *from users;", connect()); 

?>
<h3 class="wintitle">General Configuration</h3>
<form method="post">
<table width=100% border="1" cellpadding="4" cellspacing="0" bgcolor="white">
	<tr style="cursor:pointer;">
		<td id="td1" width=11.11% align="center" onclick="GenFrame('companyLI.php','td1');" style=""><?php echo getword("Company"); ?></td>
		<td id="td2" width=11.11% align="center" onclick="GenFrame('branches.php','td2');"><?php echo getword("Branches"); ?></td>
		<td id="td3" width=11.11% align="center" onclick="GenFrame('positions.php','td3');"><?php echo getword("Positions"); ?></td>
		<td id="td4" width=11.11% align="center" onclick="GenFrame('departments.php','td4');"><?php echo getword("Departments"); ?></td>
		<td id="td5" width=11.11% align="center" onclick="GenFrame('skills.php','td5');"><?php echo getword("Skills"); ?></td>
		<td id="td6" width=11.11% align="center" onclick="GenFrame('languages.php','td6');" style=""><?php echo getword("Languages"); ?></td>
		<td id="td7" width=11.11% align="center" onclick="GenFrame('educations.php','td7');"><?php echo getword("Education"); ?></td>
		<td id="td8" width=11.11% align="center" onclick="GenFrame('licenses.php','td8');"><?php echo getword("License"); ?></td>
		<td id="td9" width=11.11% align="center" onclick="GenFrame('employee_status.php','td9');" style=""><?php echo getword("Emp Status"); ?></td>
	</tr>
	<tr style="cursor:pointer;">
		<td id="td10" align="center" onclick="GenFrame('membership_types.php','td10');"><?php echo getword("Mmbr Types"); ?></td>
		<td id="td11" align="center" onclick="GenFrame('memberships.php','td11');"><?php echo getword("Membership"); ?></td>
		<td id="td12" align="center" onclick="GenFrame('nationality.php','td12');" style=""><?php echo getword("Nationality"); ?></td>
		<td id="td13" align="center" onclick="GenFrame('ethnic_race.php','td13');"><?php echo getword("Ethnic Race"); ?></td>
		<td id="td14" align="center" onclick="GenFrame('ot_rate.php','td14');"><?php echo getword("Day Type"); ?></td>
		<td id="td15" align="center" onclick="GenFrame('deductions.php','td15');"><?php echo getword("Deduction"); ?></td>
		<td id="td16" align="center" onclick="GenFrame('taxable_entry.php','td16');">Taxable</td>
		<td id="td17" align="center" onclick="GenFrame('nontaxable_entry.php','td17');">Non-Taxable</td>
		<td id="td18" align="center" onclick="GenFrame('currency.php','td18');"><?php echo getword("Currency"); ?></td>
	</tr>
	<tr style="cursor:pointer;">
		<td id="td19" align="center" onclick="GenFrame('tax_status.php','td19');"><?php echo getword("Tax Status"); ?></td>
		<td id="td20" align="center" onclick="GenFrame('paylist.php','td20');"><?php echo getword("Pay Code"); ?></td>
		<td id="td21" align="center" onclick="GenFrame('shift.php','td21');"><?php echo getword("Shift Code"); ?></td>
		<td id="td22" align="center" onclick="GenFrame('yearly_cutoff.php','td22');">Yearly Cutoff</td>
		<td id="td23" align="center" ></td>
		<td id="td24" align="center" ></td>
		<td id="td25" align="center" ></td>
		<td id="td26" align="center" ></td>
		<td id="td27" align="center" ></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=100% align="center"><iframe style="width:100%;border:none;" height=500 id="xframe" name="xframe" bgcolor="#CC9933"></iframe></td>
	</tr>
</table>	
</form>
