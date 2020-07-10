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
$reponseCow = $database->prepare("SELECT * FROM cows WHERE owner_id = ? AND id = ? LIMIT 1"); // LIMIT 1 car il ne doit y avoir qu'un seul résultat de toute façon
$reponseCow->execute([$owner_id, $currentCowId]);
$result = $reponseCow->fetch();

$currentCowIndex = $result['cow_index'];

if ($result['pregnant_number'] > 0) {
	$type = 'vache';
} else {
	$type = calculeType($result['birth_date']);
}

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

						// Update des enfants de la cow actuelle pour changer l'ID de leur mère
						// seulement si on à changé l'id
						if ($currentCowId != $cow_id) {
							$database = getPDO();
							try {
								$updateCow = $database->prepare("UPDATE cows SET id=?, name=?, birth_date=?, gender=?, race=?, mother_id=? WHERE id = $currentCowId AND owner_id = $owner_id");
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
						}

						// Update cow actuelle avec nouvelles infos
						try {
							$database = getPDO();
							$updateChildren = $database->prepare("UPDATE cows SET mother_id=? WHERE mother_id = $currentCowId AND owner_id = $owner_id");
							$updateChildren->execute([$cow_id]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}
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

// Update un GestRow
if (isset($_POST['updateGestRowSubmit'])) {
	$updateNumber = htmlspecialchars($_POST['inputGestId']);
	$updateStart = htmlspecialchars($_POST['inputGestStart']);
	$updateState = htmlspecialchars($_POST['inputGestState']);

	$updateState = (int) $updateState;

	if (empty($_POST['inputGestEnd'])) {
		$updateEnd = "";
	} else {
		$updateEnd = htmlspecialchars($_POST['inputGestEnd']);
	}

	if (empty($_POST['inputGestNote'])) {
		$updateNote = "";
	} else {
		$updateNote = htmlspecialchars($_POST['inputGestNote']);
	}

	if ($updateState != 0) {

		$pregnantState = 0;

		if ($updateEnd != '') {
			$valideEmptyEnd = true;
		} else {
			$valideEmptyEnd = false;
		}
	} else {
		$pregnantState = 1;
		$valideEmptyEnd = true;
	}

	if ($result['ispregnant'] == 1) {
		if ($updateState == 0) {
			$valide2 = false;
		} else {
			$valide2 = true;
		}
	} else {
		$valide2 = true;
	}

	if ($valide2) {
		if ((!empty($updateStart))) {
			if ($valideEmptyEnd) {
				if (strlen($updateNote) <= 50) {
					if ($updateState == 0 || $updateState == 1 || $updateState == 2) {
						$database = getPDO();
						try {
							$updateGest = $database->prepare("UPDATE gestations SET g_start = ?, g_state = ?, g_end = ?, g_note = ? WHERE g_id = $updateNumber AND g_owner_id = $owner_id");
							$updateGest->execute([
								$updateStart,
								$updateState,
								$updateEnd,
								$updateNote
							]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}

						try {
							$changePregnantState = $database->prepare("UPDATE cows SET ispregnant = ?, pregnant_since = ? WHERE id = $currentCowId AND owner_id = $owner_id");
							$changePregnantState->execute([
								$pregnantState,
								$updateStart
							]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}


						$successMessageGest = "Opération réussie.";
						header('Location: ' . $_SERVER['REQUEST_URI'] . '#gestations');
					} else {
						$errorMessageGest = 'Champs état non valide';
					}
				} else {
					$errorMessageGest = 'Le champs note est trop long. 50 charactères maximum.';
				}
			} else {
				$errorMessageGest = 'Une date de fin de gestation est nécéssaire.';
				header('Location: /cow-single?id=' . $currentCowId . '&e=1#gestations');
			}
		} else {
			$errorMessageGest = 'Une date de début de gestation est nécéssaire.';
		}
	} else {
		$errorMessageGest = 'Cet vache à déjà une gestation en cours.';
	}
}


// Add new gestation
if (isset($_POST['addGestSubmit'])) {
	$gStart = htmlspecialchars($_POST['g_start']);
	$gState = htmlspecialchars($_POST['g_state']); // 0 En cours, 1 terminé, 2 avorté

	$gState = (int) $gState;

	if (empty($_POST['g_end'])) {
		$gEnd = "";
		$pregnantState = 1;
	} else {
		$gEnd = htmlspecialchars($_POST['g_end']);
		$pregnantState = 0;
	}

	if (empty($_POST['g_note'])) {
		$gNote = "";
	} else {
		$gNote = htmlspecialchars($_POST['g_note']);
	}

	if ($gState != 0) {
		if (!empty($gEnd)) {
			$valide = true;
		} else {
			$valide = false;
		}
	} else {
		$valide = true;
	}

	date_default_timezone_set('Europe/Paris');

	if ($result['ispregnant'] == 0) {
		if ((!empty($gStart))) {
			if ($valide) {
				if (strlen($gNote) <= 50) {
					if ($gState == 0 || $gState == 1 || $gState == 2) {
						try {
							$insertGest = $database->prepare("INSERT INTO gestations(
								g_cow_index,
								g_start,
								g_end,
								g_state,
								g_note,
								g_owner_id
								) VALUES(?, ?, ?, ?, ?, ?)");
							$insertGest->execute([
								$currentCowIndex,
								$gStart,
								$gEnd,
								$gState,
								$gNote,
								$owner_id
							]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}

						try {
							$changePregnantState = $database->prepare("UPDATE cows SET ispregnant='$pregnantState', pregnant_since='$gStart', pregnant_number=pregnant_number+1 WHERE id=$currentCowId AND owner_id=$owner_id");
							$changePregnantState->execute();
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}

						$successMessage = "Opération réussie.";
						header('Location: ' . $_SERVER['REQUEST_URI']);
					} else {
						$errorMessage = 'Champs select non valide';
					}
				} else {
					$errorMessage = 'Le champs note est trop long. 50 charactères maximum.';
				}
			} else {
				$errorMessage = 'Une date de fin de gestation est nécéssaire.';
			}
		} else {
			$errorMessage = 'Une date de début de gestation est nécéssaire.';
		}
	} else {
		$errorMessage = 'Cet vache à déjà une gestation en cours.';
	}
}



// Supprimer un GestRow
if (isset($_POST['deleteGest'])) {
	$deletedNumber = htmlspecialchars($_POST['deletedNumber']);
	$deletedRowState = htmlspecialchars($_POST['deletedRowState']);
	$owner_id = $_SESSION['userID'];

	if ($deletedRowState == 0) {
		$deleteIsPregnant = $database->prepare("UPDATE cows SET ispregnant='0', pregnant_since='', pregnant_number=pregnant_number-1 WHERE id=$currentCowId AND owner_id=$owner_id");
		$deleteIsPregnant->execute();
	} else {
		$deleteIsPregnant = $database->prepare("UPDATE cows SET pregnant_number=pregnant_number-1 WHERE id=$currentCowId AND owner_id=$owner_id");
		$deleteIsPregnant->execute();
	}

	$deleteGestRow = $database->prepare("DELETE FROM gestations WHERE g_id = $deletedNumber AND g_owner_id = $owner_id");
	$deleteGestRow->execute();
	$successMessage = "Opération réussie.";
	header('Location: ' . $_SERVER['REQUEST_URI']);
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
											<?php
											$daysToEndOfPregnant = (283 - $pregnantdays);
											if ($daysToEndOfPregnant >= 0) {
											?>
												<p class="mt-3 pr-1">Vêlge prévu dans environ <?= $daysToEndOfPregnant ?> jours </p>
											<?php } else { ?>
												<p class="mt-3 pr-1">Vêlage prévu depuis environ <?= abs($daysToEndOfPregnant) ?> jours </p>
											<?php
											}
											if (abs($daysToEndOfPregnant) >= 31) {
												$monthToEndOfPregnant = round(abs($daysToEndOfPregnant) / 30.5, 1);
											?>
												<span class="font-weight-lighter"><em> (~<?= $monthToEndOfPregnant ?> mois)</em></span>
											<?php } ?>
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
							<form method="post" action="" id="cowInfos" class="enableSubmitOnChange">
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



			<?php

			$reponseGestationList = $database->prepare("SELECT * FROM gestations WHERE g_owner_id = $owner_id AND g_cow_index = $currentCowIndex ORDER BY g_id ASC");
			$reponseGestationList->execute();

			?>

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

			<!-- GESTATION -->
			<div class="row">
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
													<div class="displayEdit date" data-provide="datepicker">
														<input type="text" class="form-control g_start_edit col-12" value="" placeholder="Début" required>
														<div class="input-group-addon">
															<span class="glyphicon glyphicon-th"></span>
														</div>
													</div>
													<div class="d-none">
														<input type="text" class="col-12 g_start_origin" value="<?= $gestData['g_start']; ?>">
													</div>
												</td>
												<td>
													<div class="displayRead">
														<?php

														switch ($gestData['g_state']) {
															case 0:
																echo "En cours";
																$select0 = 'selected';
																break;
															case 1:
																echo "Terminé";
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
														<?= $gestData['g_end']; ?>
													</div>
													<div class="displayEdit date" data-provide="datepicker">
														<input type="text" class="form-control g_end_edit" value="">
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
			</div> <!-- /.row -->

			<form action="" method="POST" id="updateGestRow" name="updateGestRow" class="d-none">
				<input type="text" class="form-control" value="" id="inputGestId" name="inputGestId">
				<input type="text" class="form-control" value="" id="inputGestStart" name="inputGestStart">
				<input type="text" class="form-control" value="" id="inputGestState" name="inputGestState">
				<input type="text" class="form-control" value="" id="inputGestEnd" name="inputGestEnd">
				<input type="text" class="form-control" value="" id="inputGestNote" name="inputGestNote">
				<input type="submit" id="updateGestRowSubmit" name="updateGestRowSubmit" value="Sauvegarder">
			</form>

		</div>
		<!-- /.container-fluid -->


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
							<input type="text" id="deletedNumber" name="deletedNumber" value="">
							<input type="text" id="deletedRowState" name="deletedRowState" value="">
							<input type="submit" name="deleteGest" id="deleteGest" value="Supprimer" class="btn btn-danger">
						</form>
					</div>
				</div>
			</div>
		</div>



		<?php include 'footer.php'; ?>

		<?php


		if (!isset($_GET['e']) and $_GET['e'] == '1') {
			
				?>
				<script>
					alert("Coucou");
				</script>
		<?php
		}
		?>