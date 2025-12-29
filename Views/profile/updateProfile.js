document.addEventListener('DOMContentLoaded', () => {
document.getElementById('profileForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('/OnlineLanguageBuddyFinder/Controllers/updateProfile.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    const statusDiv = document.getElementById('updateStatus');

    if (data.status === 'success') {
      statusDiv.textContent = `Updated: ${data.updated_fields.join(', ')}`;
    } 
    else if (data.status === 'noop') {
      statusDiv.textContent = "Nothing changed.";
    } 
    else {
      statusDiv.textContent = "Error: " + data.message;
    }
  })
  .catch(err => console.error(err));
});
});
