<?php
     require_once '../../config.php';
     require_once '../../models/ParticipationModel.php';
     require_once '../../models/EventModel.php';

     class ParticipationController {
         private $pdo;

         public function __construct($pdo) {
             $this->pdo = $pdo;
         }

         public function participate($event_id) {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'participant') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("SELECT * FROM participations WHERE user_id = ? AND event_id = ?");
             $stmt->execute([$_SESSION['user']['id'], $event_id]);
             if ($stmt->fetch()) {
                 return ['status' => 'error', 'message' => 'Already participating'];
             }
             $stmt = $this->pdo->prepare("INSERT IGNORE INTO participations (user_id, event_id) VALUES (?, ?)");
             $stmt->execute([$_SESSION['user']['id'], $event_id]);
             return ['status' => 'success', 'redirect' => '../front/events.php'];
         }

         public function cancel($event_id, $user_id = null) {
             if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'participant' && $_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'organizer')) {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $user_id = $user_id ?? $_SESSION['user']['id'];
             $stmt = $this->pdo->prepare("DELETE FROM participations WHERE user_id = ? AND event_id = ?");
             $stmt->execute([$user_id, $event_id]);
             return ['status' => 'success', 'redirect' => $_SESSION['user']['role'] === 'participant' ? '../front/my_participations.php' : '../back/participations.php?event_id=' . $event_id];
         }

         public function manage($event_id) {
             if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'organizer')) {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
             $stmt->execute([$event_id]);
             $event = $stmt->fetch(PDO::FETCH_ASSOC);
             if ($_SESSION['user']['role'] === 'organizer' && $event['organizer_id'] !== $_SESSION['user']['id']) {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("SELECT p.*, u.username FROM participations p LEFT JOIN users u ON p.user_id = u.id WHERE event_id = ?");
             $stmt->execute([$event_id]);
             $participations = $stmt->fetchAll(PDO::FETCH_ASSOC);
             return ['status' => 'success', 'data' => ['event' => new EventModel($event['id'], $event['name'], $event['description'],$event['image'], $event['date'], $event['organizer_id']), 'participations' => $participations], 'template' => '../back/participations.php'];
         }

         public function myParticipations() {
             if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'participant') {
                 return ['status' => 'error', 'message' => 'Unauthorized', 'redirect' => '../front/unauthorized.php'];
             }
             $stmt = $this->pdo->prepare("SELECT p.*, e.name FROM participations p LEFT JOIN events e ON p.event_id = e.id WHERE user_id = ?");
             $stmt->execute([$_SESSION['user']['id']]);
             $participations = $stmt->fetchAll(PDO::FETCH_ASSOC);
             return ['status' => 'success', 'data' => $participations, 'template' => '../front/my_participations.php'];
         }
     }
     ?>