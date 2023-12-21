<?php
$servername='localhost';
$enter1='ibktwfor_vtuser';
$enter2='q*K+]WBUrx7f';
$dbname=getdbname();
$wole=mysqli_connect('localhost',$enter1,$enter2);
mysqli_select_db($wole,$dbname);

$mainurl=getdurl();
$weburl="$mainurl/client";
$webprog="$weburl/simple.php";


function getdbname(){
	$myfile = fopen("basefile.txt", "r") or die("Unable to open file!");
	$dname= fread($myfile,filesize("basefile.txt"));
	fclose($myfile);
	return $dname;
}
function getdurl(){
	$myfile = fopen("baseurl.txt", "r") or die("Unable to open file!");
	$durl= fread($myfile,filesize("baseurl.txt"));
	fclose($myfile);
	return $durl;
}


?>