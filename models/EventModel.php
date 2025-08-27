<?php
class EventModel {
    private $id;
    private $name;
    private $description;
    private $image; 
    private $date;
    private $organizer_id;

    public function __construct($id, $name, $description, $image, $date, $organizer_id) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->date = $date;
        $this->organizer_id = $organizer_id;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getImage() { return $this->image; }     public function getDate() { return $this->date; }
    public function getOrganizerId() { return $this->organizer_id; }

    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }
    public function setImage($image) { $this->image = $image; } 
    public function setDate($date) { $this->date = $date; }
    public function setOrganizerId($organizer_id) { $this->organizer_id = $organizer_id; }
}
?>