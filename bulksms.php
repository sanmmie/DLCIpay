<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/bulksms";
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

if(isset($_POST['smsto'])){
	extract ($_POST);

	$postdata = http_build_query( 
			array(
				'gtype' => $gtype,
				'uname' => $uname, 
				'pass' =>  $pass,
				'smsfrom' => $smsfrom,
				'smsto' => $smsto,
				'smsmsg' => $smsmsg
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
	
	$_SESSION[$sessname2]=$gob;		
	header("location:$weburl/bulksms");
	
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
  <title>DLCIpay | Bulk SMS</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Bulk SMS</a>
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
        <li class="breadcrumb-item active">Bulk SMS</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Bulk SMS</h1>
          <p>
		  <?php echo $msg ;?>
		  <form action="bulksms" method="post" name="form1" id="form1">
		  <div class="form-group">
		  	<div class="form-row">
              		<div class="col-md-6">
						 1 SMS= 160 characters<br>
		    			From (Sender, max of 11 characters):<br>	
					   <input class="form-control" name="smsfrom" type="text" id="smsfrom" maxlength="11" />
					</div>					
			</div>		  	
		  </div>
		   <div class="form-group">
		  		<div class="form-row">
					<div class="col-md-6">
						To (Receivers):<br>
					  One phone number per line or separate numbers with comma .e.g 23480xxxx, 080xxxx etc.<br>
					  <textarea class="form-control" name="smsto" rows="4"  id="smsto"></textarea>
					</div>				              		
				</div>
			</div>
			 <div class="form-group">	
				<div class="form-row">					
              		<div class="col-md-6">
						Message:<br>
						<textarea class="form-control" name="smsmsg"  id="smsmsg" onKeyDown="textCounter(document.getElementById('smsmsg'),160)"  onKeyUp=	"textCounter(document.getElementById('smsmsg'),159)" rows="4"  ></textarea><div id="counter" style="color: #006699; font-size:11px"></div>
					</div>
				</div>
			</div>	
			<div class="form-group">	
				<div class="form-row">					
              		<div class="col-md-6">
						Route:<br>
						<select name="route" id="route" class="form-control">
							<option value="1">Normal</option>
							<option value="2">Corporate (Delivers to DND)</option>
						</select>
					</div>
				</div>
			</div>			
		  <div class="form-group">	
				<div class="form-row">
					<div class="col-md-6">
						<input class="form-control" type="button" value="Send SMS" onClick="sendsms()" style=" background-color: #FFCC66" />
						<input name="gtype" id="gtype" type="hidden" value="">
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
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='bulksms' order by id desc");
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
					elseif($trans=='bulksms'){
						$trans=strtoupper($trans);						
						$qc=mysqli_query($wole,"select * from gbogbotxtsum where batch='$batch'");
						if(mysqli_num_rows($qc)==1){
							$rpt="<a href='smsreport?bth=$batch'>View report here</a>";
							$msg="$describe Paid N$amt. $rpt";
						}
						else{
							$msg="$describe Paid $amt";
						}	
						
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
	if (field.value.length>=480){
		var textm=field.value;
		field.value=textm.substring(0,479);
	}
}
var h=field.value.length / maxlimit;
smspage=Math.ceil(h);
var g=smspage-1;
b1=field.value.length-(g*maxlimit);

c1='Page ' + smspage + ' /' + b1 + ' Characters';
document.getElementById('counter').innerHTML= c1;

}

function sendsms(){
	if(document.getElementById('smsfrom').value==""){
			alert("Enter sender");
			exit;
		}
	
	if(document.getElementById('smsto').value==""){
			alert("Enter recepient(s)");
			exit;
	}
	
	
	
	var conf=confirm("Are you sure you want to send the SMS?");
	if(conf==0){
		exit;
	}
	
	if(document.getElementById('smsto').value!=""){
		var abj= document.getElementById('smsto').value;
		var aCars = abj.split(',');	
		if ((document.getElementById('smsfrom').value=="") || (document.getElementById('smsmsg').value=="")){
			alert ("Enter Sender's id and message.");
			exit;
		}
		
		var gty='';
		var a=document.getElementById('route').value;
		if(a==1){
			gty="sendsms1";
		}
		else if(a==2){
			gty="sendsms2";
		}
		
		var smsfrom=document.getElementById('smsfrom').value;
		var smsto=document.getElementById('smsto').value;
		var smsmsg=document.getElementById('smsmsg').value;
		document.getElementById('gtype').value=gty;
		
		document.form1.submit();
		
	}
}


</script>

</html>