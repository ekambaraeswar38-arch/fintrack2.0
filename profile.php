<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Get current data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch();

$income = $settings['monthly_income'] ?? 0;
$currency = $settings['currency'] ?? 'INR';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_profile') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);
        $_SESSION['username'] = $username;
        header("Location: profile.php?success=1");
        exit();
    } elseif ($action == 'update_financials') {
        $income = $_POST['monthly_income'];
        $currency = $_POST['currency'];
        
        $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, monthly_income, currency) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE monthly_income = ?, currency = ?");
        $stmt->execute([$user_id, $income, $currency, $income, $currency]);
        header("Location: profile.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - FinTrack 2.0</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="display: block; height: auto;">
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="profile-container">
        <a href="dashboard.php" class="nav-item" style="display: inline-flex; width: auto; margin-bottom: 20px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="profile-card">
            <div class="auth-header">
                <h1 class="logo">User<span>Profile</span></h1>
                <p>Manage your account and financial defaults.</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 15px; text-align: center; margin-bottom: 30px;">
                    Changes saved successfully!
                </div>
            <?php endif; ?>

            <div class="profile-grid">
                <section class="profile-section">
                    <h3>Account Info</h3>
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="Username" required>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
                        </div>
                        <button type="submit" class="primary-btn">Update Profile</button>
                    </form>
                </section>

                <section class="profile-section">
                    <h3>Financial Defaults</h3>
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="action" value="update_financials">
                        <div class="input-group">
                            <i class="fas fa-wallet"></i>
                            <input type="number" step="0.01" name="monthly_income" value="<?php echo $income; ?>" placeholder="Default Monthly Income" required>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-coins"></i>
                            <select name="currency" class="row-input" style="padding-left: 45px; width: 100%;">
                                <option value="INR" <?php echo $currency == 'INR' ? 'selected' : ''; ?>>INR (₹)</option>
                                <option value="USD" <?php echo $currency == 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                <option value="EUR" <?php echo $currency == 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                <option value="GBP" <?php echo $currency == 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                <option value="AED" <?php echo $currency == 'AED' ? 'selected' : ''; ?>>AED (Dh)</option>
                            </select>
                        </div>
                        <button type="submit" class="primary-btn">Update Financials</button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
