<?php

// Supprime le .php de l'URL
$currentURL = $_SERVER['REQUEST_URI'];
if(substr($currentURL, -4) == ".php") {
  $path_parts = pathinfo($currentURL);
  $newURL = $path_parts['filename'];
  header('Location:'.$newURL);
}

// Supprime /index de l'url
if(substr($currentURL, -5) == "index") {
  $newURL = "/";
  header('Location:'.$newURL);
}

// Si l'utilisateur n'est pas connecté
if (!isset($_SESSION['userEmail'])) {
    header('Location:login');
}

?>