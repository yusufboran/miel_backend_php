<?php
require_once 'conn.php';
require_once 'token.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $sql = "SELECT * FROM contactform;";
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {

    $isTokenValid = checkTokenValidity($dbconn, $_POST['token']);
    if (!$isTokenValid) {
        die("Token is invalid");
    }

    switch ($_POST['method']) {

        case 'post':
            if (!empty($_POST['email']) && !empty($_POST['message']) && !empty($_POST['phone']) && !empty($_POST['name'])) {
                // mail sending
                // mail sending
                // mail sending

                $sql = "INSERT INTO contactform (email, name, phone, message, date) VALUES 
                ('{$_POST['email']}', '{$_POST['name']}', '{$_POST['phone']}', '{$_POST['message']}', '" . date('Y-m-d H:i:s') . "');";
            } else {
                die("Missing fields");
            }
            break;
        case 'delete':
            if (!empty($_POST['id'])) {
                $sql = "DELETE FROM contactform WHERE id = '{$_POST['id']}'";
            } else {
                die("Missing fields");
            }
            break;

        default:
            die("Invalid method");
    }
} else {
    die("Invalid request method");
}

$result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
$rows = pg_fetch_all($result);
echo json_encode($rows);
http_response_code(200);
pg_close($dbconn);