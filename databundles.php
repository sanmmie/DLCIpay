<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';
include_once 'apomi.php';

if(!isset($_SESSION[$sessname])){
	$_SESSION[$sesspage]="$weburl/databundles";
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

$msg='';
if(isset($_SESSION[$sessname2])){
	$msg=$_SESSION[$sessname2];
	unset($_SESSION[$sessname2]);
}

$tvinfo='';
$netw='';

$sb=mysqli_query($wole,"select * from prices where utype='$usertype'");
$rsb=mysqli_fetch_array($sb);
$mvtu=$rsb['mtnvtu'];
$gvtu=$rsb['glovtu'];
$gdata=$rsb['glodata'];
$avtu=$rsb['airtelvtu'];
$evtu=$rsb['etivtu'];

$gdisc=0;

if(isset($_POST['what'])){
	if($_POST['what']=='getnetwork' ){
		$network=(int)$_POST['network'];
		
		if($network=='5'){
			$net='mtn';
			$gdisc=$mvtu/100;
			$netw=strtoupper($net);
		}
		elseif($network=='6'){
			$net='glo';
			$gdisc=$gdata/100;
			$netw=strtoupper($net);
		}	
		elseif($network=='1'){
			$net='airtel';
			$gdisc=$avtu/100;
			$netw=strtoupper($net);
		}		
		elseif($network=='2'){
			$net='etisalat';
			$gdisc=$evtu/100;
			$netw=strtoupper('9MOBILE');
		}	
	
		$tvinfo=gettvdetail($airuser,$airpass,$net);
	}
	elseif($_POST['what']=='load-data' ){
		if(isset($_POST['mobile'])){
			extract ($_POST);
			
			$postdata = http_build_query( 
					array(
						'gtype' => 'databundles',
						'uname' => $uname, 
						'pass' => $pass, 
						'phone' => $mobile, 
						'network'=> $network,
						'amt'=> $bundles,
						'netw'=> $netw
					));
		
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
				$gob="<font color=blue>$g2</font>";
			}
			else{
				$gob="<font color=red>$g2</font>";
			}
			
			$_SESSION[$sessname2]=$gob;		
			header("location:$weburl/databundles");
			
		}
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
  <title>DLCIpay | Data Bundles</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | Data Bundles</a>
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
        <li class="breadcrumb-item active">Data Bundles</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Data Bundles</h1>
         <?php echo $msg ;?>
		  <p>		  
		<form action="databundles" method="post" name="form1" id="form1">
		The data will come as airtime which would be automatically converted to the expected bundle.<br>
			     A recipient that has already borrowed airtime will not receive the data top up because the airtime would have been deducted before conversion.<br>
		Check balance: <em><strong>Airtel:</strong></em> *123*10# or *140#, 
			     <em><strong>Etisalat:</strong></em> *228#,
				 <em><strong>MTN:</strong></em> *131*4#
				 <em><strong>GLO:</strong></em> *127*0#<br />
		<div class="form-group">
			<div class="form-row">
              	<div class="col-md-3">
				Network<br /><input name="what" id="what" type="hidden"> <input name="netw" id="netw" type="hidden" value="<?php echo $netw ;?>">
				<input name="gtype" id="gtype" type="hidden" value="databundles">
				<select class="form-control" name="network" id="network" onChange="getnetwork(this.value)">
					<option value="0">Select</option>
					<option value="5" <?php if($netw=='MTN') echo 'selected' ;?>>MTN</option>					
					<option value="2" <?php if($netw=='9MOBILE') echo 'selected' ;?>>9MOBILE</option>
					<option value="6" <?php if($netw=='GLO') echo 'selected' ;?>>GLO</option>
					<option value="1" <?php if($netw=='AIRTEL') echo 'selected' ;?>>AIRTEL</option>
				</select>
				</div>
				<div class="col-md-3">
				Data Bundle<br />
				<select class="form-control" name="bundles" id="bundles">
				<?php //echo $tvinfo ;
			  	$tvsplit=explode("|",$tvinfo);
				$count=count($tvsplit)-1;
				if ($count>=1){
					for ($i=0;$i<$count;$i++){
						$cont=$tvsplit[$i];
						$cont1=explode("*",$cont);
						$dvalue=$cont1[2];
						$disprice=$dvalue-($gdisc*$dvalue);
						$dtext=$cont1[1]." N".$disprice;
						echo "<option value=$dvalue>$dtext</option>";
					}
				}			  
			  ?>
									
				</select>
				</div>
				<div class="col-md-3">
				Phone No<br />
	 			<input class="form-control" type="number" name="mobile" id="mobile" value="" placeholder='Phone No'>
				</div>				
				<div class="col-md-3">
					<br>
					<input class="form-control" type="button" value="Send Data" onClick="senddata()" name="sbut" id="sbut" />	
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
			  <form action="databundles" method="post" name="form2" id="form2">          
				<?php
				$msg='';
				$count=0;
				$qs=mysqli_query($wole,"select * from transactions where uname='$uname' and transtype='databundles' order by id desc");
				while($r=mysqli_fetch_assoc($qs)){
					$trans=$r['transtype'];
					$batch=$r['batch'];
					$dt=date('d-m-Y',strtotime($r['dt']));
					$destination=$r['destination'];
					$described=$r['describe'];
					$amt=$r['amt'];
					$amtch=$r['amtch'];
					$status=$r['status'];										
					
					$cmsg="$described $destination";
					
					$count++;
				 	echo "<tr>
					  <td>$count</td>
					  <td>$dt</td>
					  <td>$cmsg</td>
					  <td>$amtch</td>
					  <td>$status</td>
					</tr>";
				}			
				
				?>
				<input name="cancelno" id="cancelno" type="hidden" value="">
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
function senddata(a){
	var phone=document.getElementById('mobile').value;
	var element=document.getElementById('network');
	var element1=document.getElementById('bundles');

	if(element1.value==''){
		alert("No data bundle selected.");
		exit;
	}
		
		
	if(phone=='' || phone.length<11 || phone.length>11 ){
		alert('Phone number should be 11 digits');
		exit;
	}
	
	var dnet = element.options[ element.selectedIndex ].text + " " +element1.options[ element1.selectedIndex ].text ;
	var conf=confirm("Data Top up for "+ phone + " with "+ dnet +"?");
	if(conf==0){
		exit;
	}
	//document.getElementById('what').value="load-data";
	document.getElementById('form1').action='simple.php';
	document.getElementById('sbut').value="Please wait...";
	document.form1.submit();
	
}

function getnetwork(a){
	if(a=='0'){
		exit;
	}	
	
	document.getElementById('what').value='getnetwork';
	document.form1.submit();
}


</script>

</html>