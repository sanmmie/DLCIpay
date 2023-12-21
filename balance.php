<?php
ini_set('max_execution_time', 300);
include_once 'isopoa.php';

if(!isset($_GET['userid']) || !isset($_GET['pass']) ){
	echo "invalid request!";
	exit;
}


$messrld='';

date_default_timezone_set("Africa/Lagos");
@$uid=$_GET['userid'];
@$pass=$_GET['pass'];

$uid = str_replace("%", "\%", $uid);
$uid = str_replace("_", "\_", $uid);

$uid=strip_tags($uid);
$pass=strip_tags($pass);

$pass = str_replace("%", "\%", $pass);
$pass = str_replace("_", "\_", $pass);

$pass=str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($pass));
$pass = preg_replace('/\s+/', '', $pass);
$pass=str_replace("=","",$pass);


$uid=strip_tags(trim($uid));
$uid=mysqli_real_escape_string($wole,$uid);

$pass=strip_tags(trim($pass));
$pass=mysqli_real_escape_string($wole,$pass);

if(!is_numeric($uid)){
	echo "104|Invalid User id";	
	exit;
}

//$pass=hash('gost',$pass);
//$query="select * from oloja where phone='$uid' and pst='$pass'";
$qs=mysqli_query($wole,"select * from users where phone='$uid' and mapk='$pass'");
if(mysqli_num_rows($qs)==1){
	$rs=mysqli_fetch_array($qs);
	$uname=$rs['uname'];	
	$fname=$rs['fname'];	
	$email=$rs['email'];	
	$approved=$rs['approved'];	
	$bal=$rs['bal'];	
	echo "1000|$bal";
}
else{
	echo "1002|invalid login";
	exit;
}


?>