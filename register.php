<?php

session_start();
include 'includes/database.php';

if (isset($_SESSION['userEmail'])) {
  header('Location:index');
}

if (isset($_POST['submit'])) {

  $firstname = ucwords(htmlspecialchars($_POST['firstname']));
  $lastname = ucwords(htmlspecialchars($_POST['lastname']));
  $email = htmlspecialchars($_POST['email']);
  $email_confirm = htmlspecialchars($_POST['email_confirm']);
  if (!empty($_POST['password'])) {
    $password = sha1($_POST['password']);
  }
  if (!empty($_POST['password_confirm'])) {
    $password_confirm = sha1($_POST['password_confirm']);
  }
  date_default_timezone_set('Europe/Paris');
  $date = date('d/m/Y à H:i:s');

  $salesYears = serialize(array());

  if ((!empty($firstname)) && (!empty($lastname)) && (!empty($email)) && (!empty($email_confirm)) && (!empty($password)) && (!empty($password_confirm))) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      if ($email == $email_confirm) {
        if ($password == $password_confirm) {
          $database = getPDO();
          $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $rowEmail = countDatabaseValue($database, 'users', 'user_email', 'user_email', $email, $email_confirm);
          if ($rowEmail == 0) {
            try {
              $insertMember = $database->prepare("INSERT INTO users(user_firstname, user_lastname, user_email, user_password, registerdate, sales_years) VALUES(?, ?, ?, ?, ?, ?)");
              $insertMember->execute([
                $firstname,
                $lastname,
                $email,
                $password,
                $date,
                $salesYears
              ]);


              $successMessage = "Votre compte à bien été créé !";
              header('refresh:3;url=login.php');
            } catch (Exception $e) {
              die('Error : ' . $e->getMessage());
            }
          } else {
            $errorMessage = 'Cette adresse email est déjà utilisée.';
          }
        } else {
          $errorMessage = 'Les mots de passes ne correspondent pas.';
        }
      } else {
        $errorMessage = "Les adresses email ne correspondent pas.";
      }
    } else {
      $errorMessage = "L'adresse email n'est pas valide.";
    }
  } else {
    $errorMessage = 'Veuillez remplir tous les champs.';
  }
}

$pageTitle = 'Inscription';
include 'header.php';

?>

<body class="bg-gradient-primary">

  <div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
          <div class="col-lg-7">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4"><?= $pageTitle ?></h1>
              </div>
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
              <form class="user" method="post" action="">
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" id="firstname" name="firstname" placeholder="Prénom" <?php if (isset($firstname)) { ?>value="<?= $firstname ?>" <?php } ?>>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" id="lastname" name="lastname" placeholder="Nom" <?php if (isset($lastname)) { ?>value="<?= $lastname ?>" <?php } ?>>
                  </div>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Adresse email" <?php if (isset($email)) { ?>value="<?= $email ?>" <?php } ?>>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-user" id="email_confirm" name="email_confirm" placeholder="Confirmez l'adresse email">
                </div>
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Mot de passe">
                  </div>
                  <div class="col-sm-6">
                    <input type="password" class="form-control form-control-user" id="password_confirm" name="password_confirm" placeholder="Confirmez le mot de passe">
                  </div>
                </div>
                <input type="submit" name="submit" value="Inscription" class="btn btn-primary btn-user btn-block">
              </form>
              <hr>
              <div class="text-center">
                <a class="small" href="forgot-password">Mot de passe oublié ?</a>
              </div>
              <div class="text-center">
                <a class="small" href="login">Vous avez déjà un compte ? Connectez vous.</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php include 'footer.php'; ?>