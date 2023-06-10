<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    require_once '../../../../connection.inc.php';
    include_once '../UserCustomAlert.php';

    $items = new UserCustomAlert($con);
    $stmt = $items->getAllUserCustomAlerts();
    $itemCount = $stmt->num_rows;

    //md5(uniqid($your_user_login, true))

    echo json_encode($itemCount);
    if ($itemCount > 0) {
        $alertArr = array();
        $alertArr["body"] = array();
        $alertArr["itemCount"] = $itemCount;

        while ($row = $stmt->fetch_assoc()) {
            extract($row);
            $e = array(
                "id" => $id,
                "option_id" => $option_id,
                "notification_id" => $notification_id,
                "max_value" => $max_value,
                "min_value" => $min_value,
                "created_at" => $created_at,
                "user_message" => $user_message,
                "alertName" => $option_name
            );
            array_push($alertArr["body"], $e);
        }
    
        echo json_encode($alertArr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No record found."));
    }
