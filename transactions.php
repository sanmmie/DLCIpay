<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';
include_once 'refresh.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/airtime";
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
if(isset($_SESSION[$sessname2])){
	$msg=$_SESSION[$sessname2];
	unset($_SESSION[$sessname2]);
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
  <title>DLCIpay | Transactions</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Transactions</a>
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
        <li class="breadcrumb-item active">Transactions</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Transactions</h1>
	<div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" >
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
				  <th>Amt Paid</th>
				  <th>Initial Bal</th>
				  <th>New Bal</th>
                  <th>Status</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Description</th>
				  <th>Amt Paid</th>
				  <th>Initial Bal</th>
				  <th>New Bal</th>
                  <th>Status</th>
                </tr>
              </tfoot>            
              <tbody>     
			  <form>          
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' order by id desc");
				while($r=mysqli_fetch_assoc($qs)){
					$trans=$r['transtype'];
					$batch=$r['batch'];
					$dt=date('d-m-Y h:i:sA',strtotime($r['dt']));
					$destination=$r['destination'];
					$describe=$r['describe'];
					$amtch=number_format($r['amtch'],2,".",",");
					$inibal=number_format($r['inibal'],2,".",",");
					$newbal=number_format($r['newbal'],2,".",",");
					$status=$r['status'];					
					
					$dcancel='';
					if($trans=='airtime'){
						$msg="$describe $destination";
					}
					elseif($trans=='Referral Bonus'){
						$msg="$describe";
					}
					elseif($trans=='Transfer to'){
						$msg="$describe";
					}
					elseif($trans=='Transfer from'){
						$msg="$describe";
					}
					elseif($trans=='bulksms'){
						$trans=strtoupper($trans);
						$msg="$describe";
					}
					else{
						$msg="$describe";
					}
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$msg</td>
					  <td>$amtch</td>
					  <td>$inibal</td>
					  <td>$newbal</td>
					  <td>$status</td>
					</tr>";
				}			
				
				?>
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
function topup(){
	var phone=document.getElementById('mobile').value;
	var element=document.getElementById('network');
	var amt = document.getElementById('amount').value;
	
	netname=element.value; 
	
		
	if(phone=='' || phone.length<11 || phone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	if(netname=='0'){
		alert('Select network');
		exit;
	}
	
	if(amt==0 || amt==''){
		alert('Enter amount');
		exit;
	}
		
	
	var dnet = element.options[ element.selectedIndex ].text;
	document.getElementById('selnetwork').value=dnet;
	
	var conf=confirm("Send "+ dnet + " " + amt + " to "+ phone +"?");
	if(conf==0){
		exit;
	}
	
	document.getElementById('sbut').value="Please wait...";
	document.form1.submit();
	
}


</script>

</html>