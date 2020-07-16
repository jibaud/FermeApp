<?php

session_start();
include 'includes/database.php';
include 'includes/forbidden.php';
include 'includes/settings-engine.php';

$pageTitle = 'Profil';
include 'header.php';


$currentUserId = $_SESSION['userID'];

$database = getPDO();
$getUser = $database->prepare("SELECT * FROM users WHERE user_id = $currentUserId");
$getUser->execute();
$resultUser = $getUser->fetch();


// UPDATE PROFILE
if (isset($_POST['saveProfil'])) {

  $firstname = ucwords(htmlspecialchars($_POST['firstname']));
  $lastname = ucwords(htmlspecialchars($_POST['lastname']));
  $email = htmlspecialchars($_POST['email']);

  if (!empty($_POST['phone'])) {
    $phone = htmlspecialchars($_POST['phone']);
  } else {
    $phone = null;
  }
  if (!empty($_POST['address'])) {
    $address = ucfirst(htmlspecialchars($_POST['address']));
  } else {
    $address = "";
  }
  if (!empty($_POST['city'])) {
    $city = ucfirst(htmlspecialchars($_POST['city']));
  } else {
    $city = "";
  }
  if (!empty($_POST['zipcode'])) {
    $zipcode = htmlspecialchars($_POST['zipcode']);
  } else {
    $zipcode = "";
  }

  if ((!empty($firstname)) && (!empty($lastname)) && (!empty($email))) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

      if ($email == $resultUser['user_email']) {
        $rowEmail == 0; // Si l'email appartient à l'utilisateur.
      } else {
        $rowEmail = countDatabaseValue($database, 'users', 'user_email', 'user_email', $email, $email);
        // Sinon on vérifie que personne n'utilise cet email.
      }
      if ($rowEmail == 0) {
        //$updateMember = $database->prepare("UPDATE users SET user_firstname = $firstname, user_lastname = $lastname, user_email = $email, user_phone = $phone, user_address = $address, user_city = $city, user_zipcode = $zipcode WHERE user_id = $currentUserId");    
        //$updateMember->execute();
        $database = getPDO();
        try {
          $updateMember = $database->prepare("UPDATE users SET user_firstname=?, user_lastname=?, user_email=?, user_phone=?, user_address=?, user_city=?, user_zipcode=? WHERE user_id = $currentUserId");
          $updateMember->execute([
            $firstname,
            $lastname,
            $email,
            $phone,
            $address,
            $city,
            $zipcode
          ]);
          $_SESSION['userFirstname'] = $firstname;
          $_SESSION['userLastname'] = $lastname;
          $_SESSION['userEmail'] = $email;
          $_SESSION['userPhone'] = $Phone;
          $_SESSION['userAddress'] = $address;
          $_SESSION['userCity'] = $city;
          $_SESSION['userZipcode'] = $zipcode;
        } catch (Exception $e) {
          echo " Error ! " . $e->getMessage();
        }

        $successMessage = "Changements sauvegardés.";
        header('refresh:1');
      } else {
        $errorMessage = 'Cette adresse email est déjà utilisée.';
      }
    } else {
      $errorMessage = "L'adresse email n'est pas valide.";
    }
  } else {
    $errorMessage = 'Veuillez remplir tous les champs.';
  }
}

// CHANGE PASSWORD
if (isset($_POST['change_password'])) {

  $verif_password = $database->prepare('SELECT user_password FROM users WHERE user_id = ?');
  $verif_password->execute([$currentUserId]);
  $verif_password = $verif_password->fetch();
  $verif_password = $verif_password['user_password'];

  $currentPassword = htmlspecialchars($_POST['current_password']);
  $password = htmlspecialchars($_POST['new_password']);
  $passwordc = htmlspecialchars($_POST['new_passwordc']);

  if (!empty($currentPassword) && !empty($password) && !empty($passwordc)) {
    if ($password == $passwordc) {
      $currentPassword = sha1($currentPassword);
      if ($currentPassword == $verif_password) {
        $password = sha1($password);
        $change_pass = $database->prepare('UPDATE users SET user_password = ? WHERE user_id = ?');
        $change_pass->execute(array($password, $currentUserId));

        $successMessage = "Changements sauvegardés.";
        header('refresh:1;url=/profile');
      } else {
        $errorMessage = "Mot de passe actuel incorrect.";
      }
    } else {
      $errorMessage = "Vos mots de passes ne correspondent pas.";
    }
  } else {
    $errorMessage = "Veuillez remplir tous les champs.";
  }
}



