<?php
// Appel la dernière gestation terminée
$lastGest = $database->prepare("SELECT * FROM gestations WHERE g_state = 1 AND g_owner_id = $owner_id AND g_cow_index = $currentCowIndex ORDER BY g_id DESC LIMIT 1");
$lastGest->execute();
$numberLastGest = $lastGest->rowCount();
$lastGestResult = $lastGest->fetch();

// Update cow
if (isset($_POST['updateCow'])) {
	$cow_id = htmlspecialchars($_POST['cow_id']);
	$name = ucfirst(htmlspecialchars($_POST['name']));
	$gender = htmlspecialchars($_POST['gender']);
	$race = htmlspecialchars($_POST['race']);
	$birthdate = htmlspecialchars($_POST['birthdate']);

	if (empty($_POST['mother_id'])) {
		$mother_id = "";
		$valideMotherId = true;
	} else {
		$mother_id = htmlspecialchars($_POST['mother_id']);
		if ($mother_id != $cow_id) {
			$valideMotherId = true;
			if (is_numeric($mother_id)) {
				$valideMotherId = true;
			} else {
				$valideMotherId = false;
			}
		} else {
			$valideMotherId = false;
		}
	}

	if (empty($_POST['note'])) {
		$note = "";
	} else {
		$note = ucfirst(htmlspecialchars($_POST['note']));
	}

	if ($cow_id == $result['id']) {
		$rowId == 0; // Si l'id est l'id actuel
	} else {
		$database = getPDO();
		$rowId = countDatabaseValue($database, 'cows', 'id', 'owner_id', $cow_id, $owner_id);
		// Sinon on vérifie que l'id n'est pas déjà utilisé par une autre vache.
	}

	if (isset($mother_id))

		if ((!empty($cow_id)) && (!empty($name)) && (!empty($gender)) && (!empty($race)) && (!empty($birthdate))) {
			if (strlen($name) <= 32) {
				if (is_numeric($cow_id) && $valideMotherId) {
					if ($rowId == 0) {

						// Update cow actuelle avec nouvelles infos
						$database = getPDO();
						try {
							$updateCow = $database->prepare("UPDATE cows SET id=?, name=?, birth_date=?, gender=?, race=?, mother_id=?, note=? WHERE id = $currentCowId AND owner_id = $owner_id");
							$updateCow->execute([
								$cow_id,
								$name,
								$birthdate,
								$gender,
								$race,
								$mother_id,
								$note
							]);
						} catch (Exception $e) {
							echo " Error ! " . $e->getMessage();
						}


						// Update des enfants de la cow actuelle pour changer l'ID de leur mère
						// seulement si on à changé l'id
						if ($currentCowId != $cow_id) {
							try {
								$database = getPDO();
								$updateChildren = $database->prepare("UPDATE cows SET mother_id=? WHERE mother_id = $currentCowId AND owner_id = $owner_id");
								$updateChildren->execute([$cow_id]);
							} catch (Exception $e) {
								echo " Error ! " . $e->getMessage();
							}
						}

						$successMessage = "Changements sauvegardés.";
						header('Location: /cow-single?id=' . $currentCowId . '&s=1#');
					} else {
						$errorMessage = 'Une vache existe déjà avec ce numéro.';
						header('Location: /cow-single?id=' . $currentCowId . '&e=1#');
					}
				} else {
					$errorMessage = 'Le numéro d\'identification n\'est pas valide.';
					header('Location: /cow-single?id=' . $currentCowId . '&e=2#');
				}
			} else {
				$errorMessage = 'Le nom est trop long. 32 charactères maximum.';
				header('Location: /cow-single?id=' . $currentCowId . '&e=3#');
			}
		} else {
			$errorMessage = 'Veuillez remplir tous les champs obligatoires.';
			header('Location: /cow-single?id=' . $currentCowId . '&e=4#');
		}
}

