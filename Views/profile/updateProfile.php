<link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/styles/auth.css">

<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <h2>👤 Update Your Profile</h2>
      <p>Keep your information up to date to find better matches</p>
    </div>

    <div id="errorMessage" class="error-message" style="display: none;"></div>
    <div id="successMessage" class="success-message" style="display: none;"></div>

    <form id="profileForm" class="auth-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          placeholder="Enter your username"
          required
          autocomplete="username"
          minlength="3"
          maxlength="50"
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
        >
      </div>

      <button type="submit" class="auth-button" id="updateButton">
        Update Profile
      </button>
    </form>

    <div class="auth-footer">
      <p><a href="/OnlineLanguageBuddyFinder/index.php?page=matches">← Back to Matches</a></p>
    </div>
  </div>
</div>

<script src="/OnlineLanguageBuddyFinder/Views/profile/updateProfile.js?v=20260227"></script>
