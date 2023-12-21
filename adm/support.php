<?php
session_start();

include_once '../isopoa.php';
include_once '../config.php';

if(!isset($_SESSION[$admsess])){
	header('location:index.php');
}

if(@$_GET['logout']=="a"){
	unset($_SESSION[$admsess]);
	header('location:index.php');
}

@$admin=$_SESSION[$admsess];

$msg='';
if(isset($_SESSION[$sessname10])){
	$msg=$_SESSION[$sessname10];
	unset($_SESSION[$sessname10]);
}


if(isset($_POST['message'])){
	extract($_POST);
	$yname=mysqli_real_escape_string($wole,$yname);
	$yphone=mysqli_real_escape_string($wole,$yphone);
	$yemail=mysqli_real_escape_string($wole,$yemail);
	
	$yemail= filter_var($yemail, FILTER_SANITIZE_EMAIL);
	$message	=filter_var($message, FILTER_SANITIZE_STRING);
	$subject	=filter_var($subject, FILTER_SANITIZE_STRING);
	
	$masteremail = "noreply@".getDomain($mainurl);		
	$to          = 'support@mobileairtimeng.com';
	$from        = "$yname <$masteremail>";
	
	$subject="Mobile Airtime: ". $subject;
	
	$content = "<p>NAME: $yname<br>PHONE: $yphone<br>MESSAGE:<br>$message</p>";
	
	
	$html_content = $content;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: ".$from."\r\nReply-To: ".$yemail;

	//send the email
	$mail_sent = @mail($to, $subject, $html_content, $headers );
	
	$_SESSION[$sessname10]="<p><font color=blue>Message sent!</font></p>";		
	header("location: support");
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
  <title>Secured</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
  <script src="//cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
</head>


<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard">Administrator</a>
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
        <li class="breadcrumb-item active">Support</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
         <?php echo $msg ;?>
		 Please fill in the details. Your name and phone number must be the one you registered for the business.
		 <form method="post" action="support" id="mform" name="mform">			
           <div class="form-group">
				<div class="col-md-6">
					 Your Name<br>
				<input class="form-control" type="text" name="yname" id="yname"  value="">	
				</div>
				<div class="col-md-6">
					 Phone No<br>
				<input class="form-control" type="number" name="yphone" id="yphone"  value="">	
				</div>	
				<div class="col-md-6">
					 Email<br>
				<input class="form-control" type="text" name="yemail" id="yemail"  value="">	
				</div>	
				<div class="col-md-6">
					Subject<br>
					<input class="form-control" type="text" name="subject" id="subject"  value="">
				</div>
				<div class="col-md-8">
					Message<br>
					<textarea class="form-control" name="message" id="message"></textarea>
				</div>
				<div class="col-md-6">
					<br>
					<input class="form-control" type="button" value="Send" onClick="sendmess()" />
				</div>			              		
			</div>
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
  </div>
</body>
<script language="javascript">

function sendmess(){
	
	var yname=document.getElementById('yname').value;
	var subj=document.getElementById('subject').value;
	var msg=document.getElementById('message').value;
	var yphone=document.getElementById('yphone').value;
	var yemail=document.getElementById('yemail').value;
	
	if(yname==''){
		alert('Enter your full name.');
		exit;
	}
	if(yemail==''){
		alert('Enter your email.');
		exit;
	}
	if(yphone==''){
		alert('Enter your phone number.');
		exit;
	}
	
	if(subj=='none'){
		alert('Select subject');
		exit;
	}
		
	document.mform.submit();
	
	
}

</script>
</html>
