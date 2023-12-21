<?php

include_once 'isopoa.php';
$gsm=getdatastatus($wole);

function getdatastatus ($wole){
	$fd='Data recharge in progress';
	$fdm='in progress';
	$n=0;
	$p=0;
	$qs=mysqli_query($wole,"select * from transactions where status='$fd' or status='$fdm'");
	if(mysqli_num_rows($qs)>=1){
		while($rs=mysqli_fetch_array($qs)){
			$n++;
			$batch=$rs['batch'];
			$uname=$rs['uname'];
			$amt=$rs['amtch'];
			$url="http://mobileairtimeng.com/httpapi/datastatus.php?batch=$batch";
			@$str = file_get_contents($url);
			if($str=='completed'){
				$p++;
				$bd='Data recharge completed';
				$qu=mysqli_query($wole,"update transactions set status='$bd' where batch='$batch'");
			}
			elseif($str=='failed'){
				$p++;
				$bd='Data recharge cancelled';
				$qu=mysqli_query($wole,"update transactions set status='$bd' where batch='$batch'");
				
				$res=mysqli_query($wole,"select * from users where uname='$uname'");
				$row=mysqli_fetch_array($res);
				$bal=$row['bal'];
				$phone=$row['phone'];
				
				$newbal=$bal+$amt;
				$id=0;
				$dt=date('Y-m-d');
				@$batch=date('ydmhis');
				$dc="Wallet Funding";
				$carr="Account credited with N$amt";
				
				$query="update users set bal='$newbal' where uname='$uname'";
				$res=mysqli_query($wole,$query);
				
				$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','$dc','$carr','$phone','$amt','','completed')");
			}
			
		}	
	}
	return "Found: $n, Fixed: $p";
}

?>