<?php
class Booking {
    private $conn;
    private $table = "bookings";

    public $id;
    public $client_id;
    public $tour_id;
    public $booking_date;
    public $status;
    public $total_price;
    public $notes;
    public $created_at;
    public $client_name;
    public $tour_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT 
                    b.*,
                    c.full_name as client_name,
                    t.name as tour_name
                  FROM " . $this->table . " b
                  LEFT JOIN clients c ON b.client_id = c.id
                  LEFT JOIN tours t ON b.tour_id = t.id
                  ORDER BY b.booking_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT 
                    b.*,
                    c.full_name as client_name,
                    t.name as tour_name
                  FROM " . $this->table . " b
                  LEFT JOIN clients c ON b.client_id = c.id
                  LEFT JOIN tours t ON b.tour_id = t.id
                  WHERE b.id = ? 
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->client_id = $row['client_id'];
            $this->tour_id = $row['tour_id'];
            $this->booking_date = $row['booking_date'];
            $this->status = $row['status'];
            $this->total_price = $row['total_price'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];
            $this->client_name = $row['client_name'];
            $this->tour_name = $row['tour_name'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET client_id=:client_id, tour_id=:tour_id, 
                      booking_date=:booking_date, status=:status,
                      total_price=:total_price, notes=:notes";
        
        $stmt = $this->conn->prepare($query);
        
        $this->client_id = htmlspecialchars(strip_tags($this->client_id));
        $this->tour_id = htmlspecialchars(strip_tags($this->tour_id));
        $this->booking_date = htmlspecialchars(strip_tags($this->booking_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        
        $stmt->bindParam(":client_id", $this->client_id);
        $stmt->bindParam(":tour_id", $this->tour_id);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":notes", $this->notes);
        
        if($stmt->execute()) {
            $this->updateTourSpots(-1);
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET status=:status, notes=:notes
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function cancel() {
        $query = "UPDATE " . $this->table . " 
                  SET status='cancelled' 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->updateTourSpots(1);
            return true;
        }
        return false;
    }

    private function updateTourSpots($change) {
        $query = "UPDATE tours 
                  SET available_spots = available_spots + :change 
                  WHERE id = :tour_id 
                    AND available_spots + :change >= 0 
                    AND available_spots + :change <= max_people";
        
        $stmt = $this->conn->prepare($query);
        $change = (int)$change;
        $this->tour_id = htmlspecialchars(strip_tags($this->tour_id));
        
        $stmt->bindParam(":change", $change);
        $stmt->bindParam(":tour_id", $this->tour_id);
        $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            $this->updateTourSpots(1);
            return true;
        }
        return false;
    }
}
?>