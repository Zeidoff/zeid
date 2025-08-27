<?php
include_once '../../config.php';
include_once '../../controllers/UserController.php';


if (isset($_SESSION['user'])) {
    $controller = new UserController($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">Unauthorized Access</h2>
        <p class="text-lg text-gray-600 mb-6">You are not authorized to access this page.</p>
        <div class="flex justify-center space-x-4">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="<?php echo $_SESSION['user']['role'] === 'admin' ? '../back/admin_dashboard.php' : ($_SESSION['user']['role'] === 'organizer' ? '../back/organizer_dashboard.php' : 'participant_dashboard.php'); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Back to Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Login</a>
            <?php endif; ?>
            <a href="landing.php" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">Home</a>
        </div>
    </div>
</body>
</html>