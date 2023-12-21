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


$bal1=0;

// POST the data to an api
$url1="$webbal1?userid=$airuser&pass=$airpass";
//$url="https://mobileairtimeng.com/httpapi/?userid=$userid&pass=$pass&network=$net&phone=$mphone&amt=$amount";
@$result1 = file_get_contents($url1);

if(isset($result1) && $result1!=''){
	$bal1=$result1;
}
else{
	$bal1="Fix error! Click <a href='portal#p4'>here</a>";
}



$query="select count(*) as totnum, sum(bal) as sumbal from users";
$res=mysqli_query($wole,$query);
$row=mysqli_fetch_array($res);
$sumbal=$row['sumbal'];
$totnum=$row['totnum'];

if($sumbal==''){
	$sumbal=0;
}

$q=mysqli_query($wole,"select count(*) as totnum1 from users");
$rs=mysqli_fetch_array($q);
$totnum1=$rs['totnum1'];

if($totnum1==''){
	$totnum1=0;
}


$qd=mysqli_query($wole,"select sum(amtch) as sumdata from transactions where transtype='data share'");
$rqd=mysqli_fetch_array($qd);
$sumdata=$rqd['sumdata'];

if($sumdata==''){
	$sumdata="0.00";
}

$qd1=mysqli_query($wole,"select sum(amtch) as sumbulk from transactions where transtype='bulksms'");
$rqd=mysqli_fetch_array($qd1);
$sumbulk=$rqd['sumbulk'];

if($sumbulk==''){
	$sumbulk="0.00";
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
  
</head>


<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard">Administrator</a>
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
          <a href="#">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">My Dashboard</li>
      </ol>      
     
      <div class="row">	 	
        <div class="col-lg-12">
          <!-- Example Bar Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-bar-chart"></i> <?php echo "Welcome, Admin" ;?></div>
            <div class="card-body">
              <div class="row">               
                <div class="col-sm-12 text-center my-auto">                 
				  <div class="h4 mb-0 text-primary">NGN <?php echo $bal1 ;?></div>
                  <div class="small text-muted">Your Main Balance</div>
                  <hr>
                  <div class="h4 mb-0 text-success">NGN <?php echo $sumbal ;?></div>
                  <div class="small text-muted">Total User Balance</div>
                  <hr>
                  <div class="h4 mb-0 text-info"><?php echo $totnum1 ;?></div>
                  <div class="small text-muted">Available users</div>				 
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>  
      <!-- Example DataTables Card-->
      
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

function getuserdet(a){
	document.getElementById('userid').value=a;
}

function credituser(a){
	window.location="crediteasy?id="+a;
	
}

</script>



</html>
