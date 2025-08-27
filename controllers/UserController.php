<?php
     require_once '../../config.php';
     require_once '../../models/UserModel.php';

     class UserController {
         private $pdo;

         public function __construct($pdo) {
             $this->pdo = $pdo;
         }

         public function index() {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->query("SELECT * FROM users");
             $users = [];
             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 $users[] = new UserModel($row['id'], $row['username'], $row['password'], $row['email'], $row['role']);
             }
             return ['status' => 'success', 'data' => $users];
         }

         public function create($data) {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             if (empty($data['username']) || empty($data['password']) || empty($data['email']) || !in_array($data['role'], ['admin', 'organizer', 'participant']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                 return ['status' => 'error', 'message' => 'Invalid input'];
             }
             $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
             $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
             try {
                 $stmt->execute([$data['username'], $data['password'], $data['email'], $data['role']]);
                 return ['status' => 'success', 'redirect' => 'users.php'];
             } catch (PDOException $e) {
                 return ['status' => 'error', 'message' => 'Username or email already exists'];
             }
         }

         public function edit($id, $data) {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
             $stmt->execute([$id]);
             $user = $stmt->fetch(PDO::FETCH_ASSOC);
             if (!$user) {
                 return ['status' => 'error', 'message' => 'User not found'];
             }
             if (empty($data['username']) || empty($data['email']) || !in_array($data['role'], ['admin', 'organizer', 'participant']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                 return ['status' => 'error', 'message' => 'Invalid input'];
             }
             if (!empty($data['password'])) {
                 $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                 $stmt = $this->pdo->prepare("UPDATE users SET username=?, password=?, email=?, role=? WHERE id=?");
                 $stmt->execute([$data['username'], $data['password'], $data['email'], $data['role'], $id]);
             } else {
                 $stmt = $this->pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
                 $stmt->execute([$data['username'], $data['email'], $data['role'], $id]);
             }
             return ['status' => 'success', 'redirect' => 'users.php'];
         }

         public function delete($id) {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
             $stmt->execute([$id]);
             return ['status' => 'success', 'redirect' => 'users.php'];
         }

         public function login($data) {
             if (empty($data['email']) || empty($data['password']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                 return ['status' => 'error', 'message' => 'Invalid email or password'];
             }
             $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
             $stmt->execute([$data['email']]);
             $user = $stmt->fetch(PDO::FETCH_ASSOC);
             if ($user && password_verify($data['password'], $user['password'])) {
                 $_SESSION['user'] = $user;
                 if ($user['role'] === 'admin') {
                     return ['status' => 'success', 'redirect' => '../back/admin_dashboard.php'];
                 } elseif ($user['role'] === 'organizer') {
                     return ['status' => 'success', 'redirect' => '../back/organizer_dashboard.php'];
                 } else {
                     return ['status' => 'success', 'redirect' => '../front/participant_dashboard.php'];
                 }
             }
             return ['status' => 'error', 'message' => 'Invalid credentials'];
         }

         public function register($data) {
             if (empty($data['username']) || strlen($data['username']) < 3 || 
                 empty($data['password']) || strlen($data['password']) < 6 || 
                 empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                 return ['status' => 'error', 'message' => 'Invalid input: Username must be at least 3 characters, password at least 6 characters, and email must be valid'];
             }
             $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
             $data['role'] = 'participant';
             $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
             try {
                 $stmt->execute([$data['username'], $data['password'], $data['email'], $data['role']]);
                 return ['status' => 'success', 'redirect' => 'login.php?success=registered'];
             } catch (PDOException $e) {
                 return ['status' => 'error', 'message' => 'Username or email already exists'];
             }
         }

         public function logout() {
             session_destroy();
             return ['status' => 'success', 'redirect' => '../front/login.php'];
         }
     }
     ?>