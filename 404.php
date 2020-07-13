<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = '404';
include 'header.php';

?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

<?php include 'sidebar.php'; ?>
<?php include 'topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- 404 Error Text -->
          <div class="text-center">
            <div class="error mx-auto" data-text="404">404</div>
            <p class="lead text-gray-800 mb-5">Page introuvable</p>
            <p class="text-gray-500 mb-0">On dirait bien que vous vous êtes perdu...</p>
            <a href="index.php">&larr; Retourner au tableau de bord</a>
          </div>

        </div>
        <!-- /.container-fluid -->

<?php include 'footer.php'; ?>