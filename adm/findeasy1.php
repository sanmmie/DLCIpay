<?php
session_start();


$phg=$_GET['phone'];

include '../isopoa.php';
$phg=strip_tags(trim($phg));
$phg=mysqli_real_escape_string($wole,$phg);

$query="select * from users where phone='$phg'";
$k=mysqli_query($wole,$query);
$row=mysqli_fetch_array($k);
$phonenumbers=$row['id']."|".$row['fname']."|".$row['usertype'];

if(mysqli_num_rows($k)==1){
	echo $phonenumbers;
}
else{
	echo "failed";
}



?>