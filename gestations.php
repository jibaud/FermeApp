<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

$pageTitle = 'Gestations';
include 'header.php';

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
            <h1 class="h3 mb-1 text-gray-800"><?= $pageTitle ?></h1>

            <h5 class="mb-4"><?= $pregnantNumber . ' ' ?> en cours</h5>

            <?php
            $reponseCowPregnant->execute([$owner_id]);

            while ($donnees = $reponseCowPregnant->fetch()) {
                $pregnantdays = daysSince($donnees['pregnant_since']);
                $pregnantpercent = ($pregnantdays / 283 * 100);
                if ($pregnantdays >= 283) {
                    $color = "danger";
                } else if ($pregnantdays >= 250 && $pregnantdays < 283) {
                    $color = "warning";
                } else {
                    $color = "success";
                }

            ?>
                <div class="card border-left-<?= $color ?> shadow h-100 py-2 mb-3">
                    <div class="card-body">
                        <div class="d-flex no-glutters align-items-center mb-3">
                            <div class="mr-3">
                                <div class="icon-circle bg-<?= $color ?>">
                                    <i class="fad fa-cow text-white"></i>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="text-gray-900 uppercase"><?= $donnees['name'] . ' - ' . $donnees['id']; ?></div>
                                <div class="progress">
                                    <div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width:<?= $pregnantpercent ?>%;" aria-valuenow="<?= $pregnantpercent ?>" aria-valuemin="0" aria-valuemax="100"><?= $pregnantdays . '/283' ?></div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $daysToEndOfPregnant = (283 - $pregnantdays);
                        if ($daysToEndOfPregnant >= 0) {
                        ?>
                            <p class="mt-1 mb-1 pr-1">Vêlage prévu dans environ <?= $daysToEndOfPregnant ?> jours, soit le <?= futureDateDay($donnees['pregnant_since'], 283) ?></p>
                        <?php } else { ?>
                            <p class="mt-1 mb-1 pr-1">Vêlage prévu depuis environ <?= abs($daysToEndOfPregnant) ?> jours </p>
                        <?php
                        }
                        if (abs($daysToEndOfPregnant) >= 31) {
                            $monthToEndOfPregnant = round(abs($daysToEndOfPregnant) / 30.5, 1);
                        ?>
                            <span class="font-weight-lighter"><em> (~<?= $monthToEndOfPregnant ?> mois)</em></span>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
            $reponseCowPregnant->closeCursor();
            ?>

        </div>
        <!-- /.container-fluid -->
        <?php include 'footer.php'; ?>