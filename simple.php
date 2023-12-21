<?php
session_start();
ini_set('max_execution_time', 300);


include_once 'isopoa.php';
include_once 'config.php';
include_once 'datastatus.php';


if(isset($_REQUEST['gtype'])){
	if(isset($_GET['gtype'])){
		extract ($_GET);
	}
	elseif(isset($_POST['gtype'])){
		extract ($_POST);
	}
			
	if($gtype=='login'){
		@$phone=mysqli_real_escape_string($wole,$phone);
		@$pass=mysqli_real_escape_string($wole,$pass);
		$pass=hash('snefru',$pass);
		$qs=mysqli_query($wole,"select * from users where phone='$phone' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);
			$uname=$rs['uname'];	
			$fname=$rs['fname'];	
			$email=$rs['email'];	
			$bal=$rs['bal'];	
			$refid=$rs['refid'];	
			$smsbonus=$rs['smsbonus'];
			$expdate=date('d-m-Y',strtotime($rs['expdate']));			
			$dexpdate=$rs['expdate'];
			
			$today=date('Y-m-d');
			if($dexpdate<$today){
				$qe=mysqli_query($wole,"update users set smsbonus='0' where phone='$phone'");
				$smsinfo="N0.00 (Expired)";
			}
			else{
				$smsinfo="N".$smsbonus." (Valid till: $expdate)";
			}
			
						
			//$price=$rs['price'];	
			echo "success|$uname|$fname|$bal|$email|$smsinfo|$refid";
		}
		else{
			echo "failed|Invalid login";
		}
	}
	elseif($gtype=='checkreg'){
		@$phone=mysqli_real_escape_string($wole,$phone);
		@$email=mysqli_real_escape_string($wole,$email);
		$q=mysqli_query($wole,"select * from users where email='$email'");
		if(mysqli_num_rows($q)==1){
			echo "failed|Email already registered";
			exit;
		}
		$q=mysqli_query($wole,"select * from users where phone='$phone'");
		if(mysqli_num_rows($q)==1){
			echo "failed|Phone already registered";
			exit;
		}
		echo "success|Proceed to payment";
	}
	elseif($gtype=='register'){
	@$phone=mysqli_real_escape_string($wole,$phone);
		@$fname=mysqli_real_escape_string($wole,$fname);
		@$email=mysqli_real_escape_string($wole,$email);
		@$passw=mysqli_real_escape_string($wole,$pass);
		@$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$fname=ucwords($fname);
		
		if($captcha!=$_SESSION["captcha_code"]){
		    //echo "failed|Enter Captcha";
			$_SESSION[$sessname4]="<font color=red>Enter Captcha</font>";
			header("location: register");
			exit;
		}
		
		$q=mysqli_query($wole,"select * from users where email='$email'");
		if(mysqli_num_rows($q)==1){
			$_SESSION[$sessname4]="<font color=red>Email already registered</font>";
			header("location: register");
			
			exit;
		}
		$q=mysqli_query($wole,"select * from users where phone='$phone'");
		if(mysqli_num_rows($q)==1){
			//echo "failed|Phone already registered";
			$_SESSION[$sessname4]="<font color=red>Phone already registered</font>";
			header("location: register");
			exit;
		}
		if(isset($referral)){
			$referral = mysqli_real_escape_string($wole,$referral);
		}
		else{
			$referral='none';
		}
		
		$id=0;
		$bonus=50;
		$bal=200;
		$uname=substr($fname,0,4).substr(hash('snefru',date('ymdhis')),3,8);
		
		$qq=mysqli_query($wole,"SELECT id from users order by id desc limit 1");
		$rd=mysqli_fetch_array($qq);
		$lastid=$rd['id'];
		//$batchno=date('ym').$lastid;
		if(strlen($fname)>5){
			$refid=substr($fname,0,5).$lastid;
		}
		else{
			$refid=substr($fname,0,4).$lastid;
		}
		
		$expdate=date('Y-m-d');
		$dtexp=date('Y-m-d', strtotime($expdate. ' + 3 days'));
		$expd=date('d-m-Y',strtotime($dtexp));
		
		$pass=hash('snefru',$passw);
		$dtreg=date('Y-m-d');
		$dky=substr(hash('whirlpool',$uname),1,15);
		$qu=mysqli_query($wole,"insert into users values ('$id','$dtreg','$uname','$fname','$refid','$referral','$dky','$pass','$phone','$email','user','0','50','$dtexp','y')");
						
		$full=explode(" ",$fname);
		if(count($full)>0){
			$fsname= $full[0];
		}
		else{
			$fsname= $fname;
		}
		
		$dsemail="<h3>Welcome $fsname</h3>This is $webname.<p>Your password is <b>$passw.</b></p>
	<p>Thank you.</p>";
		$mailback="noreply@".getDomain($weburl);
		
		$from="$webname <$mailback>";
		$to = $email;
		$subject = "Welcome to $webname";
		$message = $dsemail . ""; 
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$from."\r\nReply-To: ".$from;
		//send the email
		//$mail_sent = @mail($to, $subject, $message, $headers );
		$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);
		
		//$smsmessage="Welcome, $fsname. Your password is $passw. You have received Free bulk SMS. Load your EDS wallet with a minimum of N2,500 and start sharing data now!";
		//$telluser=notifysms($phone,$smsmessage,'EasyData');
		//$telluser=sendmessage('Easy Data Share',$smsmessage,$phone,$maph,$mapk);
		
		unset($_SESSION["captcha_code"]);
		$_SESSION[$sessname4]="<font color=blue>Registration successful!</font>";
			header("location: login");	
	}
	elseif($gtype=='resetacct1'){
		@$email=mysqli_real_escape_string($wole,$email);
		@$phone=mysqli_real_escape_string($wole,$phone);
		@$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$pass1=(int)date('his')*3;
		$pass=hash('snefru',$pass1);
		
		if($email=='info@easydatashareng.com'){
			echo "failed|Cannot reset demo account!";
			exit;
		}
		
		$qs=mysqli_query($wole,"select * from users where email='$email' and phone='$phone'");
		if(mysqli_num_rows($qs)==1){
			$qu=mysqli_query($wole,"update users set pass='$pass' where email='$email'");
			$to          = $email;
			$mailback	 ="noreply@".getDomain($weburl);
			$from        = "$webname <$mailback>";
			$subject     = "Your Password Reset";
		
			$html_content = "<h3>Your Password Reset was successful</h3>
					 <p>Your new password: <b>$pass1</b></p>					
					<p>Thanks for using $webname</p>";
		
			//call multi_attach_mail() function and pass the required arguments
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: ".$from."\r\nReply-To: ".$from;
	
			//send the email
			//$mail_sent = @mail($to, $subject, $html_content, $headers );
			$bsend=sendemailnow($to,$subject,$html_content,$weburl,$webname);
			
			echo "success|Your password has been reset. Please check your email.";
		}
		else{
			echo "failed|Invalid account details!";
			exit;
		}
		
		
	}
	elseif($gtype=='loaduser'){
		@$uname=mysqli_real_escape_string($wole,$uname);
		$qs=mysqli_query($wole,"select * from users where uname='$uname'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);
			$uname=$rs['uname'];	
			$fname=$rs['fname'];	
			$bal=$rs['bal'];
			$smsbonus=$rs['smsbonus'];
			$expdate=date('d-m-Y',strtotime($rs['expdate']));
			$smsinfo="N".$smsbonus." (Expires: $expdate)";	
			//$price=$rs['price'];	
			$price=570;
			echo "success|$fname|$price|$bal|$smsinfo";
		}
		else{
			echo "failed|Invalid login";
		}
	}
	elseif($gtype=='changepass'){
		@$uname=mysqli_real_escape_string($wole,$uname);
		if($uname=='demo'){
			echo "failed|Cannot update demo account!";
			exit;
		}
		
		@$pass=mysqli_real_escape_string($wole,$pass);
		$pass=hash('snefru',$pass);
		$qs=mysqli_query($wole,"update users set pass='$pass' where uname='$uname'");
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname'");
		
		echo "success|Password changed successfully!";
	}
	elseif($gtype=='transferto'){
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		$pass=hash('snefru',$pass);
		
		@$phone=mysqli_real_escape_string($wole,$phone);
		@$amt=mysqli_real_escape_string($wole,$amt);
		
		if($amt<100){
			echo "failed|Minimum transfer is N100";
			exit;
		}
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$bal=$rs['bal'];
			$uphone=$rs['phone'];
			$smsbonus=$rs['smsbonus'];
			$expdate=date('d-m-Y',strtotime($rs['expdate']));
			$smsinfo="N".$smsbonus." (Valid till: $expdate)";
			
			if($uphone==$phone){
				echo "failed|You cannot transfer funds to yourself.";
				exit;
			}
			
			if($bal>=$amt){
				//get user
				$qu=mysqli_query($wole,"select * from users where phone='$phone'");
				if(mysqli_num_rows($qu)==1){
					$ru=mysqli_fetch_array($qu);
					$recv=$ru['fname'];
					$recvapp=$ru['approved'];
					$irecvbal=$ru['bal'];
					$recvuname=$ru['uname'];
					
					if($recvapp=='y'){
						$recvbal=$irecvbal+$amt;
						$qud=mysqli_query($wole,"update users set bal='$recvbal' where uname='$recvuname'");			
					
						$id=0;
						$dt=date('Y-m-d H:i:s');
						@$batch=uniqid();
						$transferfrom="Transfer $amt from $fname";					
						$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$recvuname','$batch','Transfer from','$transferfrom','$phone','$amt','$irecvbal','$recvbal','completed')");
							
						$id=0;
						$dt=date('Y-m-d H:i:s');
						@$batch=uniqid();
						$transferto="Transfer $amt to $recv";
						$nbal=$bal-$amt;	
						
						$qua=mysqli_query($wole,"update users set bal='$nbal' where uname='$uname'");					
						$qm=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','Transfer to','$transferto','$uphone','$amt','$bal','$nbal','completed')");										
						
						echo "success|Transfer successful|$bal|$smsinfo";
						//echo "success|Transfer successful!";
						
					}
					else{
										
						echo "failed|This user cannot receive funds because the registration has not been completed.";
					}
				}
				else{
					//notify prospect
					echo "failed|Phone number not registered on $webname";
				}
			}
			else{
				echo "failed|Insufficient balance!";
			}
		}	
		else{
			echo "failed|authentication failed!";
		}
	}
	elseif($gtype=='cancel-data'){
		@$dataf=mysqli_real_escape_string($wole,$dataf);
		$url="http://easydatashareng.com/http/canceldata.php?tid=$dataf";
		@$str = murl_get_contents($url);
		@$pos=$sdk[0];
		$msg=$sdk[1];
		
		if($pos == '1000'){
			$rfurl=$weburl."/refresh";
			@$str = murl_get_contents($rfurl);
			echo "success|$msg";
		}
		else{
			echo "failed|$msg";
		}
		
	}
	elseif($gtype=='airtime'){
		@$uname=$_SESSION[$sessname];
		@$pass=$_SESSION[$sesspass];
		
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		@$phone=mysqli_real_escape_string($wole,$mobile);
		@$selnetwork=mysqli_real_escape_string($wole,$selnetwork);
		@$network=mysqli_real_escape_string($wole,$network);
		@$amt=(int)$amount;
		
		$pass=hash('snefru',$pass);
		
		sleep(rand(1,3));
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$email=$rs['email'];
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
							
			//$price=$rs['price'];			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			$mvtu=$rsb['mtnvtu'];
			$gvtu=$rsb['glovtu'];
			$avtu=$rsb['airtelvtu'];
			$evtu=$rsb['etivtu'];
			$mtnss=$rsb['mtnss'];
			
			
			if($network=='5'){
				$reqamt=$amt-($mvtu*$amt/100);
			}
			elseif($network=='6'){
				$reqamt=$amt-($gvtu*$amt/100);
			}	
			elseif($network=='1'){
				$reqamt=$amt-($avtu*$amt/100);
			}		
			elseif($network=='2'){
				$reqamt=$amt-($evtu*$amt/100);
			}
			elseif($network=='30'){
				$reqamt=$amt-($mtnss*$amt/100);
			}
			
			if($reqamt>$bal){
				//echo "failed|Insufficient balance";
				$_SESSION[$sessname2]="<font color=red>Insufficient balance!</font>";
			}
			else{	
			    if($network=='30'){
			        $url="https://mobileairtimeng.com/httpapi/msharesell?userid=$airuser&pass=$airpass&phone=$phone&amt=$amt";
			    }
    			else{				
    				$url="https://mobileairtimeng.com/httpapi/?userid=$airuser&pass=$airpass&network=$network&phone=$phone&amt=$amt";
    			}
				@$str = murl_get_contents($url);
				$sdk=explode("|",$str);
				
				@$pos=$sdk[0];
				if($pos == '100' || empty($pos)){
					@$status='Recharge successful';
					$batch=uniqid();
					
					$qb=mysqli_query($wole,"select * from users where uname='$uname'");
					$rb=mysqli_fetch_array($qb);
					$bal=$rb['bal'];
					
					$reqbal=$bal-$reqamt;					
					$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
					
					$id=0;
					$dt=date('Y-m-d H:i:s');
					$info=$selnetwork. " ".$amt;
					
					$dtime=date('Y-m-d H:i:s');
					$q=mysqli_query($wole,"insert into transactions values ('$id','$dtime','$uname','$batch','airtime','$info','$phone','$reqamt','$bal','$reqbal','$status')");
									
					//send email notification
					$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Phone:</b> $phone<br><b>Description:</b> $info<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
	<p>Thank you.</p>";					
					$to = $email;
					$subject = "Airtime Topup Transaction";
					$message = $dsemail . ""; 
					
					$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
					
					$status1=ucwords($status);							
					//echo "success|$status";	
					$_SESSION[$sessname2]="<font color=blue>Recharge successful</font>";
				}
				else{
					if (isset($sdk[1]) && !empty($sdk[1])){
						$tmg=$sdk[1];
					}
					else{
						$tmg="An error occured!";
					}
					//echo "failed|$tmg";						
					$_SESSION[$sessname2]="<font color=red>$tmg</font>";
				}				
			}			
		}
		else{
			//echo "failed|user authentication failed";
			$_SESSION[$sessname2]="<font color=red>Authentication failed!</font>";
		}
		header("location:airtime");
	}
	elseif($gtype=='waec'){
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		
		$pass=hash('snefru',$pass);
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$email=$rs['email'];
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
							
			//$price=$rs['prifsce'];			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			$reqamt=$rsb['waec'];				
			
			if($reqamt>$bal){
				echo "failed|Insufficient balance";
			}
			else{				
				//$url="https://mobileairtimeng.com/httpapi/?userid=$airuser&pass=$airpass&network=$network&phone=$phone&amt=$amt";
				$url="https://mobileairtimeng.com/httpapi/waecdirect.php?userid=$airuser&pass=$airpass";
				@$str = murl_get_contents($url);
				$sdk=explode("|",$str);
				
				@$pos=$sdk[0];
				if($pos == '100'){
					@$status="Successful";
					$sno=$sdk[1];
					$pin=$sdk[2];
					$batch=uniqid();
					
					$qb=mysqli_query($wole,"select * from users where uname='$uname'");
					$rb=mysqli_fetch_array($qb);
					$bal=$rb['bal'];
					
					$reqbal=$bal-$reqamt;					
					$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
					
					$id=0;
					$dt=date('Y-m-d H:i:s');
					$info="WAEC, PIN: $pin";
					$bmsg="PIN: $pin, S/N: $sno";
					
					$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','waec','$info','owner','$reqamt','$bal','$reqbal','$status')");
									
					//send email notification
					$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>PIN:</b> $pin<br><b>S/N:</b> $sno<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
	<p>Thank you.</p>";					
					$to = $email;
					$subject = "WAEC Result PIN";
					$message = $dsemail . ""; 
					
					$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
					
					$status1=ucwords($status);							
					echo "success|$bmsg";	
				}
				else{
					if (isset($sdk[1]) && !empty($sdk[1])){
						$tmg=$sdk[1];
					}
					else{
						$tmg="An error occured!";
					}
					echo "failed|$tmg";						
				}				
			}			
		}
		else{
			echo "failed|user authentication failed";
		}
	}
	elseif($gtype=='cabletvs'){
		@$uname=$_SESSION[$sessname];
		@$pass=$_SESSION[$sesspass];
		
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		@$phone=mysqli_real_escape_string($wole,$phone);
		@$smartno=mysqli_real_escape_string($wole,$smartno);
		@$cabletype=mysqli_real_escape_string($wole,$cabletype);
		@$amt=(int)$amount;
		
		$pass=hash('snefru',$pass);
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$email=$rs['email'];
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
							
			//$price=$rs['price'];			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			$bill=$rsb['bill'];
			
			$reqamt=$amt+$bill;				
			
			if($cabletype=="STARTIMES"){
				$goback="startimes";
			}
			elseif($cabletype=="GOTV"){
				$goback="gotv";
			}
			elseif($cabletype=="DSTV"){
				$goback="dstv";
			}
			
			if($reqamt>$bal){
				//echo "failed|Insufficient balance";
				$_SESSION[$sessname10]="<font color=red>Insufficient balance</font>";
			}
			else{
				if($cabletype=="STARTIMES"){
					$url="https://mobileairtimeng.com/httpapi/startimes.php?userid=$airuser&pass=$airpass&phone=08139235588&amt=$amt&smartno=$smartno&allowance=1";				
				}
				elseif($cabletype=="GOTV"||$cabletype=="DSTV"){
					$btype=strtolower($cabletype);
					
					$goname=trim(filter_var($goname, FILTER_SANITIZE_STRING));
					$goname = preg_replace('/\s+/', '', $goname);
					$invoice=(int)$goinvoice;
					$gocustno=filter_var($gocustno, FILTER_SANITIZE_STRING);
					$url="https://mobileairtimeng.com/httpapi/multichoice.php?userid=$airuser&pass=$airpass&phone=08139235588&amt=$amt&smartno=$smartno&allowance=1&customer=$goname&invoice=$invoice&billtype=$btype&customernumber=$gocustno";
				}
				
										
				@$str = murl_get_contents($url);				
				$sdk=explode("|",$str);
				
				@$pos=$sdk[0];
				if($pos == '100' || empty($pos)){
					@$status='Recharge successful';
					$batch=uniqid();
					
					$qb=mysqli_query($wole,"select * from users where uname='$uname'");
					$rb=mysqli_fetch_array($qb);
					$bal=$rb['bal'];
					
					$reqbal=$bal-$reqamt;					
					$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
					
					$id=0;
					$dt=date('Y-m-d H:i:s');
					$info= "$cabletype $amt $smartno" ;
					
					$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','$cabletype','$info','$smartno','$reqamt','$bal','$reqbal','$status')");
									
					//send email notification
					$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Smart Card:</b> $smartno<br><b>Description:</b> $info<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
	<p>Thank you.</p>";					
					$to = $email;
					$subject = "$cabletype Recharge Notification";
					$message = $dsemail . ""; 
					
					$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
					
					$status1=ucwords($status);
					$_SESSION[$sessname10]="<font color=blue>$status</font>";;							
						
				}
				else{
					if (isset($sdk[1]) && !empty($sdk[1])){
						$tmg=$sdk[1];
					}
					else{
						$tmg="An error occured!";
					}
					$_SESSION[$sessname10]="<font color=red>$tmg</font>";						
				}				
			}			
		}
		else{
			$_SESSION[$sessname10]="<font color=red>user authentication failed</font>";
		}
		header("location: $goback");
	}
	elseif($gtype=='databundles'){
		@$uname=$_SESSION[$sessname];
		@$pass=$_SESSION[$sesspass];
		
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		@$phone=mysqli_real_escape_string($wole,$mobile);
		@$selnetwork=mysqli_real_escape_string($wole,$netw);
		@$network=mysqli_real_escape_string($wole,$network);
		@$amt=$bundles;
		
		$pass=hash('snefru',$pass);
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$email=$rs['email'];
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
							
			//$price=$rs['price'];			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			$mvtu=$rsb['mtnvtu'];
			$gvtu=$rsb['glovtu'];
			$gdata=$rsb['glodata'];
			$avtu=$rsb['airtelvtu'];
			$evtu=$rsb['etivtu'];
			
			if($network=='5'){
				$reqamt=$amt-($mvtu*$amt/100);
			}
			elseif($network=='6'){
				$reqamt=$amt-($gdata*$amt/100);
			}	
			elseif($network=='1'){
				$reqamt=$amt-($avtu*$amt/100);
			}		
			elseif($network=='2'){
				$reqamt=$amt-($evtu*$amt/100);
			}					
			
			if($reqamt>$bal){
				//echo "failed|Insufficient balance";
				$_SESSION[$sessname2]="<font color=red>Insufficient balance!</font>";
			}
			else{								
				$url="https://mobileairtimeng.com/httpapi/datatopup.php?userid=$airuser&pass=$airpass&network=$network&phone=$phone&amt=$amt&allowance=1";
				@$str = murl_get_contents($url);
				$sdk=explode("|",$str);
				
				@$pos=$sdk[0];
				if($pos == '100' || empty($pos)){
					@$status='Recharge successful';
					$batch=uniqid();
					
					$qb=mysqli_query($wole,"select * from users where uname='$uname'");
					$rb=mysqli_fetch_array($qb);
					$bal=$rb['bal'];
					
					$reqbal=$bal-$reqamt;					
					$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
					
					$id=0;
					$dt=date('Y-m-d H:i:s');
					$info=$selnetwork. " DATA ".$amt;
					
					$dtime=date('Y-m-d H:i:s');
					$q=mysqli_query($wole,"insert into transactions values ('$id','$dtime','$uname','$batch','databundles','$info','$phone','$reqamt','$bal','$reqbal','$status')");
									
					//send email notification
					$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Phone:</b> $phone<br><b>Description:</b> $info<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
	<p>Thank you.</p>";					
					$to = $email;
					$subject = "$selnetwork Data Topup";
					$message = $dsemail . ""; 
					
					$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
					
					$status1=ucwords($status);							
					$_SESSION[$sessname2]="<font color=blue>Recharge successful</font>";	
				}
				else{
					if (isset($sdk[1]) && !empty($sdk[1])){
						$tmg=$sdk[1];
					}
					else{
						$tmg="An error occured!";
					}
					//echo "failed|$tmg";						
					$_SESSION[$sessname2]="<font color=red>$tmg</font>";
				}				
			}			
		}
		else{
			//echo "failed|user authentication failed";
			$_SESSION[$sessname2]="<font color=red>Authentication failed!</font>";
		}
		header("location:databundles");
	}
	elseif($gtype=='data-share'){
		@$uname=$_SESSION[$sessname];
		@$pass=$_SESSION[$sesspass];
	
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		@$phone=mysqli_real_escape_string($wole,$mobile);
		$datasize=(int)$mobiledata;
		
		$pass=hash('snefru',$pass);
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$email=$rs['email'];	
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
			$smsbonus=$rs['smsbonus'];
			$expdate=date('d-m-Y',strtotime($rs['expdate']));
			$smsinfo="N".$smsbonus." (Valid till: $expdate)";
				
			//$price=$rs['price'];			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			$mdata=$rsb['dataprice'];
			$mdata1=$rsb['dataprice1'];
			$mdata2=$rsb['dataprice2'];
			if($datasize==1){
				$reqamt=$mdata;
			}
			elseif($datasize==2){
				$reqamt=$mdata1;
			}
			elseif($datasize==5){
				$reqamt=$mdata2;
			}
			
			$amount=$reqamt;
			
			if($reqamt>$bal){
				//echo "failed|Insufficient balance";
				$_SESSION[$sessname2]="<font color=red>Insufficient balance</font>";
			}
			else{
				$dsize=(int)$datasize *1000;
				//$url="http://easydatashareng.com/http/?userid=$maph&pass=$mapk&phone=$phone&datasize=$dsize";
				$url="https://mobileairtimeng.com/httpapi/datashare.php?userid=$airuser&pass=$airpass&network=1&phone=$phone&datasize=$dsize";
				
				@$str = murl_get_contents($url);
				$sdk=explode("|",$str);
				
				@$pos=$sdk[0];
				if($pos == '100' || empty($pos) ){
					@$status=$sdk[1];
					@$batch=$sdk[2];
					
					$qb=mysqli_query($wole,"select * from users where uname='$uname'");
					$rb=mysqli_fetch_array($qb);
					$bal=$rb['bal'];
					
					$reqbal=$bal-$reqamt;					
					$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
					
					$id=0;
					$dt=date('Y-m-d H:i:s');
					$datagb="MTN ".$datasize."GB";
					
					$dtime=date('Y-m-d H:i:s');
					$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','data share','$datagb','$phone','$reqamt','$bal','$reqbal','$status')");					
					
					$status1=ucwords($status);
					
					//send email notification
					$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Phone:</b> $phone<br><b>Description:</b> $datagb<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
	<p>Thank you.</p>";					
					$to = $email;
					$subject = "MTN Data Transaction";
					$message = $dsemail . ""; 
					
					$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);
												
					//echo "success|$status|$reqbal|$smsinfo";	
					$_SESSION[$sessname2]="<font color=blue>Request successful</font>";
				}
				else{
					if (isset($sdk[1]) && !empty($sdk[1])){
						$tmg=$sdk[1];
					}
					else{
						$tmg="An error occured!";
					}
					//echo "failed|$tmg";						
					$_SESSION[$sessname2]="<font color=red>$tmg</font>";
				}				
			}					
		}
		header("location:datashare");
	}
	elseif($gtype=='sendsms1' || $gtype=='sendsms2' ){
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);
		@$smsemail=mysqli_real_escape_string($wole,$smsemail);
		
		$pass=hash('snefru',$pass);
		
		//check international format numbers
		$smsto=convphone($smsto);
		
		$sender=$_POST['smsfrom'];
		$sender1=trim(mysqli_real_escape_string($wole,$sender));
		
		$smsmess=stripslashes($_POST['smsmsg']);
		$smsmess1=trim(mysqli_real_escape_string($wole,$_POST['smsmsg']));
		$message=$smsmess;
		
		$no_unts=strlen($message)/160;
		if($no_unts<1){
			$no_unts=1;	
		}	
		else{
			$no_unts=ceil($no_unts);	
		}
		$smspage=$no_unts;
		
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$rs=mysqli_fetch_array($qs);	
			$fname=$rs['fname'];	
			$bal=$rs['bal'];
			$usertype=$rs['usertype'];
			$smsbonus=$rs['smsbonus'];
			$expdate=date('d-m-Y',strtotime($rs['expdate']));
			$dexpdate=$rs['expdate'];
			
			$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
			$rsb=mysqli_fetch_array($sb);
			if($gtype=='sendsms1'){
				$msmsprice=$rsb['smsprice'];
				$route=1;
			}
			elseif($gtype=='sendsms2'){
				$msmsprice=$rsb['smsprice1'];
				$route=2;
			}
			$costpersms=$msmsprice*$smspage;
			
			
			//check number of units and receivers
			$mypeople=explode(",",$smsto);
			$tot_people=count($mypeople);
			$tot_sms=$tot_people*$smspage*$msmsprice;			
			$mycost=1.95*$smspage;
			$reqbal=$bal-$tot_sms;
			
			$today=date('Y-m-d');			
			
			$batchid=substr(strtoupper($uname),0,2).rand(10,999999)."-".substr(hash('sha512',date('Y-m-d h:i:s')),40);
			$smslimit=200;
			
			$allphones = explode(",", $smsto);
			$totalphones= count($allphones);
			
			
			$credit=$bal;
			$doption=0;
			
			//check expiration here
			
			if($tot_sms<=$credit && ($credit>1)){
				//send from bonus
				if($totalphones>$smslimit){
					//batch blast
					$available=$totalphones-1;
					$finish=0;
					
					//breakup
					$needle = ",";
					$lastPos = 0;
					$positions = array();
					
					while (($lastPos = strpos($smsto, $needle, $lastPos))!== false) {
						$positions[] = $lastPos;
						$lastPos = $lastPos + strlen($needle);
					}
					
					$startp=0;
					$nextmove=$smslimit-1;
					$endp=$positions[$nextmove]-1;
					
					$completeresp='';
					while($nextmove<=$available){
						$charp=$endp-$startp+1;
						$sendto=substr($smsto,$startp,$charp);
						
						//push sms
						$smsresponse=sendmessage($sender,$message,$sendto,$airuser,$airpass,$route);
						$completeresp=$completeresp.",".$smsresponse;
						
						$nextmove+=$smslimit;
						if($nextmove<$available){
							$startp=$endp+2;
							$endp=$positions[$nextmove]-1;
						}
						else{
							$startp=$positions[$available-1]+1;
							$sendto=substr($smsto,$startp);
							$smsresponse=sendmessage($sender,$message,$sendto,$airuser,$airpass,$route);
							$completeresp=$completeresp.",".$smsresponse;
						}		
					}
					$completeresp=substr($completeresp,1);
					$fsms=catchthebulksms($completeresp,$batchid,$uname,$sender1,$smsmess1,$costpersms,$credit,$doption,$smsemail,$wole,$mycost);
				}
				else{
					//single blast sms
					$smsresponse=sendmessage($sender,$message,$smsto,$airuser,$airpass,$route);
					$fsms=catchthebulksms($smsresponse,$batchid,$uname,$sender1,$smsmess1,$costpersms,$credit,$doption,$smsemail,$wole,$mycost);
				}
				$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
				if(mysqli_num_rows($qs)==1){
					$rs=mysqli_fetch_array($qs);	
					$fname=$rs['fname'];	
					$bal=$rs['bal'];
					$usertype=$rs['usertype'];
					$smsbonus=$rs['smsbonus'];
					$expdate=date('d-m-Y',strtotime($rs['expdate']));
					$smsinfo="N".$smsbonus." (Valid till: $expdate)";
				}
				//@$batch=date('ydmhis');
				$fsms1=explode("|",$fsms);
				$dresp=$fsms1[0];
				$reqamt=$fsms1[1];
				
				$id=0;
				$dt=date('Y-m-d H:i:s');
				$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batchid','bulksms','$dresp','','$tot_sms','$bal','$reqbal','completed')");
					
					$status=$dresp;
					$status1=ucwords($fsms);							
					echo "success|$status|$bal|$smsinfo";				
				
			}
			else{
				echo "failed|Insufficient balance";
				exit;
			}
						
		}			
	}
	elseif($gtype=='resetapiweb'){
		@$uname=mysqli_real_escape_string($wole,$uname);
		@$pass=mysqli_real_escape_string($wole,$pass);		
		
		$pass=hash('snefru',$pass);
		
		$qs=mysqli_query($wole,"select * from users where uname='$uname' and pass='$pass'");
		if(mysqli_num_rows($qs)==1){
			$row=mysqli_fetch_array($qs);
			$id=$row['id'];
			$email=$row['email'];
			$fname=$row['fname'];
			
			$dkey=date('dmhis');
			$dky=substr(hash('whirlpool',$dkey),1,15);
			$qu=mysqli_query($wole,"update users set mapk='$dky' where uname='$uname'");
			
			$msg="success|$dky";						
			echo $msg;
		}
		else{
			echo "failed|invalid authentication";	
		}
		
	}
	
}
else{
	echo "failed";
}


