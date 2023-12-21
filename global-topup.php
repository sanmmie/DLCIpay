<?php
session_start();

ini_set('max_execution_time', 300);
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
$globvtu=$rsb['globvtu'];

if(isset($_SESSION['globalvtu'])){
	$msg=$_SESSION['globalvtu'];
	unset($_SESSION['globalvtu']);
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
  <title><?php echo $titleweb ;?></title>
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
    <a class="navbar-brand" href="dashboard"><?php echo $titleweb ;?></a>
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
        <li class="breadcrumb-item active">Global Topup</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Global Topup</h1>
		  <p>
		 You can top up any mobile network in the world and pay in Naira from your wallet. Phone number must be in international format <b>without (+)</b> .e.g. 2349062547966 is a Nigeria phone number, 233558285196 a Ghana number, 13175154840 a United States etc.
				   <form id="form1" name="form1" method="post" action="global-topup">
				   <div class="form-group">
					<div class="form-row">
						<div class="col-md-8">
				 	  <input type="hidden" name="stage"  id="stage" />
					  <input type="hidden" name="item"  id="item" />
				   	   <?php echo @$msg ;?>
					   
					    <?php
						  	if(!isset($_POST['stage']) || @$_POST['stage']=='0' ){
								echo 'Enter Phone Number<br><input type="text" name="phone"  id="phone" />';
								echo '<input name="tbut" type="button" id="tbut" onClick="vfy()" value="Verify" style="background-color: yellow"/>';
							}
							elseif(@$_POST['stage']=='1'){
								//get request
								$phone=mysqli_real_escape_string($wole,$_POST['phone']);
								echo "Phone Number<br><input type='text' name='phone' id='phone' value='$phone' class='form-control' readonly />";
								$url="http://mobileairtimeng.com/httpapi/globalvtu-conf?userid=$airuser&pass=$airpass&phone=$phone";
								@$request=file_get_contents($url);
								
								if($request){
									$result = json_decode($request, true);
									if($result['response']=="OK"){
										$network=$result['info']['operator'];
										$country=$result['info']['country'];
										if(count($result['products'])>1){
											//echo "<hr>Products with fixed amount";
											@$currency=$result['products'][0]['topup_currency'];
											@$product=$result['products'][0]['id'];
											
											
											echo "<font color=blue><b>Country:</b> $country<br>";
											echo "<b>Network:</b> $network<br>";
											
											echo "<input type='hidden' name='product' id='product' value='$product'   />";
											echo "<input type='hidden' name='amt' id='amt'    />";
											echo "<input type='hidden' name='expamt' id='expamt' value='0'   />";
											echo "<input type='hidden' name='enetwork' id='enetwork' value='$network'   />";
											echo "Select Amount<br><select name='batch' id='batch' class='form-control'>";
											$tests='';
											foreach ($result['products'] as $products) {
												$value = $products['id'] . "|" . $products['topup_amount'] . "|" . $products['price'];
												$text= $currency . $products['topup_amount'] . " (NGN". number_format($products['price'],2,".",",") . ")" ;
												echo "<option value='$value'>$text</option>";
											}
											echo "</select>";
											echo "<input type='button' onClick='cancelit()' value='Cancel' style=' width:40%' /> &nbsp;&nbsp;";
											echo "<input type='button' onClick='topupa()' id='tbut' value='Proceed' style=' width:40%' />";
										}
										else{
											//echo "<hr>Products with variable amount";
											@$currency=$result['products'][0]['topup_currency'];
											@$product=$result['products'][0]['id'];
											@$rate=$result['products'][0]['rate'];
											$rsp="1$currency = $rate NGN";
											echo "<font color=blue><b>Country:</b> $country<br>";
											echo "<b>Network:</b> $network<br>";
											echo "<b>Rate:</b> $rsp</font><br>";
											echo "Enter Amount<br><input type='number' name='amt' id='amt' placeholder='$currency' onKeyPress='return isNumber(event)' onKeyUp='calc()' class='form-control' />";
											echo "<input type='hidden' name='rate' id='rate' value='$rate'   />";
											echo "<input type='hidden' name='product' id='product' value='$product'   />";
											echo "<input type='hidden' name='expamt' id='expamt' value='0'   />";
											echo "<input type='hidden' name='enetwork' id='enetwork' value='$network'   />";
											echo "<div id='tot' style='color: blue'> </div>";
											echo "<input type='button' onClick='cancelit()' value='Cancel' style=' width:40%' /> &nbsp;&nbsp;";
											echo "<input type='button' onClick='topup()' id='tbut' value='Proceed' style=' width:40%' />";
										}
										//$id=$result['products'][0]['id'];
												
									}
									else{
										echo 'Mobile number details not available<br><input name="rtbut" type="reset" id="rtbut" onClick="cancelit()" value="Cancel" style="background-color: yellow"/>';
									}
								}
								else{
									echo 'Mobile number cannot be verified<br><input name="rtbut" type="reset" id="rtbut" onClick="cancelit()" value="Cancel" style="background-color: yellow"/>';
								}
							}
							elseif(@$_POST['stage']=='2'){
								extract($_POST);
								$reqamt=$expamt-($expamt*$globvtu/100);
								
								if($reqamt>$bal){
									$_SESSION['globalvtu']="<p><font color=red><b>Insufficient balance</b></font></p>";
								}
								else{
									$url="http://mobileairtimeng.com/httpapi/globalvtu?userid=$airuser&pass=$airpass&phone=$phone&product=$product&amt=$amt";
									@$str = file_get_contents($url);
									
									$sdk=explode("|",$str);
									$pos=$sdk[0];
									if($pos == '100' || empty($pos)){
										//deduct money					
										$tmg="Recharge successful!";
										$status=$tmg;
										$_SESSION['globalvtu']="<p><font color=blue><b>$tmg</b></font></p>";	
										
										$qb=mysqli_query($wole,"select * from users where uname='$uname'");
										$rb=mysqli_fetch_array($qb);
										$bal=$rb['bal'];
										
										$reqbal=$bal-$reqamt;					
										$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");			
										
										$id=0;
										$dt=date('Y-m-d H:i:s');
										$info=$enetwork. " ".$amt;
										$batch=uniqid();										
										
										$dtime=date('Y-m-d H:i:s');
										$q=mysqli_query($wole,"insert into transactions values ('$id','$dtime','$uname','$batch','airtime','$info','$phone','$reqamt','$bal','$reqbal','$status')");
														
										//send email notification
										$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Phone:</b> $phone<br><b>Description:</b> $info<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
						<p>Thank you.</p>";					
										$to = $email;
										$subject = "Global Topup Transaction";
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
										
										$_SESSION['globalvtu']="<p><font color=red><b>$tmg</b></font></p>";
									}
									
								}								
								echo "<script>window.location='global-topup'</script>";
								//header("location:global-topup");	
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
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='airtime' order by id desc");
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
		//return true;
			
        return false;

    }
    
	return true;
}

