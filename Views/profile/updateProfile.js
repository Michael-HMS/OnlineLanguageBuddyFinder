document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('profileForm');
  const errorDiv = document.getElementById('errorMessage');
  const successDiv = document.getElementById('successMessage');
  const updateButton = document.getElementById('updateButton');
  let redirectTimer = null;

  // Helper functions — profile uses text-only loading (no CSS spinner) so it never gets stuck
  function clearLoadingState() {
    form.classList.remove('loading');
    if (updateButton) {
      updateButton.classList.remove('is-loading');
      updateButton.disabled = false;
      updateButton.textContent = 'Update Profile';
    }
  }

  function showError(message) {
    errorDiv.textContent = message;
    errorDiv.style.display = 'flex';
    successDiv.style.display = 'none';
    clearLoadingState();
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function showSuccess(message) {
    successDiv.textContent = message;
    successDiv.style.display = 'flex';
    errorDiv.style.display = 'none';
    clearLoadingState();
    
  }

  function hideMessages() {
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';
  }

  // Load current user data
  function loadUserData() {
    fetch('/Controllers/getProfile.php')
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const user = data.user;
          document.getElementById('username').value = user.username || '';
          document.getElementById('email').value = user.email || '';
          document.getElementById('native_language').value = user.native_language || '';
          document.getElementById('learning_language').value = user.learning_language || '';
        } else {
          showError('Unable to load your profile data. Please refresh the page.');
        }
      })
      .catch(err => {
        console.error('Error loading profile:', err);
        showError('Unable to load your profile data. Please refresh the page.');
      });
  }

  // Load user data on page load
  loadUserData();

  // Handle form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    hideMessages();
    
    // Get form values
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
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

    // Show loading state without spinner CSS classes
    updateButton.disabled = true;
    updateButton.textContent = 'Updating...';

    const formData = new FormData(form);

    fetch('/Controllers/updateProfile.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        showSuccess('Profile updated successfully! Redirecting to matches...');
        if (redirectTimer) {
          clearTimeout(redirectTimer);
        }
        redirectTimer = setTimeout(() => {
          window.location.href = '/index.php?page=matches';
        }, 1200);
      } 
      else if (data.status === 'noop') {
        showError('No changes detected. Please modify at least one field before updating.');
      } 
      else {
        let errorMessage = 'Unable to update profile. Please try again.';
        
        if (data.message) {
          if (data.message.includes('Unauthorized')) {
            errorMessage = 'You must be logged in to update your profile. Please log in again.';
          } else if (data.message.includes('not found')) {
            errorMessage = 'User account not found. Please contact support.';
          } else {
            errorMessage = data.message;
          }
        }
        
        showError(errorMessage);
      }
    })
    .catch(err => {
      console.error('Update error:', err);
      let errorMessage = 'An unexpected error occurred. Please try again.';
      
      if (err.message.includes('fetch')) {
        errorMessage = 'Unable to connect to the server. Please check your internet connection.';
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
});
