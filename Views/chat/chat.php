<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
<link rel="stylesheet" href="/OnlineLanguageBuddyFinder/Views/chat/chat.css">

</head>
<body>
<script src="/OnlineLanguageBuddyFinder/Views/chat/chat.js" defer></script>

<div class="chat-container">

  <!-- TOP -->
  <div class="chat-header">
    Chatting with: <span id="chatPartnerName">Loading...</span>
  </div>

  <!-- MIDDLE -->
  <div id="chatBox" class="chat-messages"></div>

  <!-- BOTTOM -->
  <div class="chat-input">
    <input id="messageInput" type="text" placeholder="Type a message..." />
    <button id="sendBtn">Send</button>
  </div>

</div>


</body>
</html>
