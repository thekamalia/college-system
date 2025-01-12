<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-sm p-4" style="width: 400px;">
            <h2 class="text-center mb-4">Admin Login</h2>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get the email and password values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Send login request
            fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email,
                        password
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response Data:', data);
                    if (data.authorisation && data.authorisation.token) {
                        console.log('Login successful, token:', data.authorisation.token);
                        localStorage.setItem('token', data.authorisation.token);
                        window.location.href = 'students';
                    } else {
                        console.log('Login failed:', data.message || 'Unknown error');
                        alert(data.message || 'Login failed!');
                    }
                })
                .catch(error => {
                    console.error('Error during login:', error);
                    alert('An error occurred during login.');
                });
        });
    </script>
</body>

</html>
