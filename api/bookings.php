<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Booking.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if($id) {
            $booking->id = $id;
            $booking->readOne();
            
            if($booking->id != null) {
                $booking_arr = array(
                    "success" => true,
                    "data" => array(
                        "id" => $booking->id,
                        "client_id" => $booking->client_id,
                        "tour_id" => $booking->tour_id,
                        "booking_date" => $booking->booking_date,
                        "status" => $booking->status,
                        "total_price" => $booking->total_price,
                        "notes" => $booking->notes,
                        "created_at" => $booking->created_at,
                        "client_name" => $booking->client_name,
                        "tour_name" => $booking->tour_name
                    )
                );
                http_response_code(200);
                echo json_encode($booking_arr);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Бронирование не найдено."
                ));
            }
        } else {
            $stmt = $booking->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $bookings_arr = array("success" => true, "data" => array());
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $booking_item = array(
                        "id" => $row['id'],
                        "client_id" => $row['client_id'],
                        "tour_id" => $row['tour_id'],
                        "booking_date" => $row['booking_date'],
                        "status" => $row['status'],
                        "total_price" => $row['total_price'],
                        "notes" => $row['notes'],
                        "created_at" => $row['created_at'],
                        "client_name" => $row['client_name'],
                        "tour_name" => $row['tour_name']
                    );
                    array_push($bookings_arr["data"], $booking_item);
                }
                
                http_response_code(200);
                echo json_encode($bookings_arr);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Бронирования не найдены."
                ));
            }
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->client_id) && !empty($data->tour_id) && !empty($data->booking_date) && 
           !empty($data->status) && !empty($data->total_price)) {
            $booking->client_id = $data->client_id;
            $booking->tour_id = $data->tour_id;
            $booking->booking_date = $data->booking_date;
            $booking->status = $data->status;
            $booking->total_price = $data->total_price;
            $booking->notes = isset($data->notes) ? $data->notes : '';
            
            if($booking->create()) {
                http_response_code(201);
                echo json_encode(array("success" => true, "message" => "Бронирование создано."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно создать бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Невозможно создать бронирование. Данные неполные."));
        }
        break;
    
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $booking->id = $data->id;
            $booking->status = $data->status;
            $booking->notes = $data->notes;
            
            if($booking->update()) {
                http_response_code(200);
                echo json_encode(array("success" => true, "message" => "Бронирование обновлено."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно обновить бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Невозможно обновить бронирование. Укажите ID."));
        }
        break;
    
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $booking->id = $data->id;
            
            if($booking->delete()) {
                http_response_code(200);
                echo json_encode(array("success" => true, "message" => "Бронирование удалено."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно удалить бронирование."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Укажите ID бронирования для удаления."));
        }
        break;
}
?>
