<?php
session_start();

include_once 'isopoa.php';
include_once 'config.php';

if(isset($_POST['dataitem'])){
	extract ($_POST);
	$mdata=explode("|",$dataitem);
	
	$refid=$mdata[0];
	$refid=mysqli_real_escape_string($wole,$refid);
	
	$q=mysqli_query($wole,"select * from paystack where refid='$refid'");
	if(mysqli_num_rows($q)==1){
		$_SESSION['fundwallet']="failed|Transaction already confirmed!";
		header("location: fundwallet");
		exit;
	}
	
	$result = array();
	//The parameter after verify/ is the transaction reference to be verified
	$url = "https://api.paystack.co/transaction/verify/$refid";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $sk_live"));
	$request = curl_exec($ch);	
	
	if(curl_error($ch))
	{
		echo 'error:' . curl_error($ch);
		exit;
	}
		
	curl_close($ch);
	
	if ($request) {
	  $result = json_decode($request, true);
	  $status=$result['data']['status'];
	  $amount=$result['data']['amount'];
		
		
	  $amt=$mdata[3];		  
	  if($status=='success'	&& $amt==$amount){	  	
		//credit account
		$addamt=$mdata[2];
		$phone=$mdata[1];
		
		$addamt=($amount/100)/1.016;
		$fd=fundaccount($phone,$addamt,$weburl,$webname,$wole);
		
		if($fd=='yes'){
			$_SESSION['fundwallet']="success|Payment successful";
		}
		else{
			$_SESSION['fundwallet']="failed|Payment not completed";
		}
				
		$id=0;
		$q=mysqli_query($wole,"insert into paystack values ('$id','$refid')");				
		header("location: fundwallet");
		exit;			
	  }
	  else{
	  	$_SESSION['fundwallet']="failed|Unable to verify payment";
	  	header("location: fundwallet");
		exit;
	  }  
	}
	//var_dump($request);
	
}
else{
	$_SESSION['fundwallet']="failed|No payment found!";
	header("location: fundwallet");
	exit;
}


function fundaccount($phone,$addamt,$weburl,$webname,$wole){
	$addunit=$addamt;
	$phone = filter_var($phone, FILTER_SANITIZE_STRING);
	
	$query="select * from users where phone='$phone'";
	$res=mysqli_query($wole,$query);
	$row=mysqli_fetch_array($res);
	
	@$email=$row['email'];
	@$id=$row['id'];
	@$unm=$row['uname'];
	$fname=$row['fname'];
	
	$bal=$row['bal'];
	$inibal=$bal;

	$dty=date('d-m-Y');
	$expdate=date('Y-m-d');
	$dtexp=date('Y-m-d', strtotime($expdate. ' + 2 days'));
		
	$newmsg='';
	@$units=$inibal+$addunit;
	$smsmess="Account update. Date: $dty, Initial Bal: $inibal, Credit: $addunit, New Bal: $units. Thank you.";
	$query="update users set bal='$units' where id=".$id;	
	$res=mysqli_query($wole,$query);

	//save credithistory
	$id=0;
	$dt=date('Y-m-d');
	@$batch=uniqid();
	$dc="Online Wallet Funding";
	$carr="Account credited with N$addunit";
	
	$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$unm','$batch','$dc','$carr','$phone','$addunit','','completed')");
	
	//send sms notification
     $fnm=explode(" ",$fname);
		if(count($fnm)>0){
			$fno= $fnm[0];
		}
		else{
			$fno= $fname;
		}

	 
	//$sender="EasyData";
	//$smsto=urlencode($phone);
	//$sender=urlencode($sender);
	//$smsmessage=$smsmess;
    //$telluser=notifysms($smsto,$smsmessage,$sender);

	//send email notification	
	$adn=number_format($addunit,2,".",",");
	$adn1=number_format($units,2,".",",");
	
	$subject = "Transaction Notification [Credit N$adn]";
	$subject2 = "Transaction Notification for $fno";
	
	$mess1="Hello <b>$fno</b><br>A wallet transaction just occured on your account. See details;<br>Amount: N$adn<br>Current Balance: N$adn1.$newmsg<br><br>Thank you";
	//send the email
	
	$bsend=sendemailnow($email,$subject,$mess1,$weburl,$webname);	
	return "yes";
	
}

//$a=fundaccount('08139170491',100,$wole);
//echo $a;

?>