// Update un GestRow
if (isset($_POST['updateGestRowSubmit'])) {
	$updateNumber = htmlspecialchars($_POST['inputGestId']);
	$updateStart = htmlspecialchars($_POST['inputGestStart']);
	$updateState = htmlspecialchars($_POST['inputGestState']);
	$originState = htmlspecialchars($_POST['inputGestOriginState']);

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

	if ($result['ispregnant'] == 1) { // Si la vache est enceinte
		if ($updateState == 0) { // Et qu'on veut sauvegarder en cours
			if ($originState == 0) { // On vérifie que c'était déjà le row en cours
				$valide = true; // Dans ce cas OK
			} else {
				$valide = false; // Sinon pas OK
			}
		} else { // Si on ne veut pas sauvegarder en cours
			$valide = true; // OK
		}
	} else { // Si la vache n'est pas enceinte 
		$valide = true; // OK
	}

	if ($valide) {
		if ((!empty($updateStart))) {
			if ($valideEmptyEnd) {
				if (compareDate($updateEnd, $updateStart) || $updateState == 0) {
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
							header('Location: /cow-single?id=' . $currentCowId . '&s=1#gestations');
						} else {
							$errorMessageGest = 'Champs état non valide';
							header('Location: /cow-single?id=' . $currentCowId . '&eg=1#gestations');
						}
					} else {
						$errorMessageGest = 'Le champs note est trop long. 50 charactères maximum.';
						header('Location: /cow-single?id=' . $currentCowId . '&eg=2#gestations');
					}
				} else {
					$errorMessageGest = 'La date de fin ne peut pas être plus ancienne que celle du début.';
					header('Location: /cow-single?id=' . $currentCowId . '&eg=6#gestations');
				}
			} else {
				$errorMessageGest = 'Une date de fin de gestation est nécéssaire.';
				header('Location: /cow-single?id=' . $currentCowId . '&eg=3#gestations');
			}
		} else {
			$errorMessageGest = 'Une date de début de gestation est nécéssaire.';
			header('Location: /cow-single?id=' . $currentCowId . '&eg=4#gestations');
		}
	} else {
		$errorMessageGest = 'Cet vache à déjà une gestation en cours.';
		header('Location: /cow-single?id=' . $currentCowId . '&eg=5#gestations');
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
				if (compareDate($gEnd, $gStart) || $gState == 0) {
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

							header('Location: /cow-single?id=' . $currentCowId . '&s=1#gestations');
						} else {
							$errorMessageGest = 'Champs état non valide';
							header('Location: /cow-single?id=' . $currentCowId . '&eg=1#gestations');
						}
					} else {
						$errorMessageGest = 'Le champs note est trop long. 50 charactères maximum.';
						header('Location: /cow-single?id=' . $currentCowId . '&eg=2#gestations');
					}
				} else {
					$errorMessageGest = 'La date de fin ne peut pas être plus ancienne que celle du début.';
					header('Location: /cow-single?id=' . $currentCowId . '&eg=6#gestations');
				}
			} else {
				$errorMessageGest = 'Une date de fin de gestation est nécéssaire.';
				header('Location: /cow-single?id=' . $currentCowId . '&eg=3#gestations');
			}
		} else {
			$errorMessageGest = 'Une date de début de gestation est nécéssaire.';
			header('Location: /cow-single?id=' . $currentCowId . '&eg=4#gestations');
		}
	} else {
		$errorMessageGest = 'Cet vache à déjà une gestation en cours.';
		header('Location: /cow-single?id=' . $currentCowId . '&eg=5#gestations');
	}
}



// Supprimer une bête
if (isset($_POST['archive'])) {
	$database = getPDO();
	$archiveCow = $database->prepare("UPDATE cows SET isarchived = 1 WHERE id = $currentCowId AND owner_id = $owner_id");
	$archiveCow->execute();

	header('Location: /cows-manager');
}


// Déclarer une bête morte
if (isset($_POST['deadConfirm'])) {
	$deathDate = htmlspecialchars($_POST['deathDate']);

	if (!empty($deathDate)) {
		$deadCow = $database->prepare("UPDATE cows SET death_date = '$deathDate' WHERE id = $currentCowId AND owner_id = $owner_id");
		$deadCow->execute();

		header('Location: /cows-manager');
	} else {
		header('Location: /cow-single?id=' . $currentCowId . '&e=5');
	}
}

// Déclarer une bête vendue
if (isset($_POST['soldConfirm'])) {
	$saleDate = htmlspecialchars($_POST['saleDate']);
	$salePrice = htmlspecialchars($_POST['salePrice']);
	$salePrice = str_replace(',', '.', $salePrice);

	if (is_numeric($salePrice)) {
		$priceValide = true;
	} else {
		$priceValide = false;
	}

	if (!empty($saleDate) && !empty($salePrice)) {
		if ($priceValide) {
			$soldCow = $database->prepare("UPDATE cows SET sale_date = '$saleDate', sale_price = '$salePrice' WHERE id = $currentCowId AND owner_id = $owner_id");
			$soldCow->execute();

			header('Location: /cows-manager');
		} else {
			header('Location: /cow-single?id=' . $currentCowId . '&e=7');
		}
	} else {
		header('Location: /cow-single?id=' . $currentCowId . '&e=6');
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
	header('Location: /cow-single?id=' . $currentCowId . '&s=1#gestations');
}
?>