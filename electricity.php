<?php
session_start();
ini_set('max_execution_time', 500);
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/electricity";
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
$email=$rs['email'];

//get fee
$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$bankfee=(float)$rsb['bill'];
//$bankfee=100;

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
  <title>DLCIpay | Electricity</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Electricity</a>
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
        <li class="breadcrumb-item active">Electricity</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Electricity</h1>
		  <p>
		  Pay your electric bills instantly and print receipt after successful payment.
				   <form id="frmelect" name="frmelect" method="post" action="electricity">
				   <div class="form-group">
					<div class="form-row">
						<div class="col-md-8">
				 	  <input type="hidden" name="stage"  id="stage" />
					  <input type="hidden" name="item"  id="item" />
				   	   <?php echo $msg ;?>
					    <?php
						  	if(!isset($_POST['stage']) || @$_POST['stage']=='0' ){
								echo 'Service:<br>';
								echo '<select name="service" id="service" class="form-control">';
								$url="http://mobileairtimeng.com/httpapi/power-lists?userid=$airuser&pass=$airpass";
								@$request=file_get_contents($url);
								
								if($request){
									$result = json_decode($request, true);
									if($result['response']=="OK"){
										$someArray = $result['result']; // Replace ... with your PHP Array
										foreach ($someArray as $key => $value) {
											$pid= $value["product_id"];
											$pname=$value["name"];
											echo "<option value='$pid'>$pname</option>";
										}
									}
								}
								echo "</select>";
								echo 'Meter No:<br><input name="meterno" type="number" id="meterno"  onKeyPress="return isNumber(event)" class="form-control" />';
								echo 'Meter Type:<br><select name="mtype" id="mtype" class="form-control"><option value=1>Prepaid</option><option value=0>Postpaid</option></select><br>';
								echo '<input type="button" id="tbut" value="Verify Meter" onclick="checkmeter()" class="form-control" style="background-color:#f5b041" >';
							}
							elseif(@$_POST['stage']=='1'){
								$item=mysqli_real_escape_string($wole,$_POST['item']);
								$meterno=$_POST['meterno'];
								$service=$_POST['service'];								
								$mtype=$_POST['mtype'];
								if($mtype==1){
									$msc='Prepaid';
								}
								else{
									$msc='Postpaid';
								}
								
								$url="http://mobileairtimeng.com/httpapi/power-validate?userid=$airuser&pass=$airpass&service=$service&meterno=$meterno&allowance=1";
								@$request=file_get_contents($url);
								//$request='{"code":100,"message":"Nwobodo Chidera"}';
								if($request){
									$result = json_decode($request, true);
									if($result['code']=="100"){
										$metername=$result['message'];
										echo "<b>Service:</b> $item<br>";
										echo "<b>Meter No:</b> $meterno<br>";
										echo "<b>Meter Type:</b> $msc<br>";
										echo "<b>Name:</b> $metername<br>";
										
										echo '<b>Enter amount:</b><br><input name="amt" type="number" id="amt"  onKeyPress="return isNumber(event)"  style=" width:65%" class="form-control" /><BR>';
										echo "<input type='hidden' name='product' id='product' value='$service'   />";
										echo "<input type='hidden' name='disco' id='disco' value='$item'   />";
										echo "<input type='hidden' name='metern' id='metern' value='$meterno'   />";
										echo "<input type='hidden' name='metert' id='metert' value='$mtype'   />";
										echo "<input type='button' onClick='cancelit()' value='Cancel' style=' width:30%;'  /> &nbsp;&nbsp;";
										echo "<input type='button' onClick='topupa()' id='tbut2' value='Proceed' style=' width:30%; background-color:#f5b041' />";
									}
									else{
										$message=$result['message'];
										echo "<b><font color=red>Cannot verify meter. $message</font></b><br>";
										echo '<input type="button" id="tbut1" value="Go back" onclick="cancelit()" style=" width:45%; background-color:#fbfcfc " >';
									}
								}
								else{
									echo "<b><font color=red>Unable to connect to service</font></b><br>";
									echo '<input type="button" id="tbut1" value="Go back" onclick="cancelit()" style=" width:45%; background-color:#fbfcfc" >';
								}
							}
							elseif(@$_POST['stage']=='2'){
								$disco=$_POST['disco'];
								$meterno=$_POST['metern'];
								$service=$_POST['product'];
								$mtype=$_POST['metert'];
								$amt=$_POST['amt'];
								$myref='eleplc-'.uniqid();
								
								$reqamt=$bankfee+$amt;
								if($reqamt>$bal){
									echo "<b><font color=red>You have insufficient balance</font></b><br>";
									echo '<input type="button" id="tbut1" value="Go back" onclick="cancelit()" style=" width:45%; background-color:#fbfcfc" >';
								}
								else{
									$url="http://mobileairtimeng.com/httpapi/power-pay?userid=$airuser&pass=$airpass&service=$service&meterno=$meterno&mtype=$mtype&user_ref=$myref&allowance=1&amt=$amt&jsn=json";
									@$request=file_get_contents($url);
									//$request='{"code":100,"message":"Recharge successful","user_ref":"ibk748737","meterno":"36565","pincode":"ibkpin","pinmessage":"ibkpinmessage"}';
									if($request || empty($request)){
										$result = json_decode($request, true);
										if($result['code']=="100" || !isset($result['code']) ){
											@$refno=$result['user_ref'];
											@$message=$result['message'];
											@$pincode=$result['pincode'];
											@$pinmessage=$result['pinmessage'];
											
											$qb=mysqli_query($wole,"select * from users where uname='$uname'");
											$rb=mysqli_fetch_array($qb);
											$bal=$rb['bal'];
											
											//perform neccessary actions
											$newbal=$bal-$reqamt;			
											$qu=mysqli_query($wole,"update users set bal='$newbal' where uname='$uname'");
											
											$id=0;
											$dtime=date('Y-m-d H:i:s');
											$qt=mysqli_query($wole,"insert into power_trans values ('$id','$dtime','$uname','$refno','$disco','$meterno','$pincode','$pinmessage')");
											
											$det="Payment for $disco, $meterno |$refno";			
											
											$dt=date('Y-m-d');
											$info=$det;
											$batch=$refno;
											
											$q=mysqli_query($wole,"insert into transactions values ('$id','$dtime','$uname','$batch','electricity','$info','$meterno','$reqamt','$bal','$reqbal','completed')");
															
											//send email notification
											$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br>Payment for $disco<br><b>Meter no:</b> $meterno<br> $pinmessage<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p><p>Thank you.</p>";					
											$to = $email;
											$subject = "$disco Payment";
											$message = $dsemail . ""; 
											
											$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);
											
											if($pincode==''){
												$msg="<b style='color:blue'>$message</b><br>DETAILS:<br>$pinmessage<br><a href='power-rcpt?refno=$refno' target='_blank'>View receipt</a>";
											}
											else{
												$msg="<b style='color:blue'>$message</b><br>DETAILS:<br>Pin- $pincode<br>$pinmessage<br><a href='power-rcpt?refno=$refno' target='_blank'>View receipt</a>";	
											}
											echo $msg;										
										}
										else{
											$message=$result['message'];
											echo "<b style='color: red'>$message</b>";
											echo "<br><a href='#' onclick='cancelit()' >Go back</a>";
										}
									}
								}													
								
							}
					    ?> 
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
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='electricity' order by id desc");
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
					if($trans=='airtime'){
						$msg="$describe $destination";
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
					elseif($trans=='electricity'){
						$c=explode("|",$describe);
						$refno=$c[1];				
						$msg="$describe. <a href='power-rcpt?refno=$refno' target='_blank'>View receipt</a>";
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
              <span aria-hidden="true">?</span>
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

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57)) {

        return false;

    }
    return true;
}

function topupa(){
		
	var amt=parseInt(document.getElementById('amt').value);
	if(amt<500){
		alert("Minumum amount is 500.");
		exit;
	}
	
	var conf=confirm("Are you sure you want to proceed?");
	if(conf==0){
		exit;
	}
	
	document.getElementById('stage').value='2';
	document.getElementById('tbut2').value="Please wait...";
	document.frmelect.submit();
	document.getElementById('tbut2').style.disabled=true;
}

function checkmeter(){
	var meterno=document.getElementById('meterno').value;
	var element=document.getElementById('service');
	if(meterno==''){
		alert('Please enter meter no');
		exit;
	}
	
	document.getElementById('item').value = element.options[ element.selectedIndex ].text;
	document.getElementById('stage').value=1;
	document.getElementById('tbut').value="Please wait...";
	document.frmelect.submit();
}

function cancelit(){
	document.getElementById('stage').value=0;
	//document.getElementById('tbut').value="Please wait...";
	document.frmelect.submit();
}


</script>

</html>