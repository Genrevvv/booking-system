<?php    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    require 'router.php';

    $router = new Router;

    $router->add('/', function() {
        header('Location: /pages/home.php');
        exit();
    });

    // /get-bookings
    $router->add('/get-bookings', function() {
        header('Content-Type: application/json');

        $response = [];
        
        try {
            $db = new SQLite3('bookings.db');
        } 
        catch (Exception $e) {
            echo json_encode(['error' => 'database conneciton failure.']);
            return;
        }
    
        $table_name = 'bookings';
        
        $query = 'SELECT * FROM ' . $table_name;
        $result = $db->query($query);
    
        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
    
        echo json_encode($data);
    
        $db->close();    
    });

    // /submit-booking
    $router->add('/submit-booking', function() {
        header('Content-Type: application/json');

        $response = [];        
        $response['error'] = null;
        
        try {
            $db = new SQLite3('bookings.db');
        } 
        catch (Exception $e) {
            echo json_encode(['error' => 'Unable to connect to database']);
            return;
        }
    
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        $date = $data['date'];
        $pax = $data['pax'];
        $purpose = $data['purpose'];    
    
        if (!isset($date, $pax, $purpose) || (empty($date) || empty($pax) || empty($purpose))) {
            $response['success'] = false;
            echo json_encode($response);
            return;
        }
    
        $table_name = 'bookings';
        $query = sprintf(
            "INSERT INTO %s (date, pax, purpose) VALUES ('%s', %d, '%s')",
            $table_name,
            $date,
            $pax,
            $purpose
        );
    
        $success = $db->exec($query);
    
        if ($success) {
            $response['success'] = true;
            echo json_encode($response);
        }
    
        $db->close(); 
    });

    $router->dispatch($path);
?>
 