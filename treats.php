<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Traitements';
include 'header.php';

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
      <h1 class="h3 mb-1 text-gray-800"><?= $pageTitle ?></h1>

      <h5 class="mb-4"><span class="numberOfTreat">0</span> en cours</h5>

      <div class="row">

        <?php
        $reponseTreats->execute([$owner_id]); // Appel dans topbar

        while ($data = $reponseTreats->fetch()) {
          // Retrouver le nom du bovin
          $key = array_search($data['t_cow_index'], $indexArray);
          $cowName =  $dataCows[$key]['name'];
          $cowId =  $dataCows[$key]['id'];

          $dateOfToday = date('d/m/Y');
          if ($data['t_repeat'] != 0) {
            $dateEnd = futureDateDay($data['t_date'], $data['t_days']);
          }
          if (compareDate($dateEnd, $dateOfToday)) {
            $color = "warning";
          } else if ($dateOfToday == $dateEnd) {
            $color = "danger";
          } else {
            $color = "success";
          }
          if ($data['t_repeat'] == 2 && $color == 'warning') {
            $color = 'danger';
          }

          // On affiche seulement les traitements en cours, donc pas ceux qui ont la couleur success,
          // ce n'est pas la peine puisqu'ils sont terminés.
          if ($color != 'success') {

        ?>
            <div class="col-xl-4 mb-4">
              <a href="cow-single?id=<?= $cowId; ?>" class="text-decoration-none text-reset gest-card treatNotifElement">
                <div class="card border-left-<?= $color ?> shadow h-100 py-2 mb-3">
                  <div class="card-body">
                    <div class="d-flex align-items-center " href="cow-single?id=<?= $cowId; ?>">
                      <div class="mr-3">
                        <div class="icon-circle bg-<?= $color ?>">
                          <i class="fad fa-syringe text-white"></i>
                        </div>
                      </div>
                      <div class="w-100">
                        <div class="text-gray-900"><span class="font-weight-bold"><?= $data['t_name'] ?></span> <span class="font-weight-light">pour</span> <?= $cowName ?></div>
                        <div>
                          Effectué le <?= $data['t_date']; ?>
                        </div>
                        <div>
                          <?php
                          switch ($data['t_repeat']) {
                            case 0:
                              echo 'Une seule fois';
                              break;
                            case 1:
                              if ($dateEnd == $dateOfToday) {
                                echo 'Répéter '.$data['t_days'].' jours plus tard <br>(Aujourd\'hui)';
                              } else {
                                echo 'Répéter '.$data['t_days'].' jours plus tard <br>(Le ' . $dateEnd . ')';
                              }
                              break;
                            case 2:
                              if ($dateEnd == $dateOfToday) {
                                echo 'Dernier jour aujourd\'hui';
                              } else {
                                echo 'Jusqu\'au ' . $dateEnd;
                              }
                              break;
                          }
                          ?>
                        </div>
                        <div>
                          Dose :
                          <?php if($data['t_dose']) { echo $data['t_dose'];} else {echo 'Non précisée';} ?>
                        </div>
                        <div><?= $data['t_note'] ?></div>
                      </div>
                        </div>
                  </div>
                </div>
              </a>
            </div>
        <?php
          }
        }
        $reponseTreats->closeCursor();
        ?>

      </div>

    </div>
    <!-- /.container-fluid -->
    <?php include 'footer.php'; ?>