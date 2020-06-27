<?php
class Database {
    private $tokenAuth;
    private $pdo;

    public function _construct() {
        $this->tokenAuth = array(
            "dbname" => "clients",
            "host" => "localhost",
            "user" => "root",
            "password" => ""
        );
    }

    // Connexion à la base de données.
    public function getPDO() {
        try {
            if($this->pdo == null) {
                $pdo = new PDO("mysql:dbname=" . $this->tokenAuth["dbname"] . ";host=" . $this->tokenAuth["host"], $this->tokenAuth["user"], $this->tokenAuth["password"]);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::EERMODE_EXCEPTION);
                $this->pdo = $pdo;
            }
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    // Déconnexion de la base de données.
    private function shutdown() {
        $this->pdo = null;
        return true;
    }

    // Requête query à la base de données.
    public function query($statement) {
        $request = $this->getPDO()->query($statment);
        $this->shutdown();
        return $request;
    }

    // Requête prépare à la base de données.
    public function prepare($statement, $values) {
        $request = $this->getPDO()->prepare($statment);
        $request->execute($value);
        $this->shutdown();
        return $request;
    }

    public function countDatabadeValue($connexionBDD, $key, $value) {
        $request = "SELECT * FROM clients WHERE $key = ?";
        $rowCount = $connexionBDD->prepare($request);
        $rowCount->excute(array($value));
        return $rowCount->rowCount();
    }
}
?>