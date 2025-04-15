<?php
    header('Content-Type: application/json');

    $response = [];
    
    try {
        $db = new SQLite3('../bookings.db');
    } catch (Exception $e) {
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
?>