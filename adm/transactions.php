<?php
session_start();

include_once '../isopoa.php';
include_once '../config.php';
include_once '../refresh.php';

if(!isset($_SESSION[$admsess])){
	header('location:index.php');
}

if(@$_GET['logout']=="a"){
	unset($_SESSION[$admsess]);
	header('location:index.php');
}

@$admin=$_SESSION[$admsess];




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
      <span class="navbar-toggler-icon"></span>    </button>
    <?php include 'sidebar.php' ;?>
  </nav>
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Dashboard</a>        </li>
        <li class="breadcrumb-item active">Transactions</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fa fa-table"></i> Transactions</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" width="100%" id="dataTable" cellspacing="0">
               <thead>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Name</th>
                  <th>Description</th>
				  <th>Destination</th>
				  <th>Cost</th>
				  <th>Paid</th>
                  <th>Status</th>
                </tr>
              </thead>  
			  <tfoot>
                <tr>
                  <th>S/N</th>
				  <th>Date</th>
                  <th>Name</th>
                  <th>Description</th>
				  <th>Destination</th>
				  <th>Cost</th>
				  <th>Paid</th>
                  <th>Status</th>
                </tr>
              </tfoot>            
              <tbody>               
				<?php
				$msg='';
				$count=0;
				$query="select * from transactions order by id desc";
				
				$res=mysqli_query($wole,$query);
				$numrows=mysqli_num_rows($res);
				
				$rowspage=25;
				$nopages=ceil($numrows/$rowspage);
				$pageno=1;
				
				if (!isset($_GET['pid'])){
					$pg=$pageno;
					$count=0;
				}
				else{
					$pg=(int)$_GET['pid'];
					if($pg==0){
						$pg=1;
						$count=0;
					}
					elseif($pg==1){
						$count=0;
					}
					else{
						$count=25*$pg;
					}
					
				}
				
				$offset=($pg-1)*$rowspage;
				$rtv=" LIMIT $offset,$rowspage";
				
				$query1=$query.$rtv;
				$result=mysqli_query($wole,$query) or die('Error!');
				
				
				while($row=mysqli_fetch_array($result)){
					$id=$row['id'];
					$dt=date('d-m-Y',strtotime($row['dt']));
					$usid=$row['uname'];
					$describe=$row['describe'];
					$destination=$row['destination'];
					$damtch=number_format($row['amtch'],2,".",",");
					$inibal=number_format($row['inibal'],2,".",",");
					$newbal=number_format($row['newbal'],2,".",",");
					$dstatus=$row['status'];
					$qm=mysqli_query($wole,"select * from users where uname='$usid'");
					$rs=mysqli_fetch_array($qm);
					$ufname=$rs['fname'];
					$did=$rs['id'];				
					
					$suid="$usid";
					$ausid="<a href='#' onclick=getuserdet('$suid')>$ufname</a>";
					$gocredit="<a href='#' onclick=credituser('$did')>Credit</a>";
					
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$ufname<br>$gocredit</td>
					  <td>$describe</td>
					  <td>$destination</td>
					  <td>$damtch</td>
					  <td>$inibal</td>
					  <td>$newbal</td>
					  <td>$dstatus</td>
					</tr>";
				}			
				
				$listno="";
				$thepage=$_SERVER['PHP_SELF'];
				for($i=1;$i<$nopages;$i++){
					if($i!=$pg){
						$listno.="|<a href=$thepage?pid=$i>$i</a>|";
					}
					else{
						$listno.="|$i|";
					}
				}
				
				//first and previous
				
				if($pg>1){	
					$pt=$pg-1;
					$fp="|<a href=$thepage?pid=1>First</a>";
					$pp="|<a href=$thepage?pid=$pt>Previous </a>|";	
					
				}
				else{
					$fp="|First|";
					$pp="|Previous| ";
				}
				//Last and next
				if($pg<$nopages){
					$pu=$pg+1;
					$lp="|<a href=$thepage?pid=$nopages>Last</a>|";
					$np="|<a href=$thepage?pid=$pu>Next </a>|";
				}
				else{	
					$np="|Next|";
					$lp="|Last|";
				}
				
				?>
              </tbody>
            </table>
			<?php //echo "<div style='width:700px; word-wrap: break-word; font-size:14px'>$fp  $pp $np $lp</div>";
		  ?>
          </div>
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

</script>
</html>
