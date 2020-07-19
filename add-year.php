<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Ajouter une année';
include 'header.php';


$owner_id = $_SESSION['userID'];
$database = getPDO();


// Ajouter une année
if (isset($_POST['addYear'])) {
    $yearToAdd = htmlspecialchars($_POST['yearToAdd']);
    if (is_numeric($yearToAdd)) { // On vérifie qu'il n'y ai que des chiffres
        if ($yearToAdd >= 0 && $yearToAdd <= 2100) { // On vérifie que l'année soit valide, comprise entre 0 et 2100
            $salesYears = array($yearToAdd);
            try {
                $updateYearsArray = $database->prepare("UPDATE users SET sales_years = ? WHERE user_id = $owner_id");
                $updateYearsArray->execute([
                    serialize($salesYears)
                ]);

                // Créer chaque mois de l'année dans sales
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Janvier ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Février ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Mars ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Avril ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Mai ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Juin ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Juillet ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Août ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Septembre ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Octobre ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Novembre ' . $yearToAdd]);
                $insertMonth = $database->prepare("INSERT INTO sales(owner_id, date) VALUES(?, ?)");
                $insertMonth->execute([$owner_id, 'Décembre ' . $yearToAdd]);
            } catch (Exception $e) {
                echo " Error ! " . $e->getMessage();
            }

            header('Location: /sales?s=1&y=' . $yearToAdd);
        } else {
            $errorMessage = 'Cette année n\'est pas valide.';
            header('Location: /add-year?e=1');
        }
    } else {
        $errorMessage = 'L\'année doit être composé uniquement de chiffres';
        header('Location: /add-year?e=1');
    }
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
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
            </div>

            <?php if (isset($errorMessage)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $errorMessage // <?= shortcode for <?php echo 
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($successMessage)) { ?>
                <div class="alert alert-success" role="alert">
                    <?= $successMessage ?>
                </div>
            <?php } ?>


            <div class="row">

                <!-- Card  -->
                <div class="col-xl-12">
                    <p>Vous n'avez actuellement aucune valeur de vente de lait. Veuillez créer une année afin d'y ajouter des valeurs correspondantes.</p>
                    <form class="form-inline" id="addYearForm" action="" method="post" class="noEnterKey">
                        <div class="form-group mx-sm-3 mb-2">
                            <input type="text" class="form-control" id="yearToAdd" name="yearToAdd" maxlength="4" placeholder="Année" value="">
                            <button type="submit" form="addYearForm" id="addYear" name="addYear" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <!-- /.container-fluid -->
        <?php include 'footer.php'; ?>