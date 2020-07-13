<?php


session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Gestion des vaches';
include 'header.php';


// Supprimer un bovin
if (isset($_POST['archive'])) {
  $archiveidnumber = htmlspecialchars($_POST['selectedIdToArchive']);
  $owner_id = $_SESSION['userID'];
  $database = getPDO();
  $archiveCow = $database->prepare("UPDATE cows SET isarchived = 1 WHERE id = $archiveidnumber AND owner_id = $owner_id");
  $archiveCow->execute();

  header('');
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


      <?php if (isset($successMessage)) { ?>
        <div class="alert alert-success" role="alert">
          <?= $successMessage ?>
        </div>
      <?php } ?>
      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
        <a href="#" id="addNewButton" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#addCowModal"><i class="fas fa-plus-circle fa-sm mr-1 text-white-50"></i> Nouveau</a>
      </div>



      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="cowListTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Numéro</th>
                  <th>Nom</th>
                  <th>Genre</th>
                  <th>Type</th>
                  <th>Naissance</th>
                  <th>Age</th>
                  <th>Gestante</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Numéro</th>
                  <th>Nom</th>
                  <th>Genre</th>
                  <th>Type</th>
                  <th>Naissance</th>
                  <th>Age</th>
                  <th>Gestante</th>
                  <th>Actions</th>
                </tr>
              </tfoot>
              <tbody>

                <?php

                $owner_id = $_SESSION['userID'];
                $database = getPDO();
                $reponseCowList = $database->prepare("SELECT * FROM cows WHERE owner_id = $owner_id AND isarchived = 0 AND death_date = '' AND sale_date = ''");
                $reponseCowList->execute();

                // On affiche chaque entrée une à une
                while ($donnees = $reponseCowList->fetch()) {
                  if ($donnees['pregnant_number'] > 0) {
                    $type = 'vache';
                  } else {
                    $type = calculeType($donnees['birth_date']);
                  }
                ?>
                  <tr>
                    <td class="user-select-all"><?= $donnees['id']; ?></td>
                    <td class="text-capitalize user-select-all" id="namefor<?= $donnees['id']; ?>"><?= $donnees['name']; ?></td>
                    <td class="text-capitalize"><?= $donnees['gender']; ?></td>
                    <td class="text-capitalize"><?= $type ?></td>
                    <td><?= $donnees['birth_date']; ?></td>
                    <td><?= calculeAge($donnees['birth_date'], 'short') ?></td>

                    <?php
                    if ($donnees['ispregnant']) {
                      $pregnantdays = daysSince($donnees['pregnant_since']);
                      if ($pregnantdays >= 280 ) {
                        echo '<td class="text-danger">Oui (' . $pregnantdays . '/280j)</td>';
                      } else if ($pregnantdays >= 240) {
                        echo '<td class="text-warning">Oui (' . $pregnantdays . '/280j)</td>';
                      } else {
                        echo '<td class="text-success">Oui (' . $pregnantdays . '/280j)</td>';
                      }
                    } else {
                      if ($type != 'vache' & $donnees['genre'] != 'femelle') {
                        echo '<td></td>';
                      } else {
                        echo '<td>Non</td>';
                      }
                    }
                    ?>

                    <td>
                      <span data-toggle="tooltip" data-placement="top" id="viewButton<?= $donnees['id']; ?>" title="Coup d'oeil">
                        <button type="button" class="btn btn-primary btn-sm" id="<?= $donnees['id']; ?>" data-toggle="modal" data-target="#viewCowModal<?= $donnees['id']; ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                      </span>

                      <span data-toggle="tooltip" data-placement="top" title="Consulter">
                        <a class="btn btn-success btn-sm" href="cow-single?id=<?= $donnees['id']; ?>">
                          <i class="fas fa-file-search"></i>
                        </a>
                      </span>

                      <span data-toggle="tooltip" data-placement="top" title="Supprimer">
                        <button type="button" class="btn btn-danger btn-sm archiveButton selectIdButtonArchive" id="<?= $donnees['id']; ?>" data-toggle="modal" data-target="#archiveCowModal">
                          <i class="fas fa-trash"></i>
                        </button>
                      </span>
                    </td>
                  </tr>
                <?php
                }

                $reponseCowList->closeCursor(); // Termine le traitement de la requête

                ?>


              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid -->

    <!-- Add Cow Modal-->
    <div class="modal fade " id="addCowModal" tabindex="-1" role="dialog" aria-labelledby="AddCow" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-gray-800">Ajouter un nouvel élément</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">

            <?php include 'add-cow-form.php'; ?>

          </div>

        </div>
      </div>
    </div>




    <!-- Archive Modal-->
    <div class="modal fade" id="archiveCowModal" tabindex="-1" role="dialog" aria-labelledby="ArchiveCow" aria-hidden="true" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-gray-800" id="">Suppression</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Voulez-vous vraiment supprimer cette vache ?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
            <form action="" method="post">
              <input type="text" id="selectedIdToArchive" name="selectedIdToArchive" value="" style="display:none;">
              <input type="submit" name="archive" id="archive" value="Supprimer" class="btn btn-danger">
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>

    <?php

    // Children
    $childrenCowList = $database->prepare("SELECT id, name, mother_id FROM cows WHERE owner_id = $owner_id AND isarchived = 0");
    $childrenCowList->execute();
    $resultChildren = $childrenCowList->fetchAll();



    // Modals
    $reponseCowList->execute();
    while ($donnees = $reponseCowList->fetch()) {

      if ($donnees['pregnant_number'] > 0) {
        $type = 'vache';
      } else {
        $type = calculeType($donnees['birth_date']);
      }

    ?>
      <script language="javascript">
        $(document).ready(function() {
          if (window.location.href.indexOf('#viewCowModal<?= $donnees['id'] ?>') != -1) {
            $('#viewCowModal<?= $donnees['id'] ?>').modal('show');
          }

          // Change URL quand on ouvre la modal addCowModal
          $('#viewCowModal<?= $donnees['id'] ?>').on('show.bs.modal', function(e) {
            history.pushState({
              key: '<?= $donnees['id'] ?>'
            }, 'Numéro <?= $donnees['id'] ?>', '#viewCowModal<?= $donnees['id'] ?>');
          })

          $('#viewCowModal<?= $donnees['id'] ?>').on('hide.bs.modal', function(e) {
            history.pushState({
              key: 'milkow'
            }, '', 'cows-manager');
          })

        });
      </script>

      <?php

      if ($donnees["mother_id"]) {

      ?>

        <script language="javascript">
          $(document).ready(function() {
            var motherName = document.getElementById("namefor<?= $donnees["mother_id"] ?>");
            $('#tooltipFor<?= $donnees["id"] ?>').tooltip({
              title: motherName
            })

          });
        </script>

      <?php } ?>

      <!-- View Cow Modal -->
      <div class="modal fade" id="viewCowModal<?= $donnees['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="ViewCow" aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-primary" id=""><?= $donnees['name']; ?> - <?= $donnees['id']; ?></h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">


              <table class="table table-sm">
                <tbody>
                  <tr>
                    <th scope="row">Nom</th>
                    <td class="user-select-all"><?= $donnees['name']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Numéro d'identification</th>
                    <td class="user-select-all"><?= $donnees['id']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Genre</th>
                    <td class="text-capitalize"><?= $donnees['gender']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Type</th>
                    <td class="text-capitalize"><?= $type; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Race</th>
                    <td><?= $donnees['race']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Date de naissance</th>
                    <td><?= $donnees['birth_date']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Age</th>
                    <td><?= calculeAge($donnees['birth_date'], 'full') ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Numéro de la mère</th>
                    <?php if ($donnees['mother_id'] == '') { ?>
                      <td>Inconnu</td>
                    <?php } else { ?>
                      <td><a href="#" id="tooltipFor<?= $donnees['id']; ?>" data-toggle="modal" data-target="#viewCowModal<?= $donnees['mother_id']; ?>" data-dismiss="modal"><?= $donnees['mother_id']; ?></a></td>
                    <?php } ?>
                  </tr>
                  <tr>
                    <th scope="row">Gestation</th>
                    <?php if ($donnees['ispregnant'] == 0 && $donnees['pregnant_number'] > 0) { ?>
                      <td><?= $donnees['pregnant_number']; ?> géstation(s) passée(s).</td>
                    <?php } else if ($donnees['ispregnant'] == 1) { ?>
                      <td>Depuis le <?= $donnees['pregnant_since']; ?></td>
                    <?php } else { ?>
                      <td>Aucune géstation</td>
                    <?php } ?>
                  </tr>
                  <tr>
                    <th scope="row">Enfant(s)</th>
                    <td>
                      <?php
                      $currentCowId = $donnees['id'];
                      $usersWithMother = array_filter(
                        $resultChildren,
                        function (array $user) use ($currentCowId) {
                          return $user['mother_id'] == $currentCowId;
                        }
                      );
                      if (count($usersWithMother) > 0) {
                        foreach ($usersWithMother as $element) {
                      ?>

                          <a class="childrenList" href="#" data-toggle="modal" data-target="#viewCowModal<?= $element['id'] ?>" data-dismiss="modal"><?= $element['name'] ?></a>
                        <?php
                        }
                      } else {
                        ?>
                        <p>Aucun</p>
                      <?php
                      }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">Note</th>
                    <td><?= $donnees['note']; ?></td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>

    <?php } ?>


    <script language="javascript">
      // Scripts only for this page
      $(document).ready(function() {

        $("#birthdate").focusout(function() { // Avoid the datepicker to change the modal URL
          history.pushState({
            key: 'milkow'
          }, 'Nouvel élément', '#addCowModal');
        });

      });
    </script>

<script>$(document).ready(function () {$('#cowListTable_filter').child('label').child('input').val("lala");});</script>
