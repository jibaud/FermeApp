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

<form method="post" action="" id="addCowForm" class="noEnterKey">
    <p>
        <h5>Informations générales</h5>
    </p>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cow_id">Numéro d'identification <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">FR</span>
                </div>
                <input type="text" class="form-control" autofocus id="cow_id" name="cow_id" <?php if (isset($cow_id)) { ?>value="<?= $cow_id ?>" <?php } ?> aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="name">Nom <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" style="text-transform: capitalize;" <?php if (isset($name)) { ?>value="<?= $name ?>" <?php } ?>>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="birthdate">Date de naissance <span class="text-danger">*</span></label>
            <div class="input-group date" data-provide="datepicker">
                <input type="text" class="form-control" placeholder="jj/mm/aaaa" id="birthdate" name="birthdate" <?php if (isset($birthdate)) { ?>value="<?= $birthdate ?>" <?php } ?>>
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="gender">Genre <span class="text-danger">*</span></label>
            <select class="form-control" id="gender" name="gender">
                <option></option>
                <option value="femelle" <?php if (isset($gender) && $gender == "femelle") {
                                            echo "selected";
                                        } ?>>Femelle</option>
                <option value="male" <?php if (isset($gender) && $gender == "male") {
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
                    <option value="Abondance" <?php if (isset($race) && $race == "Abondance") {
                                                    echo "selected";
                                                } ?>>Abondance</option>
                    <option value="Bordelaise" <?php if (isset($race) && $race == "Bordelaise") {
                                                    echo "selected";
                                                } ?>>Bordelaise</option>
                    <option value="Brune" <?php if (isset($race) && $race == "Brune") {
                                                echo "selected";
                                            } ?>>Brune</option>
                    <option value="Froment du Léon" <?php if (isset($race) && $race == "Froment du Léon") {
                                                        echo "selected";
                                                    } ?>>Froment du Léon</option>
                    <option value="Jersiaise" <?php if (isset($race) && $race == "Jersiaise") {
                                                    echo "selected";
                                                } ?>>Jersiaise</option>
                    <option value="Pie rouge des plaines" <?php if (isset($race) && $race == "Pie rouge des plaines") {
                                                                echo "selected";
                                                            } ?>>Pie rouge des plaines</option>
                    <option value="Prim'Holstein" <?php if (isset($race) && $race == "Prim'Holstein") {
                                                        echo "selected";
                                                    } ?>>Prim'Holstein</option>
                    <option value="Rouge flamande" <?php if (isset($race) && $race == "Rouge flamande") {
                                                        echo "selected";
                                                    } ?>>Rouge flamande</option>
                </optgroup>
                <optgroup label="Races production viande">
                    <option value="Bazadaise" <?php if (isset($race) && $race == "Bazadaise") {
                                                    echo "selected";
                                                } ?>>Bazadaise</option>
                    <option value="Blanc bleu" <?php if (isset($race) && $race == "Blanc bleu") {
                                                    echo "selected";
                                                } ?>>Blanc bleu</option>
                    <option value="Blonde d'Aquitaine" <?php if (isset($race) && $race == "Blonde d'Aquitaine") {
                                                            echo "selected";
                                                        } ?>>Blonde d'Aquitaine</option>
                    <option value="Charolaise" <?php if (isset($race) && $race == "Charolaise") {
                                                    echo "selected";
                                                } ?>>Charolaise</option>
                    <option value="Corse" <?php if (isset($race) && $race == "Corse") {
                                                echo "selected";
                                            } ?>>Corse</option>
                    <option value="Créole" <?php if (isset($race) && $race == "Créole") {
                                                echo "selected";
                                            } ?>>Créole</option>
                    <option value="Gasconne" <?php if (isset($race) && $race == "Gasconne") {
                                                    echo "selected";
                                                } ?>>Gasconne</option>
                    <option value="Hereford" <?php if (isset($race) && $race == "Hereford") {
                                                    echo "selected";
                                                } ?>>Hereford</option>
                    <option value="Highland Cattle" <?php if (isset($race) && $race == "Highland Cattle") {
                                                        echo "selected";
                                                    } ?>>Highland Cattle</option>
                    <option value="bazadaise" <?php if (isset($race) && $race == "INRA 95") {
                                                    echo "selected";
                                                } ?>>INRA 95</option>
                    <option value="Limousine" <?php if (isset($race) && $race == "limousine") {
                                                    echo "selected";
                                                } ?>>Limousine</option>
                    <option value="Mirandaise" <?php if (isset($race) && $race == "Mirandaise") {
                                                    echo "selected";
                                                } ?>>Mirandaise</option>
                    <option value="Parthenaise" <?php if (isset($race) && $race == "Parthenaise") {
                                                    echo "selected";
                                                } ?>>Parthenaise</option>
                    <option value="Rouge des prés" <?php if (isset($race) && $race == "Rouge des prés") {
                                                        echo "selected";
                                                    } ?>>Rouge des prés</option>
                    <option value="Saosnoise" <?php if (isset($race) && $race == "Saosnoise") {
                                                    echo "selected";
                                                } ?>>Saosnoise</option>
                    <option value="Taureau de Camargue" <?php if (isset($race) && $race == "Taureau de Camargue") {
                                                            echo "selected";
                                                        } ?>>Taureau de Camargue</option>
                </optgroup>
                <optgroup label="Races mixtes">
                    <option value="Armoricaine" <?php if (isset($race) && $race == "Armoricaine") {
                                                    echo "selected";
                                                } ?>>Armoricaine</option>
                    <option value="Aubrac" <?php if (isset($race) && $race == "Aubrac") {
                                                echo "selected";
                                            } ?>>Aubrac</option>
                    <option value="Aure-et-saint-girons" <?php if (isset($race) && $race == "Aure-et-saint-girons") {
                                                                echo "selected";
                                                            } ?>>Aure-et-saint-girons</option>
                    <option value="Béarnaise" <?php if (isset($race) && $race == "Béarnaise") {
                                                    echo "selected";
                                                } ?>>Béarnaise</option>
                    <option value="Bretonne pie noir" <?php if (isset($race) && $race == "Bretonne pie noir") {
                                                            echo "selected";
                                                        } ?>>Bretonne pie noir</option>
                    <option value="Bleue du Nord" <?php if (isset($race) && $race == "Bleue du Nord") {
                                                        echo "selected";
                                                    } ?>>Bleue du Nord</option>
                    <option value="Ferrandaise" <?php if (isset($race) && $race == "Ferrandaise") {
                                                    echo "selected";
                                                } ?>>Ferrandaise</option>
                    <option value="Lourdaise" <?php if (isset($race) && $race == "Lourdaise") {
                                                    echo "selected";
                                                } ?>>Lourdaise</option>
                    <option value="Maraîchine" <?php if (isset($race) && $race == "Maraîchine") {
                                                    echo "Maraîchine";
                                                } ?>>Armoricaine</option>
                    <option value="Montbéliarde" <?php if (isset($race) && $race == "Montbéliarde") {
                                                        echo "selected";
                                                    } ?>>Montbéliarde</option>
                    <option value="Nantaise" <?php if (isset($race) && $race == "Nantaise") {
                                                    echo "selected";
                                                } ?>>Nantaise</option>
                    <option value="Normande" <?php if (isset($race) && $race == "Normande") {
                                                    echo "selected";
                                                } ?>>Normande</option>
                    <option value="Salers" <?php if (isset($race) && $race == "Salers") {
                                                echo "selected";
                                            } ?>>Salers</option>
                    <option value="Simmental française" <?php if (isset($race) && $race == "Simmental française") {
                                                            echo "selected";
                                                        } ?>>Simmental française</option>
                    <option value="Tarentaise (ou Tarine)" <?php if (isset($race) && $race == "Tarentaise (ou Tarine)") {
                                                                echo "selected";
                                                            } ?>>Tarentaise (ou Tarine)</option>
                    <option value="Villard-de-lans" <?php if (isset($race) && $race == "Villard-de-lans") {
                                                        echo "selected";
                                                    } ?>>Villard-de-lans</option>
                </optgroup>
                <optgroup label="Autres">
                    <option value="Brava" <?php if (isset($race) && $race == "Brava") {
                                                echo "selected";
                                            } ?>>Brava</option>
                    <option value="Marine landaise" <?php if (isset($race) && $race == "Marine landaise") {
                                                        echo "selected";
                                                    } ?>>Marine landaise</option>
                    <option value="Betizu" <?php if (isset($race) && $race == "Betizu") {
                                                echo "selected";
                                            } ?>>Betizu</option>
                    <option value="Autre" <?php if (isset($race) && $race == "Autre") {
                                                echo "selected";
                                            } ?>>Autre</option>
                </optgroup>
            </select>
            <small id="" class="form-text text-muted">Choisir "Autre" si la race recherchée n'apparait pas.</small>
        </div>
    </div>

    <hr>
    <p>
        <h5>Généalogie</h5>
    </p>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="mother_id">Numéro de la mère</label>
            <input type="text" class="form-control" id="mother_id" name="mother_id" <?php if (isset($mother_id)) { ?>value="<?= $mother_id ?>" <?php } ?>>
            <small id="" class="form-text text-muted">Laisser vide si inconnu</small>
        </div>
    </div>


    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
        <input type="submit" name="add" value="Valider" class="btn btn-primary" onclick="isPregnantChecked();">
    </div>

</form>