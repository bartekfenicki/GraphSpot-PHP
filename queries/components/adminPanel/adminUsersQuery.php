<?php
require_once "queries/dbcon.php";

class AdminUsersQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getUsers() {
        $query = $this->dbCon->prepare("SELECT * FROM Users");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
