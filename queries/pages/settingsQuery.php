<?php

class settingsQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getPrivacyPolicy() {
        $query = $this->dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 2");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getTermsConditions() {
        $query = $this->dbCon->prepare("SELECT content FROM siteInformation WHERE infoID = 3");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserBio($userID) {
        $query = $this->dbCon->prepare("SELECT userBio FROM Users WHERE userID = :userID");
        $query->bindParam(':userID', $userID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    public function getProfilePic($userID) {
        $query = $this->dbCon->prepare("SELECT profilePic FROM Users WHERE userID = :userID");
        $query->bindParam(':userID', $userID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
        
    }
}
