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

$msg='';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DLCIpay | SMS Report</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | SMS Report</a>
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
        <li class="breadcrumb-item active">SMS Report</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>SMS Report</h1>
          <p>
		  
			<div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Phone</th>
				  <th>Sender</th>
				  <th>Message</th>
                  <th>Status</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Phone</th>
				  <th>Sender</th>
				  <th>Message</th>
                  <th>Status</th>
                </tr>
              </tfoot>            
              <tbody>               
				<?php
				$msg='';
				$count=0;
				$bth = filter_var($_GET['bth'], FILTER_SANITIZE_STRING);
				$bth =mysqli_real_escape_string($wole,$bth);
				$qs=mysqli_query($wole,"select * from gbogbotxt where batch='$bth'");
				while($r=mysqli_fetch_assoc($qs)){
					$dt=$r['dt'];
					$phone=$r['phone'];
					$sender=stripslashes($r['title']);
					$message=stripslashes($r['message']);
					$status=$r['status'];									
					
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$phone</td>
					  <td>$sender</td>
					  <td>$message</td>
					  <td>$status</td>
					</tr>";
				}			
				
				?>
              </tbody></table>

          </div>
        </div>
					  
		  </p>
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

var smspage;
var b1;
var c1
function textCounter(field,maxlimit) {

var j=field.value.length-maxlimit;


if (field.value.length<maxlimit){
	smspage=1;
	b1=field.value.length;
}
else{
	if (field.value.length>=320){
		var textm=field.value;
		field.value=textm.substring(0,319);
	}
}
var h=field.value.length / maxlimit;
smspage=Math.ceil(h);
var g=smspage-1;
b1=field.value.length-(g*maxlimit);

c1='Page ' + smspage + ' /' + b1 + ' Characters';
document.getElementById('counter').innerHTML= c1;

}

function sendsms(a){
	if(document.getElementById('smsfrom').value==""){
			alert("Enter sender");
			exit;
		}
	
	if(document.getElementById('smsto').value==""){
			alert("Enter recepient(s)");
			exit;
	}
	
	
	var email =document.getElementById('smsemail').value;
	if (email==""){

		alert ('Enter your email.');

		exit;

	}

	var atpos=email.indexOf("@");

	var dotpos=email.lastIndexOf(".");

	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)

		{
  			alert('Invalid e-mail address.');  		
			exit;

  		}	
	
	var conf=confirm("Are you sure you want to send the SMS?");
	if(conf==0){
		exit;
	}
	
	if(document.getElementById('smsto').value!=""){
		var abj= document.getElementById('smsto').value;
		var aCars = abj.split(',');	
		if ((document.getElementById('smsfrom').value=="") || (document.getElementById('smsmsg').value=="")){
			alert ("Enter Sender's id and message.");
			exit;
		}
		
		var gty='';
		if(a==1){
			gty="sendsms1";
		}
		else if(a==2){
			gty="sendsms2";
		}
		
		var smsfrom=document.getElementById('smsfrom').value;
		var smsto=document.getElementById('smsto').value;
		var smsmsg=document.getElementById('smsmsg').value;
		document.getElementById('gtype').value=gty;
		
		document.form1.submit();
		
	}
}


</script>

</html>