<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

include 'includes/add-cow-engine.php';

if (isset($_POST['delete'])){
  $deleteidnumber = htmlspecialchars($_POST['selectedId']);

  $database = getPDO();
  $deleteCow = $database->prepare("UPDATE cows SET isarchived = 1 WHERE id = ?");
  $deleteCow->execute([$deleteidnumber]);
  
  $successMessage = "Elément supprimé.";
  header();
}

$pageTitle = 'Mon Étable';
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
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#addCowModal" onclick="actualise()"><i class="fas fa-plus-square fa-sm"></i> Nouveau</a>
          </div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Mes vaches</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="cowListTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Numéro</th>
                      <th>Nom</th>
                      <th>Genre</th>
                      <th>Type</th>
                      <th>DDN</th>
                      <th>Age</th>
                      <th>Enceinte</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Numéro</th>
                      <th>Nom</th>
                      <th>Genre</th>
                      <th>Type</th>
                      <th>DDN</th>
                      <th>Age</th>
                      <th>Enceinte</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                  <tbody>

<?php

// On récupère tout le contenu de la table jeux_video
$owner_id = $_SESSION['userID'];
$database = getPDO();
$reponseCowList = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND isarchived = 0");
$reponseCowList->execute([$owner_id]);

// On affiche chaque entrée une à une
while ($donnees = $reponseCowList->fetch())
{
?>
                    <tr>
                      <td><?= $donnees['id'];?></td>
                      <td><?= $donnees['name'];?></td>
                      <td><?= $donnees['gender'];?></td>
                      <td><?= $donnees['type'];?></td>
                      <td><?= $donnees['birth_date'];?></td>
                      <td><?= calculeAge($donnees['birth_date'], 'short')?></td>
                      
                      <?php 
                      if($donnees['ispregnant']){
                          $pregnantdays = daysSince($donnees['pregnant_since']);
                          echo '<td class="text-success">Oui ('.$pregnantdays.'/280j)</td>';
                      } else {
                        if($donnees['type'] != 'vache') {
                          echo '<td></td>';
                        } else {
                          echo '<td class="text-warning">Non</td>';
                        }
                      }
                      ?>

                      <td>
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-danger" id="<?= $donnees['id'];?>" data-toggle="modal" data-target="#deleteCowModal">
                            <i class="fas fa-trash"></i>
                        </button>
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
        <div class="modal fade" id="addCowModal" tabindex="-1" role="dialog" aria-labelledby="AddCow" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-gray-800" id="">Ajouter un nouvel élément</h5>
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

        <div class="modal fade" id="deleteCowModal" tabindex="-1" role="dialog" aria-labelledby="DeleteCow" aria-hidden="true" data-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-gray-800" id="">Supprimer un élément</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                  <p>Etes vous certain de vouloir supprimer cet élément ?</p>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <form action="" method="post">
                      <input type="text" id="selectedId" name="selectedId" value="">
                      <input type="submit" name="delete" id="delete" value="Supprimer" class="btn btn-danger">
                    </form>
                  </div>
                </div>
            </div>
            </div>
        </div>

<?php include 'footer.php'; ?>