function calc(){
	var rate=parseFloat(document.getElementById('rate').value);
	var amt=parseFloat(document.getElementById('amt').value);
	var total=rate * amt;
	document.getElementById('expamt').value=total;
	document.getElementById('tot').innerHTML="Price: NGN "+total;
}

function topup(){
var phone=document.getElementById('phone').value;
var amt=document.getElementById('amt').value;

phone = phone.replace(/\s+/g, '');

if(phone.length<5){
	alert("Please enter phone number.");
	exit;
}

document.getElementById('phone').value=phone;

if(amt<1){
	alert("Enter amount");
	exit;
}

var conf=confirm("Are you sure you want to proceed?");
if(conf==0){
	exit;
}

document.getElementById('stage').value='2';
document.form1.submit();
document.getElementById('tbut').style.disabled=true;
}


function topupa(){
var phone=document.getElementById('phone').value;

phone = phone.replace(/\s+/g, '');

if(phone.length<5){
	alert("Please enter phone number.");
	exit;
}

document.getElementById('phone').value=phone;

var bth=document.getElementById('batch').value;
var info=bth.split("|");

document.getElementById('product').value=info[0];
document.getElementById('amt').value=info[1];
var expamt=parseFloat(info[2]);
document.getElementById('expamt').value=expamt;

var amt=parseFloat(document.getElementById('amt').value);
if(amt<1){
	alert("Select amount");
	exit;
}

var conf=confirm("Are you sure you want to proceed?");
if(conf==0){
	exit;
}

document.getElementById('stage').value='2';
document.form1.submit();
document.getElementById('tbut').style.disabled=true;
}


function vfy(){
	var phone=document.getElementById('phone').value;
	if(phone.length<5){
		alert("Please enter phone number in international format.");
	}
	else{
		document.getElementById('stage').value='1';
		document.form1.submit();
	}
}

function cancelit(){
	document.getElementById('stage').value='0';
	document.form1.submit();
}

</script>


</html>
