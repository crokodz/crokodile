<?php
include "config.php";

echo "<ul style=\"width: 400px;\"  class=\"ulz\">";

$sql = "select name, em_id from employee where " . $kez2 . "  `name` like '%" . $_POST['name'] . "%' and " . $kez11 . " (status='active' or  status='inactive' or  status='deleted')  LIMIT 18";

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
echo  "</ul>";
?>