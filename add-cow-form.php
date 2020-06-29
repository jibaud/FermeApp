<?php if (isset($errorMessage)) {?>
              <div class="alert alert-danger" role="alert">
                <?= $errorMessage // <?= shortcode for <?php echo ?>
              </div>
<?php } ?>
<?php if (isset($successMessage)) {?>
              <div class="alert alert-success" role="alert">
                <?= $successMessage ?>
              </div>
<?php } ?>

<form method="post" action="">
<p>
    <h5>Informations générales</h5>
</p>
    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="idnumber">Numéro d'identification <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="idnumber" name="idnumber" <?php if (isset($idnumber)) { ?>value="<?= $idnumber ?>" <?php } ?>>
        </div>
        <div class="form-group col-md-6">
        <label for="name">Nom <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="name" name="name" style="text-transform: capitalize;" <?php if (isset($name)) { ?>value="<?= $name ?>" <?php } ?>>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="gender">Genre <span class="text-danger">*</span></label>
        <select class="form-control" id="gender" name="gender" onchange="noPregnant();">
            <option></option>
            <option value="femelle" <?php if (isset($gender) && $gender == "femelle") { echo "selected"; } ?>>Femelle</option>
            <option value="male" <?php if (isset($gender) && $gender == "male") { echo "selected"; } ?>>Male</option>
        </select>
        </div>
        <div class="form-group col-md-6">
        <label for="type">Type <span class="text-danger">*</span></label>
        <select class="form-control" id="type" name="type" onchange="noPregnant();">
            <option value="veau" <?php if (isset($type) && $type == "veau") { echo "selected"; } ?>>Veau</option>
            <option value="genisse" <?php if (isset($type) && $type == "genisse") { echo "selected"; } ?>>Génisse</option>
            <option value="vache" <?php if (isset($type) && $type == "vache") { echo "selected"; } ?>>Vache</option>
        </select>
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
    </div>
    <hr>
    <p>
        <h5>Grossesse</h5>
    </p>
    <div class="form-row">
        <div class="form-group col-md-6">
        <div class="form-check">
            <input class="form-check-input hiddenifmale" type="checkbox" id="ispregnant" name="ispregnant" onchange="isPregnantChecked();" <?php if ($ispregnant) {echo "checked";} ?>>
            <label class="form-check-label" for="ispregnant">
            Enceinte
            </label>
        </div>
        </div>
        <div class="form-group col-md-6">
        <label for="pregnantsince">Depuis le</label>
            <div class="input-group date" data-provide="datepicker">
            <input type="text" class="form-control hiddenifmale" placeholder="jj/mm/aaaa" id="pregnantsince" name="pregnantsince" <?php if (isset($pregnantsince)) { ?>value="<?= $pregnantsince ?>" <?php } ?>>
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="pregnancynumber">Nombre de grossesses</label>
        <input type="text" class="form-control hiddenifmale" id="pregnancynumber" name="pregnancynumber" <?php if (isset($pregnancynumber)) { ?>value="<?= $pregnancynumber ?>" <?php } ?>>
        <small id="" class="form-text text-muted">Laisser vide si inconnu</small>
        </div>
    </div>
    <hr>
    <p>
        <h5>Généalogie</h5>
    </p>
    <div class="form-row">
        <div class="form-group col-md-6">
        <label for="idnumber">Numéro de la mère</label>
        <input type="text" class="form-control" id="motherid" name="motherid" <?php if (isset($motherid)) { ?>value="<?= $motherid ?>" <?php } ?>>
        </div>
        <div class="form-group col-md-6">
        <label for="name">Nombre d'enfants</label>
        <input type="text" class="form-control" id="childrennumber" name="childrennumber" <?php if (isset($childrennumber)) { ?>value="<?= $childrennumber ?>" <?php } ?>>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
        <input type="submit" name="add" value="Valider" class="btn btn-success" onclick="isPregnantChecked();">
    </div>
</form>