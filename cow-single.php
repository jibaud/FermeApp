<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';

include 'header.php';

if (!isset($_GET['id'])) {
	header('Location:cows-manager');
}


// Usefull datas
$database = getPDO();
$owner_id = $_SESSION['userID'];
$currentCowId = $_GET['id'];


// Appel les données de la vache actuelle
$reponseCow = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND id = ?");
$reponseCow->execute([$owner_id, $currentCowId]);
$result = $reponseCow->fetch();

// Update cow
if (isset($_POST['updateCow'])) {
	$cow_id = htmlspecialchars($_POST['cow_id']);
	$name = ucfirst(htmlspecialchars($_POST['name']));
	$gender = htmlspecialchars($_POST['gender']);
	$race = htmlspecialchars($_POST['race']);
	$birthdate = htmlspecialchars($_POST['birthdate']);

	if (empty($_POST['mother_id'])) {
		$mother_id = "";
	} else {
		$mother_id = htmlspecialchars($_POST['mother_id']);
	}

	if ($cow_id == $result['id']) {
		$rowId == 0; // Si l'id est l'id actuel
	} else {
		$database = getPDO();
		$rowId = countDatabaseValue($database, 'cows', 'id', 'owner_id', $cow_id, $owner_id);
		// Sinon on vérifie que l'id n'est pas déjà utilisé par une autre vache.
	}

	if ((!empty($cow_id)) && (!empty($name)) && (!empty($gender)) && (!empty($race)) && (!empty($birthdate))) {
		if (strlen($name) <= 32) {
			if (is_numeric($cow_id)) {
				if ($rowId == 0) {
					if ($cow_id != $currentCowId) {
						try {
							$database = getPDO();
							$updateChildren = $database->prepare("UPDATE cows SET mother_id=? WHERE mother_id = $currentCowId");
							$updateChildren->execute([$cow_id]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}
					}
					$database = getPDO();
					try {
						$updateCow = $database->prepare("UPDATE cows SET id=?, name=?, birth_date=?, gender=?, race=?, mother_id=? WHERE id = $currentCowId");
						$updateCow->execute([
							$cow_id,
							$name,
							$birthdate,
							$gender,
							$race,
							$mother_id
						]);
					} catch (Exception $e) {
						echo " Error ! " . $e->getMessage();
					}

					$successMessage = "Changements sauvegardés.";
					header('refresh:1;url=/cow-single?id=' . $cow_id);
				} else {
					$errorMessage = 'Une vache existe déjà avec ce numéro.';
				}
			} else {
				$errorMessage = 'Le numéro d\'identification n\'est pas valide.';
			}
		} else {
			$errorMessage = 'Le nom est trop long. 32 charactères maximum.';
		}
	} else {
		$errorMessage = 'Veuillez remplir tous les champs obligatoires.';
	}
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

$pageTitle = $result['name'];

?>

<body id="page-top">

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
				<a href="#" id="" class="btn btn-sm btn-primary btn-icon-split">
					<span class="icon text-white">
						<i class="fas fa-ellipsis-v"></i>
					</span>
					<span class="text">Action</span>
				</a>
			</div>

			<div class="row mb-4">
				<div class="col-12">
					<p class="h5 text-gray-800"><?= $result['birth_date']; ?><span class="mb-0 text-gray-700 h6"> (<?= calculeAge($result['birth_date'], 'full') ?>)</span></p>
					<p class="h5 text-gray-800"><span class="capitalize"><?= $type = calculeType($result['birth_date']); ?></span> <?= $result['gender']; ?> de race <?= $result['race']; ?></p>
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

			<?php if ($result['ispregnant']) { ?>
				<div class="row">
					<div class="col-xl-6 col-md-6 mb-4">
						<div class="card border-left-<?= $color ?> shadow h-100 py-2">
							<div class="card-body">
								<div class="row no-gutters align-items-center">
									<div class="col mr-5">
										<p class="mb-1 text-gray-800 h5">En gestation depuis le <span class="font-weight-bold"><?= $result['pregnant_since']; ?></span></p>

										<div class="row no-gutters align-items-center mt-3">
											<div class="col-auto">
												<div class="mb-0 mr-3 font-weight-bold text-gray-800"><?= $pregnantdays . '/283 jours' ?></div>
											</div>
											<div class="col-12">
												<div class="progress progress-sm">
													<div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width:<?= $pregnantpercent ?>%" aria-valuenow="<?= $pregnantpercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
											</div>
											<p class="mt-3">Velâge prévu dans environ <?= 283 - $pregnantdays ?> jours.</p>
										</div>

									</div>
									<div class="col-auto">
										<i class="fas fa-baby-carriage fa-4x text-gray-300"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- /.row -->
			<?php } ?>




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
							<form method="post" action="" id="cowInfos">
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


			<!-- GESTATION -->
			<div class="row">
				<!-- Area Chart -->
				<div class="col-12">
					<div class="card shadow mb-4">
						<!-- Card Header - Dropdown -->
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-primary">Gestation</h6>
							<div class="dropdown no-arrow">
								<a class="" href="#" role="button" id="dropdownMenuLink">
									<i class="fas fa-pencil-alt fa-fw text-primary"></i>
								</a>
							</div>
						</div>
						<!-- Card Body -->
						<div class="card-body">
							<form method="post" action="">
								<div class="form-row">

									<table class="table table-bordered" id="" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>Début</th>
												<th>Fin</th>
												<th>Statut</th>
												<th>Note</th>
											</tr>
										</thead>
										<tbody>

											<?php

											$owner_id = $_SESSION['userID'];
											$database = getPDO();
											$reponseCowList = $database->prepare("SELECT * FROM cows WHERE owner_id = $owner_id AND id = $currentCowId");
											$reponseCowList->execute();

											// On affiche chaque entrée une à une
											while ($donnees = $reponseCowList->fetch()) {
												$type = calculeType($donnees['birth_date']);
											?>
												<tr>
													<td><?= $donnees['pregnant_since']; ?></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
											<?php
											}

											$reponseCowList->closeCursor(); // Termine le traitement de la requête

											?>


										</tbody>
									</table>

									<div class="form-row">
										<div class="form-group col-md-6">

										</div>
									</div>
									<input type="submit" name="saveCowInfo" id="saveCowInfo" value="Sauvegarder les changements" class="btn btn-primary">
							</form>
						</div>
					</div>
				</div>
			</div> <!-- /.row -->


		</div>
		<!-- /.container-fluid -->
		<?php include 'footer.php'; ?>