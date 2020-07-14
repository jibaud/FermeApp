<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Recherche';
include 'header.php';

// Appel de la recherche
if (isset($_GET['q']) && !empty($_GET['q'])) {
  $search = $_GET['q'];

  try {
    $searchQuery = $database->prepare(
      "SELECT * FROM cows WHERE owner_id = '$currentUserId' AND id LIKE '%$search%' OR name LIKE '%$search%' OR race LIKE '%$search%' OR note LIKE '%$search%'"
    );
    $searchQuery->execute();
  } catch (Exception $e) {
    echo " Error ! " . $e->getMessage();
  }
  
} else {
  header('Location: index');
}

?>

<body id="page-top">
  <?php include 'includes/loader.php'; ?>

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php include 'sidebar.php'; ?>
    <?php include 'topbar.php'; ?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

      <!-- Page Heading -->
      <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>

      <div class="list-group mb-4">
        <?php
        $resultNumber = $searchQuery->rowCount();
        if ($resultNumber > 0) {
        ?>
          <div class="mb-2"><?= $resultNumber; ?> résultat(s) pour cette recherche.</div>
          <?php
          while ($searchResult = $searchQuery->fetch()) { ?>
            <a href="cow-single?id=<?= $searchResult['id'] ?>" class="list-group-item list-group-item-action"><?= $searchResult['name'] ?> - <?= $searchResult['id'] ?></a>
          <?php
          }
        } else { ?>
          Aucun résultat pour cette recherche.
        <?php } ?>
      </div>

    </div>
    <!-- /.container-fluid -->
    <?php include 'footer.php'; ?>