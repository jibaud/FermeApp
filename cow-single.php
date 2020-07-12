<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

include 'header.php';

if (!isset($_GET['id'])) {
	header('Location:cows-manager');
}

switch ($_GET['eg']) {
	case 1:
		$errorMessageGest = 'Champs état non valide.';
		break;
	case 2:
		$errorMessageGest = 'Le champs note est trop long. 50 charactères maximum.';
		break;
	case 3:
		$errorMessageGest = 'Une date de fin de gestation est nécéssaire.';
		break;
	case 4:
		$errorMessageGest = 'Une date de début de gestation est nécéssaire.';
		break;
	case 5:
		$errorMessageGest = 'Cet vache à déjà une gestation en cours.';
		break;
	case 6:
		$errorMessageGest = 'La date de fin ne peut pas être plus ancienne que celle du début.';
		break;
}

switch ($_GET['e']) {
	case 1:
		$errorMessage = 'Une vache existe déjà avec ce numéro.';
		break;
	case 2:
		$errorMessage = 'Le numéro d\'identification n\'est pas valide.';
		break;
	case 3:
		$errorMessage = 'Le nom est trop long. 32 charactères maximum.';
		break;
	case 4:
		$errorMessage = 'Veuillez remplir tous les champs obligatoires.';
		break;
	case 5:
		$errorMessage = 'Vous devez préciser la date de mort.';
		break;
	case 5:
		$errorMessage = 'Vous devez préciser la date et le prix de vente.';
		break;
	case 6:
		$errorMessage = 'Veuillez indiquer un prix valide.';
		break;
}


// Usefull datas
$database = getPDO();
$owner_id = $_SESSION['userID'];
$currentCowId = $_GET['id'];


// Appel les données de la vache actuelle
$reponseCow = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND id = ? LIMIT 1"); // LIMIT 1 car il ne doit y avoir qu'un seul résultat de toute façon
$reponseCow->execute([$owner_id, $currentCowId]);
$result = $reponseCow->fetch();

$currentCowIndex = $result['cow_index'];

if ($result['pregnant_number'] > 0) {
	$type = 'vache';
} else {
	$type = calculeType($result['birth_date']);
}

// Un E à la fin du mot si féminin, exemple : vendu(e)
if ($result['gender'] == 'femelle') {
	$eIfFemal = 'e';
} else {
	$eIfFemal = '';
}

// Si l'id demandé dans l'URL est dans les archives.
if ($result['isarchived'] == 1) {
	header('Location:archives?e=' . $result['id']);
}

// Si l'id demandé dans l'URL n'existe pas.
$reponseCow->execute([$owner_id, $currentCowId]);
$result = $reponseCow->fetch();
if (!isset($result['id'])) {
	header('Location:cows-manager');
}

include 'includes/cow-single-engine.php';

$pageTitle = $result['name'];

?>

