<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Language Buddy Finder</title>
  <link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/styles/main.css">
  <link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/styles/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h2>🔑 Welcome Back!</h2>
        <p>Login to continue your language learning journey</p>
      </div>

      <div id="errorMessage" class="error-message" style="display: none;"></div>
      <div id="successMessage" class="success-message" style="display: none;"></div>

      <form id="loginForm" class="auth-form">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter your email"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Enter your password"
            required
            autocomplete="current-password"
          >
        </div>

        <button type="submit" class="auth-button" id="loginButton">
          Login
        </button>
      </form>

      <div class="auth-footer">
        <p>Don't have an account? <a href="/OnlineLanguageBuddyFinder/index.php?page=signup">Sign up here</a></p>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('loginForm');
    const errorDiv = document.getElementById('errorMessage');
    const successDiv = document.getElementById('successMessage');
    const loginButton = document.getElementById('loginButton');

    // Helper function to show error message
    function showError(message) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'flex';
      successDiv.style.display = 'none';
      form.classList.remove('loading');
      loginButton.disabled = false;
      loginButton.textContent = 'Login';
    }

    // Helper function to show success message
    function showSuccess(message) {
      successDiv.textContent = message;
      successDiv.style.display = 'flex';
      errorDiv.style.display = 'none';
    }

    // Helper function to hide all messages
    function hideMessages() {
      errorDiv.style.display = 'none';
      successDiv.style.display = 'none';
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Hide previous messages
      hideMessages();
      
      // Get form values
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      // Client-side validation
      if (!email) {
        showError('Please enter your email address.');
        return;
      }

      if (!email.includes('@')) {
        showError('Please enter a valid email address.');
        return;
      }

      if (!password) {
        showError('Please enter your password.');
        return;
      }

  

      // Show loading state
      form.classList.add('loading');
      loginButton.disabled = true;
      loginButton.textContent = 'Logging in...';

      const formData = new FormData(form);

      fetch('/OnlineLanguageBuddyFinder/Controllers/login.php', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        // Check if response is JSON
        const contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
          return res.json();
        } else {
          // If not JSON, it might be an error page
          throw new Error('Server returned an invalid response. Please try again.');
        }
      })
      .then(data => {
        if (data.status === 'success') {
          showSuccess('Login successful! Redirecting...');
          // Redirect after a short delay
          setTimeout(() => {
            window.location.href = '/OnlineLanguageBuddyFinder/index.php?page=matches';
          }, 1000);
        } else {
          // Handle different error types with user-friendly messages
          let errorMessage = 'Login failed. Please check your credentials and try again.';
          
          if (data.message) {
            if (data.message.includes('Invalid email') || data.message.includes('Invalid password')) {
              errorMessage = 'The email or password you entered is incorrect. Please try again.';
            } else if (data.message.includes('required')) {
              errorMessage = 'Please fill in all required fields.';
            } else if (data.message.includes('Unauthorized')) {
              errorMessage = 'You must be logged in to access this page.';
            } else {
              errorMessage = data.message;
            }
          }
          
          showError(errorMessage);
        }
      })
      .catch(err => {
        console.error('Login error:', err);
        let errorMessage = 'An unexpected error occurred. Please try again.';
        
        if (err.message.includes('fetch')) {
          errorMessage = 'Unable to connect to the server. Please check your internet connection and try again.';
        } else if (err.message) {
          errorMessage = err.message;
        }
        
        showError(errorMessage);
      });
    });

    // Clear error messages when user starts typing
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('input', () => {
        if (errorDiv.style.display === 'flex') {
          hideMessages();
        }
      });
    });
  </script>
</body>
</html>
