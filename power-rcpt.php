<?php
session_start();
ini_set('max_execution_time', 300);
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/login";
	header("location: $weburl/login");
	exit;	
}


@$refno=mysqli_real_escape_string($wole,$_GET['refno']);

if($refno!=''){
	$q=mysqli_query($wole,"select * from power_trans where refno='$refno'");
	if(mysqli_num_rows($q)==1){
		$r=mysqli_fetch_array($q);
		$dtime=date('d-m-Y h:i:sA', strtotime($r['dtime']));
		$disco=strtoupper($r['disco']);
		$meterno=$r['meterno'];
		$pincode=$r['pincode'];
		$pinmessage=$r['pinmessage'];		
		
		if($pincode==''){
			echo "<div style='border: solid #000000 thin; padding: 5px; width: 600px'><h2>$disco</h2><p><b>DATE:</b><br>$dtime<br><br><b>METER NO/CUSTOMER ACCOUNT:</b><br>$meterno<br><br><b>DESCRIPTION:</b><br>$pinmessage<br><br><input type='button' id='but' onclick='printd()' value='PRINT'><p></div>";
		}
		else{
			echo "<div style='border: solid #000000 thin; padding: 5px; width: 600px'><h2>$disco</h2><p><b>DATE:</b><br>$dtime<br><br><b>METER NO/CUSTOMER ACCOUNT:</b><br>$meterno<br><br><b>DESCRIPTION:</b><br>Pin - $pincode<br>$pinmessage<br><br><input type='button' id='but' onclick='printd()' value='PRINT'><p></div>";
		}		
	}
	else{
		echo "<h2>Invalid reference</h2>";
	}
}
else{
	header('location: $weburl');
}

mysqli_close($wole);



?>
<script language="javascript">
function printd(){
	document.getElementById('but').style.display='none';
	window.print();
	document.getElementById('but').style.display='block';
}
</script>