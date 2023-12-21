<?php
session_start();
include_once '../isopoa.php';
include_once '../config.php';

$msg='';
if(isset($_SESSION['adminerr'])){
	$msg=$_SESSION['adminerr'];
	unset($_SESSION['adminerr']);
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
  <title><?php echo $titleweb ;?></title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
</head>

<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Administrator Login</div>
      <div class="card-body">
        <form method="post" action="auth" id="mform">
		<?php echo $msg ;?>		
          <div class="form-group">
            <label for="exampleInputEmail1">Username</label>
            <input class="form-control" name="phonenumber" id="phonenumber" type="text"  placeholder="Enter Phone">
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input class="form-control" name="thepass" id="thepass" type="password" placeholder="Password">
          </div>
          
          <a class="btn btn-primary btn-block"  style=" cursor: pointer" onClick="document.getElementById('mform').submit();">Login</a>
        </form>
       
      </div>	  		
	  		<div class="text-center"><?php echo $copyright ;?></div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
</html>
