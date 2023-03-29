<?php
require_once 'conn.php';
require_once 'token.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $sql = "SELECT * FROM features;";
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    $method = $_POST['method'];
    $token = $_POST['token'];

    $isTokenValid = checkTokenValidity($dbconn, $token);
    if (!$isTokenValid) {
        die("Token is invalid");
    }

    switch ($method) {
        case 'post':
            if (!empty($_POST['title']) && !empty($_POST['trtext']) && !empty($_POST['entext'])) {
                $sql = "INSERT INTO features (title, trtext,entext) VALUES ('{$_POST['title']}', '{$_POST['trtext']}', '{$_POST['entext']}')";
            } else {
                die("Missing fields");
            }
            break;
        case 'delete':
            if (!empty($_POST['id'])) {
                $sql = "DELETE FROM features WHERE id = '{$_POST['id']}'";
            } else {
                die("Missing fields");
            }
            break;
        case 'update':
            if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['trtext']) && !empty($_POST['entext'])) {
                $sql = "UPDATE features SET title='{$_POST['title']}', trtext='{$_POST['trtext']}',entext='{$_POST['entext']}' WHERE id='{$_POST['id']}'";
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
