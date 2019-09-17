<?php
include ('Thumbnail.class.php');
$thumb=new Thumbnail($_GET['path']);
$thumb->size($_GET['w'],$_GET['h']);
$thumb->process(); 
$thumb->show();
?>