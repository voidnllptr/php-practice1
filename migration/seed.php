<?php
include_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {

    $stmt = $conn->query("SELECT COUNT(*) as count FROM information_schema.tables 
                         WHERE table_schema = DATABASE() 
                         AND table_name IN ('countries', 'clients', 'tours', 'bookings')");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 4) {
        $stmt = $conn->query("SELECT 
                                (SELECT COUNT(*) FROM countries) as countries_count,
                                (SELECT COUNT(*) FROM clients) as clients_count,
                                (SELECT COUNT(*) FROM tours) as tours_count,
                                (SELECT COUNT(*) FROM bookings) as bookings_count");
        $data_result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data_result['countries_count'] > 0) {
            echo "База данных уже заполнена. Завершение работы.\n";
            exit;
        }
    } else {
        echo "Таблицы не существуют.\n";
        exit;
    }

    $countries_data = [
        ['name' => 'Турция', 'code' => 'TR', 'visa_required' => false],
        ['name' => 'Египет', 'code' => 'EG', 'visa_required' => true],
        ['name' => 'Таиланд', 'code' => 'TH', 'visa_required' => true],
        ['name' => 'Испания', 'code' => 'ES', 'visa_required' => true],
        ['name' => 'Италия', 'code' => 'IT', 'visa_required' => true],
        ['name' => 'ОАЭ', 'code' => 'AE', 'visa_required' => false],
        ['name' => 'Мальдивы', 'code' => 'MV', 'visa_required' => true],
        ['name' => 'Грузия', 'code' => 'GE', 'visa_required' => false],
        ['name' => 'Кипр', 'code' => 'CY', 'visa_required' => true],
        ['name' => 'Черногория', 'code' => 'ME', 'visa_required' => false],
        ['name' => 'Доминикана', 'code' => 'DO', 'visa_required' => false],
        ['name' => 'Вьетнам', 'code' => 'VN', 'visa_required' => true],
        ['name' => 'Индонезия', 'code' => 'ID', 'visa_required' => false],
        ['name' => 'Греция', 'code' => 'GR', 'visa_required' => true],
        ['name' => 'Мексика', 'code' => 'MX', 'visa_required' => true]
    ];
    
    $country_ids = [];
    foreach ($countries_data as $country) {
        $query = "INSERT INTO countries (name, code, visa_required) VALUES (:name, :code, :visa_required)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $country['name']);
        $stmt->bindParam(':code', $country['code']);
        $stmt->bindParam(':visa_required', $country['visa_required'], PDO::PARAM_BOOL);
        $stmt->execute();
        $country_ids[] = $conn->lastInsertId();
    }
    
    echo "Добавлено " . count($countries_data) . " стран\n";
    
    $clients_data = [
        ['full_name' => 'Иванов Иван Иванович', 'passport_number' => '1234567890', 'phone' => '+79219999999', 'email' => 'ivanov@mail.ru', 'birth_date' => '1990-05-15'],
        ['full_name' => 'Петрова Мария Сергеевна', 'passport_number' => '0987654321', 'phone' => '+79881111111', 'email' => 'petrova@gmail.com', 'birth_date' => '1985-12-03'],
        ['full_name' => 'Сидоров Алексей Петрович', 'passport_number' => '1122334455', 'phone' => '+79258888876', 'email' => 'sidorov@yandex.ru', 'birth_date' => '1978-08-20'],
        ['full_name' => 'Козлова Анна Владимировна', 'passport_number' => '5566778899', 'phone' => '+79163332211', 'email' => 'kozlova@mail.ru', 'birth_date' => '1995-02-28'],
        ['full_name' => 'Морозов Дмитрий Александрович', 'passport_number' => '6677889900', 'phone' => '+79035556677', 'email' => 'morozov@gmail.com', 'birth_date' => '1982-11-10']
    ];
    
    $client_ids = [];
    foreach ($clients_data as $client) {
        $query = "INSERT INTO clients (full_name, passport_number, phone, email, birth_date) 
                  VALUES (:full_name, :passport_number, :phone, :email, :birth_date)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':full_name', $client['full_name']);
        $stmt->bindParam(':passport_number', $client['passport_number']);
        $stmt->bindParam(':phone', $client['phone']);
        $stmt->bindParam(':email', $client['email']);
        $stmt->bindParam(':birth_date', $client['birth_date']);
        $stmt->execute();
        $client_ids[] = $conn->lastInsertId();
    }
    
    echo "Добавлено " . count($clients_data) . " клиентов\n";
    
    $tours_data = [
        ['country_id' => 1, 'name' => 'Анталия: Все включено', 'description' => 'Отдых на берегу Средиземного моря', 'start_date' => '2024-06-01', 'end_date' => '2024-06-15', 'price' => 85000.00, 'max_people' => 20, 'available_spots' => 18],
        ['country_id' => 2, 'name' => 'Хургада: Дайвинг тур', 'description' => 'Погружения в Красное море', 'start_date' => '2024-07-10', 'end_date' => '2024-07-20', 'price' => 95000.00, 'max_people' => 15, 'available_spots' => 15],
        ['country_id' => 3, 'name' => 'Пхукет: Экзотика Таиланда', 'description' => 'Экскурсии по островам', 'start_date' => '2024-08-05', 'end_date' => '2024-08-19', 'price' => 120000.00, 'max_people' => 25, 'available_spots' => 22],
        ['country_id' => 4, 'name' => 'Барселона: Искусство и море', 'description' => 'Экскурсии по достопримечательностям', 'start_date' => '2024-09-01', 'end_date' => '2024-09-10', 'price' => 110000.00, 'max_people' => 18, 'available_spots' => 17],
        ['country_id' => 6, 'name' => 'Дубай: Роскошь и шопинг', 'description' => 'Небоскребы, пустыня и пляжи Персидского залива', 'start_date' => '2024-06-10', 'end_date' => '2024-06-17', 'price' => 135000.00, 'max_people' => 16, 'available_spots' => 14],
        ['country_id' => 7, 'name' => 'Мальдивы: Райские острова', 'description' => 'Отдых в бунгало над водой, снорклинг', 'start_date' => '2024-07-01', 'end_date' => '2024-07-10', 'price' => 220000.00, 'max_people' => 12, 'available_spots' => 10],
        ['country_id' => 8, 'name' => 'Тбилиси и горы', 'description' => 'Гастрономический тур и поездка в Казбеги', 'start_date' => '2024-08-12', 'end_date' => '2024-08-22', 'price' => 65000.00, 'max_people' => 20, 'available_spots' => 18],
        ['country_id' => 9, 'name' => 'Лимассол: Отдых на Кипре', 'description' => 'Пляжи, древние руины и винодельни', 'start_date' => '2024-09-05', 'end_date' => '2024-09-15', 'price' => 98000.00, 'max_people' => 18, 'available_spots' => 16]
    ];
    
    $tour_ids = [];
    foreach ($tours_data as $tour) {
        $query = "INSERT INTO tours (country_id, name, description, start_date, end_date, price, max_people, available_spots) 
                  VALUES (:country_id, :name, :description, :start_date, :end_date, :price, :max_people, :available_spots)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':country_id', $tour['country_id']);
        $stmt->bindParam(':name', $tour['name']);
        $stmt->bindParam(':description', $tour['description']);
        $stmt->bindParam(':start_date', $tour['start_date']);
        $stmt->bindParam(':end_date', $tour['end_date']);
        $stmt->bindParam(':price', $tour['price']);
        $stmt->bindParam(':max_people', $tour['max_people']);
        $stmt->bindParam(':available_spots', $tour['available_spots']);
        $stmt->execute();
        $tour_ids[] = $conn->lastInsertId();
    }
    
    echo "Добавлено " . count($tours_data) . " туров\n";
    
    $bookings_data = [
        ['client_id' => 1, 'tour_id' => 1, 'booking_date' => '2024-05-10', 'status' => 'confirmed', 'total_price' => 85000.00, 'notes' => 'Оплачено полностью'],
        ['client_id' => 2, 'tour_id' => 3, 'booking_date' => '2024-06-15', 'status' => 'pending', 'total_price' => 120000.00, 'notes' => 'Требуется виза'],
        ['client_id' => 3, 'tour_id' => 2, 'booking_date' => '2024-06-01', 'status' => 'completed', 'total_price' => 95000.00, 'notes' => 'Тур завершен'],
        ['client_id' => 4, 'tour_id' => 5, 'booking_date' => '2024-05-20', 'status' => 'confirmed', 'total_price' => 135000.00, 'notes' => 'Деловые переговоры'],
        ['client_id' => 5, 'tour_id' => 6, 'booking_date' => '2024-06-05', 'status' => 'cancelled', 'total_price' => 220000.00, 'notes' => 'Отмена по болезни']
    ];
    
    foreach ($bookings_data as $booking) {
        $query = "INSERT INTO bookings (client_id, tour_id, booking_date, status, total_price, notes) 
                  VALUES (:client_id, :tour_id, :booking_date, :status, :total_price, :notes)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':client_id', $booking['client_id']);
        $stmt->bindParam(':tour_id', $booking['tour_id']);
        $stmt->bindParam(':booking_date', $booking['booking_date']);
        $stmt->bindParam(':status', $booking['status']);
        $stmt->bindParam(':total_price', $booking['total_price']);
        $stmt->bindParam(':notes', $booking['notes']);
        $stmt->execute();
    }
    
    echo "Добавлено " . count($bookings_data) . " бронирований\n";
    echo "Заполнение базы данных успешно завершено!\n";
    
} catch(PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
?>