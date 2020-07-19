<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Ventes';
include 'header.php';


$owner_id = $_SESSION['userID'];
$database = getPDO();

// On récupère les ventes de lait, qu'on place dans des tableaux récupérés par le script du graphique en bas de page.
if (isset($_GET['y'])) {
    $currentYear = $_GET['y'];
} else {
    $currentYear = null;
}
$milkSalary = $database->prepare("SELECT * FROM sales WHERE owner_id = ? AND date LIKE '%$currentYear%' ORDER BY id");
$milkSalary->execute([$owner_id]);
if ($milkSalary) {
    $dates = array();
    $counts = array();
    while ($row = $milkSalary->fetch()) {
        $dates[] = $row["date"];
        $amounts[] = $row["amount"];
    }
    if (isset($amounts)) {
        $totalYear = array_sum($amounts);
    }
}

// On récupere les années déjà créées
$requestUserYears = $database->prepare("SELECT sales_years FROM users WHERE user_id = ?");
$requestUserYears->execute([$currentUserId]);
$yearsCount = $requestUserYears->rowCount();

$salesYears = $requestUserYears->fetchColumn(0);
$salesYears = unserialize($salesYears);
arsort($salesYears);
if (array_sum($salesYears) < 1) {
    header('Location: add-year');
} else if ($currentYear == null){
    header('Location: sales?y='.max($salesYears));
}

