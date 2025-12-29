<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <h2>Login</h2>
  <form id="loginForm">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
  </form>
  <div id="loginStatus"></div>

  <script>
    const form = document.getElementById('loginForm');
    const statusDiv = document.getElementById('loginStatus');

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch('/OnlineLanguageBuddyFinder/Controllers/login.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if(data.status === 'success') {
          // Redirect to matches page
          window.location.href = '/OnlineLanguageBuddyFinder/index.php?action=matches';
        } else {
          statusDiv.textContent = 'Login failed: ' + (data.message || 'Check your credentials.');
        }
      })
      .catch(err => {
        console.error(err);
        statusDiv.textContent = 'An error occurred. Try again.';
      });
    });
  </script>
</body>
</html>
