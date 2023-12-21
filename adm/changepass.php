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


if(isset($_POST['cnewpass'])){
	extract($_POST);
	$pass=mysqli_real_escape_string($wole,$newpass);
	
	$pass=hash('sha512',$pass);
	mysqli_query($wole,"update emi set log='$pass' where uname='$admin'");
	$_SESSION[$sessname10]="<font color=blue>Password updated!</font>";		
	header("location: changepass");
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
  <script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
  <script>
          tinymce.init({
              selector: "textarea",
			  menubar: false,			 
              plugins: [
                  "advlist autolink lists link image charmap print preview anchor",
                  "searchreplace visualblocks code fullscreen",
                  "insertdatetime media table contextmenu paste"
              ],
              
			  toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link"
          });
  </script>
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
        <li class="breadcrumb-item active">Change Password</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
         <?php echo $msg ;?>
		 <form method="post" action="changepass" id="mform" name="mform">			
           <div class="form-group">
		  		<div class="form-row">
					<div class="col-md-4">
						New Password<br>
						<input class="form-control" type="password" name="newpass" id="newpass" placeholder='New pasword'>
					</div>	
					<div class="col-md-4">
						Confirm Password<br>
						<input class="form-control" type="password" name="cnewpass" id="cnewpass" placeholder="Confirm password">					
					</div>
					<div class="col-md-4">
						<br>
						<input class="form-control" type="button" value="Update Password" onClick="changepass()" />
					</div>			              		
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

function changepass(){
	
	var newpass=document.getElementById('newpass').value;
	var cnewpass=document.getElementById('cnewpass').value;
	
	if(newpass=='' || newpass.length<5){
		alert('Password should not be less than 5 characters');
		exit;
	}
	
	if(newpass!=cnewpass){
		alert('Password does not match!');
		exit;
	}
	document.mform.submit();
	
	
}

</script>
</html>
