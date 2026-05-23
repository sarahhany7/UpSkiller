<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$q1 = $connect -> prepare("SELECT * FROM users");
$q1 -> execute();
$userCount = $q1 -> rowCount();

$q2 = $connect ->prepare("SELECT * FROM tracks");
$q2 -> execute();
$trackCount = $q2 -> rowCount();

$q3 = $connect ->prepare("SELECT * FROM courses");
$q3 -> execute();
$courseCount = $q3 -> rowCount();

$q4 = $connect ->prepare("SELECT * FROM platforms");
$q4 -> execute();
$platformCount = $q4 -> rowCount();

$q5 = $connect ->prepare("SELECT * FROM coursePlatforms");
$q5 -> execute();
$linkCount = $q5 -> rowCount();

$q6 = $connect ->prepare("SELECT * FROM rates");
$q6 -> execute();
$rateCount = $q6 -> rowCount();

$q7 = $connect ->prepare("SELECT * FROM contactmessages");
$q7 -> execute();
$messageCount = $q6 -> rowCount();



?>
    
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-users fa-beat-fade fa-2xl"></i>
                <h4>Users</h4>
                <h4><?php echo $userCount ?></h4>
                <a class="btn btn-outline-light" href="users.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-list fa-beat fa-2xl"></i>
                <h4>Tracks</h4>
                <h4><?php echo $trackCount ?></h4>
                <a class="btn btn-outline-light" href="tracks.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-graduation-cap fa-beat fa-2xl"></i>
                <h4>Courses</h4>
                <h4><?php echo $courseCount ?></h4>
                <a class="btn btn-outline-light" href="courses.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-brands fa-youtube fa-beat fa-2xl"></i>
                <h4>Platforms</h4>
                <h4><?php echo $platformCount ?></h4>
                <a class="btn btn-outline-light" href="platforms.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-link fa-beat-fade fa-2xl"></i>
                <h4>Course Platforms</h4>
                <h4><?php echo $linkCount ?></h4>
                <a class="btn btn-outline-light" href="coursePlatforms.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-star fa-beat-fade fa-2xl"></i>
                <h4>Rate</h4>
                <h4><?php echo $rateCount ?></h4>
                <a class="btn btn-outline-light" href="rates.php">Show</a>
            </div>
        </div>
        <div class="col-md-4 m-auto text-center">
            <div class="box">
                <i class="fa-solid fa-comments fa-beat-fade fa-2xl"></i>
                <h4>Messages</h4>
                <h4><?php echo $messageCount ?></h4>
                <a class="btn btn-outline-light" href="messages.php">Show</a>
            </div>
        </div>
    </div>
</div>


<?php
include('includes/temp/footer.php');
?>
