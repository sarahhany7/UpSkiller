<?php
include('includes/db/db.php'); 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $subject = trim($_POST['subject']);
  $message = trim($_POST['message']);

  $errors = [];

  if (empty($name)) {
    $errors[] = "Name is required";
  }
  if (empty($email)) {
    $errors[] = "Email is required";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
  }
  if (empty($subject)) {
    $errors[] = "Subject is required";
  }
  if (empty($message)) {
    $errors[] = "Message is required";
  }
  if (empty($errors)) {
    $stmt = $connect->prepare("INSERT INTO contactmessages (Name, Email, Subject, Message, Created_At) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $subject, $message]);

    $_SESSION['success_message'] = "Your message has been sent successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Upskillr</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

  <header>
    <nav class="navbar">
      <div class="logo">Upskillr</div>
      <button id="themeBtn">🌙</button>
      <div class="menu-toggle">☰</div>
      <ul class="nav-links">
        <li><a href="aftersign.php">Home</a></li>
        <li><a href="aboutsign.php">About Us</a></li>
        <li><a href="contactsig.php">Contact Us</a></li>
      </ul>
      <div class="nav-buttons">
        <a href="login.php" class="btn login" id="openLogin">Login</a>
        <a href="signup.php" class="btn signup" id="openSignup">Sign Up</a>
      </div>
    </nav>
  </header>

  <main class="contact-container">

    <section class="contact-header">
      <h1>We'd Love to <span class="highlight">Hear From You</span></h1>
      <p>Whether you have a question, suggestion, or just want to say hello, our team is ready to help.</p>
    </section>

    <section class="contact-details-grid">

      <div class="contact-form-card">
        <h2>Send Us a Message</h2>

        <?php
        if (!empty($_SESSION['success_message'])) {
          echo '<p class="success-msg">' . $_SESSION['success_message'] . '</p>';
          unset($_SESSION['success_message']);
        }
        if (!empty($errors)) {
          echo '<ul class="error-msg">';
          foreach ($errors as $err) {
            echo '<li>' . $err . '</li>';
          }
          echo '</ul>';
        }
        ?>

        <form action="" method="POST" id="contactForm">
          <label for="name">Your Name</label>
          <input type="text" id="name" name="name" placeholder="Enter your full name" required>

          <label for="email">Your Email</label>
          <input type="email" id="email" name="email" placeholder="e.g., example@domain.com" required>

          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" placeholder="e.g., Course Inquiry or Technical Support" required>

          <label for="message">Your Message</label>
          <textarea id="message" name="message" rows="5" placeholder="Write your detailed message here..." required></textarea>

          <button type="submit" class="btn primary-submit-btn">Send Message</button>
        </form>
      </div>


      <div class="contact-info-card">
        <h2>Contact Information</h2>
        <div class="info-item">
          <i class="fas fa-map-marker-alt"></i>
          <p>6 شارع قناة السويس، المنصورة، الدقهلية، مصر</p>
        </div>
        <div class="info-item">
          <i class="fas fa-envelope"></i>
          <p>support@upskillr.com</p>
        </div>
        <div class="info-item">
          <i class="fas fa-phone"></i>
          <p>+20 109 123 4567</p>
        </div>

        <h2>Follow Us</h2>
        <div class="social-links-contact">
          <a href="https://www.facebook.com/Google/" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/Google" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/Google/" target="_blank"><i class="fab fa-instagram"></i></a>
          <a href="https://www.linkedin.com/company/google/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>

    </section>

    <section class="map-placeholder">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1197.8385611680193!2d31.378930432549247!3d31.042578500662235!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14f7762a4d314811%3A0xf639e4073e5f76f7!2sMansoura%20University!5e0!3m2!1sen!2seg!4v1703080000000!5m2!1sen!2seg"
        width="100%"
        height="450"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
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