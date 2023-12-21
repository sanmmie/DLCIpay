<?php
session_start();
include_once 'isopoa.php';
include_once 'config.php';

if(!isset($_SESSION[$sessname])){
	header("location: $weburl/login");
	exit;	
}

@$uname=$_SESSION[$sessname];

$q=mysqli_query($wole,"select * from users where uname='$uname'");
if(mysqli_num_rows($q)!=1){
	header("location: $weburl/login");
	exit;
}
else{
	$rs=mysqli_fetch_array($q);
	$bal=$rs['bal'];
	$fname=$rs['fname'];
	
	
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
  <title>DLCIpay | V2B</title>
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
    <a class="navbar-brand" href="dashboard">DLCIpay | V2B</a>
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
        <li class="breadcrumb-item active">Blank Page</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Blank</h1>
PHP Form BuilderPHP Form Builder
Toggle navigation
Drag & drop Form Builder
The easiest way to create any web form
 
COMPONENTS
DROP COMPONENTS HERE TO BUILD YOUR FORM

COMPONENT SETTINGS
Instructions for use
Overview
This online Drag & drop Form Builder is suitable for any type of use.

You can create your forms with the online tool as well as from your PHP Form Builder local copy.

Create and configure your forms by simple drag and drop.
The generated forms can be easily integrated into any HTML or PHP page.

Any type of integration is possible whatever the CMS or Framework used: Wordpress, Joomla, Drupal, Prestashop, Bootstrap, Material Design, Foundation, ...

Creating your forms with this online drag-and-drop form builder does not require any knowledge. Its use is simple and intuitive, this is the easiest way to generate any Web form.

Nevertheless here are some explanations:

Adding and configuring fields and HTML content
Drag the components from the left panel to the center panel.

Click a component in the center panel to display its properties in the right panel.

The right panel allows you to choose the properties of the selected item: field name, default value, placeholder, is it a required field or not, etc...

The available options are different depending on the selected component. For example, you can add and configure the options of a select dropdown, add and configure radio buttons or checkboxes.

The right panel is generally divided into two or three tabs - one of which is used to add jQuery plugins.

Different jQuery plugins are available, again depending on the component chosen. Each plugin provides its own configuration options.

Main Settings
Here you have access to the form parameters: name, framework used (Bootstrap 3/4, Material Design, ...), layout, ... etc.

The "Form Action" tab lets you define the parameters for sending an email, saving the values in your database, and possibly a redirection.

The "Form Plugins" tab is where you can configure jQuery plugins enabled at the form level. Mainly Checkbox and Radio Buttons plugins.

The "Ajax loading" tab allows you to enable the loading of the form in Ajax. This is particularly useful if you want to insert your form in an HTML page (which does not accept PHP).
In this case, the HTML page will call the form in Ajax. The PHP form is saved in a separate file.

Preview
The preview button enables you to open a modal at any time and see what your form looks like.

The plugins are activated in the preview window.

Get Code
Click to build the form and retrieve the code. It's easy: just copy and paste following the instructions and your form is ready.

Advanced features
The online drag-and-drop form builder is designed to easily create your forms.

PHP Form Builder offers many advanced features, some of which are not available in drag-and-drop.

Once your form is generated, you can easily use PHP Form Builder functions to customize any field and add more advanced features.

Tutorial in images
Drag & drop the form elements from the left panel to create the fieldsDrag & drop the form elements from the left panel to create the fields
Click any component in the main panel to view & live-edit its settings in the Component Settings panelClick any component in the main panel to view & live-edit its settings in the Component Settings panel
Add / Edit / Remove Radio buttons or Checkboxes from the Component Settings panelAdd / Edit / Remove Radio buttons or Checkboxes from the Component Settings panel
Drag & drop the 'Start/End Condition' from the left panel to add conditional logicDrag & drop the 'Start/End Condition' from the left panel to add conditional logic
Choose conditions under which the fields will be displayed or hiddenChoose conditions under which the fields will be displayed or hidden
Enable and configure each plugin from the Component Settings panelEnable and configure each plugin from the Component Settings panel
Drag & drop to add & customize Title (h1-h6), Paragraph or custom HTMLDrag & drop to add & customize Title (h1-h6), Paragraph or custom HTML
Different plugins are available depending on the selected field typeDifferent plugins are available depending on the selected field type
Different options are available depending on the selected pluginDifferent options are available depending on the selected plugin
Drag and drop the elements to reorder them at any timeDrag and drop the elements to reorder them at any time
Click the 'preview' button for a live preview at any timeClick the 'preview' button for a live preview at any time
Plugins and all features are available during previewingPlugins and all features are available during previewing
Click the 'Main Settings' button to choose your preferred framework & others various settingsClick the 'Main Settings' button to choose your preferred framework & others various settings
Enable loading with Ajax if you want to - it allows to load your form in any HTML page without PHPEnable loading with Ajax if you want to - it allows to load your form in any HTML page without PHP
Configure the sending of posted data by email or saving in your databaseConfigure the sending of posted data by email or saving in your database
Setting up email sendingSetting up email sending
Some plugins are available in the global configuration of the formSome plugins are available in the global configuration of the form
Click the 'Get Code' button, copy/paste in your pageClick the 'Get Code' button, copy/paste in your page
The complete page code is also availableThe complete page code is also available
Load / Save your forms in JSON format at any timeLoad / Save your forms in JSON format at any time
Form Code
×
Form code
Full page
<?php
use phpformbuilder\Form;
use phpformbuilder\Validator\Validator;
use phpformbuilder\database\Mysql;

/* =============================================
    start session and include form class
============================================= */

session_start();
include_once rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/phpformbuilder/Form.php';

/* =============================================
    validation if posted
============================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && Form::testToken('fg-form') === true) {
    // create validator & auto-validate required fields
    $validator = Form::validate('fg-form');

    // recaptcha validation
    $validator->recaptcha('RECAPTCHA_PRIVATE_KEY_HERE', 'Recaptcha Error')->validate('g-recaptcha-response');

    // check for errors
    if ($validator->hasErrors()) {
        $_SESSION['errors']['fg-form'] = $validator->getAllErrors();
    } else {
        include_once rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/phpformbuilder/database/db-connect.php';
        include_once rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/phpformbuilder/database/Mysql.php';

        $db = new Mysql();
        $insert['Network'] = Mysql::SQLValue($_POST['Network']);
        $insert['Telephone'] = Mysql::SQLValue($_POST['Telephone']);
        $insert['select-2'] = Mysql::SQLValue($_POST['select-2']);
        $insert['Account Number'] = Mysql::SQLValue($_POST['Account Number']);
        $insert['Account Name'] = Mysql::SQLValue($_POST['Account Name']);
        if (!$db->insertRow('Tranx ID', $insert)) {
            $msg = '' . $db->error() . '' . $db->getLastSql() . '' . " \n";
        } else {
            $msg = '1 row inserted !' . " \n";
        }
        // clear the form
        Form::clear('fg-form');
        // redirect after success
        header('Location:https://dlcipay.com/client/v2b-status');
        exit;
    }
}

/* ==================================================
    The Form
 ================================================== */

$form = new Form('fg-form', 'horizontal', 'novalidate, data-fv-no-icon=true', 'bs4');
// $form->setMode('development');
$form->addOption('Network', 'airtel', 'Airtel', '', '');
$form->addOption('Network', 'glo', 'Glo', '', '');
$form->addOption('Network', 'mtn', 'MTN', '', '');
$form->addOption('Network', '9mobile', '9mobile', '', '');
$form->addSelect('Network', 'Network', 'required=required,class=select2,data-close-on-select=true,data-dropdown-auto-width=false,data-minimum-results-for-search,data-tags=false,data-theme=default');
$form->addIcon( 'Telephone', '<i class="fas fa-phone" aria-hidden="true"></i>', 'before');
$form->addInput('tel', 'Telephone', '', 'Telephone', 'required=required,data-intphone=true,data-allow-dropdown=true,data-initial-country=ng');
$form->addPlugin('intl-tel-input', '#Telephone', 'default');
$form->addOption('select-2', 'First Bank', 'First Bank', '', '');
$form->addOption('select-2', 'Access Bànk', 'Access Bank', '', '');
$form->addOption('select-2', 'Providus Bank', 'Providus Bank', '', '');
$form->addSelect('select-2', 'Bank Name', 'required=required');
$form->addIcon( 'Account Number', '<i class="fas fa-archway" aria-hidden="true"></i>', 'before');
$form->addInput('number', 'Account Number', '', 'Account Number', 'required=required');
$form->addIcon( 'Account Name', '<i class="fas fa-address-book" aria-hidden="true"></i>', 'before');
$form->addInput('text', 'Account Name', '', 'Account Name', 'required=required');
$form->addRecaptchaV3('RECAPTCHA_PUBLIC_KEY_HERE', 'fg-form');
$form->setCols(0, 12);
$form->centerButtons(true);
$form->addBtn('submit', 'button-1', '', 'Button', 'class=btn btn-success,class=ladda-button,data-style=zoom-in');
$form->addPlugin('formvalidation', '#fg-form');
$form->addPlugin('nice-check', 'form', 'default', array('%skin%' => 'blue'));
$form->addPlugin('icheck', 'input', 'default', array('%theme%' => 'square', '%color%' => 'blue'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Php Form Builder - Bootstrap 4 form</title>
    <meta name="description" content="">

    <!-- Bootstrap 4 CSS -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- Font awesome icons -->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <!-- fontawesome5 -->
    
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.3.1/css/all.min.css">
    
    <?php $form->printIncludes('css'); ?>
</head>

<body>

    <h1 class="text-center">Php Form Builder - Bootstrap 4 form</h1>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-md-11 col-lg-10">
                <?php
                if (isset($msg)) {
                    echo $msg;
                }
                $form->render();
                ?>

            </div>
        </div>
    </div>

    <!-- jQuery -->

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

    <!-- Bootstrap 4 JavaScript -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <?php
    $form->printIncludes('js');
    $form->printJsCode();
    ?>

</body>

</html>

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

</html>