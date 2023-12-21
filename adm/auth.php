<?php
session_start();

@$uname=$_POST['phonenumber'];
@$pass=$_POST['thepass'];

include_once '../isopoa.php';
include_once '../config.php';
$uname = mysqli_real_escape_string($wole,htmlentities($uname));
$pass= mysqli_real_escape_string($wole,htmlentities($pass));

$uname=str_replace('"','',$uname);
$uname=str_replace(array("'","'"),'',$uname);

$pass=str_replace('"','',$pass);
$pass=str_replace(array("'","'"),'',$pass);

if($uname=="" || $pass==""){
	$_SESSION['adminerr']="<font color=red>Enter log in details!</font>";
	header('location:index');
	exit;
}


$pass=hash('sha512',$pass);
$query="select * from emi where uname='$uname' and log='$pass'";
$res=mysqli_query($wole,$query);
$n=mysqli_num_rows($res);
$row=mysqli_fetch_array($res);
$type=$row['type'];
$_SESSION['adminname']=$row['fname'];


if ($n==1){
	$_SESSION[$admsess]=$uname;
	header('location:dashboard');
}
else{
	$_SESSION['adminerr']="<font color=red>Invalid login.</font>";
	header('location:index');
}


?>