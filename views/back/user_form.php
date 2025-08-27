<?php
include '../../config.php';
include '../../controllers/UserController.php';

$controller = new UserController($pdo);
$user = null;
$result = ['status' => 'none'];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $user = $user ? new UserModel($user['id'], $user['username'], $user['password'], $user['email'], $user['role']) : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = isset($_GET['id']) ? $controller->edit($_GET['id'], $_POST) : $controller->create($_POST);
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
    <title><?php echo $user ? 'Edit' : 'Create'; ?> User - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold mb-6 text-center"><?php echo $user ? 'Edit' : 'Create'; ?> User</h2>
        <?php if ($result['status'] === 'error'): ?>
            <p class="text-red-500 mb-4 text-center"><?php echo $result['message']; ?></p>
        <?php endif; ?>
        <form method="POST" onsubmit="return validateUserForm()">
            <div class="mb-4">
                <label for="username" class="block font-medium mb-2">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $user ? htmlspecialchars($user->getUsername()) : ''; ?>" placeholder="Enter username" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user ? htmlspecialchars($user->getEmail()) : ''; ?>" placeholder="Enter email" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block font-medium mb-2">Role</label>
                <select id="role" name="role" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="admin" <?php echo $user && $user->getRole() === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="organizer" <?php echo $user && $user->getRole() === 'organizer' ? 'selected' : ''; ?>>Organizer</option>
                    <option value="participant" <?php echo $user && $user->getRole() === 'participant' ? 'selected' : ''; ?>>Participant</option>
                </select>
            </div>
            <div class="mb-6">
                <label for="password" class="block font-medium mb-2">Password <?php echo $user ? '(Leave blank to keep current)' : ''; ?></label>
                <input type="password" id="password" name="password" placeholder="Enter password" class="w-full p-3 border rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" <?php echo $user ? '' : 'required'; ?>>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 transition">Save</button>
        </form>
    </div>
    <script>
        function validateUserForm() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            if (username.length < 3 || !email.match(/^\S+@\S+\.\S+$/) <?php echo $user ? '' : '|| password.length < 6'; ?>) {
                alert('Invalid input: Username min 3 chars, valid email, password min 6 chars');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>