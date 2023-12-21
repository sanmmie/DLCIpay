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
$pm1='';
$pm2='';
$pm3='';

if(isset($_SESSION[$sessname9])){
	$msg=$_SESSION[$sessname9];
	unset($_SESSION[$sessname9]);
	$msg1=explode("|",$msg);
	if($msg1[0]=="1"){
		$pm1=$msg1[1];
	}
	elseif($msg1[0]=="2"){
		$pm2=$msg1[1];
	}
	elseif($msg1[0]=="3"){
		$pm3=$msg1[1];
	}
	elseif($msg1[0]=="4"){
		$pm4=$msg1[1];
	}
	elseif($msg1[0]=="5"){
		$pm5=$msg1[1];
	}
}

if(isset($_POST['wcopy'])){
	extract ($_POST);
	$wcopy=mysqli_real_escape_string($wole,$wcopy);
	$wtitle=mysqli_real_escape_string($wole,$wtitle);
	$wcontact=mysqli_real_escape_string($wole,$wcontact);
	$wname=mysqli_real_escape_string($wole,$wname);
	$wfund=mysqli_real_escape_string($wole,$wfund);
	mysqli_query($wole,"update setup set titleweb='$wtitle',webname='$wname',webcontact='$wcontact',fundwallet='$wfund',copyright='$wcopy' where id=1");
	$_SESSION[$sessname9]="1|Description updated!";
	header("location: portal#p1");
}

if(isset($_POST['wdata'])){
	extract ($_POST);
	$wdata=(float)mysqli_real_escape_string($wole,$wdata);
	$wsms=(float)mysqli_real_escape_string($wole,$wsms);
	
	mysqli_query($wole,"update mydataprice set dataprice='$wdata' where id=1");
	mysqli_query($wole,"update mysmsprice set smsprice='$wsms' where id=1");
	$_SESSION[$sessname9]="2|Pricing updated!";
	header("location: portal#p2");
	
}

if(isset($_POST['wph'])){
	extract ($_POST);
	$wph=mysqli_real_escape_string($wole,$wph);
	$wpk=mysqli_real_escape_string($wole,$wpk);
	
	mysqli_query($wole,"update setup set matphone='$wph',matkey='$wpk' where id=1");
	$_SESSION[$sessname9]="3|EDS API pdated!";
	header("location: portal#p3");
	
}

if(isset($_POST['airph'])){
	extract ($_POST);
	$wph=mysqli_real_escape_string($wole,$airph);
	$wpk=mysqli_real_escape_string($wole,$airpk);
	
	$wph = preg_replace('/\s+/', '', $wph);
	$wpk = preg_replace('/\s+/', '', $wpk);
	mysqli_query($wole,"update setup set airuser='$wph',airpass='$wpk' where id=1");
	$_SESSION[$sessname9]="4|Airtime API updated!";
	header("location: portal#p4");
	
}

if(isset($_POST['pklive'])){
	extract ($_POST);
	$wph=mysqli_real_escape_string($wole,$sklive);
	$wpk=mysqli_real_escape_string($wole,$pklive);
	
	$wph = preg_replace('/\s+/', '', $wph);
	$wpk = preg_replace('/\s+/', '', $wpk);
	mysqli_query($wole,"update setup set sklive='$wph',pklive='$wpk' where id=1");
	$_SESSION[$sessname9]="5|Paystack API Details Updated!";
	header("location: portal#p5");
	
}


if(isset($_POST['monpassword'])){
	extract ($_POST);
	$wph=mysqli_real_escape_string($wole,$monuser);
	$wpk=mysqli_real_escape_string($wole,$monpassword);
	$wpc=mysqli_real_escape_string($wole,$moncontract);
	

	mysqli_query($wole,"update setup set monuser='$wph',monpass='$wpk', moncontract='$wpc' where id=1");
	
	header("location: portal?pmon=Monnfiy Details Updated!#pmon");
}

$mq=mysqli_query($wole,"select monuser,monpass,moncontract from setup where id=1");
$rmq=mysqli_fetch_array($mq);

