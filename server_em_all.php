<?php
include "config.php";

echo "<ul style=\"width: 400px;\"  class=\"ulz\">";

$sql = "select name, em_id from employee where " . $kez1 . $kez2 . "   status='active' and (`name` like '%" . $_POST['keyword'] . "%' or em_id like '%" . $_POST['keyword'] . "%') LIMIT 18";

$result = mysql_query($sql, connect());
while ($data = mysql_fetch_array($result,MYSQL_ASSOC)){
	
echo "<li id=\"" . 
$data['em_id'] . "@@" . 
$data['name']  . "@@" . 
"\" class=\"liz\">
<table width=100% style='border: 1px solid white;' rules='all'><tr>
<td>" . $data['name'] . "</td>
<td width=70px>" . $data['em_id'] . "</td>
</tr></table>
</li>";
}
echo "</ul>";
?>