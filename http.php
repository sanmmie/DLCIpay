<?php
ini_set('max_execution_time', 300);
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_REQUEST['userid']) || !isset($_REQUEST['pass']) ){
	echo "invalid request!";
	exit;
}

$messrld='';

date_default_timezone_set("Africa/Lagos");
@$uid=$_REQUEST['userid'];
@$pass=$_REQUEST['pass'];

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
	$usertype=$rs['usertype'];
	$bal=$rs['bal'];	
}
else{
	echo "1002|invalid login";
	exit;
}


@$phone=mysqli_real_escape_string($wole,strip_tags($_REQUEST['phone']));
if($phone=="" || strlen($phone)>11 || strlen($phone)<11){
	echo "1002|invalid receiver phone";	
	exit;
}


if(isset($_REQUEST['datasize'])){
	$datasize=(int)$_REQUEST['datasize'];
	$datasize=$datasize/1000;
	$dgb=$datasize."GB";
}
else{
	echo "1002|data size missing";
	exit;
}

if($datasize!=1 && $datasize!=2 && $datasize!=5 ){
	echo "1002|invalid data size";
	exit;
}

$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$mdata=$rsb['dataprice'];

$sb=mysqli_query($wole,"select * from mydataprice where id=1");
$rsb=mysqli_fetch_array($sb);
$mdata=$rsb['dataprice'];

$bdata=528;
$reqamt=$mdata*(int)$datasize;

$amtpt=$bdata*(int)$datasize;
$gain=$reqamt-$amtpt;
if($reqamt>$bal){
	
	$content="<h3>Hi $fname</h3><p>The transaction below failed because you have insufficient fund in your wallet.<br>Phone no: $phone<br>Data: $dgb</p>	
	<p>Thank you.</p>";
	
	$bs=sendemailnow($email,'Insufficient Balance',$content,$weburl,$titleweb);
	echo "1002|insufficient vendor balance";
	exit;
}

$dsize=(int)$datasize *1000;
//$url="http://easydatashareng.com/httpapi/datashare.php?userid=$matphone&pass=$matkey&network=1&phone=$phone&datasize=$dsize";
$url="https://easydatashareng.com/http/?userid=$matphone&pass=$matkey&phone=$phone&datasize=$dsize";
@$str = file_get_contents($url);
$sdk=explode("|",$str);

@$pos=$sdk[0];
if($pos == '1000'){
	@$status=$sdk[2];
	@$batch=$sdk[1];
	$reqbal=$bal-$reqamt;					
	$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
	
	$id=0;
	$dt=date('Y-m-d');
	$datagb="MTN ".$datasize."GB";
	
	$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','data share','$datagb','$phone','$reqamt','$reqamt','$status')");
	
	$status1=ucwords($status);
	
	$content="<h3>Hi $fname</h3><p>The transaction below was initiated on your Data Share account via API;<br>Phone no: $phone<br><br>Status: $status1<br>Amount Charged: $reqamt<br>Initial Balance: $bal<br>Current Balance: $reqbal</p>	
	<p>Thank you.</p>";
	
	$bs=sendemailnow($email,'Data Share on API',$content,$weburl,$titleweb);
	
	if($status=="Data recharge completed"){
		echo "1000|$batch|completed";
	}
	elseif($status=="Data recharge in progress"){
		echo "1001|$batch|in progress";
	}
									
}
else{
	if (isset($sdk[1]) && !empty($sdk[1])){
		$tmg=$sdk[1];
	}
	else{
		$tmg="An error occured!";
	}
	echo "1002|$tmg";						
}




?>