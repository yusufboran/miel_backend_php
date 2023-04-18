<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';

$data = json_decode(file_get_contents("php://input"));


var_dump($data );

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // $token = $_POST['token'];
    // $isTokenValid = checkTokenValidity($dbconn, $token);
    // if (!$isTokenValid) {
    //     die("Token is invalid");
    // }

    switch ($data->method) {
        case 'register':
            if (!empty($data->email) && !empty($data->password) && !empty($data->username)) {

                $sql = "SELECT * FROM public.userlist WHERE email =  '" . $data->email . "';";
                $result = pg_query($dbconn, $sql);
                $user = pg_fetch_all($result);

                if ($user) {
                    echo "Böyle bir hesap mevcut";
                    return;
                }

                $passHash = password_hash($data->password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO userlist (email, password,username) VALUES ('" . $data->email . "', '" . $passHash . "','" . $data->username . "');";
            } else {
                die("Missing fields");
            }
            break;
        case 'login':
            if (!empty($data->email) && !empty($data->password)) {
                if (isset($data->method) && $data->method === 'login') {

                    if (!empty($data->email) && !empty($data->password)) {
                        $sql = "SELECT * FROM public.userlist WHERE email =  '" . $data->email . "';";
                        $result = pg_query($dbconn, $sql);
                        $user = pg_fetch_all($result);
                        if (!$user) {
                            echo "Böyle bir hesap yok";
                            return;
                        }
                        if (password_verify($data->password, $user[0]["password"])) {


                            $response = array(
                                'token' => bin2hex(random_bytes(8)),
                                'email' => $user[0]["email"],
                                'username' => $user[0]["username"]
                            );
                            header('Content-Type: application/json');
                            echo json_encode($response);
                            http_response_code(200);
                            exit;
                        } else {
                            echo 'Invalid password.';
                        }
                    }
                } else
                    echo "received data";

            } else {
                die("Missing fields");
            }
            break;
        case 'update':
            if (!empty($data->id) && !empty($data->title) && !empty($data->trtext) && !empty($data->entext)) {
                $sql = "UPDATE features SET title='{$data->title}', trtext='{$data->trtext}',entext='{$data->entext}' WHERE id='{$data->id}'";
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