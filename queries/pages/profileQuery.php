<?php

class profileQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getUserProfile($userID) {
        $query = $this->dbCon->prepare("
            SELECT * 
            FROM userprofileview 
            WHERE userID = :userID
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserPosts($userID) {
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
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLikedPosts($userID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username, i.media AS first_image
            FROM Likes l
            JOIN Posts p ON l.postID = p.postID
            JOIN Users u ON p.userID = u.userID
            LEFT JOIN post_images pi ON p.postID = pi.postID
            LEFT JOIN Images i ON pi.imageID = i.imageID
            WHERE l.userID = :userID AND p.type = 'post'
            GROUP BY p.postID
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSavedPosts($userID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username, i.media AS first_image
            FROM Saves sp
            JOIN Posts p ON sp.postID = p.postID
            JOIN Users u ON p.userID = u.userID
            LEFT JOIN post_images pi ON p.postID = pi.postID
            LEFT JOIN Images i ON pi.imageID = i.imageID
            WHERE sp.userID = :userID
            GROUP BY p.postID
            ORDER BY sp.saved_at DESC
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFollowers($userID) {
        $query = $this->dbCon->prepare("
            SELECT u.userID, u.username 
            FROM UserFollows uf
            JOIN Users u ON uf.followerID = u.userID
            WHERE uf.followedID = :userID
            ORDER BY u.username ASC
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFollowing($userID) {
        $query = $this->dbCon->prepare("
            SELECT u.userID, u.username 
            FROM UserFollows uf
            JOIN Users u ON uf.followedID = u.userID
            WHERE uf.followerID = :userID
            ORDER BY u.username ASC
        ");
        $query->execute([':userID' => $userID]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

