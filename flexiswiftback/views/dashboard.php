<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <p id="role"></p>

    <form id="signoutForm">
        <button type="submit">Sign Out</button>
    </form>

    <div id="loadingMessage" style="display: none;">Loading...</div>

    <script>
        // Fetch user role when the page loads
        fetch('/dashboard/data')
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {
                document.getElementById('role').textContent = "Your role is: " + data.role; // Extract the role
            })
            .catch((error) => {
                console.error('Error:', error);
            });

        document.getElementById('signoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var loadingMessage = document.getElementById('loadingMessage');
            loadingMessage.style.display = 'block'; // Show loading message when request starts

            fetch('/api/signout/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                loadingMessage.style.display = 'none';
                if (response.status === 200) {
                    window.location.href = "/signin";
                } else {
                    return response.json();
                }
            });
        });
    </script>
</body>
</html>
