<?php
header('Content-Type: application/json');

require_once('includes/database.php');

$database = getPDO();
$query = $database->prepare("SELECT * FROM cows");
$query->execute();
$result = $query->fetchAll();

mysqli_close($database);

echo json_encode($result);
?>