<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Étable';
include 'header.php';


// Supprimer une bête
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
                      <td style="text-transform:capitalize;" id="namefor<?= $donnees['id'];?>"><?= $donnees['name'];?></td>
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
                          <button type="button" class="btn btn-primary btn-sm" id="<?= $donnees['id'];?>" data-toggle="modal" data-target="#viewCowModal<?= $donnees['id'];?>">
                            <i class="fas fa-eye"></i>
                          </button>
                    
                        <button type="button" class="btn btn-success btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                        </button>

                          <button type="button" class="btn btn-danger btn-sm deleteButton" id="<?= $donnees['id'];?>" data-toggle="modal" data-target="#deleteCowModal">
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

<?php
$reponseCowList->execute();
while ($donnees = $reponseCowList->fetch())
{
?>

<script>
  $('#tooltipFor<?= $donnees["id"]?>').tooltip({ 
  title: 'coucou'
 })
</script>

        <!-- View Cow Modal -->
        <div class="modal fade" id="viewCowModal<?= $donnees['id']?>" tabindex="-1" role="dialog" aria-labelledby="ViewCow" aria-hidden="true" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-gray-800" id=""><?= $donnees['name'];?> - <?= $donnees['id'];?></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                  <table class="table table-sm">
                    <tbody>
                      <tr>
                        <th scope="row">Nom</th>
                        <td><?= $donnees['name'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Numéro d'identification</th>
                        <td><?= $donnees['id'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Genre</th>
                        <td><?= $donnees['gender'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Type</th>
                        <td><?= $donnees['type'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Race</th>
                        <td><?= $donnees['race'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Date de naissance</th>
                        <td><?= $donnees['birth_date'];?></td>
                      </tr>
                      <tr>
                        <th scope="row">Age</th>
                        <td><?= calculeAge($donnees['birth_date'], 'full') ?></td>
                      </tr>
                      <tr>
                        <th scope="row">Numéro de la mère</th>
                        <?php if($donnees['mother_id'] == '') { ?>
                        <td>Inconnu</td>
                        <?php } else { ?>
                        <td><a href="#" id="tooltipFor<?= $donnees['id'];?>" data-toggle="modal" data-target="#viewCowModal<?= $donnees['mother_id'];?>" data-dismiss="modal"><?= $donnees['mother_id'];?></a></td>
                        <?php } ?>
                      </tr>
                    </tbody>
                  </table>

                </div>
            </div>
            </div>
        </div>

<?php } ?>