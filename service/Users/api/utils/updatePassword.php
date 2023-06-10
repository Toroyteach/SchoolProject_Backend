<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../../../connection.inc.php';
    include_once '../UserProfile.php';
    
    $item = new UserProfile($con);
    $item->id = isset($_POST['id']) ? $_POST['id'] : die();

    // Check if the request contains the necessary data for updating a user
    if (isset($_POST['id'], $_POST['password'])) {
        // Assign the data from the request to variables

        $password = md5($_POST['password']);
        
        $userData = $item->updatePassword($item->id, $password);
    
        // Add the new user custom alert
        if ($userData) {
            // Alert created successfully
            http_response_code(201);
            echo json_encode(array("message" => "Updated user Password Successfuly."));
        } else {
            // Failed to create the alert
            http_response_code(500);
            echo json_encode(array("message" => "Failed to Update User Password."));
        }
    } else {
        // Required data not provided in the request
        http_response_code(400);
        echo json_encode(array("message" => "Missing required data for update user data."));
    }
?>