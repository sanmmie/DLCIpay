<?php
session_start();

ini_set('max_execution_time', 300);
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/money-transfer";
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



if(isset($_SESSION['montra'])){
	$msg=$_SESSION['montra'];
	unset($_SESSION['montra']);
}

//get fee
$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$bankfee=$rsb['money'];
$start=0;

if(isset($_POST['accno'])){
	$accno=$_POST['accno'];
	$bank=$_POST['bank'];
	$amount=$_POST['amt'];
	$bankname=$_POST['bkname'];
	@$scap=mysqli_real_escape_string($wole,$_POST['scap']);
	
	if(floatval($amount)<200){
		$start=0;
		$msg="<p><font color=red><b>Invalid amount. Amount should be greater than &#8358;200</b></font></p>";	
	}
	else{
		$start=1;
	
		$url="http://mobileairtimeng.com/money-transfer/get-account?userid=$airuser&pass=$airpass&accno=$accno&bankcode=$bank&allowance=1";
		@$str = file_get_contents($url);
		
		$sdk=explode("|",$str);
		$pos=$sdk[0];
		if($pos == 'success'){
			//deduct money					
			$accname=$sdk[1];
			
			$vat=0;
						
			$fee=$bankfee;
			$amt1=$amount;
			$dtotal=$amount+$fee;
			$mtotal=$dtotal;
			$amount=number_format($amount,2,".",",");
			$dtotal=number_format($dtotal,2,".",",");
			
			
			$dcontent="<b>Account Name: </b> $accname<br><b>Account Number:</b> $accno<br><b>Bank:</b> $bankname<br><b>Amount:</b> &#8358;$amount<br><b>Fee:</b> &#8358;$fee<br><b>VAT:</b> &#8358;$vat<br><b>Short description:</b> $scap<br><b>Total:</b> &#8358;$dtotal";
				
		}
		else{
			$start=0;				
			if (isset($sdk[1])){
				// do stuff	
				$tmg=$sdk[1];
			}
			else{
				$tmg="An error occured!";
			}
			
			$msg="<p><font color=red><b>$tmg</b></font></p>";
		}
		//header("location:vtransfer-money.php");	
	
	}
	
	
}


if(isset($_POST['daccno'])){
	$daccno=$_POST['daccno'];
	$dbank=$_POST['dbank'];
	$dbankname=$_POST['bankname1'];
	$damount=$_POST['damt'];
	$dfee=$_POST['fee'];
	$dmtotal=$_POST['mtotal'];
	extract($_POST);
	
	if($damount<200){
		$start=0;
		$msg="<p><font color=red><b>$damount Invalid amount. Amount should not be less than &#8358;200</b></font></p>";	
	}
	else{
		if($dmtotal>$bal){
			$start=0;
			$msg="<p><font color=red><b>You have insufficient balance.</b></font></p>";	
		}
		else{
			if(!isset($dscap) || empty($dscap) || $dscap==''){
		      $caption=urlencode("Fund Transfer");
			}
			else{
				$caption=urlencode(substr($dscap,0,14));
			}
		
			$start=2;
			$refno='FT'.uniqid();
			$batch=$refno;
			$reqamt=$dmtotal;
			$amt=$damount;
			//deduct money
			$det="N$damount to $dbankname $daccno, Fee: N$dfee| Refno: $refno";	
				
			$qb=mysqli_query($wole,"select * from users where uname='$uname'");
			$rb=mysqli_fetch_array($qb);
			$bal=$rb['bal'];	
				
			$id=0;
			$dt=date('Y-m-d H:i:s');
			$info=$det;
			$q=mysqli_query($wole,"insert into transactions values ('$id','$dt','$uname','$batch','money-transfer','$info','$daccno','$reqamt','$bal','$reqbal','pending')");
			
			$url="http://mobileairtimeng.com/money-transfer/send-money?userid=$airuser&pass=$airpass&accno=$daccno&bankcode=$dbank&amount=$damount&reference=$refno&caption=$caption";
			@$strh = file_get_contents($url);
			//$str='success|$refno|successful';
			
			$sdk=explode("|",$strh);
			$pos=$sdk[0];
			if($pos == 'success' || $pos == 'pending' || empty($pos) ){
				
				$reqbal=$bal-$reqamt;					
				$qu=mysqli_query($wole,"update users set bal='$reqbal' where uname='$uname'");	
				
				$start=2;
				$damount=number_format($damount,2,".",",");
				@$refno=$sdk[1];
				@$message=$sdk[2];
				$dcontent2="<b>$message</b><br>Reference No: $refno";
				
				$qu=mysqli_query($wole,"update transactions set status='$pos' where batch='$refno'");
				
				//send email
				$dsemail="<h3>Dear $fname</h3><p>Details of transaction below;<br><b>Account No:</b> $daccno<br><b>Bank:</b> $dbankname<br><b>Amount charged:</b> $reqamt<br><b>Initial Bal:</b> $bal<br><b>New Bal:</b> $reqbal</p>
				<p>Thank you.</p>";					
				$to = $email;
				$subject = "Money Transfer - $refno";
				$message = $dsemail . ""; 
				
				$bsend=sendemailnow($to,$subject,$message,$weburl,$webname);
				
			}
			else{
				$start=0;				
				if (isset($sdk[2])){
					// do stuff	
					$tmg=$sdk[2];
				}
				else{
					$tmg="An error occured!";
				}
				
				$msg="<p><font color=red><b>$tmg</b></font></p>";
			}
		}			
	}
		
}





