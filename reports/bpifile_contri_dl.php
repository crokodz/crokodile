<?php
$File=$_GET['file'];
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=".$File);
header('Content-Disposition: attachment; filename="'.$File.'"');
readfile($File);
?>