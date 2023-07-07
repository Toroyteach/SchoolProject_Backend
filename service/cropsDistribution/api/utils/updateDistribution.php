<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserCropDistribution.php';
    
    $item = new UserCropDistribution($con);

    // Check if the request contains the necessary data for creating a new alert
    if (isset($_POST['crop_id'], $_POST['season_id'], $_POST['quantity'], $_POST['user_id'])) {
        // Assign the data from the request to variables
        $crop_id = $_POST['crop_id'];
        $season_id = $_POST['season_id'];
        $quantity = $_POST['quantity'];
        $user_id = $_POST['user_id'];
    
        // Add the new user custom alert
        if ($item->updateDistribution($crop_id, $season_id, $quantity, $user_id)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "User Crop Distribution Updated Successfully."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to Update Crop Distribution Item."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for Update Crop Distribution."));
    }
?>