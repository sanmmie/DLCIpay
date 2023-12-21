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
$addisp="";
$updisp="none";
$deldisp="none";
$frmdisp="none";
$readonly='';

$selopts='';

if(isset($_POST['chkno'])){
	extract($_POST);
	$mtnvtu=(float)$mtnvtu;
	$glovtu=(float)$glovtu;
	$glodata=(float)$glodata;
	$airvtu=(float)$airvtu;
	$etivtu=(float)$etivtu;
	$globvtu=(float)$globvtu;
	$dataprice=(float)$dataprice;
	$smsprice=(float)$smsprice;
	$smsprice1=(float)$smsprice1;
	$waec=(float)$waec;
	$neco=(float)$neco;
	$billfee=(float)$billfee;
	$moneyfee=(float)$moneyfee;
	$mtnss=(float)$mtnss;
	
	mysqli_query($wole,"update prices set mtnvtu='$mtnvtu', glovtu='$glovtu', glodata='$glodata', etivtu='$etivtu', airtelvtu='$airvtu', globvtu='$globvtu', dataprice='$dataprice', dataprice1='$dataprice1', dataprice2='$dataprice2', smsprice='$smsprice', smsprice1='$smsprice1', waec='$waec', neco='$neco', bill='$billfee', money='$moneyfee', mtnss='$mtnss' where utype='$ulevel'");
	
	$_SESSION[$sessname8]="<font color=blue>Price updated!</font>";	;
	header("location: prices?suser=$ulevel");
}

if(isset($_POST['addnew'])){
	extract($_POST);
	$ulevel=mysqli_real_escape_string($wole,$ulevel);
	$mtnvtu=(float)$mtnvtu;
	$glovtu=(float)$glovtu;
	$glodata=(float)$glodata;
	$airvtu=(float)$airvtu;
	$etivtu=(float)$etivtu;
	$globvtu=(float)$globvtu;
	$dataprice=(float)$dataprice;
	$dataprice1=(float)$dataprice1;
	$dataprice2=(float)$dataprice2;
	$smsprice=(float)$smsprice;
	$smsprice1=(float)$smsprice1;
	$waec=(float)$waec;
	$neco=(float)$neco;
	$billfee=(float)$billfee;
	$moneyfee=(float)$moneyfee;
	$mtnss=(float)$mtnss;
	
	$q=mysqli_query($wole,"select * from prices where utype='$ulevel'");
	if(mysqli_num_rows($q)>=1){
		$_SESSION[$sessname8]="<font color=red>Duplicated user level name!</font>";
		header("location: prices");
	}
	else{
		$id=0;
		mysqli_query($wole,"insert into prices values('$id','$ulevel','$mtnvtu','$glovtu','$glodata','$airvtu','$etivtu','$globvtu','$dataprice','$dataprice1','$dataprice2','$smsprice','$smsprice1','$waec','$neco','$billfee','$moneyfee','$mtnss')");
		$_SESSION[$sessname8]="<font color=blue>New user level added!</font>";
		header("location: prices?suser=$ulevel");
	}
		
}

if(isset($_POST['cancelrec'])){
	$_SESSION['seluser']='none';
}

if(isset($_POST['what']) && $_POST['what']=="delme" ){
	extract($_POST);
	$ulevel=mysqli_real_escape_string($wole,$ulevel);
	
	if($ulevel=='user'){
		$_SESSION[$sessname8]="<font color=red>Cannot delete default user!</font>";
	}
	else{
		mysqli_query($wole,"delete from prices where utype='$ulevel'");
		mysqli_query($wole,"update users set usertype='user' where usertype='$ulevel'");
		$_SESSION[$sessname8]="<font color=blue>User level deleted!</font>";
	}
	
	header("location: prices");		
}

if(isset($_REQUEST['suser'])){
	extract($_REQUEST);
	$suser=mysqli_real_escape_string($wole,$suser);
	if($suser=="01new"){
		$_SESSION['seluser']='none';
		$readme="";
		$frmdisp="";
	}
	elseif($suser=="none"){
		$_SESSION['seluser']='none';
		$readme="";
		$frmdisp="none";
	}
	else{
		$_SESSION['seluser']=$suser;
		$addisp="none";
		$updisp="";
		$deldisp="";
		$readme="readonly";
		$frmdisp="";
	}
	$selopts=$suser;
	
}

