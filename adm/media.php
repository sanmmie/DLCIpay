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
if(isset($_SESSION[$sessname7])){
	$msg=$_SESSION[$sessname7];
	unset($_SESSION[$sessname7]);
}


if(isset($_POST['logobt'])){
	$target_file = basename($_FILES["filelogo"]["name"]);
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	if($imageFileType!="png" && $imageFileType!="jpg" && $imageFileType!="jpeg" ){			
		$_SESSION[$sessname7]="<p><font color=red>Invalid logo format $imageFileType. Picture should be png or jpg</font></p>";
		header("location: media");
		exit;	
	}
	
	$source_url = $_FILES["filelogo"]["tmp_name"];
	
	// Set the path to the image to resize
	$input_image = $source_url;
	// Get the size of the original image into an array
	$size = getimagesize( $input_image );
	// Set the new width of the image
	$thumb_width = 250;
	// Calculate the height of the new image to keep the aspect ratio
	$thumb_height = 123;
	// Create a new true color image in the memory
	$thumbnail = imagecreatetruecolor( $thumb_width, $thumb_height );
	// Create a new image from file 
	if($imageFileType=="png"){
		$src_img = imagecreatefrompng( $input_image );
		imagealphablending($thumbnail, false);
		$colorTransparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 0x7fff0000);
		imagefill($thumbnail, 0, 0, $colorTransparent);
		imagesavealpha($thumbnail, true);
	}
	elseif($imageFileType=="jpg" || $imageFileType=="jpeg"){
		$src_img = imagecreatefromjpeg( $input_image );
	}
	// Create the resized image
	imagecopyresampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
	// Save the image as resized.jpg
	imagepng( $thumbnail, "../../assets/img/header.png" );
	// Clear the memory of the tempory image 
	imagedestroy( $thumbnail );

	$dest_photo = "../../assets/img/header.png";
	//move_uploaded_file($source_url,$dest_photo);
	
	$_SESSION[$sessname7]="<p><font color=blue>Logo uploaded!</font></p>";
	header("location: media");
}

if(isset($_POST['backimgbt'])){
	$target_file = basename($_FILES["filelogo1"]["name"]);
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	if($imageFileType!="jpg" && $imageFileType!="jpeg"){			
		$_SESSION[$sessname7]="<p><font color=red>Invalid background format $imageFileType. Picture should be jpg</font></p>";
		header("location: media");
		exit;	
	}
	
	$source_url = $_FILES["filelogo1"]["tmp_name"];

	$dest_photo = "../../assets/img/wallpaper.jpg";
	move_uploaded_file($source_url,$dest_photo);
	
	$_SESSION[$sessname7]="<p><font color=blue>Background image uploaded!</font></p>";
	header("location: media");
}


if(isset($_POST['sliderimgbt1'])){
	$target_file2 = basename($_FILES["filelogo2"]["name"]);
	$imageFileType2 = pathinfo($target_file2,PATHINFO_EXTENSION);
	if($imageFileType2!="jpg" && $imageFileType2!="jpeg"){			
		$_SESSION[$sessname7]="<p><font color=red>Invalid slider image 1. Picture should be jpg</font></p>";
		header("location: media");
		exit;	
	}
	
	$source_url = $_FILES["filelogo2"]["tmp_name"];

	// Set the path to the image to resize
	$input_image = $source_url;
	// Get the size of the original image into an array
	$size = getimagesize( $input_image );
	// Set the new width of the image
	$thumb_width = 1060;
	// Calculate the height of the new image to keep the aspect ratio
	$thumb_height = 510;
	// Create a new true color image in the memory
	$thumbnail = imagecreatetruecolor( $thumb_width, $thumb_height );
	// Create a new image from file 
	$src_img = imagecreatefromjpeg( $input_image );
	// Create the resized image
	imagecopyresampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
	// Save the image as resized.jpg
	imagejpeg( $thumbnail, "../../assets/img/slideshow/slide1.jpg" );
	// Clear the memory of the tempory image 
	imagedestroy( $thumbnail );

	$dest_photo = "../../assets/img/slideshow/slide1.jpg";
	//move_uploaded_file($source_url,$dest_photo);
	$_SESSION[$sessname7]="<p><font color=blue>Slider image 1 uploaded!</font></p>";
	header("location: media");
	
}

