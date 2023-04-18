<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'upload/';
    $allowedMimeTypes = ['image/jpeg', 'image/png'];


    $file = $_FILES['file'];
    $fileType = $file['type'];

    if (in_array($fileType, $allowedMimeTypes)) {
        $fileName = uniqid() . '_' . $file['name'];
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {


            $filePath = "https://mielproje.com.tr/api/". $filePath;
            $response = [
                'status' => 'success',
                'url' => $filePath,
            ];
            echo json_encode($response);
            exit();
        }
    }
    $response = [
        'status' => 'error',
        'message' => 'Dosya yüklenemedi',
    ];
    echo json_encode($response);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {

    $target_dir = "upload/"; // specify target directory

    // Loop through all uploaded files
    $count = count($_FILES['images']['name']);
    for ($i = 0; $i < $count; $i++) {

        $target_file = $target_dir . basename($_FILES["images"]["name"][$i]); // get the name of the file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // get the extension of the file

        // Check if file is an image
        $check = getimagesize($_FILES["images"]["tmp_name"][$i]);
        if ($check === false) {
            http_response_code(400);
            echo 'Error: File ' . ($i + 1) . ' is not an image.';
            exit;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            http_response_code(409);
            echo 'Error: File ' . ($i + 1) . ' already exists.';
            exit;
        }

        // // Check file size
        // if ($_FILES["images"]["size"][$i] > 5000000) {
        //     http_response_code(413);
        //     echo 'Error: File ' . ($i + 1) . ' is too large.';
        //     exit;
        // }

        // Allow only certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            http_response_code(415);
            echo 'Error: Only JPG, JPEG, PNG & GIF files are allowed.';
            exit;
        }

        // Move the file to the target directory
        if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $target_file)) {
            echo "https://mielproje.com.tr/api/upload/" . htmlspecialchars(basename($_FILES["images"]["name"][$i]));
        } else {
            http_response_code(500);
            echo "Sorry, there was an error uploading your file.";
        }
    }

} else {
    http_response_code(405);
    echo 'Error: Method not allowed.';
    exit;
}
?>