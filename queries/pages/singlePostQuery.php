<?php

class singlePostQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getPost($postID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username 
            FROM Posts p 
            JOIN Users u ON p.userID = u.userID 
            WHERE p.postID = :postID
        ");
        $query->bindParam(':postID', $postID, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getComments($postID) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username 
            FROM Posts p 
            JOIN Users u ON p.userID = u.userID 
            WHERE p.parentPostID = :postID AND p.type = 'comment'
            ORDER BY p.created_at ASC
        ");
        $query->bindParam(':postID', $postID, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostImages($postID) {
        $query = $this->dbCon->prepare("
            SELECT i.media 
            FROM post_images pi 
            JOIN Images i ON pi.imageID = i.imageID 
            WHERE pi.postID = :postID
        ");
        $query->bindParam(':postID', $postID, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLikesCount($postID) {
        $query = $this->dbCon->prepare("SELECT COUNT(*) FROM Likes WHERE postID = :postID");
        $query->bindParam(':postID', $postID, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn();
    }

    public function userHasLiked($postID, $userID) {
        $query = $this->dbCon->prepare("SELECT COUNT(*) FROM Likes WHERE postID = :postID AND userID = :userID");
        $query->bindParam(':postID', $postID, PDO::PARAM_INT);
        $query->bindParam(':userID', $userID, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchColumn() > 0;
    }
}