if(isset($_POST['sliderimgbt2'])){	
	$target_file3 = basename($_FILES["filelogo3"]["name"]);
	$imageFileType3 = pathinfo($target_file3,PATHINFO_EXTENSION);
	if($imageFileType3!="jpg" && $imageFileType3!="jpeg"){			
		$_SESSION[$sessname7]="<p><font color=red>Invalid slider image 2. Picture should be jpg</font></p>";
		header("location: media");
		exit;	
	}
	
	$source_url = $_FILES["filelogo3"]["tmp_name"];
	
	// Set the path to the image to resize
	$input_image = $source_url;
	// Get the size of the original image into an array
	$size = getimagesize( $input_image );
	// Set the new width of the image
	$thumb_width = 1060;
	// Calculate the height of the new image to keep the aspect ratio
	$thumb_height = 510;
	// Create a new true color image in the memory
	$thumbnail = imagecreatetruecolor( $thumb_width, $thumb_height );
	// Create a new image from file 
	$src_img = imagecreatefromjpeg( $input_image );
	// Create the resized image
	imagecopyresampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );
	// Save the image as resized.jpg
	imagejpeg( $thumbnail, "../../assets/img/slideshow/slide2.jpg" );
	// Clear the memory of the tempory image 
	imagedestroy( $thumbnail );

	$dest_photo = "../../assets/img/slideshow/slide2.jpg";
	//move_uploaded_file($source_url,$dest_photo);
	
	$_SESSION[$sessname7]="<p><font color=blue>Silder 2 image uploaded!</font></p>";
	header("location: media");
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
        <li class="breadcrumb-item active">Media</li>
      </ol>      
     
    
      <!-- Example DataTables Card-->
      <div class="card mb-3">
        <div class="card-body">
         <?php echo $msg ;?>
		 <h2>Logo, Background image, Slider images</h2>
		 You will need to refresh page after successful upload.
		 <form class="form-control" method="post" name='frmimages' enctype="multipart/form-data">
		<h3>Logo</h3>
		Recommended logo size 250x123 and file format PNG.<br>
		  <img id="imagelogo" /><br>
		  <input type="file" id="filelogo" name="filelogo"/><input name="logoavail" type="hidden" id="logoavail">						
		 <input name="avail" type="hidden" id="avail">
		 <input class="btn btn-primary" type="submit" value="Update logo" name='logobt'>
		 </form>
		 
		 <form class="form-control" method="post" name='frmbackimages' enctype="multipart/form-data">
		<h3>Background Picture</h3>
		Recommended size 1280x800 and file format JPG.<br>
		  <img id="imagelogo1" /><br>
		  <input type="file" id="filelogo1" name="filelogo1"/><input name="logoavail1" type="hidden" id="logoavail1">						
		 <input name="avail1" type="hidden" id="avail1">
		 <input class="btn btn-primary" type="submit" value="Update Background Image" name='backimgbt'>
		 </form>	
		 
		  <form class="form-control" method="post" name='frmsliderimages' enctype="multipart/form-data">
		<h3>Slider images</h3>
		Recommended size 1060x510 and file format JPG.<br>
		  <img id="imagelogo2" /><br>
		  <input type="file" id="filelogo2" name="filelogo2"/><input name="logoavail2" type="hidden" id="logoavail2">						
		 <input name="avail2" type="hidden" id="avail2">
		 <input class="btn btn-primary" type="submit" value="Update Slider Image" name='sliderimgbt1'>
		 <br><br>
		   <img id="imagelogo3" /><br>
		  <input type="file" id="filelogo3" name="filelogo3"/><input name="logoavail3" type="hidden" id="logoavail3">						
		 <input name="avail3" type="hidden" id="avail3">
		 <input class="btn btn-primary" type="submit" value="Update Slider Image" name='sliderimgbt2'>
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

