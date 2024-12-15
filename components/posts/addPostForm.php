<?php 
require_once "queries/dbcon.php"; 

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    // echo "Generated CSRF Token: " . $_SESSION['csrf_token'];
}

$csrfToken = $_SESSION['csrf_token'];

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/componentStyles/postForm.css">
</head>
<body>
    <div id="addPostModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Add New Post</h2>
<form action="queries/components/posts/addPostHandler.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
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

<script src="scripts/components/addPostForm.js"></script> 
</body>