if(isset($_SESSION['seluser']) && $_SESSION['seluser']!='none'){
	$seluser=$_SESSION['seluser'];
	$q=mysqli_query($wole,"select * from prices where utype='$seluser'");
	$r=mysqli_fetch_array($q);
	$ulevel=$r['utype'];
	$mtnvtu=$r['mtnvtu'];
	$glovtu=$r['glovtu'];
	$glodata=$r['glodata'];
	$airvtu=$r['airtelvtu'];
	$etivtu=$r['etivtu'];
	$globvtu=$r['globvtu'];
	$dataprice=$r['dataprice'];
	$dataprice1=$r['dataprice1'];
	$dataprice2=$r['dataprice2'];
	$smsprice=$r['smsprice'];
	$smsprice1=$r['smsprice1'];
	$waec=$r['waec'];
	$neco=$r['neco'];
	$billfee=$r['bill'];
	$moneyfee=$r['money'];
	$mtnss=$r['mtnss'];
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
 
  
  <style type="text/css">
<!--
.style1 {color: #FF0000}
-->
  </style>
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
        <li class="breadcrumb-item active">Prices</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
		 <h3>Discount & Prices</h3>
         <?php echo $msg ;?>
		  <form method="post" action="prices" id="form2" name="form2">	
			<div class="col-md-6">
				Select user level<br>
				<select class="form-control" name="suser" id="suser" onChange="myuser(this.value)">
					<option value="none">Select</option>
					<option value="01new" <?php if ($selopts=="01new") echo 'selected'; ?>>Create new</option>
					<?php
						$qs=mysqli_query($wole,"select * from prices order by id");
						while($rs=mysqli_fetch_array($qs)){
							$optns=$rs['utype'];
							if ($selopts==$optns){
								echo "<option value='$optns' selected >$optns</option>";
							}
							else{
								echo "<option value='$optns'>$optns</option>";
							}
							
						}
					?>
				</select>					
			</div>
		 </form>
		 <div style="display: <?php echo $frmdisp ;?>">
		 <form method="post" action="prices" id="form1" name="form1">					
          <div class="form-group">		  		 
				<div class="col-md-6">
					Level<br>
					 <input class="form-control" name="ulevel" type="text" id="ulevel" value="<?php echo @$ulevel ;?>" <?php echo @$readme ;?>  />
				</div>            	
              	<div class="col-md-6">
					MTN VTU Discount<br>
					 <input class="form-control" name="mtnvtu" type="number" id="mtnvtu" value="<?php echo @$mtnvtu ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					MTN shareNsell Discount<br>
					 <input class="form-control" name="mtnss" type="number" id="mtnss" value="<?php echo @$mtnss ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					GLO VTU Discount<br>
					 <input class="form-control" name="glovtu" type="number" id="glovtu" value="<?php echo @$glovtu ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					GLO Data Discount<br>
					 <input class="form-control" name="glodata" type="number" id="glodata" value="<?php echo @$glodata ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					Airtel VTU Discount<br>
					 <input class="form-control" name="airvtu" type="number" id="airvtu" value="<?php echo @$airvtu ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					Etisalat VTU Discount<br>
					 <input class="form-control" name="etivtu" type="number" id="etivtu" value="<?php echo @$etivtu ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					Global Top Discount<br>
					 <input class="form-control" name="globvtu" type="number" id="globvtu" value="<?php echo @$globvtu ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					MTN SME Data 1GB Price<br>
					 <input class="form-control" name="dataprice" type="number" id="dataprice" value="<?php echo @$dataprice ;?>" />
				</div>
				<div class="col-md-6">
					MTN SME Data 2GB Price<br>
					 <input class="form-control" name="dataprice1" type="number" id="dataprice1" value="<?php echo @$dataprice1 ;?>" />
				</div>
				<div class="col-md-6">
					MTN SME Data 5GB Price<br>
					 <input class="form-control" name="dataprice2" type="number" id="dataprice2" value="<?php echo @$dataprice2 ;?>" />
				</div>
				<div class="col-md-6">
					Bill Payment (Startimes, Gotv/DStv, Electricity etc) fee. e.g. Quickteller adds N100<br>
					 <input class="form-control" name="billfee" type="number" id="billfee" value="<?php echo @$billfee ;?>" />
				</div>
				<div class="col-md-6">
				Money Transfer fee<br>
					 <input class="form-control" name="moneyfee" type="number" id="moneyfee" value="<?php echo @$moneyfee ;?>" />
				</div>
				<div class="col-md-6">
					WAEC Result PIN checker<br>
					 <input class="form-control" name="waec" type="number" id="waec" value="<?php echo @$waec ;?>" />
				</div>
				<div class="col-md-6">
					NECO Result Token<br>
					 <input class="form-control" name="neco" type="number" id="neco" value="<?php echo @$neco ;?>" />
				</div>
				<div class="col-md-6">
					Price per SMS (Normal route)<br>
					 <input class="form-control" name="smsprice" type="number" id="smsprice" value="<?php echo @$smsprice ;?>" step=".01" />
				</div>
				<div class="col-md-6">
					Price per SMS (Corporate)<br>
					 <input class="form-control" name="smsprice1" type="number" id="smsprice1" value="<?php echo @$smsprice1 ;?>" step=".01" />
				</div>
				<div class="col-md-6" style="display: <?php echo $addisp ;?>">					
					  <br>
					  <input class="form-control" name="addnew" type="submit" id="addnew" value="Add New" />
				</div>	
				<div class="col-md-6" style="display: <?php echo $updisp ;?>">					
					  <input name="what" type="hidden" id="what" /><br>
					  <input class="form-control" name="chkno" type="submit" id="chkno" value="Update" />
				</div>
				<div class="col-md-6" style="display: <?php echo $deldisp ;?>">										  
					  <br>
						<input class="form-control" name="delrec" type="button" id="delrec" value="Delete" onClick="deluser()" />
					  <span class="style1">All users on this level will be moved to the default user level and cannot be reversed when you delete. </span>
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

function myuser(a){
	document.form2.submit();
}

function deluser(){
	document.getElementById('what').value='delme';	
	
	
	var conf=confirm("Are you sure sure want to delete this user level?");
	if(conf==0){
		exit;
	}
	document.form1.submit();
	
}

</script>



</html>
