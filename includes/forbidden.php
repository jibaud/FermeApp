<?php

if (!isset($_SESSION['userEmail'])) {
    header('Location:login.php');
}

?>