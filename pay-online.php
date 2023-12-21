<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';
include_once 'refresh.php';

if(!isset($_SESSION[$sessname])){
	header("location: $weburl/login");
	exit;	
}

@$uname=$_SESSION[$sessname];

$q=mysqli_query($wole,"select * from users where uname='$uname'");
if(mysqli_num_rows($q)!=1){
	header("location: $weburl/login");
	exit;
}
else{
	$rs=mysqli_fetch_array($q);
	$bal=$rs['bal'];
	$smsbonus=$rs['smsbonus'];
	$fname=$rs['fname'];
}

if(!isset($_POST['theamt'])){
	header("location:fundwallet");
	exit;
}
extract($_POST);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DLCIpay | Pay Online</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
</head>


<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard">DLCIpay | Pay Online</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>    </button>
    <?php include 'sidebar.php' ;?>
  </nav>
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Dashboard</a>        </li>
        <li class="breadcrumb-item active">Fund Wallet</li>
      </ol>      
     
      <div class="row">	 	
        <div class="col-lg-12">
          <!-- Example Bar Chart Card-->		 
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-bar-chart"></i></div>
           	<form action="processpay2" method="post" name="form2" id="form2">		 
		  				<div id='paymentdiv'>Connecting to secured payment gateway. Please wait...</div>
						<input class="form-control" type="hidden" name="fundamt" id="fundamt"  value="<?php echo $famt ;?>"><input id="email" name="email" type="hidden" value="<?php echo $email ;?>"><input id="phone" name="phone" type="hidden" value="<?php echo $phone ;?>"> <input id="dataitem" name="dataitem" type="hidden"> <input id="theamt" name="theamt" type="hidden" value="<?php echo $theamt ;?>">
				
			<script src="https://js.paystack.co/v1/inline.js"></script>
			
		  </form>
          </div>
        </div>
      </div>  
      
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
      <div class="container">
        <div class="text-center">
          <small><?php echo $copyright ;?></small>        </div>
      </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">�</span>            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="logout">Logout</a>          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
    <!-- Custom scripts for this page-->
    <script src="js/sb-admin-datatables.min.js"></script>
    <script src="js/sb-admin-charts.min.js"></script>
	<script src="jquerymy.js"></script>
  </div>
</body>
<script language="javascript">
	
var ajx;
var ajxbool;

try{
	ajx=new XMLHttpRequest();
	ajxbool=true;
} catch(e){
	ajx=new ActiveXObject("Microsoft.XMLHTTP");
	ajxbool=true;
}


var myref;
function getref(){
	//var vx=new Date(milliseconds);
	//myref=Math.floor((Math.random() * 10000) + 10)+new Date().getTime();
	//myref= Math.random().toString(36).substr(2, 9);
	myref=Math.random().toString().substr(2);
	//alert(myref);

}
	
var fundamt;
var theamt;
var themail;
var phone;
var dataitem;

function payWithPaystack(){

	theamt=parseFloat(document.getElementById('theamt').value);
	theamt=parseFloat(theamt*100);	
	
	fundamt=parseFloat(document.getElementById('fundamt').value);
	themail=document.getElementById('email').value;
	phone=document.getElementById('phone').value;
	
	getref();
	
	var handler = PaystackPop.setup({
	  key: "<?php echo $pk_live ;?>",
	  email: themail,
	  amount: theamt,
	  ref: myref, 
	  metadata: {
		 custom_fields: [
			{
				display_name: "Mobile Number",
				variable_name: "mobile_number",
				value: phone
			}
		 ]
	  },
	  callback: function(response){
		  //alert('success. transaction ref is ' + response.reference);
		  var compref=response.reference;
		  document.getElementById('dataitem').value=compref+"|"+phone+"|"+fundamt+"|"+theamt;
			dataprocess();	
	  },
	  onClose: function(){
		  document.getElementById('paymentdiv').innerHTML="Payment cancelled. Please wait...";
		  window.location= 'fundwallet';	
	  }
	});
	
	handler.openIframe();
}

function dataprocess(){
	 document.getElementById('paymentdiv').innerHTML="Finalizing payment...";
	 document.form2.submit();
}

payWithPaystack();
	
</script>
</html>