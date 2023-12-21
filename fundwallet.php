<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
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
$phone=$rs['phone'];
$email=$rs['email'];

$msg='';
if(isset($_SESSION['fundwallet'])){
	$mg=explode("|",$_SESSION['fundwallet']);
	$msg=$mg[1];
	$msg="<b>$msg</b>";
	unset($_SESSION['fundwallet']);
}

if(isset($_POST['famt1'])){
	extract ($_POST);
	$paydetails=filter_var($paydetails, FILTER_SANITIZE_STRING);
	$err='';
	if($famt1<500){
		$err.="Minimum deposit/transfer is N500";
	}
	if($err!=''){
		echo "<script>alert('".$err."')</script>";
	}
	else{
		$content = "<p>Deposit from $fname</p><b>$phone $famt1</b><p><b>Description</b><br>$paydetails</p>";
		$masteremail = "noreply@dlcipay.com";	
		$to          = 'sanmi@t-dlc.com';
		$from        = "Deposit Notification <$masteremail>";
		$replyto        = $email;
		$subject	 =	"Deposit $fname ($famt1)";	
		$html_content = $content;
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$from."\r\nReply-To: ".$replyto;
	
		//send the email
		$mail_sent = @mail($to, $subject, $html_content, $headers );
		$_SESSION['fundwallet']="success|Deposit notification submitted!";
		echo "<script>alert('Deposit notification submitted!');window.location='fundwallet';</script>";
	}	
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
  <title>DLCIpay | Fund Wallet</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Fund Wallet</a>
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
        <li class="breadcrumb-item active">Fund Account</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Fund Account</h1>
		  <h3>Bank transfer, Cash deposit</h3>		 
		  <?php echo $fundwallet ;?>
		   <form action="fundwallet" method="post" name="form1" id="form1">
		  	<div class="col-md-8">
			Amount<br />
			<input class="form-control" type="number" name="famt1" id="famt1" value="" placeholder='Enter amount'>
			</div>
			<div class="col-md-8">
			Payment details<br />
			<textarea class="form-control" name="paydetails" id="paydetails" placeholder="Please state sender's account name and bank. If USSD state phone number also. Give details..."></textarea>
			</div>
			<div class="col-md-8">
			<br />
			<input class="form-control" type="button" onClick="cashdeposit()" value="Send Payment Notification">
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
              <span aria-hidden="true">?</span>
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

var fundamt;
var theamt;
var themail;
var phone;
var dataitem;

function cashdeposit(){
	
	var famt=document.getElementById('famt1').value;
	var pdet=document.getElementById('paydetails').value;
	
	if(famt<500){
		alert('Minimum amount via cash deposit is N500');
		exit;
	}
	if(pdet==''){
		alert('Enter payment details.');
		exit;
	}

	document.form1.action='fundwallet';
	document.form1.submit();
	
}

function payonline(){

	fundamt=parseFloat(document.getElementById('famt').value);
	
	if(fundamt<100){
		alert('Minimum amount via ATM is N100');
		exit;
	}
	if(fundamt>9950){
		alert('Maximum funding via ATM is N9,950');
		exit;
	}
	
	theamt=fundamt+(fundamt*0.016);
	document.getElementById('theamt').value=theamt;
	
	document.form2.action='pay-online';
	document.form2.submit();
	//payWithPaystack();
}

</script>

</html>