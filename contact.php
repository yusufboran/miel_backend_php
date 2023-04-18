<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';


$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $sql = "SELECT * FROM contactform;";
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {

    if ($data->deneme) {
        echo "deneme doğru yazısı ";
    }
    if (!empty($data->ids)) {
        $ids = implode(",", $data->ids);
        $sql = "DELETE FROM contactform WHERE id IN ($ids)";
        // Perform the database query and handle any errors
        // Return a success message to the client
        echo json_encode(["message" => "Items deleted successfully"]);
    } else {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Missing or invalid fields"]);
    }

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (!empty($data->email) && !empty($data->message) && !empty($data->phone) && !empty($data->name)) {
        // mail sending
        // mail sending
        // mail sending

        $sql = "INSERT INTO contactform (email, name, phone, message, date) VALUES 
        ('{$data->email}', '{$data->name}', '{$data->phone}', '{$data->message}', '" . date('Y-m-d H:i:s') . "');";
    } else {
        die("Missing fields");
    }
}

if ($sql) {

    $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
    $rows = pg_fetch_all($result);
    echo json_encode($rows);
    http_response_code(200);
    pg_close($dbconn);

}