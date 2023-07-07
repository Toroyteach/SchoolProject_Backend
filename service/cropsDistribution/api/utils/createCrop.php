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
    if (isset($_POST['crop_name'], $_POST['planting_date'], $_POST['harvesting_date'], $_POST['user_id'])) {
        // Assign the data from the request to variables
        $crop_name = $_POST['crop_name'];
        $planting_date = $_POST['planting_date'];
        $harvesting_date = $_POST['harvesting_date'];
        $user_id = $_POST['user_id'];
    
        // Add the new user custom alert
        if ($item->createCrops($crop_name, $planting_date, $harvesting_date, $user_id)) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "User Crop Created Successfully."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to create user crop Item."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for creating user crop."));
    }
?>