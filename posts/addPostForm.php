<?php require_once "dbcon.php"; ?>

<div id="addPostModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Add New Post</h2>
<form action="posts/addPostHandler.php" method="post" enctype="multipart/form-data">
    <label for="content">Description:</label>
    <textarea name="content" id="content" rows="5" required></textarea>

    <label for="location">Location:</label>
    <input type="text" name="location" id="location">

    <label for="tags">Tags (comma-separated):</label>
    <input type="text" name="tags" id="tags">

    <label for="images">Upload Images (up to 5):</label>
    <input type="file" name="images[]" id="images" accept="image/*" multiple>

    <button type="submit">Submit</button>
</form>
    </div>
</div>

<!-- Modal CSS -->
<style>
    .modal { 
        display: none; 
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        background-color: #00000077; 
        z-index: 1000;
    }
    .modal-content { 
        background-color: #fff;
        border-radius: 20px;
        margin: 10% auto; 
        padding: 20px; 
        width: 80%; 
        max-width: 500px; 
        position: relative; 
    }
    .close-btn { 
        position: absolute; 
        top: 10px; 
        right: 10px; 
        font-size: 24px; 
        cursor: pointer; }
</style>