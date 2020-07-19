<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

  <!-- Main Content -->
  <div id="content">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

      <!-- Sidebar Toggle (Topbar) -->
      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
      </button>

      <!-- Topbar Search -->
      <form class="d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="search">
        <div class="input-group">
          <input type="search" class="form-control bg-light border-0 small" name="q" id="q" placeholder="Rechercher un nom ou un identifiant" aria-label="Search" aria-describedby="basic-addon2">
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
              <i class="fas fa-search fa-sm text-white"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Topbar Navbar -->
      <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
          <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-search fa-fw"></i>
          </a>
          <!-- Dropdown - Messages -->
          <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
            <form class="form-inline mr-auto w-100 navbar-search">
              <div class="input-group">
                <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher..." aria-label="Search" aria-describedby="basic-addon2">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>

        <?php

        // On récupère tout le contenu de la table cows si vache enceinte, non archivée et appartenant a l'utilisateur en cours
        $owner_id = $_SESSION['userID'];
        $database = getPDO();
        $reponseCowPregnant = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND isarchived = 0 AND ispregnant = 1 AND death_date = '' AND sale_date = '' ORDER BY `cows`.`pregnant_since` DESC");
        $reponseCowPregnant->execute([$owner_id]);
        $pregnantNumber = $reponseCowPregnant->rowCount();

        ?>

        <!-- Nav Item - Pregnancy -->
        <li class="nav-item dropdown no-arrow mx-1" id="gestNotification">
          <a class="nav-link dropdown-toggle" href="#" id="pregnancyAlert" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-baby-carriage"></i>
            <!-- Counter - Pregnancy -->
            <?php if ($pregnantNumber > 0) { ?>
              <span class="badge badge-success badge-counter" id="gestNotifBadge"><?= $pregnantNumber . ' ' ?></span>
            <?php } ?>
          </a>
          <!-- Dropdown - Pregnancy -->
          <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
              <?php if ($pregnantNumber > 1) {
                $ps = 's';
              }else {
                $ps = '';
              } ?>
              <?= $pregnantNumber . ' ' ?> Gestation<?= $ps ?> en cours
            </h6>
            <?php
            while ($donnees = $reponseCowPregnant->fetch()) {
              $pregnantdays = daysSince($donnees['pregnant_since']);
              $pregnantpercent = ($pregnantdays / 283 * 100);
              if ($pregnantdays >= 280) {
                $color = "bg-danger";
              } else if ($pregnantdays >= 250 && $pregnantdays < 280) {
                $color = "bg-warning";
              } else {
                $color = "bg-success";
              }

            ?>
              <a class="dropdown-item d-flex align-items-center" href="cow-single?id=<?= $donnees['id']; ?>">
                <div class="mr-3">
                  <div class="icon-circle bg-primary <?= $color ?>">
                    <i class="fad fa-cow text-white"></i>
                  </div>
                </div>
                <div class="w-100">
                  <div class="text-gray-900"><?= $donnees['name'] . ' - ' . $donnees['id']; ?></div>
                  <div class="progress">
                    <div class="progress-bar <?= $color ?>" role="progressbar" style="width:<?= $pregnantpercent ?>%;" aria-valuenow="<?= $pregnantpercent ?>" aria-valuemin="0" aria-valuemax="100"><?= $pregnantdays . '/283' ?></div>
                  </div>
                </div>
              </a>
            <?php
            }
            $reponseCowPregnant->closeCursor();
            ?>

            <a class="dropdown-item text-center small text-gray-500" href="gestations">Voir tout</a>
          </div>
        </li>

        <?php
        // On récupère tout le contenu de la table treats sauf les traitements qui ne se repettent pas
        // puisqu'on ne va pas les afficher.
        $owner_id = $_SESSION['userID'];
        $database = getPDO();
        $reponseTreats = $database->prepare("SELECT * FROM treats WHERE t_owner_id = ? AND t_repeat != 0");
        $reponseTreats->execute([$owner_id]);

        // Un appel qui servira à récuperer le nom du bovin associé au traitement
        $needCows = $database->prepare("SELECT cow_index, id, name FROM cows WHERE owner_id = ? AND isarchived = 0 AND death_date = '' AND sale_date = ''");
        $needCows->execute([$owner_id]);
        $dataCows = $needCows->fetchAll(); // Cré un tableau avec les resultats
        $indexArray = array_column($dataCows, 'cow_index'); // Cré un tableau avec seulement les cow_index car on le connait dans la BDD des traitements
        
        ?>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1" id="treatNotification">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-syringe fa-fw"></i>
            <!-- Counter - Alerts -->
            <span class="badge badge-success badge-counter" id="treatNotifBadge"></span>
          </a>
          <!-- Dropdown - Alerts -->
          <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
            <h6 class="dropdown-header">
              Traitements <span class="capitalize" id="howManyToday"></span>
            </h6>
            <?php
            while ($data = $reponseTreats->fetch()) {
              // Retrouver le nom du bovin
              $key = array_search($data['t_cow_index'], $indexArray);
              $cowName =  $dataCows[$key]['name'];
              $cowId =  $dataCows[$key]['id'];

              $dateOfToday = date('d/m/Y');
              if ($data['t_repeat'] != 0) {
                $dateEnd = futureDateDay($data['t_date'], $data['t_days']);
              }
              if (compareDate($dateEnd, $dateOfToday)) {
                $color = "bg-warning";
              } else if ($dateOfToday == $dateEnd) {
                $color = "bg-danger";
              } else {
                $color = "bg-success";
              }
              if ($data['t_repeat'] == 2 && $color == 'bg-warning') {
                $color = 'bg-danger';
              }

              // On affiche seulement les traitements en cours, donc pas ceux qui ont la couleur success,
              // ce n'est pas la peine puisqu'ils sont terminés.
              if ($color != 'bg-success') {

            ?>
                <a class="dropdown-item d-flex align-items-center treatNotifElement" href="cow-single?id=<?= $cowId; ?>">
                  <div class="mr-3">
                    <div class="icon-circle <?= $color ?>">
                      <i class="fad fa-syringe text-white"></i>
                    </div>
                  </div>
                  <div class="w-100">
                    <div class="text-gray-900"><span class="font-weight-bold"><?= $data['t_name'] ?></span> <span class="font-weight-light">pour</span> <?= $cowName ?></div>
                    
                    <div>
                      <?php
                      switch ($data['t_repeat']) {
                        case 0:
                          echo 'Une seule fois';
                          break;
                        case 1:
                          if ($dateEnd == $dateOfToday) {
                            echo 'Répéter aujourd\'hui';
                          } else {
                            echo 'Répéter le ' . $dateEnd;

                          }
                          break;
                        case 2:
                          if ($dateEnd == $dateOfToday) {
                            echo 'Dernier jour aujourd\'hui';
                          } else {
                            echo 'Jusqu\'au ' . $dateEnd;
                          }
                          break;
                      }
                      ?>
                    </div>
                  </div>
                </a>
            <?php
              }
            }
            $reponseTreats->closeCursor();
            ?>
            <a class="dropdown-item text-center small text-gray-500" href="treats">Voir tout</a>
          </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['userFirstname'] ?> <?= $_SESSION['userLastname'] ?></span>
            <img class="img-profile rounded-circle" src="img/<?php if (!empty($_SESSION['userImg'])) {
                                                                echo 'profilepic/' . $_SESSION['userImg'];
                                                              } else {
                                                                echo 'default.png';
                                                              }; ?>">
          </a>
          <!-- Dropdown - User Information -->
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="profile">
              <i class="fas fa-user-cowboy fa-sm fa-fw mr-2 text-gray-400"></i>
              Profil
            </a>
            <a class="dropdown-item" href="settings">
              <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
              Réglages
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Se déconnecter
            </a>
          </div>
        </li>

      </ul>

    </nav>
    <!-- End of Topbar -->