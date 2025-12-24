<?php
class Client {
    private $conn;
    private $table = "clients";

    public $id;
    public $full_name;
    public $passport_number;
    public $phone;
    public $email;
    public $birth_date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->full_name = $row['full_name'];
            $this->passport_number = $row['passport_number'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->birth_date = $row['birth_date'];
            $this->created_at = $row['created_at'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET full_name=:full_name, passport_number=:passport_number, 
                      phone=:phone, email=:email, birth_date=:birth_date";
        
        $stmt = $this->conn->prepare($query);
        
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->passport_number = htmlspecialchars(strip_tags($this->passport_number));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":passport_number", $this->passport_number);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":birth_date", $this->birth_date);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET full_name=:full_name, passport_number=:passport_number, 
                      phone=:phone, email=:email, birth_date=:birth_date 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->passport_number = htmlspecialchars(strip_tags($this->passport_number));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":passport_number", $this->passport_number);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>