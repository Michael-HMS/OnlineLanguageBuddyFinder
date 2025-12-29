document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('matchesContainer');

  // Fetch matches from backend
  fetch('/OnlineLanguageBuddyFinder/Controllers/matchUsers.php')
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'success') {
        container.textContent = "Error: " + data.message;
        return;
      }

      const matches = data.matches;
      if (!matches.length) {
        container.textContent = "No matches found.";
        return;
      }

      container.innerHTML = '';
      matches.forEach(user => {
        const div = document.createElement('div');
        div.className = 'match';
        div.innerHTML = `
          <strong>${user.username}</strong> <br>
          Native: ${user.native_language} <br>
          Learning: ${user.learning_language} <br>
          <button data-user-id="${user.id}">Start Chat</button>
        `;
        container.appendChild(div);
      });

      // Handle start chat button clicks
      container.addEventListener('click', e => {
        if (e.target.tagName === 'BUTTON') {
          const otherId = e.target.dataset.userId;

          // Send request to createConversation.php
          fetch('/OnlineLanguageBuddyFinder/Controllers/createConversation.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ other_user_id: otherId })
          })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              // Redirect to chat page with conversation_id
             window.location.href = `/OnlineLanguageBuddyFinder/index.php?page=chat&conversation_id=${data.conversation_id}`;


            } else {
              alert("Error starting chat: " + data.message);
            }
          })
          .catch(err => {
            console.error("Error creating conversation:", err);
            alert("Could not start chat. See console for details.");
          });
        }
      });
    })
    .catch(err => {
      container.textContent = "Error loading matches.";
      console.error(err);
    });
});
