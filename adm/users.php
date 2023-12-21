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
if(isset($_SESSION[$sessname7])){
	$msg=$_SESSION[$sessname7];
	unset($_SESSION[$sessname7]);
}

if(isset($_GET['cid'])){
	$cid=mysqli_real_escape_string($wole,$_GET['cid']);
	$query="delete from users where uname='$cid'";
	$res=mysqli_query($wole,$query);
	$_SESSION[$sessname7]="<font color=blue>Record deleted!</font>";
	header('location: users.php');
}

$query="select count(*) as totnum, sum(bal) as sumbal from users";
$res=mysqli_query($wole,$query);
$row=mysqli_fetch_array($res);
$bal=$row['sumbal'];
$totnum=$row['totnum'];


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
        <li class="breadcrumb-item active">Users</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-header">		
          <i class="fa fa-table"></i> Users: <?php echo $totnum ;?></div> <?php echo $msg ;?>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Name</th>
				  <th>Level</th>
                  <th>Email</th>
                  <th>Phone</th>
				  <th>Balance</th>
				  <th>-</th>
				  <th>X</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                 <th>S/N</th>
				  <th>Name</th>
				  <th>Level</th>
                  <th>Email</th>
                  <th>Phone</th>
				  <th>Balance</th>
				  <th>-</th>
				  <th>X</th>
                </tr>
              </tfoot>            
              <tbody>               
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from users order by uname asc");
				while($row=mysqli_fetch_array($qs)){
					$id=$row['id'];
					$uname=$row['uname'];
					$fname=$row['fname'];
					$phone=$row['phone'];
					$email=$row['email'];
					$ulevel=$row['usertype'];
					$units=$row['bal'];
					
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$fname</td>
					  <td>$ulevel</td>
					  <td>$email</td>
					  <td>$phone</td>
					  <td>$units</td>
					  <td><a href='credituser?id=$id'>Credit</a></td>
					  <td><a href='users?cid=$uname' onclick=rmv('$uname')>Remove</a></td>
					</tr>";
				}			
				
				?>
              </tbody>
            </table>
          </div>
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
	window.location="credituser?id="+a;
	
}

function rmv(a){
	
	var conf=confirm("Are you sure you want to delete this user?");
	if(conf==1){
		window.location="users?cid="+a;
	}
		
}

</script>



</html>
