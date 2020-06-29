<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Settings';
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
          <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>

        </div>
        <!-- /.container-fluid -->

<?php include 'footer.php'; ?>