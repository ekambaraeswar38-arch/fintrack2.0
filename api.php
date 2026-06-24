<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET') {
    switch ($action) {
        case 'get_data':
            // Get user settings
            $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get transactions
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
            $stmt->execute([$user_id]);
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get goals
            $stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get bills
            $stmt = $pdo->prepare("SELECT * FROM bills WHERE user_id = ? ORDER BY due_date ASC");
            $stmt->execute([$user_id]);
            $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get debts
            $stmt = $pdo->prepare("SELECT * FROM debts WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $debts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get assets
            $stmt = $pdo->prepare("SELECT * FROM assets WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get stats
            $stmt = $pdo->prepare("SELECT * FROM user_stats WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$stats) {
                $pdo->prepare("INSERT INTO user_stats (user_id) VALUES (?)")->execute([$user_id]);
                $stats = ['xp' => 0, 'level' => 1];
            }

            echo json_encode([
                'settings' => $settings,
                'transactions' => $transactions,
                'goals' => $goals,
                'bills' => $bills,
                'debts' => $debts,
                'assets' => $assets,
                'stats' => $stats
            ]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'save_settings':
            $income = $data['monthly_income'] ?? 0;
            $currency = $data['currency'] ?? 'INR';

            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, monthly_income, currency) 
                                   VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE monthly_income = ?, currency = ?");
            $stmt->execute([$user_id, $income, $currency, $income, $currency]);
            echo json_encode(['success' => true]);
            break;

        case 'add_transaction':
            $type = $data['type'];
            $name = $data['name'];
            $amount = $data['amount'];
            $category = $data['category'] ?? 'General';
            $date = $data['date'] ?? date('Y-m-d');

            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, name, amount, category, transaction_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $type, $name, $amount, $category, $date]);
            
            // Award XP
            $pdo->prepare("UPDATE user_stats SET xp = xp + 10 WHERE user_id = ?")->execute([$user_id]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'delete_transaction':
            $id = $data['id'];
            $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            echo json_encode(['success' => true]);
            break;

        case 'save_goal':
            $name = $data['name'];
            $target = $data['target_amount'];
            $savings = $data['current_savings'] ?? 0;

            $stmt = $pdo->prepare("INSERT INTO goals (user_id, name, target_amount, current_savings) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $target, $savings]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'save_bill':
            $stmt = $pdo->prepare("INSERT INTO bills (user_id, name, amount, due_date, recurring) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $data['name'], $data['amount'], $data['due_date'], $data['recurring']]);
            echo json_encode(['success' => true]);
            break;

        case 'pay_bill':
            $stmt = $pdo->prepare("UPDATE bills SET is_paid = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$data['id'], $user_id]);
            $pdo->prepare("UPDATE user_stats SET xp = xp + 50 WHERE user_id = ?")->execute([$user_id]);
            echo json_encode(['success' => true]);
            break;

        case 'save_debt':
            $stmt = $pdo->prepare("INSERT INTO debts (user_id, name, total_amount, interest_rate, min_payment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $data['name'], $data['total_amount'], $data['interest_rate'], $data['min_payment']]);
            echo json_encode(['success' => true]);
            break;

        case 'save_asset':
            $stmt = $pdo->prepare("INSERT INTO assets (user_id, name, type, value) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $data['name'], $data['type'], $data['value']]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}
?>
