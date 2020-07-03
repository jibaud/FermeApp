<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'A simple page';
include 'header.php';

?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

<?php include 'sidebar.php'; ?>
<?php include 'topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <iframe src="http://infolabo.fr/" width="100%" height="1000px" style="border: none;"></iframe>


            
            
        </div>
        <!-- /.container-fluid -->
<?php include 'footer.php'; ?>