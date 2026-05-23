<?php

session_start();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/message.css">
</head>
<body>
    <div id="accessModal" class="modal-target">
    <div class="modal-content">
        <a href="#" class="close-btn">&times;</a>
        <div class="modal-icon">⚠️</div>
        <h3>Access Denied</h3>
        <p>Please Sign Up or Log In first to view the Contact page.</p>
        <a href="signup.php" class="btn modal-signup-btn">Sign Up Now</a>
    </div>
</div>
</body>
</html>