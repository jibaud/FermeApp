<?php 

    /**
     * Connexion à la base de données.
     */
    function getPDO() {
        try {
            $pdo = new PDO('mysql:dbname=milkow;host=localhost', 'root', 'root');
            $pdo->exec("SET CHARACTER SET utf8");
            return $pdo;
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    // Fonction qui empeche les doublons (2 vaches ayant le meme ID)
    function countDatabaseValue($connexionBDD, $tableName, $key1, $key2, $value1, $value2) {
        $request = "SELECT * FROM $tableName WHERE $key1 = ? AND $key2 = ?";
        $rowCount = $connexionBDD->prepare($request);
        $rowCount->execute(array($value1, $value2));
        return $rowCount->rowCount();
    }

?>