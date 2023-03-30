<?php
require_once 'conn.php';
require_once 'token.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    // $token = $_POST['token'];
    // $isTokenValid = checkTokenValidity($dbconn, $token);
    // if (!$isTokenValid) {
    //     die("Token is invalid");
    // }
    switch ($_POST['method']) {
        case 'register':
            if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['username'])) {

                $sql = "SELECT * FROM public.userlist WHERE email =  '" . $_POST['email'] . "';";
                $result = pg_query($dbconn, $sql);
                $user = pg_fetch_all($result);

                if ($user) {
                    echo "Böyle bir hesap mevcut";
                    return;
                }

                $passHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

                $sql = "INSERT INTO userlist (email, password,username) VALUES ('" . $_POST['email'] . "', '" . $passHash . "','" . $_POST['username'] . "');";
                echo $sql;
            } else {
                die("Missing fields");
            }
            break;
        case 'login':
            if (!empty($_POST['email']) && !empty($_POST['password'])) {

                $sql = "SELECT * FROM public.userlist WHERE email =  '" . $_POST['email'] . "';";
                $result = pg_query($dbconn, $sql);
                $user = pg_fetch_all($result);

                if (!$user) {
                    echo "Böyle bir hesap yok";
                    return;
                }

                if (password_verify($_POST['password'], $user[0]["password"])) {


                    $response = array(
                        'token' => tokenGenerator($dbconn),
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