// Ajouter une année
if (isset($_POST['addYear'])) {
    $yearToAdd = htmlspecialchars($_POST['yearToAdd']);
    if (in_array($yearToAdd, $salesYears)) { // Si l'année est déjà dans le tableau dans la BDD
        $errorMessage = 'Cette année existe déjà.';
        header('Location: /sales?e=1&y=' . $yearToAdd);
    } else {
        if (is_numeric($yearToAdd)) { // On vérifie qu'il n'y ai que des chiffres
            if ($yearToAdd >= 0 && $yearToAdd <= 2100) { // On vérifie que l'année soit valide, comprise entre 0 et 2100
                array_push($salesYears, $yearToAdd);
                arsort($salesYears);
                try {
                    $currentUserId = $_SESSION['userID'];
                    $updateYearsArray = $database->prepare("UPDATE users SET sales_years = ? WHERE user_id = $currentUserId");
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
                header('Location: /sales?e=1');
            }
        } else {
            $errorMessage = 'L\'année doit être composé uniquement de chiffres';
            header('Location: /sales?e=1');
        }
    }
}


// Supprimer une année
if (isset($_POST['deleteYear'])) {
    if (($key = array_search($currentYear, $salesYears)) !== false) {
        unset($salesYears[$key]);
    }
    try {
        $currentUserId = $_SESSION['userID'];
        $updateYearsArray = $database->prepare("UPDATE users SET sales_years = ? WHERE user_id = $currentUserId");
        $updateYearsArray->execute([
            serialize($salesYears)
        ]);

        // Supprimer chaque mois de l'année dans sales
        $deleteMonth = $database->prepare("DELETE FROM sales WHERE owner_id = '$owner_id' AND date LIKE '%$currentYear%'");
        $deleteMonth->execute();
    } catch (Exception $e) {
        echo " Error ! " . $e->getMessage();
    }

    header('Location: /sales?s=1');
}


// Modifier les valeurs des mois
if (isset($_POST['updateMonths'])) {
    $january = htmlspecialchars($_POST['january']);
    $february = htmlspecialchars($_POST['february']);
    $march = htmlspecialchars($_POST['march']);
    $april = htmlspecialchars($_POST['april']);
    $may = htmlspecialchars($_POST['may']);
    $june = htmlspecialchars($_POST['june']);
    $july = htmlspecialchars($_POST['july']);
    $august = htmlspecialchars($_POST['august']);
    $september = htmlspecialchars($_POST['september']);
    $october = htmlspecialchars($_POST['october']);
    $november = htmlspecialchars($_POST['november']);
    $december = htmlspecialchars($_POST['december']);

    try {
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$january, 'Janvier ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$february, 'Février ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$march, 'Mars ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$april, 'Avril ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$may, 'Mai ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$june, 'Juin ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$july, 'Juillet ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$august, 'Août ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$september, 'Septembre ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$october, 'Octobre ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$november, 'Novembre ' . $currentYear]);
        $updateMonths = $database->prepare("UPDATE sales SET amount = ? WHERE owner_id = '$owner_id' and date = ?");
        $updateMonths->execute([$december, 'Décembre ' . $currentYear]);
    } catch (Exception $e) {
        echo " Error ! " . $e->getMessage();
    }

    header('Location: /sales?s=1');
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
                <?php if (isset($currentYear)) { ?>
                    <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?> de lait en <?= $currentYear ?></h1>
                <?php } else { ?>
                    <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?> de lait</h1>
                <?php } ?>
                <div class="btn-group mt-2 mb-3">

                    <?php
                    if (in_array($currentYear, $salesYears)) {
                    ?>

                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-calendar-alt mr-1"></i> <?= $currentYear ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addYearModal"><i class="far fa-calendar-plus mr-1"></i> Ajouter une année</a>
                            <div class="dropdown-divider"></div>
                            <?php
                            foreach ($salesYears as $year) {
                                if ($currentYear == $year) {
                                    echo '<a class="dropdown-item active" href="sales?y=' . $year . '">' . $year . '</a>';
                                } else {
                                    echo '<a class="dropdown-item" href="sales?y=' . $year . '">' . $year . '</a>';
                                }
                            }
                            ?>
                        </div>
                    <?php
                    } else {
                    ?>
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Année
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addYearModal"><i class="far fa-calendar-plus mr-1"></i> Ajouter une année</a>
                            <div class="dropdown-divider"></div>
                            <?php
                            foreach ($salesYears as $year) {
                                if ($currentYear == $year) {
                                    echo '<a class="dropdown-item active" href="sales?y=' . $year . '">' . $year . '</a>';
                                } else {
                                    echo '<a class="dropdown-item" href="sales?y=' . $year . '">' . $year . '</a>';
                                }
                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>

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
                <?php if (isset($currentYear)) { ?>
                    <div class="col-xl-12">
                        <div class="card shadow mb-4" id="dashboardMilkSale">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><?= $totalYear ?> euros</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#updateMonthsModal"><i class="far fa-calendar-edit mr-1"></i> Modifier les valeurs</a>
                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addYearModal"><i class="far fa-calendar-plus mr-1"></i> Ajouter une année</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteYearModal"><i class="far fa-calendar-times mr-1"></i> Supprimer <?= $currentYear ?></a>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="myAreaChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div>

        <!-- /.container-fluid -->
        <?php include 'footer.php'; ?>


        <!-- MODAL ADD YEAR -->
        <div class="modal fade" id="addYearModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une année</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="form-inline" id="addYearForm" action="" method="post" class="noEnterKey">
                            <div class="form-group mx-sm-3 mb-2">
                                <input type="text" class="form-control" id="yearToAdd" name="yearToAdd" maxlength="4" placeholder="Année" value="">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" form="addYearForm" id="addYear" name="addYear" class="btn btn-primary">Ajouter</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL UPDATE MONTHS VALUES -->
        <div class="modal fade" id="updateMonthsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier les valeurs en <?= $currentYear ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="updateMonthsValues" class="noEnterKey">
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="january">Janvier</label>
                                    <input type="text" class="form-control" id="january" name="january" <?php if (isset($amounts)) { ?>value="<?= $amounts[0] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="february">Février</label>
                                    <input type="text" class="form-control" id="february" name="february" <?php if (isset($amounts)) { ?>value="<?= $amounts[1] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="march">Mars</label>
                                    <input type="text" class="form-control" id="march" name="march" <?php if (isset($amounts)) { ?>value="<?= $amounts[2] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="april">Avril</label>
                                    <input type="text" class="form-control" id="april" name="april" <?php if (isset($amounts)) { ?>value="<?= $amounts[3] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="may">Mai</label>
                                    <input type="text" class="form-control" id="may" name="may" <?php if (isset($amounts)) { ?>value="<?= $amounts[4] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="june">Juin</label>
                                    <input type="text" class="form-control" id="june" name="june" <?php if (isset($amounts)) { ?>value="<?= $amounts[5] ?>" <?php } ?>>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="july">Juillet</label>
                                    <input type="text" class="form-control" id="july" name="july" <?php if (isset($amounts)) { ?>value="<?= $amounts[6] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="august">Août</label>
                                    <input type="text" class="form-control" id="august" name="august" <?php if (isset($amounts)) { ?>value="<?= $amounts[7] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="september">Septembre</label>
                                    <input type="text" class="form-control" id="september" name="september" <?php if (isset($amounts)) { ?>value="<?= $amounts[8] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="april">Octobre</label>
                                    <input type="text" class="form-control" id="october" name="october" <?php if (isset($amounts)) { ?>value="<?= $amounts[9] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="november">Novembre</label>
                                    <input type="text" class="form-control" id="november" name="november" <?php if (isset($amounts)) { ?>value="<?= $amounts[10] ?>" <?php } ?>>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="december">Décembre</label>
                                    <input type="text" class="form-control" id="december" name="december" <?php if (isset($amounts)) { ?>value="<?= $amounts[11] ?>" <?php } ?>>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" form="updateMonthsValues" id="updateMonths" name="updateMonths" class="btn btn-success">Valider</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- MODAL DELETE YEAR -->
        <div class="modal fade" id="deleteYearModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer <?= $currentYear ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Voulez-vous vraiment supprimer l'année <?= $currentYear ?> et toutes ses valeurs ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <form action="" method="post">
                            <input type="submit" id="deleteYear" name="deleteYear" class="btn btn-danger" value="Supprimer">
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            // Area Chart Script
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($dates); ?>,
                    datasets: [{
                        label: "Lait",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: <?= json_encode($amounts); ?>,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return number_format(value, 0, ',', '.') + "€";
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + number_format(tooltipItem.yLabel, 2, ',', '.') + '€';
                            }
                        }
                    }
                }
            });
        </script>