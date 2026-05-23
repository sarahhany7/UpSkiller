document.addEventListener('DOMContentLoaded', () => {
  
 
  const openLogin = document.getElementById("openLogin"); 
  const openSignup = document.getElementById("openSignup"); 
  const logoutBtn = document.getElementById("logoutBtn"); 
  const welcomeMessage = document.getElementById("welcomeMessage"); 
  
  const signupModal = document.getElementById("signupModal");
  const closeSignup = document.getElementById("closeSignup");
  const signupForm = document.getElementById("signupForm"); 

  
  const modalTitle = document.getElementById("modalTitle"); 
  const modalSubmitBtn = document.getElementById("modalSubmitBtn");
  
 
  function openAuthModal(isLogin) {
    if (!signupModal || !modalTitle || !modalSubmitBtn || !signupForm) return;

    if (isLogin) {
      modalTitle.textContent = "Login";
      modalSubmitBtn.textContent = "Login";
      
      signupForm.querySelector('input[type="text"]').style.display = 'none';
      signupForm.querySelector('input[type="text"]').required = false; 
    } else {
      modalTitle.textContent = "Sign Up";
      modalSubmitBtn.textContent = "Sign Up";
     
      signupForm.querySelector('input[type="text"]').style.display = 'block';
      signupForm.querySelector('input[type="text"]').required = true; 
    }
    signupModal.style.display = "block";
  }

  
  function setLoginState(isLoggedIn = false, userName = 'User') {
    if (isLoggedIn) {
      if (openLogin) openLogin.style.display = 'none';
      if (openSignup) openSignup.style.display = 'none';
      
      if (logoutBtn) logoutBtn.style.display = 'inline-block';
      if (welcomeMessage) {
        welcomeMessage.textContent = `Welcome, ${userName}!`;
        welcomeMessage.style.display = 'inline-block';
      }
    } else {
      if (openLogin) openLogin.style.display = 'inline-block';
      if (openSignup) openSignup.style.display = 'inline-block';
      
      if (logoutBtn) logoutBtn.style.display = 'none';
      if (welcomeMessage) welcomeMessage.style.display = 'none';
    }
  }

  
  if (openLogin && signupModal) {
    openLogin.addEventListener('click', () => openAuthModal(true)); 
  }
  

  if (openSignup && signupModal) {
    openSignup.addEventListener('click', () => openAuthModal(false));
  }

  
  if (closeSignup && signupModal) {
    closeSignup.addEventListener('click', () => signupModal.style.display = "none");
  }
  window.addEventListener('click', (e) => {
    if (e.target === signupModal) signupModal.style.display = "none";
  });
  
  
  if (signupForm) {
    signupForm.addEventListener('submit', (e) => {
      e.preventDefault(); 
      
      const isLoginProcess = modalTitle.textContent === "Login";
      
      let userName = 'User';
      if (!isLoginProcess) {

        const userNameInput = signupForm.querySelector('input[type="text"]');
        userName = userNameInput ? userNameInput.value : 'User';
        alert(`تم التسجيل بنجاح! مرحباً بك يا ${userName}.`);
      } else {
        
        
        alert(`تم تسجيل الدخول بنجاح! مرحباً بك يا ${userName}.`);
      }
      
      signupModal.style.display = "none"; 
      setLoginState(true, userName);
      signupForm.reset();
    });
  }

  if (logoutBtn) {
      logoutBtn.addEventListener('click', () => {
          setLoginState(false);
          alert('تم تسجيل الخروج بنجاح.');
      });
  }
  
  const menuToggle = document.querySelector('.menu-toggle');
  const navLinks = document.querySelector('.nav-links');
  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => navLinks.classList.toggle('active'));
  }

  const themeBtn = document.getElementById('themeBtn');
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark');
    if (themeBtn) themeBtn.textContent = '☀️';
  } else {
    if (themeBtn) themeBtn.textContent = '🌙';
  }

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const isDark = document.body.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      themeBtn.textContent = isDark ? '☀️' : '🌙';
    });
  }
  
  setLoginState(false);
});