<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../NewsInformation.php';
    
    $item = new NewsInformation($con);

    $notifications = $item->getNewsCategories();

    if (!empty($notifications)) {
        http_response_code(200);
        echo json_encode($notifications);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No News found."));
    }