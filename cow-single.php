<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Harmonie'; // A CHANGER !!
include 'header.php';

$database = getPDO();
$owner_id = $_SESSION['userID'];
$currentId = $_GET['id'];
echo 'owner_id ='.$owner_id.'<br>';
echo 'currentId ='.$currentId;
$reponseCow = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND id = ?");
$reponseCow->execute([$owner_id, $currentId]);

// Stocker résultats dans un array
$result = $reponseCow->fetchAll();
print_r($result);

?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

<?php include 'sidebar.php'; ?>
<?php include 'topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

<?php
while ($donnees = $reponseCow->fetch())
{
?>
<?= 'Nom : '.$donnees['name'];?>
<?php
}
?>

          <!-- Page Heading -->
          <div class="row">
          <div class="col-xl-5 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                  <span class="badge badge-warning text-black text-lg h5">3928</span>
                  <h2 class="capitalize font-weight-bold text-primary text-uppercase mb-1"><?= $pageTitle ?></h2>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">02/12/1991</div>
                    <div class="mb-0 text-gray-800">28 ans et 3 mois</div>
                  </div>
                  <div class="col-auto">
                    <i class="fad fa-cow fa-4x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div> 
            </div>
          </div>
            
            
        </div>
        <!-- /.container-fluid -->
<?php include 'footer.php'; ?>