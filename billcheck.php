<?php
include_once 'config.php';


@$bill=$_GET['bill'];
@$smartno=$_GET['smartno'];

if($bill=='' || $smartno==''){
	echo 'bill type or smart card no missing';
	exit;
}

$url="https://mobileairtimeng.com/httpapi/blookup.php?userid=$airuser&pass=$airpass&bill=$bill&smartno=$smartno";
$str = @file_get_contents($url);
$str= filter_var($str, FILTER_SANITIZE_STRING);
if ($str === false) {
	$str='unable to load customer information';
}
echo $str;


?>