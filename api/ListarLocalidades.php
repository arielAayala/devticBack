<?php

include_once "../models/Localidades.php";

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

switch ($_SERVER["REQUEST_METHOD"]) {

    case "GET":
        if(isset($_COOKIE["token"])){
            if ($lstLocalidades=Localidades::listarLocalidades($_COOKIE["token"])) {
                echo json_encode($lstLocalidades);
            }else {
                http_response_code(400);
                echo json_encode(["error" => "Ocurrio un error"]);
            }
        }else{
            http_response_code(401);
            echo json_encode(["error" => "Token no existente"]);
        }

        break;
    
    default:
        # code...
        break;
}