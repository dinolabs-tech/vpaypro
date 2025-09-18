<?php
include './database/db_connection.php';
session_start();



session_unset();
session_destroy();
header("Location: index.php");
exit(); 
?>
