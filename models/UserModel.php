<?php
class UserModel {
    private $id;
    private $username;
    private $password;
    private $email;
    private $role;

    public function __construct($id = null, $username = '', $password = '', $email = '', $role = 'participant') {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
    }

    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }

    // Setters
    public function setUsername($username) { $this->username = $username; }
    public function setPassword($password) { $this->password = $password; }
    public function setEmail($email) { $this->email = $email; }
    public function setRole($role) { $this->role = $role; }
}
?>