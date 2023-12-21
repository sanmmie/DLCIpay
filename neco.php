<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';
include_once 'refresh.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/neco";
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
$bal=$rs['bal'];
$usertype=$rs['usertype'];

$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$necoprice=$rsb['neco'];

$msg='';
if(isset($_SESSION['sessneco'])){
	$msg=$_SESSION['sessneco'];
	unset($_SESSION['sessneco']);
}

if(isset($_POST['gneco'])){
	extract ($_POST);
	if($bal<$necoprice){
		$_SESSION['sessneco']="<font color=red>Insufficient balance</font>";
		header("location:$weburl/neco");
		exit;
	}
	
	$reqamt=$necoprice;
	
	$url="http://mobileairtimeng.com/httpapi/neco?userid=$airuser&pass=$airpass";
	@$str = file_get_contents($url);	
	$sdk=explode("|",$str);
	
	$pos=$sdk[0];
	if($pos == '100'){
		//deduct money					
		$tmg="Request completed";
		$status=$tmg;
		$_SESSION['sessneco']="<p><font color=blue><b>$tmg</b></font></p>";	
		
		$pin=$sdk[1];
		$batch=uniqid();
		
		$qb=mysqli_query($wole,"select * from users where uname='$uname'");
		$rb=mysqli_fetch_array($qb);
		$bal=$rb['bal'];
		
		$reqbal=$bal-$reqamt;					
		$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
		
		$id=0;
		$dt=date('Y-m-d H:i:s');
		$info="NECO, PIN: $pin";
		$bmsg="PIN: $pin";
		
		$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','neco','$info','owner','$reqamt','$bal','$reqbal','$status')");
						
		//send email notification
		$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>PIN:</b> $pin<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
<p>Thank you.</p>";					
		$to = $email;
		$subject = "NECO Result Token";
		$message = $dsemail . ""; 
		
		$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);					
			
	}
	else{
		if (isset($sdk[1])){
			// do stuff	
			$tmg=$sdk[1];
		}
		else{
			$tmg="An error occured!";
		}
		
		$_SESSION['sessneco']="<p><font color=red><b>$tmg</b></font></p>";
	}

	header("location:$weburl/neco");
	
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
  <title>DLCIpay | NECO</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | NECO</a>
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
        <li class="breadcrumb-item active">NECO</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>NECO Result Token</h1>
         <?php echo $msg ;?>
		  <p>		  
		<form action="neco" method="post" name="form1" id="form1">
		Price N<?php echo @$necoprice ?>. Card details (Token) will be displayed here and also be sent to your email.<br />
		<div class="form-group">
			<div class="form-row">
              	<div class="col-md-4">
				<input class="form-control" name="tbut" type="button" id="tbut" onClick="getwaec()" value="Place Order"/>	
				<input name="gneco" type="hidden" id="gneco"> 			
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
			  <form action="waec" method="post" name="form2" id="form2">          
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='neco' order by id desc");
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
					if($trans=='neco'){
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
function getwaec(){

var b=confirm("Are you sure you want to proceed?");
if(b==1){
	document.getElementById('gneco').value="gwa";
	document.getElementById('tbut').value="Please wait...";
	document.form1.submit();
	}
}


</script>

</html>