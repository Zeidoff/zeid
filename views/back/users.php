<?php
include '../../config.php';
include '../../controllers/UserController.php';

$controller = new UserController($pdo);
$result = $controller->index();

if ($result['status'] === 'error') {
    echo "<script>window.location.href = '{$result['redirect']}';</script>";
    exit;
}
$users = $result['data'];

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $result = $controller->delete($_GET['id']);
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
    <title>Manage Users - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6">
    <div class="container mx-auto">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Manage Users</h1>
            <div>
                <a href="admin_dashboard.php" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Back to Dashboard</a>
                <a href="user_form.php" class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Create User</a>
            </div>
        </header>
        <div class="bg-gray-800 rounded-lg shadow-md overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-700">
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Username</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-t border-gray-700">
                            <td class="p-4"><?php echo $user->getId(); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($user->getUsername()); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($user->getRole()); ?></td>
                            <td class="p-4">
                                <a href="user_form.php?id=<?php echo $user->getId(); ?>" class="text-indigo-400 hover:underline">Edit</a>
                                <a href="?action=delete&id=<?php echo $user->getId(); ?>" class="ml-4 text-red-400 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>