document.getElementById("filelogo").onchange = function () {
    var reader = new FileReader();
	var imgPath = document.getElementById("filelogo").value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

	if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		reader.onload = function (e) {
			// get loaded data and render thumbnail.
			document.getElementById("imagelogo").src = e.target.result;
			document.getElementById("imagelogo").style.width="180px";
			document.getElementById("imagelogo").style.height="150px";
			document.getElementById("logoavail").value="yes";
		};
	
		// read the image file as a data URL.
		reader.readAsDataURL(this.files[0]);
	}
	else{
		document.getElementById("logoavail").value="no";
		document.getElementById("imagelogo").src = '';
		document.getElementById("imagelogo").style.width="0px";
		document.getElementById("imagelogo").style.height="0px";
		
		alert("Please select only image!");
	}
};

document.getElementById("imagelogo").src = "../../assets/img/header.png";
document.getElementById("imagelogo").style.width="180px";
document.getElementById("imagelogo").style.height="150px";
document.getElementById("avail").value="yes";	


//background picture
document.getElementById("filelogo1").onchange = function () {
    var reader = new FileReader();
	var imgPath = document.getElementById("filelogo1").value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

	if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		reader.onload = function (e) {
			// get loaded data and render thumbnail.
			document.getElementById("imagelogo1").src = e.target.result;
			document.getElementById("imagelogo1").style.width="180px";
			document.getElementById("imagelogo1").style.height="150px";
			document.getElementById("logoavail1").value="yes";
		};
	
		// read the image file as a data URL.
		reader.readAsDataURL(this.files[0]);
	}
	else{
		document.getElementById("logoavail1").value="no";
		document.getElementById("imagelogo1").src = '';
		document.getElementById("imagelogo1").style.width="0px";
		document.getElementById("imagelogo1").style.height="0px";
		
		alert("Please select only image!");
	}
};

document.getElementById("imagelogo1").src = "../../assets/img/wallpaper.jpg";
document.getElementById("imagelogo1").style.width="180px";
document.getElementById("imagelogo1").style.height="150px";
document.getElementById("avail1").value="yes";	


document.getElementById("filelogo2").onchange = function () {
    var reader = new FileReader();
	var imgPath = document.getElementById("filelogo2").value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

	if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		reader.onload = function (e) {
			// get loaded data and render thumbnail.
			document.getElementById("imagelogo2").src = e.target.result;
			document.getElementById("imagelogo2").style.width="180px";
			document.getElementById("imagelogo2").style.height="150px";
			document.getElementById("logoavail2").value="yes";
		};
	
		// read the image file as a data URL.
		reader.readAsDataURL(this.files[0]);
	}
	else{
		document.getElementById("logoavail2").value="no";
		document.getElementById("imagelogo2").src = '';
		document.getElementById("imagelogo2").style.width="0px";
		document.getElementById("imagelogo2").style.height="0px";
		
		alert("Please select only image!");
	}
};

document.getElementById("imagelogo2").src = "../../assets/img/slideshow/slide1.jpg";
document.getElementById("imagelogo2").style.width="180px";
document.getElementById("imagelogo2").style.height="150px";
document.getElementById("avail2").value="yes";	


document.getElementById("filelogo3").onchange = function () {
    var reader = new FileReader();
	var imgPath = document.getElementById("filelogo3").value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();

	if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		reader.onload = function (e) {
			// get loaded data and render thumbnail.
			document.getElementById("imagelogo3").src = e.target.result;
			document.getElementById("imagelogo3").style.width="180px";
			document.getElementById("imagelogo3").style.height="150px";
			document.getElementById("logoavail3").value="yes";
		};
	
		// read the image file as a data URL.
		reader.readAsDataURL(this.files[0]);
	}
	else{
		document.getElementById("logoavail3").value="no";
		document.getElementById("imagelogo3").src = '';
		document.getElementById("imagelogo3").style.width="0px";
		document.getElementById("imagelogo3").style.height="0px";
		
		alert("Please select only image!");
	}
};

document.getElementById("imagelogo3").src = "../../assets/img/slideshow/slide2.jpg";
document.getElementById("imagelogo3").style.width="180px";
document.getElementById("imagelogo3").style.height="150px";
document.getElementById("avail3").value="yes";

</script>



</html>
