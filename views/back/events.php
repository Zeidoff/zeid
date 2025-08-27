<?php
include_once '../../config.php';
include_once '../../controllers/EventController.php';

$controller = new EventController($pdo);
$result = $controller->index();

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$events = $result['data'];

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteResult = $controller->delete($_GET['id']);
    if ($deleteResult['status'] === 'success') {
        echo "<script>window.location.href = '{$deleteResult['redirect']}';</script>";
        exit;
    } else {
        $error = $deleteResult['message'];
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Manage Events</h1>
            <div>
                <a href="<?php echo $_SESSION['user']['role'] === 'admin' ? 'admin_dashboard.php' : 'organizer_dashboard.php'; ?>" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
                <a href="event_form.php" class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Create Event</a>
            </div>
        </header>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="bg-gray-800 rounded-lg shadow-md overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-700">
                        <th class="p-4 font-semibold">Image</th>
                        <th class="p-4 font-semibold">Name</th>
                        <th class="p-4 font-semibold">Date</th>
                        <th class="p-4 font-semibold">Organizer</th>
                        <th class="p-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr class="border-t border-gray-700">
                            <td class="p-4">
                                <?php if ($event->image): ?>
                                    <img src="../../<?php echo htmlspecialchars($event->image); ?>" alt="<?php echo htmlspecialchars($event->name); ?>" class="w-16 h-16 object-cover rounded-md">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td class="p-4"><?php echo htmlspecialchars($event->name); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($event->date); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($event->organizer); ?></td>
                            <td class="p-4">
                                <a href="event_form.php?id=<?php echo $event->id; ?>" class="text-indigo-400 hover:underline">Edit</a>
                                <a href="?action=delete&id=<?php echo $event->id; ?>" class="ml-4 text-red-400 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
                                <a href="participations.php?event_id=<?php echo $event->id; ?>" class="ml-4 text-green-400 hover:underline">Manage Participations</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>