<?php
require_once '../../config.php';
require_once '../../models/EventModel.php';

class EventController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        $role = $_SESSION['user']['role'];
        if ($role === 'admin') {
            $stmt = $this->pdo->query("SELECT e.*, u.username as organizer FROM events e LEFT JOIN users u ON e.organizer_id = u.id");
            $events = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = (object) [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $row['image'],
                    'date' => $row['date'],
                    'organizer_id' => $row['organizer_id'],
                    'organizer' => $row['organizer']
                ];
            }
            return ['status' => 'success', 'data' => $events, 'template' => '../back/events.php'];
        } elseif ($role === 'organizer') {
            $stmt = $this->pdo->prepare("SELECT e.*, u.username as organizer FROM events e LEFT JOIN users u ON e.organizer_id = u.id WHERE organizer_id = ?");
            $stmt->execute([$_SESSION['user']['id']]);
            $events = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = (object) [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $row['image'],
                    'date' => $row['date'],
                    'organizer_id' => $row['organizer_id'],
                    'organizer' => $row['organizer']
                ];
            }
            return ['status' => 'success', 'data' => $events, 'template' => '../back/events.php'];
        } else {
            $stmt = $this->pdo->query("SELECT e.*, u.username as organizer FROM events e LEFT JOIN users u ON e.organizer_id = u.id");
            $events = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = (object) [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $row['image'],
                    'date' => $row['date'],
                    'organizer_id' => $row['organizer_id'],
                    'organizer' => $row['organizer']
                ];
            }
            return ['status' => 'success', 'data' => $events, 'template' => '../front/events.php'];
        }
    }

    public function create($data, $file) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'organizer')) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        // Validation
        if (empty($data['name']) || strlen($data['name']) < 5 || empty($data['date']) || strtotime($data['date']) < time()) {
            return ['status' => 'error', 'message' => 'Invalid input: Name must be at least 5 characters, and date must be in the future'];
        }
        // Validate organizer_id
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'organizer'");
        $stmt->execute([$data['organizer_id']]);
        if (!$stmt->fetch()) {
            return ['status' => 'error', 'message' => 'Invalid organizer selected'];
        }
        // Handle image upload
        $imagePath = null;
        if (!empty($file['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            if (!in_array($file['image']['type'], $allowedTypes) || $file['image']['size'] > $maxSize) {
                return ['status' => 'error', 'message' => 'Invalid image: Must be JPEG, PNG, or GIF and less than 5MB'];
            }
            $ext = pathinfo($file['image']['name'], PATHINFO_EXTENSION);
            $imagePath = 'uploads/event_' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($file['image']['tmp_name'], '../../' . $imagePath)) {
                return ['status' => 'error', 'message' => 'Failed to upload image'];
            }
        }
        $data['organizer_id'] = $_SESSION['user']['role'] === 'organizer' ? $_SESSION['user']['id'] : $data['organizer_id'];
        $stmt = $this->pdo->prepare("INSERT INTO events (name, description, image, date, organizer_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $imagePath, $data['date'], $data['organizer_id']]);
        return ['status' => 'success', 'redirect' => 'events.php'];
    }

    public function edit($id, $data, $file) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'organizer')) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            return ['status' => 'error', 'message' => 'Event not found'];
        }
        if ($_SESSION['user']['role'] === 'organizer' && $event['organizer_id'] !== $_SESSION['user']['id']) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        // Validation
        if (empty($data['name']) || strlen($data['name']) < 5 || empty($data['date']) || strtotime($data['date']) < time()) {
            return ['status' => 'error', 'message' => 'Invalid input: Name must be at least 5 characters, and date must be in the future'];
        }
        // Validate organizer_id
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'organizer'");
        $stmt->execute([$data['organizer_id']]);
        if (!$stmt->fetch()) {
            return ['status' => 'error', 'message' => 'Invalid organizer selected'];
        }
        // Handle image upload
        $imagePath = $event['image'];
        if (!empty($file['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            if (!in_array($file['image']['type'], $allowedTypes) || $file['image']['size'] > $maxSize) {
                return ['status' => 'error', 'message' => 'Invalid image: Must be JPEG, PNG, or GIF and less than 5MB'];
            }
            $ext = pathinfo($file['image']['name'], PATHINFO_EXTENSION);
            $imagePath = 'uploads/event_' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($file['image']['tmp_name'], '../' . $imagePath)) {
                return ['status' => 'error', 'message' => 'Failed to upload image'];
            }
            // Delete old image if exists
            if ($event['image'] && file_exists('../' . $event['image'])) {
                unlink('../' . $event['image']);
            }
        }
        $data['organizer_id'] = $_SESSION['user']['role'] === 'organizer' ? $_SESSION['user']['id'] : $data['organizer_id'];
        $stmt = $this->pdo->prepare("UPDATE events SET name=?, description=?, image=?, date=?, organizer_id=? WHERE id=?");
        $stmt->execute([$data['name'], $data['description'], $imagePath, $data['date'], $data['organizer_id'], $id]);
        return ['status' => 'success', 'redirect' => 'events.php'];
    }

    public function delete($id) {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'organizer')) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($_SESSION['user']['role'] === 'organizer' && $event['organizer_id'] !== $_SESSION['user']['id']) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        // Delete image if exists
        if ($event['image'] && file_exists('../' . $event['image'])) {
            unlink('../' . $event['image']);
        }
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return ['status' => 'success', 'redirect' => 'events.php'];
    }

    public function stats() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        $stmt = $this->pdo->query("SELECT e.id, e.name, COUNT(p.id) as participants, COUNT(p.id) as sales FROM events e LEFT JOIN participations p ON e.id = p.event_id GROUP BY e.id");
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['status' => 'success', 'data' => $stats, 'template' => '../back/stats.php'];
    }

    public function getEvent($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        return $event ? new EventModel($event['id'], $event['name'], $event['description'], $event['image'], $event['date'], $event['organizer_id']) : null;
    }

    public function getOrganizers() {
        $stmt = $this->pdo->query("SELECT id, username FROM users WHERE role = 'organizer'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     public function getEventDetails($id) {
        if (!isset($_SESSION['user'])) {
            return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
        }
        $stmt = $this->pdo->prepare("SELECT e.*, u.username as organizer FROM events e LEFT JOIN users u ON e.organizer_id = u.id WHERE e.id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) {
            return ['status' => 'error', 'message' => 'Event not found', 'redirect' => '../front/events.php'];
        }
        return ['status' => 'success', 'data' => (object) [
            'id' => $event['id'],
            'name' => $event['name'],
            'description' => $event['description'],
            'image' => $event['image'],
            'date' => $event['date'],
            'organizer_id' => $event['organizer_id'],
            'organizer' => $event['organizer']
        ], 'template' => '../front/event_details.php'];
    }
    public function createEvent($data, $file) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organizer') {
        return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
    }
    
    // Validation
    if (empty($data['name']) || strlen($data['name']) < 5 || 
        empty($data['date']) || strtotime($data['date']) < time()) {
        return ['status' => 'error', 'message' => 'Invalid input: Name must be at least 5 characters, and date must be in the future'];
    }
    
    // Handle image upload
    $imagePath = null;
    if (!empty($file['image']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['image']['type'], $allowedTypes) || $file['image']['size'] > $maxSize) {
            return ['status' => 'error', 'message' => 'Invalid image: Must be JPEG, PNG, or GIF and less than 5MB'];
        }
        
        $ext = pathinfo($file['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'uploads/event_' . uniqid() . '.' . $ext;
        
        if (!move_uploaded_file($file['image']['tmp_name'], '../../' . $imagePath)) {
            return ['status' => 'error', 'message' => 'Failed to upload image'];
        }
    }
    
    // Set organizer_id to current user
    $data['organizer_id'] = $_SESSION['user']['id'];
    
    // Insert into database
    $stmt = $this->pdo->prepare("INSERT INTO events (name, description, image, date, organizer_id) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['name'], 
            $data['description'], 
            $imagePath, 
            $data['date'], 
            $data['organizer_id']
        ]);
        
        return ['status' => 'success', 'message' => 'Event created successfully', 'redirect' => 'organizer_dashboard.php'];
    } catch (PDOException $e) {
        // Delete uploaded file if database insert failed
        if ($imagePath && file_exists('../../' . $imagePath)) {
            unlink('../../' . $imagePath);
        }
        
        return ['status' => 'error', 'message' => 'Failed to create event: ' . $e->getMessage()];
    }
}
public function updateEvent($post, $files) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organizer') {
        return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
    }

    $id = $post['id'];
    $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        return ['status' => 'error', 'message' => 'Event not found'];
    }
    if ($_SESSION['user']['role'] === 'organizer' && $event['organizer_id'] !== $_SESSION['user']['id']) {
        return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
    }

    // Validation
    if (empty($post['name']) || strlen($post['name']) < 5 || 
        empty($post['date']) || strtotime($post['date']) < time()) {
        return ['status' => 'error', 'message' => 'Invalid input: Name must be at least 5 characters, and date must be in the future'];
    }

    // Handle image upload (reusing createEvent logic)
    $imagePath = $event['image'];
    if (!empty($files['image']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        if (!in_array($files['image']['type'], $allowedTypes) || $files['image']['size'] > $maxSize) {
            return ['status' => 'error', 'message' => 'Invalid image: Must be JPEG, PNG, or GIF and less than 5MB'];
        }
        $ext = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
        $imagePath = 'Uploads/event_' . uniqid() . '.' . $ext;
        $uploadDir = dirname(__DIR__, 2) . '/zeid/Uploads/';
        $uploadPath = $uploadDir . basename($imagePath);
        
        // Create Uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return ['status' => 'error', 'message' => 'Failed to create Uploads directory'];
            }
        }

        if (!move_uploaded_file($files['image']['tmp_name'], $uploadPath)) {
            return ['status' => 'error', 'message' => 'Failed to upload image'];
        }
        // Delete old image if exists
        if ($event['image'] && file_exists(dirname(__DIR__, 2) . '/zeid/' . $event['image'])) {
            unlink(dirname(__DIR__, 2) . '/zeid/' . $event['image']);
        }
    }

    // Update database
    $stmt = $this->pdo->prepare("UPDATE events SET name = ?, description = ?, date = ?, image = ? WHERE id = ?");
    try {
        $stmt->execute([
            $post['name'],
            $post['description'] ?? '',
            $post['date'],
            $imagePath,
            $id
        ]);
        return ['status' => 'success', 'message' => 'Event updated successfully', 'redirect' => 'organizer_dashboard.php'];
    } catch (PDOException $e) {
        if ($imagePath && $imagePath !== $event['image'] && file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        return ['status' => 'error', 'message' => 'Failed to update event: ' . $e->getMessage()];
    }
}
}
?>