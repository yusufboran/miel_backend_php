<?php
require_once 'conn.php';
require_once 'token.php';


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

    $token = $_POST['token'];

    $isTokenValid = checkTokenValidity($dbconn, $token);
    if ($isTokenValid) {

        $title = $_POST['title'];
        $image_path = $_POST['image_path'];
        $contextTr = $_POST['contextTr'];
        $contextEn = $_POST['contextEn'];

        if (!(isset($title) && isset($image_path) && isset($contextTr) && isset($contextEn))) {
            echo ("All input is required");
            return;
        }

        $sql = "SELECT image_path FROM about_page WHERE title = '" . $title . "';";
        $result = pg_query($dbconn, $sql);
        $deleteImg = pg_fetch_all($result)[0]["image_path"];

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


    } else {
        echo ("Token is invalid");
        return;
    }

}

pg_close($dbconn);