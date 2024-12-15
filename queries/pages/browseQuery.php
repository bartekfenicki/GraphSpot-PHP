<?php

class BrowseQueries {
    private $dbCon;

    public function __construct($dbCon) {
        $this->dbCon = $dbCon;
    }

    public function searchPosts($searchQuery) {
        $query = $this->dbCon->prepare("
            SELECT p.*, u.username, i.media AS first_image
            FROM Posts p
            LEFT JOIN Users u ON p.userID = u.userID
            LEFT JOIN post_images pi ON p.postID = pi.postID
            LEFT JOIN Images i ON pi.imageID = i.imageID
            WHERE p.type = 'post' 
            AND (p.location LIKE :query 
                OR EXISTS (
                    SELECT 1 
                    FROM post_tags pt
                    JOIN tags t ON pt.tagID = t.tagID
                    WHERE pt.postID = p.postID AND t.tag LIKE :query
                )
                OR u.username LIKE :query)
            GROUP BY p.postID
            ORDER BY p.created_at DESC
        ");
        $query->bindValue(':query', '%' . $searchQuery . '%');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchUsers($searchQuery) {
        $query = $this->dbCon->prepare("
            SELECT userID, username 
            FROM Users 
            WHERE username LIKE :query
            ORDER BY username ASC
        ");
        $query->bindValue(':query', '%' . $searchQuery . '%');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
