<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserCustomAlert.php';
    
    $item = new UserCustomAlert($con);

    // Check if the request contains the necessary data for creating a new alert
    if (isset($_POST['user_id'], $_POST['option_id'], $_POST['notification_id'], $_POST['max_value'], $_POST['min_value'], $_POST['user_message'])) {
        // Assign the data from the request to variables
        $user_id = $_POST['user_id'];
        $option_id = $_POST['option_id'];
        $notification_id = $_POST['notification_id'];
        $max_value = $_POST['max_value'];
        $min_value = $_POST['min_value'];
        $user_message = $_POST['user_message'];
    
        // Add the new user custom alert
        if ($item->addUserCustomAlert($user_id, $option_id, $notification_id, $max_value, $min_value, $user_message)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "User custom alert created."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create user custom alert."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for creating user custom alert."));
    }
?>