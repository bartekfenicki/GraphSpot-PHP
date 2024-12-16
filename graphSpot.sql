DROP DATABASE IF EXISTS GraphSpotDB;
CREATE DATABASE GraphSpotDB;
USE GraphSpotDB;

CREATE TABLE Images (
    imageID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    media MEDIUMBLOB NOT NULL
);

CREATE TABLE likes (
    likeID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    postID INT(11) NULL,
    userID INT(11) NULL,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE
);


CREATE TABLE LogoDisplay (
    logoID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    logo MEDIUMBLOB NOT NULL
);

CREATE TABLE Posts (
    postID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255),
    content MEDIUMTEXT NOT NULL,
    type ENUM('post', 'comment') NOT NULL,
    userID INT(11) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    parentPostID INT(11) NULL,
    commentImage LONGBLOB,
    isPinned TINYINT(1) DEFAULT NULL,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (parentPostID) REFERENCES Posts(postID) ON DELETE CASCADE
);


CREATE TABLE post_images (
    post_imageID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    postID INT(11) NOT NULL,
    imageID INT(11) NOT NULL,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE,
    FOREIGN KEY (imageID) REFERENCES Images(imageID) ON DELETE CASCADE
);

CREATE TABLE post_tags (
    post_tagID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    postID INT(11) NOT NULL,
    tagID INT(11) NOT NULL,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE,
    FOREIGN KEY (tagID) REFERENCES Tags(tagID) ON DELETE CASCADE
);


CREATE TABLE Saves (
    saveID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT(11) NOT NULL,
    postID INT(11) NOT NULL,
    saved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);


CREATE TABLE siteInformation (
    infoID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(80) NOT NULL,
    content MEDIUMTEXT NOT NULL
);

CREATE TABLE styles (
    styleID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sidemenu_background VARCHAR(50) NOT NULL,
    body_background VARCHAR(50) NOT NULL,
    style_name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 0
);


CREATE TABLE Tags (
    tagID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(60) NOT NULL UNIQUE
);


CREATE TABLE UserFollows (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    followerID INT(11) NOT NULL,
    followedID INT(11) NOT NULL,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (followerID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (followedID) REFERENCES Users(userID) ON DELETE CASCADE
);


CREATE TABLE Users (
    userID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Fname VARCHAR(60) NOT NULL,
    Lname VARCHAR(60) DEFAULT NULL,
    email VARCHAR(60) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    username VARCHAR(60) NOT NULL UNIQUE,
    profilePic MEDIUMBLOB DEFAULT NULL,
    isBanned TINYINT(1) DEFAULT NULL,
    userRole ENUM('user', 'admin') DEFAULT 'user',
    userBio TEXT DEFAULT NULL
);


CREATE VIEW followingsview AS
SELECT 
    uf.followerID,
    uf.followedID,
    u.username AS followedUsername,
    u.profilePic AS followedProfilePic
FROM UserFollows uf
JOIN Users u ON uf.followedID = u.userID;


CREATE VIEW userprofileview AS
SELECT 
    u.userID,
    u.username,
    u.Fname,
    u.Lname,
    u.profilePic,
    u.userBio,
    (
        SELECT COUNT(*) FROM UserFollows WHERE followedID = u.userID
    ) AS followerCount,
    (
        SELECT COUNT(*) FROM UserFollows WHERE followerID = u.userID
    ) AS followingCount,
    (
        SELECT COUNT(*) FROM Posts WHERE userID = u.userID AND type = 'post'
    ) AS totalPosts
FROM Users u;

-- Triggers
DELIMITER $$

-- Trigger: after_post_delete
CREATE TRIGGER after_post_delete
AFTER DELETE ON Posts
FOR EACH ROW
BEGIN
    DELETE FROM post_images WHERE postID = OLD.postID;
    DELETE FROM Images 
    WHERE imageID NOT IN (SELECT imageID FROM post_images);
END$$

-- Trigger: after_post_delete_tag
CREATE TRIGGER after_post_delete_tag
AFTER DELETE ON Posts
FOR EACH ROW
BEGIN
    DELETE FROM post_tags WHERE postID = OLD.postID;
    DELETE FROM Tags
    WHERE tagID NOT IN (SELECT DISTINCT tagID FROM post_tags);
END$$

-- Trigger: delete_user_related_data
CREATE TRIGGER delete_user_related_data
AFTER DELETE ON Users
FOR EACH ROW
BEGIN
    DELETE FROM likes WHERE userID = OLD.userID;
    DELETE FROM Posts WHERE userID = OLD.userID AND type = 'comment';
    DELETE FROM Posts WHERE userID = OLD.userID AND type = 'post';
END$$

DELIMITER ;



