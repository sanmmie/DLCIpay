<?php
session_start();
set_time_limit(200);
include_once 'isopoa.php';
include_once 'config.php';
include_once 'refresh.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/airtime";
	header("location: $weburl/login");
	exit;	
}

@$uname=$_SESSION[$sessname];
@$pass=$_SESSION[$sesspass];

$q=mysqli_query($wole,"select * from users where uname='$uname'");
if(mysqli_num_rows($q)!=1){
	header("location: $weburl/login");
	exit;
}
$rs=mysqli_fetch_array($q);
$bal=$rs['bal'];
$fname=$rs['fname'];
$usertype=$rs['usertype'];

$msg='';
if(isset($_SESSION['bulkvtu'])){
	$msg=$_SESSION['bulkvtu'];
	unset($_SESSION['bulkvtu']);
}


if(isset($_POST['tbulk']) && ($_POST['tbulk']=='bulk') ){
	extract ($_POST);
	if(empty($phones)){
		$_SESSION['bulkvtu']="<p><font color=red><b>No phone number available!</b></font></p>";
		header("location:bulkairtime");
		exit;
  }
	if(empty($amts)){
		$_SESSION['bulkvtu']="<p><font color=red><b>No amount available!</b></font></p>";
		header("location:bulkairtime");
		exit;
  }
	$N = count($phones); 
   	$rpt="";
	$err='';
	$count=0;
	$failed=0;
	$fnet=0;
		
	$sumamt=0;
	//get sum
	for($i=0; $i < $N; $i++)
	{
		$sumamt+=$amts[$i];	
	}
	
	if($sumamt>$bal){
		$_SESSION['bulkvtu']="<p><font color=red><b>Insufficient balance to complete the requests!</b></font></p>";
		header("location:bulkairtime");
		exit;
	}
		
	for($i=0; $i < $N; $i++)
	{
		$err='';
		$net=$networks[$i];
		$network=$networks[$i];
		$mphone=$phones[$i];
		$amount=$amts[$i];
	  	$amt=$amount;
		
		if(substr($mphone,0,1)!="0"){
			$mphone="0".$mphone;
		}
		$mphone=str_replace(' ', '', $mphone);
		if(strlen($mphone)<11 || strlen($mphone)>11){
			$err="Invalid Phone Number";
		}
		if($amount=="" || !is_numeric($amount)){
			$err="Invalid Amount!";
		}
		
		//$price=$rs['price'];			
		$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
		$rsb=mysqli_fetch_array($sb);
		$mvtu=$rsb['mtnvtu'];
		$gvtu=$rsb['glovtu'];
		$avtu=$rsb['airtelvtu'];
		$evtu=$rsb['etivtu'];
		
		if($network=='15'){
			$reqamt=$amt-($mvtu*$amt/100);
			$selnetwork='MTN';
		}
		elseif($network=='6'){
			$reqamt=$amt-($gvtu*$amt/100);
			$selnetwork='GLO';
		}	
		elseif($network=='1'){
			$reqamt=$amt-($avtu*$amt/100);
			$selnetwork='Airtel';
		}		
		elseif($network=='2'){
			$reqamt=$amt-($evtu*$amt/100);
			$selnetwork='9Mobile';
		}
			
		if($err==''){
			//$url="https://mobileairtimeng.com/httpapi/?userid=$userid&pass=$pass&network=$net&phone=$mphone&amt=$amount";
			$dd=substr(date('D'),0,1).date('is');
			$batch="$dd-".uniqid();
			
			$url="https://mobileairtimeng.com/httpapi/?userid=$airuser&pass=$airpass&network=$net&phone=$mphone&amt=$amt&user_ref=$batch";
			@$str = file_get_contents($url);			
			$sdk=explode("|",$str);
			$pos=$sdk[0];
			if($pos == '100' || empty($pos)){		
				$count++;
				$rpt.="$count. $mphone - N$amount<br>";	
				
				$qs=mysqli_query($wole,"select * from users where uname='$uname'");
				$rs=mysqli_fetch_array($qs);	
				$bal=$rs['bal'];
				
				$reqbal=$bal-$reqamt;					
				$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
				
				$status='Recharge successful';
				$id=0;
				$dt=date('Y-m-d');
				$info=$selnetwork. " ".$amt;
				
				$dtime=date('Y-m-d H:i:s');
				$q=mysqli_query($wole,"insert into transactions values ('$id','$dtime','$uname','$batch','airtime','$info','$mphone','$reqamt','$bal','$reqbal','$status')");
			}
			else{
				if($pos=='103'){
					break;
				}
				elseif($pos=='108'){
					if($fnet==''){
						$fnet=$net;						
					}
					elseif($fnet==$net){
						$failed++;
					}
					
					if($failed==3){
						break;
					}			
				}
			}
		}
	
	//iteratn	
	}
	$fullreport="Bulk Recharge Report: $count phone no(s) recharged<br>$rpt";
	$_SESSION['bulkvtu']="<p><font color=blue>$fullreport</b></font></p>";
	header("location:bulkairtime");
	exit;
	  
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DLCIpay | Bulk Airtime</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard">DLCIpay | Bulk Airtime</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php include 'sidebar.php' ;?>
  </nav>
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="dashboard">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Bulk Airtime Topup</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Bulk Airtime</h1>
         <?php echo $msg ;?>
		  <p>		  
		<form action="bulkairtime" method="post" name="form1" id="form1" onSubmit="return topup(document.getElementById('howm').value)">		
		<div class="form-group">
			<div class="form-row">
				 <div class="col-md-4">
					 Number of Recharge
					 <input name="howm" type="hidden" id="howm" value=""><input name="tbulk" type="hidden" id="tbulk" value="">
					   <select name="select" class="form-control"  onChange="loadbulk(this.value)">
					   <?php
						echo "<option value=2>2</option>"; 
					   for ($i=4;$i<=30;$i+=2){
							echo "<option value=$i>$i</option>";
					   }
					   ?>
					   </select>
			   </div>
			</div>
			   <div id='bulkitems'></div>
							                 	
				<div class="col-md-3">
					<br>
					<input name="tbut" type="submit" id="tbut" value="Bulk Top up" class="form-control"/>
				</div>
			</div>
	  	</div>
	</form>
	</p>
	
        </div>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
      <div class="container">
        <div class="text-center">
          <small><?php echo $copyright ;?></small>
        </div>
      </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">�</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="logout">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
  </div>
</body>
<script language="javascript">
function topup(){
	var phone=document.getElementById('mobile').value;
	var element=document.getElementById('network');
	var amt = document.getElementById('amount').value;
	
	netname=element.value; 
	
		
	if(phone=='' || phone.length<11 || phone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	if(netname=='0'){
		alert('Select network');
		exit;
	}
	
	if(amt==0 || amt==''){
		alert('Enter amount');
		exit;
	}
		
	
	var dnet = element.options[ element.selectedIndex ].text;
	document.getElementById('selnetwork').value=dnet;
	
	var conf=confirm("Send "+ dnet + " " + amt + " to "+ phone +"?");
	if(conf==0){
		exit;
	}
	
	document.getElementById('sbut').value="Please wait...";
	document.form1.submit();
	
}


var howm;
function loadbulk(a){
	//document.getElementById('bulkitems').innerHTML=a;
	document.getElementById('howm').value=a;
	var dfrm;
	var doptions="<option value=''>Select</option><option value='15'>MTN</option><option value='6'>Glo</option>";
	doptions+="<option value='1'>Airtel</option><option value='2'>Etisalat</option>";
	var j;
	

	dfrm='';
	
	for(var i=0;i<a;i++){
		j=i+1;
		dfrm+="<br><b>ITEM " +j +"</b><br><div class='form-row'><div class='col-md-4'>Phone No<br /><input class='form-control' type='number' name='phones[]' value='' placeholder='Phone No' required></div><div class='col-md-4'>Network<br /><select name='networks[]' id='network' class='form-control' required>"+doptions+"</select></div><div class='col-md-4'>Amount<br /><input type='number' name='amts[]' required class='form-control' /></div></div>";
		
		//dfrm+="<tr><td valign='middle'>"+j+"</td><td><select name='networks[]' id='network'>"+doptions+"</select></td><td><input type='text' name='phones[]' /></td><td><input type='text' name='amts[]' onKeyPress='return isNumber(event)' /></td><td></tr>";
	}
	document.getElementById('bulkitems').innerHTML=dfrm;
}
loadbulk(2);


function topup(b){

var conf=confirm("Are you sure you want to proceed with the Bulk recharge?");
if(conf==0){
	return false
}
document.getElementById('tbulk').value='bulk';
document.getElementById('tbut').value="Please wait...";
//document.form1.submit();
}

</script>

</html>