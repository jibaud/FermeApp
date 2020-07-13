<?php
$currentUserId = $_SESSION['userID'];
$database = getPDO();
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// La première fois que l'utilisateur se connecte on lui cré une base de donnée settings
$rowSetting = countDatabaseValue($database, 'settings', 'set_for', 'set_for', $currentUserId, $currentUserId);
if ($rowSetting == 0) {
  try {
    $createSetting = $database->prepare("INSERT INTO settings(set_for) VALUES(?)");
    $createSetting->execute([
      $currentUserId
    ]);
    header('');
  } catch (Exception $e) {
    die('Error : ' . $e->getMessage());
  }
} else {
// Sinon on appelle ses settings enregistrés
$getSettings = $database->prepare("SELECT * FROM settings WHERE set_for = $currentUserId");
$getSettings->execute();
$settings = $getSettings->fetch();

$set_prefixId = $settings['set_prefix'];
}
?>