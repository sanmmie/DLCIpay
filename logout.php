<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

unset($_SESSION[$sessname]);
unset($_SESSION[$sesspage]);
header("location: $mainurl");



?>