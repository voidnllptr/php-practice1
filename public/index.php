<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class ApiRouter {
    private $basePath = '/../api/';
    private $routes = [
        'bookings' => 'bookings.php',
        'clients' => 'clients.php',
        'countries' => 'countries.php',
        'tours' => 'tours.php'
    ];

   public function route() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $relativePath = str_replace($docRoot, '', $path);
    $segments = explode('/', trim($relativePath, '/'));
    
    $resource = $segments[1] ?? '';
    
    if (isset($this->routes[$resource])) {
        $scriptPath = __DIR__ . '/../api/' . $this->routes[$resource];
        
        if (file_exists($scriptPath)) {
            include_once $scriptPath;
        } else {
            $this->sendError(404, "API endpoint не найден: $scriptPath");
        }
    } else {
        $this->sendApiInfo();
    }
}
    
    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode([
            "error" => true,
            "code" => $code,
            "message" => $message
        ]);
        exit();
    }
    
    private function sendApiInfo() {
        http_response_code(200);
        echo json_encode([
            "api" => "Tour Booking API v1.0",
            "endpoints" => [
                "GET/POST/PUT/DELETE /api/bookings" => "Управление бронированиями",
                "GET/POST/PUT/DELETE /api/clients" => "Управление клиентами", 
                "GET/POST/PUT/DELETE /api/countries" => "Управление странами",
                "GET/POST/PUT/DELETE /api/tours" => "Управление турами",
                "?id=1" => "Получить конкретный ресурс",
                "?available=true (tours)" => "Только доступные туры"
            ],
            "status" => "online",
            "timestamp" => date('Y-m-d H:i:s')
        ]);
    }
}

try {
    $router = new ApiRouter();
    $router->route();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Внутренняя ошибка сервера: " . $e->getMessage()
    ]);
}
?>
