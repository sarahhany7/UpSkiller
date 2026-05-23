<?php
session_start();
include('includes/db/db.php');



// متغيرات للأخطاء والقيم القديمة
$errors = [];
$old = [
    'name' => '',
    'dob' => '',
    'gender' => '',
    'university' => '',
    'email' => '',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات
    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $university = trim($_POST['university']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // حفظ القيم القديمة
    $old = compact('name','dob','gender','university','email','password');

    // فاليديشن
    if (empty($name)) $errors[] = "Please enter your full name.";
    if (empty($dob)) $errors[] = "Please select your date of birth.";
    if (!in_array($gender, ['male','female','other'])) $errors[] = "Please select a valid gender.";
    if (empty($university)) $errors[] = "Please enter your university name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters long.";

    // التحقق من وجود الايميل مسبقًا
    $stmt = $connect->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) $errors[] = "This email is already registered.";

    // إذا مفيش أخطاء، نضيف المستخدم
    if (empty($errors)) {
        $stmt = $connect->prepare("INSERT INTO users (User_Name, Birthdate, Gender, University_Name, Email, 
        Password, Created_At)
         VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $dob, $gender, $university, $email, $password]);

        $_SESSION['success'] = "Your account has been created successfully!";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
                    <h1>Join Us Today!</h1>
                    <p>Enter your personal details to start your journey with us.</p>

                    <a href="login.php" class="sign-in-btn">LOG IN</a>
                </div>

                <div class="form-panel">
                    <h2>Create Account</h2>

                    <!-- عرض الأخطاء -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="social-login">
                        <a href="https://www.facebook.com/login/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://accounts.google.com/signin" class="social-icon"><i class="fab fa-google-plus-g"></i></a>
                        <a href="https://www.linkedin.com/login" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>

                    <p class="separator-text"> Use your
                        <a href="https://mail.google.com/" class="email-link">Email</a> to sign up
                    </p>

                    <!-- الفورم -->
                    <form id="signupForm" action="" method="POST">
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Full Name" name="name" required value="<?= htmlspecialchars($old['name']) ?>">
                        </div>

                        <div class="input-group">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" placeholder="Date of Birth" name="dob" required value="<?= htmlspecialchars($old['dob']) ?>">
                        </div>

                        <div class="input-group">
                            <i class="fas fa-venus-mars"></i>
                            <select name="gender" required>
                                <option value="" disabled <?= $old['gender']==""?'selected':'' ?>>Select Gender</option>
                                <option value="male" <?= $old['gender']=='male'?'selected':'' ?>>Male</option>
                                <option value="female" <?= $old['gender']=='female'?'selected':'' ?>>Female</option>
                                <option value="other" <?= $old['gender']=='other'?'selected':'' ?>>Other</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <i class="fas fa-graduation-cap"></i>
                            <input type="text" placeholder="University Name" name="university" required value="<?= htmlspecialchars($old['university']) ?>">
                        </div>

                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="Email" name="email" required value="<?= htmlspecialchars($old['email']) ?>">
                        </div>

                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" name="password" required minlength="6" value="<?= htmlspecialchars($old['password']) ?>">
                        </div>

                        <button type="submit" class="sign-up-btn">SIGN UP</button>
                    </form>

                    <p class="switch-link">
                        Already have an account? <a href="login.php">Log In here</a>
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