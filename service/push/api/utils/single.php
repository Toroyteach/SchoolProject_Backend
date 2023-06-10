<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserCustomAlert.php';


    $item = new UserCustomAlert($con);
    $item->id = isset($_GET['id']) ? $_GET['id'] : die();
    
    $item->getSingleAlert($item->id);
    
    if ($item->notification_id != null) {
        // Alert found
        $alert_arr = array(
            "id" => $item->id,
            "option_id" => $item->option_id,
            "notification_id" => $item->notification_id,
            "max_value" => $item->max_value,
            "min_value" => $item->min_value,
            "created_at" => $item->created_at,
            "user_message" => $item->user_message,
            "alertName" => $item->option_name
        );
    
        http_response_code(200);
        echo json_encode($alert_arr);
    } else {
        // Alert not found
        http_response_code(404);
        echo json_encode(array("message" => "User custom alert not found."));
    }
    
    
