<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Ajouter un nouvel élément';
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
          <hr>

<?php include 'add-cow-form.php'; ?>

        </div>
        <!-- /.container-fluid -->

<?php include 'footer.php'; ?>