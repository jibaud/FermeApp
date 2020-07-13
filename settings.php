<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Paramètres';
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
      <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>

      <form class="enableSubmitOnChange" method="post">
        <div class="mt-5 mb-5">
          <h5>Général</h5>
          <div class="row">
            <div class="col-md-6">
              <label for="">Langue</label>
              <select name="" id="" class="form-control">
                <option value="">Francais</option>
              </select>
            </div>
          </div>
        </div>

        <div class="mb-5">
          <h5>Identification des bovins</h5>
          <div class="row">
            <div class="col-md-6">
              <label for="">Préfixe <span class="font-weight-lighter">[Pays] [Département] [Exploitation]</span></label>
              <input type="text" class="form-control" id="prefixId" name="prefixId" value="<?php if(isset($set_prefixId)){echo $set_prefixId;}?>">
            </div>
          </div>
        </div>

        <input type="submit" class="btn btn-primary mb-3" id="settingsSubmit" name="settingsSubmit" value="Sauvegarder les changements">
      </form>


    </div>
    <!-- /.container-fluid -->

    <?php include 'footer.php'; ?>