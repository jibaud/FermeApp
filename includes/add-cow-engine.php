<?php

if (isset($_POST['add'])){
    $idnumber = htmlspecialchars($_POST['idnumber']);
    $name = htmlspecialchars($_POST['name']);
    $owner_id = $_SESSION['userID'];
    $gender = htmlspecialchars($_POST['gender']);
    $type = htmlspecialchars($_POST['type']);
    $birthdate = htmlspecialchars($_POST['birthdate']);
    $ispregnant = htmlspecialchars($_POST['ispregnant']);

    if (empty($_POST['motherid'])){
        $motherid = "";
    } else {
        $motherid = htmlspecialchars($_POST['motherid']);
    }

    if (empty($_POST['childrennumber'])){
        $childrennumber = 0;
    } else {
        $childrennumber = htmlspecialchars($_POST['childrennumber']);
    }

    if (empty($_POST['ispregnant'])){
        $ispregnant = 0;
    } else {
        $ispregnant = 1;
    }

    if (empty($_POST['pregnancynumber'])){
      $pregnancynumber = 0;
    } else {
      $pregnancynumber = htmlspecialchars($_POST['pregnancynumber']);
    }

    if (empty($_POST['pregnantsince'])){
        $pregnantsince = "";
    } else {
        $pregnantsince = htmlspecialchars($_POST['pregnantsince']);
    }

    $deathDate = "";
    $saleDate = "";
    $salePrice = 0;
    $isArchived = 0;
  
    date_default_timezone_set('Europe/Paris');
    $date = date('d/m/Y à H:i:s');

  
    if ((!empty($idnumber)) && (!empty($name)) && (!empty($gender)) && (!empty($type)) && (!empty($birthdate)) ) {
        if (strlen($name) <= 32) {
            if (is_numeric($pregnancynumber) && ($pregnancynumber >= 0)) {
                if (is_numeric($idnumber)) {
                    $database = getPDO();
                    $rowId = countDatabaseValue($database, 'cows', 'id', $idnumber);
                    if ($rowId == 0) {
                        $insertCow = $database->prepare("INSERT INTO cows(
                            id,
                            name,
                            owner_id,
                            gender,
                            type,
                            birth_date,
                            mother_id,
                            children_number,
                            ispregnant,
                            pregnancy_number,
                            pregnant_since,
                            death_date,
                            sale_date,
                            sale_price,
                            isarchived,
                            create_date
                            ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $insertCow->execute([
                            $idnumber,
                            $name,
                            $owner_id,
                            $gender,
                            $type,
                            $birthdate,
                            $motherid,
                            $childrennumber,
                            $ispregnant,
                            $pregnancynumber,
                            $pregnantsince,
                            $deathDate,
                            $saleDate,
                            $salePrice,
                            $isArchived,
                            $date
                        ]);
                        $successMessage = "Opération réussie.";
                        header('refresh:1;url=../cows-manager.php');
                    } else {
                        $errorMessage = 'Une vache existe déjà avec ce numéro.';
                    }
                } else {
                    $errorMessage = 'Le numéro d\'identification n\'est pas valide.';
                }
            } else {
                $errorMessage = 'Le champs nombre de grossesse n\'est pas valide.';
            }
        } else {
            $errorMessage = 'Le nom est trop long. 32 charactères maximum.';
        }
    } else {
        $errorMessage = 'Veuillez remplir tous les champs obligatoires.';
    }
  }

  ?>