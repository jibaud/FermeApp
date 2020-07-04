<?php
session_start();
include 'includes/database.php';
 
if (isset($_SESSION['userEmail'])) {
    header('Location:index.php');
}
$pageTitle = 'Mot de passe oublié';
include 'header.php';

if(isset($_GET['section'])) {
  $section = htmlspecialchars($_GET['section']);
}
if(isset($_GET['code'])) {
  $code = htmlspecialchars($_GET['code']);
}

$database = getPDO();

if(isset($_POST['recup_submit'],$_POST['recup_mail'])) {
  if(!empty($_POST['recup_mail'])) {
     $recup_mail = htmlspecialchars($_POST['recup_mail']);
     if(filter_var($recup_mail,FILTER_VALIDATE_EMAIL)) {
        $mailexist = $database->prepare('SELECT user_id,user_firstname FROM users WHERE user_email = ?');
        $mailexist->execute(array($recup_mail));
        $mailexist_count = $mailexist->rowCount();
        if($mailexist_count == 1) {
           $user_firstname = $mailexist->fetch();
           $user_firstname = $user_firstname['user_firstname'];
           
           $_SESSION['recup_mail'] = $recup_mail;
           $recup_code = "";
           for($i=0; $i < 8; $i++) { 
              $recup_code .= mt_rand(0,9);
           }
           $randomNumber = rand(0,25);
           $alpha = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
           $randomLetter = mb_strtoupper($alpha[$randomNumber]); //mb_strtoupper = majuscule
           $recup_code = $recup_code.$randomLetter;

           $mail_recup_exist = $database->prepare('SELECT id FROM recuperation WHERE mail = ?');
           $mail_recup_exist->execute(array($recup_mail));
           $mail_recup_exist = $mail_recup_exist->rowCount();
           if($mail_recup_exist == 1) {
              $recup_insert = $database->prepare('UPDATE recuperation SET code = ? WHERE mail = ?');
              $recup_insert->execute(array($recup_code,$recup_mail));
           } else {
              $recup_insert = $database->prepare('INSERT INTO recuperation(mail,code) VALUES (?, ?)');
              $recup_insert->execute(array($recup_mail,$recup_code));
           }
           $header="MIME-Version: 1.0\r\n";
        $header.='From:"Milkow"<donotreply@jeanbaptistebaud.fr>'."\n";
        $header.='Content-Type:text/html; charset="utf-8"'."\n";
        $header.='Content-Transfer-Encoding: 8bit';
        $message = '
        <html>
        <head>
          <title>Récupération de mot de passe - Milkow</title>
          <meta charset="utf-8" />
        </head>
        <body background-color="#ededed">
          <font color="#303030";>
            <div max-width="600px" margin="0 auto" background-color="#fff">
              <table width="600px" background-color="#fff">
                <tr>
                  <td>
                    
                    <p>Bonjour <b>'.$user_firstname.'</b>,</p>
                    <p>
                      Voici votre code de récupération: <b>'.$recup_code.'</b><br>
                      Vous pouvez aussi cliquer directement sur le lien suivant ou le copier et le coller dans votre navigateur :<br>
                      <a href="http://localhost:8888/forgot-password.php?section=code&code='.$recup_code.'">http://localhost:8888/forgot-password.php?section=code&code='.$recup_code.'</a> !
                    </p>
                  </td>
                </tr>
                <tr>
                  <td>
                    <font size="2">
                      <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
                      <p>À bientot !</p>
                    </font>
                  </td>
                </tr>
              </table>
            </div>
          </font>
        </body>
        </html>
        ';
        mail($recup_mail, "Récupération de mot de passe - Milkow", $message, $header);
           header("Location:forgot-password.php?section=code");
        } else {
           $errorMessage = "Cette adresse email n'est pas enregistrée.";
        }
     } else {
        $errorMessage = "Adresse email invalide.";
     }
  } else {
     $errorMessage = "Veuillez entrer votre adresse email.";
  }
}
if(isset($_POST['verif_submit'],$_POST['verif_code'])) {
  if(!empty($_POST['verif_code'])) {
     $verif_code = htmlspecialchars($_POST['verif_code']);
     $verif_req = $database->prepare('SELECT id FROM recuperation WHERE mail = ? AND code = ?');
     $verif_req->execute(array($_SESSION['recup_mail'],$verif_code));
     $verif_req = $verif_req->rowCount();
     if($verif_req == 1) {
        $up_req = $database->prepare('UPDATE recuperation SET confirm = 1 WHERE mail = ?');
        $up_req->execute(array($_SESSION['recup_mail']));
        header('Location:forgot-password.php?section=changemdp');
     } else {
        $errorMessage = "Code invalide.";
     }
  } else {
     $errorMessage = "Veuillez entrer votre code de vérification.";
  }
}
if(isset($_POST['change_submit'])) {
  if(isset($_POST['change_mdp'],$_POST['change_mdpc'])) {
     $verif_confirme = $database->prepare('SELECT confirm FROM recuperation WHERE mail = ?');
     $verif_confirme->execute(array($_SESSION['recup_mail']));
     $verif_confirme = $verif_confirme->fetch();
     $verif_confirme = $verif_confirme['confirm'];
     if($verif_confirme == 1) {
        $mdp = htmlspecialchars($_POST['change_mdp']);
        $mdpc = htmlspecialchars($_POST['change_mdpc']);
        if(!empty($mdp) AND !empty($mdpc)) {
           if($mdp == $mdpc) {
              $mdp = sha1($mdp);
              $ins_mdp = $database->prepare('UPDATE users SET user_password = ? WHERE user_email = ?');
              $ins_mdp->execute(array($mdp,$_SESSION['recup_mail']));
             $del_req = $database->prepare('DELETE FROM recuperation WHERE mail = ?');
             $del_req->execute(array($_SESSION['recup_mail']));
              header('Location:login.php');
           } else {
              $errorMessage = "Vos mots de passes ne correspondent pas.";
           }
        } else {
           $errorMessage = "Veuillez remplir tous les champs.";
        }
     } else {
        $errorMessage = "Veuillez valider votre mail grâce au code de vérification qui vous a été envoyé par mail.";
     }
  } else {
     $errorMessage = "Veuillez remplir tous les champs.";
  }
}
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
              <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
