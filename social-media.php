<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $sql = "SELECT * FROM socialmedialist";
    try {
        $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
        $rows = pg_fetch_all($result);
        echo json_encode($rows);
        return;
    } catch (PDOException $e) {

        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $socialmedia = $data->socialmedia;
    $username = $data->username;
    $sql = "INSERT INTO socialmedialist (socialmedia, username) VALUES ('" . $socialmedia . "', '" . $username . "');";

}
if ($_SERVER['REQUEST_METHOD'] == "DELETE" && isset($data->id)) {
    $sql = "DELETE FROM socialmedialist WHERE id = $data->id";
} 
if ($_SERVER['REQUEST_METHOD'] == "PUT") {

    $id = $data->id;
    $username = $data->username;
    $sql = "UPDATE socialmedialist SET  username='" . $username . "' WHERE id=" . $id . ";";
}


$result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
if (!$result) {
    echo pg_last_error($connection);
} else {
    http_response_code(200);
    exit;
}
return;
pg_close($dbconn);