<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/api";
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
$apkey=$rs['mapk'];

$msg='';
if(isset($_SESSION[$sessname2])){
	$msg=$_SESSION[$sessname2];
	unset($_SESSION[$sessname2]);
}

if(isset($_POST['resetapi'])){
	extract ($_POST);
	
	$postdata = http_build_query( 
			array(
				'gtype' => 'resetapiweb',
				'uname' => $uname, 
				'pass' => $pass				
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
		$gob="<br><font color=blue>API key reset successfully</font>";
	}
	else{
		$gob="<br><font color=red>$g2</font>";
	}
	
	$_SESSION[$sessname1]=$gob;		
	header("location:$weburl/api");
	
}

$apiurlvtu=$weburl."/httpvtu?userid=x&pass=x&phone=x&network=x&amount=x" ;
$apiurlgo=$weburl."/http?userid=x&pass=x&phone=x&datasize=x" ;
$apiurlcheck=$weburl."/status?userid=x&pass=x&phone=x&tid=x" ;
$apiurlbal=$weburl."/balance?userid=x&pass=x&phone=x&tid=x" ;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DLCIpay | API</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | API</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>    </button>
    <?php include 'sidebar.php' ;?>
  </nav>
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="dashboard">Dashboard</a>        </li>
        <li class="breadcrumb-item active">API</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>API</h1>
           <?php echo $msg ;?>
		   <form action="api" method="post" name="form1" id="form1">
		   Your API key: <?php echo $apkey ;?> <input name="resetapi" value="Reset API" type="submit"><p>
		   </form>
			<b>AIRTIME RECHARGE</b><br>
		 <?php echo $apiurlvtu ;?>		 
		  <p>
		 
		  <b>Parameters</b><br>
		  userid = your registered phone number<br>
		pass = your API key<br> 
		phone = phone number <br>
		amount = amount <br>
		Network codes:<br>
		MTN = 5<br>
		GLO = 6 <br>
		AIRTEL = 1<br>
		9MOBILE = 2<br>
		</p>
		<p>
		<b>Responses</b><br>
		Response is separated with pipe (|).<br>
Format: statusCode|statusMessage<br>
e.g. Successful response; 1000|Recharge successful<br>
e.g. Other platform and service errors; 1002|insufficient balance<br> 
		</p>
			
				
		  <b>SEND DATA</b><br>
		 <?php echo $apiurlgo ;?>		 
		  <p>
		 
		  <b>Parameters</b><br>
		  userid = your registered phone number<br>
		pass = your API key<br> 
		phone = receiver's MTN phone number <br>
		datasize:<br>
		1000 = 1GB<br>
		2000 = 2GB <br>
		5000 = 5GB<br>
		</p>
		<p>
		<b>Responses</b><br>
Format: statusCode|TransactionID|statusMessage<br>
e.g. Successful response; 1000|177829|completed<br>
e.g. Pending request; 1001|177827|in progress<br>
e.g. Error; 1002|invalid phone number<br> 
		</p>
		
		<p><b>CHECK STATUS</b><br>
		<?php echo $apiurlcheck ;?><br>
		 tid = Transaction ID<br><br>
Response is separated with pipe.<br>
Format: statusCode|statusMessage<br>
e.g. Successful response; 1000|completed<br>
e.g Pending; 1001|in progress<br>
e.g. Error; 1002|invalid transaction<br>
e.g. Failed; 1004|failed<br></p>

		<p><b>CHECK BALANCE</b><br>
		<?php echo $apiurlbal ;?><br><br>

Response is separated with pipe.<br>
e.g. Successful response; 1000|balance<br>
e.g. Error; 1002|invalid transaction<br>
</p>
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
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
  </div>
</body>
</html>