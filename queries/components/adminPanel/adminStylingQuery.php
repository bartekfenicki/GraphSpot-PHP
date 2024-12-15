<?php
require_once "queries/dbcon.php";

class AdminStylingQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getCurrentLogo() {
        $query = $this->dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1");
        $query->execute();
        $logoData = $query->fetch(PDO::FETCH_ASSOC);

        return $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';
    }

    public function getStyles() {
        $query = $this->dbCon->prepare("SELECT styleID, sidemenu_background, body_background, style_name, is_active FROM styles");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStyle($styleID) {
        $this->dbCon->beginTransaction();

        $deactivateQuery = $this->dbCon->prepare("UPDATE styles SET is_active = 0");
        $deactivateQuery->execute();

        $activateQuery = $this->dbCon->prepare("UPDATE styles SET is_active = 1 WHERE styleID = :styleID");
        $activateQuery->bindParam(':styleID', $styleID);
        $activateQuery->execute();

        return $this->dbCon->commit();
    }
}
