<?php

//upload.php

if (isset($_POST["image"])) {
    $data = $_POST["image"];

    $image_array_1 = explode(";", $data);

    $image_array_2 = explode(",", $image_array_1[1]);

    $data = base64_decode($image_array_2[1]);

    $imageName = time() . '.png';
    $imagePath = '../img/profilepic/' . $imageName;

    file_put_contents($imagePath, $data);

    echo '<img src="' . $imagePath . '" class="img-thumbnail" />';
    echo '<input type="text" id="imageName" name="imageName" class="d-none" value="'.$imageName.'">';
    echo '<input type="submit" class="btn btn-primary" id="imageSubmit" name="imageSubmit" value="Sauvegarder">';

}
