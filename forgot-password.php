<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

$msg='';
if(isset($_SESSION[$sessname5])){
	$msg=$_SESSION[$sessname5];
	unset($_SESSION[$sessname5]);
}

if(isset($_POST['email'])){
	extract ($_POST);
	
		$postdata = http_build_query( 
			array(
				'gtype' => 'resetacct1',
				'email' => $email, 
				'phone' => $phone 
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
	@$g2=$gres[1];
	if($g1=="success"){
		$gob="<font color=blue>$g2</font>";
		$_SESSION[$sessname4]=$gob;	
		header("location:$weburl/login");
	}
	else{
		$gob="<font color=red>$g2</font>";
		$_SESSION[$sessname5]=$gob;		
		header("location:$weburl/forgot-password");
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
  <title>DLCIpay | Forgot Password</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
  <style type="text/css">
<!--
.style1 {
	color: #990000;
	font-style: italic;
}
.style2 {
	color: #003399;
	font-style: italic;
}
-->
  </style>
</head>

<body class="bg-dark">
  <div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-header">Reset Password</div>
      <div class="card-body">
	  <?php echo $msg ;?>
        <form action="forgot-password" id="form1" name="form1" method="post">          
		   <div class="form-group">
            <label for="expphone">Phone Number</label>
            <input class="form-control" id="phone" name="phone" type="number" placeholder="Enter Phone">
          </div>
		  
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input class="form-control" id="email" name="email" type="email" aria-describedby="emailHelp" placeholder="Enter email">
          </div>
		  
	      <div class="form-group">            
            <input class="form-control" id="mairef" type="text" placeholder="Enter Sponsor" style=" display: none">
		  </div>
         
          <a class="btn btn-primary btn-block" style=" cursor: pointer" onClick="acctreset()">Reset Password</a>
        </form>
        <div class="text-center">
          <a class="d-block small mt-3" href="login">Login Page</a>
          <a class="d-block small" href="register">Register</a>
		  <a class="d-block small" href="<?php echo $mainurl ;?>">Main Site</a>    
		       </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>

<script language="javascript">
function acctreset(){
	
	maiphone=document.getElementById('phone').value;
	var email=document.getElementById('email').value;
	
	if(maiphone=='' || maiphone.length<11 || maiphone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	var atpos=email.indexOf("@");
	var dotpos=email.lastIndexOf(".");

	if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
		{
  			alert('Invalid e-mail address.');  		
			exit;
  		}
	
	document.form1.submit();
	
}
</script>
</html>