$monusername = $rmq['monuser'];
$monpassword = $rmq['monpass'];
$moncontract=$rmq['moncontract'];


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
  <script src="//cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
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
        <li class="breadcrumb-item active">Setup</li>
      </ol>      
     
      <div class="row">	 	
        <div class="col-lg-12">
          <!-- Example Bar Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <a name="p1">Descriptions</a></div>
            <div class="card-body">  
			            
			  <?php			  
			  if(isset($pm1)){
			  	@$pm1=htmlspecialchars($pm1);
			  	echo "<font color=blue>$pm1</font>";
			  }
			  ?>
			  <form id="form1" name="form1" method="post" action="portal">
			  	<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					Web Name on Portal<br>
					 <input class="form-control" name="wname" type="text" id="wname" value="<?php echo $webname ;?>" placeholder='e.g. Easy Data Share' />
					</div>
					<div class="col-md-6" style="display: none">
					Web Title (appears on the title, good for SEO)<br>
					<input class="form-control" name="wtitle" type="text" id="wtitle" value="<?php echo $titleweb ;?>" placeholder='Fast MTN data recharge in Nigeria' />
					</div>
				</div>		
          		</div>
                <div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					Contact Address/Details<br>
					 <textarea class="form-control" name="wcontact" id="wcontact"><?php echo $webcontact ;?></textarea>
					     <script>
                        CKEDITOR.replace( 'wcontact' );
                    </script>
					</div>
					<div class="col-md-6">
					Fund Wallet Details<br>
					 <textarea class="form-control" name="wfund" id="wfund"><?php echo $fundwallet ;?></textarea>
					  <script>
                        CKEDITOR.replace( 'wfund' );
                    </script>
					</div>
				</div>		
          		</div>

				<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					Copyright on Portal<br>
					<input class="form-control" name="wcopy" type="text" id="wcopy" value="<?php echo $copyright ;?>" placeholder='Copyright 2017' />
					</div>
					<div class="col-md-6">
					<br>
					<input class="form-control" type="submit" value="Submit" />
					</div>
				</div>		
          		</div>
			  </form>
			  
            </div>
          </div>
           
      <!-- Example DataTables Card-->    
	
	 <div class="card mb-3" style=" display:none">
        <div class="card-header">
           <a name="p3">EDS API Connect</a></div>
        <div class="card-body">
			<?php
			   if(isset($pm3)){
			  	@$pm3=htmlspecialchars($pm3);
			  	echo "<font color=blue>$pm3</font>";
			  }
			  ?>
			
			 <form id="form3" name="form3" method="post" action="portal">
			  	<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-4">
					Your Phone (on EDS)<br>
					 <input class="form-control" name="wph" type="text" id="wph" value="<?php echo $matphone ;?>" />
					</div>
					<div class="col-md-4">
					API Key<br>
					<input class="form-control" name="wpk" type="text" id="wpk" value="<?php echo $matkey ;?>" />
					</div>
					<div class="col-md-4">
					<br>
					<input class="form-control" value="Submit" type="submit" />
					</div>
				</div>		
          		</div>
			</form>          
        </div>
    </div>
	<div class="card mb-3">
        <div class="card-header">
           <a name="p4">Mobile Airtime API Connect</a></div>
        <div class="card-body">
			<?php
			   if(isset($pm4)){
			  	@$pm4=htmlspecialchars($pm4);
			  	echo "<font color=blue>$pm4</font>";
			  }
			  ?>
			
			 <form id="form4" name="form4" method="post" action="portal">
			  	<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-4">
					Your Phone<br>
					 <input class="form-control" name="airph" type="text" id="airph" value="<?php echo $airuser ;?>" />
					</div>
					<div class="col-md-4">
					API Key<br>
					<input class="form-control" name="airpk" type="text" id="airpk" value="<?php echo $airpass ;?>" />
					</div>
					<div class="col-md-4">
					<br>
					<input class="form-control" value="Submit" type="submit" />
					</div>
				</div>		
          		</div>
			</form>          
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
           <a name="p5">Paystack API Connect</a></div>
        <div class="card-body">
			<?php
			   if(isset($pm5)){
			  	@$pm5=htmlspecialchars($pm5);
			  	echo "<font color=blue>$pm5</font>";
			  }
			  ?>
			
			 <form id="form5" name="form5" method="post" action="portal">
			  	<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-4">
					Live Secret Key<br>
					 <input class="form-control" name="sklive" type="text" id="sklive" value="<?php echo $sk_live ;?>" />
					</div>
					<div class="col-md-4">
					Live Public Key<br>
					<input class="form-control" name="pklive" type="text" id="pklive" value="<?php echo $pk_live ;?>" />
					</div>
					<div class="col-md-4">
					<br>
					<input class="form-control" value="Submit" type="submit" />
					</div>
				</div>		
          		</div>
			</form>          
        </div>
    </div>
	
	<div class="card mb-3">
	<div class="card-header">
	   <a name="pmon" id='pmon'>Monnify Settings</a></div>
	<div class="card-body">
		<?php
		   if(isset($_GET['pmon'])){
			@$pmon=htmlspecialchars($_GET['pmon']);
			echo "<font color=blue>$pmon</font>";
		  }
		  ?>
		
		 <form id="formoni" name="formmoni" method="post" action="portal">
			<div class="form-group">
			<div class="form-row">
				<div class="col-md-3">
				API Key<br>
				 <input class="form-control" name="monuser" type="text" id="monuser" value="<?php echo $monusername ;?>" />
				</div>
				<div class="col-md-3">
				Secret Key<br>
				<input class="form-control" name="monpassword" type="text" id="monpassword" value="<?php echo $monpassword ;?>" />
				</div>
				<div class="col-md-3">
				Contract Code<br>
				<input class="form-control" name="moncontract" type="text" id="moncontract" value="<?php echo $moncontract ;?>" />
				</div>
				<div class="col-md-3">
				<br>
				<input class="form-control" value="Submit" type="submit" />
				</div>
			</div>		
			</div>
		</form>          
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
	window.location="crediteasy?id="+a;
	
}

</script>



</html>
