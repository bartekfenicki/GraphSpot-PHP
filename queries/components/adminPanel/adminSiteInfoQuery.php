<?php
require_once "queries/dbcon.php";

class AdminSiteInfoQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getSiteInfo($infoID) {
        $query = $this->dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = :infoID");
        $query->bindParam(':infoID', $infoID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSiteInfo($infoID, $content) {
        $query = $this->dbCon->prepare("UPDATE siteInformation SET content = :content WHERE infoID = :infoID");
        $query->bindParam(':content', $content);
        $query->bindParam(':infoID', $infoID);
        return $query->execute();
    }
}
