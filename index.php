<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

$msg='';
if(isset($_SESSION[$sessname4])){
	$msg=$_SESSION[$sessname4];
	unset($_SESSION[$sessname4]);
}

if(isset($_POST['phonenumber']) && @$_POST['phonenumber']!=''){
	extract ($_POST);
	$phone=mysqli_real_escape_string($wole,$phonenumber);
	$pass=mysqli_real_escape_string($wole,$thepass);
	
	$postdata = http_build_query( 
			array(
				'phone' => $phone,
				'pass' => $pass,
				'gtype' =>'login'
			));

//$postdata="userid=08139170491&pass=f46dd040fe51c6d"; 
	// Set the POST options
	$opts = array('http' => 
		array (
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata,
		)
	);
 
	// Create the POST context
	$context  = stream_context_create($opts);
 
	// POST the data to an api
	$url=$webprog;
	$result = file_get_contents($url, false, $context);

	$gres=explode("|",$result);
	$g1=$gres[0];
	
	if($g1=='success'){
		$uname=$gres[1];
		$_SESSION[$sessname]=$uname;
		$_SESSION[$sesspass]=$pass;		
		header("location:$weburl/dashboard");
	}
	else{
		$g2=$gres[1];
		$msg="<font color=red>$g2</font>";
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
  <meta name="author" content="DLCItechnologies">
  <title>DLCIpay</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
</head>

<body class="bg-dark">
  <div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-header">DLCIpay</div>
      <div class="card-body">
	  <p align="left"> <a href="login">Login</a> | <a href="register">Sign up</a></p>
		<?php echo $webhome ;?>
		
        
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