document.addEventListener('click', (e) => {
  if (e.target.classList.contains('delete-button')) {
    const chatMessage = e.target.closest('.chat-message');
    const messageId = chatMessage.dataset.messageId;

    // Debugging: Log the message ID
    console.log(`Deleting message with ID: ${messageId}`);

    if (confirm('Are you sure you want to delete this message?')) {
      fetch('delete_message.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `message_id=${messageId}`,
      })
        .then((response) => response.json())
        .then((data) => {
          console.log(data); // Debug response from server
          if (data.success) {
            alert('Message deleted successfully.');
            chatMessage.remove(); // Remove the message from the DOM
          } else {
            alert(data.error || 'Failed to delete the message.');
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          alert('An error occurred while deleting the message.');
        });
    }
  }
});
