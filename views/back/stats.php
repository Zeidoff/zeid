<?php
include '../../config.php';
include '../../controllers/EventController.php';

$controller = new EventController($pdo);
$result = $controller->stats();

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$stats = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Statistics - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Event Statistics</h1>
            <a href="admin_dashboard.php" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
        </header>
        <div class="bg-gray-800 rounded-lg shadow-md overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-700">
                        <th class="p-4 font-semibold">Event Name</th>
                        <th class="p-4 font-semibold">Participants</th>
                        <th class="p-4 font-semibold">Sales (Participations)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $stat): ?>
                        <tr class="border-t border-gray-700">
                            <td class="p-4"><?php echo htmlspecialchars($stat['name']); ?></td>
                            <td class="p-4"><?php echo $stat['participants']; ?></td>
                            <td class="p-4"><?php echo $stat['sales']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>