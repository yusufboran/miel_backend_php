<?php
require_once 'conn.php';
require_once 'token.php';
 
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

    $method = $_POST['method'];
    $token = $_POST['token'];

    $isTokenValid = checkTokenValidity($dbconn, $token);
    if (!$isTokenValid) {
        echo ("Token is invalid");
        return;
    }

    if ($method == "post") {

        $socialmedia = $_POST['socialmedia'];
        $username = $_POST['username'];
        
        if (!(isset($socialmedia) && isset($username) )) {
            echo ("All input is required");
            return;
        }
        $sql = "INSERT INTO socialmedialist (socialmedia, username) VALUES ('" . $socialmedia . "', '" . $username . "');";
    }
    if ($method == "delete") {

        $id = $_POST['id'];

        if (!(isset($id))) {
            echo ("All input is required");
            return;
        }

        $sql = "DELETE FROM socialmedialist where id = " . $id . ";";
    }

    if ($method == "update") {

        $id = $_POST['id'];
        $username = $_POST['username'];

        if (!(isset($id) && isset($username)  )) {
            echo ("All input is required");
            return;
        }
        $sql = "UPDATE socialmedialist SET  username='".$username."' WHERE id=" . $id . ";";
    }

    $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
    if (!$result) {
        echo pg_last_error($connection);
    } else {
        http_response_code(200);
        exit;
    }
    return;
}
pg_close($dbconn);