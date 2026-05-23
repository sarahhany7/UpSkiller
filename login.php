<?php
session_start();
include('includes/db/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $connect->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if ($user['Password'] == $password) { 
            
            $_SESSION['user_email'] = $user['Email'];
            $_SESSION['Role'] = $user['Role'];

            
            if ($user['Role'] == 'Admin') {
                header("Location:/UpSkiller/Admin/dashboard.php");
                exit;
            } else {
                header("Location: aftersign.php");
                exit;
            }
        } else {
            $error = "Email or Password is incorrect";
        }
    } else {
        $error = "Email or Password is incorrect";
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>


<body data-theme="light">

    <div class="full-screen-modal show-on-load">
        <div class="modal-content">

            <button id="themeToggle" class="theme-toggle-btn">
                <i class="fas fa-moon"></i>
            </button>

            <div class="auth-container">

                <div class="welcome-panel">

                    <div class="logo">Upskillr</div>
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info.</p>

                    <a href="signup.php" class="sign-in-btn">CREATE ACCOUNT</a>
                </div>

                <div class="form-panel">
                    <h2>Login</h2>

                    <div class="social-login">
                        <a href="https://www.facebook.com/login/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://accounts.google.com/signin" class="social-icon"><i class="fab fa-google-plus-g"></i></a>
                        <a href="https://www.linkedin.com/login" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>

                    <p class="separator-text"> Use your

                        <a href="https://mail.google.com/" class="email-link">Email</a>
                        account
                    </p>


                    <?php
                    if ($error != '') {
                        echo "<p style='color:red;text-align:center'>" . $error . "</p>";
                    }
                    ?>

                    <form id="loginForm" action="login.php" method="POST">
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="Email" name="email" required>
                        </div>

                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" name="password" required>
                        </div>

                        <button type="submit" class="sign-up-btn">LOGIN</button>
                    </form>


                    <p class="switch-link">
                        Need an account? <a href="signup.php">Sign Up here</a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('themeToggle');
            const body = document.body;

            const savedTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', savedTheme);
            updateToggleIcon(savedTheme);

            toggleButton.addEventListener('click', () => {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';

                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateToggleIcon(newTheme);
            });

            function updateToggleIcon(theme) {
                const icon = toggleButton.querySelector('i');
                if (theme === 'dark') {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            }
        });
    </script>
</body>

</html>