<?php    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    switch ($path) {
        case '':
        case '/':
            header("Location: /pages/home.php");
            exit;
        default:
            header("Location: /pages/404.php");
            exit;
    }
?>
 