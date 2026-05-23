<?php
include('includes/db/db.php');
session_start();




$stmt = $connect->prepare("SELECT * FROM tracks");
$stmt->execute();
$tracks = $stmt->fetchAll();



$userId = $_SESSION['User_Id'] ?? 0;


if(isset($_POST['submit_rate'])) {
    $courseId = $_POST['course_id'];
    $rate = $_POST['rate'];

    $stmt = $connect->prepare("
        INSERT INTO rates (User_Id, Course_Id, Rate)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE Rate = ?
    ");
    $stmt->execute([$userId, $courseId, $rate, $rate]);

    header("Location: track.php?id=".$_GET['id']);
    exit;
}


$trackId = $_GET['id'] ?? 0;
$stmtTrack = $connect->prepare("SELECT * FROM tracks WHERE Track_Id = ?");
$stmtTrack->execute([$trackId]);
$track = $stmtTrack->fetch();


$stmtCourses = $connect->prepare("SELECT * FROM courses WHERE Track_Id = ?");
$stmtCourses->execute([$trackId]);
$courses = $stmtCourses->fetchAll();


$coursePlatforms = [];
foreach ($courses as $course) {
    $stmtPlatforms = $connect->prepare("
        SELECT cp.Url, p.Platform_Name
        FROM courseplatforms cp
        INNER JOIN platforms p ON cp.Platform_Id = p.Platform_Id
        WHERE cp.Course_Id = ?
    ");
    $stmtPlatforms->execute([$course['Course_Id']]);
    $coursePlatforms[$course['Course_Id']] = $stmtPlatforms->fetchAll();
}


$courseRatings = [];
foreach ($courses as $course) {
    $stmtRating = $connect->prepare("SELECT AVG(Rate) as avgRating FROM rates
     WHERE Course_Id = ?");
    $stmtRating->execute([$course['Course_Id']]);
    $courseRatings[$course['Course_Id']] = $stmtRating->fetch()['avgRating'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($track['Track_Name']) ?> Track</title>
<link rel="stylesheet" href="css/style2.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <div class="logo"></div>
    <nav>
        <ul>
            <li><a href="aftersign.php">Home</a></li>
            <li><a href="aboutsign.php">About Us</a></li>
            <li><a href="#courses">Tracks</a></li>
            <li><a href="contactsig.php">Contact us</a></li>
        </ul>
    </nav>
</header>

<section class="hero-section">
    <h1><?= htmlspecialchars($track['Track_Name']) ?></h1>
    <div class="description-container">
        <p><?= htmlspecialchars($track['Description']) ?></p>
    </div>
</section>

<section class="explore-courses">
    <div class="courses-grid">
        <?php foreach ($courses as $course): ?>
        <div class="course-card">
            <h4><?= htmlspecialchars($course['Course_Name']) ?></h4>


            <select class="course-source">
                <option value="">Select Platform</option>
                <?php foreach ($coursePlatforms[$course['Course_Id']] ?? [] as $platform): ?>
                <option value="<?= htmlspecialchars($platform['Url']) ?>">
                    <?= htmlspecialchars($platform['Platform_Name']) ?>
                </option>
                <?php endforeach; ?>
            </select>


            <p>Rating: <?= number_format($courseRatings[$course['Course_Id']], 1) ?> / 5</p>


            <form method="post" style="margin-top:10px;">
                <input type="hidden" name="course_id" value="<?= $course['Course_Id'] ?>">
                <label>Rate this course:</label>
                <select name="rate">
                    <option value="1">1 ⭐</option>
                    <option value="2">2 ⭐⭐</option>
                    <option value="3">3 ⭐⭐⭐</option>
                    <option value="4">4 ⭐⭐⭐⭐</option>
                    <option value="5">5 ⭐⭐⭐⭐⭐</option>
                </select>
                <button class="btn go-btn" style="margin-top:10px;" type="submit" name="submit_rate">GO</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
document.querySelectorAll('.course-card').forEach(card => {
    const select = card.querySelector('.course-source');
    const goBtn = card.querySelector('.go-btn');

    goBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const url = select.value;
        if(url) window.open(url,'_blank');
        else alert('Please select a platform first.');
    });
});
</script>

</body>
</html>








<button id="themeBtn">🌙</button>

<script>
    const themeBtn = document.getElementById('themeBtn');
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark');
        themeBtn.textContent = '☀️';
    }
    themeBtn.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        themeBtn.textContent = isDark ? '☀️' : '🌙';
    });

    document.querySelectorAll('.course-card').forEach(card => {
        const select = card.querySelector('.course-source');
        const goBtn = card.querySelector('.go-btn');

        goBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = select.value;
            if (url) {
                window.open(url, '_blank');
            } else {
                alert('Please select a source first.');
            }
        });
    });
</script>

<section class="courses" id="courses">
    <h2>Popular <span class="highlight">Programming Tracks</span></h2>
</section>

<div class="course cards">
    <?php foreach($tracks as $track): ?>
        <div class="course card" id="track<?= $track['Track_Id'] ?>">
            <img src="Admin/<?= htmlspecialchars($track['Image_Path']) ?>" 
                 alt="<?= htmlspecialchars($track['Track_Name']) ?>" style="width:100%; height:auto;">
            <h3><?= htmlspecialchars($track['Track_Name']) ?></h3>
            <a href="track.php?id=<?= $track['Track_Id'] ?>" class="btn">View Track</a>
        </div>
    <?php endforeach; ?>
</div>
<br>
<br>
<br>
</body>

</html>