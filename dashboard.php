<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Tableau de bord';
include 'header.php';

?>


<body id="page-top">
  <?php include 'includes/loader.php'; ?>




  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php include 'sidebar.php'; ?>
    <?php include 'topbar.php'; ?>



    <?php

    // On récupère tout le contenu de la table cows
    $dashboardCow = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND isarchived = 0 AND death_date = '' AND sale_date = ''");
    $dashboardCow->execute([$owner_id]);
    $totalCow = 0;
    $totalCalf = 0;
    $totalYoungCow = 0;

    while ($data = $dashboardCow->fetch()) {

      if (calculeType($data['birth_date']) == 'veau') {
        $totalCalf++;
      } else if (calculeType($data['birth_date']) == 'génisse') {
        if ($data['pregnant_number'] > 0) {
          $totalCow++;
        } else {
          $totalYoungCow++;
        }
      }
    }


    // On récupère les ventes de lait, qu'on place dans des tableaux récupérés par le script du graphique en bas de page.
    $currentYear = date('Y');
    $milkSalary = $database->prepare("SELECT * FROM sales WHERE owner_id = '$currentUserId' AND date LIKE '%$currentYear%' ORDER BY id");
    $milkSalary->execute([$owner_id]);
    if ($milkSalary) {
      $dates = array();
      $counts = array();
      while ($row = $milkSalary->fetch()) {
        $dates[] = $row["date"];
        $amounts[] = $row["amount"];
      }
    }

    ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">



        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Card  -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total vaches</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCow ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-cow fa-3x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card  -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total génisses</div>
                      <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $totalYoungCow ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fad fa-cow fa-3x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card  -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total veaux</div>
                      <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $totalCalf ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="icon-calf fa-3x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card  -->
            <div class="col-xl-3 col-md-6 mb-4">
              <a href="treats" class="text-decoration-none text-reset gest-card">
                <div class="card shadow h-100 py-2" id="treatDashboardCard">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" id="treatDashboardTitle">Traitements</div>
                        <div class="row no-gutters align-items-center">
                          <div class="col-auto">
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="treatDashboardText"></div>
                          </div>
                        </div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-syringe fa-3x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <!-- Card  -->
            <div class="col-xl-3 col-md-6 mb-4">
              <a href="gestations" class="text-decoration-none text-reset gest-card">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gestation<?= $ps ?> en cours</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pregnantNumber //topbar top
                                                                            ?></div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-baby-carriage fa-3x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>


          <?php
          // On récupere les années déjà créées
          $requestUserYears = $database->prepare("SELECT sales_years FROM users WHERE user_id = ?");
          $requestUserYears->execute([$currentUserId]);
          $yearsCount = $requestUserYears->rowCount();

          $salesYears = $requestUserYears->fetchColumn(0);
          $salesYears = unserialize($salesYears);
          ?>

          <!-- Content Row -->

          <div class="row">

            <!-- Card  -->
            <div class="col-xl-8 col-lg-7">
              <div class="card shadow mb-4" id="dashboardMilkSale">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Ventes de lait en <?= $currentYear ?></h6>
                  <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                      <div class="dropdown-header">Année:</div>
                      
                      <?php
                      foreach ($salesYears as $year) {
                        echo '<a class="dropdown-item" href="sales?y=' . $year . '">' . $year . '</a>';
                      }
                      ?>
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

            <!-- Quote -->
            <div class="col-xl-4 col-lg-5">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-quote-left"></i> Citation au hasard</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">

                  <?php
                  // Citation aléatoire.
                  $fichier = file('assets/quotes.txt');
                  $totalRow = count($fichier); // Total du nombre de lignes du fichier
                  $iRandom = mt_rand(0, $totalRow - 1);
                  echo $fichier[$iRandom]; // On affiche une citation au hasard
                  ?>

                </div>
              </div>
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Météo Rumilly</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <!-- widget meteo -->
                  <div id="widget_3e388d86e28dcc0580e6a2296fb6b6b4">
                    <span id="l_3e388d86e28dcc0580e6a2296fb6b6b4"><a href="http://www.mymeteo.info/r/rumilly-74_f">https://www.my-meteo.com</a></span>
                    <script type="text/javascript">
                      (function() {
                        var my = document.createElement("script");
                        my.type = "text/javascript";
                        my.async = true;
                        my.src = "https://services.my-meteo.com/widget/js?ville=24113&format=carre&nb_jours=5&temps&icones&vent&precip&coins&c1=393939&c2=2b86c6&c3=transparent&c4=ffffff&c5=45a5e9&c6=ff3838&police=4&t_icones=2&x=336&y=500&d=0&id=3e388d86e28dcc0580e6a2296fb6b6b4";
                        var z = document.getElementsByTagName("script")[0];
                        z.parentNode.insertBefore(my, z);
                      })();
                    </script>
                  </div>
                  <!-- widget meteo -->

                </div>
              </div>
            </div>

            <div class="col-xl-4 col-lg-5">

            </div>
          </div>

          <div class="row">

          </div>


        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <?php include 'footer.php'; ?>

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