//Update profile pic
if (isset($_POST['imageSubmit'])) {
  $imgName = htmlspecialchars($_POST['imageName']);
  $oldimgName = htmlspecialchars($_POST['imageNameToDelete']);
  try {
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $insertUserImg = $database->prepare("UPDATE users SET user_img=? WHERE user_id = $currentUserId");
    $insertUserImg->execute([$imgName]);
    unlink('img/profilepic/' . $oldimgName); // Supprimer l'image du serveur
    $_SESSION['userImg'] = $imgName;
    $successMessage = "Photo de profil changée.";
    header('Location: profile?s=1');
  } catch (Exception $e) {
    die('Error : ' . $e->getMessage());
  }
}

//Delete profile pic
if (isset($_POST['deleteImg'])) {
  $oldimgName = htmlspecialchars($_POST['imageNameToDelete']);

  try {
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $insertUserImg = $database->prepare("UPDATE users SET user_img='' WHERE user_id = $currentUserId");
    $insertUserImg->execute();
    unlink('img/profilepic/' . $oldimgName); // Supprimer l'image du serveur
    $_SESSION['userImg'] = $imgName;
    $successMessage = "Photo de profil supprimée.";
    header('Location: profile?s=1');
  } catch (Exception $e) {
    die('Error : ' . $e->getMessage());
  }
}

?>

