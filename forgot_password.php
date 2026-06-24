<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FinTrack 2.0</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <main class="auth-container">
        <div class="glass-card">
            <div class="auth-header">
                <h1 class="logo">Recover<span>Access</span></h1>
                <p>Enter your email to reset your password.</p>
            </div>

            <form action="forgot_password.php" method="POST" class="auth-form active">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <button type="submit" class="primary-btn">Send Reset Link</button>
                <div class="form-footer" style="justify-content: center; margin-top: 20px;">
                    <a href="index.php" class="forgot-link">Back to Login</a>
                </div>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                echo "<p style='color: #4facfe; text-align: center; margin-top: 20px;'>If this email is registered, you will receive a reset link shortly.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
