<?php
include_once 'isopoa.php';

$webbal1="https://mobileairtimeng.com/httpapi/balance.php"; 

$sessname=$dbname;
$sessuser=$dbname."ur";

$sessname1=$sessname."1";
$sessname2=$sessname."2";
$sessname3=$sessname."3";
$sessname4=$sessname."4";
$sessname5=$sessname."5";
$sessname6=$sessname."6";
$sessname7=$sessname."7";
$sessname8=$sessname."8";
$sessname9=$sessname."9";
$sessname10=$sessname."10";

$sesslog=$sessname."log";
$sessvtuerr=$sessname."err";
$sesspass=$sessname."pass";
$admsess=$sessname."1";
$whichpage=$sessname."pge";
$sesspage=$sessname."pge";

//$admlog="$weblink/safe";

$con=mysqli_query($wole,"select * from setup where id=1");
$rcon=mysqli_fetch_array($con);
$titleweb=$rcon['titleweb'];
$matphone=$rcon['matphone'];
$matkey=$rcon['matkey'];
$airuser=$rcon['airuser'];
$airpass=$rcon['airpass'];
$maph=$matphone;
$mapk=$matkey;


$webhome=$rcon['webhome'];
$webname=$rcon['webname'];
$webcontact=$rcon['webcontact'];
$fundwallet=$rcon['fundwallet'];
$copyright=$rcon['copyright'];
$sk_live=$rcon['sklive'];
$pk_live=$rcon['pklive'];

$cn=mysqli_query($wole,"select * from announce where id=1");
$rn=mysqli_fetch_array($cn);
$information=$rn['news'];

$cdata=mysqli_query($wole,"select * from mydataprice where id=1");
$rsdata=mysqli_fetch_array($cdata);
$pricedata=$rsdata['dataprice'];

$csms=mysqli_query($wole,"select * from mysmsprice where id=1");
$rssms=mysqli_fetch_array($csms);
$pricesms=$rssms['smsprice'];


function getDomain($url){
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
        return $regs['domain'];
    }
    return FALSE;
}


function sendemailnow($email,$subject,$content,$mainurl,$webt){
	$masteremail = "noreply@".getDomain($mainurl);	
	$to          = $email;
	$from        = "$webt <$masteremail>";

	$html_content = $content;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: ".$from."\r\nReply-To: ".$from;

	//send the email
	$mail_sent = @mail($to, $subject, $html_content, $headers );
}

?>