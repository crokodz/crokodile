<?php
unset($_SESSION['user']);
unset($_SESSION['pass']);
unset($_SESSION['level']);
unset($_SESSION['company']);
unset($_SESSION['language']);
unset($_SESSION['em_id']);
unset($_SESSION['keyword']);
unset($_SESSION['key']);
unset($_SESSION['attemp']);
$_SESSION['login'] = false;
$_SESSION['admin'] = false;
refresh();
?>