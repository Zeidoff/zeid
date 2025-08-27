<?php
include '../../config.php';
include '../../controllers/ParticipationController.php';

$controller = new ParticipationController($pdo);
$result = $controller->manage($_GET['event_id'] ?? 0);

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$event = $result['data']['event'];
$participations = $result['data']['participations'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participations for <?php echo htmlspecialchars($event->getName()); ?> - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Participations for <?php echo htmlspecialchars($event->getName()); ?></h1>
            <a href="events.php" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Back to Events</a>
        </header>
        <div class="bg-gray-800 rounded-lg shadow-md overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-700">
                        <th class="p-4 font-semibold">Username</th>
                        <th class="p-4 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participations as $p): ?>
                        <tr class="border-t border-gray-700">
                            <td class="p-4"><?php echo htmlspecialchars($p['username']); ?></td>
                            <td class="p-4">
                                <form method="POST" action="cancel_participation.php">
                                    <input type="hidden" name="event_id" value="<?php echo $p['event_id']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $p['user_id']; ?>">
                                    <button type="submit" class="text-red-400 hover:underline" onclick="return confirm('Remove this participant?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>