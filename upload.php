<?php
$random = (rand()%9);
?>
<h3 class="wintitle">Upload File</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td align="left">
		<iframe name="patient_list" width=100% height=600px frameborder="0" src="myupload.php?<?php echo $random; ?>"></iframe>
		</td>
	</tr>
</table>	
</form>