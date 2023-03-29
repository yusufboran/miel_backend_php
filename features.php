<?php
require_once 'conn.php';
require_once 'token.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $sql = "SELECT * FROM features;";
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $method = $_POST['method'];
    $token = $_POST['token'];

    $isTokenValid = checkTokenValidity($dbconn, $token);
    if (!$isTokenValid) {
        echo ("Token is invalid");
        return;
    }
    if ($method == "post" && !empty($_POST['title']) && !empty($_POST['trtext']) && !empty($_POST['entext'])) {
        $sql = "INSERT INTO features (title, trtext,entext) VALUES 
        ('" . $_POST['title'] . "','" . $_POST['trtext'] . "','" . $_POST['entext'] . "');";

    } else if ($method == "delete" && !empty($_POST['id'])) {
        $sql = "DELETE from features where id = '" . $_POST['id'] . "'";
    } else if ($method == "update" && !empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['trtext']) && !empty($_POST['entext'])) {
        $sql = "UPDATE features SET title='" . $_POST['title'] . "', trtext='" . $_POST['trtext'] . "',entext='" . $_POST['entext'] . "' WHERE id='" . $_POST['id'] . "'";
    } else {
        echo ("Token is invalid");
        return;
    }
}

$result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
if (!$result) {
    echo pg_last_error($connection);
} else {
    $rows = pg_fetch_all($result);
    echo json_encode($rows);
    http_response_code(200);
    exit;
}
return;
pg_close($dbconn);