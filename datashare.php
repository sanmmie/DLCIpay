<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';
include_once 'refresh.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/datashare";
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
$usertype=$rs['usertype'];

$msg='';
if(isset($_SESSION[$sessname2])){
	$msg=$_SESSION[$sessname2];
	unset($_SESSION[$sessname2]);
}

$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$mdata=$rsb['dataprice'];
$mdata1=$rsb['dataprice1'];
$mdata2=$rsb['dataprice2'];

if(isset($_POST['cancelno'])){
	extract ($_POST);
	
	$postdata = http_build_query( 
			array(
				'gtype' => 'cancel-data',
				'dataf' => $_POST['cancelno'], 
				
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
	}
	else{
		$gob="<font color=red>$g2</font>";
	}
	
	$_SESSION[$sessname2]=$gob;		
	header("location:$weburl/datashare");
	
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
  <title>DLCIpay | Data Share</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Data Share</a>
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
        <li class="breadcrumb-item active">MTN Data Share</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>MTN Data Share</h1>
         <?php echo $msg ;?>
		  <p>		  
		<form action="simple" method="post" name="form1" id="form1">
		Check data balance: *461*7#<br />
		<div class="form-group">
			<div class="form-row">
              	<div class="col-md-4">
				Phone No<br />
	 			<input class="form-control" type="number" name="mobile" id="mobile" value="" placeholder='MTN Phone No'>
	 			<input name="gtype" id="gtype" type="hidden" value="data-share">
				</div>
				<div class="col-md-4">
				Data Bundle<br />
				<select class="form-control" name="mobiledata" id="mobiledata">
					<option value="1">1GB <?php echo "N$mdata" ;?></option>
					<option value="2">2GB <?php echo "N$mdata1" ;?></option>
					<option value="5">5GB <?php echo "N$mdata2" ;?></option>
				</select>
				</div>
				<div class="col-md-4">
					<br>
					<input class="form-control" type="button" value="Send Data" onClick="sharedata()" name="sbut" id="sbut" />	
				</div>
			</div>
	  	</div>
	</form>
	</p>
	<div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
				  <th>Amt Paid</th>
                  <th>Status</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
				  <th>Amt Paid</th>
                  <th>Status</th>
                </tr>
              </tfoot>            
              <tbody>     
			  <form action="datashare" method="post" name="form2" id="form2">          
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='data share' order by id desc");
				while($r=mysqli_fetch_assoc($qs)){
					$trans=$r['transtype'];
					$batch=$r['batch'];
					$dt=date('d-m-Y',strtotime($r['dt']));
					$destination=$r['destination'];
					$describe=$r['describe'];
					$amt=$r['amt'];
					$amtch=$r['amtch'];
					$status=$r['status'];					
					
					$dcancel='';
					if($trans=='data share' || $trans=='data topup' || $trans=='paytv'){
						if($status=='Data recharge in progress' || $status=='in progress'){
							$dcancel="<a href='#' onclick='datacancel($batch)'>Cancel</a>";
							$sts="in progress/$dcancel";
							$msg="$describe to $destination $sts";
						}
						else{
							$msg="$describe sent to $destination";
						}
												
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
					elseif($trans=='bulksms'){
						$trans=strtoupper($trans);
						$msg="$describe. Paid $amt";
					}
					elseif($trans=='Wallet Funding'){
						$msg="$describe";
					}
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$msg</td>
					  <td>$amtch</td>
					  <td>$status</td>
					</tr>";
				}			
				
				?>
				<input name="cancelno" id="cancelno" type="hidden" value="">
				</form>
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
function sharedata(a){
	var phone=document.getElementById('mobile').value;
	var element=document.getElementById('mobiledata');
	mobilephone=document.getElementById('mobile').value;
	
	
	datasize=element.value; 
	
		
	if(phone=='' || phone.length<11 || phone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	var dnet = element.options[ element.selectedIndex ].text;
	
	
	var conf=confirm("Send "+ dnet + " to "+ phone +"?");
	if(conf==0){
		exit;
	}
	
	document.getElementById('sbut').value="Please wait...";
	document.form1.submit();
	
}

function datacancel(a){
	if(a==''){
		exit;
	}	

	var conf=confirm("Are you sure sure want to cancel this data request?");
	if(conf==0){
		exit;
	}
	document.getElementById('cancelno').value=a;
	document.form2.submit();
}


</script>

</html>