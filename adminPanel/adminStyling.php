<?php
require_once "dbcon.php"; 
$dbCon = dbCon($user, $DBpassword);

$query = $dbCon->prepare("SELECT logo FROM LogoDisplay WHERE logoID = 1");
$query->execute();
$logoData = $query->fetch(PDO::FETCH_ASSOC);

$currentLogo = $logoData ? 'data:image/png;base64,' . base64_encode($logoData['logo']) : 'assets/images/logo1.png';

// Fetch all styles
$stylesQuery = $dbCon->prepare("SELECT styleID, sidemenu_background, body_background, style_name, is_active FROM styles");
$stylesQuery->execute();
$styles = $stylesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Current Logo</h2>
<div class="logo-display">
    <img src="<?php echo $currentLogo; ?>" alt="Current Logo" style="max-width: 200px;">
</div>

<form action="adminPanel/uploadLogo.php" method="post" enctype="multipart/form-data">
    <label for="newLogo">Select new logo:</label>
    <input type="file" name="newLogo" id="newLogo" accept="image/*">
    <button type="submit">Upload</button>
</form>

<h2>Customize Site Styles</h2>

<form action="adminPanel/updateStyles.php" method="post">
    <label for="style_choice">Choose a Style:</label><br>
    <div id="style-options">
        <?php foreach ($styles as $style): ?>
            <div class="style-option" style="margin-bottom: 10px;">
                <input 
                    type="radio" 
                    name="selected_style" 
                    id="style_<?= $style['styleID'] ?>" 
                    value="<?= $style['styleID'] ?>" 
                    <?= ($style['is_active'] == 1) ? 'checked' : '' ?>
                >
                <label for="style_<?= $style['styleID'] ?>">
                    <?= htmlspecialchars($style['style_name']) ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <br>
    <button type="submit">Save Changes</button>
</form>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo "<p>Logo updated successfully!</p>";
    } elseif ($_GET['status'] === 'invalidfile') {
        echo "<p>Invalid file type. Please upload an image.</p>";
    } elseif ($_GET['status'] === 'uploaderror') {
        echo "<p>There was an error uploading the file. Please try again.</p>";
    } elseif ($_GET['status'] === 'dberror') {
        echo "<p>Database error. Please contact support.</p>";
    } elseif ($_GET['status'] === 'nofile') {
        echo "<p>No file uploaded. Please select a file to upload.</p>";
    }
}

?>

<style>
/* Highlight the active style option */
#style-options .style-option {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
#style-options .style-option input[type="radio"]:checked + label {
    font-weight: bold;
    color: #4CAF50;
}
</style>