<?php if (isset($errorMessage)) {?>
              <div class="alert alert-danger" role="alert">
                <?= $errorMessage // <?= shortcode for <?php echo ?>
              </div>
<?php } else { ?>
              <div class="alert alert-danger" role="alert" style="visibility:hidden">
                ...
              </div>
<?php } ?>
                    
                  </div>
<?php if($section == 'code') { ?>
                  <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-2">Code de vérification</h1>
                    <p class="mb-4">Un email comprenant un code de vérification vous a été envoyé à l'adresse suivante : <?= $_SESSION['recup_mail'] ?></p>
                  </div>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="verif_code" id="verif_code" placeholder="Code de vérification" value="<?= $code ?>">
                    </div>
                    <input type="submit" value="Valider" name="verif_submit" class="btn btn-primary btn-user btn-block">
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="register.php">Pas encore de compte ? Inscrivez-vous.</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="login.php">Vous avez déjà un compte ? Connectez vous.</a>
                  </div>          
<?php } elseif($section == "changemdp") { ?>
                  <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-2">Nouveau mot de passe</h1>
                    <p class="mb-4">Choisissez un nouveau mot de passe pour <?= $_SESSION['recup_mail'] ?></p>
                  </div>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="change_mdp" placeholder="Nouveau mot de passe">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="change_mdpc" placeholder="Confirmation du mot de passe">
                    </div>
                    <input type="submit" value="Valider" name="change_submit" class="btn btn-primary btn-user btn-block">
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="register.php">Pas encore de compte ? Inscrivez-vous.</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="login.php">Vous avez déjà un compte ? Connectez vous.</a>
                  </div> 
<?php } else { ?>
                  <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-2"><?= $pageTitle ?> ?</h1>
                    <p class="mb-4">Ça arrive ! Entrez votre adresse email associée à votre compte et nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
                  </div>
                  <form class="user" method="post">
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" name="recup_mail" id="emailHelp" aria-describedby="emailHelp" placeholder="Votre adresse email de connexion">
                    </div>
                    <input type="submit" value="Valider" name="recup_submit" class="btn btn-primary btn-user btn-block">
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="register.php">Pas encore de compte ? Inscrivez-vous.</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="login.php">Vous avez déjà un compte ? Connectez vous.</a>
                  </div>
<?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <?php include 'footer.php'; ?>
