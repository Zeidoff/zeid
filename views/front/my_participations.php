<?php
include '../../config.php';
include '../../controllers/ParticipationController.php';

$controller = new ParticipationController($pdo);
$result = $controller->myParticipations();

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$participations = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Participations - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Participations</h1>
            <p class="text-gray-600">Manage your event participations</p>
        </div>

        <!-- Participations Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-4 px-6 text-left font-semibold text-gray-700">Event Name</th>
                            <th class="py-4 px-6 text-left font-semibold text-gray-700">Participation Date</th>
                            <th class="py-4 px-6 text-left font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participations as $p): ?>
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="py-4 px-6"><?php echo htmlspecialchars($p['name']); ?></td>
                                <td class="py-4 px-6 text-gray-500">
                                    <?php echo isset($p['participation_date']) ? htmlspecialchars($p['participation_date']) : 'N/A'; ?>
                                </td>
                                <td class="py-4 px-6">
                                    <form method="POST" action="cancel_participation.php" onsubmit="return confirm('Are you sure you want to cancel your participation in this event?');">
                                        <input type="hidden" name="event_id" value="<?php echo $p['event_id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($participations)): ?>
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">No participations yet</h3>
                    <p class="text-gray-500 mb-4">You haven't registered for any events yet.</p>
                    <a href="events.php" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Browse Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 EventSphere. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>