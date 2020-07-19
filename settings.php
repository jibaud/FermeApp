<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Réglages';
include 'header.php';

// Update settings
if (isset($_POST['settingsSubmit'])) {
  $updatePrefixId = htmlspecialchars($_POST['prefixId']);
  try {
    $database = getPDO();
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $updateSettings = $database->prepare("UPDATE settings SET set_prefix = ? WHERE set_for = $currentUserId");
    $updateSettings->execute([$updatePrefixId]);
  } catch (Exception $e) {
    die('Error : ' . $e->getMessage());
  }

  header('Location:settings');
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
      <form class="enableSubmitOnChange" method="post">

        <div class="d-sm-flex align-items-center justify-content-between mb-2">
          <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>
          <input type="submit" class="btn btn-primary mb-3" id="settingsSubmit" name="settingsSubmit" value="Sauvegarder les changements">
        </div>

        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Général</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Bovins</a>
          </div>
        </nav>

        <div class="tab-content pb-5 pt-4" id="nav-tabContent">

          <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="row">
              <div class="col-md-6">
                <label for="">Langue</label>
                <select name="" id="" class="form-control">
                  <option value="">Francais</option>
                </select>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="row">
              <div class="col-md-6">
                <label for="prefixId">Préfixe <span class="font-weight-lighter">[Pays] [Département] [Exploitation]</span></label>
                <input type="text" class="form-control" id="prefixId" name="prefixId" value="<?php if (isset($set_prefixId)) {
                                                                                                echo $set_prefixId;
                                                                                              } ?>">
              </div>
            </div>
          </div>

        </div> <!-- /.tab-content -->

      </form>

    </div>
    <!-- /.container-fluid -->

    <?php include 'footer.php'; ?>