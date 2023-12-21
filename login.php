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
		if(isset($_SESSION[$sesspage]) && $_SESSION[$sesspage]!="" ){
			$dsesspage=$_SESSION[$sesspage];
			header("location:$dsesspage");
		}
		else{
			header("location:$weburl/dashboard");
		}				
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
  <meta name="author" content="">
  <title>DLCIpay | Login</title>
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
      <div class="card-header">Login</div>
      <div class="card-body">
        <form method="post" action="login" id="mform">
		<?php echo $msg ;?>		
          <div class="form-group">
            <label for="exampleInputEmail1">Phone Number</label>
            <input class="form-control" name="phonenumber" id="phonenumber" type="number"  placeholder="Enter Phone">
          </div>
          <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input class="form-control" name="thepass" type="password" placeholder="Password" id="myInput">
            <input type="checkbox" onclick="myFunction()">Show Password
            <script>
function myFunction() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
          </div>
          
          <a class="btn btn-primary btn-block"  style=" cursor: pointer" onClick="document.getElementById('mform').submit();">Login</a>
        </form>
        <div class="text-center"><p><p>
          <a class="btn btn-primary btn-block" href="register">Register an Account</a></p></p>
          <a class="d-block small" href="forgot-password">Forgot Password?</a> 
		  <a class="d-block small" href="<?php echo $mainurl ;?>">Main Site</a>        
		  </div>
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