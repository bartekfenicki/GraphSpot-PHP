<?php

require_once "dbcon.php";

class domQuery {
    private $dbCon;

    public function __construct($user, $password) {
        $this->dbCon = $this->connect($user, $password);
    }

    private function connect($user, $password) {
        return dbCon($user, $password);
    }

    public function fetchLogoPath() {
        $query = $this->dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1 LIMIT 1");
        $query->execute();
        $logoData = $query->fetch(PDO::FETCH_ASSOC);
        return $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';
    }

    public function fetchStyleSettings() {
        $query = $this->dbCon->prepare("SELECT sidemenu_background, body_background FROM styles WHERE is_active = 1");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchProfilePicture($userID) {
        $query = $this->dbCon->prepare("SELECT profilePic FROM Users WHERE userID = :userID");
        $query->bindParam(':userID', $userID); 
        $query->execute();
        $profilePic = $query->fetch(PDO::FETCH_ASSOC);
        return $profilePic && !empty($profilePic['profilePic'])
            ? 'data:image/png;base64,' . base64_encode($profilePic['profilePic'])
            : 'assets/images/profileUser.png';
    }
}
?>
