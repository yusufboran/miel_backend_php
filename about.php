<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';

$data = json_decode(file_get_contents("php://input"));


if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $sql = "SELECT * FROM about_page";
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


    $title = $data->title;
    $image_path = $data->image_path;
    $contextTr = $data->contextTr;
    $contextEn = $data->contextEn;

    if (!(isset($title) && isset($image_path) && isset($contextTr) && isset($contextEn))) {
        echo ("All input is required");
        exit;
    }

    $sql = "SELECT image_path FROM about_page WHERE title = '" . $title . "';";
    $result = pg_query($dbconn, $sql);
    $deleteImg = pg_fetch_all($result)[0]["image_path"];

    var_dump($sql);
    $sql = "UPDATE about_page SET image_path = '$image_path', context = '$contextTr', context_tr = '$contextTr', context_en = '$contextEn' WHERE title = '$title'";

    try {
        $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
        $rows = pg_fetch_all($result);
        echo json_encode($rows);
        http_response_code(200);

        echo "upload/" . $deleteImg . "";
        try {
            unlink("upload/" . $deleteImg . ""); // $deleteImg
        } catch (\Throwable $th) {
            throw $th;
        }


        exit;
    } catch (PDOException $e) {

        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }



}

pg_close($dbconn);