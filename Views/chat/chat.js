document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chatBox');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatPartnerName = document.getElementById("chatPartnerName");

    // Get conversation_id from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentConversationId = urlParams.get('conversation_id');

    function loadMessages() {
        if (!currentConversationId) return;

        fetch(`/OnlineLanguageBuddyFinder/Controllers/messages.php?conversation_id=${currentConversationId}`)
        .then(res => res.json())
        .then(data => {
            const { messages, otherUser, loggedInUserId } = data; // backend returns loggedInUserId

            chatPartnerName.textContent = otherUser?.username || "Unknown";
            chatBox.innerHTML = "";

            messages.forEach(msg => {
                const div = document.createElement("div");
                div.className = msg.sender_id == loggedInUserId ? "me" : "them"; // compare to session user
                div.innerHTML = `<strong>${msg.username}:</strong> ${msg.message}`;
                chatBox.appendChild(div);
            });

            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(err => console.error("Error loading messages:", err));
    }

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message || !currentConversationId) return;

        const formData = new FormData();
        formData.append('conversation_id', currentConversationId);
        formData.append('message', message);

        fetch('/OnlineLanguageBuddyFinder/Controllers/send.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                messageInput.value = '';
                loadMessages();
            }
        })
        .catch(err => console.error("Error sending message:", err));
    }

    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') sendMessage();
    });

    setInterval(loadMessages, 2000);
    loadMessages();
});
