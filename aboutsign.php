<?php
session_start();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Upskillr</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo">Upskillr</div>
            <button id="themeBtn">🌙</button>
            <div class="menu-toggle">&#9776;</div>
            <ul class="nav-links">
                <li><a href="aftersign.php">Home</a></li>
                <li><a href="contactsig.php">Contact us</a></li>

                <li><a href="aboutsign.php">About Us</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="login.php" class="btn login" id="openLogin">Login</a>
                <a href="signup.php" class="btn signup" id="openSignup">Sign Up</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="about-hero">
            <div class="hero-content">
                <h1>Our Story | <span class="highlight">Why We Are Here</span></h1>
                <p class="mission-statement">
                    We believe that education is the driving force for transformation. Upskillr was founded on a vision to bridge the gap between academic learning and the actual needs of the job market, enabling every student to acquire future skills with maximum efficiency and quality.
                </p>
                <div class="stats-grid">
                    <div>
                        <h2>30K+</h2>
                        <p>Enrolled Students</p>
                    </div>
                    <div>
                        <h2>500+</h2>
                        <p>Specialized Courses</p>
                    </div>
                    <div>
                        <h2>4.8</h2>
                        <p>Course Rating</p>
                    </div>
                </div>
            </div>
            <img src="images/section1.svg" alt="Team working together on a goal" class="hero-image">
        </section>

        <section class="values-section">
            <h2>Pillars of <span class="highlight">Success</span></h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-rocket"></i>
                    <h3>Continuous Innovation</h3>
                    <p>We work to constantly update our curricula and tools to stay at the forefront of technology and in-demand skills.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hands-helping"></i>
                    <h3>Community Support</h3>
                    <p>We provide a supportive learning environment where students collaborate and exchange knowledge under expert guidance.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-cogs"></i>
                    <h3>Practical Application</h3>
                    <p>Focusing on practical projects and real-world scenarios to ensure skill mastery, not just theoretical knowledge.</p>
                </div>
            </div>
        </section>

        <section class="team-section">
            <h2>Meet the <span class="highlight">Founding Team</span></h2>
            <div class="team-cards">

                <div class="member-card">
                    <img src="images/man.png" alt="CEO Image">
                    <h3>Ahmed Ali</h3>
                    <p>CEO & Founder</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="member-card">
                    <img src="images/woman (1).png" alt="CTO Image">
                    <h3>Sara Mohamed</h3>
                    <p>Chief Technology Officer (CTO)</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>

                <div class="member-card">
                    <img src="images/woman.png" alt="Head of Content Image">
                    <h3>Khaled Fouad</h3>
                    <p>Head of Content</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

            </div>
        </section>

        <section class="cta-section">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of students who are transforming their careers today.</p>
            <a href="signup.php" class="btn primary-cta">Start Your Journey Now</a>
        </section>

    </main>

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
</body>

</html>