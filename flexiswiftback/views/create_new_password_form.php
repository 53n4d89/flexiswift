<!-- HTML code for the forgot password form -->
<h1>Create New Password</h1>
<form id="nPassword">
  <label for="email">Enter password:</label>
  <input type="password" id="password" name="password" required>
  <label for="email">Confirm password:</label>
  <input type="password" id="repeat_password" name="repeat_password" required>
  <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($token); ?>">
  <button type="submit">Submit</button>
</form>
<div id="errors"></div>
<div id="loadingMessage" style="display: none;">Loading...</div>
<div>
  <button onclick="window.location.href = '/signin';">Sign In</button>
</div>

<script>
document.getElementById('nPassword').addEventListener('submit', function(e) {
  e.preventDefault();

  var password = document.getElementById('password');
  var confirm_password = document.getElementById('repeat_password');
  var token = document.getElementById('token');

  var loadingMessage = document.getElementById('loadingMessage');
  loadingMessage.style.display = 'block'; // Show loading message when request starts

  fetch('/api/createnewpassword/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      password: password.value,
      repeat_password: repeat_password.value,
      token: token.value
    })
  })
  .then(response => {
    loadingMessage.style.display = 'none';
    if (response.status === 201) {
      window.location.href = "/newpasswordsuccess";
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

      let inputElement;
      switch (key) {
        case 'password':
          inputElement = password;
          break;
        case 'repeat_password':
          inputElement = repeat_password;
          break;
      }

      if (inputElement) {
        inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
      }
    });
  })
  .catch((error) => {
    loadingMessage.style.display = 'none'; // Hide loading message when request finishes
    console.error('Error:', error);
  });
});
</script>
