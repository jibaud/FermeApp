<?php
 
session_start();
include 'includes/database.php';
 
if (isset($_SESSION['userEmail'])) {
    header('Location:index.php');
}
 
if (isset($_POST['submit'])){
   
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $email = htmlspecialchars($_POST['email']);
    if (!empty($_POST['password'])){
      $password = sha1($_POST['password']);
    }
    if (!empty($_POST['password_confirm'])){
      $password_confirm = sha1($_POST['password']);
    }
    date_default_timezone_set('Europe/Paris');
    $date = date('d/m/Y à H:i:s');
 
    if ((!empty($pseudo)) && (!empty($email)) && (!empty($password_confirm)) && (!empty($password))) {
        if (strlen($pseudo) <= 16) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($password == $password_confirm) {
 
                    $database = getPDO();
                    $rowEmail = countDatabaseValue($database, 'users', 'user_email', $email);
                    if ($rowEmail == 0) {
                      $insertMember = $database->prepare("INSERT INTO users(user_pseudo, user_email, user_password, isadmin, registerdate) VALUES(?, ?, ?, ?, ?)");
                      $insertMember->execute([
                          $pseudo,
                          $email,
                          $password,
                          0,
                          $date
                      ]);
                      $successMessage = "Votre compte à bien été créé !";
                      header('refresh:3;url=login.php');
                    } else {
                        $errorMessage = 'Cette email est déjà utilisée..';
                    }
                } else {
                    $errorMessage = 'Les mots de passes ne correspondent pas...';
                }
            } else {
                $errorMessage = "Votre email n'est pas valide...";
            }
        } else {
            $errorMessage = 'Le pseudo est trop long...';
        }
    } else {
        $errorMessage = 'Veuillez remplir tous les champs...';
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
                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
              </div>
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
              <form class="user" method="post" action="">
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" id="exampleFirstName" name="pseudo" placeholder="First Name" <?php if (isset($pseudo)) { ?>value="<?= $pseudo ?>" <?php } ?>>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name">
                  </div>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" placeholder="Email Address" <?php if (isset($email)) { ?>value="<?= $email ?>" <?php } ?>>
                  <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="password" class="form-control form-control-user" id="exampleInputPassword" name="password" placeholder="Password">
                  </div>
                  <div class="col-sm-6">
                    <input type="password" class="form-control form-control-user" id="exampleRepeatPassword" name="password_confirm" placeholder="Repeat Password">
                  </div>
                </div>
                <input type="submit" name="submit" value="Register Account" class="btn btn-primary btn-user btn-block">
              </form>
              <hr>
              <div class="text-center">
                <a class="small" href="forgot-password.php">Forgot Password?</a>
              </div>
              <div class="text-center">
                <a class="small" href="login.php">Already have an account? Login!</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
