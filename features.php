<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';
require_once 'token.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $sql = "SELECT * FROM features;";
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!empty($data->title) && !empty($data->trtext) && !empty($data->entext)) {
        $sql = "INSERT INTO features (title, trtext,entext) VALUES ('{$data->title}', '{$data->trtext}', '{$data->entext}')";
    } else {
        die("Missing fields");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    if (!empty($data->id)) {
        $sql = "DELETE FROM features WHERE id = '{$data->id}'";
    } else {
        die("Missing fields");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "update") {

    if (!empty($data->id) && !empty($data->title) && !empty($data->trtext) && !empty($data->entext)) {
        $sql = "UPDATE features SET title='{$data->title}', trtext='{$data->trtext}',entext='{$data->entext}' WHERE id='{$data->id}'";
    } else {
        die("Missing fields");
    }
}



$result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
$rows = pg_fetch_all($result);
echo json_encode($rows);
http_response_code(200);
pg_close($dbconn);