<?php 

    /**
     * Connexion à la base de données.
     */
    function getPDO() {
        try {
            $pdo = new PDO('mysql:dbname=fermeapp;host=localhost', 'root', 'root');
            $pdo->exec("SET CHARACTER SET utf8");
            return $pdo;
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    function countDatabaseValue($connexionBDD, $tableName, $key, $value) {
        $request = "SELECT * FROM $tableName WHERE $key = ?";
        $rowCount = $connexionBDD->prepare($request);
        $rowCount->execute(array($value));
        return $rowCount->rowCount();
    }

?>