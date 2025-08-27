<?php
include_once '../../config.php';
include_once '../../controllers/EventController.php';

$controller = new EventController($pdo);
$event = null;
$result = ['status' => 'none'];
$organizers = $controller->getOrganizers();

if (isset($_GET['id'])) {
    $event = $controller->getEvent($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = isset($_GET['id']) ? $controller->edit($_GET['id'], $_POST, $_FILES) : $controller->create($_POST, $_FILES);
    if ($result['status'] === 'success') {
        echo "<script>window.location.href = '{$result['redirect']}';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event ? 'Edit' : 'Create'; ?> Event - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold mb-6 text-center"><?php echo $event ? 'Edit' : 'Create'; ?> Event</h2>
        <?php if ($result['status'] === 'error'): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo htmlspecialchars($result['message']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $event ? htmlspecialchars($event->getName()) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>" placeholder="Enter event name" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block font-medium mb-2">Description</label>
                <textarea id="description" name="description" placeholder="Enter description" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo $event ? htmlspecialchars($event->getDescription()) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="image" class="block font-medium mb-2">Event Image <?php echo $event ? '(Leave blank to keep current)' : ''; ?></label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" <?php echo $event ? '' : 'required'; ?>>
                <?php if ($event && $event->getImage()): ?>
                    <img src="../<?php echo htmlspecialchars($event->getImage()); ?>" alt="Current event image" class="mt-2 w-full h-32 object-cover rounded-md">
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="date" class="block font-medium mb-2">Date</label>
                <input type="datetime-local" id="date" name="date" value="<?php echo $event ? date('Y-m-d\TH:i', strtotime($event->getDate())) : (isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''); ?>" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                <div class="mb-4">
                    <label for="organizer_id" class="block font-medium mb-2">Organizer</label>
                    <select id="organizer_id" name="organizer_id" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        <option value="">Select an organizer</option>
                        <?php foreach ($organizers as $organizer): ?>
                            <option value="<?php echo $organizer['id']; ?>" <?php echo $event && $event->getOrganizerId() == $organizer['id'] ? 'selected' : (isset($_POST['organizer_id']) && $_POST['organizer_id'] == $organizer['id'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($organizer['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 transition">Save</button>
        </form>
    </div>
</body>
</html>