?>

<?php

function convphone($thenos){
$thenos = str_replace(' ', '', $thenos);
$thenos = preg_replace( "/\r|\n/", ",", $thenos);

$w = explode(",", $thenos);
$norec=count($w);

$jm=0;
$smstos="";

for($i=0;$i<$norec;$i++){
	if(substr($w[$i],0,3)!="234"){
		if(strlen($w[$i])==11){
			$np="234".substr($w[$i],1,strlen($w[$i]));
			$smstos.=",".$np;
		}
		else{
			$jm++;		
		}
	}
	else{
		if(substr($w[$i],0,3)=="234"){
			if(strlen($w[$i])==13){
				$np=$w[$i];
				$smstos.=",".$np;
			}
			else{
				$jm++;		
			}
		}
		else{
			$jm++;
		}
	}
}

$convnos=substr($smstos,1,strlen($smstos));
return $convnos;

}

function url_get_contents ($Url,$data) {
	if (!function_exists('curl_init')){
	die('CURL is not installed!');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function sendmessage($sender,$message,$recv,$m1,$m2,$route){
	$message=urlencode($message);
	$sender=urlencode($sender);
	$data= 'username=$m1&password=$m2' . '&message=' . $message. '&mobile=' . $recv. '&sender=' . $sender;
	//$url="https://www.mobileairtimeng.com/smsapi/bulksms.php";
	
	$postdata = http_build_query(
    array(
        'username' => $m1,
        'password' => $m2,
		'message' => urldecode($message),
		'mobile' => $recv,
		'sender' => urldecode($sender),
		'route' => $route,
		'vtype' => 1,
		)
	);
	
	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		)
	);
	
	$context  = stream_context_create($opts);
	$result = file_get_contents('https://www.mobileairtimeng.com/smsapi/bulksms.php', false, $context);

	return $result;
}

