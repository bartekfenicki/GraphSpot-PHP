<?php

class UserProfileQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getUserProfile($profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT username, Fname, Lname, profilePic 
            FROM Users 
            WHERE userID = :userID
        ");
        $query->bindParam(':userID', $profileUserID);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserPosts($profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username, i.media AS first_image
            FROM Posts p
            LEFT JOIN Users u ON p.userID = u.userID
            LEFT JOIN post_images pi ON p.postID = pi.postID
            LEFT JOIN Images i ON pi.imageID = i.imageID
            WHERE p.userID = :userID AND p.type = 'post'
            GROUP BY p.postID
            ORDER BY p.created_at DESC
        ");
        $query->bindParam(':userID', $profileUserID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLikedPosts($profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username, i.media AS first_image
            FROM likes l
            JOIN Posts p ON l.postID = p.postID
            JOIN Users u ON p.userID = u.userID
            LEFT JOIN post_images pi ON p.postID = pi.postID
            LEFT JOIN Images i ON pi.imageID = i.imageID
            WHERE l.userID = :userID AND p.type = 'post'
            GROUP BY p.postID
        ");
        $query->bindParam(':userID', $profileUserID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserFollowing($userID, $profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT COUNT(*) 
            FROM UserFollows 
            WHERE followerID = :followerID AND followedID = :followedID
        ");
        $query->execute([
            ':followerID' => $userID,
            ':followedID' => $profileUserID
        ]);
        return $query->fetchColumn() > 0;
    }

    public function getFollowerCount($profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT COUNT(*) AS followerCount 
            FROM UserFollows 
            WHERE followedID = :userID
        ");
        $query->execute([':userID' => $profileUserID]);
        return $query->fetchColumn();
    }

    public function getFollowingCount($profileUserID) {
        $query = $this->dbCon->prepare("
            SELECT COUNT(*) AS followingCount 
            FROM UserFollows 
            WHERE followerID = :userID
        ");
        $query->execute([':userID' => $profileUserID]);
        return $query->fetchColumn();
    }
}
?>
