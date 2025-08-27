<?php
include '../../config.php';
include '../../controllers/ParticipationController.php';

$controller = new ParticipationController($pdo);
$result = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $result = $controller->participate($_POST['event_id']);
}
if ($result['status'] === 'success') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
} else {
    echo "<script>window.location.href = 'events.php?error=" . urlencode($result['message']) . "';</script>";
}
exit;
?>