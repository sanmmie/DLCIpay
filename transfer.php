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
if(isset($_SESSION[$sessname3])){
	$msg=$_SESSION[$sessname3];
	unset($_SESSION[$sessname3]);
}

if(isset($_POST['tmoney'])){
	extract ($_POST);

	$postdata = http_build_query( 
			array(
				'gtype' => 'transferto',
				'uname' => $uname, 
				'pass' =>  $pass,
				'phone' => $tmobile,
				'amt' => $tmoney				
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
		$gob="<font color=green>$g2</font>";
	}
	else{
		$gob="<font color=red>$g2</font>";
	}
	
	$_SESSION[$sessname3]=$gob;		
	header("location:$weburl/transfer");
	
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
  <title>DLCIpay | Transfer</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Transfer</a>
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
        <li class="breadcrumb-item active">Transfer</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Transfer</h1>
          <p>
		  <?php echo $msg ;?>
		  <form action="transfer" method="post" name="form1" id="form1">		 
		   <div class="form-group">
		  		<div class="form-row">
					<div class="col-md-4">
						Transfer to Phone No<br>
						<input class="form-control" type="number" name="tmobile" id="tmobile" value="" placeholder='Phone Number'>
					</div>	
					<div class="col-md-4">
						Amount<br>
						<input class="form-control" type="number" name="tmoney" id="tmoney" value="" placeholder='0'>					
					</div>
					<div class="col-md-4">
						<br>
						<input class="form-control" type="button" value="Transfer" onClick="transferto()" />
					</div>			              		
				</div>
			</div>
			
		 </form>
		</p>
		</div>	
			<div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
                  <th>Status</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
                  <th>Status</th>
                </tr>
              </tfoot>            
              <tbody>               
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and (transtype='Transfer to' or transtype='Transfer from' ) order by id desc");
				while($r=mysqli_fetch_assoc($qs)){
					$trans=$r['transtype'];
					$batch=$r['batch'];
					$dt=date('d-m-Y',strtotime($r['dt']));
					$destination=$r['destination'];
					$describe=$r['describe'];
					$amt=$r['amt'];
					$amtch=$r['amtch'];
					$status=$r['status'];					
					
					if($trans=='data share' || $trans=='data topup' || $trans=='paytv'){
						$msg="$describe sent to $destination";
					}
					elseif($trans=='Referral Bonus'){
						$msg="N$amt $describe";
					}
					elseif($trans=='Transfer to'){
						$msg="$describe N$amt";
					}
					elseif($trans=='Transfer from'){
						$msg="$describe N$amt";
					}					
					elseif($trans=='Wallet Funding'){
						$msg="$describe";
					}
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$msg</td>
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

function transferto(){
	
	var phone=document.getElementById('tmobile').value;
	var amt=document.getElementById('tmoney').value;
	
	if(phone=='' || phone.length<11 || phone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	if(isNaN(amt)){
		alert('Invalid amount!');
		exit;
	}
	
	var conf=confirm("Are you sure you want to transfer NGN"+ amt + " to "+ phone+ "?");
	if(conf==0){
		exit;
	}
	document.form1.submit();
	
	
	
}


</script>

</html>