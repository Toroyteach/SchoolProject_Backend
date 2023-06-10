<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    require_once '../../../../connection.inc.php';
    include_once '../UserCustomAlert.php';
    
    $item = new UserCustomAlert($con);
    $item->id = isset($_POST['id']) ? $_POST['id'] : die();
    
    // Check if the request contains the necessary data for updating the alert
    if (isset($_POST['option_id'], $_POST['max_value'], $_POST['min_value'], $_POST['user_message'])) {
        // Assign the data from the request to variables
        $option_id = $_POST['option_id'];
        $max_value = $_POST['max_value'];
        $min_value = $_POST['min_value'];
        $user_message = $_POST['user_message'];
    
        // Update the user custom alert
        if ($item->updateUserCustomAlert($item->id, $option_id, $max_value, $min_value, $user_message)) {
            // Alert updated successfully
            http_response_code(200);
            echo json_encode(array("message" => "User custom alert updated."));
        } else {
            // Failed to update the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to update user custom alert."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for updating user custom alert."));
    }
?>