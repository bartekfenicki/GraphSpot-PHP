<?php

class FollowersQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getFollowers($userID) {
        $query = $this->dbCon->prepare("
            SELECT u.userID, u.username, u.profilePic 
            FROM UserFollows uf
            JOIN Users u ON uf.followerID = u.userID
            WHERE uf.followedID = :userID
            ORDER BY u.username ASC
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
