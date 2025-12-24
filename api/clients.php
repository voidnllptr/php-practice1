<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../models/Client.php';

$database = new Database();
$db = $database->getConnection();
$client = new Client($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if($id) {
            $client->id = $id;
            $client->readOne();
            
            if($client->full_name != null) {
                $client_arr = array(
                    "success" => true,
                    "data" => array(
                        "id" => $client->id,
                        "full_name" => $client->full_name,
                        "passport_number" => $client->passport_number,
                        "phone" => $client->phone,
                        "email" => $client->email,
                        "birth_date" => $client->birth_date,
                        "created_at" => $client->created_at
                    )
                );
                http_response_code(200);
                echo json_encode($client_arr);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Клиент не найден."
                ));
            }
        } else {
            $stmt = $client->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $clients_arr = array("success" => true, "data" => array());
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $client_item = array(
                        "id" => $row['id'],
                        "full_name" => $row['full_name'],
                        "passport_number" => $row['passport_number'],
                        "phone" => $row['phone'],
                        "email" => $row['email'],
                        "birth_date" => $row['birth_date'],
                        "created_at" => $row['created_at']
                    );
                    array_push($clients_arr["data"], $client_item);
                }
                
                http_response_code(200);
                echo json_encode($clients_arr);
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Клиенты не найдены."
                ));
            }
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->full_name) && !empty($data->passport_number) && !empty($data->phone) && !empty($data->email) && !empty($data->birth_date)) {
            $client->full_name = $data->full_name;
            $client->passport_number = $data->passport_number;
            $client->phone = $data->phone;
            $client->email = $data->email;
            $client->birth_date = $data->birth_date;

            if($client->create()) {
                http_response_code(201);
                echo json_encode(array("success" => true, "message" => "Клиент создан."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно создать клиента."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Невозможно создать клиента. Данные неполные."));
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $client->id = $data->id;
            $client->full_name = $data->full_name;
            $client->passport_number = $data->passport_number;
            $client->phone = $data->phone;
            $client->email = $data->email;
            $client->birth_date = $data->birth_date;
            
            if($client->update()) {
                http_response_code(200);
                echo json_encode(array("success" => true, "message" => "Клиент обновлен."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно обновить клиента."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Невозможно обновить клиента. Данные неполные."));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $client->id = $data->id;
            
            if($client->delete()) {
                http_response_code(200);
                echo json_encode(array("success" => true, "message" => "Клиент удален."));
            } else {
                http_response_code(503);
                echo json_encode(array("success" => false, "message" => "Невозможно удалить клиента."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("success" => false, "message" => "Укажите ID клиента для удаления."));
        }
        break;
}
?>
