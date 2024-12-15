<body>
<?php 
// DB connect
require_once "queries/dbcon.php";
$dbCon = dbCon($user, $DBpassword);

// Fetch users
$queryUser = $dbCon->prepare("SELECT * FROM Users");
$queryUser->execute();
$getUsers = $queryUser->fetchAll(PDO::FETCH_ASSOC);

foreach ($getUsers as $getUser): ?>
            <div class="container">
                <?php if ($getUser['profilePic']): ?>
                    <?php $imageData = base64_encode($getUser['profilePic']); ?>
                    <div>
                        <img src="data:image/jpeg;base64,<?= $imageData ?>" alt="user picture" style="max-width: 50px; border-radius: 20px;">
                    </div>
                <?php else: ?>
                    <div>No image available</div>
                <?php endif; ?>
                <div><strong>User ID:</strong> <?= htmlspecialchars($getUser['userID']) ?></div>
                <div><strong>Username:</strong> <?= htmlspecialchars($getUser['username']) ?></div>
                <div><strong>Name:</strong> <?= htmlspecialchars($getUser['Fname'] . " " . $getUser['Lname']) ?></div>
                <div><strong>Email:</strong> <?= htmlspecialchars($getUser['email']) ?></div>
                <div>
                    <?php if ($getUser['isBanned']): ?>
                        <button onclick="updateBanStatus(<?= $getUser['userID'] ?>, 0)">Unban</button>
                    <?php else: ?>
                        <button onclick="updateBanStatus(<?= $getUser['userID'] ?>, 1)">Ban</button>
                    <?php endif; ?>
                </div>
            </div>
<?php endforeach; ?>

<script src="scripts/components/adminUsers.js"></script> 
</body>

