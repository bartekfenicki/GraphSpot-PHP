<?php
require_once "queries/dbcon.php";
require_once "queries/components/adminPanel/adminStylingQuery.php";

$dbCon = dbCon($user, $DBpassword);

$stylingQueries = new AdminStylingQueries($dbCon);

$currentLogo = $stylingQueries->getCurrentLogo();
$styles = $stylingQueries->getStyles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/componentStyles/adminStyling.css">
    <title>Admin Styling</title>
</head>
<body>
    <h2>Current Logo</h2>
    <div class="logo-display">
        <img src="<?php echo $currentLogo; ?>" alt="Current Logo" style="max-width: 200px;">
    </div>

    <form action="queries/components/adminPanel/uploadLogo.php" method="post" enctype="multipart/form-data">
        <label for="newLogo">Select new logo:</label>
        <input type="file" name="newLogo" id="newLogo" accept="image/*">
        <button type="submit">Upload</button>
    </form>

    <h2>Customize Site Styles</h2>
    <form action="queries/components/adminPanel/updateStyles.php" method="post">
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
            echo "<p>Operation completed successfully!</p>";
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
</body>
</html>

