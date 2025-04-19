<?php    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    require 'router.php';

    $router = new Router;

    $router->add('/', function() {
        header('Location: /pages/home.html');
        exit();
    });

    # /clear-table
    $router->add('/clear-table', function() {
        header('Content-Type: appplication/json');

        $db =connectDB();

        $db->exec('DELETE FROM bookings');
        $db->close();

        echo json_encode(['success' => true]);
    });

    # /get-bookings
    $router->add('/get-bookings', function() {
        header('Content-Type: application/json');

        $response = [];
        
        $db = connectDB();
        $db->exec('CREATE TABLE IF NOT EXISTS bookings (
                        id INTEGER PRIMARY KEY,
                        date TEXT,
                        pax INTEGER, 
                        purpose TEXT
                    )');
    
        $result = $db->query('SELECT * FROM bookings');
    
        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }

        $db->close();    

        echo json_encode($data);
    });

    # /submit-booking
    $router->add('/submit-booking', function() {
        header('Content-Type: application/json');

        $response = [];        
        $response['error'] = null;
        
        $db = connectDB();
    
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        $date = $data['date'] ?? null;
        $pax = $data['pax'] ?? null;
        $purpose = $data['purpose'] ?? null;    
            
        if ((empty($date) || empty($pax) || empty($purpose))) {
            $response['success'] = false;
            echo json_encode($response);
            return;
        }

        $purposes = ['Chicken Jockey', 'Rest', 'Vacation', 'Other'];

        if (!in_array($purpose, $purposes)) {
            $response['error'] = 'Invalid purpose';
            echo json_encode($response);
            return;
        }
    
        $stmt = $db->prepare("INSERT INTO bookings (date, pax, purpose) VALUES (:date, :pax, :purpose)");
        $stmt->bindValue(':date', $date, SQLITE3_TEXT);
        $stmt->bindValue(':pax', $pax, SQLITE3_INTEGER);
        $stmt->bindValue(':purpose', $purpose, SQLITE3_TEXT);
        
        $stmt->execute();
        $affectedRows = $db->changes();

        $db->close(); 

        if ($affectedRows > 0) {
            $response['success'] = true;
            echo json_encode($response);
        }
    });

    function connectDB() {
        try {
            return new SQLite3('bookings.db');
        }
        catch (Exception $e) {
            echo json_encode(['error' => 'Unable to connect to database']);
            exit();
        }
    }
    
    $router->dispatch($path);
?>
 
