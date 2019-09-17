<?php

include "config.php";


if($_POST['token'] == 'HiiiSSjhyAll'){
    $insert="insert into biotime values (NULL, '" . $_POST['em_id'] . "', '" . $_POST['date'] . "', '" . $_GET['machine'] . "')";
    mysql_query($insert, connect());
}
?>
