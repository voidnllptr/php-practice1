<?php
class Country {
    private $conn;
    private $table = "countries";

    public $id;
    public $name;
    public $code;
    public $visa_required;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name";
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
            $this->name = $row['name'];
            $this->code = $row['code'];
            $this->visa_required = $row['visa_required'];
            $this->created_at = $row['created_at'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name=:name, code=:code, visa_required=:visa_required";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->visa_required = htmlspecialchars(strip_tags($this->visa_required));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":visa_required", $this->visa_required);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name=:name, code=:code, visa_required=:visa_required 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->visa_required = htmlspecialchars(strip_tags($this->visa_required));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":visa_required", $this->visa_required);
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