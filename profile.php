<?php
 
session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Profile';
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

          <!-- Content Row -->

          <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                     <form>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                            <label for="validationDefault01">First name</label>
                            <input type="text" class="form-control" id="validationDefault01" value="Mark" required>
                            </div>
                            <div class="col-md-6 mb-3">
                            <label for="validationDefault02">Last name</label>
                            <input type="text" class="form-control" id="validationDefault02" value="Otto" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                            <label for="validationDefault01">Adresse email</label>
                            <input type="text" class="form-control" id="validationDefault01" value="<?= $_SESSION['userEmail'] ?>" required>
                            <small id="emailHelp" class="form-text text-muted">Votre identifiant de connexion</small>
                            </div>
                            <div class="col-md-6 mb-3">
                            <label for="validationDefault02">Téléphone</label>
                            <input type="text" class="form-control" id="validationDefault02" value="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                            <label for="validationDefault03">Adresse</label>
                            <input type="text" class="form-control" id="validationDefault03" required>
                            </div>
                            <div class="col-md-3 mb-3">
                            <label for="validationDefault05">Ville</label>
                            <input type="text" class="form-control" id="validationDefault05" required>
                            </div>
                            <div class="col-md-3 mb-3">
                            <label for="validationDefault05">Code Postal</label>
                            <input type="text" class="form-control" id="validationDefault05" required>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-primary" type="submit">Sauvegarder les changements</button>
                        </form>
                </div>
              </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Photo de profil</h6>
                  <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                      <a class="dropdown-item" href="#">Modifier</a>
                      <a class="dropdown-item" href="#">Supprimer</a>
                    </div>
                  </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <img class="img-profile rounded img-fluid mx-auto d-block" src="https://source.unsplash.com/QAB-WJcbgJk/200x200">
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

<?php include 'footer.php'; ?>