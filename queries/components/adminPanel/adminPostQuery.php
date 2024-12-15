<?php
require_once "queries/dbcon.php";

class AdminPostQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function getAllPosts() {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username
            FROM Posts p
            LEFT JOIN Users u ON p.userID = u.userID
            WHERE p.type = 'post'
            ORDER BY p.created_at DESC
        ");
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
        $query->bindParam(':postID', $postID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getComments($postID) {
        $query = $this->dbCon->prepare("
            SELECT c.postID, c.content, u.username, c.commentImage
            FROM Posts c
            LEFT JOIN Users u ON c.userID = u.userID
            WHERE c.parentPostID = :postID AND c.type = 'comment'
            ORDER BY c.created_at ASC
        ");
        $query->bindParam(':postID', $postID);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
