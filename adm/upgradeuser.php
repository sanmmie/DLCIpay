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
if(isset($_SESSION[$sessname8])){
	$msg=$_SESSION[$sessname8];
	unset($_SESSION[$sessname8]);
}


if(@$_POST['what']=="update"){

	@$id=(int)mysqli_real_escape_string($wole,$_POST['id']);
	$fid=$id;

	@$phone=mysqli_real_escape_string($wole,$_POST['phone']);

	@$inibal=mysqli_real_escape_string($wole,$_POST['inibal']);
	@$suser=mysqli_real_escape_string($wole,$_POST['suser']);

	if($suser==$inibal){
		$_SESSION[$sessname8]="<font color=red>Account cannot be changed to the same level!</font>";
		header('location:upgradeuser?id='.$fid."&phone=".$phone);		
		exit;
	}

	$query="select * from users where id=".$id;
	$res=mysqli_query($wole,$query);
	$row=mysqli_fetch_array($res);

	@$email=$row['email'];
	@$unm=$row['uname'];

	$fname=$row['fname'];
	$acctype=$row['usertype'];
	$approved=$row['approved'];
	
	$dty=date('d-m-Y');
	$expdate=date('Y-m-d');
	$dtexp=date('Y-m-d', strtotime($expdate. ' + 2 days'));
		
	$newmsg='';
	//@$units=$inibal+$addunit;
	$smsmess="Account update. Your account has changed to $suser.";
	$query="update users set usertype='$suser' where id=".$id;	
	$res=mysqli_query($wole,$query);

	//save credithistory
	$id=0;
	$dt=date('Y-m-d');
	@$batch=uniqid();
	$dc="Account update";
	$carr="Account updated to $suser";
	
	$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$unm','$batch','$dc','$carr','$phone','','','completed')");
	
	//send sms notification
     $fnm=explode(" ",$fname);
	if(count($fnm)>0){
		$fno= $fnm[0];
	}
	else{
		$fno= $fname;
	}

	//send email notification	
	//$adn=number_format($addunit,2,".",",");
	//$adn1=number_format($units,2,".",",");
	
	$subject = "Account Update";
	$subject2 = "Account Update for $fno";
	
	$mess1="Hello <b>$fno</b><br>Your account has been changed to $suser.<br>Thank you";
	//send the email
	
	$bsend=sendemailnow($email,$subject,$mess1,$weburl,$webname);
		
	$_SESSION[$sessname8]="<font color=blue>Account updated!</font>";
	
	header('location:upgradeuser?id='.$fid."&phone=".$phone);

}

if(isset($_GET['id'])){

	@$id=(int)$_GET['id'];

	@$phone=mysqli_real_escape_string($wole,$_GET['phone']);	

	@$addunit=(int)$_GET['ad'];	

	$query="select * from users where id=".$id;

	$res=mysqli_query($wole,$query);

	$row=mysqli_fetch_array($res);

	@$inibal=$row['usertype'];	

	@$fname=$row['fname'];	
	@$acctype=$row['usertype'];	

	if($phone==""){


		@$phone=$row['phone'];

	}//$msg="";

	if($addunit==""){

		@$addunit="";

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
        <li class="breadcrumb-item active">Change User Level</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
		<h3>Change User Level</h3>
         <?php echo $msg ;?>
		 <form method="post" action="upgradeuser" id="form1" name="form1">			
          <div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					Phone<br>
					 <input class="form-control" name="phone" type="text" id="phone" value="<?php echo @$phone ;?>" />
					</div>
					<div class="col-md-6">
					<input name="id" type="hidden" id="id" value="<?php if(@$id==""){echo "" ;}else{echo @$id ;} ?>" />
					  <input name="what" type="hidden" id="what" /><br>
					  <input class="form-control" name="chkno" type="button" id="chkno" value="Check Number" onClick="findphone()" />
					</div>
				</div>		
          </div>
		<div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					Name<br>
					  <input class="form-control" name="fname" type="text" id="fname"  value="<?php echo @$fname ; ?>" readonly="true"/>
					</div>
					<div class="col-md-6">
					Current Level<br>
						<input class="form-control" name="inibal" type="text" id="inibal" value="<?php if(@$inibal==""){echo "";}else{echo @$inibal;} ?>"  readonly="true"/>
					</div>
				</div>		
          </div>		  
		   <div class="form-group">
            	<div class="form-row">
	              	<div class="col-md-6">
					New Level<br>
					<select class="form-control" name="suser" id="suser">			
					<?php
						$qs=mysqli_query($wole,"select * from prices order by id");
						while($rs=mysqli_fetch_array($qs)){
							$optns=$rs['utype'];
							echo "<option value='$optns'>$optns</option>";							
						}
					?>
				</select>	
					</div>
					<div class="col-md-6">
					<br>
					 <input class="form-control" type="button" name="Submit2" value="Update Account" onClick="updateacct()"/>
					</div>
				</div>		
          </div>
        </form>
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

var ajx;
var ajxbool;
var smspage=0;
var b1;
var c1;


try{

	ajx=new XMLHttpRequest();

	ajxbool=true;

} catch(e){

	ajx=new ActiveXObject("Microsoft.XMLHTTP");

	ajxbool=true;

}

function findphone(){

	var ph=document.getElementById('phone').value;

	if ((ajxbool==true) &&(ph!="")){		
		ajx.onreadystatechange=function(){
			document.getElementById('chkno').value="Please wait...";
			document.getElementById('id').value="";
			document.getElementById('fname').value="";
			document.getElementById('inibal').value="";


			if(ajx.readyState==4){	
				var resp=ajx.responseText;

				if(resp=="failed"){
					alert("Phone number not registered!");
				}

				else{
					var info=resp.split("|");
					document.getElementById('id').value=info[0];
					document.getElementById('fname').value=info[1];
					document.getElementById('inibal').value=info[2];					
				}

				document.getElementById('chkno').value="Check Number";

			}

		}

		var code="?phone=" + ph ;

		var pga="findeasy1.php" + code;

		ajx.open ("GET",pga,true);

		ajx.send(null);

	}	

	

}


function updateacct(){

	if (document.getElementById('id').value!=""){	
		document.getElementById('what').value="update";

		document.getElementById('form1').submit();

	}

	else{

		alert("Please verify phone number.");	

	}

	

}


</script>



</html>