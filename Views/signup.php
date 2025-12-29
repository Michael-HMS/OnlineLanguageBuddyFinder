<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup - Language Buddy Finder</title>
</head>
<body>
  <h2>Signup</h2>
  <form action="index.php?page=signup_action" method="POST">

    <label>
      Username:<br>
      <input type="text" name="username" required>
    </label>
    <br><br>
    
    <label>
      Email:<br>
      <input type="email" name="email" required>
    </label>
    <br><br>
    
    <label>
      Password:<br>
      <input type="password" name="password" required>
    </label>
    <br><br>
    
    <label>
      Native Language:<br>
      <input type="text" name="native_language" required>
    </label>
    <br><br>
    
    <label>
      Learning Language:<br>
      <input type="text" name="learning_language" required>
    </label>
    <br><br>
    
    <button type="submit">Signup</button>
  </form>
</body>
</html>
