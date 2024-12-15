<?php

class FollowingQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getFollowings($userID) {
        $query = $this->dbCon->prepare("
            SELECT followedID, followedUsername, followedProfilePic
            FROM followingsview
            WHERE followerID = :userID
            ORDER BY followedUsername ASC
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}