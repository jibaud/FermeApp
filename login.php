<?php
 
 session_start();
 include 'includes/database.php';
  
 if (isset($_SESSION['userEmail'])) {
     header('Location:index.php');
 }
  
 if (isset($_POST['submit'])) {
  
    $email = htmlspecialchars($_POST['email']);
    if (!empty($_POST['password'])){
      $password = sha1($_POST['password']);
    }
  
     if ((!empty($email)) && (!empty($password))) {
  
         $database = getPDO();
         $requestUser = $database->prepare("SELECT * FROM users WHERE user_email = ? AND user_password = ?");
         $requestUser->execute(array($email, $password));
         $userCount = $requestUser->rowCount();
         if ($userCount == 1) {
            
             $userInfo = $requestUser->fetch();
             $_SESSION['userID'] = $userInfo['user_id'];
             $_SESSION['userPseudo'] = $userInfo['user_pseudo'];
             $_SESSION['userEmail'] = $userInfo['user_email'];
             $_SESSION['userPassword'] = $userInfo['user_password'];
             $_SESSION['userAdmin'] = $userInfo['isadmin'];
             $_SESSION['userRegisterDate'] = $userInfo['registerdate'];
             $successMessage = "Connexion réussie";
             header('refresh:2;url=index.php');
  
         } else {
             $errorMessage = 'Email ou mot de passe incorrect!';
         }
     } else {
         $errorMessage = 'Veuillez remplir tous les champs..';
     }
 }

$pageTitle = 'Login';
include 'header.php';

?>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
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
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="exampleInputPassword" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div>
                    <input type="submit" name="submit" value="Login" class="btn btn-primary btn-user btn-block">
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="forgot-password.php">Forgot Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="register.php">Create an Account!</a>
                  </div>
                </div>
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
