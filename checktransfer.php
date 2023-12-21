<?php
session_start();
ini_set('max_execution_time', 300);
include_once 'isopoa.php';
include_once 'config.php';


$q=mysqli_query($wole,"select * from transactions where transtype='money-transfer' and status='pending'");
if(mysqli_num_rows($q)>=1){
	while($r=mysqli_fetch_array($q)){
		$refno=$r['batch'];
		$uname=$r['uname'];
		$amtch=$r['amtch'];
		
		$url="https://mobileairtimeng.com/money-transfer/statusapi?refno=$refno";		
		@$strh = file_get_contents($url);
		$sdk=explode("|",$strh);
		$pos=$sdk[0];
			
		if($pos == 'success'){
			$qu=mysqli_query($wole,"update transactions set status='$pos' where batch='$refno'");
		}
		elseif($pos == 'failed'){
			$qu=mysqli_query($wole,"update transactions set status='$pos' where batch='$refno'");
			
			//refund user
			$qx=mysqli_query($wole,"select * from users wheren uname='$uname'");
			$rx=mysqli_fetch_array($qx);
			$bal=$rx['bal'];
			$phone=$rx['phone'];
			
			$units=$bal+$amtch;
			$query="update users set bal='$units' where uname='$uname'";	
			$res=mysqli_query($wole,$query);
			
			$id=0;
			$dt=date('Y-m-d');
			$batch=uniqid();
			$dc="Wallet Funding";
			$carr="Reversal for money transfer, N$amtch";
			
			$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','$dc','$carr','$phone','$amtch','','completed')");
				
		}
	}
}


?>