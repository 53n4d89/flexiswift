<!-- HTML code for the forgot password form -->
<?php

if (function_exists('opcache_reset')) {
    opcache_reset();
}
 ?>

<!-- HTML code for the registration form -->
<h1>SignUp</h1>

<form id="myForm">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <label for="repeat_password">Repeat Password:</label>
  <input type="password" id="repeat_password" name="repeat_password" required>

  <button type="submit">Register</button>
</form>
<div id="errors"></div>

<div>
  <button onclick="window.location.href = '/signin';">Sign In</button>
</div>

<script>
document.getElementById('myForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var username = document.getElementById('username');
  var email = document.getElementById('email');
  var password = document.getElementById('password');
  var repeat_password = document.getElementById('repeat_password');

  fetch('/api/signup/user/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      username: username.value,
      email: email.value,
      password: password.value,
      repeat_password: repeat_password.value
    })
  })
  .then(response => {
    if (response.status === 201) {
      window.location.href = "/usersignupsuccess";
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
        case 'username':
          inputElement = username;
          break;
        case 'email':
          inputElement = email;
          break;
        case 'password':
          inputElement = password;
          break;
        case 'repeat_password':
          inputElement = repeat_password;
          break;
        case 'apologize':
          inputElement = repeat_password;
          break;
        case 'failed':
          inputElement = repeat_password;
          break;
      }

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
