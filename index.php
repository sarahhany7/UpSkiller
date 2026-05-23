<?php
session_start();


include('includes/db/db.php');

$stmt = $connect->prepare("SELECT * FROM tracks");
$stmt->execute();
$tracks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UpSkiller</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/message.css">
</head>

<body>
  <header class="head">
    <nav class="navbar">
      <div class="logo">Upskillr</div>
      <button id="themeBtn">🌙</button>
      <div class="menu-toggle">&#9776;</div>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <a href="#accessModal">about us</a>
        <li><a href="index.php">Course</a></li>
        <a href="#accessModal">Contact us</a>
      </ul>
      <div class="nav-buttons">
        <a href="login.php" class="btn login">Login</a>

        <a href="signup.php" class="btn signup">Sign Up</a>

        <button class="btn logout" id="logoutBtn" style="display:none;">Logout</button>
        <span id="welcomeMessage" style="display:none; font-weight: bold; margin-right: 15px;"></span>
      </div>
    </nav>

    <section class="head">
      <div class="content">
        <h1>Improve your <span class="highlight">Skill</span> Faster</h1>
        <p>Speed Up The Skills Acquisition Process By Finding Unlimited Courses That Mathches Your Niche.</p>
        <a href="login.php">
        <button>Enroll Now</button>
        </a>
      </div>
      <img src="images/header.svg" alt="learning">
    </section>
  </header>

  <div id="signupModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeSignup">&times;</span>
      <h2 id="modalTitle">Sign Up</h2>
      <form id="signupForm">
        <input type="text" placeholder="Name" required>
        <input type="email" placeholder="Email" required>
        <input type="password" placeholder="Password" required minlength="6">
        <button type="submit" class="submit-btn" id="modalSubmitBtn">Submit</button>
      </form>
    </div>
  </div>
  </div>

  <section class="stats">
    <div><strong>4.5</strong><br>80K Reviews</div>
    <div><strong>30M</strong><br>Enroliments</div>
    <div><strong>2M+</strong><br>Learners</div>
    <div><strong>1K+</strong><br>Popular Courses</div>
  </section>

  <section class="section1">
    <div class="section1 img"></div>
    <img src="images/section1.svg">
    <div class="section1 text">
      <h2>We Provide <br>Smart Online Education</h2>
      <p>Our Courses Come With Assigned Projects, Direct Interations With Mentors, Relevant Resources, And Tools That Help You Dive Into In-Depth Learning From Anywhere.</p>
    </div>
  </section>

  <section class="features">
    <h2>Our Features Special For You</h2>
    <button>See All Features</button>
    <div class="feature card">
      <h3>Get Certificate</h3>
      <p>Add Value To Your Certificates And Increase Your Chances Of Getting Hired In Your Dream Jop</p>
    </div>
    <div class="feature card">
      <h3>Amazing Instructor</h3>
      <p>Our Amazing Instructors Bring Experience, Knowledge And Fun On The Table</p>
    </div>
    <div class="feature card">
      <h3> Video Lessons</h3>
      <p>Recorded Version Of Lectures From Professional Instructions To Boost Your Growth.</p>
    </div>
    <div class="feature card">
      <h3>Life Time Support</h3>
      <p>You Will Have Life Times Access Of The Courses & Resources Also Contacting Instructors Any Time!</p>
    </div>
  </section>




<section class="courses" id="courses">
    <h2>Popular <span class="highlight">Programming Tracks</span></h2>
</section>

<div class="course cards">
    <?php foreach($tracks as $track): ?>
        <div class="course card" id="track<?= $track['Track_Id'] ?>">
            <img src="Admin/<?= htmlspecialchars($track['Image_Path']) ?>" 
                 alt="<?= htmlspecialchars($track['Track_Name']) ?>" style="width:100%; height:auto;">
            <h3><?= htmlspecialchars($track['Track_Name']) ?></h3>
            <a href="#accessModal" class="btn">View Track</a>
        </div>
    <?php endforeach; ?>
</div>



  <section class="easy started">
    <h2>It's easy to start <span class="highlight">learning</span></h2>
    <p>Our Sign-In Process Lets You Start Your Learning Journey Without Much Hassle. Our Aim Is To Create A Great Learning Experience For You</p>
    <ul>
      <li>Create Account</li>
      <li> Purchase Lessons</li>
      <li> Start Learning</li>
    </ul>
  </section>

  <div class="Ready">
    <h2>Get Ready To Started</h2>
    <p>After A Good One Can Forgive Anybody, Even One Own Relations</p>
    <button>Join Now</button>
  </div>

  <section class="apps">
    <h2>Try Learning Free<br>on <span class="highlight">Mobile App</span></h2>


    <a href="https://appleid.apple.com/" class="btn download app-store-login">
      App Store
    </a>

    <a href="https://play.google.com/console" class="btn download google-play-login">
      Google Play
    </a>
  </section>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section logo-section">
        <h2 class="logo">MONAC</h2>
        <p>Speed up the skill acquisition process by finding unlimited courses that matches your niche.</p>
        <p class="copyright">© Musemind 2022 | All rights reserved.</p>
      </div>

      <div class="footer-section">
        <h4>Company</h4>
        <ul>
          <li><a href="about.php">About Us</a></li>
        </ul>
      </div>

      <div class="terms">
        <a href="#">Terms & Privacy Policy</a>
      </div>
  </footer>

  <script src="js/script.js"></script>
  <div id="accessModal" class="modal-target">
    <div class="modal-content">
      <a href="#" class="close-btn">&times;</a>
      <div class="modal-icon">⚠️</div>
      <h3>Access Denied</h3>
      <p>Please Sign Up or Log In first to view this page.</p>
      <a href="signup.php" class="btn modal-signup-btn">Sign Up Now</a>
    </div>
  </div>
</body>

</html>