<!-- HTML code for the forgot password form -->
<h1>Forgot Password</h1>
<form id="fPassForm">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>
  <button type="submit">Submit</button>
</form>
<div id="errors"></div>
<div id="loadingMessage" style="display: none;">Loading...</div>
<div>
  <button onclick="window.location.href = '/signin';">Sign In</button>
</div>

<script>
document.getElementById('fPassForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var email = document.getElementById('email');

  var loadingMessage = document.getElementById('loadingMessage');
  loadingMessage.style.display = 'block'; // Show loading message when request starts

  fetch('/api/forgotpassword/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: email.value,
    })
  })
  .then(response => {
    loadingMessage.style.display = 'none';
    if (response.status === 201) {
      window.location.href = "/forgotpasswordsucess";
    } else {
      return response.json();
    }
  })
  .then(data => {
    // Clear any existing error messages
    document.querySelectorAll('.error-message').forEach(function(errorMessage) {
      errorMessage.remove();
    });

    // Display new error messages
    Object.keys(data).forEach(function(key) {
      let errorElement = document.createElement('p');
      errorElement.textContent = data[key];
      errorElement.className = 'error-message';

      let inputElement = email;

      if (inputElement) {
        inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
      }
    });
  })
  .catch((error) => {
    console.error('Error:', error);
  });
});
</script>