function catchthebulksms($b,$batch,$user,$title,$msg,$cost,$crd,$dopt,$email,$ws,$mycost){
	$msgback='';
	$dopt=2;
	$dono=0;
	$counter=0;
	$getbatchsms=explode(",",$b);
	$totalsms=count($getbatchsms);
	$totalcost=0;
	$dt=date('d-m-Y');
	$tomail="<table width='80%' cellpadding=0 cellspacing=0 border=1>";
	$tomail.="<tr><th align='center'>S/N</th><th align='center'>Date</th><th align='center'>Phone</th><th style='word-wrap: break-word; width: 50%'>Message</th><th align='center'>Status</th></tr>";
	$j=0;
	if($totalsms>1){
		for($i=0;$i<$totalsms;$i++){
			$splitmess=explode("|",$getbatchsms[$i]);	
			$response=$splitmess[2];
			$phone=$splitmess[1];
			if($splitmess[0]=='2001'){
				$counter++;
				$msgback="Message sent to $counter recipient(s)!";
				$dono=1;
			}
			
			if($dono==0){
				$msgback="$response";
			}
			//log sms
			$id=0;
			$dt=date('d-m-Y');
			$j++;
			$tomail.="<tr><td align='center'>$j</td><td align='center'>$dt</td><td align='center'>$phone</td><td style='word-wrap: break-word; width: 50%'>$msg</td><td align='center'>$response</td></tr>";	
			$qs=mysqli_query($ws,"insert into gbogbotxt values('$id','$dt','$batch','$user','$phone','$title','$msg','$response')");	
		}
		$id=0;
		$dt=date('d-m-Y');
		$totalcost=$cost*$counter;
		$amtpt=$mycost*$counter;
		$q=mysqli_query($ws,"insert into gbogbotxtsum values('$id','$dt','$batch','$user','$title','$msg','$counter','$totalcost')");
	}
	else{
		$splitmess=explode("|",$b);		
		$response=$splitmess[2];
		$phone=$splitmess[1];
		if($splitmess[0]=='2001'){
			$counter++;
			$msgback="Message sent to $counter recipient(s)!";
			$dono=1;
		}
		else{
			$msgback="$response";
			$dono=0;
		}
		
		//log sms
		$id=0;
		$dt=date('d-m-Y');
		$totalcost=$cost*$counter;
		$amtpt=$mycost*$counter;
		$qs=mysqli_query($ws,"insert into gbogbotxt values('$id','$dt','$batch','$user','$phone','$title','$msg','$response')");
		$q=mysqli_query($ws,"insert into gbogbotxtsum values('$id','$dt','$batch','$user','$title','$msg','$counter','$totalcost')");
		$tomail.="<tr><td align='center'>1</td><td align='center'>$dt</td><td align='center'>$phone</td><td style='word-wrap: break-word; width: 50%'>$msg</td><td align='center'>$response</td></tr>";
	}
	
	if($dono==1){
		//reduce
		$bal=$crd-$totalcost;
		if($dopt==1){
			$qp=mysqli_query($ws,"update users set smsbonus='$bal' where uname='$user'");
		}
		elseif($dopt==2){
			$qp=mysqli_query($ws,"update users set bal='$bal' where uname='$user'");
			
			$gain=$totalcost-$amtpt;
			$dtere=date('Y-m-d');
			$id=0;
		}
		
	}
	$tomail.="</table>";
	//send email here
	

	//send the email
	//$mail_sent = @mail($to, $subject, $html_content, $headers );	
	return "$msgback|$totalcost";
}


function murl_get_contents ($Url) {
if (!function_exists('curl_init')){
die('CURL is not installed!');
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $Url);
curl_setopt($ch, CURLOPT_TIMEOUT, 40);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);
return $output;
}



?>