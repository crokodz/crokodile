<?php
$result = mysql_query("select *from users;", connect()); 

?>
<h3 class="wintitle">Report Windows Powered by ezPDF</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=100% align="center"><iframe style="width:100%;border:none;" height=600 id="xframe" name="xframe" bgcolor="#CC9933"></iframe></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>