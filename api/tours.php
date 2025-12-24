<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Tour.php';

$database = new Database();
$db = $database->getConnection();
$tour = new Tour($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $available = isset($_GET['available']) ? $_GET['available'] : null;
        
        if($id) {
            $tour->id = $id;
            $tour->readOne();
            
            if($tour->id != null) {
                $tour_arr = array(
                    "id" => $tour->id,
                    "country_id" => $tour->country_id,
                    "name" => $tour->name,
                    "description" => $tour->description,
                    "start_date" => $tour->start_date,
                    "end_date" => $tour->end_date,
                    "price" => $tour->price,
                    "max_people" => $tour->max_people,
                    "available_spots" => $tour->available_spots,
                    "created_at" => $tour->created_at,
                    "country_name" => $tour->country_name
                );
                
                http_response_code(200);
                echo json_encode($tour_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Тур не найден."));
            }
        } elseif($available === 'true') {
            $stmt = $tour->readAvailable();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $tours_arr = array();
                $tours_arr["data"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $tour_item = array(
                        "id" => $id,
                        "country_id" => $country_id,
                        "name" => $name,
                        "description" => $description,
                        "start_date" => $start_date,
                        "end_date" => $end_date,
                        "price" => $price,
                        "max_people" => $max_people,
                        "available_spots" => $available_spots,
                        "created_at" => $created_at,
                        "country_name" => $country_name
                    );
                    array_push($tours_arr["data"], $tour_item);
                }
                
                http_response_code(200);
                echo json_encode($tours_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Доступные туры не найдены."));
            }
        } else {
            $stmt = $tour->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $tours_arr = array();
                $tours_arr["data"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $tour_item = array(
                        "id" => $id,
                        "country_id" => $country_id,
                        "name" => $name,
                        "description" => $description,
                        "start_date" => $start_date,
                        "end_date" => $end_date,
                        "price" => $price,
                        "max_people" => $max_people,
                        "available_spots" => $available_spots,
                        "created_at" => $created_at,
                        "country_name" => $country_name
                    );
                    array_push($tours_arr["data"], $tour_item);
                }
                
                http_response_code(200);
                echo json_encode($tours_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Туры не найдены."));
            }
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->country_id) && !empty($data->name) && !empty($data->start_date) && 
           !empty($data->end_date) && !empty($data->price) && !empty($data->max_people) &&
           !empty($data->available_spots)) {
            $tour->country_id = $data->country_id;
            $tour->name = $data->name;
            $tour->description = isset($data->description) ? $data->description : '';
            $tour->start_date = $data->start_date;
            $tour->end_date = $data->end_date;
            $tour->price = $data->price;
            $tour->max_people = $data->max_people;
            $tour->available_spots = $data->available_spots;
            
            if($tour->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Тур создан."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно создать тур."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно создать тур. Данные неполные."));
        }
        break;
    
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $tour->id = $data->id;
            $tour->country_id = $data->country_id;
            $tour->name = $data->name;
            $tour->description = $data->description;
            $tour->start_date = $data->start_date;
            $tour->end_date = $data->end_date;
            $tour->price = $data->price;
            $tour->max_people = $data->max_people;
            $tour->available_spots = $data->available_spots;
            
            if($tour->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Тур обновлен."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно обновить тур."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Невозможно обновить тур. Укажите ID."));
        }
        break;
    
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $tour->id = $data->id;
            
            if($tour->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Тур удален."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Невозможно удалить тур."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Укажите ID тура для удаления."));
        }
        break;
}
?>