$curl = curl_init();
$base_url = "http://api.ravepay.co/banks";
$header = array("Content-Type: application/json");

curl_setopt_array($curl, array(
  CURLOPT_URL => $base_url,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 180,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_HTTPHEADER => $header,
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$dresponse = json_decode($response, true);


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DLCIpay | Mobile Transfer</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Mobile Transfer</a>
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
        <li class="breadcrumb-item active">Money Transfer</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Quick Money Transfer</h1>
		  <p>
				   <?php echo @$msg;?>
				    <?php				   
					   if($start==0){
					   
					   ?>	
				    Transfer funds securely from your wallet to any bank account in Nigeria.
				   <form id="form1" name="form1" method="post" action="money-transfer">
				   <div class="form-group">
					<div class="form-row">
						<div class="col-md-4">
							Bank<br>
							<select name="bank" id="bank" class="form-control">
							<option value="0">Select</option>
							<?php
							if(@$dresponse['status']=='success'){
								foreach ($dresponse['data'] as $key => $value) {
									if($value['country']=='NG'){
										$bankn=strtoupper($value['name']);
										$bankc=$value['code'];
										echo "<option value='$bankc'>$bankn</option>";
									}
									
								}
							}
							?>												
							</select><input type="hidden" name="bkname" id="bkname">
						</div>
						<div class="col-md-4">
							Account Number<br>
							 <input type="number" name="accno"  id="accno" class="form-control" onKeyPress="return isNumber(event)"  />
						</div>
						<div class="col-md-4">
							Amount:<br>
								<input name="amt" type="number" id="amt" class="form-control" onKeyPress="return isNumber(event)" />															
						</div>
						<div class="col-md-4">
							Short description (optional)<br>
							<i>(Max 14 Characters)</i><br>
								<input name="scap" id="scap" type="text" class="form-control" maxlength="14" placeholder='optional'  />
						</div>						
					</div>						
					</div>
					<input name="tbut1" type="button" id="tbut1" onClick="confirmt()" value="Confirm"/>	
					</form>
					 <?php
						}
						elseif($start==1){
						
					  ?>
					<form id="form2" name="form2" method="post" action="money-transfer.php" onSubmit="return gosubmit()">	
					  <div id='conf1' style=" background-color: #CCCCCC; padding: 6px">
								<?php echo @$dcontent ;?>
						</div>
						<center>
						<input name="damt" type="hidden" id="damt" value="<?php echo @$amt1 ?>"  />
						<input name="dbank" type="hidden" id="dbank" value="<?php echo @$bank ?>"  />
						<input name="daccno" type="hidden" id="daccno" value="<?php echo @$accno ?>"  />
						<input name="mtotal" type="hidden" id="mtotal" value="<?php echo @$mtotal ?>"  />
						<input name="fee" type="hidden" id="fee" value="<?php echo @$fee ?>"  />
						<input name="bankname1" type="hidden" id="bankname1" value="<?php echo @$bankname ?>"  />
						<input name="dscap" type="hidden" id="dscap" value="<?php echo @$scap ?>"  />
						
						<input name="tbut2" type="button" id="tbut2" onClick="cancelt()" value="Cancel" style=" width: 40%"/>
						 <input name="tbut" type="submit" id="tbut" value="Proceed" style=" width: 40%"/>
						</center>
					</form>	
				  
					  <?php
						}
						elseif($start==2){
					  ?>
						<div id='conf2' style=" background-color: #CCCCCC; padding: 6px">
								<?php echo @$dcontent2 ;?>
						</div>
					 <?php
						}
					  ?>
		    
		
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
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='money-transfer' order by id desc");
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
					elseif($trans=='money-transfer'){
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

function confirmt(){
	if(document.getElementById('bank').value=="0"){
		alert("Select bank");
		exit;
	}
	if(document.getElementById('accno').value==''){
		alert("Enter account number");
		exit;
	}	
	if(document.getElementById('amt').value<200){
		alert("Minimum amount is 200 Naira");
		exit;
	}
	var element=document.getElementById('bank');
	document.getElementById('bkname').value= element.options[ element.selectedIndex ].text;
	document.getElementById('tbut1').value="Please wait";
	document.getElementById('form1').submit();
	
}

function cancelt(){
	window.location='money-transfer';
}

function gosubmit(){
	var conf=confirm("Are you sure you want to proceed?");
	if(conf==1){
		document.getElementById('tbut').value="Please wait";
		return true;
	}
	else{
		return false;
	}
}





</script>


</html>