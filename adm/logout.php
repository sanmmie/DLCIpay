<?php
session_start();
include_once '../config.php';
unset($_SESSION[$admsess]);
header('location:index.php');

?>