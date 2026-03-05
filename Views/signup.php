<?php
// Start session if not already started (in case this file is accessed directly)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Language Buddy Finder</title>
  <link rel="stylesheet" href="/Views/styles/main.css">
  <link rel="stylesheet" href="/Views/styles/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h2>✨ Join Us Today!</h2>
        <p>Create your account and start learning languages with native speakers</p>
      </div>

      <?php
      // Display error from session if redirected back with error
      if (isset($_SESSION['signup_error'])) {
        echo '<div id="errorMessage" class="error-message">' . htmlspecialchars($_SESSION['signup_error']) . '</div>';
        unset($_SESSION['signup_error']);
      } else {
        echo '<div id="errorMessage" class="error-message" style="display: none;"></div>';
      }
      ?>
      <div id="successMessage" class="success-message" style="display: none;"></div>

      <form id="signupForm" class="auth-form" action="index.php?page=signup_action" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Choose a username (3-50 characters)"
            required
            autocomplete="username"
            minlength="3"
            maxlength="50"
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
          >
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter your email"
            required
            autocomplete="email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          >
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Create a password (min. 6 characters)"
            required
            autocomplete="new-password"
            minlength="6"
          >
        </div>

        <div class="form-group">
          <label for="native_language">Native Language</label>
          <input 
            type="text" 
            id="native_language" 
            name="native_language" 
            placeholder="e.g., English, Spanish, French"
            required
            autocomplete="off"
            value="<?php echo isset($_POST['native_language']) ? htmlspecialchars($_POST['native_language']) : ''; ?>"
          >
        </div>

        <div class="form-group">
          <label for="learning_language">Language You're Learning</label>
          <input 
            type="text" 
            id="learning_language" 
            name="learning_language" 
            placeholder="e.g., Spanish, French, Japanese"
            required
            autocomplete="off"
            value="<?php echo isset($_POST['learning_language']) ? htmlspecialchars($_POST['learning_language']) : ''; ?>"
          >
        </div>

        <button type="submit" class="auth-button" id="signupButton">
          Create Account
        </button>
      </form>

      <div class="auth-footer">
        <p>Already have an account? <a href="/index.php?page=login">Login here</a></p>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('signupForm');
    const errorDiv = document.getElementById('errorMessage');
    const successDiv = document.getElementById('successMessage');
    const signupButton = document.getElementById('signupButton');

    // Show error message helper
    function showError(message) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'flex';
      successDiv.style.display = 'none';
      form.classList.remove('loading');
      signupButton.disabled = false;
      signupButton.textContent = 'Create Account';
      // Scroll to error
      errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Show success message helper
    function showSuccess(message) {
      successDiv.textContent = message;
      successDiv.style.display = 'flex';
      errorDiv.style.display = 'none';
    }

    // Hide messages helper
    function hideMessages() {
      errorDiv.style.display = 'none';
      successDiv.style.display = 'none';
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Hide previous messages
      hideMessages();
      
      // Get form values
      const username = document.getElementById('username').value.trim();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const nativeLanguage = document.getElementById('native_language').value.trim();
      const learningLanguage = document.getElementById('learning_language').value.trim();

      // Client-side validation
      if (!username) {
        showError('Please enter a username.');
        return;
      }

      if (username.length < 3) {
        showError('Username must be at least 3 characters long.');
        return;
      }

      if (username.length > 50) {
        showError('Username must be less than 50 characters.');
        return;
      }

      if (!email) {
        showError('Please enter your email address.');
        return;
      }

      if (!email.includes('@') || !email.includes('.')) {
        showError('Please enter a valid email address.');
        return;
      }

      if (!password) {
        showError('Please enter a password.');
        return;
      }

      if (password.length < 6) {
        showError('Password must be at least 6 characters long.');
        return;
      }

      if (!nativeLanguage) {
        showError('Please enter your native language.');
        return;
      }

      if (!learningLanguage) {
        showError('Please enter the language you want to learn.');
        return;
      }

      // Check if native and learning languages are the same
      if (nativeLanguage.toLowerCase() === learningLanguage.toLowerCase()) {
        showError('Your native language and learning language cannot be the same.');
        return;
      }

      // Show loading state
      form.classList.add('loading');
      signupButton.disabled = true;
      signupButton.textContent = 'Creating account...';

      const formData = new FormData(form);

      // Call the signup controller directly so we don't wrap the JSON/redirect in the full HTML layout
      fetch('/Controllers/signup.php', {
        method: 'POST',
        body: formData
      })
      .then(res => {
        // Check if response is JSON (error) or redirect (success)
        const contentType = res.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
          // It's a JSON error response
          return res.json().then(data => {
            throw new Error(data.message || 'Registration failed.');
          });
        } else if (res.redirected || res.status === 302 || res.status === 301) {
          // Success - redirect happened
          showSuccess('Account created successfully! Redirecting...');
          setTimeout(() => {
            // Use the redirect URL from the response if present, otherwise go to matches
            window.location.href = res.url || '/index.php?page=matches';
          }, 500);
          return null;
        } else {
          // Try to get response text
          return res.text().then(text => {
            // If we get here and it's not JSON, it might be a redirect
            if (res.ok) {
              showSuccess('Account created successfully! Redirecting...');
              setTimeout(() => {
                window.location.href = '/index.php?page=matches';
              }, 500);
              return null;
            }
            throw new Error('Registration failed. Please try again.');
          });
        }
      })
      .catch(err => {
        console.error('Signup error:', err);
        
        // Handle error messages
        let errorMessage = 'An unexpected error occurred. Please try again.';
        
        if (err.message) {
          if (err.message.includes('Email already registered') || err.message.includes('already exists')) {
            errorMessage = 'This email address is already registered. Please use a different email or try logging in.';
          } else if (err.message.includes('required') || err.message.includes('All fields')) {
            errorMessage = 'Please fill in all required fields.';
          } else if (err.message.includes('Failed to register') || err.message.includes('Database error')) {
            errorMessage = 'Unable to create your account. Please try again later.';
          } else {
            errorMessage = err.message;
          }
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
