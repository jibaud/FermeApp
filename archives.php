<?php


session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Corbeille';
include 'header.php';


// Réstaurer un bovin archivée
if (isset($_POST['restaure'])) {
  $restaureIndexNumber = htmlspecialchars($_POST['selectedIdToRestaure']);
  $owner_id = $_SESSION['userID'];
  $database = getPDO();
  $restaureCow = $database->prepare("UPDATE cows SET isarchived = 0 WHERE cow_index = $restaureIndexNumber AND owner_id = $owner_id");
  $restaureCow->execute();

  // Réstaurer les treats associés
	$archiveCowTreats = $database->prepare("UPDATE treats SET t_isarchived = 0 WHERE t_cow_index = $restaureIndexNumber AND t_owner_id = $owner_id");
	$archiveCowTreats->execute();


  header('Location:archives');
}

// Supprimer un bovin définitivement
if (isset($_POST['delete'])) {
  $deleteindexnumber = htmlspecialchars($_POST['selectedIndexToDelete']);
  $owner_id = $_SESSION['userID'];
  $database = getPDO();
  $deleteCow = $database->prepare("DELETE FROM cows WHERE cow_index = $deleteindexnumber AND owner_id = $owner_id");
  $deleteCow->execute();

  // Supprime aussi les gestations associées à ce bovin
  $deleteCowGest = $database->prepare("DELETE FROM gestations WHERE g_cow_index = $deleteindexnumber AND g_owner_id = $owner_id");
  $deleteCowGest->execute();

  // Supprime aussi les traitements associés à ce bovin
  $deleteCowTreat = $database->prepare("DELETE FROM treats WHERE t_cow_index = $deleteindexnumber AND t_owner_id = $owner_id");
  $deleteCowTreat->execute();

  header('Location:archives');
}

// Message d'erreur redirection from cow-single if archived
if (isset($_GET['e'])) {
  $warningMessage = "Le bovin portant le numéro d'identification " . $_GET['e'] . " se trouve dans la corbeille. Vous pouvez le restaurer si vous le souhaitez.";
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
      <?php if (isset($warningMessage)) { ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <?= $warningMessage ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php } ?>
      <!-- Page Heading -->
      <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
      </div>


      <!-- DataTales Example -->
      <div class="card shadow mb-4">

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="archiveListTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>Numéro</th>
                  <th>Nom</th>
                  <th>Genre</th>
                  <th>Naissance</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Numéro</th>
                  <th>Nom</th>
                  <th>Genre</th>
                  <th>Naissance</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </tfoot>
              <tbody>

                <?php

                $owner_id = $_SESSION['userID'];
                $database = getPDO();
                $reponseCowList = $database->prepare("SELECT * FROM cows WHERE owner_id = $owner_id AND isarchived = 1");
                $reponseCowList->execute();

                // On affiche chaque entrée une à une
                while ($donnees = $reponseCowList->fetch()) {
                  $type = calculeType($donnees['birth_date']);
                ?>
                  <tr>
                    <td><?= $donnees['id']; ?></td>
                    <td style="text-transform:capitalize;" id="namefor<?= $donnees['id']; ?>"><?= $donnees['name']; ?></td>
                    <td style="text-transform:capitalize;"><?= $donnees['gender']; ?></td>
                    <td><?= $donnees['birth_date']; ?></td>

                    
                    <?php if ($donnees['death_date'] != '') { ?>
                      <td class="text-warning">Mort(e)</td>
                    <?php } else if ($donnees['sale_date']) { ?>
                      <td class="text-success">Vendu(e)</td>
                    <?php } else if ($donnees['isarchived']) { ?>
                      <td class="text-danger">Supprimé</td>
                    <?php } else { ?>
                      <td></td>
                    <?php } ?>


                    <td>
                      <span data-toggle="tooltip" data-placement="top" id="viewButton<?= $donnees['id']; ?>" title="Coup d'oeil">
                        <button type="button" class="btn btn-primary btn-sm" id="<?= $donnees['id']; ?>" data-toggle="modal" data-target="#viewCowModal<?= $donnees['id']; ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                      </span>

                      <span data-toggle="tooltip" data-placement="top" title="Restaurer">
                        <a class="btn btn-success btn-sm selectIdButtonRestaure" id="<?= $donnees['cow_index']; ?>" href="" data-toggle="modal" data-target="#restaureCowModal">
                          <i class="fas fa-inbox-out"></i>
                        </a>
                      </span>

                      <span data-toggle="tooltip" data-placement="top" title="Supprimer">
                        <button type="button" class="btn btn-danger btn-sm deleteButton selectIndexButtonDelete" id="<?= $donnees['cow_index']; ?>" data-toggle="modal" data-target="#deleteCowModal">
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


    <!-- Restaure Modal-->
    <div class="modal fade" id="restaureCowModal" tabindex="-1" role="dialog" aria-labelledby="RestaureCow" aria-hidden="true" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-gray-800" id="">Restaurer</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Vous êtes sur le point de sortir ce bovin des archives pour le replacer dans la liste principale.</p>
          </div>
          <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
              <form action="" method="post">
                <input type="text" id="selectedIdToRestaure" name="selectedIdToRestaure" value="" class="d-none">
                <input type="submit" name="restaure" id="restaure" value="Restaurer" class="btn btn-success">
              </form>
            </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal-->
    <div class="modal fade" id="deleteCowModal" tabindex="-1" role="dialog" aria-labelledby="DeleteCow" aria-hidden="true" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-gray-800" id="">ATTENTION</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Si vous supprimer ce bovin vous n'aurez aucun moyen de le retrouver. Etes vous certain de vouloir supprimer ce bovin définitivement ?</p>
          </div>
          <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
              <form action="" method="post">
                <input type="text" id="selectedIndexToDelete" name="selectedIndexToDelete" value="" style="display:none;">
                <input type="submit" name="delete" id="delete" value="Confirmer la suppression définitive" class="btn btn-outline-danger">
              </form>
            </div>
        </div>
      </div>
    </div>



    <?php include 'footer.php'; ?>

    <?php



    // Modals
    $reponseCowList->execute();
    while ($donnees = $reponseCowList->fetch()) {

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
            }, '', 'archives');
          })

        });
      </script>


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
                    <td><?= $donnees['name']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Numéro d'identification</th>
                    <td><?= $donnees['id']; ?></td>
                  </tr>
                  <tr>
                    <th scope="row">Genre</th>
                    <td><?= $donnees['gender']; ?></td>
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
                    <th scope="row">Numéro de la mère</th>
                    <?php if ($donnees['mother_id'] == '') { ?>
                      <td>Inconnu</td>
                    <?php } else { ?>
                      <td><?= $donnees['mother_id']; ?></td>
                    <?php } ?>
                  </tr>
                  <tr>
                    <th scope="row">Gestation</th>
                    <?php if ($donnees['ispregnant'] == 0 && $donnees['pregnant_number'] > 0) { ?>
                      <td><?= $donnees['pregnant_number']; ?> gestation(s) passée(s)</td>
                    <?php } else if ($donnees['ispregnant'] == 1) { ?>
                      <td>Depuis le <?= $donnees['pregnant_since']; ?></td>
                    <?php } else { ?>
                      <td>Aucune gestation</td>
                    <?php } ?>
                  </tr>

                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>

    <?php } ?>