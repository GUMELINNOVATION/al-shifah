<?php
// process_donation.php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    $amount = (float)($data['amount'] ?? 0);
    $donorName = trim($data['donor_name'] ?? 'Generous Donor');
    $isAnonymous = (bool)($data['is_anonymous'] ?? false);
    $campaignId = isset($data['campaign_id']) ? (int)$data['campaign_id'] : null;
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $paymentRef = trim($data['payment_reference'] ?? '');

    if ($amount <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid amount']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO donations (donor_name, payment_reference, amount, donation_date, campaign_id, user_id, is_anonymous) VALUES (?, ?, ?, CURDATE(), ?, ?, ?)");
        $stmt->execute([$donorName, $paymentRef, $amount, $campaignId, $userId, $isAnonymous ? 1 : 0]);

        // If campaign specified, optionally update campaign's current_amount
        if ($campaignId) {
            $stmt = $pdo->prepare("UPDATE campaigns SET current_amount = current_amount + ? WHERE id = ?");
            $stmt->execute([$amount, $campaignId]);
        }

        echo json_encode(['success' => true, 'message' => 'Donation recorded successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
