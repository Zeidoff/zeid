<?php
include '../../config.php';
include '../../controllers/UserController.php';

$controller = new UserController($pdo);
$result = $controller->logout();
echo "<script>window.location.href = '{$result['redirect']}';</script>";
exit;
?>