<h1>Signin</h1>
<form id="signinForm">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required>

  <button type="submit">Signin</button>
</form>
<div id="signinErrors"></div>
<div id="loadingMessage" style="display: none;">Loading...</div>

<div>
  <button onclick="window.location.href = '/signup';">Sign Up</button>
  <button onclick="window.location.href = '/forgotpassword';">Forgot Password</button>
</div>

<script>
document.getElementById('signinForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var email = document.getElementById('email');
  var password = document.getElementById('password');

  if (!email.value.includes('@') || password.value === '') {
    var errorMessage = document.createElement('p');
    errorMessage.textContent = 'Please enter a valid email and password.';
    document.getElementById('signinErrors').appendChild(errorMessage);
    return;
  }

  var loadingMessage = document.getElementById('loadingMessage');
  loadingMessage.style.display = 'block'; // Show loading message when request starts

  fetch('/api/signin/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      email: email.value,
      password: password.value
    })
  })
  .then(response => {
    loadingMessage.style.display = 'none'; // Hide loading message when request finishes
    if (!response.ok) {
      return response.json();
    }
    return response.json();
  })
  .then(data => {
    if (data.token) {
      // The server should set the JWT as an HttpOnly cookie, so we just need to redirect to the dashboard.
      window.location.href = "/dashboard";
    } else {
      // Clear any existing error messages
      document.querySelectorAll('.signin-error-message').forEach(function(errorMessage) {
        errorMessage.remove();
      });

      // Display new error messages
      let errorElement = document.createElement('p');
      errorElement.textContent = data.message;
      errorElement.className = 'signin-error-message';

      document.getElementById('signinErrors').appendChild(errorElement);
    }
  })
  .catch((error) => {
    loadingMessage.style.display = 'none'; // Hide loading message when request finishes
    console.log('Error in signin request:', error); // Added console log

    // Show a user-friendly error message
    var errorMessage = document.createElement('p');
    errorMessage.textContent = error.message;
    document.getElementById('signinErrors').appendChild(errorMessage);
  });
});
</script>
