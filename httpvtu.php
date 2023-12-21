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

@$amount=(int)mysqli_real_escape_string($wole,strip_tags($_REQUEST['amount']));
$amt=$amount;
if($amt<5){
	echo "1002|invalid amount";	
	exit;
}

$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$mvtu=$rsb['mtnvtu'];
$gvtu=$rsb['glovtu'];
$avtu=$rsb['airtelvtu'];
$evtu=$rsb['etivtu'];

@$network=(int)mysqli_real_escape_string($wole,strip_tags($_REQUEST['network']));
if($network=='5'){
	$selnetwork='MTN';
	$reqamt=$amt-($mvtu*$amt/100);
}
elseif($network=='6'){
	$selnetwork='GLO';
	$reqamt=$amt-($gvtu*$amt/100);
}	
elseif($network=='1'){
	$selnetwork='AIRTEL';
	$reqamt=$amt-($avtu*$amt/100);
}		
elseif($network=='2'){
	$selnetwork='9MOBILE';
	$reqamt=$amt-($evtu*$amt/100);
}				
else{
	echo "1002|invalid network $network";	
	exit;
}

if($reqamt>$bal){
	echo "1002|insufficient balance";
	exit;
}

$url="https://mobileairtimeng.com/httpapi/?userid=$airuser&pass=$airpass&network=$network&phone=$phone&amt=$amt";
@$str = file_get_contents($url);
$sdk=explode("|",$str);

@$pos=$sdk[0];
if($pos == '100'){
	@$status=$sdk[1];
	$batch=uniqid();
	
	$reqbal=$bal-$reqamt;					
	$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
	
	$id=0;
	$dt=date('Y-m-d');
	$info=$selnetwork. " ".$amt;
	
	$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','airtime','$info','$phone','$amt','$reqamt','$status')");
					
	//send email notification
	$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Phone:</b> $phone<br><b>Description:</b> $info<br><b>Amount charged:</b> $reqamt</p>
<p>Thank you.</p>";					
	$to = $email;
	$subject = "Airtime Topup Transaction API";
	$message = $dsemail . ""; 
	
	$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
	
	$status1=ucwords($status);							
	echo "1000|$status";	
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