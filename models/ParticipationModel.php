<?php
class ParticipationModel {
    private $id;
    private $userId;
    private $eventId;

    public function __construct($id = null, $userId = null, $eventId = null) {
        $this->id = $id;
        $this->userId = $userId;
        $this->eventId = $eventId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getEventId() { return $this->eventId; }

    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setEventId($eventId) { $this->eventId = $eventId; }
}
?>