<body id="page-top">
  <?php include 'includes/loader.php'; ?>

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php include 'sidebar.php'; ?>
    <?php include 'topbar.php'; ?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

      <!-- Page Heading -->
      <h1 class="h3 mb-4 text-gray-800"><?= $pageTitle ?></h1>

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





      <script>
        $(document).ready(function() {

          $image_crop = $('#image_demo').croppie({
            enableExif: true,
            viewport: {
              width: 300,
              height: 300,
              type: 'square' //circle
            },
            boundary: {
              width: 400,
              height: 400
            }
          });

          $('#upload_image').on('change', function() {
            var reader = new FileReader();
            reader.onload = function(event) {
              $image_crop.croppie('bind', {
                url: event.target.result
              }).then(function() {
                console.log('jQuery bind complete');
              });
            }
            reader.readAsDataURL(this.files[0]);
            $('#uploadimageModal').modal('show');
          });

          $('.crop_image').click(function(event) {
            $image_crop.croppie('result', {
              type: 'canvas',
              size: 'viewport'
            }).then(function(response) {
              $.ajax({
                url: "includes/upload.php",
                type: "POST",
                data: {
                  "image": response
                },
                success: function(data) {
                  $('#uploadimageModal').modal('hide');
                  $('#uploaded_image').html(data);
                }
              });
            })
          });

        });
      </script>





      <!-- Content Row -->
      <div class="row">

        <div class="col-xl-8 col-lg-7">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <form action="" method="post" id="profilInfos" class="enableSubmitOnChange">
                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label for="firstname">Prénom <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" class="form-control capitalize" id="firstname" name="firstname" value="<?php if (isset($firstname)) {
                                                                                                                                    echo $firstname;
                                                                                                                                  } else {
                                                                                                                                    echo $resultUser['user_firstname'];
                                                                                                                                  } ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="lastname">Nom <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" class="form-control capitalize" id="lastname" name="lastname" value="<?php if (isset($lastname)) {
                                                                                                                                  echo $lastname;
                                                                                                                                } else {
                                                                                                                                  echo $resultUser['user_lastname'];
                                                                                                                                } ?>" required>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label for="email">Adresse email <span class="text-danger">*</span></label>
                    <input type="email" autocomplete="off" class="form-control" id="email" name="email" value="<?php if (isset($email)) {
                                                                                                                  echo $email;
                                                                                                                } else {
                                                                                                                  echo $resultUser['user_email'];
                                                                                                                } ?>" required>
                    <small id="emailHelp" class="form-text text-muted">Votre identifiant de connexion</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="phone">Téléphone</label>
                    <input type="tel" autocomplete="off" class="form-control" id="phone" name="phone" value="<?php if (isset($phone)) {
                                                                                                                echo $phone;
                                                                                                              } else {
                                                                                                                echo $resultUser['user_phone'];
                                                                                                              } ?>">
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-6 mb-3">
                    <label for="address">Adresse</label>
                    <input type="text" autocomplete="off" class="form-control capitalize" id="address" name="address" value="<?php if (isset($address)) {
                                                                                                                                echo $address;
                                                                                                                              } else {
                                                                                                                                echo $resultUser['user_address'];
                                                                                                                              } ?>">
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="city">Ville</label>
                    <input type="text" autocomplete="off" class="form-control capitalize" id="city" name="city" value="<?php if (isset($city)) {
                                                                                                                          echo $city;
                                                                                                                        } else {
                                                                                                                          echo $resultUser['user_city'];
                                                                                                                        } ?>">
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="zipcode">Code Postal</label>
                    <input type="text" autocomplete="off" class="form-control" id="zipcode" name="zipcode" value="<?php if (isset($zipcode)) {
                                                                                                                    echo $zipcode;
                                                                                                                  } else {
                                                                                                                    echo $resultUser['user_zipcode'];
                                                                                                                  } ?>">
                  </div>
                </div>
                <hr>
                <input type="submit" name="saveProfil" id="saveProfil" value="Sauvegarder les changements" class="btn btn-primary">
              </form>
            </div>
          </div>
        </div>


        <div class="col-xl-4 col-lg-5">
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Photo de profil</h6>
              <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item" id="modifyImgButton" href="#">Modifier</a>
                  <a class="dropdown-item" id="deleteImgButton" href="#" data-toggle="modal" data-target="#deleteImgModal">Supprimer</a>
                </div>
              </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <div class="input-group d-none" id="imgUploadInput">
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="upload_image" name="upload_image" accept="image/*" data-browse="Charger">
                  <label class="custom-file-label" for="upload_image">Choisissez un fichier</label>
                </div>
              </div>
              <form action="" method="post" id="imageForm">
                <div id="uploaded_image"></div>
                <input type="text" class="d-none" id="imageNameToDelete" name="imageNameToDelete" value="<?= $resultUser['user_img']; ?>">
              </form>
              <div id="profilePicture">
                <img class="img-profile rounded img-fluid mx-auto d-block" src="img/<?php if (!empty($resultUser['user_img'])) {
                                                                                                  echo 'profilepic/'.$resultUser['user_img'];
                                                                                                } else {
                                                                                                  echo 'default.png';
                                                                                                }; ?>">
              </div>
            </div>
          </div>
          <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Modifier mot de passe</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
              <form method="post" class="enableSubmitOnChange">
                <div class="form-group">
                  <input type="password" class="form-control" name="current_password" placeholder="Mot de passe actuel">
                </div>
                <div class="form-group">
                  <input type="password" autocomplete="new-password" class="form-control" name="new_password" placeholder="Nouveau mot de passe">
                </div>
                <div class="form-group">
                  <input type="password" autocomplete="new-password" class="form-control" name="new_passwordc" placeholder="Confirmation du mot de passe">
                </div>
                <input type="submit" value="Valider" name="change_password" class="btn btn-primary btn-block">
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid -->



    <!-- Image Cropper Modal-->
    <div id="uploadimageModal" class="modal" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Upload & Crop Image</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-8 text-center">
                <div id="image_demo" style="width:350px; margin-top:30px"></div>
              </div>
              <div class="col-md-4" style="padding-top:30px;">
                <br />
                <br />
                <br />
                <button class="btn btn-success crop_image">Crop & Upload Image</button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Image Modal-->
    <div class="modal fade" id="deleteImgModal" tabindex="-1" role="dialog" aria-labelledby="deleteGest" aria-hidden="true" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-gray-800" id="">Supprimer</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Voulez-vous vraiment supprimer votre photo de profil ?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
            <form action="" method="post">
              <input form="imageForm" type="submit" name="deleteImg" id="deleteImg" value="Supprimer" class="btn btn-danger">
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>