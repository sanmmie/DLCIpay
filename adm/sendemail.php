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
if(isset($_SESSION[$sessname10])){
	$msg=$_SESSION[$sessname10];
	unset($_SESSION[$sessname10]);
}

if (isset($_POST['mess'])){
	//send e-mail
	
	$appx='y';
	$from="noreply@".getDomain($weburl);
	

	//define the subject of the email
	$subject = mysqli_real_escape_string($wole,$_POST['subj']);

	//define the message to be sent. Each line should be separated with \n
	$mess = mysqli_real_escape_string($wole,$_POST['mess']); 
	$mess=str_replace('\r','',$mess);
	$mess=str_replace('\n','',$mess);
	
	if($subject==""){
		$_SESSION[$sessname10]="<font color=red><b>No subject!</b></font>";
		header('location: sendemail');		
		exit;
	}
	
	
	//define the headers we want passed. Note that they are separated with \r\n
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers.= "From: ".$from."\r\nReply-To: ".$from;
	//send the email
	
	if($appx=="y"){
		$query="select * from users";
	
		$result=mysqli_query($wole,$query) or die('Error!');	
		$disp="";	
		$nm=0;
		
		while($row = mysqli_fetch_array($result)){	
			$email=$row['email'];
			$fname=$row['fname'];
			$message="Dear <b>$fname</b>,<p>$mess</p>";
			if(filter_var($email,FILTER_VALIDATE_EMAIL)){	
				   $to=$email;	
				   //$mail_sent = @mail($to, $subject, $message, $headers );	
				   $bsend=sendemailnow($email,$subject,$message,$weburl,$webname);
				   //$jp=$mail_sent ? $nm++ : 0;			   	
			}
		
		}
	}
	
	$_SESSION[$sessname10]="<font color=blue>E-mail sent successfully!</font>";
	header('location:sendemail');

}

$emails="";
$phones="";
$query="select * from users";
$result=mysqli_query($wole,$query) or die('Error!');	
$disp="";	
$nm=0;

while($row = mysqli_fetch_array($result)){	
	$email=$row['email'];
	$phone=$row['phone'];
	if(filter_var($email,FILTER_VALIDATE_EMAIL)){	
		$emails.=",".$email;
		$phones.=",".$phone;
	}
}

$emails=substr($emails,1);
$phones=substr($phones,1);
$phones = trim(preg_replace('/\s+/', '', $phones));
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
  <script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
  <script>
          tinymce.init({
              mode : "exact", elements: "mess",
			  menubar: true,			 
              plugins: [
                  "textcolor","advlist autolink lists link image charmap print preview anchor",
                  "searchreplace visualblocks code fullscreen",
                  "insertdatetime media table contextmenu paste"
              ],
              
			  toolbar: "forecolor backcolor | insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link"
          });
  </script>
  
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
        <li class="breadcrumb-item active">User Emails/Phones</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
         <?php echo $msg ;?>
		  <form id="formsms" name="formsms" method="post" action="sendemail"> 
		  Send email broadcast to your users.
		  <div class="form-group">
			  <div class="form-row">
	              	<div class="col-md-6">
                    Emails<br>
                <textarea class="form-control" name="emails" id="emails" rows='5'><?php echo $emails ;?></textarea>
                <br>
                    Phone Numbers<br>
                <textarea class="form-control" name="phones" id="phones" rows='5'><?php echo $phones ;?></textarea>
					</div>				
			  	</div>
			</div>

			<div class="form-group">
			  <div class="form-row">
	              					
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

function getuserdet(a){
	document.getElementById('userid').value=a;
}

function credituser(a){
	window.location="crediteasy?id="+a;
	
}

</script>



</html>
