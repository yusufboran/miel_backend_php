<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
require_once 'conn.php';


$data = json_decode(file_get_contents("php://input"));


if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $last_segment = basename(rtrim($_SERVER['REQUEST_URI'], '/'));
    if ($last_segment !== "project.php") {

        $sql = "SELECT p.id, p.projectname, p.descriptionen, p.descriptiontr, p.features, p.created_at, p.pid, 
        array_agg(json_build_object('id', pf.id, 'image_path', pf.image_path)) AS paths 
        FROM project p 
        INNER JOIN project_file pf 
        ON p.pid = pf.project  
        WHERE p.id ='" . $last_segment . "'
        GROUP BY p.id, p.projectname, p.descriptionen, p.descriptiontr, p.features, p.created_at, p.pid;";

    } else {

        $sql = "SELECT p.id, p.projectname, p.descriptionen, p.descriptiontr, p.features, p.created_at, p.pid, 
        array_agg(json_build_object('id', pf.id, 'image_path', pf.image_path)) AS paths 
        FROM project p 
        INNER JOIN project_file pf 
        ON p.pid = pf.project 
        GROUP BY p.id, p.projectname, p.descriptionen, p.descriptiontr, p.features, p.created_at, p.pid;";
    }

    $result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());
    $rows = pg_fetch_all($result);

    $arr = [];

    if ($rows) {
        foreach ($rows as $row) {
            $item = new stdClass();
            $item->id = $row['id'];
            $item->projectname = $row['projectname'];
            $item->descriptionen = $row['descriptionen'];
            $item->descriptiontr = $row['descriptiontr'];

            $features_str = str_replace(['{', '}'], '', $row['features']);
            $features_arr = explode(',', $features_str);

            $jsonString = "{\"Havalimanına yakın\",\"Hastaneye yakın\"}";
            $array = json_decode('[' . $features_str . ']', true);
            $item->features = $array;

            $item->created_at = $row['created_at'];
            $item->pid = $row['pid'];

            $paths = array();
            $paths_str = preg_replace('/[\{\}\\\\\"]/', '', $row['paths']);
            $paths_str = preg_replace('/id\s*:\s*|image_path\s*:\s*/m', '', $paths_str);
            $paths_arr = explode(',', $paths_str);

            foreach ($paths_arr as $key => $value) {

                if ($key % 2 == 0) {
                    $obj = new stdClass();
                    $obj->id = $value;
                }
                if ($key % 2 == 1) {
                    $obj->path = ltrim($value);
                    $paths[] = $obj;
                }
            }

            $item->paths = $paths;


            $arr[] = $item;
        }
    }

    $json = json_encode($arr);
    echo $json;

    exit;

}

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    $sql = "SELECT image_path FROM  project_file where project = '" . $data->id . "';";
    $result = pg_query($dbconn, $sql);
    $files = pg_fetch_all($result);

    foreach ($files as &$file) {
        unlink("./upload/" . $file["image_path"]);
    }
    $sql = "DELETE from project where pid ='" . $data->id . "';";

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!empty($data->descriptionEN) && !empty($data->descriptionTR) && !empty($data->projectName) && !empty($data->features) && !empty($data->paths)) {

        $features = "'" . implode("', '", $data->features) . "'";
        $pid = strtoupper(dechex(time()));
        $sql = "INSERT INTO project 
        (projectname,descriptionen,descriptiontr,features, pid ,created_at) 
          VALUES ('" . $data->projectName . "', '" . $data->descriptionEN . "',  '" . $data->descriptionTR . "',  ARRAY[" . $features . "],  '" . $pid . "',now());";

        $sql = $sql . " INSERT INTO public.project_file (project, image_path) VALUES ";
        $paths = $data->paths;
        var_dump($paths);
        foreach ($paths as &$path) {
            $sql = $sql . " ('${pid}', '${path}'),";
        }

        $sql = rtrim($sql, ",");
        $sql = $sql . ";";

        echo $sql;
    } else {
        die("Missing fields");
    }
}


if ($_SERVER['REQUEST_METHOD'] == "PUT") {


    $features = "'" . implode("', '", $data->features) . "'";

    $sql = "UPDATE project SET projectName='" . $data->projectName . "', descriptionen='" . $data->descriptionEN . "',
    descriptiontr='" . $data->descriptionTR . "', features= ARRAY[" . $features . "], created_at=now() WHERE pid='" . $data->pid . "';";


    if (isset($data->deleteImg) && $data->deleteImg) {
        echo "girdi*****************************************************************";
        $sql = $sql . "DELETE FROM project_file WHERE id IN (";
        foreach ($data->deleteImg as $id) {

            $sql = $sql . $id . " ,";
        }
        $sql = rtrim($sql, ",");
        $sql = $sql . "); ";
    }




    if (isset($data->paths) && $data->paths) {
        $sql = $sql . "INSERT INTO public.project_file (project, image_path)  VALUES";
        foreach ($data->paths as $image) {
            $sql = $sql . "(' $data->pid ', '$image')";
        }
        $sql = $sql . "; ";
    }

    echo $sql;
}


$result = pg_query($dbconn, $sql) or die('Error message: ' . pg_last_error());

$rows = pg_fetch_object($result);
echo json_encode($rows);
http_response_code(200);
pg_close($dbconn);