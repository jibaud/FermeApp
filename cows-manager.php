<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Étable';
include 'header.php';

if (isset($_POST['delete'])){
  $deleteidnumber = htmlspecialchars($_POST['selectedId']);
  $owner_id = $_SESSION['userID'];
  $database = getPDO();
  $deleteCow = $database->prepare("UPDATE cows SET isarchived = 1 WHERE id = $deleteidnumber AND owner_id = $owner_id");
  $deleteCow->execute();
  
  header();
}

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
                      <th>Né(e) le</th>
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
                      <th>Né(e) le</th>
                      <th>Age</th>
                      <th>Enceinte</th>
                      <th>Actions</th>
                    </tr>
                  </tfoot>
                  <tbody>

<?php

$owner_id = $_SESSION['userID'];
$database = getPDO();
$reponseCowList = $database->prepare("SELECT * FROM cows WHERE owner_id = $owner_id AND isarchived = 0");
$reponseCowList->execute();

// On affiche chaque entrée une à une
while ($donnees = $reponseCowList->fetch())
{
?>
                    <tr>
                      <td><?= $donnees['id'];?></td>
                      <td style="text-transform:capitalize;"><?= $donnees['name'];?></td>
                      <td style="text-transform:capitalize;"><?= $donnees['gender'];?></td>
                      <td style="text-transform:capitalize;"><?= $donnees['type'];?></td>
                      <td><?= $donnees['birth_date'];?></td>
                      <td><?= calculeAge($donnees['birth_date'], 'short')?></td>
                      
                      <?php 
                      if($donnees['ispregnant']){
                          $pregnantdays = daysSince($donnees['pregnant_since']);
                          if ($pregnantdays >= 240) {
                            echo '<td class="text-warning">Oui ('.$pregnantdays.'/280j)</td>';
                          } else if ($pregnantdays >= 280) {
                            echo '<td class="text-danger">Oui ('.$pregnantdays.'/280j)</td>';
                          } else {
                            echo '<td class="text-success">Oui ('.$pregnantdays.'/280j)</td>';
                          }
                      } else {
                        if($donnees['type'] != 'vache' & $donnees['genre'] != 'femelle') {
                          echo '<td></td>';
                        } else {
                          echo '<td>Non</td>';
                        }
                      }
                      ?>

                      <td>
                        <button type="button" class="btn btn-primary btn-sm" id="<?= $donnees['id'];?>" data-toggle="modal" data-target="#viewCowModal">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="<?= $donnees['id'];?>" data-toggle="modal" data-target="#deleteCowModal">
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
<?php 
// Stocker résultats dans un array
$reponseCowSingle = $database->prepare("SELECT * FROM cows WHERE id = 1");
$reponseCowSingle->execute();
$result = $reponseCowSingle->fetch();

$_1 = new Cow();
$_1->setId($result['id']);
$_1->setName($result['name']);
$_1->setOwner($result['owner_id']);
$_1->setGender($result['gender']);
$_1->setType($result['type']);
$_1->setRace($result['race']);

?>
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

         <!-- View Cow Modal -->
         <div class="modal fade" id="viewCowModal" tabindex="-1" role="dialog" aria-labelledby="ViewCow" aria-hidden="true" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-gray-800" id="">Harmonie</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">

<?= $_1->getId(); ?>
<?= $_1->getName(); ?>
<?= $_1->getOwner(); ?>
<?= $_1->getGender(); ?>
<?= $_1->getType(); ?>
<?= $_1->getRace(); ?>

                </div>
            </div>
            </div>
        </div>


        <!-- Delete Modal-->
        <div class="modal fade" id="deleteCowModal" tabindex="-1" role="dialog" aria-labelledby="DeleteCow" aria-hidden="true" data-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-gray-800" id="">Supprimer une bête</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                  <p>Etes vous certain de vouloir supprimer cette bête ?</p>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <form action="" method="post">
                      <input type="text" id="selectedId" name="selectedId" value="" style="display:none;">
                      <input type="submit" name="delete" id="delete" value="Supprimer" class="btn btn-danger">
                    </form>
                  </div>
                </div>
            </div>
            </div>
        </div>

<?php include 'footer.php'; ?>