<body id="page-top">
	<?php include 'includes/loader.php'; ?>

	<!-- Page Wrapper -->
	<div id="wrapper">


		<?php include 'sidebar.php'; ?>
		<?php include 'topbar.php'; ?>

		<!-- Begin Page Content -->
		<div class="container-fluid">

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


			<!-- Page Heading -->
			<div class="d-sm-flex align-items-center justify-content-between mb-2">
				<h2 class="capitalize font-weight-bold text-primary"><?= $pageTitle ?><span class="badge badge-warning text-black ml-2"><?= $result['id']; ?></span></h2>
				<div class="dropdown">
					<a class="btn btn-primary btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-tools fa-sm mr-1 text-white-50"></i> Actions
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
						<a href="#gestations" class="dropdown-item <?php if ($result['ispregnant']) {
																		echo 'disabled" tabindex="-1" aria-disabled="true"';
																	} else {
																		echo '"';
																	}; ?>>Ajouter une gestation</a>
						<div class=" dropdown-divider"></div>
					<a class="dropdown-item" href="#" data-toggle="modal" data-target="#deadCowModal">Déclarer mort<?= $eIfFemal ?></a>
					<a class="dropdown-item" href="#" data-toggle="modal" data-target="#soldCowModal">Déclarer vendu<?= $eIfFemal ?></a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#archiveCowModal">Supprimer</a>
				</div>
			</div>
		</div>

		<div class="row mb-4">
			<div class="col-12">
				<p class="h5 text-gray-800"><?= $result['birth_date']; ?><span class="mb-0 text-gray-700 h6"> (<?= calculeAge($result['birth_date'], 'full') ?>)</span></p>
				<p class="h5 text-gray-800"><span class="capitalize"><?= $type; ?></span> <?= $result['gender']; ?> de race <?= $result['race']; ?></p>
			</div>
		</div> <!-- /.row -->


		<?php
		$pregnantdays = daysSince($result['pregnant_since']);
		$pregnantpercent = ($pregnantdays / 283 * 100);
		if ($pregnantdays >= 283) {
			$color = "danger";
		} else if ($pregnantdays >= 250 && $pregnantdays < 283) {
			$color = "warning";
		} else {
			$color = "success";
		}
		?>

		<div class="row mb-4">
			<?php if ($numberLastGest > 0) { ?>

				<div class="col-xl-6 col-md-6 mb-4">
					<div class="card border-left-primary shadow h-100 py-2">
						<div class="card-body">
							<div class="container">
								<!-- Stack the columns on mobile by making one full-width and the other half-width -->
								<div class="row mb-3 gestInfo">
									<div class="col-sm-2 align-middle">
										<div class="icon-circle bg-primary"><i class="icon-calf text-white"></i></div>
									</div>
									<div class="col-sm-10 align-middle">Dernier vêlage le <?= $lastGestResult['g_end']; ?>
									</div>
								</div>
								<div class="row mb-3 gestInfo">
									<div class="col-sm-2 align-middle">
										<div class="icon-circle bg-primary"><i class="fas fa-tint text-white"></i></div>
									</div>
									<div class="col-sm-10 align-middle"><?php
																		date_default_timezone_set('Europe/Paris');
																		$dateToday = date('j/m/Y');
																		$dateEndLact = futureDate($lastGestResult['g_end'], 10);
																		if (compareDate($dateEndLact, $dateToday)) { // Si date 1 > date 2
																		?>
											Lactation prévue pendant les <?= daysSince($dateEndLact) ?> prochains jours, soit jusqu'au <?= $dateEndLact; ?> environ.
										<?php
																		} else {
										?>
											Vache en tarrissement depuis <?= daysSince($dateEndLact); ?> jours
											<i class="fad fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="Information téhorique car cette vache a vêlé il y a plus de 10 mois"></i>
											<span class="font-weight-lighter"><em> (~<?= $dateEndLact; ?>)</em></span>
										<?php
																		}
										?></div>
								</div>
								<div class="row gestInfo">
									<div class="col-sm-2 align-middle">
										<div class="icon-circle bg-primary"></i><i class="icon-sperm text-white"></i></div>
									</div>
									<div class="col-sm-10 align-middle"><?php
																		$dateNextInsemin = futureDate($lastGestResult['g_end'], 3);
																		if (compareDate($dateNextInsemin, $dateToday)) {
																		?>
											Prévoir une insémination vers le <?= $dateNextInsemin ?>
											<i class="fad fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="3 mois après le dernier vêlage"></i>
										<?php
																		} else {
										?>
											La date idéale d'insémination était le <?= $dateNextInsemin ?>.
											<i class="fad fa-question-circle text-primary" data-toggle="tooltip" data-placement="top" title="3 mois après le dernier vêlage"></i>
										<?php
																		}
										?></div>
								</div>
							</div>
						</div>
					</div>
				</div>


			<?php } // END IF PREGNANT NUMBER > 1
			?>

			<?php if ($result['ispregnant']) { ?>
				<div class="col-xl-6 col-md-6 mb-4">
					<div class="card border-left-<?= $color ?> shadow h-100 py-2">
						<div class="card-body">
							<div class="row no-gutters align-items-center">
								<div class="col mr-5">

									<div class="row mb-1 gestInfo">
										<div class="col-sm-2 align-middle">
											<div class="icon-circle bg-<?= $color ?>"><i class="fas fa-baby-carriage text-white"></i></div>
										</div>
										<div class="col-sm-10 align-middle">
											<p class="mb-1 text-gray-800 h6">En gestation depuis le <span class="font-weight-bold"><?= $result['pregnant_since']; ?></span></p>
										</div>
									</div>


									<div class="row no-gutters align-items-center mt-4">
										<div class="col-auto">
											<div class="mb-0 mr-3 font-weight-bold text-gray-800"><?= $pregnantdays . '/283 jours' ?></div>
										</div>
										<div class="col-12">
											<div class="progress progress-sm">
												<div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width:<?= $pregnantpercent ?>%" aria-valuenow="<?= $pregnantpercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										</div>
										<?php
										$daysToEndOfPregnant = (283 - $pregnantdays);
										if ($daysToEndOfPregnant >= 0) {
										?>
											<p class="mt-4 pr-1">Vêlage prévu dans environ <?= $daysToEndOfPregnant ?> jours, soit le <?= futureDateDay($result['pregnant_since'], 283) ?></p>
										<?php } else { ?>
											<p class="mt-4 pr-1">Vêlage prévu depuis environ <?= abs($daysToEndOfPregnant) ?> jours </p>
										<?php
										}
										if (abs($daysToEndOfPregnant) >= 31) {
											$monthToEndOfPregnant = round(abs($daysToEndOfPregnant) / 30.5, 1);
										?>
											<span class="font-weight-lighter"><em> (~<?= $monthToEndOfPregnant ?> mois)</em></span>
										<?php } ?>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div> <!-- /.row -->

		<!-- INFORMATIONS GENERALES -->
		<div class="row">
			<!-- Area Chart -->
			<div class="col-12">
				<div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
					</div>
					<!-- Card Body -->
					<div class="card-body">
						<form method="post" action="" id="cowInfos" class="enableSubmitOnChange noEnterKey">
							<p class="mb-4 h5">Description</p>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="cow_id">Numéro d'identification <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="cow_id" name="cow_id" value="<?= $result['id']; ?>">
								</div>
								<div class="form-group col-md-6">
									<label for="name">Nom <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="name" name="name" style="text-transform: capitalize;" value="<?= $result['name']; ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="birthdate">Date de naissance <span class="text-danger">*</span></label>
									<div class="input-group date" data-provide="datepicker">
										<input type="text" class="form-control" placeholder="jj/mm/aaaa" id="birthdate" name="birthdate" onchange="noPregnant();" value="<?= $result['birth_date']; ?>">
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-th"></span>
										</div>
									</div>
								</div>
								<div class="form-group col-md-6">
									<label for="gender">Genre <span class="text-danger">*</span></label>
									<select class="form-control" id="gender" name="gender" onchange="noPregnant();">
										<option></option>
										<option value="femelle" <?php if ($result['gender'] == "femelle") {
																	echo "selected";
																} ?>>Femelle</option>
										<option value="male" <?php if ($result['gender'] == "male") {
																	echo "selected";
																} ?>>Male</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="type">Race <span class="text-danger">*</span></label>
									<select class="form-control selectpicker" data-live-search="true" data-style="btn-select" id="race" name="race">
										<option></option>
										<optgroup label="Races laitières">
											<option value="Abondance" <?php if ($result['race'] == "Abondance") {
																			echo "selected";
																		} ?>>Abondance</option>
											<option value="Bordelaise" <?php if ($result['race'] == "Bordelaise") {
																			echo "selected";
																		} ?>>Bordelaise</option>
											<option value="Brune" <?php if ($result['race'] == "Brune") {
																		echo "selected";
																	} ?>>Brune</option>
											<option value="Froment du Léon" <?php if ($result['race'] == "Froment du Léon") {
																				echo "selected";
																			} ?>>Froment du Léon</option>
											<option value="Jersiaise" <?php if ($result['race'] == "Jersiaise") {
																			echo "selected";
																		} ?>>Jersiaise</option>
											<option value="Pie rouge des plaines" <?php if ($result['race'] == "Pie rouge des plaines") {
																						echo "selected";
																					} ?>>Pie rouge des plaines</option>
											<option value="Prim'Holstein" <?php if ($result['race'] == "Prim'Holstein") {
																				echo "selected";
																			} ?>>Prim'Holstein</option>
											<option value="Rouge flamande" <?php if ($result['race'] == "Rouge flamande") {
																				echo "selected";
																			} ?>>Rouge flamande</option>
										</optgroup>
										<optgroup label="Races production viande">
											<option value="Bazadaise" <?php if ($result['race'] == "Bazadaise") {
																			echo "selected";
																		} ?>>Bazadaise</option>
											<option value="Blanc bleu" <?php if ($result['race'] == "Blanc bleu") {
																			echo "selected";
																		} ?>>Blanc bleu</option>
											<option value="Blonde d'Aquitaine" <?php if ($result['race'] == "Blonde d'Aquitaine") {
																					echo "selected";
																				} ?>>Blonde d'Aquitaine</option>
											<option value="Charolaise" <?php if ($result['race'] == "Charolaise") {
																			echo "selected";
																		} ?>>Charolaise</option>
											<option value="Corse" <?php if ($result['race'] == "Corse") {
																		echo "selected";
																	} ?>>Corse</option>
											<option value="Créole" <?php if ($result['race'] == "Créole") {
																		echo "selected";
																	} ?>>Créole</option>
											<option value="Gasconne" <?php if ($result['race'] == "Gasconne") {
																			echo "selected";
																		} ?>>Gasconne</option>
											<option value="Hereford" <?php if ($result['race'] == "Hereford") {
																			echo "selected";
																		} ?>>Hereford</option>
											<option value="Highland Cattle" <?php if ($result['race'] == "Highland Cattle") {
																				echo "selected";
																			} ?>>Highland Cattle</option>
											<option value="bazadaise" <?php if ($result['race'] == "INRA 95") {
																			echo "selected";
																		} ?>>INRA 95</option>
											<option value="Limousine" <?php if ($result['race'] == "limousine") {
																			echo "selected";
																		} ?>>Limousine</option>
											<option value="Mirandaise" <?php if ($result['race'] == "Mirandaise") {
																			echo "selected";
																		} ?>>Mirandaise</option>
											<option value="Parthenaise" <?php if ($result['race'] == "Parthenaise") {
																			echo "selected";
																		} ?>>Parthenaise</option>
											<option value="Rouge des prés" <?php if ($result['race'] == "Rouge des prés") {
																				echo "selected";
																			} ?>>Rouge des prés</option>
											<option value="Saosnoise" <?php if ($result['race'] == "Saosnoise") {
																			echo "selected";
																		} ?>>Saosnoise</option>
											<option value="Taureau de Camargue" <?php if ($result['race'] == "Taureau de Camargue") {
																					echo "selected";
																				} ?>>Taureau de Camargue</option>
										</optgroup>
										<optgroup label="Races mixtes">
											<option value="Armoricaine" <?php if ($result['race'] == "Armoricaine") {
																			echo "selected";
																		} ?>>Armoricaine</option>
											<option value="Aubrac" <?php if ($result['race'] == "Aubrac") {
																		echo "selected";
																	} ?>>Aubrac</option>
											<option value="Aure-et-saint-girons" <?php if ($result['race'] == "Aure-et-saint-girons") {
																						echo "selected";
																					} ?>>Aure-et-saint-girons</option>
											<option value="Béarnaise" <?php if ($result['race'] == "Béarnaise") {
																			echo "selected";
																		} ?>>Béarnaise</option>
											<option value="Bretonne pie noir" <?php if ($result['race'] == "Bretonne pie noir") {
																					echo "selected";
																				} ?>>Bretonne pie noir</option>
											<option value="Bleue du Nord" <?php if ($result['race'] == "Bleue du Nord") {
																				echo "selected";
																			} ?>>Bleue du Nord</option>
											<option value="Ferrandaise" <?php if ($result['race'] == "Ferrandaise") {
																			echo "selected";
																		} ?>>Ferrandaise</option>
											<option value="Lourdaise" <?php if ($result['race'] == "Lourdaise") {
																			echo "selected";
																		} ?>>Lourdaise</option>
											<option value="Maraîchine" <?php if ($result['race'] == "Maraîchine") {
																			echo "Maraîchine";
																		} ?>>Armoricaine</option>
											<option value="Montbéliarde" <?php if ($result['race'] == "Montbéliarde") {
																				echo "selected";
																			} ?>>Montbéliarde</option>
											<option value="Nantaise" <?php if ($result['race'] == "Nantaise") {
																			echo "selected";
																		} ?>>Nantaise</option>
											<option value="Normande" <?php if ($result['race'] == "Normande") {
																			echo "selected";
																		} ?>>Normande</option>
											<option value="Salers" <?php if ($result['race'] == "Salers") {
																		echo "selected";
																	} ?>>Salers</option>
											<option value="Simmental française" <?php if ($result['race'] == "Simmental française") {
																					echo "selected";
																				} ?>>Simmental française</option>
											<option value="Tarentaise (ou Tarine)" <?php if ($result['race'] == "Tarentaise (ou Tarine)") {
																						echo "selected";
																					} ?>>Tarentaise (ou Tarine)</option>
											<option value="Villard-de-lans" <?php if ($result['race'] == "Villard-de-lans") {
																				echo "selected";
																			} ?>>Villard-de-lans</option>
										</optgroup>
										<optgroup label="Autres">
											<option value="Brava" <?php if ($result['race'] == "Brava") {
																		echo "selected";
																	} ?>>Brava</option>
											<option value="Marine landaise" <?php if ($result['race'] == "Marine landaise") {
																				echo "selected";
																			} ?>>Marine landaise</option>
											<option value="Betizu" <?php if ($result['race'] == "Betizu") {
																		echo "selected";
																	} ?>>Betizu</option>
											<option value="Autre" <?php if ($result['race'] == "Autre") {
																		echo "selected";
																	} ?>>Autre</option>
										</optgroup>
									</select>
									<small id="" class="form-text text-muted">Choisir "Autre" si la race recherchée n'apparait pas.</small>
								</div>
								<div class="form-group col-md-6">
									<label for="note">Note</label>
									<div class="input-group">
										<textarea name="note" id="note" class="form-control" maxlength="500" cols="30" rows="3"><?= $result['note']; ?></textarea>
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-th"></span>
										</div>
									</div>
								</div>
							</div>
							<p class="h5 mt-3">
								Mère
								<?php if (!$result['mother_id'] == '') { ?>
									<a href="cow-single?id=<?= $result['mother_id']; ?>"><i class="fas fa-link h6"></i></a>
								<?php } ?>
							</p>
							<div class="form-row mb-3">
								<div class="form-group col-md-6">
									<label for="cow_id">Numéro de la mère</label>
									<input type="text" class="form-control" id="mother_id" name="mother_id" value="<?= $result['mother_id']; ?>" placeholder="Inconnu">
								</div>
							</div>

							<p class="h5">Enfant(s)</p>
							<div class="form-row">
								<div class="form-group col-md-12">
									<table class="table table-sm">

										<tbody>

											<?php
											// Children
											$childrenCowList = $database->prepare("SELECT id, name, birth_date FROM cows WHERE mother_id=$currentCowId AND owner_id = $owner_id AND isarchived = 0");
											$childrenCowList->execute();
											$childrenNumber = $childrenCowList->rowCount();

											if ($childrenNumber < 1) {
											?>
												<p>Aucun</p>
												<?php
											} else {
												while ($child = $childrenCowList->fetch()) {
												?>

													<tr>
														<th scope="row"><a href="/cow-single?id=<?= $child['id']; ?>"><?= $child['name']; ?></a></th>
														<td>Numéro <?= $child['id']; ?></td>
														<td>Né(e) le <?= $child['birth_date']; ?></td>
													</tr>

											<?php
												}
												$childrenCowList->closeCursor();
											}
											?>
										</tbody>
									</table>
								</div>
							</div>

							<input type="submit" name="updateCow" id="updateCow" value="Sauvegarder les changements" class="btn btn-primary">
						</form>
					</div>
				</div>
			</div>
		</div> <!-- /.row -->



		<?php

		$reponseGestationList = $database->prepare("SELECT * FROM gestations WHERE g_owner_id = $owner_id AND g_cow_index = $currentCowIndex ORDER BY g_id ASC");
		$reponseGestationList->execute();
		$GestNumber = $reponseGestationList->rowCount();

		?>


		<!-- GESTATION -->
		<div class="row <?php if (calculeType($result['birth_date']) == 'veau') {
							echo 'd-none';
						} ?>">
			<!-- Area Chart -->
			<div class="col-12">
				<div class="card shadow mb-4" id="gestations">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Gestations</h6>
						<div class="dropdown no-arrow">
						</div>
					</div>
					<!-- Card Body -->
					<div class="card-body">

						<?php if (isset($errorMessageGest)) { ?>
							<div class="alert alert-danger" role="alert">
								<?= $errorMessageGest // <?= shortcode for <?php echo 
								?>
							</div>
						<?php } ?>
						<?php if (isset($successMessageGest)) { ?>
							<div class="alert alert-success" role="alert">
								<?= $successMessageGest ?>
							</div>
						<?php } ?>


						<p><?= $result['name']; ?> a un total de <?= $GestNumber ?> géstations.</p>
						<div class="table-responsive">
							<table class="table table-bordered" id="gestTable" width="100%" cellspacing="0">

								<thead>
									<tr>
										<th>Début</th>
										<th>État</th>
										<th>Fin</th>
										<th>Note</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>

									<?php

									while ($gestData = $reponseGestationList->fetch()) {
									?>

										<tr class="rowGestList">
											<td>
												<div class="displayRead">
													<?= $gestData['g_start']; ?>
												</div>
												<div class="displayEdit input-group date" data-provide="datepicker">
													<input type="text" class="form-control g_start_edit" style="width:100%" value="" placeholder="Début" required>
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-th"></span>
													</div>
												</div>
												<div class="d-none">
													<input type="text" class="col-12 g_start_origin" value="<?= $gestData['g_start']; ?>">
												</div>
											</td>
											<td class="positionRelative hideOverflow">
												<div class="displayRead">
													<?php

													switch ($gestData['g_state']) {
														case 0:
															echo "En cours";
															echo '<div class="progressBarGest bg-' . $color . '" style="width:' . $pregnantpercent . '%;"></div>';
															$select0 = 'selected';
															break;
														case 1:
															echo "Terminée";
															$select1 = 'selected';
															break;
														case 2:
															echo "Avorté";
															$select2 = 'selected';
															break;
													}
													?>
												</div>
												<div class="displayEdit">
													<select class="form-control g_state_edit" required>
														<option value="0" <?= $select0 ?>>En cours</option>
														<option value="1" <?= $select1 ?>>Vêlage</option>
														<option value="2" <?= $select2 ?>>Avortement</option>
													</select>
												</div>
												<div class="d-none">
													<input type="text" class="col-12 g_state_origin" value="<?= $gestData['g_state']; ?>">
												</div>


											</td>
											<td>
												<div class="displayRead">
													<?php if (!empty($gestData['g_end'])) {
														echo $gestData['g_end'];
													} else { ?>
														Vêlage prévu le <?= futureDateDay($gestData['g_start'], 283) ?>
													<?php
													} ?>
												</div>
												<div class="displayEdit input-group date" data-provide="datepicker">
													<input type="text" class="form-control g_end_edit" value="" style="width:100%">
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-th"></span>
													</div>
												</div>
												<div class="d-none">
													<input type="text" class="col-12 g_end_origin" value="<?= $gestData['g_end']; ?>">
												</div>
											</td>
											<td>
												<div class="displayRead">
													<?= $gestData['g_note']; ?>
												</div>
												<div class="displayEdit">
													<input type="text" class="form-control g_note_edit" value="">
												</div>
												<div class="d-none">
													<input type="text" class="col-12 g_note_origin" value="<?= $gestData['g_note']; ?>">
												</div>
											</td>
											<td>

												<span data-toggle="tooltip" data-placement="top" title="Modifier" class="displayRead">
													<button class="btn btn-primary btn-sm editGestRow" id="<?= $gestData['g_id']; ?>">
														<i class="fas fa-pencil-alt"></i>
													</button>
												</span>

												<span data-toggle="tooltip" data-placement="top" title="Sauvegarder" class="displayEdit">
													<label for="updateGestRowSubmit" class="btn btn-success btn-sm mb-0 updateGestFormSubmit">
														<i class="fas fa-check"></i>
													</label>
												</span>

												<span data-toggle="tooltip" data-placement="top" title="Annuler" class="displayEdit">
													<button type="button" class="btn btn-warning btn-sm cancelGestEdit">
														<i class="fas fa-times"></i>
													</button>
												</span>

												<span data-toggle="tooltip" data-placement="top" title="Supprimer" class="displayEdit">
													<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteGestModal">
														<i class="fas fa-trash"></i>
													</button>
												</span>

											</td>
										</tr>

									<?php
									}
									$reponseGestationList->closeCursor();
									?>
								</tbody>
							</table>
						</div>



						<?php
						// BOUTON ADD GEST SI NON PREGNANT
						if (!$result['ispregnant']) {
						?>

							<div class="d-flex flex-row align-items-center justify-content-end mt-4 mb-2" id="displayGestForm">
								<div class="pr-2">Déclarer une gestation</div>
								<i class="fad fa-plus-circle fa-fw text-success" id="displayGestFormIcon"></i>
							</div>
							<form method="post" action="" id="addGest" class="enableSubmitOnChange">
								<div class="form-row">
									<div class="form-group input-group date col-md-3" data-provide="datepicker">
										<input type="text" class="form-control" id="g_start" name="g_start" value="" placeholder="Début" required>
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-th"></span>
										</div>
									</div>

									<div class="form-group col-md-3">
										<select class="form-control" id="g_state" name="g_state" onchange="gestationState();" required>
											<option value="0" selected>En cours</option>
											<option value="1">Vêlage</option>
											<option value="2">Avortement</option>
										</select>
									</div>

									<div class="form-group input-group date col-md-3" data-provide="datepicker">
										<input type="text" class="form-control" id="g_end" name="g_end" value="" placeholder="Fin" disabled>
										<div class="input-group-addon">
											<span class="glyphicon glyphicon-th"></span>
										</div>
									</div>

									<div class="form-group col-md-3">
										<textarea class="form-control" name="g_note" id="g_note" cols="30" rows="2" value="" maxlength="50" placeholder="Note"></textarea>
									</div>
								</div>
								<div class="modal-footer">
									<input type="submit" name="addGestSubmit" id="addGestSubmit" value="Ajouter" class="btn btn-success">
								</div>
							</form>

						<?php } //End if pregnant 
						?>


					</div>
				</div>
			</div>

			<form action="" method="POST" id="updateGestRow" name="updateGestRow" class="d-none">
				<input type="text" class="form-control" value="" id="inputGestId" name="inputGestId">
				<input type="text" class="form-control" value="" id="inputGestStart" name="inputGestStart">
				<input type="text" class="form-control" value="" id="inputGestState" name="inputGestState">
				<input type="text" class="form-control" value="" id="inputGestOriginState" name="inputGestOriginState">
				<input type="text" class="form-control" value="" id="inputGestEnd" name="inputGestEnd">
				<input type="text" class="form-control" value="" id="inputGestNote" name="inputGestNote">
				<input type="submit" id="updateGestRowSubmit" name="updateGestRowSubmit" value="Sauvegarder">
			</form>
		</div> <!-- /.row -->



		<div class="row">
			<!-- Area Chart -->
			<div class="col-12">
				<div class="card shadow mb-4">
					<!-- Card Header - Dropdown -->
					<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
						<div class="dropdown no-arrow">

						</div>
					</div>
					<!-- Card Body -->
					<div class="card-body">

						<div id="timeline"></div>

					</div> <!-- Card Body -->
				</div>
			</div>
		</div> <!-- ./row -->

	</div>
	<!-- /.container-fluid -->

	<!-- Archive Cow Modal-->
	<div class="modal fade" id="archiveCowModal" tabindex="-1" role="dialog" aria-labelledby="ArchiveCow" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-gray-800" id="">Supprimer</h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Voulez-vous vraiment supprimer cette vache ?</p>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
					<form action="" method="post">
						<input type="submit" name="archive" id="archive" value="Supprimer" class="btn btn-danger">
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Dead Cow Modal-->
	<div class="modal fade" id="deadCowModal" tabindex="-1" role="dialog" aria-labelledby="DeadCow" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-gray-800" id="">Déclarer mort<?= $eIfFemal ?></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<p>Désolé de l'apprendre... Quand cela est-il arrivé ?</p>
						<div class="form-group input-group date" data-provide="datepicker" data-orientation="bottom">
							<input type="text" class="form-control" placeholder="jj/mm/aaaa" id="deathDate" name="deathDate">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-th"></span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>


						<input type="submit" name="deadConfirm" id="deadConfirm" value="Confirmer" class="btn btn-warning">

					</div>
				</form>
			</div>
		</div>
	</div>



	<!-- Sold Cow Modal-->
	<div class="modal fade" id="soldCowModal" tabindex="-1" role="dialog" aria-labelledby="soldCow" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-gray-800" id="">Déclarer vendu<?= $eIfFemal ?></h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<p>Quand et à combien l'avez-vous vendu ?</p>
						<div class="form-group input-group date" data-provide="datepicker" data-orientation="bottom">
							<input type="text" class="form-control" placeholder="jj/mm/aaaa" id="saleDate" name="saleDate">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-th"></span>
							</div>
						</div>
						<input type="text" class="form-control" placeholder="Prix en euro" id="salePrice" name="salePrice">
					</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>


						<input type="submit" name="soldConfirm" id="soldConfirm" value="Confirmer" class="btn btn-success">

					</div>
				</form>
			</div>
		</div>
	</div>



	<!-- DeleteGestRow Modal-->
	<div class="modal fade" id="deleteGestModal" tabindex="-1" role="dialog" aria-labelledby="deleteGest" aria-hidden="true" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-gray-800" id="">Supprimer</h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Voulez-vous vraiment supprimer cette gestation ?</p>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
					<form action="" method="post">
						<input type="text" id="deletedNumber" name="deletedNumber" value="" class="d-none">
						<input type="text" id="deletedRowState" name="deletedRowState" value="" class="d-none">
						<input type="submit" name="deleteGest" id="deleteGest" value="Supprimer" class="btn btn-danger">
					</form>
				</div>
			</div>
		</div>
	</div>



	<?php include 'footer.php'; ?>


	<?php

	if (isset($_GET['e']) || isset($_GET['eg'])) {
		echo "<script>showSnackBar('Opération échouée.', 'danger');</script>";
	}
	if (isset($_GET['s'])) {
		echo "<script>showSnackBar('Opération réussie.', 'primary');</script>";
	}

	?>