<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';


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
    sendMail($data->email, $data->message, $data->phone, $data->name);

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

function sendMail($email, $message, $phone, $name)
{
  $mail = new PHPMailer(true);

  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SetLanguage("tr", "phpmailer/language");
    $mail->CharSet = "utf-8";

    $mail->Username = 'yusuf.boran@rbbt.com.tr'; // YOUR gmail email
    $mail->Password = "qyygdxyzzivrodvi"; // YOUR gmail password

    // Sender and recipient settings
    $mail->setFrom('email@mail.com', $name);
    $mail->addAddress('ybsrfn@gmail.com', 'Receiver Name');
    //  $mail->addReplyTo('example@gmail.com', 'Sender Name'); // to set the reply to

    // Setting the email content
    $mail->IsHTML(true);
    $mail->Subject = "Send email using Gmail SMTP and PHPMailer";
    $mail->Body =
      '<html lang="tr">

      <head>
          <style>
              @import url(https://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100);
      
              div.table-title {
                  display: block;
                  margin: auto;
                  max-width: 600px;
                  padding: 5px;
                  width: 100%;
              }
      
              .table-title h3 {
                  color: #fafafa;
                  font-size: 30px;
                  font-weight: 400;
                  font-style: normal;
                  font-family: "Roboto", helvetica, arial, sans-serif;
                  text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.1);
                  text-transform: uppercase;
              }
      
              /*** Table Styles **/
      
              .table-fill {
                  background: white;
                  border-radius: 3px;
                  border-collapse: collapse;
                  height: 320px;
                  margin: auto;
                  max-width: 600px;
                  padding: 5px;
                  width: 100%;
                  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                  animation: float 5s infinite;
              }
      
              th {
                  color: #D5DDE5;
                  ;
                  background: #1b1e24;
                  border-bottom: 4px solid #9ea7af;
                  border-right: 1px solid #343a45;
                  font-size: 23px;
                  font-weight: 100;
                  padding: 24px;
                  text-align: left;
                  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                  vertical-align: middle;
              }
      
              th:first-child {
                  border-top-left-radius: 3px;
              }
      
              th:last-child {
                  border-top-right-radius: 3px;
                  border-right: none;
              }
      
              tr {
                  border-top: 1px solid #C1C3D1;
                  border-bottom-: 1px solid #C1C3D1;
                  color: #666B85;
                  font-size: 16px;
                  font-weight: normal;
                  text-shadow: 0 1px 1px rgba(256, 256, 256, 0.1);
              }
      
              tr:first-child {
                  border-top: none;
              }
      
              tr:last-child {
                  border-bottom: none;
              }
      
              tr:nth-child(odd) td {
                  background: #EBEBEB;
              }
      
              tr:last-child td:first-child {
                  border-bottom-left-radius: 3px;
              }
      
              tr:last-child td:last-child {
                  border-bottom-right-radius: 3px;
              }
      
              td {
                  background: #FFFFFF;
                  padding: 20px;
                  text-align: left;
                  vertical-align: middle;
                  font-weight: 300;
                  font-size: 18px;
                  text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.1);
                  border-right: 1px solid #C1C3D1;
              }
      
              td:last-child {
                  border-right: 0px;
              }
      
              th.text-left {
                  text-align: left;
              }
      
              td.text-left {
                  text-align: left;
              }
          </style>
          <meta charset="utf-8" />
          <title>Table Style</title>
          <meta name="viewport" content="initial-scale=1.0; maximum-scale=1.0; width=device-width;">
      </head>
      
      <body>
          <table class="table-fill">
              <thead>
                  <tr>
                      <th class="text-left">İsim</th>
                      <th class="text-left">E-mail</th>
                      <th class="text-left">Telefon Numarası</th>
                  </tr>
              </thead>
              <tbody class="table-hover">
                  <tr>
                      <td class="text-left" style="white-space: nowrap;">' . $name . '</td>
                      <td class="text-left" style="white-space: nowrap;">' . $email . '</td>
                      <td class="text-left" style="white-space: nowrap;"><a href={"tel:"' . $phone . '"}> ' . $phone . '</a></td>
            </tr>
            </tbody>
            
            <thead>
            <tr>
            <th colspan ="4"class="text-left">Mesaj</th>
            </tr>
            </thead>
            <tbody class="table-hover">
            <tr>
            <td colspan ="4" class="text-left">' . $message . '</td>
            </tr>
            </tbody>
            </table>
              </body>
              </html>';

    $mail->send();
    echo "Email message sent.";
  } catch (Exception $e) {
    echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
  }

}