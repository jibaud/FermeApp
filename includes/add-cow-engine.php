<?php

if (isset($_POST['add'])){
    $cow_id = htmlspecialchars($_POST['cow_id']);
    $name = ucfirst(htmlspecialchars($_POST['name']));
    $owner_id = $_SESSION['userID'];
    $gender = htmlspecialchars($_POST['gender']);
    $race = htmlspecialchars($_POST['race']);
    $birthdate = htmlspecialchars($_POST['birthdate']);

    if (empty($_POST['mother_id'])) {
		$mother_id = "";
		$valideMotherId = true;
	} else {
		$mother_id = htmlspecialchars($_POST['mother_id']);
		if ($mother_id != $cow_id){
			$valideMotherId = true;
			if (is_numeric($mother_id)){
				$valideMotherId = true;
			} else {
				$valideMotherId = false;
			}
		} else {
			$valideMotherId = false;
		}
	}

    $note = "";
    $ispregnant = 0;
    $pregnantsince= "";
    $pregnantNumber = 0;
    $deathDate = "";
    $saleDate = "";
    $salePrice = null;
    $isArchived = 0;
  
    date_default_timezone_set('Europe/Paris');
    $date = date('d/m/Y à H:i:s');

  
    if ((!empty($cow_id)) && (!empty($name)) && (!empty($gender)) && (!empty($race)) && (!empty($birthdate)) ) {
        if (strlen($name) <= 32) {
                if (is_numeric($cow_id) && $valideMotherId) {
                    if (true){
                        $database = getPDO();
                        $rowId = countDatabaseValue($database, 'cows', 'id', 'owner_id', $cow_id, $owner_id);
                        if ($rowId == 0) {
                            $insertCow = $database->prepare("INSERT INTO cows(
                                id,
                                name,
                                owner_id,
                                gender,
                                race,
                                birth_date,
                                mother_id,
                                note,
                                ispregnant,
                                pregnant_since,
                                pregnant_number,
                                death_date,
                                sale_date,
                                sale_price,
                                isarchived,
                                create_date
                                ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $insertCow->execute([
                                $cow_id,
                                $name,
                                $owner_id,
                                $gender,
                                $race,
                                $birthdate,
                                $mother_id,
                                $note,
                                $ispregnant,
                                $pregnantsince,
                                $pregnantNumber,
                                $deathDate,
                                $saleDate,
                                $salePrice,
                                $isArchived,
                                $date
                            ]);
                            $successMessage = "Opération réussie.";
                            header('refresh:1;url=../cow-single?id='.$cow_id);
                        } else {
                            $errorMessage = 'Une vache existe déjà avec ce numéro.';
                        }
                    } else {
                        $errorMessage = 'Les dates doivent être au format jj/mm/aaaa. Exemple, le 2 décembre 1991 doit être écrit 02/12/1991.';
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
